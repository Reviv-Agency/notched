# Elementor + Custom Widgets — Reuse / Hand-off Guide

How to stand up the **Elementor Pro + custom widget stack** on a new site. The
widget plugin (`agency-elementor-widgets`, "AEW") is brand-agnostic: **font sizes,
spacing, and layout are reusable as-is — only the colours (and a few brand strings)
change per project.**

---

## 1. What the stack is

| Piece | Role |
|---|---|
| **Elementor 4.1.x** | Page builder (free core). |
| **Elementor Pro 4.1.x** | Theme Builder (header/footer/single templates), dynamic widgets, WooCommerce widgets. |
| **`agency-elementor-widgets` (AEW)** | The custom plugin — ~48 widgets, all suffixed `-v2` for the current generation. **This is the reusable asset.** |
| Theme | A thin **hello-elementor child** (`functions.php` + `style.css`). Layout lives in Elementor + AEW, not theme PHP. |

Optional companions used on Notched (not required for the widgets): WooCommerce,
WP Mail SMTP, hello-elementor parent.

---

## 2. Standing it up on a new site

1. **Install Elementor + Elementor Pro** (Pro needs a license for Theme Builder).
2. **Copy the `agency-elementor-widgets` plugin** into `wp-content/plugins/`
   (or `web/app/plugins/` on Bedrock) and activate it.
   - On Bedrock/Composer sites, third-party plugins go through Composer; the
     custom AEW plugin is force-included and tracked in git (see Notched's
     `.gitignore` / `composer.json` for the pattern).
3. **Create a thin child theme** (clone hello-elementor child): `style.css` +
   `functions.php`. Most page CSS comes from the widgets, not the theme.
4. **Set the brand colours + fonts** (Section 4) — this is 90% of the rebrand.
5. Build pages in Elementor using the AEW widgets (category **"Agency Widgets"**).

---

## 3. Plugin architecture (so you can add/edit widgets)

```
agency-elementor-widgets/
├── agency-elementor-widgets.php     # bootstrap: AEW_VERSION, compat shim, init
├── includes/
│   ├── class-plugin.php             # registers assets + enqueues (init / wp_enqueue_scripts)
│   ├── class-widgets-loader.php     # require_once + register every widget  ← reg point #1
│   ├── class-widget-assets.php      # per-widget CSS/JS handle map           ← reg point #2
│   ├── class-design-tokens.php      # the `aew-tokens` stylesheet: fonts + type-scale + colour CSS vars
│   ├── class-color-vars.php         # global-aware colour resolver (CRITICAL — see §6)
│   ├── class-rich-text.php          # wp_kses_post WYSIWYG output helper
│   ├── class-lead-store.php         # consultation-form submissions → DB + wp_mail
│   ├── class-cpt-testimonial.php    # testimonial CPT
│   └── class-post-engagement.php
└── widgets/  +  assets/widgets/<slug>/css|js/
    └── class-widget-<slug>.php      # one PHP class per widget
```

### The 3 registration points for a NEW widget
Miss any one and the widget won't appear / won't be styled:

1. **`includes/class-widgets-loader.php`** — `require_once` the file **and**
   `$widgets_manager->register( new Widget_Xxx() );`
2. **`includes/class-widget-assets.php`** → `register_defaults()` —
   ```php
   self::register_widget( 'my-slug', [
       'style'      => 'css/my-slug.css',
       'script'     => 'js/my-slug.js',   // omit if no JS
       'style_deps' => [ 'aew-tokens' ],
   ] );
   ```
