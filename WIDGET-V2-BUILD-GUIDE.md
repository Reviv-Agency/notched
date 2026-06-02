# Notched Widget V2 — Build Guide

Reference for building the **next** widget in the Notched Elementor plugin. Header V2 and Footer V2 are both complete and serve as the reference implementations. Mirror their conventions exactly — they encode every gotcha we've already burned through.

**Source repos:**
- Plugin root: `web/app/plugins/agency-elementor-widgets/`
- Child theme: `web/app/themes/notched/`
- Brand: [design-system.md](design-system.md) (single source of truth for colors/type/spacing)

**Existing V2 widgets to reference:**
- [widgets/class-widget-header-v2.php](web/app/plugins/agency-elementor-widgets/widgets/class-widget-header-v2.php) (~446 lines)
- [widgets/class-widget-footer-v2.php](web/app/plugins/agency-elementor-widgets/widgets/class-widget-footer-v2.php) (~460 lines)
- [assets/widgets/header-v2/css/header-v2.css](web/app/plugins/agency-elementor-widgets/assets/widgets/header-v2/css/header-v2.css) (~720 lines)
- [assets/widgets/footer-v2/css/footer-v2.css](web/app/plugins/agency-elementor-widgets/assets/widgets/footer-v2/css/footer-v2.css) (~360 lines)

---

## 1. Naming convention

For a hypothetical "Hero V2" widget, the names cascade like this — apply the same shape to whatever you're building:

| Aspect | Value |
|---|---|
| Widget class | `Widget_Hero_V2` in `widgets/class-widget-hero-v2.php` |
| Elementor widget name | `agency-hero-v2` |
| Asset slug (folder) | `hero-v2` (folder `assets/widgets/hero-v2/`) |
| BEM block | `aew-hev2` (or a similar 4-letter abbreviation — keep it short and unique) |
| Data attribute | `data-aew-hero-v2` |
| JS init flag | `dataset.aewHev2Init` |

Rules:
- New widgets are **additive**. Never modify or replace existing widgets (`class-widget-header.php`, etc.) — leave the old as a fallback. The site stack only swaps to V2 when an Elementor template/page actually uses the new widget.
- BEM block prefix is always `aew-` + short widget abbreviation. Every element inside is `aew-{block}__{element}`; every modifier is `aew-{block}__{element}--{modifier}`.

---

## 2. AEW_VERSION cache busting — CRITICAL

CSS and JS are enqueued with `AEW_VERSION` as the `?ver=` query string. If you edit a stylesheet but don't bump the version, browsers serve the cached file and your changes appear not to work.

