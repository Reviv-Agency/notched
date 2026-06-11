<?php
/**
 * Plugin Name: {{PROJECT}} — Brand Colours
 * Description: Seeds this site's AEW (aew-*) Elementor global colours. Edit the palette to recolour the site (or use Elementor → Site Settings → Global Colours).
 *
 * ---------------------------------------------------------------------------
 * HOW TO USE THIS TEMPLATE
 * ---------------------------------------------------------------------------
 * Copy this file to web/app/mu-plugins/<project>-colors.php, then:
 *   1. Set the Plugin Name in the header above to your project name.
 *   2. Pick a unique option key (replace `project_colors_seeded` everywhere
 *      below) so this site's seed flag does not collide with another site.
 *   3. Replace the placeholder grey hexes in $palette with your real brand
 *      colours.
 *   4. Load wp-admin once. On that admin load the aew-* Elementor global
 *      colours are seeded into the active kit (idempotent — missing colours
 *      are added, existing ones are never overwritten) and the kit CSS is
 *      regenerated.
 * ---------------------------------------------------------------------------
 */

defined('ABSPATH') || exit;

add_action('admin_init', function () {
	if (get_option('project_colors_seeded') === '1') {
		return;
	}
	$palette = [
		'aew-cta' => ['Main CTA', '#444444'],
		'aew-cta-hover' => ['CTA Hover', '#333333'],
		'aew-background' => ['Background', '#EEEEEE'],
		'aew-text' => ['Text', '#222222'],
		'aew-cards' => ['Cards', '#FFFFFF'],
		'aew-lines' => ['Lines & Accents', '#CCCCCC'],
		'aew-secondary-bg' => ['Headers (H1-H2)', '#555555'],
		'aew-secondary-accent' => ['Secondary Accent', '#666666'],
		'aew-misc-accent' => ['Misc Accent', '#777777'],
		'aew-secondary-cards' => ['Secondary Cards', '#DDDDDD'],
		'aew-gold-light' => ['Gold Light', '#888888'],
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
	update_option('project_colors_seeded', '1');
});