3. **Bump `AEW_VERSION`** in `agency-elementor-widgets.php` on **every** CSS/JS edit —
   assets are enqueued `?ver=AEW_VERSION`; skip it and browsers serve stale cached
   files (the #1 "my change isn't showing" cause).

---

## 4. ⭐ Rebrand = change these (font sizes stay the same)

### 4a. Type scale — REUSE AS-IS
Defined as CSS vars on the `aew-tokens` stylesheet (`class-design-tokens.php`) and
echoed by every widget. **Desktop / Mobile / line-height:**

| Token | Desktop | Mobile | LH | Font |
|---|---|---|---|---|
| H1 | **5rem / 80px** | 3rem / 48px | 85% | Heading (Teko) |
| H2 | **4rem / 64px** | 2.5rem / 40px | 85% | Heading |
| H3 | **2.5rem / 40px** | 1.5rem / 24px | 85% | Heading |
| Subhead / eyebrow | 1.25rem / 20px | 1.25rem / 20px | 100% | Subhead (Playfair) |
| Paragraph | 1.125rem / 18px | 0.875rem / 14px | 140% | Body (Lato) |
| Button | 1.125rem / 20px | 1rem / 16px | 85% | Heading |

**Fonts (roles, swap the family per brand):**
- Headings H1–H3 → **Teko SemiBold (600)**
- Subhead / eyebrow → **Playfair Display Bold (700)**
- Body → **Lato Regular (400)**

Change a font by swapping the family in `class-design-tokens.php`
(`font_heading` / `font_body`) — the sizes/line-heights stay.

### 4b. Colours — CHANGE PER BRAND
Notched palette (replace the hexes, keep the variable names):

| Role | Notched hex | Token name |
|---|---|---|
| Background (cream) | `#F6F0EC` | `--notched-background` |
| Secondary BG / H1–H2 | `#2A4F41` | `--notched-secondary-bg` |
| Secondary accent | `#093328` | `--notched-secondary-accent` |
| Text / H3+ | `#141C19` | `--notched-text` |
| CTA | `#876137` | `--notched-cta` |
| CTA hover | `#6E4F2D` | `--notched-cta-hover` |
| Light gold | `#CDB797` | — |

> Note: the CTA was darkened from `#AA7D44` → `#876137` for WCAG AA contrast.
> Pick CTA/hover that pass **4.5:1** against white if buttons are white-on-CTA.

Two places hold colours:
- **Per-widget control defaults** (`'default' => '#876137'` in the widget PHP) — the
  out-of-the-box look.
- **Scoped `--notched-*` brand tokens** at the top of each widget's CSS (with hex
  fallbacks). Swap these for a global recolour.

**Best practice:** define brand colours as **Elementor Global Colors** in the kit,
then bind widget controls to them — one place to change per site (see §6 for why
the resolver matters).

### 4c. Width / spacing model (reusable)
- Max content width **1360–1440px**, centered. Section X-padding **40px desktop /
  16px mobile**. Stacking gap 16px.
- Card radius **24px** (img 16px) desktop; 20px / 12px mobile.
- Button: pad ~`20/24/16`, radius **8px**.
- **Desktop-first** CSS: base = desktop, step down at `@media (max-width:1024px)`
  (tablet) and `@media (max-width:768px)` (mobile).

---

## 5. Widget conventions (match these when editing/adding)

```php
namespace AEW;
class Widget_My_Thing extends \Elementor\Widget_Base {
    private const ASSET_SLUG = 'my-thing';
    public function get_name(): string  { return 'agency-my-thing'; }      // slug
    public function get_title(): string { return 'My Thing (Brand)'; }      // ← brand string
    public function get_icon(): string  { return 'eicon-...'; }
    public function get_categories(): array { return [ 'agency-widgets' ]; }
    public function get_style_depends(): array {
        return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; // 'aew-widget-my-thing'
    }
    protected function register_controls(): void { /* Content + Style tabs */ }
    protected function render(): void { /* echo BEM markup: .aew-<block>__<el> */ }
}
```

- **BEM CSS** classes: `aew-<block>` / `aew-<block>__<element>` / `--<modifier>`.
- **Colours go through `Color_Vars::build()` in `render()`** — never paint a colour
  straight from a control's `selectors` (see §6).
- **Rich text:** `Rich_Text::echo_html()` / `Rich_Text::is_empty()` (uses
  `wp_kses_post`) for WYSIWYG fields.
- **JS:** vanilla only (no jQuery for widget logic). IIFE + a `dataset.*Init`
  idempotency guard + hook on `elementor/frontend/init` + a
  `prefers-reduced-motion` guard for animation.
- After a CSS/PHP change: **bump `AEW_VERSION`** and flush Elementor CSS
  (`wp elementor flush-css`, plus clear `_elementor_css` / `_elementor_element_cache`).

