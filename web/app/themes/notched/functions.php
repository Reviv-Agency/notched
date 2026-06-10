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

/*
 * Anti-FOUC for the variation swatches: on product pages, print an inline <head>
 * script that immediately marks <html> with `aew-swatch-pending`. CSS uses that
 * to hide the native variation dropdowns from the very first paint, so the user
 * never sees dropdowns flash before woo-variations.js converts them to swatches.
 * The JS removes the class once conversion is done; a safety timeout also removes
 * it so the form is never permanently hidden if JS fails.
 */
add_action('wp_head', function () {
    if (!function_exists('is_product') || !is_product()) { return; }
    echo "<script>document.documentElement.classList.add('aew-swatch-pending');"
       . "setTimeout(function(){document.documentElement.classList.remove('aew-swatch-pending');},3000);</script>\n";
}, 0);

/*
 * Dynamic product name in the Single Product template's marketing widgets.
 * The template (id 1707) applies to ALL products, so its CTA / feature-band copy
 * uses the token {{product}} (or {{Product}} / {{PRODUCT}}). On a product page we
 * replace that token in each Elementor widget's rendered output with the current
 * product's name — so "WHY CHOOSE THE {{product}}?" becomes the real product name
 * per product. Runs via Elementor's widget render_content filter (AEW widgets
 * extend Elementor\Widget_Base, so their output passes through here).
 */
add_filter('elementor/widget/render_content', function ($content) {
    if (!function_exists('is_product') || !is_product()) { return $content; }
    if (strpos($content, '{{') === false) { return $content; }
    $name = '';
    $pid = get_queried_object_id();
    if ($pid) { $name = get_the_title($pid); }
    if ('' === $name) { return $content; }
    return strtr($content, [
        '{{product}}' => $name,
        '{{Product}}' => $name,
        '{{PRODUCT}}' => $name,
    ]);
}, 10);

/*
 * Meta description for search engines. There is no SEO plugin on this stack and
 * Elementor pages keep post_content empty, so derive a description per context:
 * manual excerpt → product short description → term description → the site-wide
 * brand default. Output early in <head> so crawlers find it before asset tags.
 */
add_action('wp_head', function () {
    $default = 'Notched builds handcrafted timber pergola, pavilion and zen den kits — '
             . 'precision pre-cut, dovetail-notched and ready to assemble in your backyard.';
    $desc = '';
    if (is_front_page()) {
        $desc = $default;
    } elseif (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            if ('' !== $post->post_excerpt) {
                $desc = $post->post_excerpt;
            } elseif ('' !== $post->post_content) {
                $desc = wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 30, '…');
            }
        }
    } elseif (is_category() || is_tag() || is_tax()) {
        $desc = term_description();
    }
    $desc = trim(wp_strip_all_tags((string) $desc));
    if ('' === $desc) { $desc = $default; }
    printf("<meta name=\"description\" content=\"%s\" />\n", esc_attr($desc));
}, 1);

/*
 * Fully permissive robots.txt: every crawler, AI agent and bot may access the
 * whole site (requested 2026-06-10). Replaces WordPress/WooCommerce's default
 * directives (which disallowed wp-admin and some Woo upload paths) with a
 * blanket Allow, plus the core sitemap for discovery.
 */
add_filter('robots_txt', function () {
    return "User-agent: *\nAllow: /\n\nSitemap: " . home_url('/wp-sitemap.xml') . "\n";
}, 99);

/*
 * Drop WooCommerce's render-blocking CSS (and its marketing/tracking JS) on
 * pages with no WooCommerce context. The homepage & landing pages render
 * product cards through AEW widgets with their own styles; Woo's stylesheets
 * cost ~750ms of render-blocking time there. Cart/checkout/account/product
 * and taxonomy pages keep everything.
 */
add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_woocommerce')) { return; }
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) { return; }
    foreach (['woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen', 'wc-blocks-style', 'wc-blocks-vendors-style'] as $handle) {
        wp_dequeue_style($handle);
    }
    foreach (['sourcebuster-js', 'wc-order-attribution', 'woocommerce', 'wc-add-to-cart', 'jquery-blockui', 'js-cookie'] as $handle) {
        wp_dequeue_script($handle);
    }
}, 99);

/*
 * Remove jquery-migrate on the front end. Nothing in the stack relies on the
 * pre-3.x jQuery APIs it shims (Elementor 4.x, Woo 10.x and our vanilla-JS
 * widgets are all migrate-clean), and it is a render-blocking head script.
 */
add_action('wp_default_scripts', function ($scripts) {
    if (is_admin() || !isset($scripts->registered['jquery'])) { return; }
    $scripts->registered['jquery']->deps = array_diff(
        $scripts->registered['jquery']->deps,
        ['jquery-migrate']
    );
});

/*
 * Wrap Elementor full-width page content in <main id="content">. Pages using
 * the "Elementor Full Width" template (home, landing pages) bypass the parent
 * theme's template-parts, so they shipped with no <main> landmark and a
 * "Skip to content" link pointing at a #content that didn't exist — both
 * Lighthouse accessibility failures.
 */
add_action('elementor/page_templates/header-footer/before_content', function () {
    echo '<main id="content">';
});
add_action('elementor/page_templates/header-footer/after_content', function () {
    echo '</main>';
});

/*
 * Preload the hero's self-hosted display fonts. Teko (H1) and Lato (body)
 * are discovered late — HTML → google-fonts CSS → woff2 — so the headline
 * painted in the fallback font and reflowed when Teko arrived (CLS 0.16 on
 * mobile PSI). Preloading lets the first paint use the real fonts.
 * Filenames are Elementor's content-hashed exports; guarded by file_exists
 * so a font re-export can't leave dangling preloads.
 */
