<?php
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
    }
}, 20);
