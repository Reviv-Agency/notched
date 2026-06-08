<?php
/**
 * Job Listings — a stack of open-role cards, each with a title, a rich
 * qualifications block, and an APPLY button. Built for the notched.com
 * /career listings section.
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
 * Stacked job-opening cards with title + qualifications + apply button.
 */
class Widget_Job_Listings extends Widget_Base {

	private const ASSET_SLUG = 'job-listings';

	public function get_name(): string {
		return 'agency-job-listings';
	}

	public function get_title(): string {
		return esc_html__( 'Job Listings', 'agency-elementor-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-bullet-list';
	}

	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	public function get_keywords(): array {
		return [ 'jobs', 'careers', 'openings', 'listings', 'apply', 'notched' ];
	}

	private function default_jobs(): array {
		$body = '<p class="aew-jobs__quals-label">MINIMUM QUALIFICATIONS</p>'
			. '<ul><li>PhD degree in Electrical Engineering, Computer Engineering, Computer Science, a related field, or equivalent practical experience.</li>'
			. '<li>Academic, educational, internship, or project experience with physical design.</li></ul>';
		$jobs = [];
		foreach ( [ 'Open Role', 'Open Role', 'Open Role', 'Open Role' ] as $title ) {
			$jobs[] = [ 'title' => $title, 'body' => $body, 'btn_text' => 'APPLY NOW', 'btn_link' => [ 'url' => '/contact-us/' ] ];
		}
		return $jobs;
	}

	protected function register_controls(): void {
		// ── CONTENT ──
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Section heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );

		$this->add_responsive_control( 'columns', [
			'label'          => esc_html__( 'Columns', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '2',
			'tablet_default' => '2',
			'mobile_default' => '1',
			'options'        => [ '1' => '1', '2' => '2', '3' => '3' ],
			'selectors'      => [ '{{WRAPPER}} .aew-jobs__list' => '--aew-jobs-cols: {{VALUE}};' ],
		] );

		$repeater = new Repeater();
		$repeater->add_control( 'title', [
			'label'   => esc_html__( 'Role title', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Open Role', 'agency-elementor-widgets' ),
		] );
		$repeater->add_control( 'body', [
			'label'   => esc_html__( 'Details', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '',
		] );
		$repeater->add_control( 'btn_text', [
			'label'   => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'APPLY NOW', 'agency-elementor-widgets' ),
		] );
		$repeater->add_control( 'btn_link', [
			'label'   => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '/contact-us/' ],
		] );

		$this->add_control( 'jobs', [
			'label'       => esc_html__( 'Openings', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_jobs(),
			'title_field' => '{{{ title }}}',
		] );

		$this->end_controls_section();

		// ── STYLE ──
		$this->start_controls_section( 's_style', [ 'label' => esc_html__( 'Style', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-section-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_bg', [
			'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-card-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => esc_html__( 'Card corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-jobs__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-heading: {{VALUE}};' ],
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Role title color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-title: {{VALUE}};' ],
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Button background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_color', [
			'label'     => esc_html__( 'Button text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-jobs-btn-text: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typography',
			'selector'       => '{{WRAPPER}} .aew-jobs__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 48 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 34 ] ],
			],
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$jobs = $s['jobs'] ?? [];
		if ( ! is_array( $jobs ) || empty( $jobs ) ) {
			return;
		}

		$heading = (string) ( $s['heading'] ?? '' );
		$tag     = preg_replace( '/[^a-z0-9]/i', '', (string) ( $s['heading_tag'] ?? 'h2' ) ) ?: 'h2';

		$this->add_render_attribute( 'wrapper', 'class', 'aew-jobs' );
		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'     => '--aew-jobs-section-bg',
			'card_bg'        => '--aew-jobs-card-bg',
			'heading_color'  => '--aew-jobs-heading',
			'title_color'    => '--aew-jobs-title',
			'text_color'     => '--aew-jobs-text',
			'btn_bg'         => '--aew-jobs-btn-bg',
			'btn_text_color' => '--aew-jobs-btn-text',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-jobs__inner">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<<?php echo esc_html( $tag ); ?> class="aew-jobs__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>
				<div class="aew-jobs__list">
					<?php
					foreach ( $jobs as $job ) :
						$title    = (string) ( $job['title'] ?? '' );
						$body     = (string) ( $job['body'] ?? '' );
						$btn_text = (string) ( $job['btn_text'] ?? '' );
						$link     = $job['btn_link'] ?? [];
						$btn_url  = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : '';
						$target   = ( is_array( $link ) && ! empty( $link['is_external'] ) ) ? ' target="_blank"' : '';
						$rel      = ( is_array( $link ) && ! empty( $link['nofollow'] ) ) ? ' rel="nofollow"' : '';
						?>
						<article class="aew-jobs__card">
							<div class="aew-jobs__card-body">
								<?php if ( '' !== trim( $title ) ) : ?>
									<h3 class="aew-jobs__title"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( ! Rich_Text::is_empty( $body ) ) : ?>
									<div class="aew-jobs__quals aew-rich-text"><?php Rich_Text::echo_html( $body ); ?></div>
								<?php endif; ?>
							</div>
							<?php if ( '' !== trim( $btn_text ) ) : ?>
								<a class="aew-jobs__btn" href="<?php echo esc_url( $btn_url ?: '#' ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
									<?php echo esc_html( $btn_text ); ?>
								</a>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
