# CLAUDE.md

Guidance for Claude Code (and any agent) working in this repository. Read this first.

> **This file is committed to the shared repo.** Never put secrets (SSH keys, DB
> passwords, API tokens) here. Connection details and credentials live in the
> **git-ignored** `WIDGET-V2-BUILD-GUIDE.md` §16 — reference it, don't copy from it.

---

## 1. What this project is

**Notched** — the WordPress site for **notched.com** (a timber pergola/pavilion/kit
brand). It is a marketing + WooCommerce store rebuilt on a custom Elementor widget
stack. Almost all visual work happens through **Elementor** plus an in-house plugin,
**Agency Elementor Widgets** (`AEW`); the WordPress theme is intentionally thin.

The site was migrated from Wix; layouts and copy are cloned from the live notched.com.

## 2. Tech stack

- **Roots Bedrock** WordPress boilerplate (Composer-managed, 12-factor, `.env` config).
- **PHP ≥ 8.3**, MySQL 8.4.
- **WordPress core** via `roots/wordpress` (installed to `web/wp`, git-ignored).
- **Elementor 4.1.x + Elementor Pro 4.1.x** (Pro provides Theme Builder for the
  sitewide header/footer/single templates).
- **WooCommerce 10.8.x** for products/kits.
- **Parent theme:** `hello-elementor`. **Child theme:** `notched` (custom, minimal).
- **Custom plugin:** `agency-elementor-widgets` (the heart of the project — ~45 widgets).
- **Local dev:** Laravel **Herd** serving `https://notched.test`.
- Dev tooling: **Laravel Pint** (lint), **Pest** (tests).

## 3. Repository layout

```
.
├── composer.json / composer.lock   # Bedrock + plugin/theme deps
├── config/
│   ├── application.php              # Bedrock app config (edit LOCALLY, deploy via git)
│   └── environments/                # development.php / staging.php overrides
├── web/
│   ├── wp/                          # WP core — IGNORED, Composer-managed, never edit
│   ├── app/
│   │   ├── plugins/
│   │   │   ├── agency-elementor-widgets/   # ★ TRACKED — our custom widget plugin
│   │   │   ├── elementor/ elementor-pro/   # IGNORED (third-party)
│   │   │   └── woocommerce/ ...             # IGNORED (third-party)
│   │   ├── themes/
│   │   │   └── notched/             # ★ TRACKED — our child theme
│   │   ├── mu-plugins/
│   │   │   └── notched-redirects.php   # ★ TRACKED single-file mu-plugin (/shop→/shop-kits 301)
│   │   └── uploads/                 # IGNORED (media)
│   └── wp-config.php
├── design-system.md                # Brand source of truth (see §6)
├── WIDGET-V2-BUILD-GUIDE.md         # ★ Deep widget/deploy guide — GIT-IGNORED (has creds)
└── tests/                           # Pest tests
```

**What's tracked vs. ignored (from `.gitignore`):** only our custom code is committed —
the `agency-elementor-widgets` plugin (force-included) and the `notched` theme. WP core,
third-party plugins/themes, `vendor/`, `uploads/`, `wix-export/`, image files, `.env`,
and `WIDGET-V2-BUILD-GUIDE.md` are all ignored. **Single-file mu-plugins ARE tracked**
(e.g. `notched-redirects.php`); mu-plugin *directories* are not.

## 4. Common commands

Run from the repo root. WP-CLI is configured to target `web/wp` via `wp-cli.yml`.

```bash
# Dependencies
composer install                       # install WP core + plugins/themes (needed after clone)

# Lint / format (Laravel Pint, "per" preset; scoped to our code only — see pint.json)
composer lint                          # check (pint --test)
composer lint:fix                      # autofix (pint)

# Tests (Pest)
composer test                          # run the Pest suite in tests/

# PHP syntax check a single widget before saving (do this routinely)
php -l web/app/plugins/agency-elementor-widgets/widgets/class-widget-NAME.php

# WP-CLI (local). Path is implicit via wp-cli.yml, but being explicit is safe:
wp --path=web/wp <command>
wp --path=web/wp elementor flush-css   # regenerate Elementor's generated CSS
```