---

## 6. ⚠️ Critical gotchas (these bite hard)

1. **Colour controls MUST resolve through `Color_Vars::build()` in `render()`.**
   Elementor silently **drops global-bound colours** from the front-end CSS when a
   custom widget assigns them via `selectors`. Symptom: "colour shows in the editor,
   not on the live page." Fix: each widget emits the resolved value as an inline CSS
   var on its wrapper; the control's `selectors` still set the same var so the editor
   preview updates. (Read `class-color-vars.php` header.)

2. **Bump `AEW_VERSION` on every asset edit** — or you'll debug stale cached CSS/JS.

3. **Elementor Pro compat shim** (in `agency-elementor-widgets.php`): stubs
   `elementorCommon.helpers.softDeprecated`. Without it, Pro's frontend JS can throw
   and silently kill *every* Pro feature on the page (sliders, scroll effects, widget
   handlers). Keep it when porting.

4. **Cloud Library 403 fatal guard** (also in the bootstrap): Elementor's
   cloud-library module throws an uncaught 403 on malformed `?screenshot_proxy`
   requests, which can break the **editor canvas** preview of custom widgets. The
   guard strips the bad param on `plugins_loaded`. Keep it.

5. **Forms — never use `form.action` in JS.** The consultation form has a hidden
   `<input name="action">` (admin-ajax routing) that **shadows** the `form.action`
   DOM property, so it returns the input node, not the URL → posts to a 404 →
   "Unexpected server response." Always use `form.getAttribute('action')`.

6. **Editor "widget gone" after editing data via WP-CLI/DB:** Elementor loads the
   **autosave** if one exists. If you patch `_elementor_data` directly, delete the
   post's autosave or the editor shows the stale version.

7. **Theme Builder conditions** are stored in post meta `_elementor_conditions`
   (array, e.g. `['include/singular/product']` or `.../product/<id>` for one product)
   **and** mirrored in option `elementor_pro_theme_builder_conditions`
   (keyed by location: `single` / `header` / `footer`). Set both when scripting.

8. **PHP 8.4 deprecation noise** can leak into AJAX/JSON responses and break parsing.
   When piping Elementor output to `jq`/parsers, strip notices first.

---

## 7. Per-site rebrand checklist

- [ ] Install Elementor + Elementor Pro (license Pro).
- [ ] Copy + activate the AEW plugin; create the thin child theme.
- [ ] **Set brand colours** — define Elementor Global Colors, and/or swap the
      `--notched-*` tokens + control defaults to the new palette.
- [ ] **Swap fonts** (family only) in `class-design-tokens.php` if the brand differs
      — keep the type scale.
- [ ] Rename brand strings in widget `get_title()` (e.g. "… (Notched)" → "… (NewBrand)").
- [ ] Build header/footer/single templates in Theme Builder.
- [ ] Wire the consultation form recipient (`notify_email` per widget) + SMTP
      (WP Mail SMTP) if using forms.
- [ ] On every widget edit: **bump `AEW_VERSION`** + flush Elementor CSS.

---

## 8. Handy commands (Bedrock + WP-CLI)

```bash
php -l web/app/plugins/agency-elementor-widgets/widgets/class-widget-NAME.php  # syntax check
wp --path=web/wp elementor flush-css                                           # regen Elementor CSS
# clear a page's cached CSS after a direct data edit:
wp --path=web/wp eval 'delete_post_meta($id,"_elementor_css"); delete_post_meta($id,"_elementor_element_cache");'
```

The widget list (current generation, `-v2`): banner-hero, benefits, booking-cards,
card-row, comments, consultation-form, contact-regions, crew-collage, cta-banner,
cta-band, faq, feature-band, feature-rows, footer, gallery, header, heading-band,
hero, icon-cards, icon-grid, image-cta-band, info-columns, job-listings, media-cta,
numbered-features, overlay-banner, parallax-image, post-archive, products-slider,
quote-band, recent-posts, region-cards, single-post, split-media, sticky-image,
team-grid, testimonial-grid, testimonials, values-grid, welcome.
