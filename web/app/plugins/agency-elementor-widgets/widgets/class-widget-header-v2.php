<?php

/**
 * Header V2 — [Company] brand.
 *
 * Right-side layout (matches Wix original):
 *   Account icon | Cart icon (live count) | Phone | SHOP KITS (arrow) | Hamburger
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined('ABSPATH') || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Header_V2 extends Widget_Base
{

	private const ASSET_SLUG = 'header-v2';

	public function get_name(): string
	{
		return 'agency-header-v2';
	}
	public function get_title(): string
	{
		return esc_html__('Header V2', 'agency-elementor-widgets');
	}
	public function get_icon(): string
	{
		return 'eicon-header';
	}
	public function get_categories(): array
	{
		return ['agency-widgets'];
	}
	public function get_keywords(): array
	{
		return ['header', 'navigation', 'menu', 'logo'];
	}

	public function get_style_depends(): array
	{
		return ['aew-tokens', Widget_Assets::handle(self::ASSET_SLUG)];
	}
	public function get_script_depends(): array
	{
		return [Widget_Assets::handle(self::ASSET_SLUG)];
	}

	public function get_stack($with_common_controls = true)
	{
		$stack = parent::get_stack($with_common_controls);
		if ($with_common_controls && isset($stack['controls']['_padding'])) {
			$stack['controls']['_padding']['selectors']      = ['{{WRAPPER}} .aew-hv2__bar-inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};'];
			// Leave the override empty so the stylesheet owns the responsive X
			// padding (40px mobile/tablet → 80px desktop). A non-empty default
			// here is emitted by Elementor WITHOUT media queries and would
			// clobber the stylesheet at every breakpoint.
			$stack['controls']['_padding']['default']        = ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false];
			$stack['controls']['_padding']['tablet_default'] = ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false];
			$stack['controls']['_padding']['mobile_default'] = ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void
	{
		$this->controls_logo();
		$this->controls_phone();
		$this->controls_cta();
		$this->controls_icons();
		$this->controls_nav();
		$this->style_bar();
		$this->style_phone();
		$this->style_cta();
		$this->style_icons();
		$this->style_drawer();
	}

	private function controls_logo(): void
	{
		$this->start_controls_section('s_logo', ['label' => 'Logo']);
		$this->add_control('logo', ['label' => 'Logo image', 'type' => Controls_Manager::MEDIA, 'default' => ['url' => Widget_Assets::url(self::ASSET_SLUG, 'images/logo-default.svg')]]);
		$this->add_control('logo_link', ['label' => 'Logo link', 'type' => Controls_Manager::URL, 'default' => ['url' => home_url('/')]]);
		$this->add_responsive_control('logo_height', ['label' => 'Height', 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 16, 'max' => 80]], 'default' => ['unit' => 'px', 'size' => 50], 'selectors' => ['{{WRAPPER}} .aew-hv2__logo-img' => 'height: {{SIZE}}{{UNIT}}; width: auto;']]);
		$this->end_controls_section();
	}

	private function controls_phone(): void
	{
		$this->start_controls_section('s_phone', ['label' => 'Phone']);
		$this->add_control('show_phone',        ['label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('phone_number',      ['label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '801.410.4255', 'condition' => ['show_phone' => 'yes']]);
		$this->add_control('phone_hide_mobile', ['label' => 'Hide on mobile', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_phone' => 'yes']]);
		$this->end_controls_section();
	}

	private function controls_cta(): void
	{
		$this->start_controls_section('s_cta', ['label' => 'CTA Button']);
		$this->add_control('show_cta',      ['label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('cta_text',      ['label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'SHOP KITS', 'condition' => ['show_cta' => 'yes']]);
		$this->add_control('cta_link',      ['label' => 'Link', 'type' => Controls_Manager::URL, 'default' => ['url' => home_url('/shop/')], 'condition' => ['show_cta' => 'yes']]);
		$this->add_control('show_cta_arrow', ['label' => 'Show arrow icon', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['show_cta' => 'yes']]);
		$this->end_controls_section();
	}

	private function controls_icons(): void
	{
		$this->start_controls_section('s_icons', ['label' => 'Icons']);
		$this->add_control('show_account', ['label' => 'Show account icon', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('account_link', ['label' => 'Account link', 'type' => Controls_Manager::URL, 'default' => ['url' => home_url('/my-account/')], 'condition' => ['show_account' => 'yes']]);
		$this->add_control('show_cart',    ['label' => 'Show cart icon', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->add_control('cart_link',    ['label' => 'Cart link', 'type' => Controls_Manager::URL, 'default' => ['url' => home_url('/cart/')], 'condition' => ['show_cart' => 'yes']]);
		$this->end_controls_section();
	}

	private function controls_nav(): void
	{
		$this->start_controls_section('s_nav', ['label' => 'Navigation (Drawer)']);
		$this->add_control('nav_source', ['label' => 'Source', 'type' => Controls_Manager::SELECT, 'default' => 'manual', 'options' => ['manual' => 'Manual', 'wp_menu' => 'WordPress Menu']]);
		$menus = wp_get_nav_menus();
		$opts  = ['' => '— Select —'];
		foreach ($menus as $m) {
			$opts[(string) $m->term_id] = $m->name;
		}
		$this->add_control('wp_menu_id', ['label' => 'WordPress menu', 'type' => Controls_Manager::SELECT, 'options' => $opts, 'condition' => ['nav_source' => 'wp_menu']]);
		$rep = new Repeater();
		$rep->add_control('label', ['label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Link']);
		$rep->add_control('url',   ['label' => 'URL',   'type' => Controls_Manager::URL]);
		$this->add_control('nav_items', ['label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => $this->default_nav_items(), 'title_field' => '{{{ label }}}', 'condition' => ['nav_source' => 'manual']]);
		$this->add_control('close_on_click', ['label' => 'Close on link click', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_bar(): void
	{
		$this->start_controls_section('ss_bar', ['label' => 'Bar', 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_control('bar_bg',     ['label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__bar' => 'background-color: {{VALUE}};']]);
		$this->add_control('bar_height', ['label' => 'Height', 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 48, 'max' => 120]], 'default' => ['unit' => 'px', 'size' => 64], 'selectors' => [
			'{{WRAPPER}} .aew-hv2__bar-inner' => 'min-height: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .aew-hv2'            => '--aew-hv2-bar-h: {{SIZE}}{{UNIT}};',
		]]);
		$this->add_responsive_control('bar_max_w', ['label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 800, 'max' => 1920]], 'default' => ['unit' => 'px', 'size' => 1440], 'selectors' => ['{{WRAPPER}} .aew-hv2__bar-inner' => 'max-width: {{SIZE}}{{UNIT}};']]);
		$this->end_controls_section();
	}

	private function style_phone(): void
	{
		$this->start_controls_section('ss_phone', ['label' => 'Phone', 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_control('phone_color', ['label' => 'Color', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__phone' => 'color: {{VALUE}};']]);
		$this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'phone_typo', 'selector' => '{{WRAPPER}} .aew-hv2__phone', 'fields_options' => ['font_family' => ['default' => 'Lato'], 'font_weight' => ['default' => '700'], 'font_size'   => ['default' => ['unit' => 'px', 'size' => 16]]]]);
		$this->end_controls_section();
	}

	private function style_cta(): void
	{
		$this->start_controls_section('ss_cta', ['label' => 'CTA Button', 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_control('cta_bg',       ['label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__cta' => 'background-color: {{VALUE}};']]);
		$this->add_control('cta_bg_hover', ['label' => 'Hover background', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__cta:hover' => 'background-color: {{VALUE}};']]);
		$this->add_control('cta_color',    ['label' => 'Text color', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .aew-hv2__cta' => 'color: {{VALUE}};']]);
		$this->add_control('cta_color_hover', ['label' => 'Hover text color', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .aew-hv2__cta:hover' => 'color: {{VALUE}};', '{{WRAPPER}} .aew-hv2__cta:focus-visible' => 'color: {{VALUE}};']]);
		$this->add_control('cta_radius',   ['label' => 'Border radius', 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => 6], 'selectors' => ['{{WRAPPER}} .aew-hv2__cta' => 'border-radius: {{SIZE}}{{UNIT}};']]);
		$this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'cta_typo', 'selector' => '{{WRAPPER}} .aew-hv2__cta', 'fields_options' => ['font_family' => ['default' => 'Teko'], 'font_weight' => ['default' => '600'], 'font_size'   => ['default' => ['unit' => 'px', 'size' => 16]], 'line_height' => ['default' => ['unit' => 'em', 'size' => 0.85]]]]);
		$this->end_controls_section();
	}

	private function style_icons(): void
	{
		$this->start_controls_section('ss_icons', ['label' => 'Icons', 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_control('icon_color',      ['label' => 'Icon color', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__icon' => 'color: {{VALUE}};', '{{WRAPPER}} .aew-hv2__toggle' => 'color: {{VALUE}};']]);
		$this->add_control('icon_size',       ['label' => 'Icon size', 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['unit' => 'px', 'size' => 22], 'selectors' => ['{{WRAPPER}} .aew-hv2__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']]);
		$this->end_controls_section();
	}

	private function style_drawer(): void
	{
		$this->start_controls_section('ss_drawer', ['label' => 'Drawer', 'tab' => Controls_Manager::TAB_STYLE]);
		$this->add_control('drawer_bg',          ['label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__drawer' => 'background-color: {{VALUE}};']]);
		$this->add_control('drawer_parent_color', ['label' => 'Parent link color', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__drawer-list .is-parent > a' => 'color: {{VALUE}};']]);
		$this->add_control('drawer_child_color',  ['label' => 'Child link color', 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .aew-hv2__drawer-list .is-child > a' => 'color: {{VALUE}};']]);
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void
	{
		$s         = $this->get_settings_for_display();
		$wid       = $this->get_id();
		$logo      = $s['logo'] ?? [];
		$logo_url  = is_array($logo) ? ($logo['url'] ?? '') : '';
		$logo_link = $this->parse_link($s['logo_link'] ?? []);
		$cta_link  = $this->parse_link($s['cta_link']  ?? []);
		$acc_link  = $this->parse_link($s['account_link'] ?? []);
		$cart_link = $this->parse_link($s['cart_link']    ?? []);
		$phone_raw = trim((string) ($s['phone_number'] ?? ''));
		$phone_tel = preg_replace('/[^\d+]/', '', $phone_raw);
		$nav       = []; // unused — drawer uses nav_tree() directly
		$close     = ('yes' === ($s['close_on_click'] ?? 'yes')) ? '1' : '0';

		// WooCommerce cart count
		$cart_count = 0;
		if (function_exists('WC') && WC()->cart) {
			$cart_count = WC()->cart->get_cart_contents_count();
		}
?>
		<header class="aew-hv2" data-aew-header-v2 data-close-on-click="<?php echo esc_attr($close); ?>">
			<div class="aew-hv2__bar">
				<div class="aew-hv2__bar-inner">

					<!-- ── Logo ── -->
					<div class="aew-hv2__logo">
						<?php if ($logo_url) : ?>
							<a class="aew-hv2__logo-link" href="<?php echo esc_url($logo_link['url'] ?: home_url('/')); ?>"
								<?php echo $logo_link['target'] ? 'target="' . esc_attr($logo_link['target']) . '"' : ''; ?>>
								<img class="aew-hv2__logo-img" src="<?php echo esc_url($logo_url); ?>"
									alt="<?php echo esc_attr(get_bloginfo('name')); ?>" height="50" decoding="async" loading="eager" fetchpriority="high" />
							</a>
						<?php endif; ?>
					</div>

					<!-- ── Right actions ── -->
					<div class="aew-hv2__actions">

						<!-- Account icon -->
						<?php if ('yes' === ($s['show_account'] ?? '')) : ?>
							<a class="aew-hv2__icon aew-hv2__icon--account"
								href="<?php echo esc_url($acc_link['url'] ?: home_url('/my-account/')); ?>"
								aria-label="<?php esc_attr_e('My account', 'agency-elementor-widgets'); ?>">
								<svg viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor">
									<path d="M25 48.077c-5.924 0-11.31-2.252-15.396-5.921 2.254-5.362 7.492-8.267 15.373-8.267 7.889 0 13.139 3.044 15.408 8.418-4.084 3.659-9.471 5.77-15.385 5.77m.278-35.3c4.927 0 8.611 3.812 8.611 8.878 0 5.21-3.875 9.456-8.611 9.456s-8.611-4.246-8.611-9.456c0-5.066 3.684-8.878 8.611-8.878M25 0C11.193 0 0 11.193 0 25c0 .915.056 1.816.152 2.705.032.295.091.581.133.873.085.589.173 1.176.298 1.751.073.338.169.665.256.997.135.515.273 1.027.439 1.529.114.342.243.675.37 1.01.18.476.369.945.577 1.406.149.331.308.657.472.98.225.446.463.883.714 1.313.182.312.365.619.56.922.272.423.56.832.856 1.237.207.284.41.568.629.841.325.408.671.796 1.02 1.182.22.244.432.494.662.728.405.415.833.801 1.265 1.186.173.154.329.325.507.475l.004-.011A24.886 24.886 0 0 0 25 50a24.881 24.881 0 0 0 16.069-5.861.126.126 0 0 1 .003.01c.172-.144.324-.309.49-.458.442-.392.88-.787 1.293-1.209.228-.232.437-.479.655-.72.352-.389.701-.78 1.028-1.191.218-.272.421-.556.627-.838.297-.405.587-.816.859-1.24a26.104 26.104 0 0 0 1.748-3.216c.208-.461.398-.93.579-1.406.127-.336.256-.669.369-1.012.167-.502.305-1.014.44-1.53.087-.332.183-.659.256-.996.126-.576.214-1.164.299-1.754.042-.292.101-.577.133-.872.095-.89.152-1.791.152-2.707C50 11.193 38.807 0 25 0" />
								</svg>
							</a>
						<?php endif; ?>

						<!-- Cart icon with live count -->
						<?php if ('yes' === ($s['show_cart'] ?? '')) : ?>
							<a class="aew-hv2__icon aew-hv2__icon--cart"
								href="<?php echo esc_url($cart_link['url'] ?: home_url('/cart/')); ?>"
								aria-label="<?php printf(esc_attr__('Cart with %d items', 'agency-elementor-widgets'), $cart_count); ?>">
								<span class="aew-hv2__cart-wrap">
									<svg viewBox="0 0 105.5 126.1" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor">
										<path d="M102.143 118.16L93.812 48.207C93.386 44.66 90.357 42 86.759 42H79.138V56H74.405V42H31.803V56H27.07V42H19.449C15.851 42 12.822 44.66 12.396 48.16L4.065 118.16C3.781 120.167 4.444 122.173 5.769 123.667 7.142 125.16 9.082 126 11.07 126H95.137c1.988 0 3.929-.84 5.302-2.333 1.325-1.494 1.988-3.5 1.704-5.507z" />
										<path d="M32.059 25.667C32.059 14.093 41.506 4.667 53.104 4.667S74.148 14.093 74.148 25.667V42h4.677V25.667C78.825 11.527 67.274 0 53.104 0 38.934 0 27.383 11.527 27.383 25.667V42h4.676V25.667z" />
									</svg>
									<span class="aew-hv2__cart-count" data-cart-count><?php echo esc_html($cart_count); ?></span>
								</span>
							</a>
						<?php endif; ?>

						<!-- Phone -->
						<?php if ('yes' === ($s['show_phone'] ?? '') && $phone_raw) : ?>
							<a class="aew-hv2__phone<?php echo ('yes' === ($s['phone_hide_mobile'] ?? '')) ? ' aew-hv2__phone--hide-mobile' : ''; ?>"
								href="tel:<?php echo esc_attr($phone_tel); ?>">
								<?php echo esc_html($phone_raw); ?>
							</a>
						<?php endif; ?>

						<!-- SHOP KITS CTA -->
						<?php if ('yes' === ($s['show_cta'] ?? '') && $cta_link['url']) : ?>
							<a class="aew-hv2__cta"
								href="<?php echo esc_url($cta_link['url']); ?>"
								<?php echo $cta_link['target'] ? 'target="' . esc_attr($cta_link['target']) . '"' : ''; ?>>
								<span class="aew-hv2__cta-text"><?php echo esc_html($s['cta_text'] ?? 'SHOP KITS'); ?></span>
								<?php if ('yes' === ($s['show_cta_arrow'] ?? 'yes')) : ?>
									<span class="aew-hv2__cta-arrow" aria-hidden="true">
										<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M3 8h10M9 4l4 4-4 4" />
										</svg>
									</span>
								<?php endif; ?>
							</a>
						<?php endif; ?>

						<!-- Hamburger -->
						<button type="button" class="aew-hv2__toggle"
							aria-expanded="false"
							aria-haspopup="dialog"
							aria-controls="aew-hv2-drawer-<?php echo esc_attr($wid); ?>"
							aria-label="<?php esc_attr_e('Menu', 'agency-elementor-widgets'); ?>">
							<svg viewBox="60 70 80 60" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor" width="24" height="18" style="width:24px;height:18px;min-width:24px;min-height:18px;">
								<path d="M64 78h72a4 4 0 0 0 0-8H64a4 4 0 0 0 0 8z" />
								<path d="M136 96H64a4 4 0 0 0 0 8h72a4 4 0 0 0 0-8z" />
								<path d="M136 122H64a4 4 0 0 0 0 8h72a4 4 0 0 0 0-8z" />
							</svg>
						</button>

					</div><!-- /.aew-hv2__actions -->
				</div><!-- /.aew-hv2__bar-inner -->
			</div><!-- /.aew-hv2__bar -->

			<!-- ── Drawer overlay ── -->
			<div class="aew-hv2__overlay" id="aew-hv2-drawer-<?php echo esc_attr($wid); ?>" hidden>
				<div class="aew-hv2__drawer" role="dialog" aria-modal="true"
					aria-label="<?php esc_attr_e('Site navigation', 'agency-elementor-widgets'); ?>">

					<!-- Drawer top bar: LOGIN | phone | cart | SHOP KITS | × -->
					<div class="aew-hv2__drawer-topbar">
						<?php if ('yes' === ($s['show_account'] ?? '')) : ?>
							<a class="aew-hv2__drawer-login"
								href="<?php echo esc_url($acc_link['url'] ?: home_url('/my-account/')); ?>">
								LOGIN
							</a>
						<?php endif; ?>

						<?php if ('yes' === ($s['show_phone'] ?? '') && $phone_raw) : ?>
							<a class="aew-hv2__drawer-phone" href="tel:<?php echo esc_attr($phone_tel); ?>">
								<?php echo esc_html($phone_raw); ?>
							</a>
						<?php endif; ?>

						<?php if ('yes' === ($s['show_cart'] ?? '')) : ?>
							<a class="aew-hv2__drawer-cart"
								href="<?php echo esc_url($cart_link['url'] ?: home_url('/cart/')); ?>"
								aria-label="<?php printf(esc_attr__('Cart with %d items', 'agency-elementor-widgets'), $cart_count); ?>">
								<span class="aew-hv2__cart-wrap">
									<svg viewBox="0 0 105.5 126.1" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor" width="18" height="22">
										<path d="M102.143 118.16L93.812 48.207C93.386 44.66 90.357 42 86.759 42H79.138V56H74.405V42H31.803V56H27.07V42H19.449C15.851 42 12.822 44.66 12.396 48.16L4.065 118.16C3.781 120.167 4.444 122.173 5.769 123.667 7.142 125.16 9.082 126 11.07 126H95.137c1.988 0 3.929-.84 5.302-2.333 1.325-1.494 1.988-3.5 1.704-5.507z" />
										<path d="M32.059 25.667C32.059 14.093 41.506 4.667 53.104 4.667S74.148 14.093 74.148 25.667V42h4.677V25.667C78.825 11.527 67.274 0 53.104 0 38.934 0 27.383 11.527 27.383 25.667V42h4.676V25.667z" />
									</svg>
									<span class="aew-hv2__cart-count" data-cart-count><?php echo esc_html($cart_count); ?></span>
								</span>
							</a>
						<?php endif; ?>

						<?php if ('yes' === ($s['show_cta'] ?? '') && $cta_link['url']) : ?>
							<a class="aew-hv2__drawer-cta"
								href="<?php echo esc_url($cta_link['url']); ?>">
								<span><?php echo esc_html($s['cta_text'] ?? 'SHOP KITS'); ?></span>
								<svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="14" height="14" aria-hidden="true">
									<path d="M46.5 28.9L20.6 3c-.6-.6-1.6-.6-2.2 0l-4.8 4.8c-.6.6-.6 1.6 0 2.2l19.8 20-19.9 19.9c-.6.6-.6 1.6 0 2.2l4.8 4.8c.6.6 1.6.6 2.2 0l21-21 4.8-4.8c.8-.6.8-1.6.2-2.2z" />
								</svg>
							</a>
						<?php endif; ?>

						<button type="button" class="aew-hv2__drawer-close"
							aria-label="<?php esc_attr_e('Close menu', 'agency-elementor-widgets'); ?>">
							<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="24" height="24" aria-hidden="true">
								<path d="M171.3 158.4L113 100l58.4-58.4c3.6-3.6 3.6-9.4 0-13s-9.4-3.6-13 0L100 87 41.6 28.7c-3.6-3.6-9.4-3.6-13 0s-3.6 9.4 0 13L87 100l-58.4 58.4c-3.6 3.6-3.6 9.4 0 13s9.4 3.6 13 0L100 113l58.4 58.4c3.6 3.6 9.4 3.6 13 0s3.5-9.5-.1-13z" />
							</svg>
						</button>
					</div><!-- /.aew-hv2__drawer-topbar -->

					<!-- Accordion nav -->
					<?php
					$tree = $this->nav_tree();
					if (! empty($tree)) :
					?>
						<nav class="aew-hv2__drawer-nav" aria-label="<?php esc_attr_e('Primary', 'agency-elementor-widgets'); ?>">
							<ul class="aew-hv2__drawer-list">
								<?php foreach ($tree as $idx => $item) :
									$has_children = ! empty($item['children']);
									$uid = 'aew-sub-' . esc_attr($wid) . '-' . $idx;
								?>
									<li class="aew-hv2__drawer-item<?php echo $has_children ? ' has-children' : ''; ?>">
										<?php if ($has_children) : ?>
											<button class="aew-hv2__drawer-item-row aew-hv2__drawer-toggle"
												type="button"
												aria-expanded="false"
												aria-controls="<?php echo esc_attr($uid); ?>"
												aria-label="<?php echo esc_attr($item['label']); ?>">
												<span class="aew-hv2__drawer-label"><?php echo esc_html($item['label']); ?></span>
												<svg class="aew-hv2__drawer-chevron" viewBox="0 0 9.2828 4.89817" xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="12" height="7" aria-hidden="true">
													<path d="M4.641 4.898a.5.5 0 0 1-.343-.136L.157.864A.5.5 0 0 1 .843.136L4.641 3.712 8.44.136a.5.5 0 0 1 .686.729L4.984 4.762a.5.5 0 0 1-.343.136z" />
												</svg>
											</button>
										<?php else : ?>
											<div class="aew-hv2__drawer-item-row">
												<a class="aew-hv2__drawer-label"
													href="<?php echo esc_url($item['url']); ?>"
													<?php echo $item['target'] ? 'target="' . esc_attr($item['target']) . '"' : ''; ?>>
													<?php echo esc_html($item['label']); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ($has_children) : ?>
											<ul class="aew-hv2__drawer-submenu" id="<?php echo esc_attr($uid); ?>" hidden>
												<?php foreach ($item['children'] as $child) : ?>
													<li>
														<a href="<?php echo esc_url($child['url']); ?>"
															<?php echo $child['target'] ? 'target="' . esc_attr($child['target']) . '"' : ''; ?>>
															<?php echo esc_html($child['label']); ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

				</div><!-- /.aew-hv2__drawer -->
			</div><!-- /.aew-hv2__overlay -->
		</header>
<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	private function parse_link($d): array
	{
		if (! is_array($d)) return ['url' => '', 'target' => '', 'rel' => ''];
		$t = ! empty($d['is_external']) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if (! empty($d['nofollow'])) $r .= ' nofollow';
		return ['url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim($r)];
	}

	private function default_nav_items(): array
	{
		return [
			['label' => 'Our Portfolio',  'url' => ['url' => home_url('/our-portfolio/')]],
			['label' => 'Shop All Kits',  'url' => ['url' => home_url('/shop/')]],
			['label' => 'Pergola Kits',   'url' => ['url' => home_url('/pergola-kits/')]],
			['label' => 'Pavilion Kits',  'url' => ['url' => home_url('/pavilion-kits/')]],
			['label' => 'Our Story',      'url' => ['url' => home_url('/our-story/')]],
			['label' => 'Contact',        'url' => ['url' => home_url('/contact-us/')]],
			['label' => 'FAQs',           'url' => ['url' => home_url('/faqs/')]],
		];
	}

	/**
	 * Returns a parent→children tree for the accordion drawer nav.
	 * Each entry: [ label, url, target, children[] ]
	 */
	private function nav_tree(): array
	{
		$s    = $this->get_settings_for_display();
		$tree = [];

		if ('wp_menu' === ($s['nav_source'] ?? '') && ! empty($s['wp_menu_id'])) {
			$raw = wp_get_nav_menu_items((int) $s['wp_menu_id']);
			if (! is_array($raw)) return $tree;

			// First pass: index all items by ID
			$index = [];
			foreach ($raw as $item) {
				$index[$item->ID] = [
					'label'    => $item->title,
					'url'      => $item->url ?? '',
					'target'   => $item->target ?: '',
					'parent'   => (int) $item->menu_item_parent,
					'children' => [],
				];
			}

			// Second pass: attach children to parents
			foreach ($index as $id => $node) {
				if ($node['parent'] && isset($index[$node['parent']])) {
					$index[$node['parent']]['children'][] = $node;
				}
			}

			// Third pass: collect top-level items
			foreach ($index as $id => $node) {
				if (! $node['parent'] || ! isset($index[$node['parent']])) {
					$tree[] = $node;
				}
			}

			return $tree;
		}

		// Manual items — no hierarchy
		foreach ($s['nav_items'] ?? [] as $item) {
			$ud  = $item['url'] ?? [];
			$url = is_array($ud) ? ($ud['url'] ?? '') : '';
			if (! $url) continue;
			$tree[] = ['label' => $item['label'] ?? '', 'url' => $url, 'target' => ! empty($ud['is_external']) ? '_blank' : '', 'children' => []];
		}
		return $tree;
	}

	// Keep for backwards compatibility — used nowhere now but left to avoid fatal errors
	private function all_nav_items(): array
	{
		return [];
	}
}
