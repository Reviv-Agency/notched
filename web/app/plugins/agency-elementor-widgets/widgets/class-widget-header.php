<?php
/**
 * Header Elementor widget.
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
 * Universal site header widget.
 */
class Widget_Header extends Widget_Base {

	private const ASSET_SLUG = 'header';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-header';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Header', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-header';
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
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_script_depends(): array {
		return [ Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'header', 'navigation', 'menu', 'logo' ];
	}

	/**
	 * Point Advanced → Layout → Padding at the header bar, not the widget wrapper.
	 *
	 * @param bool $with_common_controls Include common widget controls.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );

		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['label']       = esc_html__( 'Header bar padding', 'agency-elementor-widgets' );
			$stack['controls']['_padding']['description'] = esc_html__(
				'Padding inside the header bar. The default Advanced padding targets the widget wrapper; this control applies to the visible bar instead.',
				'agency-elementor-widgets'
			);
			$stack['controls']['_padding']['default']         = [
				'top'      => '8',
				'right'    => '40',
				'bottom'   => '8',
				'left'     => '40',
				'unit'     => 'px',
				'isLinked' => false,
			];
			$stack['controls']['_padding']['tablet_default']  = [
				'top'      => '8',
				'right'    => '16',
				'bottom'   => '8',
				'left'     => '16',
				'unit'     => 'px',
				'isLinked' => false,
			];
			$stack['controls']['_padding']['mobile_default']  = [
				'top'      => '8',
				'right'    => '16',
				'bottom'   => '8',
				'left'     => '16',
				'unit'     => 'px',
				'isLinked' => false,
			];
			$stack['controls']['_padding']['selectors']       = [
				'{{WRAPPER}} .aew-header__bar-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			];
		}

		return $stack;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * @return void
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_logo',
			[
				'label' => esc_html__( 'Logo', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'logo',
			[
				'label'   => esc_html__( 'Logo image (desktop)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/logo-default.svg' ),
				],
			]
		);

		$this->add_control(
			'logo_mobile',
			[
				'label'   => esc_html__( 'Logo image (mobile & tablet)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/logo-mobile.svg' ),
				],
			]
		);

		$this->add_control(
			'logo_link',
			[
				'label'       => esc_html__( 'Logo link', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => home_url( '/' ),
				'default'     => [
					'url' => home_url( '/' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => esc_html__( 'Navigation', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'nav_source',
			[
				'label'   => esc_html__( 'Navigation source', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual'   => esc_html__( 'Manual', 'agency-elementor-widgets' ),
					'wp_menu'  => esc_html__( 'WordPress Menu', 'agency-elementor-widgets' ),
				],
			]
		);

		$menus = wp_get_nav_menus();
		$menu_options = [ '' => esc_html__( '— Select —', 'agency-elementor-widgets' ) ];
		foreach ( $menus as $menu ) {
			$menu_options[ (string) $menu->term_id ] = $menu->name;
		}

		$this->add_control(
			'wp_menu_id',
			[
				'label'     => esc_html__( 'WordPress menu', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $menu_options,
				'condition' => [
					'nav_source' => 'wp_menu',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'label',
			[
				'label'   => esc_html__( 'Label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Link', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'url',
			[
				'label' => esc_html__( 'URL', 'agency-elementor-widgets' ),
				'type'  => Controls_Manager::URL,
			]
		);

		$this->add_control(
			'nav_items',
			[
				'label'       => esc_html__( 'Menu items', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_nav_items(),
				'title_field' => '{{{ label }}}',
				'condition'   => [
					'nav_source' => 'manual',
				],
			]
		);

		$this->add_control(
			'nav_aria_label',
			[
				'label'   => esc_html__( 'Navigation aria-label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Primary', 'agency-elementor-widgets' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_phone',
			[
				'label' => esc_html__( 'Phone', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_phone',
			[
				'label'   => esc_html__( 'Show phone icon', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'phone_number',
			[
				'label'       => esc_html__( 'Phone number', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '+1 555 000 0000',
				'condition'   => [
					'show_phone' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cta',
			[
				'label' => esc_html__( 'Call to action', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_cta',
			[
				'label'   => esc_html__( 'Show CTA button', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cta_text_desktop',
			[
				'label'     => esc_html__( 'CTA text (desktop)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Book Your FREE Consultation', 'agency-elementor-widgets' ),
				'condition' => [
					'show_cta' => 'yes',
				],
			]
		);

		$this->add_control(
			'cta_text_mobile',
			[
				'label'     => esc_html__( 'CTA text (mobile)', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Book Now', 'agency-elementor-widgets' ),
				'condition' => [
					'show_cta' => 'yes',
				],
			]
		);

		$this->add_control(
			'cta_link',
			[
				'label'     => esc_html__( 'CTA link', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'condition' => [
					'show_cta' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mobile',
			[
				'label' => esc_html__( 'Mobile menu', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label'       => esc_html__( 'Mobile & tablet max width (px)', 'agency-elementor-widgets' ),
				'description' => esc_html__( 'At this width and below, the mobile header layout is used (hamburger, Book Now, etc.). Default: 1024.', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1024,
				'min'         => 768,
				'max'         => 1400,
			]
		);

		$this->add_control(
			'menu_toggle_label',
			[
				'label'   => esc_html__( 'Menu button label (accessibility)', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Open menu', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'close_on_link_click',
			[
				'label'   => esc_html__( 'Close menu when a link is clicked', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_style_bar',
			[
				'label' => esc_html__( 'Header bar', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bar_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .aew-header__bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_max_width',
			[
				'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 320,
						'max' => 1920,
					],
					'%'  => [
						'min' => 50,
						'max' => 100,
					],
					'vw' => [
						'min' => 50,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1440,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-header__bar-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_max_height',
			[
				'label'      => esc_html__( 'Logo max height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 56,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-header__logo-img--desktop' => 'max-height: {{SIZE}}{{UNIT}}; width: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'logo_mobile_max_height',
			[
				'label'      => esc_html__( 'Mobile logo max height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 56,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-header__logo-img--mobile' => 'max-height: {{SIZE}}{{UNIT}}; width: auto;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav',
			[
				'label' => esc_html__( 'Navigation', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_color',
			[
				'label'     => esc_html__( 'Link color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#305B83',
				'selectors' => [
					'{{WRAPPER}} .aew-header__nav a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_hover_color',
			[
				'label'     => esc_html__( 'Link hover color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252F37',
				'selectors' => [
					'{{WRAPPER}} .aew-header__nav a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .aew-header__nav a',
				'fields_options' => [
					'font_family' => [ 'default' => 'Poppins' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 16,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cta',
			[
				'label' => esc_html__( 'CTA button', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cta_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3C400',
				'selectors' => [
					'{{WRAPPER}} .aew-header__cta' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#252F37',
				'selectors' => [
					'{{WRAPPER}} .aew-header__cta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cta_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '12',
					'right'    => '24',
					'bottom'   => '12',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-header__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cta_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-header__cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .aew-header__cta',
				'fields_options' => [
					'font_family' => [ 'default' => 'Poppins' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 16,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.25,
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Bright Homes demo nav defaults.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function default_nav_items(): array {
		$items = [
			[ 'label' => 'About', 'url' => [ 'url' => '#' ] ],
			[ 'label' => 'Process', 'url' => [ 'url' => '#' ] ],
			[ 'label' => 'Services', 'url' => [ 'url' => '#' ] ],
			[ 'label' => 'Portfolio', 'url' => [ 'url' => '#' ] ],
			[ 'label' => 'Contact Us', 'url' => [ 'url' => '#' ] ],
			[ 'label' => 'Resources', 'url' => [ 'url' => '#' ] ],
		];

		return $items;
	}

	/**
	 * @return array<int, array{label: string, url: string, target: string, rel: string}>
	 */
	private function get_nav_links(): array {
		$settings = $this->get_settings_for_display();
		$links    = [];

		if ( 'wp_menu' === ( $settings['nav_source'] ?? '' ) && ! empty( $settings['wp_menu_id'] ) ) {
			$menu_items = wp_get_nav_menu_items( (int) $settings['wp_menu_id'] );
			if ( is_array( $menu_items ) ) {
				foreach ( $menu_items as $item ) {
					if ( empty( $item->url ) ) {
						continue;
					}
					$links[] = [
						'label'  => $item->title,
						'url'    => $item->url,
						'target' => $item->target ?: '',
						'rel'    => '',
					];
				}
			}
			return $links;
		}

		$items = $settings['nav_items'] ?? [];
		if ( ! is_array( $items ) ) {
			return $links;
		}

		foreach ( $items as $item ) {
			$url_data = $item['url'] ?? [];
			$url      = is_array( $url_data ) ? ( $url_data['url'] ?? '' ) : '';
			if ( '' === $url ) {
				continue;
			}
			$target = ! empty( $url_data['is_external'] ) ? '_blank' : '';
			$rel    = [];
			if ( $target ) {
				$rel[] = 'noopener';
			}
			if ( ! empty( $url_data['nofollow'] ) ) {
				$rel[] = 'nofollow';
			}
			$links[] = [
				'label'  => $item['label'] ?? '',
				'url'    => $url,
				'target' => $target,
				'rel'    => implode( ' ', $rel ),
			];
		}

		return $links;
	}

