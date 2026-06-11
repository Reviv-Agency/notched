<?php
/**
 * Team Grid V2 Elementor widget ("Our Crew" member grid).
 *
 * An optional section heading + subtext, then a responsive grid of member
 * cards — each a photo with the member's name and role beneath it. Mirrors
 * example.com/our-crew. Column count, gap, image radius and all colours are
 * editable per-instance from the Style tab (§6.8 var pattern).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Team member grid — photo + name + role cards.
 */
class Widget_Team_Grid_V2 extends Widget_Base {

	private const ASSET_SLUG = 'team-grid-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-team-grid-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Team Grid V2', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	/**
	 * @return array<int, string>
	 */
	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_keywords(): array {
		return [ 'team', 'crew', 'staff', 'people' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper.
	 * Defaults left EMPTY (WIDGET-V2-BUILD-GUIDE §5 / gotcha #16).
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-team__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->controls_content();
		$this->controls_layout();
		$this->style_section();
		$this->style_card();
		$this->style_typography();
	}

	/**
	 * Default members (the live Our Crew roster).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_members(): array {
		return [
			[ 'name' => 'Jardin',   'role' => 'Owner' ],
			[ 'name' => 'Isabelle', 'role' => 'Drafts Woman' ],
			[ 'name' => 'Caroline', 'role' => 'Office Manager' ],
			[ 'name' => 'Dave',     'role' => 'Project Designer' ],
			[ 'name' => 'Brad',     'role' => 'Project Designer' ],
			[ 'name' => 'Daryl',    'role' => 'Shop Manager' ],
		];
	}

	/**
	 * CONTENT tab — heading, subtext, member repeater.
	 *
	 * @return void
	 */
	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
			'placeholder' => esc_html__( 'Optional section heading', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_control( 'subtext', [
			'label'   => esc_html__( 'Subtext', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => '',
		] );

		$this->add_control( 'show_button', [
			'label'   => esc_html__( 'Show header button', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'button_text', [
			'label'     => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'MEET OUR TEAM', 'agency-elementor-widgets' ),
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$this->add_control( 'button_link', [
			'label'     => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => '' ],
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$this->add_control( 'button_arrow', [
			'label'     => esc_html__( 'Button arrow', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'show_button' => 'yes' ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'photo', [
			'label'   => esc_html__( 'Photo', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$repeater->add_control( 'name', [
			'label'   => esc_html__( 'Name', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Name', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'role', [
			'label'   => esc_html__( 'Role', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Role', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'members', [
			'label'       => esc_html__( 'Members', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_members(),
			'title_field' => '{{{ name }}}',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — columns, gap, image aspect.
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'columns', [
			'label'     => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '3',
			'options'   => [ '2' => '2', '3' => '3', '4' => '4' ],
			'selectors' => [ '{{WRAPPER}} .aew-team__grid' => '--aew-team-cols: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'gap', [
			'label'      => esc_html__( 'Gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 64 ] ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'photo_ratio', [
			'label'   => esc_html__( 'Photo aspect ratio', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '1 / 1',
			'options' => [
				'1 / 1'  => esc_html__( 'Square (1:1)', 'agency-elementor-widgets' ),
				'4 / 5'  => esc_html__( 'Portrait (4:5)', 'agency-elementor-widgets' ),
				'3 / 4'  => esc_html__( 'Portrait (3:4)', 'agency-elementor-widgets' ),
				'4 / 3'  => esc_html__( 'Landscape (4:3)', 'agency-elementor-widgets' ),
			],
			'selectors' => [ '{{WRAPPER}} .aew-team__photo' => 'aspect-ratio: {{VALUE}};' ],
		] );

		$this->add_control( 'align', [
			'label'     => esc_html__( 'Text alignment', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'center',
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
			],
			'selectors' => [ '{{WRAPPER}} .aew-team__card' => 'text-align: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-section-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'tray_bg', [
			'label'       => esc_html__( 'Cards tray background', 'agency-elementor-widgets' ),
			'description' => esc_html__( 'The inner box behind the member cards. Leave empty for none.', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'selectors'   => [ '{{WRAPPER}}' => '--aew-team-tray-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'tray_radius', [
			'label'      => esc_html__( 'Cards tray corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__tray' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Header button background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_color', [
			'label'     => esc_html__( 'Header button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-btn-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_border', [
			'label'     => esc_html__( 'Header button border', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-btn-border: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_radius', [
			'label'      => esc_html__( 'Header button corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card / photo.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Photo', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'photo_height', [
			'label'      => esc_html__( 'Photo height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 160, 'max' => 600 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 300 ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__photo' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;' ],
		] );

		$this->add_control( 'photo_radius', [
			'label'      => esc_html__( 'Photo corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__photo' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'photo_bg', [
			'label'     => esc_html__( 'Photo placeholder colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-photo-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'textbox_bg', [
			'label'       => esc_html__( 'Name/title box colour', 'agency-elementor-widgets' ),
			'description' => esc_html__( 'The box under each photo holding the name + role. Leave empty for none.', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'selectors'   => [ '{{WRAPPER}}' => '--aew-team-textbox-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [
				'{{WRAPPER}} .aew-team__card'    => 'border-radius: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .aew-team__textbox' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — typography + colours.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Section heading colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'subtext_color', [
			'label'     => esc_html__( 'Subtext colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-subtext: {{VALUE}};' ],
		] );

		$this->add_control( 'name_color', [
			'label'     => esc_html__( 'Name colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-name: {{VALUE}};' ],
		] );

		$this->add_control( 'role_color', [
			'label'     => esc_html__( 'Role colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-role: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$members = $s['members'] ?? [];
		if ( ! is_array( $members ) || empty( $members ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-team' );
		$this->add_render_attribute( 'wrapper', 'data-aew-team-grid-v2', '' );

		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'     => '--aew-team-section-bg',
			'tray_bg'        => '--aew-team-tray-bg',
			'textbox_bg'     => '--aew-team-textbox-bg',
			'photo_bg'       => '--aew-team-photo-bg',
			'heading_color'  => '--aew-team-heading',
			'subtext_color'  => '--aew-team-subtext',
			'name_color'     => '--aew-team-name',
			'role_color'     => '--aew-team-role',
			'btn_bg'         => '--aew-team-btn-bg',
			'btn_text_color' => '--aew-team-btn-text',
			'btn_border'     => '--aew-team-btn-border',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$subtext = (string) ( $s['subtext'] ?? '' );

		$show_btn = 'yes' === ( $s['show_button'] ?? '' );
		$btn_text = (string) ( $s['button_text'] ?? '' );
		$link     = $s['button_link'] ?? [];
		$btn_url  = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : '';
		$btn_tg   = ( is_array( $link ) && ! empty( $link['is_external'] ) ) ? ' target="_blank"' : '';
		$btn_rel  = ( is_array( $link ) && ! empty( $link['nofollow'] ) ) ? ' rel="nofollow"' : '';
		$btn_arrow = 'yes' === ( $s['button_arrow'] ?? '' );
		$has_btn   = $show_btn && '' !== trim( $btn_text );

		$has_header = '' !== trim( $heading ) || '' !== trim( $subtext ) || $has_btn;
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-team__inner">
				<?php if ( $has_header ) : ?>
					<div class="aew-team__header<?php echo $has_btn ? ' aew-team__header--with-btn' : ''; ?>">
						<div class="aew-team__header-text">
							<?php if ( '' !== trim( $heading ) ) : ?>
								<<?php echo esc_html( $tag ); ?> class="aew-team__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
							<?php endif; ?>
							<?php if ( '' !== trim( $subtext ) ) : ?>
								<p class="aew-team__subtext"><?php echo esc_html( $subtext ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( $has_btn ) : ?>
							<a class="aew-team__btn" href="<?php echo esc_url( $btn_url ?: '#' ); ?>"<?php echo $btn_tg . $btn_rel; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
								<span class="aew-team__btn-label"><?php echo esc_html( $btn_text ); ?></span>
								<?php if ( $btn_arrow ) : ?>
									<svg class="aew-team__btn-arrow" viewBox="0 0 200 200" width="20" height="20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M179.7 96.3H55.9c10.4-6 15.6-15.8 15.6-29.1h-7.3c0 7.2 0 28.9-43 29.1h-.9v7.3c43.2.2 43.2 22 43.2 29.1h7.3c0-13.4-5.3-23.1-15.8-29.1h124.6v-7.3z"/></svg>
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="aew-team__tray">
					<div class="aew-team__grid">
						<?php
						foreach ( $members as $m ) :
							$photo = $m['photo'] ?? [];
							$url   = is_array( $photo ) ? (string) ( $photo['url'] ?? '' ) : '';
							$name  = (string) ( $m['name'] ?? '' );
							$role  = (string) ( $m['role'] ?? '' );

							if ( '' === $url && '' === trim( $name ) && '' === trim( $role ) ) {
								continue;
							}
							?>
							<article class="aew-team__card">
								<?php if ( '' !== $url ) : ?>
									<img class="aew-team__photo" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $name ); ?>" decoding="async" loading="lazy" />
								<?php else : ?>
									<div class="aew-team__photo aew-team__photo--empty" aria-hidden="true"></div>
								<?php endif; ?>
								<?php if ( '' !== trim( $name ) || '' !== trim( $role ) ) : ?>
									<div class="aew-team__textbox">
										<?php if ( '' !== trim( $name ) ) : ?>
											<p class="aew-team__name"><?php echo esc_html( $name ); ?></p>
										<?php endif; ?>
										<?php if ( '' !== trim( $role ) ) : ?>
											<p class="aew-team__role"><?php echo esc_html( $role ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
