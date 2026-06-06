<?php
/**
 * Feature Band V2 — Notched brand.
 *
 * A FULL-BLEED background-image band (edge to edge, NOT capped at the 1440
 * content rail) with a content BOX overlaying it plus an optional tall side
 * photo. The box holds an eyebrow, a Teko heading, a repeater of numbered
 * STEPS (each a Teko step title + a Lato description) and two buttons — a
 * primary solid CTA and a secondary phone button with an inline phone icon.
 * On /custom-project it renders the "YOUR CUSTOM PROJECT IN 3 STEPS" section.
 *
 * Mirrors the Banner Hero V2 / Image CTA Band V2 conventions (see
 * WIDGET-V2-BUILD-GUIDE.md): the X gutter lives on the INNER so the bg image
 * stays full-bleed, every colour control uses the §6.8 Color_Vars + render()
 * pattern, and _padding is re-pointed to the inner wrapper.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Feature_Band_V2 extends Widget_Base {

	private const ASSET_SLUG = 'feature-band-v2';

	public function get_name(): string      { return 'agency-feature-band-v2'; }
	public function get_title(): string     { return esc_html__( 'Feature Band V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-image-box'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'feature', 'band', 'steps', 'process', 'notched' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background image. Defaults are left EMPTY
	 * on purpose so the stylesheet owns the responsive X padding (§6.5 / gotcha
	 * #16).
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-fbv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	/**
	 * Default steps mirroring the /custom-project reference design.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_steps(): array {
		return [
			[
				'step_title' => esc_html__( 'STEP 1 | SHARE YOUR VISION', 'agency-elementor-widgets' ),
				'step_text'  => esc_html__( 'Tell us about your space, goals, inspiration, and budget range.', 'agency-elementor-widgets' ),
			],
			[
				'step_title' => esc_html__( 'STEP 2 | DESIGN & QUOTE', 'agency-elementor-widgets' ),
				'step_text'  => esc_html__( 'We craft a custom design and a clear, detailed quote for your project.', 'agency-elementor-widgets' ),
			],
			[
				'step_title' => esc_html__( 'STEP 3 | BUILD & INSTALL', 'agency-elementor-widgets' ),
				'step_text'  => esc_html__( 'Our crew builds and installs your timber structure with precision craftsmanship.', 'agency-elementor-widgets' ),
			],
		];
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_content();
		$this->controls_buttons();

		$this->style_band();
		$this->style_box();
		$this->style_eyebrow();
		$this->style_heading();
		$this->style_steps();
		$this->style_buttons();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'bg_image', [
			'label'       => esc_html__( 'Background image', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'description' => esc_html__( 'Full-bleed background image (edge to edge).', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'eyebrow', [
			'label'   => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'AS EASY AS 1-2-3', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading', [
			'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'YOUR CUSTOM PROJECT IN 3 STEPS', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading_tag', [
			'label'   => esc_html__( 'Heading HTML tag', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
			],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'step_title', [
			'label'   => esc_html__( 'Step title', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'STEP 1 | SHARE YOUR VISION', 'agency-elementor-widgets' ),
		] );

		$repeater->add_control( 'step_text', [
			'label'   => esc_html__( 'Step description', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => esc_html__( 'Tell us about your space, goals, inspiration, and budget range.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'steps', [
			'label'       => esc_html__( 'Steps', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => $this->default_steps(),
			'title_field' => '{{{ step_title }}}',
		] );

		$this->add_control( 'side_image', [
			'label'       => esc_html__( 'Side image', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'default'     => [ 'url' => '' ],
			'separator'   => 'before',
			'description' => esc_html__( 'Optional tall photo beside the content box. Leave empty to hide.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	private function controls_buttons(): void {
		$this->start_controls_section( 's_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'primary_text', [
			'label'   => esc_html__( 'Primary button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'SCHEDULE', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'primary_link', [
			'label'   => esc_html__( 'Primary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'show_secondary', [
			'label'        => esc_html__( 'Show secondary button', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'separator'    => 'before',
			'label_on'     => esc_html__( 'Yes', 'agency-elementor-widgets' ),
			'label_off'    => esc_html__( 'No', 'agency-elementor-widgets' ),
			'return_value' => 'yes',
		] );

		$this->add_control( 'secondary_text', [
			'label'     => esc_html__( 'Secondary button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( '801.410.4255', 'agency-elementor-widgets' ),
			'condition' => [ 'show_secondary' => 'yes' ],
		] );

		$this->add_control( 'secondary_link', [
			'label'     => esc_html__( 'Secondary button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => 'tel:8014104255' ],
			'condition' => [ 'show_secondary' => 'yes' ],
		] );

		$this->add_control( 'secondary_icon', [
			'label'        => esc_html__( 'Show phone icon', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'condition'    => [ 'show_secondary' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ──────────────────────────────────────────────────────

	private function style_band(): void {
		$this->start_controls_section( 'ss_band', [ 'label' => esc_html__( 'Band', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'overlay_color', [
			'label'       => esc_html__( 'Image overlay', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'description' => esc_html__( 'Optional tint over the background image for legibility.', 'agency-elementor-widgets' ),
			'selectors'   => [ '{{WRAPPER}}' => '--aew-fbv2-overlay: {{VALUE}};' ],
		] );

		$this->add_control( 'bg_position', [
			'label'     => esc_html__( 'Background position', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => 'center center',
			'selectors' => [ '{{WRAPPER}} .aew-fbv2' => 'background-position: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'pad_top', [
			'label'      => esc_html__( 'Padding top', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 240 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 80 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__inner' => 'padding-top: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'pad_bottom', [
			'label'      => esc_html__( 'Padding bottom', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 240 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 80 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__inner' => 'padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'col_gap', [
			'label'      => esc_html__( 'Column gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 120 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 40 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_box(): void {
		$this->start_controls_section( 'ss_box', [ 'label' => esc_html__( 'Content box', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Default transparent so the content sits directly over the image (the
		// live design); set a cream/dark colour to give the box a surface.
		$this->add_control( 'box_bg', [
			'label'     => esc_html__( 'Box background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-box-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'box_radius', [
			'label'      => esc_html__( 'Box border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__box' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'box_max_width', [
			'label'      => esc_html__( 'Box max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 360, 'max' => 900 ], '%' => [ 'min' => 40, 'max' => 100 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 620 ],
			'mobile_default' => [ 'unit' => '%', 'size' => 100 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__box' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'img_radius', [
			'label'      => esc_html__( 'Side image radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__photo' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_eyebrow(): void {
		$this->start_controls_section( 'ss_eyebrow', [ 'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-eyebrow: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'eyebrow_typo',
			'selector' => '{{WRAPPER}} .aew-fbv2__eyebrow',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Playfair Display' ],
				'font_weight'    => [ 'default' => '700' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-fbv2__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 56 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.9 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_steps(): void {
		$this->start_controls_section( 'ss_steps', [ 'label' => esc_html__( 'Steps', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'step_title_color', [
			'label'     => esc_html__( 'Step title color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-step-title: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'step_title_typo',
			'selector' => '{{WRAPPER}} .aew-fbv2__step-title',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 24 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_control( 'step_text_color', [
			'label'     => esc_html__( 'Step description color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-step-text: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'step_text_typo',
			'selector' => '{{WRAPPER}} .aew-fbv2__step-text',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 16 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.4 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_buttons(): void {
		$this->start_controls_section( 'ss_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Primary (solid) colour controls — assign to wrapper CSS vars; render()
		// re-emits the resolved value inline so global-bound colours survive on
		// the live page (gotcha #19). The stylesheet consumes each with a token
		// fallback.
		$this->add_control( 'pri_heading', [
			'label' => esc_html__( 'Primary button', 'agency-elementor-widgets' ),
			'type'  => Controls_Manager::HEADING,
		] );
		$this->add_control( 'pri_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-pri-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_text', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-pri-text: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-pri-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'pri_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-pri-text-hover: {{VALUE}};' ],
		] );

		// Secondary (phone) — white outline for legibility over the dark image
		// (design-system B3 exception). render() re-emits resolved values inline.
		$this->add_control( 'sec_heading', [
			'label'     => esc_html__( 'Secondary button (phone)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'sec_color', [
			'label'     => esc_html__( 'Border & text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-sec-color: {{VALUE}};' ],
		] );
		$this->add_control( 'sec_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-sec-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'sec_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fbv2-sec-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'label'     => esc_html__( 'Button typography', 'agency-elementor-widgets' ),
			'selector'  => '{{WRAPPER}} .aew-fbv2__btn',
			'separator' => 'before',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_control( 'btn_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 32 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fbv2__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$heading  = (string) ( $s['heading'] ?? '' );
		$eyebrow  = (string) ( $s['eyebrow'] ?? '' );
		$bg_image = $s['bg_image'] ?? [];
		$bg_url   = is_array( $bg_image ) ? (string) ( $bg_image['url'] ?? '' ) : '';

		$steps = $s['steps'] ?? [];
		if ( ! is_array( $steps ) ) {
			$steps = [];
		}

		// Bail only when there is nothing to show.
		if ( '' === $heading && '' === $bg_url && empty( $steps ) ) {
			return;
		}

		$allowed_tags = [ 'h1', 'h2', 'h3' ];
		$tag          = in_array( $s['heading_tag'] ?? 'h2', $allowed_tags, true ) ? $s['heading_tag'] : 'h2';

		$side_image = $s['side_image'] ?? [];
		$side_url   = is_array( $side_image ) ? (string) ( $side_image['url'] ?? '' ) : '';
		$side_alt   = is_array( $side_image ) ? (string) ( $side_image['alt'] ?? '' ) : '';
		$side_id    = is_array( $side_image ) ? (int) ( $side_image['id'] ?? 0 ) : 0;
		if ( '' === $side_alt && $side_id > 0 ) {
			$side_alt = (string) get_post_meta( $side_id, '_wp_attachment_image_alt', true );
		}

		$primary_text = (string) ( $s['primary_text'] ?? '' );
		$primary      = $this->parse_link( $s['primary_link'] ?? [] );
		$has_primary  = '' !== $primary_text && '' !== $primary['url'];

		$show_secondary = 'yes' === ( $s['show_secondary'] ?? 'yes' );
		$secondary_text = (string) ( $s['secondary_text'] ?? '' );
		$secondary      = $this->parse_link( $s['secondary_link'] ?? [] );
		$has_secondary  = $show_secondary && '' !== $secondary_text && '' !== $secondary['url'];
		$sec_icon       = 'yes' === ( $s['secondary_icon'] ?? 'yes' );

		$this->add_render_attribute( 'wrapper', 'class', 'aew-fbv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-feature-band-v2', '' );

		// Emit resolved colours as inline CSS vars on the wrapper (live-page
		// guarantee for global-bound picks — see guide §6.8 / gotcha #19).
		$style = Color_Vars::build(
			$this,
			$s,
			[
				'overlay_color'    => '--aew-fbv2-overlay',
				'box_bg'           => '--aew-fbv2-box-bg',
				'eyebrow_color'    => '--aew-fbv2-eyebrow',
				'heading_color'    => '--aew-fbv2-heading',
				'step_title_color' => '--aew-fbv2-step-title',
				'step_text_color'  => '--aew-fbv2-step-text',
				'pri_bg'           => '--aew-fbv2-pri-bg',
				'pri_text'         => '--aew-fbv2-pri-text',
				'pri_bg_hover'     => '--aew-fbv2-pri-bg-hover',
				'pri_text_hover'   => '--aew-fbv2-pri-text-hover',
				'sec_color'        => '--aew-fbv2-sec-color',
				'sec_bg_hover'     => '--aew-fbv2-sec-bg-hover',
				'sec_text_hover'   => '--aew-fbv2-sec-text-hover',
			]
		);
		if ( '' !== $bg_url ) {
			$style .= '--aew-fbv2-image: url(' . esc_url( $bg_url ) . ');';
		}
		if ( '' !== $style ) {
			$this->add_render_attribute( 'wrapper', 'style', $style );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-fbv2__inner">
				<div class="aew-fbv2__grid">

					<div class="aew-fbv2__box">

						<?php if ( '' !== $eyebrow ) : ?>
							<p class="aew-fbv2__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $heading ) : ?>
							<<?php echo esc_html( $tag ); ?> class="aew-fbv2__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $tag ); ?>>
						<?php endif; ?>

						<?php if ( ! empty( $steps ) ) : ?>
							<div class="aew-fbv2__steps">
								<?php
								foreach ( $steps as $step ) :
									$step_title = (string) ( $step['step_title'] ?? '' );
									$step_text  = (string) ( $step['step_text'] ?? '' );

									if ( '' === trim( $step_title ) && '' === trim( $step_text ) ) {
										continue;
									}
									?>
									<div class="aew-fbv2__step">
										<?php if ( '' !== trim( $step_title ) ) : ?>
											<h4 class="aew-fbv2__step-title"><?php echo esc_html( $step_title ); ?></h4>
										<?php endif; ?>
										<?php if ( '' !== trim( $step_text ) ) : ?>
											<p class="aew-fbv2__step-text"><?php echo esc_html( $step_text ); ?></p>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $has_primary || $has_secondary ) : ?>
							<div class="aew-fbv2__buttons">

								<?php if ( $has_primary ) : ?>
									<a class="aew-fbv2__btn aew-fbv2__btn--primary"
										href="<?php echo esc_url( $primary['url'] ); ?>"
										<?php echo $primary['target'] ? 'target="' . esc_attr( $primary['target'] ) . '"' : ''; ?>
										<?php echo $primary['rel'] ? 'rel="' . esc_attr( $primary['rel'] ) . '"' : ''; ?>>
										<span class="aew-fbv2__btn-label"><?php echo esc_html( $primary_text ); ?></span>
									</a>
								<?php endif; ?>

								<?php if ( $has_secondary ) : ?>
									<a class="aew-fbv2__btn aew-fbv2__btn--secondary"
										href="<?php echo esc_url( $secondary['url'] ); ?>"
										<?php echo $secondary['target'] ? 'target="' . esc_attr( $secondary['target'] ) . '"' : ''; ?>
										<?php echo $secondary['rel'] ? 'rel="' . esc_attr( $secondary['rel'] ) . '"' : ''; ?>>
										<?php if ( $sec_icon ) : ?>
											<svg class="aew-fbv2__btn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
												<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path>
											</svg>
										<?php endif; ?>
										<span class="aew-fbv2__btn-label"><?php echo esc_html( $secondary_text ); ?></span>
									</a>
								<?php endif; ?>

							</div>
						<?php endif; ?>

					</div>

					<?php if ( '' !== $side_url ) : ?>
						<div class="aew-fbv2__media">
							<img class="aew-fbv2__photo"
								src="<?php echo esc_url( $side_url ); ?>"
								alt="<?php echo esc_attr( $side_alt ); ?>"
								decoding="async"
								loading="lazy" />
						</div>
					<?php endif; ?>

				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * @param array<string, mixed>|string $d URL control value.
	 * @return array{url: string, target: string, rel: string}
	 */
	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) return [ 'url' => '', 'target' => '', 'rel' => '' ];
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) $r .= ' nofollow';
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}
}