- Constant: [agency-elementor-widgets.php:16](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php#L16) — currently `1.2.1` as of last update
- **Bump it on every CSS/JS change** while iterating (e.g. `1.2.1` → `1.2.2`)
- Without the bump, you'll waste hours debugging stale CSS

---

## 3. File layout for a new widget

```
agency-elementor-widgets/
├── agency-elementor-widgets.php         # AEW_VERSION + Elementor compat shim
├── includes/
│   ├── class-widget-assets.php          # asset registration map (register new slug here)
│   └── class-widgets-loader.php         # widget registration (register new class here)
├── widgets/
│   ├── class-widget-header-v2.php       # reference
│   ├── class-widget-footer-v2.php       # reference
│   └── class-widget-{NEW}-v2.php        # ← new file
└── assets/
    └── widgets/
        ├── header-v2/{css,js,images}/   # reference
        ├── footer-v2/{css,js,images}/   # reference
        └── {NEW}-v2/{css,js,images}/    # ← new folder
```

---

## 4. Three registration points (edit existing files)

These three edits must happen for any new widget to load. Forgetting #2 is the most common cause of "widget doesn't appear in the panel."

### 4.1 Register the widget class
[includes/class-widgets-loader.php](web/app/plugins/agency-elementor-widgets/includes/class-widgets-loader.php) — inside `register_widgets()`:
```php
require_once AEW_PLUGIN_DIR . 'widgets/class-widget-NEW-v2.php';
// …
$widgets_manager->register( new Widget_NEW_V2() );
```

### 4.2 Register the asset slug
[includes/class-widget-assets.php](web/app/plugins/agency-elementor-widgets/includes/class-widget-assets.php) — inside `register_defaults()`:
```php
self::register_widget(
    'NEW-v2',
    [
        'style'      => 'css/NEW-v2.css',
        'script'     => 'js/NEW-v2.js',          // omit if no JS
        'style_deps' => [ 'aew-tokens' ],
    ]
);
```

### 4.3 Bump version
[agency-elementor-widgets.php:16](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php#L16) — increment `AEW_VERSION`.

---

## 5. Widget class skeleton (PHP)

The pattern below works for any widget. Copy [class-widget-footer-v2.php](web/app/plugins/agency-elementor-widgets/widgets/class-widget-footer-v2.php) as your starting point — it's the most modern and includes WP-menu integration, repeater fields, responsive controls, and Group_Control_Typography usage.

```php
namespace AEW;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_NEW_V2 extends Widget_Base {
    private const ASSET_SLUG = 'NEW-v2';

    public function get_name(): string      { return 'agency-NEW-v2'; }
    public function get_title(): string     { return esc_html__( 'NEW V2 (Notched)', 'agency-elementor-widgets' ); }
    public function get_icon(): string      { return 'eicon-XYZ'; }
    public function get_categories(): array { return [ 'agency-widgets' ]; }
    public function get_keywords(): array   { return [ 'NEW', 'notched' ]; }

    public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
    public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

    /**
     * Re-point Elementor's built-in _padding control to OUR inner wrapper
     * so the outer block keeps its full-bleed background while sidebar
     * padding controls behave as expected.
     *
     * IMPORTANT — leave every default EMPTY. The re-pointed _padding common
     * control is emitted by Elementor as a SINGLE non-responsive rule (the
     * base `default` only — tablet_default/mobile_default are ignored for the
     * generated page CSS) at a higher specificity than your stylesheet. A
     * non-empty default here clobbers the stylesheet at EVERY breakpoint.
     * Let the stylesheet own the responsive X padding (see §6.5); this
     * override only re-points the control's selector so a value the user
     * types in the sidebar still lands on the inner wrapper.
     */
    public function get_stack( $with_common_controls = true ) {
        $stack = parent::get_stack( $with_common_controls );
        if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
            $stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-NEW__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
            $stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
            $stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
            $stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
        }
        return $stack;
    }

    protected function register_controls(): void {
        // CONTENT tab
        $this->controls_logo();
        $this->controls_nav();
        // STYLE tab
        $this->style_body();
        $this->style_typography();
    }

    // Each control section is its own private method — keeps register_controls() readable
    private function controls_logo(): void { /* … */ }
    private function style_body():    void { /* … */ }

    protected function render(): void {
        $s = $this->get_settings_for_display();
        // Resolve colour controls → inline CSS vars on the wrapper (§6.8).
        $color_vars = Color_Vars::build( $this, $s, [ /* key => --aew-NEW-var */ ] );
        if ( '' !== $color_vars ) { $this->add_render_attribute( 'wrapper', 'style', $color_vars ); }
        // build the rest of the vars, then echo HTML
    }

    // Reusable helpers
    private function parse_link( $d ): array {
        if ( ! is_array( $d ) ) return [ 'url' => '', 'target' => '', 'rel' => '' ];
        $t = ! empty( $d['is_external'] ) ? '_blank' : '';
        $r = $t ? 'noopener' : '';
        if ( ! empty( $d['nofollow'] ) ) $r .= ' nofollow';
        return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
    }
}
```

Key points:
- **Namespaced** under `AEW;` — no exceptions.
- `get_style_depends()` always includes both `'aew-tokens'` AND the widget's own handle.
- `get_stack()` override re-points `_padding` to the inner wrapper. Without this, Elementor's built-in padding controls fight your full-bleed background.
- Split controls into themed private methods (`controls_*` for Content tab, `style_*` for Style tab). Don't put everything in `register_controls()` directly.
- **Colour controls go through `Color_Vars::build()` in `render()`** (§6.8) — never paint a property straight from a colour control's `selectors`, or global-colour picks silently break on live (gotcha #19).

---

## 6. CSS conventions — variables and tokens

### 6.1 Brand tokens at the top of every widget CSS

Both Header V2 and Footer V2 now scope brand tokens to the widget block. Source of truth is [design-system.md](design-system.md) — copy the canonical values into every new widget's CSS. (Long-term these should hoist into the `aew-tokens` stylesheet that every widget depends on, but the current convention is one definition per widget for explicitness and to avoid coupling.)

```css
/* ── Brand tokens ────────────────────────────────────────────────────────── */
/* Scoped to .aew-NEW so they don't leak into other widgets. Source of truth
   is design-system.md. Update there first; copy here second. */
.aew-NEW {
  --notched-cards:             #FFFFFF;
  --notched-background:        #F6F0EC;
  --notched-lines:             #BFC0BF;
  --notched-misc-accent:       #3B413F;
  --notched-secondary-cards:   #7D958D;
  --notched-secondary-bg:      #2A4F41;
  --notched-secondary-accent:  #093328;
  --notched-text:              #141C19;
  --notched-cta:               #AA7D44;
  --notched-cta-hover:         #876137;
}
```

> **CTA colors:** default `#AA7D44`, hover `#876137` — this is the canonical
> order in [design-system.md](design-system.md), and what every live V2 widget
> uses. (Earlier drafts had these inverted; don't copy old code.)

Then use `var(--notched-cta)` everywhere in the stylesheet — never hardcode hex.

### 6.2 Fonts & type scale (per design-system.md)

**Families:**
- **Headings H1–H3+:** Teko SemiBold (600)
- **Subhead / Eyebrow:** Playfair Display Bold (700)
- **Body / Paragraph:** Lato Regular (400)

**Scale — follow these exactly; don't eyeball sizes** (line-height in %):

| Element | Desktop | Mobile | Line height |
|---|---|---|---|
| H1 | 80px | 48px | 85% |
| H2 | 64px | 40px | 85% |
| H3 | 40px | 24px | 85% |
| Subhead / Eyebrow | 20px | 20px | 100% |
| Paragraph | 18px | 14px | 140% |

**Buttons:** Teko SemiBold, 20px desktop / 16px mobile, line-height 85%,
padding `20px 24px 16px` (top/x/bottom), radius 8px, BG `#AA7D44` → hover
`#876137`, text `#FFFFFF`. (Mobile padding-top drops to 18px.)

When you expose typography via `Group_Control_Typography`, set these as the
`fields_options` defaults so the widget ships matching the guide even before
anyone touches the Style tab.

### 6.3 Strip Elementor's column padding (mandatory)

Elementor wraps every widget in a column with default padding. For full-bleed widgets you must zero it out. Add to the **top** of every widget CSS, replacing `NEW-v2` with the actual widget name:

```css
.elementor-column
  > .elementor-element-populated:has(.elementor-widget-agency-NEW-v2),
.e-con:has(> .e-con-inner > .elementor-widget-agency-NEW-v2),
.e-con:has(.elementor-widget-agency-NEW-v2) {
  --padding-top: 0px;
  --padding-right: 0px;
  --padding-bottom: 0px;
  --padding-left: 0px;
  padding: 0 !important;
}
```

### 6.4 Reset Elementor's button styles inside the widget

Elementor injects styles on `<button>` elements that bleed into your widget. Any button you render must explicitly reset them:

```css
.aew-NEW__toggle,
button.aew-NEW__item-row {
  border: 0 !important;
  background: transparent !important;
  background-color: transparent !important;
  box-shadow: none !important;
  -webkit-appearance: none;
  appearance: none;
}
```

### 6.5 Inner wrapper pattern — max-width + responsive X padding

The stylesheet (not the `_padding` control, see §5) owns the horizontal
padding. The canonical values across all V2 widgets:

- **Max width:** `1440px` (NOT 1360 — the whole stack was standardized to 1440)
- **X padding:** `40px` (2.5rem) on mobile/tablet → `80px` (5rem) on desktop (≥1025px)
- **Y padding** (for banded/section widgets, not header/footer bars): `64px`
  desktop → `32px` mobile, per design-system section Y

```css
.aew-NEW {
  background: var(--notched-secondary-bg);
  width: 100%;
  position: relative;
}

.aew-NEW__inner {
  max-width: 1440px;       /* standardized stack-wide */
  margin: 0 auto;
  padding: 0 80px;         /* desktop X: 5rem (banded widgets: 64px 80px) */
}

/* Tablet & mobile X: 2.5rem */
@media (max-width: 1024px) {
  .aew-NEW__inner { padding: 0 40px; }     /* banded widgets: 64px 40px */
}
@media (max-width: 768px) {
  .aew-NEW__inner { padding: 0 40px; }     /* banded widgets: 32px 40px */
}
```

Outer block carries the full-bleed background. **One** inner wrapper holds max-width + horizontal padding. Don't put `max-width` on the outer block — it breaks full-bleed.

> Why the stylesheet and not the control default? See §5 and gotcha #16 — a
> non-empty `_padding` default emits a single non-responsive rule that
> overrides the stylesheet at every breakpoint. Keep the control empty; put
> the responsive padding here.

### 6.6 Responsive breakpoints — base = desktop, step DOWN

These widgets are **desktop-first**: the base (un-queried) rules are the
desktop styles, then `max-width` media queries step values down. Header V2,
Hero V2, Footer V2 and Icon Cards all use:

- `@media (max-width: 1024px)` — tablet (columns collapse; X padding → 40px)
- `@media (max-width: 768px)` — mobile (single column; X padding stays 40px)

So the **base rule holds the desktop value** (e.g. `padding: 0 80px`) and the
≤1024 / ≤768 blocks override it (e.g. `padding: 0 40px`). The breakpoint cutoff
for "desktop vs. tablet" is **1024px**.

Always set responsive padding/spacing at both breakpoints when needed; don't rely on a single mobile-first cascade.

### 6.7 Section height tied to the viewport (e.g. Hero V2)

When a widget should fill the screen below the header, use a `dvh` calc with a
`vh` fallback and a header-height variable — and leave the matching Elementor
height control's **desktop default empty** (same emission gotcha as §5, see
gotcha #16) so the stylesheet wins:

```css
.aew-NEW {
  --aew-NEW-header-h: 64px;                 /* keep in sync with header-v2 */
  min-height: calc(100vh - var(--aew-NEW-header-h));   /* fallback */
  min-height: calc(100dvh - var(--aew-NEW-header-h));  /* desktop ≥1025 */
}
@media (max-width: 1024px) { .aew-NEW { min-height: 680px; } }
```

### 6.8 Color controls — the CSS-variable + render() pattern (MANDATORY)

**Never** point a colour control's `selectors` straight at a paint property
(`background-color: {{VALUE}}`, `color: {{VALUE}}`). Elementor's front-end CSS
generator **silently DROPS the value when the user binds it to a global colour**
(the editor's JS preview resolves the global, but the generated page CSS omits
it). Result: the colour shows in the editor and reverts to the stylesheet
default on the live page — the single most expensive bug in this plugin's
history (see gotcha #19). Plain-hex picks work either way; globals are the trap.

Every colour control uses a **three-part** pattern so the editor preview AND the
live page agree, for both hex and global picks:

**(a) Control → assign to a CSS variable on the wrapper** (drives the *editor*
live-preview, which still runs through `selectors`):

```php
$this->add_control( 'btn_bg', [
    'label'     => 'Background',
    'type'      => Controls_Manager::COLOR,
    'default'   => '#AA7D44',
    'selectors' => [ '{{WRAPPER}}' => '--aew-NEW-btn-bg: {{VALUE}};' ],
] );
```

Host the var on `{{WRAPPER}}` so every descendant inherits it. One var per
colour slot; name it `--aew-{block}-{slot}`.

**(b) `render()` → emit the RESOLVED value inline on the wrapper** (the *live*
guarantee). Use the shared global-aware helper
[includes/class-color-vars.php](web/app/plugins/agency-elementor-widgets/includes/class-color-vars.php) —
`Color_Vars::build()` reads the raw `__globals__` map and returns
`var(--e-global-color-<id>)` for global-bound slots, or the hex for plain picks.
Do **not** hand-roll this with `$s['btn_bg']`: `get_settings_for_display()`
returns an **empty string** for a global-bound key (gotcha #19), so inlining the
"resolved" setting yourself silently drops globals too.

```php
$color_vars = Color_Vars::build( $this, $s, [
    'btn_bg'       => '--aew-NEW-btn-bg',
    'btn_bg_hover' => '--aew-NEW-btn-bg-hover',
    'heading_color'=> '--aew-NEW-heading',
    // …one entry per colour control: setting_key => css_var
] );
// then append $color_vars to the wrapper's style attribute, e.g.
if ( '' !== $color_vars ) {
    $this->add_render_attribute( 'wrapper', 'style', $color_vars );
}
```

The kit defines `--e-global-color-<id>` site-wide, and Elementor keeps those in
the kit CSS even when it drops the per-widget rule — so the reference resolves
on the front end. The inline `style` wins over the `(a)` selector rule by
specificity on live; in the editor `(a)` drives the preview; both derive from
the same saved setting, so they never disagree.

**(c) Stylesheet → consume each var with the design-system token as fallback**
so untouched controls look identical to before:

```css
.aew-NEW__btn {
  background-color: var(--aew-NEW-btn-bg, var(--notched-cta));
  color:            var(--aew-NEW-btn-text, #FFFFFF);
}
.aew-NEW__btn:hover {
  background-color: var(--aew-NEW-btn-bg-hover, var(--notched-cta-hover));
}
```

**Heading colours need a specificity bump.** Elementor's kit ships
`.elementor-kit-N h1..h6 { color }` at specificity (0,1,1). A bare
`.aew-NEW__title { color }` rule is (0,1,0) and **loses** — the heading stays the
kit's default colour no matter what the control says (gotcha #20). Prefix the
wrapper class so the heading rule is (0,2,0) and wins:

```css
/* (0,2,0) beats the kit's .elementor-kit-N h1..h6 (0,1,1) */
.aew-NEW .aew-NEW__title { color: var(--aew-NEW-title, var(--notched-secondary-bg)); }
```

(Controls that target an `<a>`/`<span>`/`<div>` don't need this — only real
`<h1>`–`<h6>` headings collide with the kit rule. Controls whose `selectors`
already include a tag, like `h2.aew-NEW__title`, are already high enough.)

**Don't disable globals to dodge the bug.** An earlier draft put
`'global' => [ 'default' => '' ]` on benefits-v2's colours to stop global
binding. That's the wrong fix — it removes a feature instead of supporting it.
Use the (a)+(b)+(c) pattern and globals work everywhere.

> Every live V2 widget (hero, region-cards, products-slider, footer, split-media,
> benefits, media-cta) now uses this pattern — copy any of them.

---

## 7. JS skeleton (vanilla, no jQuery)

If the widget has any interactivity. Skip the JS file entirely if presentational. Footer V2 has scroll-parallax JS as a reference; Header V2 has drawer toggle/accordion JS.

```js
(function () {
    'use strict';

    var prefersReducedMotion =
        window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initWidget(el) {
        if (!el || el.dataset.aewNewInit === '1') return;  // idempotency guard
        el.dataset.aewNewInit = '1';

        if (prefersReducedMotion) return;

        // wire up event listeners
    }

    function boot() {
        document.querySelectorAll('[data-aew-NEW-v2]').forEach(initWidget);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    // Re-init when Elementor editor re-renders the widget
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(window).on('elementor/frontend/init', function () {
            if (typeof elementorFrontend === 'undefined') return;
            elementorFrontend.hooks.addAction('frontend/element_ready/agency-NEW-v2.default', function ($scope) {
                var el = $scope[0] && $scope[0].querySelector('[data-aew-NEW-v2]');
                if (el) initWidget(el);
            });
        });
    }
})();
```

Always include:
- IIFE wrapper
- `dataset.aewXxxInit` idempotency guard (re-init is fatal in Elementor editor)
- `elementor/frontend/init` hook so the widget works in editor preview
- `prefers-reduced-motion` guard for any animation

---

## 8. WordPress menu integration

Both Header V2's drawer and Footer V2's link columns pull from WP nav menus via this pattern:

```php
private function controls_nav(): void {
    $this->start_controls_section( 's_nav', [ 'label' => 'Navigation' ] );

    $menus = wp_get_nav_menus();
    $opts  = [ '' => '— Select —' ];
    foreach ( $menus as $m ) { $opts[ (string) $m->term_id ] = $m->name; }

    // Pre-fill with the menu assigned to the theme's "Footer" location
    $default_menu = $this->default_menu_id_for_location( 'menu-2' );

    $this->add_control( 'menu_id', [
        'label'   => 'WordPress menu',
        'type'    => Controls_Manager::SELECT,
        'options' => $opts,
        'default' => $default_menu,
        'description' => 'Defaults to whichever menu is assigned to the theme location.',
    ] );

    $this->end_controls_section();
}

private function default_menu_id_for_location( string $location ): string {
    $locations = get_nav_menu_locations();
    if ( ! empty( $locations[ $location ] ) ) {
        return (string) (int) $locations[ $location ];
    }
    return '';
}

private function nav_tree_from_menu_id( int $menu_id ): array {
    if ( $menu_id <= 0 ) return [];
    $raw = wp_get_nav_menu_items( $menu_id );
    if ( ! is_array( $raw ) ) return [];
    $out = [];
    foreach ( $raw as $item ) {
        if ( (int) $item->menu_item_parent !== 0 ) continue; // top-level only
        $out[] = [
            'label'  => $item->title,
            'url'    => $item->url ?? '',
            'target' => $item->target ?: '',
        ];
    }
    return $out;
}
```

Theme menu locations registered by Hello Elementor (parent theme): `menu-1` (Header), `menu-2` (Footer).

**Gotcha:** `default_menu_id_for_location()` runs when the *control* is registered, not when the widget renders. If the menu isn't yet assigned to the location at the time you create an Elementor template, the saved value will be empty. After assigning the menu, re-save the widget or update the template's `_elementor_data` manually (see §11).

---

## 9. Elementor + Elementor Pro version compat shim

**This is in the main plugin file already** ([agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php)) but is critical to understand: Pro 3.7.2 calls `window.elementorCommon.helpers.softDeprecated()` on the frontend, but Elementor 3.30.4 doesn't load `common.js` on the frontend (and Elementor 4.x removed the function entirely). Without the shim, Pro's JS pipeline crashes and **every Pro feature on the page dies silently** — including animated headlines, scroll utilities, and any widget that depends on `elementor/frontend/init`.

The shim:
- Defines `window.elementorCommon.helpers.softDeprecated/hardDeprecated/deprecatedMessage` as no-ops
- Uses a `Proxy` on `window.elementorCommon` so any later reassignment can't wipe the stubs
- Prints at both `wp_head` priority 1 AND `wp_print_footer_scripts` priority 1 (belt + suspenders)

**Do not remove this** until either Elementor (free) is upgraded to a version that re-includes `common.js` on the frontend, OR Elementor Pro is upgraded to a version (4.x+) that doesn't call `softDeprecated` on the frontend.

When the user sees `Uncaught TypeError: window.elementorCommon.helpers.softDeprecated is not a function`, the fix is the shim — not a different version of either plugin.

**Current pinned versions** (do not auto-update):
- Elementor: 3.30.4
- Elementor Pro: 3.7.2

---

## 10. Wiring a widget to display site-wide via Elementor Pro Theme Builder

For headers/footers/single templates, the widget itself isn't enough — Elementor Pro needs a template document with display conditions. This is how Header V2 and Footer V2 actually appear on every page.

### 10.1 Create the template document

```bash
# WP-CLI — replace TYPE with 'header', 'footer', 'single', 'archive', etc.
FID=$(wp post create \
  --post_type=elementor_library \
  --post_status=publish \
  --post_title="Notched NEW" \
  --porcelain)

wp post meta update "$FID" _elementor_template_type TYPE
wp post meta update "$FID" _elementor_edit_mode builder
wp post meta update "$FID" _elementor_version "4.1.1"
wp post meta update "$FID" _wp_page_template default

# Load the Elementor JSON for the widget
wp post meta update "$FID" _elementor_data "$(cat /tmp/widget-data.json)" --format=json
```

### 10.2 Set display conditions — MUST use a real PHP array

The most common bug: `wp post meta update` stores the conditions as a *string* (double-serialized). Elementor reads it as garbage and the template never displays.

```php
// Use wp eval-file with this snippet (not wp post meta update from CLI):
update_post_meta( $FID, '_elementor_conditions', [ 'include/general' ] );
```

Display conditions strings:
- `include/general` — entire site
- `include/singular/post` — all posts
- `include/singular/page` — all pages
- `include/singular/page/123` — only page ID 123
- `exclude/singular/page/45` — everywhere except page 45

### 10.3 Rebuild Pro's Conditions Cache — MANDATORY

Setting the meta isn't enough. Elementor Pro maintains a separate serialized cache map. After creating or modifying a template's conditions, force it to rebuild:

```php
// wp eval-file:
$ep = \ElementorPro\Plugin::instance();
$tb = $ep->modules_manager->get_modules( 'theme-builder' );
$cm = $tb->get_conditions_manager();
$cache = $cm->get_cache();
$cache->regenerate();
```

Without this step, the template exists in the DB but Elementor Pro doesn't know about it. The widget will not render until cache is regenerated.

### 10.4 Updating a live template's settings

If you change widget defaults in PHP, existing template instances still use their *saved* values. To sync:

```php
$data = get_post_meta( $FID, '_elementor_data', true );
$tree = json_decode( $data, true );
$settings = &$tree[0]['elements'][0]['elements'][0]['settings'];
$settings['some_key'] = 'new value';

update_post_meta( $FID, '_elementor_data', wp_slash( wp_json_encode( $tree ) ) );
delete_post_meta( $FID, '_elementor_element_cache' );
delete_post_meta( $FID, '_elementor_css' );
\Elementor\Plugin::instance()->files_manager->clear_cache();
```

Skipping `_elementor_element_cache` deletion → the rendered HTML stays cached → you'll see stale output.

---

## 11. Active templates and menus on this site

For reference when extending:

| Item | ID | Notes |
|---|---|---|
| Header template | 258 | "Notched Header", uses `agency-header-v2`, site-wide |
| Footer template | 311 | "Notched Footer", uses `agency-footer-v2`, site-wide |
| Primary Nav menu | 28 | "Primary Navigation", assigned to `menu-1` (Header) |
| Footer menu | 30 | "Footer Menu", assigned to `menu-2` (Footer) — Shop Kits, Custom Structure, Our Portfolio, Our Story, Meet the Crew, Find Installer |
| Quick Links menu | 31 | "Footer Quick Links", FAQs, Testimonials, Blog Posts, Career Page, Contact Us |
| Front page | 259 | Uses `elementor_header_footer` template (calls `elementor_theme_do_location()`) |

---

## 12. Accessibility patterns

- `aria-label` on every icon-only link/button
- `aria-expanded` + `aria-controls` for any toggle
- `role="dialog" aria-modal="true"` for drawers/modals
- `role="img" aria-label="..."` on `<div>` elements styled with `background-image`
- `<svg aria-hidden="true">` for decorative icons
- `decoding="async"` on `<img>` tags
- Focus-visible styles (don't strip outlines globally)
- `prefers-reduced-motion` guard for any animation

---

## 13. Gotchas already burned through

These have all cost real debugging time. Don't repeat them.

1. **AEW_VERSION cache bust** — bump on every CSS/JS edit. Without it: stale CSS, ghost-chasing.
2. **Elementor button styles bleed into widget buttons** — every `<button>` needs the explicit reset block (§6.4).
3. **Semi-transparent backgrounds on submenus** look like phantom shadow overlays when the parent changes color. Default to `background: transparent` unless you specifically want a tint.
4. **Topbar dark BG bleeds through during slide-out animations** — if the same element has different `background` in two media queries, the close animation can flash the desktop color. Use `!important` on background in the mobile override OR define ONE background on the outermost block and inherit.
5. **`@keyframes` tied to `.is-open` snap to start state when the class is removed** — if you build a stagger-in animation for accordion items, kill it on close to avoid corner-flicker.
6. **Browser scrollbar in a translating panel** — `overflow-y: auto` on a `translateX`-animated drawer can flash a scrollbar gutter in the top corner. Use `scrollbar-width: none` + `::-webkit-scrollbar { display: none; }` to suppress.
7. **`width: fit-content` on a flex-column drawer** — content can blow it past viewport. Cap with `min-width: min(360px, 92vw); width: min(360px, 92vw);`.
8. **The "strip Elementor padding" `:has()` rule needs the new widget class added** — when you create a new widget, add `.elementor-widget-agency-NEW-v2` to the same `:has()` selector list at the top of its CSS.
9. **`default_menu_id_for_location()` runs at control registration, not render** — if the menu isn't assigned to the location when an Elementor template is first created, the saved `menu_id` will be empty even after you assign the menu later. Either reassign before creating the template, or patch `_elementor_data` directly afterward (§10.4).
10. **`_elementor_conditions` must be a real PHP array, not a serialized string** — `wp post meta update` from the shell stores it as a string. Use `wp eval-file` with `update_post_meta( $id, '_elementor_conditions', [ 'include/general' ] )`.
11. **Pro Conditions Cache must be regenerated** after creating/changing a template's conditions. Just writing the meta isn't enough (§10.3).
12. **Per-template `_elementor_element_cache`** caches the rendered widget HTML. Changing widget settings without clearing this cache → stale output. Always `delete_post_meta( $id, '_elementor_element_cache' )` after editing `_elementor_data`.
13. **`object-fit: cover` doesn't give horizontal slack to pan** if the container is wider relative to its height than the image's aspect ratio. The image scales to fill width, leaving only vertical overflow — `object-position: 0% center` vs `100% center` looks identical. Use `background-image` + `background-size: auto 130%` instead for guaranteed horizontal slack (see Footer V2 hero parallax).
14. **WP-CLI deprecation noise corrupts stdout JSON pipes** — Elementor 3.30.4 emits PHP 8.4 deprecation notices during CLI ops. Always `2>/dev/null | head -1` before piping to `jq`/`python3 -c "json.load(...)"` or the JSON parser will choke.
15. **Sed self-references when replacing hex with `var(--token)`** — if a sed pattern matches part of the token name itself (e.g. `#876137` and `--notched-cta` both contain `cta`), the replacement can collapse `--notched-cta: #876137;` into `--notched-cta: var(--notched-cta);`. Verify the token definition block by eye after any bulk substitution.
16. **The re-pointed `_padding` (and any re-pointed common control) emits ONE non-responsive rule** — Elementor writes only the base `default` into the generated page CSS for these, ignoring `tablet_default`/`mobile_default`, at a specificity that beats your stylesheet. So a non-empty default forces that one value at every breakpoint (this is exactly what made mobile padding stay at the desktop value). **Fix: keep the override's defaults EMPTY** (§5) and let the stylesheet own responsive padding (§6.5). Same trap for any control whose responsive value you want the stylesheet to own — e.g. Hero V2's `min_height` desktop default is left empty so `calc(100dvh - header)` wins (§6.7).
17. **Computed MEDIA control defaults don't re-merge onto legacy saved instances** — if a control default is a function call (e.g. `'default' => [ 'url' => Widget_Assets::url(...) ]`), an instance saved before that default existed stores NO value, and Elementor won't backfill it — `render()` sees an empty URL and bails (a sticky-image badge rendered as a collapsed 1px strip this way). **Fix: fall back to the asset URL inside `render()`** (`if ( ! $url ) { $url = Widget_Assets::url( self::ASSET_SLUG, '...' ); }`), don't rely on the control default alone.
18. **A pinned/`position:fixed` widget still leaves its host section in flow** — the badge floats over the page but its (otherwise empty) Elementor section/column renders a ~1px strip that shows the page background as a thin seam (the white line seen between header and hero). Collapse the host chain to zero when the widget is the sole child: `height/min-height/line-height/font-size: 0 !important; overflow: visible` on the `:has(... :only-child)` section/column/wrap.
19. **Elementor DROPS global-bound colours from front-end CSS for custom controls** — if a colour control's `selectors` paint a property directly (`background-color: {{VALUE}}`) and the user binds the value to a *global* swatch, the editor's JS preview shows it but the generated page CSS omits the rule entirely → live reverts to the stylesheet default. ("Works in edit, not on live.") Worse, `get_settings_for_display()` returns an **empty string** for a global-bound key, so naively inlining `$s['key']` in `render()` drops globals too. **Fix: the §6.8 three-part pattern** — control assigns to a CSS var (editor preview), `render()` inlines the *resolved* value via `Color_Vars::build()` which emits `var(--e-global-color-<id>)` for globals (live guarantee), stylesheet consumes the var with a token fallback. Do NOT "fix" it by disabling globals (`'global' => ['default'=>'']`) — that removes a feature.
20. **The Elementor kit's `.elementor-kit-N h1..h6 { color }` beats a bare `.aew-NEW__title`** — kit heading colour is specificity (0,1,1); a single-class heading rule is (0,1,0) and loses, so the heading ignores its own colour control and shows the kit default. **Fix: prefix the wrapper class** — `.aew-NEW .aew-NEW__title { color: … }` is (0,2,0) and wins (§6.8). Only real `<h1>`–`<h6>` headings collide; `<a>`/`<span>`/`<div>` targets and tag-qualified selectors (`h2.aew-NEW__title`) are already safe.

---

## 14. Quick checklist for a new V2 widget

- [ ] Create `widgets/class-widget-NEW-v2.php` (new file; do NOT modify any existing widget)
- [ ] Create `assets/widgets/NEW-v2/{css,js,images}/`
- [ ] Register the widget in `includes/class-widgets-loader.php`
- [ ] Register the asset slug in `includes/class-widget-assets.php`
- [ ] Use BEM block `aew-NEW`; widget name `agency-NEW-v2`; data attr `data-aew-NEW-v2`; init flag `dataset.aewNEWInit`
- [ ] One outer wrapper (`.aew-NEW`) for full-bleed BG + one inner wrapper (`.aew-NEW__inner`) for max-width/padding
- [ ] Inner wrapper `max-width: 1440px` (NOT 1360); X padding `40px` mobile/tablet → `80px` desktop, **owned by the stylesheet** (§6.5)
- [ ] `_padding` (and any re-pointed common control) override defaults left **EMPTY** in `get_stack()` — non-empty defaults emit one non-responsive rule that clobbers the stylesheet (§5, gotcha #16)
- [ ] Typography matches the §6.2 scale exactly (H1 80/48, eyebrow 20, paragraph 18/14, buttons 20/16); CTA `#AA7D44` → hover `#876137`
- [ ] Strip Elementor column padding by adding `.elementor-widget-agency-NEW-v2` to the `:has()` block at the top of the CSS
- [ ] Copy the brand tokens block from §6.1 to the top of the CSS; use `var(--notched-*)` everywhere
- [ ] **Colour controls follow the §6.8 pattern**: control `selectors` assign to a `--aew-NEW-*` var on `{{WRAPPER}}`; `render()` inlines resolved values via `Color_Vars::build()`; CSS consumes `var(--aew-NEW-*, <token fallback>)`. NEVER `background-color: {{VALUE}}` directly (gotcha #19). Prefix heading colour rules with the wrapper class for specificity (gotcha #20)
- [ ] Desktop-first cascade: base rules = desktop, step DOWN at `@media (max-width: 1024px)` (tablet) and `@media (max-width: 768px)` (mobile)
- [ ] Bump `AEW_VERSION` after each CSS/JS edit
- [ ] Lint PHP with `php -l` before saving each widget file
- [ ] If site-wide via Theme Builder: create template, set `_elementor_conditions` as an array, regenerate Pro Conditions Cache, verify with `curl https://notched.test/ | grep aew-NEW`

---

## 15. Files to read before starting (in order)

1. [design-system.md](design-system.md) — colors, type scale, spacing tokens (source of truth)
2. [widgets/class-widget-footer-v2.php](web/app/plugins/agency-elementor-widgets/widgets/class-widget-footer-v2.php) — most modern widget skeleton (controls, render, WP menu, repeater)
3. [assets/widgets/footer-v2/css/footer-v2.css](web/app/plugins/agency-elementor-widgets/assets/widgets/footer-v2/css/footer-v2.css) — token block + BEM + responsive
4. [widgets/class-widget-header-v2.php](web/app/plugins/agency-elementor-widgets/widgets/class-widget-header-v2.php) — drawer/accordion/Woo cart integration
5. [assets/widgets/header-v2/css/header-v2.css](web/app/plugins/agency-elementor-widgets/assets/widgets/header-v2/css/header-v2.css) — large reference for complex layouts
6. [assets/widgets/footer-v2/js/footer-v2.js](web/app/plugins/agency-elementor-widgets/assets/widgets/footer-v2/js/footer-v2.js) — scroll-parallax JS skeleton
7. [includes/class-color-vars.php](web/app/plugins/agency-elementor-widgets/includes/class-color-vars.php) — global-aware colour resolver used by every widget's `render()` (§6.8)
8. [includes/class-widget-assets.php](web/app/plugins/agency-elementor-widgets/includes/class-widget-assets.php) — how asset handles are derived
9. [includes/class-widgets-loader.php](web/app/plugins/agency-elementor-widgets/includes/class-widgets-loader.php) — widget registration
10. [agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php) — `AEW_VERSION` constant + compat shim
