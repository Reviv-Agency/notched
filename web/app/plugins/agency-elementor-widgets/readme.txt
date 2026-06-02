=== Agency Elementor Widgets ===
Contributors: agency
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.3
Requires Plugins: elementor
Stable tag: 1.0.0
License: GPLv2 or later

Universal Elementor widgets with configurable design tokens (colors & typography).

== Description ==

* Widget category: Agency Widgets
* Design tokens: Settings → Agency Widgets, or `aew_design_tokens` filter
* CSS variables: `--aew-color-*`, `--aew-font-*`, `--aew-text-*`

== Widgets ==

* Header — logo, WP menu or manual nav, phone, CTA, mobile drawer
* Hero — background image, overlay copy, feature cards
* Heading Band — centered prompt heading
* Feature Rows — alternating image/text rows
* Split Media — image + copy section
* FAQ Accordion — expandable Q&A list with closing copy

== Other sites ==

Override tokens in wp-admin or via:

add_filter( 'aew_design_tokens', function ( $tokens ) {
    $tokens['color_blue'] = '#0066cc';
    return $tokens;
} );

== Fonts ==

Default: DM Serif Display (headings), Poppins (body). Loaded via Google Fonts when the plugin is active.
