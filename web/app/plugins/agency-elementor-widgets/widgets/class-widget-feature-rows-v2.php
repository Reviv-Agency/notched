<?php
/**
 * Feature Rows V2 — Notched brand.
 *
 * A flexible grid band built from up to 4 SEPARATE rows. Each row is its own
 * self-contained control group: a column count (2 or 3) and that many slots
 * (A/B/C). Every slot is either an Image or a Text Box, so the editor mixes
 * layouts row by row — e.g. a row of "2 images + 1 text box" above a row of
 * "1 image + 2 text boxes". Each row can be hidden, so you use only what you
 * need. (Elementor's repeater can't nest a repeater inside a repeater item, so
 * rows/slots are flat, generated controls rather than nested repeaters.)
 *
 * Image slots stretch to the row's height (object-fit: cover) so a tall text
 * box and a shorter image stay flush. Text boxes are fully configurable:
 * alignment, optional CTA button, colours and sizes — including the box
 * background.
 *
 * Owns its own full-bleed background. Mirrors the Region Cards V2 / Welcome V2
 * conventions (see WIDGET-V2-BUILD-GUIDE.md).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Feature_Rows_V2 extends Widget_Base {

	private const ASSET_SLUG = 'feature-rows-v2';

	public function get_name(): string      { return 'agency-feature-rows-v2'; }
	public function get_title(): string     { return esc_html__( 'Feature Rows V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-gallery-grid'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'feature', 'rows', 'grid', 'cards', 'media', 'text', 'notched' ]; }

	public function get_style_depends(): array { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to the inner wrapper so the
	 * outer block keeps its full-bleed background. Leave defaults EMPTY so the
	 * stylesheet owns responsive X padding (WIDGET-V2-BUILD-GUIDE §5/§6.5).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-frv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	private const ROW_COUNT = 4;
	private const SLOTS     = [ 'a', 'b', 'c' ];

	protected function register_controls(): void {
		$this->controls_header();
		$this->controls_rows();

		$this->style_section();
		$this->style_header();
		$this->style_image();
		$this->style_text_box();
		$this->style_title();
		$this->style_text();
		$this->style_button();
	}

	private function controls_header(): void {
		$this->start_controls_section( 's_header', [ 'label' => esc_html__( 'Section header', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'show_header', [
			'label'        => esc_html__( 'Show section header', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'description'  => esc_html__( 'Heading on the left, optional button on the right, above the grid.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'heading', [
			'label'     => esc_html__( 'Heading', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXTAREA,
			'default'   => esc_html__( 'What makes Notched different?', 'agency-elementor-widgets' ),
			'condition' => [ 'show_header' => 'yes' ],
		] );

		$this->add_control( 'header_btn_text', [
			'label'     => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Book Now', 'agency-elementor-widgets' ),
			'condition' => [ 'show_header' => 'yes' ],
			'description' => esc_html__( 'Leave empty to hide the header button.', 'agency-elementor-widgets' ),
		] );

		$this->add_control( 'header_btn_link', [
			'label'     => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => '#' ],
			'condition' => [ 'show_header' => 'yes' ],
		] );

		$this->add_control( 'header_btn_arrow', [
			'label'        => esc_html__( 'Show arrow on button', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'condition'    => [ 'show_header' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	/**
	 * Sensible starter content per (row, slot). Keeps the widget looking like the
	 * reference the moment it's dropped in. row/slot are 1-based row + a/b/c.
	 *
	 * @return array<string, mixed>
	 */
	private function slot_defaults(): array {
		$img1 = Widget_Assets::url( self::ASSET_SLUG, 'images/feature-1.webp' );
		$img2 = Widget_Assets::url( self::ASSET_SLUG, 'images/feature-2.webp' );
		$img3 = Widget_Assets::url( self::ASSET_SLUG, 'images/feature-3.webp' );

		return [
			// Row 1: image · text · image
			'row1' => [ 'cols' => '3', 'show' => 'yes' ],
			'row1a' => [ 'type' => 'image', 'image' => $img1 ],
			'row1b' => [ 'type' => 'text', 'title' => esc_html__( 'Unmatched Strength', 'agency-elementor-widgets' ), 'body' => '<p>' . esc_html__( 'Built with the thickest timber sizes, your pergola withstands the elements with ease.', 'agency-elementor-widgets' ) . '</p>', 'align' => 'center', 'bg' => '' ],
			'row1c' => [ 'type' => 'image', 'image' => $img2 ],
			// Row 2: text · image · text
			'row2' => [ 'cols' => '3', 'show' => 'yes' ],
			'row2a' => [ 'type' => 'text', 'title' => esc_html__( 'Extended Shade Coverage', 'agency-elementor-widgets' ), 'body' => '<p>' . esc_html__( 'With extra wood overhead, you\'ll enjoy maximum shade and comfort.', 'agency-elementor-widgets' ) . '</p>', 'align' => 'center', 'bg' => '' ],
			'row2b' => [ 'type' => 'image', 'image' => $img3 ],
			'row2c' => [ 'type' => 'text', 'title' => esc_html__( 'Solid Wood Construction', 'agency-elementor-widgets' ), 'body' => '<p>' . esc_html__( 'We never use hollow timbers — each beam is crafted from solid, rough-sawn Douglas Fir for lasting durability.', 'agency-elementor-widgets' ) . '</p>', 'align' => 'center', 'bg' => '' ],
		];
	}

	private function controls_rows(): void {
		$defaults = $this->slot_defaults();

		for ( $r = 1; $r <= self::ROW_COUNT; $r++ ) {
			$row_d = $defaults[ "row{$r}" ] ?? [ 'cols' => '3', 'show' => ( 1 === $r ? 'yes' : '' ) ];

			$this->start_controls_section(
				"s_row{$r}",
				[ 'label' => sprintf( /* translators: %d: row number */ esc_html__( 'Row %d', 'agency-elementor-widgets' ), $r ) ]
			);

			$this->add_control( "row{$r}_show", [
				'label'        => esc_html__( 'Show this row', 'agency-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => $row_d['show'] ?? '',
				'return_value' => 'yes',
			] );

			$this->add_control( "row{$r}_cols", [
				'label'     => esc_html__( 'Items in this row', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => $row_d['cols'] ?? '3',
				'options'   => [
					'2' => esc_html__( '2 items', 'agency-elementor-widgets' ),
					'3' => esc_html__( '3 items', 'agency-elementor-widgets' ),
				],
				'condition' => [ "row{$r}_show" => 'yes' ],
			] );

			foreach ( self::SLOTS as $slot ) {
				// Slot C only applies when the row has 3 items.
				$slot_cond = [ "row{$r}_show" => 'yes' ];
				if ( 'c' === $slot ) {
					$slot_cond[ "row{$r}_cols" ] = '3';
				}
				$this->register_slot_controls( $r, $slot, strtoupper( $slot ), $slot_cond, $defaults );
			}

			$this->end_controls_section();
		}
	}

	/**
	 * Register the flat controls for one slot (image OR text box).
	 *
	 * @param int                  $r        Row number (1-based).
	 * @param string               $slot     Slot key (a|b|c).
	 * @param string               $label    Display label (A|B|C).
	 * @param array<string,string> $base_cond Visibility condition for the whole slot.
	 * @param array<string,mixed>  $defaults slot_defaults() map.
	 * @return void
	 */
	private function register_slot_controls( int $r, string $slot, string $label, array $base_cond, array $defaults ): void {
		$p     = "row{$r}_{$slot}_";                 // control-id prefix
		$d     = $defaults[ "row{$r}{$slot}" ] ?? []; // starter content
		$type  = $d['type'] ?? ( 'image' );
		$type_key = $p . 'type';

		$img_cond  = $base_cond + [ $type_key => 'image' ];
		$text_cond = $base_cond + [ $type_key => 'text' ];

		$this->add_control( $p . 'heading', [
			'type'      => Controls_Manager::HEADING,
			/* translators: %s: slot letter */
			'label'     => sprintf( esc_html__( 'Item %s', 'agency-elementor-widgets' ), $label ),
			'separator' => 'before',
			'condition' => $base_cond,
		] );

		$this->add_control( $type_key, [
			'label'     => esc_html__( 'Type', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => $type,
			'options'   => [
				'image' => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'text'  => esc_html__( 'Text box', 'agency-elementor-widgets' ),
			],
			'condition' => $base_cond,
		] );

		// ── Image ─────────────────────────────────────────────────────────────
		$this->add_control( $p . 'image', [
			'label'     => esc_html__( 'Image', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::MEDIA,
			'default'   => [ 'url' => $d['image'] ?? Widget_Assets::url( self::ASSET_SLUG, 'images/feature-1.webp' ) ],
			'condition' => $img_cond,
		] );

		$this->add_control( $p . 'image_alt', [
			'label'       => esc_html__( 'Image alt text', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => esc_html__( 'Describe the image for accessibility. Leave empty if decorative.', 'agency-elementor-widgets' ),
			'condition'   => $img_cond,
		] );

		$this->add_control( $p . 'image_focal', [
			'label'     => esc_html__( 'Focal point', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'center center',
			'options'   => [
				'center center' => esc_html__( 'Center', 'agency-elementor-widgets' ),
				'center top'    => esc_html__( 'Top', 'agency-elementor-widgets' ),
				'center bottom' => esc_html__( 'Bottom', 'agency-elementor-widgets' ),
				'left center'   => esc_html__( 'Left', 'agency-elementor-widgets' ),
				'right center'  => esc_html__( 'Right', 'agency-elementor-widgets' ),
			],
			'condition' => $img_cond,
			'selectors' => [ '{{WRAPPER}} .aew-frv2__slot-' . $r . $slot . ' .aew-frv2__img' => 'object-position: {{VALUE}};' ],
		] );

		// ── Text box ────────────────────────────────────────────────────────
		$this->add_control( $p . 'title', [
			'label'     => esc_html__( 'Title', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => $d['title'] ?? esc_html__( 'Box title', 'agency-elementor-widgets' ),
			'condition' => $text_cond,
		] );

		$this->add_control( $p . 'body', [
			'label'     => esc_html__( 'Body', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::WYSIWYG,
			'default'   => $d['body'] ?? ( '<p>' . esc_html__( 'Box description.', 'agency-elementor-widgets' ) . '</p>' ),
			'condition' => $text_cond,
		] );

		$this->add_control( $p . 'align', [
			'label'     => esc_html__( 'Text alignment', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => $d['align'] ?? 'left',
			'options'   => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ),   'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ),  'icon' => 'eicon-text-align-right' ],
			],
			'condition' => $text_cond,
			'selectors' => [ '{{WRAPPER}} .aew-frv2__slot-' . $r . $slot . ' .aew-frv2__box' => 'text-align: {{VALUE}}; --aew-frv2-box-align: {{VALUE}};' ],
		] );

		$this->add_control( $p . 'show_button', [
			'label'        => esc_html__( 'Show button', 'agency-elementor-widgets' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => $d['show_button'] ?? '',
			'return_value' => 'yes',
			'condition'    => $text_cond,
		] );

		$this->add_control( $p . 'btn_text', [
			'label'     => esc_html__( 'Button text', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'Learn More', 'agency-elementor-widgets' ),
			'condition' => $text_cond + [ $p . 'show_button' => 'yes' ],
		] );

		$this->add_control( $p . 'btn_link', [
			'label'     => esc_html__( 'Button link', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::URL,
			'default'   => [ 'url' => '#' ],
			'condition' => $text_cond + [ $p . 'show_button' => 'yes' ],
		] );

		// Per-box background. Resolved settings turn a global pick into hex, so we
		// inline it per slot in render() (works on live; the selector here drives
		// the editor preview). Empty = transparent (sits on the section bg).
		$this->add_control( $p . 'bg', [
			'label'       => esc_html__( 'Box background', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => $d['bg'] ?? '',
			'description' => esc_html__( 'Leave empty for a transparent box on the section background.', 'agency-elementor-widgets' ),
			'condition'   => $text_cond,
			'selectors'   => [ '{{WRAPPER}} .aew-frv2__slot-' . $r . $slot . ' .aew-frv2__box' => 'background-color: {{VALUE}};' ],
		] );
	}

	// ── STYLE SECTIONS ──────────────────────────────────────────────────────

	private function style_section(): void {
		$this->start_controls_section( 'ss_section', [ 'label' => esc_html__( 'Section', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'section_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'section_max_w', [
			'label'      => esc_html__( 'Inner max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1440 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'section_pad_y', [
			'label'          => esc_html__( 'Vertical padding', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px' ],
			'range'          => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 64 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 32 ],
			'selectors'      => [ '{{WRAPPER}} .aew-frv2__inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'col_gap', [
			'label'          => esc_html__( 'Gap between items', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px' ],
			'range'          => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 16 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'      => [ '{{WRAPPER}} .aew-frv2__row' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'row_gap', [
			'label'          => esc_html__( 'Gap between rows', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px' ],
			'range'          => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 16 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'      => [ '{{WRAPPER}} .aew-frv2__list' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_header(): void {
		$this->start_controls_section( 'ss_header', [
			'label'     => esc_html__( 'Section header', 'agency-elementor-widgets' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_header' => 'yes' ],
		] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#C9B89A',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-frv2__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [
					'default'        => [ 'unit' => 'px', 'size' => 64 ],
					'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
				],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_responsive_control( 'header_spacing', [
			'label'      => esc_html__( 'Space below header', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 48 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__header' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		// Header pill button — outlined cream style in the reference.
		$this->add_control( 'header_btn_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( 'Button', 'agency-elementor-widgets' ),
			'separator' => 'before',
		] );

		$this->add_control( 'header_btn_bg', [
			'label'       => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'description' => esc_html__( 'Leave empty for a transparent (outlined) button.', 'agency-elementor-widgets' ),
			'selectors'   => [ '{{WRAPPER}}' => '--aew-frv2-hbtn-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'header_btn_text_color', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-hbtn-text: {{VALUE}};' ],
		] );
		$this->add_control( 'header_btn_border', [
			'label'     => esc_html__( 'Border color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-hbtn-border: {{VALUE}};' ],
		] );
		$this->add_control( 'header_btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-hbtn-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'header_btn_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#093328',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-hbtn-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'header_btn_typo',
			'selector'  => '{{WRAPPER}} .aew-frv2__header-btn',
			'separator' => 'before',
			'fields_options' => [
				'typography'     => [ 'default' => 'custom' ],
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_responsive_control( 'header_btn_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 999 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 999 ],
			'separator'  => 'before',
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__header-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_image(): void {
		$this->start_controls_section( 'ss_image', [ 'label' => esc_html__( 'Images', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'image_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__media' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'image_min_h', [
			'label'       => esc_html__( 'Image minimum height (floor)', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px' ],
			'range'       => [ 'px' => [ 'min' => 120, 'max' => 800 ] ],
			'default'     => [ 'unit' => 'px', 'size' => 280 ],
			'description' => esc_html__( 'Images always match the height of the text box sharing their row. This floor only applies to rows that have no text box (image-only rows) so they do not collapse.', 'agency-elementor-widgets' ),
			'selectors'   => [ '{{WRAPPER}}' => '--aew-frv2-img-floor: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_text_box(): void {
		$this->start_controls_section( 'ss_box', [ 'label' => esc_html__( 'Text box', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'box_bg_help', [
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => esc_html__( 'Each text box can override its own background colour in the Items list. These styles apply to every text box.', 'agency-elementor-widgets' ),
			'content_classes' => 'elementor-descriptor',
		] );

		$this->add_responsive_control( 'box_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__box' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'box_padding', [
			'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'        => [ 'top' => '120', 'right' => '20', 'bottom' => '120', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
			'mobile_default' => [ 'top' => '40', 'right' => '20', 'bottom' => '40', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => esc_html__( 'Box title', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-title: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-frv2__title',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [
					'default'        => [ 'unit' => 'px', 'size' => 40 ],
					'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
				],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_responsive_control( 'title_spacing', [
			'label'      => esc_html__( 'Space below title', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_text(): void {
		$this->start_controls_section( 'ss_text', [ 'label' => esc_html__( 'Box body', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-text: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'text_typo',
			'selector' => '{{WRAPPER}} .aew-frv2__body',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [
					'default'        => [ 'unit' => 'px', 'size' => 18 ],
					'mobile_default' => [ 'unit' => 'px', 'size' => 14 ],
				],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
			],
		] );

		$this->add_responsive_control( 'text_spacing', [
			'label'      => esc_html__( 'Space below body (before button)', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__body' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_button(): void {
		$this->start_controls_section( 'ss_button', [ 'label' => esc_html__( 'Button', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'btn_bg', [
			'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-btn-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text_color', [
			'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-btn-text: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_bg_hover', [
			'label'     => esc_html__( 'Background (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-btn-bg-hover: {{VALUE}};' ],
		] );
		$this->add_control( 'btn_text_hover', [
			'label'     => esc_html__( 'Text color (hover)', 'agency-elementor-widgets' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-frv2-btn-text-hover: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'btn_typo',
			'selector'  => '{{WRAPPER}} .aew-frv2__btn',
			'separator' => 'before',
			'fields_options' => [
				'typography'     => [ 'default' => 'custom' ],
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [
					'default'        => [ 'unit' => 'px', 'size' => 20 ],
					'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->add_responsive_control( 'btn_radius', [
			'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 32 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-frv2__btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		// Build the visible rows from the fixed row/slot controls. Each row is its
		// own group: a column count (2/3) and that many slots (a/b/c).
		$rows = [];
		for ( $r = 1; $r <= self::ROW_COUNT; $r++ ) {
			if ( 'yes' !== ( $s[ "row{$r}_show" ] ?? '' ) ) {
				continue;
			}
			// Rows default to 3 items: only an explicit "2 items" pick makes a row
			// 2-up. An empty/unsaved cols therefore renders 3 — matching the
			// control default and sidestepping Elementor's habit of not persisting
			// a SELECT left at its default (which used to drop slot C to 2-up).
			$cols  = ( '2' === (string) ( $s[ "row{$r}_cols" ] ?? '' ) ) ? 2 : 3;
			$slots = array_slice( self::SLOTS, 0, $cols );

			$items = [];
			foreach ( $slots as $slot ) {
				$items[] = $this->collect_slot( $s, $r, $slot );
			}
			$rows[] = [ 'cols' => $cols, 'items' => $items ];
		}

		if ( empty( $rows ) ) {
			return;
		}

		// Shared widget-level colours through the global-aware resolver (§6.8).
		$color_vars = Color_Vars::build( $this, $s, [
			'section_bg'            => '--aew-frv2-bg',
			'heading_color'         => '--aew-frv2-heading',
			'title_color'           => '--aew-frv2-title',
			'text_color'            => '--aew-frv2-text',
			'btn_bg'                => '--aew-frv2-btn-bg',
			'btn_text_color'        => '--aew-frv2-btn-text',
			'btn_bg_hover'          => '--aew-frv2-btn-bg-hover',
			'btn_text_hover'        => '--aew-frv2-btn-text-hover',
			'header_btn_bg'         => '--aew-frv2-hbtn-bg',
			'header_btn_text_color' => '--aew-frv2-hbtn-text',
			'header_btn_border'     => '--aew-frv2-hbtn-border',
			'header_btn_bg_hover'   => '--aew-frv2-hbtn-bg-hover',
			'header_btn_text_hover' => '--aew-frv2-hbtn-text-hover',
		] );

		$this->add_render_attribute( 'wrapper', 'class', 'aew-frv2' );
		$this->add_render_attribute( 'wrapper', 'data-aew-feature-rows-v2', '' );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}

		$show_header = 'yes' === ( $s['show_header'] ?? '' );
		$heading     = (string) ( $s['heading'] ?? '' );
		$h_btn_text  = (string) ( $s['header_btn_text'] ?? '' );
		$h_btn_link  = $this->parse_link( $s['header_btn_link'] ?? [] );
		$h_btn_arrow = 'yes' === ( $s['header_btn_arrow'] ?? '' );
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-frv2__inner">
				<?php if ( $show_header && ( '' !== $heading || ( '' !== $h_btn_text && '' !== $h_btn_link['url'] ) ) ) : ?>
					<div class="aew-frv2__header">
						<?php if ( '' !== $heading ) : ?>
							<h2 class="aew-frv2__heading"><?php echo nl2br( esc_html( $heading ) ); ?></h2>
						<?php endif; ?>
						<?php if ( '' !== $h_btn_text && '' !== $h_btn_link['url'] ) : ?>
							<a class="aew-frv2__header-btn"
								href="<?php echo esc_url( $h_btn_link['url'] ); ?>"
								<?php echo $h_btn_link['target'] ? 'target="' . esc_attr( $h_btn_link['target'] ) . '"' : ''; ?>
								<?php echo $h_btn_link['rel'] ? 'rel="' . esc_attr( $h_btn_link['rel'] ) . '"' : ''; ?>>
								<span class="aew-frv2__header-btn-label"><?php echo esc_html( $h_btn_text ); ?></span>
								<?php if ( $h_btn_arrow ) : ?>
									<svg class="aew-frv2__header-btn-arrow" width="22" height="14" viewBox="0 0 22 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M1 7H20M20 7L14.5 1.5M20 7L14.5 12.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="aew-frv2__list">
					<?php foreach ( $rows as $row ) : ?>
						<div class="aew-frv2__row aew-frv2__row--cols-<?php echo (int) $row['cols']; ?>">
							<?php foreach ( $row['items'] as $item ) : ?>
								<?php $this->render_item( $item ); ?>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Normalise one slot's flat controls into a render-ready array.
	 *
	 * @param array<string, mixed> $s    Resolved settings.
	 * @param int                  $r    Row number.
	 * @param string               $slot Slot key (a|b|c).
	 * @return array<string, mixed>
	 */
	private function collect_slot( array $s, int $r, string $slot ): array {
		$p     = "row{$r}_{$slot}_";
		$type  = ( 'text' === ( $s[ $p . 'type' ] ?? 'image' ) ) ? 'text' : 'image';
		$class = 'aew-frv2__slot-' . $r . $slot;

		if ( 'image' === $type ) {
			$image     = $s[ $p . 'image' ] ?? [];
			$image_url = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
			if ( '' === $image_url ) {
				$fallback_n = ( ( ( $r - 1 ) * 3 + array_search( $slot, self::SLOTS, true ) ) % 3 ) + 1;
				$image_url  = Widget_Assets::url( self::ASSET_SLUG, 'images/feature-' . $fallback_n . '.webp' );
			}
			return [
				'type'  => 'image',
				'class' => $class,
				'url'   => $image_url,
				'alt'   => (string) ( $s[ $p . 'image_alt' ] ?? '' ),
			];
		}

		// Fall back to the control's intended default when the saved value is
		// empty. Elementor does NOT backfill a new control default onto an
		// already-saved instance (gotcha #17), so a footer/template saved before
		// the align control existed stores '' here — without this it would render
		// left even though the default is center.
		$align_raw = (string) ( $s[ $p . 'align' ] ?? '' );
		if ( '' === $align_raw ) {
			$defaults  = $this->slot_defaults();
			$align_raw = (string) ( $defaults[ "row{$r}{$slot}" ]['align'] ?? 'left' );
		}
		$align = in_array( $align_raw, [ 'left', 'center', 'right' ], true ) ? $align_raw : 'left';
		return [
			'type'      => 'text',
			'class'     => $class,
			'title'     => (string) ( $s[ $p . 'title' ] ?? '' ),
			'body'      => (string) ( $s[ $p . 'body' ] ?? '' ),
			'align'     => $align,
			'bg'        => trim( (string) ( $s[ $p . 'bg' ] ?? '' ) ),
			'show_btn'  => 'yes' === ( $s[ $p . 'show_button' ] ?? '' ),
			'btn_text'  => (string) ( $s[ $p . 'btn_text' ] ?? '' ),
			'btn_link'  => $this->parse_link( $s[ $p . 'btn_link' ] ?? [] ),
		];
	}

	/**
	 * @param array<string, mixed> $item Normalised slot (from collect_slot()).
	 * @return void
	 */
	private function render_item( array $item ): void {
		$slot_class = (string) ( $item['class'] ?? '' );

		if ( 'image' === ( $item['type'] ?? 'image' ) ) {
			?>
			<div class="aew-frv2__item aew-frv2__item--image <?php echo esc_attr( $slot_class ); ?>">
				<div class="aew-frv2__media">
					<img class="aew-frv2__img" src="<?php echo esc_url( (string) ( $item['url'] ?? '' ) ); ?>" alt="<?php echo esc_attr( (string) ( $item['alt'] ?? '' ) ); ?>" loading="lazy" decoding="async" />
				</div>
			</div>
			<?php
			return;
		}

		// Text box. Inline the resolved per-box background so a global pick still
		// paints on the live page (resolved settings carry hex for globals).
		$title    = (string) ( $item['title'] ?? '' );
		$body     = (string) ( $item['body'] ?? '' );
		$show_btn = (bool) ( $item['show_btn'] ?? false );
		$btn_text = (string) ( $item['btn_text'] ?? '' );
		$link     = $item['btn_link'] ?? [ 'url' => '', 'target' => '', 'rel' => '' ];
		$align    = (string) ( $item['align'] ?? 'left' );

		$box_style = '';
		$box_bg    = (string) ( $item['bg'] ?? '' );
		if ( '' !== $box_bg ) {
			$box_style .= 'background-color:' . $box_bg . ';';
		}
		$box_style .= 'text-align:' . $align . ';--aew-frv2-box-align:' . $align . ';';
		?>
		<div class="aew-frv2__item aew-frv2__item--text <?php echo esc_attr( $slot_class ); ?>">
			<div class="aew-frv2__box" style="<?php echo esc_attr( $box_style ); ?>">
				<?php if ( '' !== $title ) : ?>
					<h3 class="aew-frv2__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( ! Rich_Text::is_empty( $body ) ) : ?>
					<div class="aew-frv2__body aew-rich-text"><?php Rich_Text::echo_html( $body ); ?></div>
				<?php endif; ?>
				<?php if ( $show_btn && '' !== $btn_text && '' !== $link['url'] ) : ?>
					<a class="aew-frv2__btn"
						href="<?php echo esc_url( $link['url'] ); ?>"
						<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
						<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
						<?php echo esc_html( $btn_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed>|string $d URL control value.
	 * @return array{url: string, target: string, rel: string}
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
