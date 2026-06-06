<?php
/**
 * Plugin Name: Agency Elementor Widgets
 * Description: Universal, configurable Elementor widgets with design tokens for agency sites.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.3
 * Author: Agency
 * Text Domain: agency-elementor-widgets
 *
 * @package Agency_Elementor_Widgets
 */

defined( 'ABSPATH' ) || exit;

define( 'AEW_VERSION', '1.13.8' );
define( 'AEW_PLUGIN_FILE', __FILE__ );
define( 'AEW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AEW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AEW_PLUGIN_DIR . 'includes/class-design-tokens.php';
require_once AEW_PLUGIN_DIR . 'includes/class-rich-text.php';
require_once AEW_PLUGIN_DIR . 'includes/class-color-vars.php';
require_once AEW_PLUGIN_DIR . 'includes/class-widget-assets.php';
require_once AEW_PLUGIN_DIR . 'includes/class-settings.php';
require_once AEW_PLUGIN_DIR . 'includes/class-cpt-testimonial.php';
require_once AEW_PLUGIN_DIR . 'includes/class-lead-store.php';
require_once AEW_PLUGIN_DIR . 'includes/class-post-engagement.php';
require_once AEW_PLUGIN_DIR . 'includes/class-widgets-loader.php';
require_once AEW_PLUGIN_DIR . 'includes/class-plugin.php';

AEW\Lead_Store::init();
AEW\Post_Engagement::init();

/**
 * @return void
 */
function aew_activate(): void {
	AEW\Design_Tokens::seed_defaults();
	AEW\Cpt_Testimonial::register_post_type();
	AEW\Lead_Store::install();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'aew_activate' );

/**
 * Shim Elementor Pro frontend JS that calls APIs which only exist in the
 * Elementor editor (elementorCommon.helpers.softDeprecated, etc.).
 *
 * Pro 3.7.2 was built against Elementor 3.31+ where these were available
 * on the frontend; Elementor 3.30.4 no longer loads common.js on the
 * frontend. Without this shim Pro's preloaded-elements-handlers.js throws
 * a TypeError that halts the entire Pro module pipeline (frontend.js,
 * scroll utilities, widget handlers — all dead). That broke Footer V2's
 * scroll parallax even though nothing in Footer V2 itself was wrong.
 *
 * The shim is a no-op: softDeprecated() in real Elementor only logs a
 * deprecation warning to the console — skipping that is fine.
 */
/**
 * Renders a defensive shim that ensures elementorCommon.helpers has
 * no-op deprecation methods. Uses a Proxy on .helpers so any later
 * code that assigns `elementorCommon.helpers = {...}` still appears
 * to have softDeprecated/hardDeprecated/deprecatedMessage as no-ops.
 */
function aew_print_elementor_compat_shim(): void {
	?>
<script id="aew-elementor-compat-shim-<?php echo esc_attr( current_action() ); ?>">
/* AEW compat shim — Pro 3.7.2 calls elementorCommon.helpers.softDeprecated()
 * which doesn't exist on the frontend in Elementor 3.30.4. Define it as a no-op
 * and protect via a Proxy so later reassignment can't wipe it. */
(function(){
	var noop = function(){};
	var stubs = { softDeprecated: noop, hardDeprecated: noop, deprecatedMessage: noop };

	function ensure(target) {
		target = target || {};
		Object.keys(stubs).forEach(function(k){
			if (typeof target[k] !== 'function') target[k] = stubs[k];
		});
		return target;
	}

	function install() {
		var ec = window.elementorCommon || {};
		ec.helpers = ensure(ec.helpers);
		window.elementorCommon = ec;
	}

	install();

	// Also re-install lazily — if any later script reassigns elementorCommon
	// or its helpers, our getter re-applies the stubs on next access.
	try {
		var realCommon = window.elementorCommon;
		Object.defineProperty(window, 'elementorCommon', {
			configurable: true,
			get: function () { ensure(realCommon.helpers); return realCommon; },
			set: function (v) {
				if (v && typeof v === 'object') {
					v.helpers = ensure(v.helpers);
				}
				realCommon = v;
			},
		});
	} catch (e) { /* property already non-configurable — install() already ran */ }
})();
</script>
	<?php
}

// Print the shim at multiple points so it always runs before Pro scripts
// regardless of how WP orders enqueues.
add_action( 'wp_head', 'aew_print_elementor_compat_shim', 1 );
add_action( 'wp_print_footer_scripts', 'aew_print_elementor_compat_shim', 1 );

add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					if ( ! current_user_can( 'activate_plugins' ) ) {
						return;
					}
					echo '<div class="notice notice-warning"><p>';
					echo esc_html__( 'Agency Elementor Widgets requires Elementor to be installed and active.', 'agency-elementor-widgets' );
					echo '</p></div>';
				}
			);
			return;
		}

		AEW\Plugin::instance();
	}
);