	/**
	 * @param array<string, mixed>|string $link_data URL control value.
	 * @return array{url: string, target: string, rel: string}
	 */
	private function parse_link( $link_data ): array {
		if ( ! is_array( $link_data ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}

		$url = $link_data['url'] ?? '';
		$target = ! empty( $link_data['is_external'] ) ? '_blank' : '';
		$rel = [];
		if ( $target ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link_data['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		return [
			'url'    => $url,
			'target' => $target,
			'rel'    => implode( ' ', $rel ),
		];
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$breakpoint = max( 768, (int) ( $settings['mobile_breakpoint'] ?? 1024 ) );
		$nav_links  = $this->get_nav_links();
		$logo        = $settings['logo'] ?? [];
		$logo_url    = is_array( $logo ) ? ( $logo['url'] ?? '' ) : '';
		$logo_mobile = $settings['logo_mobile'] ?? [];
		$logo_mobile_url = is_array( $logo_mobile ) ? ( $logo_mobile['url'] ?? '' ) : '';
		if ( '' === $logo_mobile_url ) {
			$logo_mobile_url = Widget_Assets::url( self::ASSET_SLUG, 'images/logo-mobile.svg' );
		}
		$logo_link  = $this->parse_link( $settings['logo_link'] ?? [] );
		$cta_link   = $this->parse_link( $settings['cta_link'] ?? [] );
		$phone      = preg_replace( '/\s+/', '', (string) ( $settings['phone_number'] ?? '' ) );
		$widget_id  = $this->get_id();

		$this->add_render_attribute( 'wrapper', 'class', 'aew-header' );
		$this->add_render_attribute( 'wrapper', 'data-aew-header', '' );
		$this->add_render_attribute( 'wrapper', 'style', '--aew-header-bp:' . $breakpoint );
		$this->add_render_attribute( 'wrapper', 'data-close-on-click', ( 'yes' === ( $settings['close_on_link_click'] ?? '' ) ) ? '1' : '0' );

		?>
		<header <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-header__bar">
				<div class="aew-header__bar-inner">
					<div class="aew-header__logo">
						<?php if ( $logo_url || $logo_mobile_url ) : ?>
							<a class="aew-header__logo-link" href="<?php echo esc_url( $logo_link['url'] ?: home_url( '/' ) ); ?>"
								<?php echo $logo_link['target'] ? ' target="' . esc_attr( $logo_link['target'] ) . '"' : ''; ?>
								<?php echo $logo_link['rel'] ? ' rel="' . esc_attr( $logo_link['rel'] ) . '"' : ''; ?>>
								<?php if ( $logo_url ) : ?>
									<img class="aew-header__logo-img aew-header__logo-img--desktop" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="341" height="56" decoding="async" />
								<?php endif; ?>
								<?php if ( $logo_mobile_url ) : ?>
									<img class="aew-header__logo-img aew-header__logo-img--mobile" src="<?php echo esc_url( $logo_mobile_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="64" height="44" decoding="async" />
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $nav_links ) ) : ?>
						<nav class="aew-header__nav aew-header__nav--desktop" aria-label="<?php echo esc_attr( $settings['nav_aria_label'] ?? '' ); ?>">
							<ul class="aew-header__nav-list">
								<?php foreach ( $nav_links as $link ) : ?>
									<li>
										<a href="<?php echo esc_url( $link['url'] ); ?>"
											<?php echo $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
											<?php echo $link['rel'] ? ' rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
											<?php echo esc_html( $link['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

					<div class="aew-header__actions">
						<?php if ( 'yes' === ( $settings['show_phone'] ?? '' ) && $phone ) : ?>
							<a class="aew-header__phone" href="tel:<?php echo esc_attr( $phone ); ?>" aria-label="<?php esc_attr_e( 'Call us', 'agency-elementor-widgets' ); ?>">
								<?php $this->render_phone_icon(); ?>
							</a>
						<?php elseif ( 'yes' === ( $settings['show_phone'] ?? '' ) ) : ?>
							<span class="aew-header__phone aew-header__phone--static" aria-hidden="true">
								<?php $this->render_phone_icon(); ?>
							</span>
						<?php endif; ?>

						<?php if ( 'yes' === ( $settings['show_cta'] ?? '' ) && $cta_link['url'] ) : ?>
							<a class="aew-header__cta aew-header__cta--desktop" href="<?php echo esc_url( $cta_link['url'] ); ?>"
								<?php echo $cta_link['target'] ? ' target="' . esc_attr( $cta_link['target'] ) . '"' : ''; ?>
								<?php echo $cta_link['rel'] ? ' rel="' . esc_attr( $cta_link['rel'] ) . '"' : ''; ?>>
								<?php echo esc_html( $settings['cta_text_desktop'] ?? '' ); ?>
							</a>
							<a class="aew-header__cta aew-header__cta--mobile" href="<?php echo esc_url( $cta_link['url'] ); ?>"
								<?php echo $cta_link['target'] ? ' target="' . esc_attr( $cta_link['target'] ) . '"' : ''; ?>
								<?php echo $cta_link['rel'] ? ' rel="' . esc_attr( $cta_link['rel'] ) . '"' : ''; ?>>
								<?php echo esc_html( $settings['cta_text_mobile'] ?? '' ); ?>
							</a>
						<?php endif; ?>

						<button type="button" class="aew-header__toggle" aria-expanded="false" aria-controls="aew-menu-<?php echo esc_attr( $widget_id ); ?>">
							<span class="screen-reader-text"><?php echo esc_html( $settings['menu_toggle_label'] ?? '' ); ?></span>
							<?php $this->render_menu_icon(); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="aew-header__overlay" id="aew-menu-<?php echo esc_attr( $widget_id ); ?>" hidden>
				<div class="aew-header__overlay-panel">
					<button type="button" class="aew-header__close" aria-label="<?php esc_attr_e( 'Close menu', 'agency-elementor-widgets' ); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
					<?php if ( ! empty( $nav_links ) ) : ?>
						<nav class="aew-header__nav aew-header__nav--mobile" aria-label="<?php echo esc_attr( $settings['nav_aria_label'] ?? '' ); ?>">
							<ul class="aew-header__nav-list">
								<?php foreach ( $nav_links as $link ) : ?>
									<li>
										<a href="<?php echo esc_url( $link['url'] ); ?>"
											<?php echo $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
											<?php echo $link['rel'] ? ' rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
											<?php echo esc_html( $link['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>
					<?php if ( 'yes' === ( $settings['show_cta'] ?? '' ) && $cta_link['url'] ) : ?>
						<a class="aew-header__cta aew-header__cta--overlay" href="<?php echo esc_url( $cta_link['url'] ); ?>"
							<?php echo $cta_link['target'] ? ' target="' . esc_attr( $cta_link['target'] ) . '"' : ''; ?>
							<?php echo $cta_link['rel'] ? ' rel="' . esc_attr( $cta_link['rel'] ) . '"' : ''; ?>>
							<?php echo esc_html( $settings['cta_text_mobile'] ?? $settings['cta_text_desktop'] ?? '' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</header>
		<?php
	}

	/**
	 * @return void
	 */
	private function render_phone_icon(): void {
		$path = Widget_Assets::path( self::ASSET_SLUG, 'images/phone-icon.svg' );

		if ( ! is_readable( $path ) ) {
			return;
		}

		$svg = file_get_contents( $path );

		if ( false === $svg || '' === $svg ) {
			return;
		}

		if ( ! str_contains( $svg, 'class="aew-header__phone-icon"' ) ) {
			$svg = preg_replace( '/<svg\b/', '<svg class="aew-header__phone-icon" aria-hidden="true"', $svg, 1 );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin asset.
		echo $svg;
	}

	/**
	 * @return void
	 */
	private function render_menu_icon(): void {
		?>
		<svg width="28" height="20" viewBox="0 0 28 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<rect y="1" width="28" height="3" rx="1.5" fill="currentColor"/>
			<rect y="8.5" width="28" height="3" rx="1.5" fill="currentColor"/>
			<rect y="16" width="28" height="3" rx="1.5" fill="currentColor"/>
		</svg>
		<?php
	}
}