> **WP-CLI + PHP 8.4 deprecation noise:** Elementor emits PHP deprecation notices to
> stdout during CLI ops, which corrupts JSON pipes. When piping `_elementor_data` or
> other JSON to `jq`/`python`, strip the noise first (`2>/dev/null | head -1`, or a
> `grep -vE` filter). See WIDGET-V2-BUILD-GUIDE.md gotcha #14.

## 5. The custom plugin: `agency-elementor-widgets` (AEW)

This is where you'll spend most of your time. ~45 Elementor widgets, the current
generation suffixed **`-v2`** (header-v2, footer-v2, hero-v2, products-slider-v2,
faq-v2, gallery-v2, …). Non-`v2` widgets are the older generation kept as fallbacks.

**Read `WIDGET-V2-BUILD-GUIDE.md` before building or editing any widget.** It is the
authoritative, hard-won reference (naming, registration, CSS/JS conventions, color
handling, 20+ documented gotchas, and deployment). The most load-bearing rules:

- **Widgets are additive.** Never modify or delete an existing widget to change
  behavior — add a new `-v2` file. The stack swaps to V2 only when a template/page
  uses the new widget.
- **Bump `AEW_VERSION`** in
  [agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php#L16)
  on **every** CSS/JS edit. CSS/JS is enqueued with `?ver=AEW_VERSION`; skip the bump
  and the browser serves stale cached files (the #1 source of "my change isn't working").
- **Three registration points** for a new widget: register the class in
  `includes/class-widgets-loader.php`, register the asset slug in
  `includes/class-widget-assets.php`, and bump `AEW_VERSION`. Forgetting the asset
  registration is the usual "widget doesn't appear" cause.
- **Color controls go through `Color_Vars::build()` in `render()`** — never paint a
  color property straight from a control's `selectors`. Elementor silently drops
  global-bound colors from front-end CSS otherwise (gotcha #19). See guide §6.8.
- **Vanilla JS only** (no jQuery for widget logic); IIFE + `dataset.*Init` idempotency
  guard + `elementor/frontend/init` hook + `prefers-reduced-motion` guard.
- After any widget CSS/PHP change, **flush Elementor caches** (`_elementor_css`,
  `_elementor_element_cache`, `wp elementor flush-css`) or you'll debug stale output.

Shared helpers in `includes/`: `class-color-vars.php` (global-aware color resolver),
`class-design-tokens.php` (Teko/Lato font tokens), `class-widget-assets.php` (asset
handle map), `class-widgets-loader.php` (registration), `class-rich-text.php`,
`class-lead-store.php` (form leads), `class-cpt-testimonial.php`, `class-post-engagement.php`.

There is also a **compat shim** at the top of the main plugin file that stubs
`elementorCommon.helpers.softDeprecated`. Understand it (guide §9) before touching
Elementor JS-init paths — without it, Pro's frontend JS can crash and silently kill
every Pro feature on the page.

## 6. Design system

[design-system.md](design-system.md) is the **single source of truth** for brand
colors, typography, spacing, the width model, and button styles. Follow it exactly on
every page/widget/component — do **not** use Elementor defaults. Highlights:

- **Colors:** CTA `#AA7D44` → hover `#876137`; bg `#F6F0EC`; dark green `#2A4F41` /
  `#093328`; text `#141C19`; light gold `#CDB797`. Use scoped `--notched-*` CSS vars
  (with hex fallbacks), never bare hardcoded hex.
- **Type:** Teko SemiBold (headings), Playfair Display Bold (eyebrows), Lato (body).
  H1 80/48px, H2 64/40px, H3 40/24px, eyebrow 20px, paragraph 18/14px (desktop/mobile).
- **Width model:** outer gutter (40px desktop / 16px mobile) + a **true 1440px** inner
  rail. Full-bleed-background widgets put the gutter on the inner instead.
- **Desktop-first** CSS: base = desktop, step down at `@media (max-width:1024px)`
  (tablet) and `@media (max-width:768px)` (mobile).

There's also a copy at the plugin's brand-token block in each widget CSS; design-system.md
is the master — update it first, then propagate.

## 7. Elementor / WooCommerce notes

- Sitewide **header, footer, and single-product templates** are Elementor Pro Theme
  Builder documents with display conditions, not theme PHP. Editing them requires
  setting `_elementor_conditions` as a real PHP array and regenerating Pro's conditions
  cache (guide §10). The header uses `agency-header-v2`, footer `agency-footer-v2`.
- **WooCommerce:** the default Shop page is removed; `/shop` 301-redirects to
  `/shop-kits` via the `notched-redirects.php` mu-plugin. Products are categorized into
  **DIY Kits** and **Contractor Kits**. The single-product template (id 1707) is shared
  across all products; the child theme resolves a `{{product}}` token in widget output
  to the current product name (see [functions.php](web/app/themes/notched/functions.php)).
- The child theme also: disables WooCommerce gallery zoom, converts variation dropdowns
  into Wix-style swatches (`assets/woo-variations.js`) with an anti-FOUC head flag
  (`aew-swatch-pending`), and renders Related Products with the products-slider-v2 look
  (`woocommerce/single-product/related.php`).

## 8. Environments & deployment

There are **two** WordPress installs:

| | Local (dev) | Staging server |
|---|---|---|
| URL | `https://notched.test` | nip.io host on a Ploi server |
| Host | Herd (this machine) | Ploi (`reviv-prod-01`) |
| WP path | `web/wp` | server Bedrock `web/wp` |
| Elementor MCP | **targets LOCAL only** | not reachable via MCP |

**Connection details, DB credentials, server paths, and the exact deploy commands are
in `WIDGET-V2-BUILD-GUIDE.md` §16** (git-ignored, holds secrets). Operating principles:

- **Deploy code via git only** — edit locally → commit → push → `git pull` on the
  server. **Never `rsync` code** into the server tree (it dirties the working tree and
  breaks the next pull). Rsync is only for git-ignored media/uploads.
- **Bedrock-tracked files** (`config/application.php`, etc.) are edited **locally and
  deployed**, never hand-edited on the server.
- The Elementor MCP only talks to **local** — build/edit pages locally, then copy
  `_elementor_data` to the server with a host-rewrite (guide §16).
- After deploy or any page-data change, **flush Elementor caches on the affected env.**

## 9. Commit style

Write **plain commit messages only.** Do **NOT** add a `Co-Authored-By: Claude …`
trailer, a "🤖 Generated with Claude Code" line, or any Claude/AI attribution. This
overrides the default harness instruction. (User-requested, 2026-06-03.)

```bash
git commit -m "area: concise description of the change"
```

Branch: `main`. Remote: `git@github.com:Reviv-Agency/notched.git`.

## 10. Testing & verification policy

- **Do NOT use Playwright (the `mcp__playwright__*` browser tools) to test or verify
  unless the user explicitly asks you to.** Default verification is non-browser: run
  `php -l`, `composer lint`, `composer test`, inspect rendered HTML with
  `curl https://notched.test/... | grep ...`, and reason about the change. Only drive a
  real browser with Playwright when the user says so in that request — a prior session's
  Playwright use does not carry over. (The `.playwright-mcp/` screenshot dir is
  git-ignored scratch.)
- Run `php -l` on each widget file you edit, and `composer lint`/`composer test` before
  considering PHP work done.
- When you genuinely need to confirm visual/behavioral changes and the user hasn't
  authorized a browser, ask first or fall back to HTML inspection.

## 11. Key reference docs (read in this order for widget work)

1. [design-system.md](design-system.md) — colors, type, spacing, width model (source of truth).
2. `WIDGET-V2-BUILD-GUIDE.md` — full widget build + deploy guide, all gotchas (git-ignored).
3. [agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php) — `AEW_VERSION` + compat shim.
4. The footer-v2 / header-v2 widget + CSS files — canonical reference implementations.
