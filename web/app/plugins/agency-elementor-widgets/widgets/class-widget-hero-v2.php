<?php
/**
 * Hero V2 — Notched brand.
 *
 * Full-bleed background video (edge to edge, no padding, no rounded corners)
 * with a centered overlay: eyebrow + large Teko headline + description +
 * two CTA buttons + social-proof row (avatars + stars + label).
 *
 * Mirrors the Header V2 / Footer V2 conventions (see WIDGET-V2-BUILD-GUIDE.md).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Hero_V2 extends Widget_Base {

	private const ASSET_SLUG = 'hero-v2';

	public function get_name(): string      { return 'agency-hero-v2'; }
	public function get_title(): string     { return esc_html__( 'Hero V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-banner'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'hero', 'video', 'banner', 'notched', 'cta' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to the inner content wrapper
	 * so the outer block keeps its full-bleed video background while sidebar
	 * padding controls behave as expected.
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-hev2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			// Leave the override empty so the stylesheet owns the responsive X
			// padding (40px mobile/tablet → 80px desktop). A non-empty default
			// here is emitted by Elementor WITHOUT media queries and would
			// clobber the stylesheet at every breakpoint.
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_media();
		$this->controls_content();
		$this->controls_buttons();
		$this->controls_social();

		$this->style_layout();
		$this->style_overlay();
		$this->style_eyebrow();
		$this->style_headline();
		$this->style_description();
		$this->style_buttons();
		$this->style_social();
	}

	private function default_video_url(): string {
		return Widget_Assets::url( self::ASSET_SLUG, 'images/Hero-1.mp4' );
	}

	private function default_mobile_image_url(): string {
		return Widget_Assets::url( self::ASSET_SLUG, 'images/mobile image.avif' );
	}

	private function controls_media(): void {
		$this->start_controls_section( 's_media', [ 'label' => esc_html__( 'Background video', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'video', [
			'label'       => esc_html__( 'Video file', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::MEDIA,
			'media_types' => [ 'video' ],
			'default'     => [ 'url' => $this->default_video_url() ],
			'description' => esc_html__( 'MP4 plays muted, looped and autoplay as a full-bleed background.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'poster', [
			'label'   => esc_html__( 'Poster image (fallback)', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
			'description' => esc_html__( 'Shown before the video loads and where autoplay is blocked.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'mobile_image', [
			'label'   => esc_html__( 'Mobile & tablet image', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => $this->default_mobile_image_url() ],
			'description' => esc_html__( 'Used instead of the video on tablet and mobile (the video does not load there).', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Content', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'eyebrow', [
			'label'   => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'TIMBER FRAME PERGOLAS & PAVILIONS', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'headline', [
			'label'   => esc_html__( 'Headline', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Built in a day. Built for life.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'description', [
			'label'   => esc_html__( 'Description', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Custom structures and precision-cut kits built from real timber with traditional joinery, made to elevate your space for generations.', 'agency-elementor-widgets' ),
		] );

		$this->end_controls_section();
	}

	private function controls_buttons(): void {
		$this->start_controls_section( 's_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'primary_text', [
			'label'   => esc_html__( 'Primary button text', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'Shop Standard Kits', 'agency-elementor-widgets' ),
		] );
		$this->add_control( 'primary_link', [
			'label'   => esc_html__( 'Primary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->add_control( 'secondary_text', [
			'label'     => esc_html__( 'Secondary button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Free Consultation', 'agency-elementor-widgets' ),
			'separator' => 'before',
		] );
		$this->add_control( 'secondary_link', [
			'label'   => esc_html__( 'Secondary button link', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => '#' ],
		] );

		$this->end_controls_section();
	}

	private function controls_social(): void {
		$this->start_controls_section( 's_social', [ 'label' => esc_html__( 'Social proof', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'show_social', [
			'label'   => esc_html__( 'Show social proof', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'social_text', [
			'label'     => esc_html__( 'Label', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Over 1,500 Happy Clients & Counting', 'agency-elementor-widgets' ),
			'condition' => [ 'show_social' => 'yes' ],
		] );

		$this->add_control( 'star_count', [
			'label'     => esc_html__( 'Star count', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 5,
			'min'       => 1,
			'max'       => 5,
			'condition' => [ 'show_social' => 'yes' ],
		] );

		foreach ( [ 1, 2, 3 ] as $i ) {
			$this->add_control( 'avatar_' . $i, [
				'label'     => sprintf(
					/* translators: %d: avatar number */
					esc_html__( 'Avatar %d', 'agency-elementor-widgets' ),
					$i
				),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/' . $i . '.png' ) ],
				'condition' => [ 'show_social' => 'yes' ],
			] );
		}

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ─────────────────────────────────────────────────────────

	private function style_layout(): void {
		$this->start_controls_section( 'ss_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'min_height', [
			'label'      => esc_html__( 'Section height', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [
				'px' => [ 'min' => 360, 'max' => 1200 ],
				'vh' => [ 'min' => 40, 'max' => 100 ],
			],
			// Desktop (≥1025px) is left empty on purpose so the stylesheet's
			// calc(100dvh - header) wins. Setting a value here emits per-element
			// CSS at higher specificity that would override it. Tablet/mobile
			// keep explicit defaults.
			'default'        => [ 'unit' => 'px', 'size' => '' ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 680 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 680 ],
			'selectors'  => [ '{{WRAPPER}} .aew-hev2' => 'min-height: {{SIZE}}{{UNIT}};' ],
			'description' => esc_html__( 'Desktop fills the viewport minus the header (100dvh − 64px). Set a value to override.', 'agency-elementor-widgets' ),
		] );

		$this->add_responsive_control( 'content_max_w', [
			'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px' ],
			'range'      => [
				'%'  => [ 'min' => 40, 'max' => 100 ],
				'px' => [ 'min' => 480, 'max' => 1600 ],
			],
			'default'    => [ 'unit' => '%', 'size' => 90 ],
			'selectors'  => [ '{{WRAPPER}} .aew-hev2__content' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'content_gap', [
			'label'      => esc_html__( 'Spacing between items', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 8, 'max' => 64 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 16 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-hev2__content' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_overlay(): void {
		$this->start_controls_section( 'ss_overlay', [ 'label' => esc_html__( 'Video overlay', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'overlay_color', [
			'label'     => esc_html__( 'Overlay color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#0A0F0D',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__overlay' => '--aew-hev2-overlay-color: {{VALUE}};' ],
		] );

		$this->add_control( 'overlay_opacity', [
			'label'      => esc_html__( 'Overlay opacity', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'custom' ],
			'range'      => [ 'custom' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'    => [ 'unit' => 'custom', 'size' => 0.45 ],
			'selectors'  => [ '{{WRAPPER}} .aew-hev2__overlay' => '--aew-hev2-overlay-opacity: {{SIZE}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_eyebrow(): void {
		$this->start_controls_section( 'ss_eyebrow', [ 'label' => esc_html__( 'Eyebrow', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'eyebrow_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__eyebrow' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'eyebrow_typo',
			'selector' => '{{WRAPPER}} .aew-hev2__eyebrow',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Playfair Display' ],
				'font_weight'    => [ 'default' => '700' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_headline(): void {
		$this->start_controls_section( 'ss_headline', [ 'label' => esc_html__( 'Headline', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'headline_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__headline' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'headline_typo',
			'selector' => '{{WRAPPER}} .aew-hev2__headline',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 80 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 0.85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_description(): void {
		$this->start_controls_section( 'ss_description', [ 'label' => esc_html__( 'Description', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'description_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.92)',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__description' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'description_typo',
			'selector' => '{{WRAPPER}} .aew-hev2__description',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.4 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_buttons(): void {
		$this->start_controls_section( 'ss_buttons', [ 'label' => esc_html__( 'Buttons', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		// Primary.
		$this->add_control( 'h_primary', [ 'label' => esc_html__( 'Primary', 'agency-elementor-widgets' ), 'type' => Controls_Manager::HEADING ] );
		// Colour controls assign to CSS vars on the wrapper (NOT direct
		// properties). The selector rule drives the editor's live preview;
		// render() ALSO emits the resolved value as an inline wrapper var so
		// global-bound colours survive on the front end (Elementor's CSS
		// generator drops globals for direct-property custom-control selectors).
		// The stylesheet consumes each var with a design-system fallback.
		$this->add_control( 'primary_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-pri-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'primary_color', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-pri-text: {{VALUE}};' ],
		] );
		$this->add_control( 'primary_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#6E4F2D',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-pri-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'primary_color_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-pri-text-hover: {{VALUE}};' ],
		] );

		// Secondary.
		$this->add_control( 'h_secondary', [ 'label' => esc_html__( 'Secondary (outline)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::HEADING, 'separator' => 'before' ] );
		$this->add_control( 'secondary_color', [
			'label'     => esc_html__( 'Text & border color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-sec-text: {{VALUE}};' ],
		] );
		$this->add_control( 'secondary_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-sec-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'secondary_color_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-hev2-sec-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'label'     => esc_html__( 'Button typography', 'agency-elementor-widgets' ),
			'selector'  => '{{WRAPPER}} .aew-hev2__btn',
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
			'selectors'  => [ '{{WRAPPER}} .aew-hev2__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_social(): void {
		$this->start_controls_section( 'ss_social', [ 'label' => esc_html__( 'Social proof', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_responsive_control( 'avatar_size', [
			'label'      => esc_html__( 'Avatar size', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 32, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 48 ],
			'selectors'  => [ '{{WRAPPER}} .aew-hev2__avatar-frame' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'star_color', [
			'label'     => esc_html__( 'Star color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#EBC543',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__stars' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'social_color', [
			'label'     => esc_html__( 'Label color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}} .aew-hev2__social-text' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'social_typo',
			'selector' => '{{WRAPPER}} .aew-hev2__social-text',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '700' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 15 ] ],
			],
		] );
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$video      = $s['video'] ?? [];
		$video_url  = is_array( $video ) ? ( $video['url'] ?? '' ) : '';
		if ( '' === $video_url ) {
			$video_url = $this->default_video_url();
		}
		$poster     = $s['poster'] ?? [];
		$poster_url = is_array( $poster ) ? ( $poster['url'] ?? '' ) : '';
		$mobile     = $s['mobile_image'] ?? [];
		$mobile_url = is_array( $mobile ) ? ( $mobile['url'] ?? '' ) : '';
		if ( '' === $mobile_url ) {
			$mobile_url = $this->default_mobile_image_url();
		}

		$eyebrow     = (string) ( $s['eyebrow'] ?? '' );
		$headline    = (string) ( $s['headline'] ?? '' );
		$description = (string) ( $s['description'] ?? '' );

		$primary      = $this->parse_link( $s['primary_link'] ?? [] );
		$secondary    = $this->parse_link( $s['secondary_link'] ?? [] );
		$primary_text = (string) ( $s['primary_text'] ?? '' );
		$secondary_text = (string) ( $s['secondary_text'] ?? '' );

		$show_social = 'yes' === ( $s['show_social'] ?? 'yes' );

		$this->add_render_attribute( 'wrapper', 'class', 'aew-hev2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-hero-v2', '' );

		/*
		 * Emit the resolved button colours as inline CSS variables on the wrapper.
		 * get_settings_for_display() resolves BOTH plain hex AND Elementor global
		 * colours (e.g. `globals/colors?id=notched-cards`) down to a hex string,
		 * so whatever the user picks in the editor — custom hex or a global — lands
		 * here. Elementor's front-end CSS generator silently drops global-bound
		 * custom-control values, which made the live page disagree with the editor;
		 * piping the resolved value through a CSS var (consumed in hero-v2.css with
		 * a design-system fallback) makes editor == live in every case.
		 */
		$btn_color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'primary_bg'            => '--aew-hev2-pri-bg',
				'primary_color'         => '--aew-hev2-pri-text',
				'primary_bg_hover'      => '--aew-hev2-pri-bg-hover',
				'primary_color_hover'   => '--aew-hev2-pri-text-hover',
				'secondary_color'       => '--aew-hev2-sec-text',
				'secondary_bg_hover'    => '--aew-hev2-sec-bg-hover',
				'secondary_color_hover' => '--aew-hev2-sec-text-hover',
			]
		);
		if ( '' !== $btn_color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $btn_color_vars );
		}
		?>
		<?php if ( $mobile_url ) : ?>
			<?php
			/*
			 * The mobile/tablet hero is a CSS background-image (≤1024px, see
			 * hero-v2.css), so the browser can't discover it until layout — it was
			 * the page's LCP element arriving seconds late. Preloading it from the
			 * markup with fetchpriority=high starts the download with the document.
			 */
			?>
			<link rel="preload" as="image" fetchpriority="high"
				href="<?php echo esc_url( $mobile_url ); ?>"
				media="(max-width: 1024px)">
		<?php endif; ?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<div class="aew-hev2__media" aria-hidden="true">
				<?php if ( $video_url ) : ?>
					<video class="aew-hev2__video"
						muted loop playsinline preload="none"
						data-src="<?php echo esc_url( $video_url ); ?>"
						<?php echo $poster_url ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>></video>
				<?php elseif ( $poster_url ) : ?>
					<div class="aew-hev2__video aew-hev2__video--poster" style="background-image: url('<?php echo esc_url( $poster_url ); ?>');"></div>
				<?php endif; ?>
				<?php if ( $mobile_url ) : ?>
					<div class="aew-hev2__mobile-image" style="background-image: url('<?php echo esc_url( $mobile_url ); ?>');"></div>
				<?php endif; ?>
				<div class="aew-hev2__overlay"></div>
			</div>

			<div class="aew-hev2__inner">
				<div class="aew-hev2__content">

					<?php if ( '' !== $eyebrow ) : ?>
						<?php
						$this->add_render_attribute( 'eyebrow', 'class', 'aew-hev2__eyebrow' );
						$this->add_inline_editing_attributes( 'eyebrow', 'none' );
						?>
						<p <?php $this->print_render_attribute_string( 'eyebrow' ); ?>><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $headline ) : ?>
						<?php
						$this->add_render_attribute( 'headline', 'class', 'aew-hev2__headline' );
						$this->add_inline_editing_attributes( 'headline', 'none' );
						?>
						<h1 <?php $this->print_render_attribute_string( 'headline' ); ?>><?php echo esc_html( $headline ); ?></h1>
					<?php endif; ?>

					<?php if ( '' !== $description ) : ?>
						<?php
						$this->add_render_attribute( 'description', 'class', 'aew-hev2__description' );
						$this->add_inline_editing_attributes( 'description', 'none' );
						?>
						<p <?php $this->print_render_attribute_string( 'description' ); ?>><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<?php if ( ( '' !== $primary_text && $primary['url'] ) || ( '' !== $secondary_text && $secondary['url'] ) ) : ?>
						<div class="aew-hev2__buttons">
							<?php if ( '' !== $primary_text && $primary['url'] ) : ?>
								<a class="aew-hev2__btn aew-hev2__btn--primary"
									href="<?php echo esc_url( $primary['url'] ); ?>"
									<?php echo $primary['target'] ? 'target="' . esc_attr( $primary['target'] ) . '"' : ''; ?>
									<?php echo $primary['rel'] ? 'rel="' . esc_attr( $primary['rel'] ) . '"' : ''; ?>>
									<?php echo esc_html( $primary_text ); ?>
								</a>
							<?php endif; ?>
							<?php if ( '' !== $secondary_text && $secondary['url'] ) : ?>
								<a class="aew-hev2__btn aew-hev2__btn--secondary"
									href="<?php echo esc_url( $secondary['url'] ); ?>"
									<?php echo $secondary['target'] ? 'target="' . esc_attr( $secondary['target'] ) . '"' : ''; ?>
									<?php echo $secondary['rel'] ? 'rel="' . esc_attr( $secondary['rel'] ) . '"' : ''; ?>>
									<?php echo esc_html( $secondary_text ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_social ) : ?>
						<?php $this->render_social_proof( $s ); ?>
					<?php endif; ?>

				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * @param array<string, mixed> $s Widget settings.
	 * @return void
	 */
	private function render_social_proof( array $s ): void {
		$stars = min( 5, max( 1, (int) ( $s['star_count'] ?? 5 ) ) );
		$label = (string) ( $s['social_text'] ?? '' );
		?>
		<div class="aew-hev2__social">
			<div class="aew-hev2__avatars" aria-hidden="true">
				<?php
				foreach ( [ 1, 2, 3 ] as $i ) {
					$avatar = $s[ 'avatar_' . $i ] ?? [];
					$url    = is_array( $avatar ) ? ( $avatar['url'] ?? '' ) : '';
					if ( $url ) {
						printf(
							'<span class="aew-hev2__avatar-frame"><img class="aew-hev2__avatar" src="%s" alt="" decoding="async" loading="eager" width="48" height="48" /></span>',
							esc_url( $url )
						);
					}
				}
				?>
			</div>
			<div class="aew-hev2__rating">
				<div class="aew-hev2__stars" aria-hidden="true">
					<?php for ( $n = 0; $n < $stars; $n++ ) : ?>
						<?php $this->render_star_icon(); ?>
					<?php endfor; ?>
				</div>
				<?php if ( '' !== $label ) : ?>
					<?php
					$this->add_render_attribute( 'social_text', 'class', 'aew-hev2__social-text' );
					$this->add_inline_editing_attributes( 'social_text', 'none' );
					?>
					<p <?php $this->print_render_attribute_string( 'social_text' ); ?>><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_star_icon(): void {
		?>
		<svg class="aew-hev2__star" width="18" height="18" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M9 1.5L11.04 6.63L16.5 7.24L12.45 11.07L13.59 16.44L9 13.77L4.41 16.44L5.55 11.07L1.5 7.24L6.96 6.63L9 1.5Z"/>
		</svg>
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