add_action('wp_head', function () {
    $base = wp_upload_dir()['basedir'] . '/elementor/google-fonts/fonts/';
    $url  = wp_upload_dir()['baseurl'] . '/elementor/google-fonts/fonts/';
    $fonts = [
        'teko-lyjndg7kme0gfan9pq.woff2',       // Teko 400-700 latin (H1/H2/buttons)
        'lato-s6uyw4bmutphjx4wxg.woff2',        // Lato 400 latin (body)
        'lato-s6u9w4bmutphh6uvswipgq.woff2',    // Lato 700 latin (bold/price)
    ];
    foreach ($fonts as $f) {
        if (is_readable($base . $f)) {
            printf("<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url($url . $f));
        }
    }

    /*
     * Page-aware preloads, derived from the page's Elementor data so they only
     * print where the asset is actually used:
     * - Hero V2's mobile LCP image. The widget used to emit its own <link
     *   rel=preload>, but that sits mid-<body>, so on slow connections the
     *   browser discovered it ~670ms late (PSI "resource load delay").
     *   Emitting it here in <head> starts the download with the document.
     * - Welcome V2's self-hosted Dancing Script woff2, whose late arrival
     *   from Google's CDN previously reflowed the section (CLS 0.19 desktop).
     */
    if (is_singular() && defined('AEW_PLUGIN_URL')) {
        $data = get_post_meta(get_queried_object_id(), '_elementor_data', true);
        if (is_string($data) && '' !== $data) {
            if (strpos($data, 'agency-hero-v2') !== false) {
                $hero = '';
                if (preg_match('/"mobile_image":\{[^{}]*?"url":"([^"]*)"/', $data, $m)) {
                    $hero = str_replace('\\/', '/', $m[1]);
                }
                if ('' === $hero) {
                    // Default media values aren't saved into _elementor_data.
                    $hero = AEW_PLUGIN_URL . 'assets/widgets/hero-v2/images/mobile image.avif';
                }
                printf("<link rel=\"preload\" as=\"image\" fetchpriority=\"high\" href=\"%s\" media=\"(max-width: 1024px)\">\n", esc_url($hero));
            }
            if (strpos($data, 'agency-welcome-v2') !== false) {
                printf(
                    "<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n",
                    esc_url(AEW_PLUGIN_URL . 'assets/widgets/welcome-v2/fonts/dancing-script-700-latin.woff2')
                );
            }
        }
    }
}, 2);

/*
 * Load the external Google Fonts stylesheets (AEW's Teko/Lato + the Welcome
 * widget's Dancing Script) asynchronously: fetched as media="print", swapped
 * to "all" onload. They use font-display:swap anyway, and Elementor self-hosts
 * Teko/Lato on Elementor pages, so nothing visible blocks on fonts.googleapis.
 */
add_filter('style_loader_tag', function ($tag, $handle) {
    /*
     * Also deferred: widget stylesheets for sections that sit below the fold
     * on every current layout (the hero/banner fills the first viewport, so
     * none of these are visible at first paint). Deferring them takes ~14
     * requests off the render-blocking critical path (~3.5s est. on slow 4G).
     * Do NOT add header-v2 / hero-v2 / banner-hero-v2 / products-slider-v2 /
     * tokens here — those style above-the-fold content.
     */
    static $async_handles = [
        'aew-google-fonts',
        // NOT aew-welc-script-font: now a tiny self-hosted @font-face whose
        // woff2 is preloaded — it must be present at first paint or the
        // Dancing Script swap reflows the section (CLS).
        'elementor-gf-local-roboto',     // kit-default fonts, unused by the
        'elementor-gf-local-robotoslab', // visible widgets (all Teko/Lato)
        'aew-widget-benefits-v2',
        'aew-widget-split-media',
        'aew-widget-media-cta',
        'aew-widget-testimonials-v2',
        'aew-widget-region-cards-v2',
        'aew-widget-consultation-form-v2',
        'aew-widget-parallax-image-v2',
        'aew-widget-welcome-v2',
        'aew-widget-feature-rows-v2',
        'aew-widget-cta-banner-v2',
        'aew-widget-faq-v2',
        'aew-widget-footer-v2',
        'aew-widget-icon-cards',
        // NOT sticky-image: it positions an overlay badge; unstyled it renders
        // in normal flow and shoves the hero down (CLS 0.76 when deferred).
    ];
    if (!in_array($handle, $async_handles, true)) { return $tag; }
    if (strpos($tag, "media='print'") !== false || strpos($tag, 'onload=') !== false) { return $tag; }
    return str_replace(
        "media='all'",
        "media='print' onload=\"this.media='all'\"",
        $tag
    );
}, 10, 2);

/*
 * Preconnect to the Google Fonts origins. Elementor self-hosts most fonts, but
 * the Dancing Script face still loads from fonts.googleapis.com/fonts.gstatic.com;
 * warming those connections removes ~2 round-trips from the critical path.
 */
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = ['href' => 'https://fonts.googleapis.com'];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
    }
    return $urls;
}, 10, 2);

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

        // slug => image URL for the END CUT boxes (term meta `cut_img`),
        // previewed the same way as the stain image.
        $cutImg = [];
        if (taxonomy_exists('pa_end-cut')) {
            foreach (get_terms(['taxonomy' => 'pa_end-cut', 'hide_empty' => false]) as $t) {
                $img = get_term_meta($t->term_id, 'cut_img', true);
                if ($img) { $cutImg[$t->slug] = ['url' => $img, 'name' => $t->name]; }
            }
        }
        wp_localize_script('notched-woo-variations', 'NotchedCutImg', $cutImg);
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
