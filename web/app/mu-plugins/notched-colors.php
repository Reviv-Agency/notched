<?php
/**
 * Plugin Name: Notched — Brand Colours
 * Description: Seeds this site's AEW (aew-*) Elementor global colours. Edit the palette to recolour the site (or use Elementor → Site Settings → Global Colours).
 */

defined('ABSPATH') || exit;

add_action('admin_init', function () {
	if (get_option('notched_colors_seeded') === '1') {
		return;
	}
	$palette = [
		'aew-cta' => ['Main CTA', '#876137'],
		'aew-cta-hover' => ['CTA Hover', '#6E4F2D'],
		'aew-background' => ['Background', '#F6F0EC'],
		'aew-text' => ['Text', '#141C19'],
		'aew-cards' => ['Cards', '#FFFFFF'],
		'aew-lines' => ['Lines & Accents', '#BFC0BF'],
		'aew-secondary-bg' => ['Headers (H1-H2)', '#2A4F41'],
		'aew-secondary-accent' => ['Secondary Accent', '#093328'],
		'aew-misc-accent' => ['Misc Accent', '#3B413F'],
		'aew-secondary-cards' => ['Secondary Cards', '#7D958D'],
		'aew-gold-light' => ['Gold Light', '#CDB797'],
		'aew-black' => ['Black', '#000000'],
		'aew-gold-tint' => ['Gold Tint', '#A27E4D1A'],
	];
	$kid = (int) get_option('elementor_active_kit');
	if (!$kid) {
		return; // Elementor not ready yet — retries on the next admin load.
	}
	$settings = get_post_meta($kid, '_elementor_page_settings', true);
	if (!is_array($settings)) {
		$settings = [];
	}
	$custom = (isset($settings['custom_colors']) && is_array($settings['custom_colors'])) ? $settings['custom_colors'] : [];
	$existing = [];
	foreach ($custom as $c) {
		if (isset($c['_id'])) {
			$existing[$c['_id']] = true;
		}
	}
	$added = false;
	foreach ($palette as $id => $def) {
		if (isset($existing[$id])) {
			continue;
		}
		$custom[] = ['_id' => $id, 'title' => $def[0], 'color' => $def[1]];
		$added = true;
	}
	if ($added) {
		$settings['custom_colors'] = $custom;
		update_post_meta($kid, '_elementor_page_settings', $settings);
		if (class_exists('\Elementor\Core\Files\CSS\Post')) {
			try {
				\Elementor\Core\Files\CSS\Post::create($kid)->update();
			} catch (\Throwable) {
			}
		}
	}
	update_option('notched_colors_seeded', '1');
});
