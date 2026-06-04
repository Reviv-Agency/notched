<?php
/**
 * Team Grid V2 Elementor widget ("Our Crew" member grid).
 *
 * An optional section heading + subtext, then a responsive grid of member
 * cards — each a photo with the member's name and role beneath it. Mirrors
 * notched.com/our-crew. Column count, gap, image radius and all colours are
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
		return esc_html__( 'Team Grid V2 (Notched)', 'agency-elementor-widgets' );
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
		return [ 'team', 'crew', 'staff', 'people', 'notched' ];
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

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card / photo.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Photo', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'photo_radius', [
			'label'      => esc_html__( 'Photo corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-team__photo' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'photo_bg', [
			'label'     => esc_html__( 'Photo placeholder colour', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#7D958D',
			'selectors' => [ '{{WRAPPER}}' => '--aew-team-photo-bg: {{VALUE}};' ],
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
			'section_bg'    => '--aew-team-section-bg',
			'photo_bg'      => '--aew-team-photo-bg',
			'heading_color' => '--aew-team-heading',
			'subtext_color' => '--aew-team-subtext',
			'name_color'    => '--aew-team-name',
			'role_color'    => '--aew-team-role',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';
		$subtext = (string) ( $s['subtext'] ?? '' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-team__inner">
				<?php if ( '' !== trim( $heading ) || '' !== trim( $subtext ) ) : ?>
					<div class="aew-team__header">
						<?php if ( '' !== trim( $heading ) ) : ?>
							<<?php echo esc_html( $tag ); ?> class="aew-team__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>
						<?php if ( '' !== trim( $subtext ) ) : ?>
							<p class="aew-team__subtext"><?php echo esc_html( $subtext ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

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
							<?php if ( '' !== trim( $name ) ) : ?>
								<p class="aew-team__name"><?php echo esc_html( $name ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== trim( $role ) ) : ?>
								<p class="aew-team__role"><?php echo esc_html( $role ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
