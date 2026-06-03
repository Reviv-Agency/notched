<?php
/**
 * Booking Cards V2 Elementor widget (consultation booking cards).
 *
 * A row of clean service cards — each with an optional icon, a heading, a
 * tagline, a duration label (e.g. "30 min") on a divided footer row, and a
 * "Book Now" button. Unlike Region Cards V2 there is NO background image; the
 * cards are flat surfaces on the section background, matching the live
 * notched.com/book-online consultation layout. Colours, the card surface and
 * the button are editable per-instance from the Style tab (§6.8 var pattern).
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
 * Booking cards — flat consultation cards with icon, copy, duration and CTA.
 */
class Widget_Booking_Cards_V2 extends Widget_Base {

	private const ASSET_SLUG = 'booking-cards-v2';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-booking-cards-v2';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Booking Cards V2 (Notched)', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-price-table';
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
		return [ 'booking', 'consultation', 'cards', 'schedule', 'notched' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY (see
	 * WIDGET-V2-BUILD-GUIDE §5 / gotcha #16) — the stylesheet owns responsive X
	 * padding; a non-empty default would clobber it at every breakpoint.
	 *
	 * @param bool $with_common_controls Whether to include common controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-bkcv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_cards();
		$this->controls_layout();
		$this->style_section();
		$this->style_card();
		$this->style_typography();
		$this->style_button();
	}

	/**
	 * Default cards shipped with the widget (Virtual / Utah / Arizona).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_cards(): array {
		return [
			[
				'heading'  => 'Free Notched Consultation | Virtual',
				'tagline'  => 'Anytime, Anywhere!',
				'duration' => '30 min',
				'btn_text' => 'Book Now',
				'btn_link' => [ 'url' => '#' ],
			],
			[
				'heading'  => 'Free Notched Consultation | Utah',
				'tagline'  => "Living in Utah? Let's chat in person!",
				'duration' => '30 min',
				'btn_text' => 'Book Now',
				'btn_link' => [ 'url' => '#' ],
			],
			[
				'heading'  => 'Free Notched Consultation | Arizona',
				'tagline'  => "Want a timber structure in Arizona? Let's chat!",
				'duration' => '30 min',
				'btn_text' => 'Book Now',
				'btn_link' => [ 'url' => '#' ],
			],
		];
	}

	/**
	 * CONTENT tab — the card repeater.
	 *
	 * @return void
	 */
	private function controls_cards(): void {
		$this->start_controls_section( 's_cards', [ 'label' => esc_html__( 'Cards', 'agency-elementor-widgets' ) ] );

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			[
				'label'       => esc_html__( 'Icon image', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'Optional small icon shown at the top of the card.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Free Notched Consultation | Virtual', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'tagline',
			[
				'label'   => esc_html__( 'Tagline', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'Anytime, Anywhere!', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'duration',
			[
				'label'       => esc_html__( 'Duration label', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '30 min', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'Leave empty to hide the duration row.', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'btn_text',
			[
				'label'   => esc_html__( 'Button label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Book Now', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'btn_link',
			[
				'label'       => esc_html__( 'Button link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'placeholder' => esc_html__( 'https://your-link.com', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'cards',
			[
				'label'       => esc_html__( 'Cards', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_cards(),
				'title_field' => '{{{ heading }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * CONTENT tab — layout knobs (columns + gap).
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_control(
			'columns',
			[
				'label'   => esc_html__( 'Columns (desktop)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .aew-bkcv2__grid' => '--aew-bkcv2-cols: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => esc_html__( 'Gap', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .aew-bkcv2__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label'     => esc_html__( 'Text alignment', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
				],
				'selectors' => [
					'{{WRAPPER}} .aew-bkcv2__card' => '--aew-bkcv2-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — section background.
	 *
	 * @return void
	 */
	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'section_bg',
			[
				'label'     => esc_html__( 'Section background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-section-bg: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — card surface.
	 *
	 * @return void
	 */
	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => esc_html__( 'Card', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-card-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Card radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
				'selectors'  => [ '{{WRAPPER}} .aew-bkcv2__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__( 'Divider colour', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-divider: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — typography + colours.
	 *
	 * @return void
	 */
	private function style_typography(): void {
		$this->start_controls_section( 'ss_type', [ 'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'heading_color',
			[
				'label'     => esc_html__( 'Heading colour', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-heading: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typo',
				'selector' => '{{WRAPPER}} .aew-bkcv2__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '600' ],
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Tagline colour', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-text: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'duration_color',
			[
				'label'     => esc_html__( 'Duration colour', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-duration: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * STYLE tab — button colours.
	 *
	 * @return void
	 */
	private function style_button(): void {
		$this->start_controls_section( 'ss_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control(
			'btn_bg',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#AA7D44',
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-btn-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => esc_html__( 'Text', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-btn-text: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_bg_hover',
			[
				'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#876137',
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-btn-bg-hover: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'btn_text_hover',
			[
				'label'     => esc_html__( 'Text (hover)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}}' => '--aew-bkcv2-btn-text-hover: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$cards = $s['cards'] ?? [];
		if ( ! is_array( $cards ) || empty( $cards ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'aew-bkcv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-booking-cards-v2', '' );

		/*
		 * Resolve colour controls → inline CSS vars on the wrapper so global-
		 * bound picks survive on the front end (§6.8 / gotcha #19). The matching
		 * `selectors` on each control drive the editor preview; this inline value
		 * wins on live.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'section_bg'     => '--aew-bkcv2-section-bg',
				'card_bg'        => '--aew-bkcv2-card-bg',
				'divider_color'  => '--aew-bkcv2-divider',
				'heading_color'  => '--aew-bkcv2-heading',
				'text_color'     => '--aew-bkcv2-text',
				'duration_color' => '--aew-bkcv2-duration',
				'btn_bg'         => '--aew-bkcv2-btn-bg',
				'btn_text_color' => '--aew-bkcv2-btn-text',
				'btn_bg_hover'   => '--aew-bkcv2-btn-bg-hover',
				'btn_text_hover' => '--aew-bkcv2-btn-text-hover',
			]
		);
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-bkcv2__inner">
				<div class="aew-bkcv2__grid">
					<?php
					foreach ( $cards as $card ) :
						$icon     = $card['icon'] ?? [];
						$icon_url = is_array( $icon ) ? ( $icon['url'] ?? '' ) : '';
						$heading  = (string) ( $card['heading'] ?? '' );
						$tagline  = (string) ( $card['tagline'] ?? '' );
						$duration = (string) ( $card['duration'] ?? '' );
						$btn_lbl  = (string) ( $card['btn_text'] ?? '' );
						$link     = $this->parse_link( $card['btn_link'] ?? [] );
						?>
						<article class="aew-bkcv2__card">
							<div class="aew-bkcv2__body">
								<?php if ( '' !== $icon_url ) : ?>
									<img class="aew-bkcv2__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" decoding="async" loading="lazy" />
								<?php endif; ?>
								<?php if ( '' !== $heading ) : ?>
									<h3 class="aew-bkcv2__title"><?php echo esc_html( $heading ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== trim( $tagline ) ) : ?>
									<p class="aew-bkcv2__text"><?php echo esc_html( $tagline ); ?></p>
								<?php endif; ?>
							</div>
							<div class="aew-bkcv2__footer">
								<?php if ( '' !== trim( $duration ) ) : ?>
									<p class="aew-bkcv2__duration"><?php echo esc_html( $duration ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== trim( $btn_lbl ) ) : ?>
									<a class="aew-bkcv2__btn"
										href="<?php echo esc_url( $link['url'] ?: '#' ); ?>"
										<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
										<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
										<?php echo esc_html( $btn_lbl ); ?>
									</a>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Normalise an Elementor URL control value into url/target/rel.
	 *
	 * @param mixed $d Raw URL control value.
	 * @return array{url:string,target:string,rel:string}
	 */
	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) {
			$r .= ' nofollow';
		}
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}
}
