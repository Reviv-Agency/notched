<?php
/**
 * Products Slider V2 — Notched brand.
 *
 * "Our most popular kit options" — a horizontal, scroll-snapping slider of
 * WooCommerce products. Each card shows the product image + title and links
 * to the product page. Products are sourced by category (one or more), with
 * optional hand-picked products, plus an optional "Shop all" CTA.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Products_Slider_V2 extends Widget_Base {

	private const ASSET_SLUG = 'products-slider-v2';

	public function get_name(): string      { return 'agency-products-slider-v2'; }
	public function get_title(): string     { return esc_html__( 'Products Slider V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-media-carousel'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'products', 'slider', 'carousel', 'grid', 'shop', 'woocommerce', 'kits', 'notched' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to our inner wrapper so the
	 * full-bleed background is preserved. Defaults left EMPTY — the stylesheet
	 * owns responsive X padding (see WIDGET-V2-BUILD-GUIDE §5 + gotcha #16).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-prsv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_header();
		$this->controls_source();
		$this->controls_cta();

		$this->style_body();
		$this->style_heading();
		$this->style_card();
		$this->style_arrows();
		$this->style_cta();
	}

	private function controls_header(): void {
		$this->start_controls_section( 's_header', [ 'label' => 'Heading' ] );
		$this->add_control( 'heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'Our most popular kit options',
		] );
		$this->add_control( 'heading_tag', [
			'label'   => 'Heading tag',
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => [ 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
		] );
		$this->add_control( 'subtext', [
			'label'       => 'Subtext (under heading)',
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 3,
			'default'     => '',
			'description' => 'Optional paragraph shown beneath the heading. Leave empty to hide.',
		] );
		$this->add_control( 'difficulty', [
			'label'       => 'Assembly difficulty',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. Beginner, Intermediate',
			'description' => 'Shown top-right above the slider. Leave empty to hide.',
		] );
		$this->add_control( 'difficulty_prefix', [
			'label'     => 'Difficulty label prefix',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Assembly Difficulty:',
			'condition' => [ 'difficulty!' => '' ],
		] );
		$this->end_controls_section();
	}

	private function controls_source(): void {
		$this->start_controls_section( 's_source', [ 'label' => 'Products' ] );

		$this->add_control( 'layout', [
			'label'   => 'Layout',
			'type'    => Controls_Manager::SELECT,
			'default' => 'slider',
			'options' => [
				'slider' => 'Slider (carousel)',
				'grid'   => 'Grid (wrap, no carousel)',
			],
		] );

		$this->add_responsive_control( 'grid_columns', [
			'label'          => 'Grid columns',
			'type'           => Controls_Manager::SELECT,
			'default'        => '4',
			'tablet_default' => '3',
			'mobile_default' => '2',
			'options'        => [ '2' => '2', '3' => '3', '4' => '4', '5' => '5' ],
			'condition'      => [ 'layout' => 'grid' ],
			'selectors'      => [ '{{WRAPPER}} .aew-prsv2__track' => '--aew-prsv2-grid-cols: {{VALUE}};' ],
		] );

		if ( ! self::woo_active() ) {
			$this->add_control( 'woo_notice', [
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<strong>WooCommerce is not active.</strong> Activate it to populate this slider with products.',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			] );
		}

		$this->add_control( 'source', [
			'label'   => 'Source',
			'type'    => Controls_Manager::SELECT,
			'default' => 'category',
			'options' => [
				'category' => 'By category',
				'manual'   => 'Hand-picked only',
				'featured' => 'Featured products',
			],
		] );

		$this->add_control( 'categories', [
			'label'       => 'Categories',
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label_block' => true,
			'options'     => self::category_options(),
			'description' => 'Leave empty to pull from all categories.',
			'condition'   => [ 'source' => 'category' ],
		] );

		$this->add_control( 'manual_ids', [
			'label'       => 'Pick products',
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label_block' => true,
			'options'     => self::product_options(),
			'description' => 'Shown first, in the order picked. With "By category" source these are added before the category results.',
			'condition'   => [ 'source!' => 'featured' ],
		] );

		$this->add_control( 'limit', [
			'label'   => 'Max products',
			'type'    => Controls_Manager::NUMBER,
			'default' => 12,
			'min'     => 1,
			'max'     => 48,
			'condition' => [ 'source!' => 'manual' ],
		] );

		$this->add_control( 'orderby', [
			'label'   => 'Order by',
			'type'    => Controls_Manager::SELECT,
			'default' => 'menu_order',
			'options' => [
				'menu_order'  => 'Default (menu order)',
				'popularity'  => 'Popularity (sales)',
				'date'        => 'Newest',
				'title'       => 'Title (A–Z)',
				'price'       => 'Price (low → high)',
				'rand'        => 'Random',
			],
			'condition' => [ 'source!' => 'manual' ],
		] );

		$this->add_control( 'hide_out_of_stock', [
			'label'   => 'Hide out-of-stock',
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->add_control( 'open_new_tab', [
			'label'   => 'Open products in new tab',
			'type'    => Controls_Manager::SWITCHER,
			'default' => '',
		] );

		$this->end_controls_section();
	}

	private function controls_cta(): void {
		$this->start_controls_section( 's_cta', [ 'label' => 'Shop all button' ] );
		$this->add_control( 'show_cta', [ 'label' => 'Show button', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
		$this->add_control( 'cta_label', [
			'label'     => 'Label',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Shop all kits',
			'condition' => [ 'show_cta' => 'yes' ],
		] );
		$this->add_control( 'cta_link', [
			'label'     => 'Link',
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' ) ],
			'condition' => [ 'show_cta' => 'yes' ],
		] );
		$this->add_control( 'cta_arrow', [
			'label'     => 'Show arrow',
			'type'      => Controls_Manager::SWITCHER,
			'default'   => 'yes',
			'condition' => [ 'show_cta' => 'yes' ],
		] );
		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => 'Body', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'body_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-body-bg: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'heading_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-heading: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-prsv2__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 64 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		// Subtext (paragraph under the heading).
		$this->add_control( 'subtext_color', [
			'label'     => 'Subtext color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-subtext: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'subtext_typo',
			'selector' => '{{WRAPPER}} .aew-prsv2__subtext',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.4 ] ],
			],
		] );

		// ── Assembly difficulty — fully customizable ─────────────────────────
		// The label ("ASSEMBLY DIFFICULTY:") and the value ("BEGINNER") each get
		// their own colour + typography. A small dot precedes the value.
		$this->add_control( 'h_difficulty', [
			'label'     => 'Assembly difficulty',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		// Label.
		$this->add_control( 'difficulty_color', [
			'label'     => 'Label color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-difficulty: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'difficulty_label_typo',
			'label'    => 'Label typography',
			'selector' => '{{WRAPPER}} .aew-prsv2__difficulty-label',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Playfair Display' ],
				'font_weight'    => [ 'default' => '700' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		// Value (the "Beginner" / "Intermediate" part).
		$this->add_control( 'difficulty_value_color', [
			'label'     => 'Value color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'separator' => 'before',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-difficulty-value: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'difficulty_value_typo',
			'label'    => 'Value typography',
			'selector' => '{{WRAPPER}} .aew-prsv2__difficulty-value',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Lato' ],
				'font_weight'    => [ 'default' => '400' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		// Dot before the value.
		$this->add_control( 'difficulty_dot', [
			'label'        => 'Show dot before value',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'separator'    => 'before',
		] );
		$this->add_control( 'difficulty_dot_color', [
			'label'     => 'Dot color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'condition' => [ 'difficulty_dot' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-difficulty-dot: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => 'Cards', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_responsive_control( 'card_width', [
			'label'      => 'Card width',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 700 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 500 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 440 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 220 ],
			'selectors'  => [ '{{WRAPPER}} .aew-prsv2__slide' => 'flex-basis: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'card_height', [
			'label'       => 'Image height',
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px' ],
			'range'       => [ 'px' => [ 'min' => 200, 'max' => 700 ] ],
			'default'        => [ 'unit' => 'px', 'size' => '' ],
			'tablet_default' => [ 'unit' => 'px', 'size' => '' ],
			'mobile_default' => [ 'unit' => 'px', 'size' => '' ],
			'description' => 'Leave empty to keep the default square (1:1) image. Set a value to use a fixed image height instead.',
			'selectors'   => [ '{{WRAPPER}} .aew-prsv2__media' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;' ],
		] );
		$this->add_control( 'card_radius', [
			'label'      => 'Image radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-prsv2__media' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'title_color', [
			'label'     => 'Title color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-title: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-prsv2__title',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 22 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->add_control( 'show_price', [
			'label'   => 'Show price',
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );
		$this->add_control( 'price_prefix', [
			'label'     => 'Price prefix',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'From ',
			'condition' => [ 'show_price' => 'yes' ],
		] );
		$this->add_control( 'price_color', [
			'label'     => 'Price color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-price: {{VALUE}};' ],
			'condition' => [ 'show_price' => 'yes' ],
		] );

		// ── Corner badge (top-left of the image; text = product category) ─────
		$this->add_control( 'badge_heading', [
			'label'     => 'Corner badge',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'show_badge', [
			'label'       => 'Show category badge',
			'type'        => Controls_Manager::SWITCHER,
			'default'     => '',
			'description' => 'Shows each product’s category (e.g. DIY KIT, TRADITIONAL) as a pill in the top-left corner of the image.',
		] );
		$this->add_control( 'badge_bg', [
			'label'     => 'Badge background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'condition' => [ 'show_badge' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-badge-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'badge_text_color', [
			'label'     => 'Badge text color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'condition' => [ 'show_badge' => 'yes' ],
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-badge-text: {{VALUE}};' ],
		] );
		$this->add_control( 'badge_radius', [
			'label'      => 'Badge corner radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'condition'  => [ 'show_badge' => 'yes' ],
			'selectors'  => [ '{{WRAPPER}} .aew-prsv2__badge' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'badge_typo',
			'label'     => 'Badge typography',
			'selector'  => '{{WRAPPER}} .aew-prsv2__badge',
			'condition' => [ 'show_badge' => 'yes' ],
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		// ── Quick View band (revealed on hover, over the bottom of the image) ──
		$this->add_control( 'quick_view_heading', [
			'label'     => 'Quick View',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'show_quick_view', [
			'label'   => 'Show Quick View on hover',
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );
		$this->add_control( 'quick_view_text', [
			'label'     => 'Quick View label',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Quick View',
			'condition' => [ 'show_quick_view' => 'yes' ],
		] );
		$this->add_control( 'quick_view_bg', [
			'label'     => 'Quick View background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-qv-bg: {{VALUE}};' ],
			'condition' => [ 'show_quick_view' => 'yes' ],
		] );
		$this->add_control( 'quick_view_color', [
			'label'     => 'Quick View text color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-qv-text: {{VALUE}};' ],
			'condition' => [ 'show_quick_view' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	private function style_arrows(): void {
		$this->start_controls_section( 'ss_arrows', [ 'label' => 'Arrows & dots', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'arrow_bg', [
			'label'     => 'Arrow background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-arrow-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'arrow_color', [
			'label'     => 'Arrow icon color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-arrow-color: {{VALUE}};' ],
		] );
		$this->add_control( 'dot_color', [
			'label'     => 'Dot color (active)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#3B413F',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-dot-active: {{VALUE}};' ],
		] );
		$this->add_control( 'dot_color_idle', [
			'label'     => 'Dot color (idle)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#BFC0BF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-dot-idle: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_cta(): void {
		$this->start_controls_section( 'ss_cta', [ 'label' => 'Shop all button', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_cta' => 'yes' ] ] );
		$this->add_control( 'cta_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-cta-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'cta_bg_hover', [
			'label'     => 'Background (hover)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-cta-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'cta_text', [
			'label'     => 'Text color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-cta-text: {{VALUE}};' ],
		] );
		$this->add_control( 'cta_text_hover', [
			'label'     => 'Text color (hover)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-prsv2-cta-text-hover: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'cta_typo',
			'selector' => '{{WRAPPER}} .aew-prsv2__cta',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$heading  = (string) ( $s['heading'] ?? '' );
		$tag      = in_array( $s['heading_tag'] ?? 'h2', [ 'h2', 'h3', 'div' ], true ) ? $s['heading_tag'] : 'h2';
		$products = $this->query_products( $s );
		$new_tab  = 'yes' === ( $s['open_new_tab'] ?? '' );
		$is_grid  = 'grid' === ( $s['layout'] ?? 'slider' );

		$cta = $this->parse_link( $s['cta_link'] ?? [] );
		// Fall back to the shop page when the link is empty so an enabled button
		// isn't silently suppressed just because the URL field wasn't filled.
		// wc_get_page_id('shop') returns -1/0 when the WooCommerce shop page
		// isn't set, and get_permalink() of that is false — so guard for a real
		// permalink and otherwise use /shop/.
		if ( empty( $cta['url'] ) ) {
			$shop_url = '';
			if ( function_exists( 'wc_get_page_id' ) ) {
				$shop_id = (int) wc_get_page_id( 'shop' );
				if ( $shop_id > 0 ) {
					$perm = get_permalink( $shop_id );
					if ( is_string( $perm ) && '' !== $perm ) {
						$shop_url = $perm;
					}
				}
			}
			$cta['url'] = '' !== $shop_url ? $shop_url : home_url( '/shop/' );
		}
		// Respect the control's `yes` DEFAULT for saved instances that predate the
		// control (a missing value must read as ON — gotcha #17). Only an explicit
		// empty string (toggled off) hides the button.
		$show_cta = 'yes' === ( $s['show_cta'] ?? 'yes' ) && ! empty( $cta['url'] );

		$show_price   = 'yes' === ( $s['show_price'] ?? 'yes' );
		$price_prefix = (string) ( $s['price_prefix'] ?? '' );

		// Corner category badge: default OFF (opt-in).
		$show_badge = 'yes' === ( $s['show_badge'] ?? '' );

		// Quick View band: default ON for saved instances that predate the control.
		$show_quick_view = 'yes' === ( $s['show_quick_view'] ?? 'yes' );
		$quick_view_text = (string) ( $s['quick_view_text'] ?? '' );
		if ( '' === trim( $quick_view_text ) ) {
			$quick_view_text = esc_html__( 'Quick View', 'agency-elementor-widgets' );
		}

		$subtext           = (string) ( $s['subtext'] ?? '' );
		$difficulty        = trim( (string) ( $s['difficulty'] ?? '' ) );
		$difficulty_prefix = (string) ( $s['difficulty_prefix'] ?? '' );
		$difficulty_dot    = 'yes' === ( $s['difficulty_dot'] ?? 'yes' );

		/*
		 * Resolved colours as inline CSS vars on the wrapper. get_settings_for_display()
		 * resolves global colours → hex so global-bound picks render on the front end
		 * (Elementor drops globals for direct-property custom-control selectors). The
		 * matching `selectors` on each control drive the editor preview; this inline
		 * value wins on live. CSS consumes each var with a design-system fallback.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'body_bg'                => '--aew-prsv2-body-bg',
				'heading_color'          => '--aew-prsv2-heading',
				'subtext_color'          => '--aew-prsv2-subtext',
				'difficulty_color'       => '--aew-prsv2-difficulty',
				'difficulty_value_color' => '--aew-prsv2-difficulty-value',
				'difficulty_dot_color'   => '--aew-prsv2-difficulty-dot',
				'title_color'            => '--aew-prsv2-title',
				'badge_bg'         => '--aew-prsv2-badge-bg',
				'badge_text_color' => '--aew-prsv2-badge-text',
				'price_color'      => '--aew-prsv2-price',
				'arrow_bg'         => '--aew-prsv2-arrow-bg',
				'arrow_color'      => '--aew-prsv2-arrow-color',
				'dot_color'        => '--aew-prsv2-dot-active',
				'dot_color_idle'   => '--aew-prsv2-dot-idle',
				'cta_bg'           => '--aew-prsv2-cta-bg',
				'cta_bg_hover'     => '--aew-prsv2-cta-bg-hover',
				'cta_text'         => '--aew-prsv2-cta-text',
				'cta_text_hover'   => '--aew-prsv2-cta-text-hover',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';
		?>
		<section class="aew-prsv2<?php echo $is_grid ? ' aew-prsv2--grid' : ''; ?>" <?php echo $is_grid ? '' : 'data-aew-products-slider-v2'; ?><?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value escaped via esc_attr above ?>>
			<div class="aew-prsv2__inner">

				<div class="aew-prsv2__head">
					<div class="aew-prsv2__head-main">
						<?php if ( $heading ) : ?>
							<<?php echo esc_attr( $tag ); ?> class="aew-prsv2__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $tag ); ?>>
						<?php endif; ?>
						<?php if ( '' !== $subtext ) : ?>
							<p class="aew-prsv2__subtext"><?php echo esc_html( $subtext ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( '' !== $difficulty || $show_cta ) : ?>
						<div class="aew-prsv2__head-aside">
							<?php if ( '' !== $difficulty ) : ?>
								<span class="aew-prsv2__difficulty">
									<?php if ( '' !== $difficulty_prefix ) : ?>
										<span class="aew-prsv2__difficulty-label"><?php echo esc_html( $difficulty_prefix ); ?></span>
									<?php endif; ?>
									<span class="aew-prsv2__difficulty-value">
										<?php if ( $difficulty_dot ) : ?><span class="aew-prsv2__difficulty-dot" aria-hidden="true"></span><?php endif; ?>
										<?php echo esc_html( $difficulty ); ?>
									</span>
								</span>
							<?php endif; ?>

							<?php if ( $show_cta ) : ?>
								<a class="aew-prsv2__cta"
									href="<?php echo esc_url( $cta['url'] ); ?>"
									<?php echo $cta['target'] ? 'target="' . esc_attr( $cta['target'] ) . '"' : ''; ?>
									<?php echo $cta['rel'] ? 'rel="' . esc_attr( $cta['rel'] ) . '"' : ''; ?>>
									<span class="aew-prsv2__cta-label"><?php echo esc_html( $s['cta_label'] ?? '' ); ?></span>
									<?php if ( 'yes' === ( $s['cta_arrow'] ?? '' ) ) : ?>
										<?php echo $this->arrow_svg( 'right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( empty( $products ) ) : ?>
					<p class="aew-prsv2__empty">
						<?php
						echo self::woo_active()
							? esc_html__( 'No products found for the selected source.', 'agency-elementor-widgets' )
							: esc_html__( 'WooCommerce is not active.', 'agency-elementor-widgets' );
						?>
					</p>
				<?php else : ?>
					<div class="aew-prsv2__viewport">
						<?php if ( ! $is_grid ) : ?>
							<button type="button" class="aew-prsv2__arrow aew-prsv2__arrow--prev" data-aew-prs-prev aria-label="<?php esc_attr_e( 'Previous products', 'agency-elementor-widgets' ); ?>">
								<?php echo $this->arrow_svg( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						<?php endif; ?>

						<ul class="aew-prsv2__track"<?php echo $is_grid ? '' : ' data-aew-prs-track'; ?>>
							<?php foreach ( $products as $p ) : ?>
								<li class="aew-prsv2__slide">
									<a class="aew-prsv2__card"
										href="<?php echo esc_url( $p['url'] ); ?>"
										<?php echo $new_tab ? 'target="_blank" rel="noopener"' : ''; ?>>
										<span class="aew-prsv2__media"<?php echo $p['img'] ? ' style="background-image:url(\'' . esc_url( $p['img'] ) . '\');"' : ''; ?>
											role="img"
											aria-label="<?php echo esc_attr( $p['title'] ); ?>">
											<?php if ( $show_badge && '' !== ( $p['badge'] ?? '' ) ) : ?>
												<span class="aew-prsv2__badge"><?php echo esc_html( $p['badge'] ); ?></span>
											<?php endif; ?>
											<?php if ( $show_quick_view ) : ?>
												<span class="aew-prsv2__quick" aria-hidden="true"><?php echo esc_html( $quick_view_text ); ?></span>
											<?php endif; ?>
										</span>
										<span class="aew-prsv2__title"><?php echo esc_html( $p['title'] ); ?></span>
										<?php if ( $show_price && '' !== $p['price'] ) : ?>
											<span class="aew-prsv2__price"><?php echo esc_html( $price_prefix . $p['price'] ); ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php if ( ! $is_grid ) : ?>
							<button type="button" class="aew-prsv2__arrow aew-prsv2__arrow--next" data-aew-prs-next aria-label="<?php esc_attr_e( 'Next products', 'agency-elementor-widgets' ); ?>">
								<?php echo $this->arrow_svg( 'right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						<?php endif; ?>
					</div>

					<?php if ( ! $is_grid ) : ?>
						<div class="aew-prsv2__dots" data-aew-prs-dots aria-hidden="true"></div>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		</section>
		<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// DATA
	// ─────────────────────────────────────────────────────────────────────────

	private static function woo_active(): bool {
		return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_product' );
	}

	/**
	 * Build the normalized product list for render.
	 *
	 * @param array<string,mixed> $s Settings.
	 * @return array<int, array{title:string,url:string,img:string,price:string}>
	 */
	private function query_products( array $s ): array {
		if ( ! self::woo_active() ) {
			return [];
		}

		$source  = $s['source'] ?? 'category';
		$limit   = max( 1, (int) ( $s['limit'] ?? 12 ) );
		$orderby = (string) ( $s['orderby'] ?? 'menu_order' );
		$ids     = [];

		// Manual picks first (only for non-featured sources).
		$manual = array_filter( array_map( 'intval', (array) ( $s['manual_ids'] ?? [] ) ) );
		if ( 'featured' !== $source ) {
			$ids = $manual;
		}

		if ( 'manual' !== $source ) {
			$args = [
				'status'   => 'publish',
				'limit'    => $limit,
				'orderby'  => 'price' === $orderby ? 'meta_value_num' : $orderby,
				'order'    => in_array( $orderby, [ 'title' ], true ) ? 'ASC' : ( 'price' === $orderby ? 'ASC' : 'DESC' ),
				'return'   => 'ids',
				'exclude'  => $ids,
			];
			if ( 'menu_order' === $orderby ) {
				$args['orderby'] = 'menu_order';
				$args['order']   = 'ASC';
			}
			if ( 'price' === $orderby ) {
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			}
			if ( 'yes' === ( $s['hide_out_of_stock'] ?? '' ) ) {
				$args['stock_status'] = 'instock';
			}

			if ( 'featured' === $source ) {
				$args['featured'] = true;
			} elseif ( 'category' === $source ) {
				$cats = array_filter( array_map( 'intval', (array) ( $s['categories'] ?? [] ) ) );
				if ( ! empty( $cats ) ) {
					$args['category'] = $this->term_slugs( $cats );
				}
			}

			$found = wc_get_products( $args );
			$ids   = array_merge( $ids, array_map( 'intval', (array) $found ) );
		}

		// De-dupe while preserving order; cap at limit (manual-only ignores limit).
		$ids = array_values( array_unique( array_filter( $ids ) ) );
		if ( 'manual' !== $source ) {
			$ids = array_slice( $ids, 0, $limit );
		}

		$out = [];
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( ! $product || 'publish' !== $product->get_status() ) {
				continue;
			}
			if ( 'yes' === ( $s['hide_out_of_stock'] ?? '' ) && ! $product->is_in_stock() ) {
				continue;
			}
			$img_id = $product->get_image_id();
			$img    = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : wc_placeholder_img_src( 'large' );
			$out[]  = [
				'title' => $product->get_name(),
				'url'   => get_permalink( $id ),
				'img'   => $img ? $img : '',
				'price' => $this->format_price( $product->get_price() ),
				'badge' => $this->product_badge( $id ),
			];
		}
		return $out;
	}

	/**
	 * Turn a WooCommerce price value into a plain "$4,290" style string.
	 * Empty / zero / non-numeric prices return '' (render nothing).
	 *
	 * @param mixed $price Raw product price.
	 */
	private function format_price( $price ): string {
		if ( '' === $price || null === $price || ! is_numeric( $price ) ) {
			return '';
		}
		$amount = (float) $price;
		if ( $amount <= 0 ) {
			return '';
		}
		$symbol   = function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : '$';
		$decimals = ( floor( $amount ) === $amount ) ? 0 : 2;
		return $symbol . number_format( $amount, $decimals );
	}

	/**
	 * Map product_cat term IDs to slugs (wc_get_products 'category' takes slugs).
	 *
	 * @param array<int,int> $term_ids
	 * @return array<int,string>
	 */
	private function term_slugs( array $term_ids ): array {
		$slugs = [];
		foreach ( $term_ids as $tid ) {
			$term = get_term( $tid, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$slugs[] = $term->slug;
			}
		}
		return $slugs;
	}

	/**
	 * Corner badge text for a product = its primary (first) product category
	 * name, excluding the catch-all "Uncategorized". Returns '' when none.
	 *
	 * @param int $product_id
	 * @return string
	 */
	private function product_badge( int $product_id ): string {
		$terms = get_the_terms( $product_id, 'product_cat' );
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return '';
		}
		foreach ( $terms as $t ) {
			if ( ! is_object( $t ) || empty( $t->name ) ) {
				continue;
			}
			if ( 'uncategorized' === ( $t->slug ?? '' ) ) {
				continue;
			}
			return (string) $t->name;
		}
		return '';
	}

	/**
	 * @return array<string,string> term_id => "Name (count)"
	 */
	private static function category_options(): array {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return [];
		}
		$terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
		$opts  = [];
		if ( is_array( $terms ) ) {
			foreach ( $terms as $t ) {
				$opts[ (string) $t->term_id ] = sprintf( '%s (%d)', $t->name, $t->count );
			}
		}
		return $opts;
	}

	/**
	 * @return array<string,string> product_id => "Title"
	 */
	private static function product_options(): array {
		if ( ! self::woo_active() ) {
			return [];
		}
		$ids  = wc_get_products( [ 'status' => 'publish', 'limit' => 200, 'orderby' => 'title', 'order' => 'ASC', 'return' => 'ids' ] );
		$opts = [];
		foreach ( (array) $ids as $id ) {
			$opts[ (string) $id ] = get_the_title( $id );
		}
		return $opts;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	private function arrow_svg( string $dir ): string {
		$d = 'left' === $dir
			? 'M15 5l-7 7 7 7'   // chevron left
			: 'M9 5l7 7-7 7';    // chevron right
		return '<svg class="aew-prsv2__chev" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="' . esc_attr( $d ) . '"/></svg>';
	}

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
