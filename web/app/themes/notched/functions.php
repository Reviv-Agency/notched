<?php
/*
 * Disable WooCommerce product-gallery ZOOM (hello-elementor enables it).
 * jquery.zoom adds a hover-zoom .zoomImg overlay that (a) magnifies on hover and
 * (b) sits on top of the image link, swallowing clicks so only the magnifier icon
 * opened the lightbox. Removing zoom restores whole-image click + kills the hover
 * zoom. We keep the lightbox + slider supports. Runs after the parent theme's
 * after_setup_theme (priority 11 > parent's default 10).
 */
add_action('after_setup_theme', function () {
    remove_theme_support('wc-product-gallery-zoom');
}, 11);

add_action('wp_enqueue_scripts', function () {
    // Version by file mtime so edits to style.css bust the browser cache.
    $css = get_stylesheet_directory() . '/style.css';
    $ver = is_readable($css) ? (string) filemtime($css) : null;
    wp_enqueue_style('notched-style', get_stylesheet_uri(), [], $ver);

    /*
     * Single product pages render the Related Products section with the
     * Products Slider V2 look (see woocommerce/single-product/related.php).
     * That markup needs the widget's CSS + JS, which the agency-elementor-widgets
     * plugin registers on `init` under the `aew-widget-products-slider-v2` handle.
     * Enqueue them here so the slider styles + carousel behaviour load on the
     * product page even though no Elementor widget is present.
     */
    if (function_exists('is_product') && is_product()) {
        if (class_exists('AEW\\Widget_Assets')) {
            $handle = \AEW\Widget_Assets::handle('products-slider-v2'); // aew-widget-products-slider-v2
            if (wp_style_is($handle, 'registered')) {
                wp_enqueue_style('aew-tokens');
                wp_enqueue_style($handle);
            }
            if (wp_script_is($handle, 'registered')) {
                wp_enqueue_script($handle);
            }
        }

        /*
         * Convert WooCommerce variation dropdowns into Wix-style button-boxes
         * (sizes / timber / power / end-cut) and colour swatches (stain / roof).
         * The JS keeps the native <select> as WC's source of truth.
         */
        $dir = get_stylesheet_directory();
        $uri = get_stylesheet_directory_uri();
        $cssv = is_readable("$dir/assets/woo-variations.css") ? (string) filemtime("$dir/assets/woo-variations.css") : null;
        $jsv  = is_readable("$dir/assets/woo-variations.js")  ? (string) filemtime("$dir/assets/woo-variations.js")  : null;
        $btnv = is_readable("$dir/assets/woo-buttons.css")    ? (string) filemtime("$dir/assets/woo-buttons.css")    : null;
        $galv = is_readable("$dir/assets/woo-gallery.js")     ? (string) filemtime("$dir/assets/woo-gallery.js")     : null;
        wp_enqueue_style('notched-woo-variations', "$uri/assets/woo-variations.css", [], $cssv);
        wp_enqueue_style('notched-woo-buttons', "$uri/assets/woo-buttons.css", [], $btnv);
        wp_enqueue_script('notched-woo-variations', "$uri/assets/woo-variations.js", ['jquery'], $jsv, true);
        wp_enqueue_script('notched-woo-gallery', "$uri/assets/woo-gallery.js", [], $galv, true);

        // slug => #hex map for the colour attributes, read from term meta `swatch_hex`.
        $hex = [];
        foreach (['pa_stain-color', 'pa_roof-color'] as $tax) {
            if (!taxonomy_exists($tax)) { continue; }
            foreach (get_terms(['taxonomy' => $tax, 'hide_empty' => false]) as $t) {
                $h = get_term_meta($t->term_id, 'swatch_hex', true);
                if ($h) { $hex[$t->slug] = $h; }
            }
        }
        wp_localize_script('notched-woo-variations', 'NotchedSwatchHex', $hex);

        // slug => image URL for the STAIN COLOR swatches (term meta `stain_img`),
        // used to preview the selected/hovered stain below the swatch row.
        $stainImg = [];
        if (taxonomy_exists('pa_stain-color')) {
            foreach (get_terms(['taxonomy' => 'pa_stain-color', 'hide_empty' => false]) as $t) {
                $img = get_term_meta($t->term_id, 'stain_img', true);
                if ($img) { $stainImg[$t->slug] = ['url' => $img, 'name' => $t->name]; }
            }
        }
        wp_localize_script('notched-woo-variations', 'NotchedStainImg', $stainImg);
    }
}, 20);

/*
 * Show up to 8 related products on the single product page (default is 4), so the
 * Products-Slider-V2-styled related section (woocommerce/single-product/related.php)
 * has enough cards to scroll through — 4 are visible at a time, the rest scroll.
 */
add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 8;
    $args['columns']        = 8; // prevent WC from chunking into rows; our slider lays them out
    return $args;
});
