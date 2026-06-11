# Using Agency Elementor Widgets on a new site

The `agency-elementor-widgets` (AEW) plugin ships **zero brand colour**. Every widget
reads its colours from Elementor global CSS variables of the form
`var(--e-global-color-aew-<role>)`, each with a neutral-grey hex fallback baked in. With
no palette seeded, the site renders in greys — it never looks broken, it just looks
unbranded. Branding is a per-site concern, layered on top via a tiny mu-plugin.

## Brand a site

1. Copy the template into this site's mu-plugins directory:

   ```
   web/app/plugins/agency-elementor-widgets/templates/brand-colors.example.php
     →  web/app/mu-plugins/<site>-colors.php
   ```

2. In the copy: set the `Plugin Name` header, pick a **unique option key** (replace
   `project_colors_seeded` everywhere), and fill the `$palette` array with the site's
   brand hexes.

3. Load **wp-admin** once. On that admin load the seeder writes the `aew-*` colours into
   the active Elementor kit. It is idempotent: it adds only missing colours, never
   overwrites existing ones, and is guarded by the option flag so it runs once. It then
   regenerates the kit CSS.

## Edit colours later

After seeding, the colours are real Elementor global colours. Edit them live in
**Elementor → Site Settings → Global Colours** — no code change needed. The mu-plugin
only seeds; it never re-imposes its palette once the flag is set.

## The 11 standard role ids

| Role id | Meaning |
|---|---|
| `aew-cta` | Main CTA |
| `aew-cta-hover` | CTA hover |
| `aew-background` | Page background |
| `aew-text` | Body text |
| `aew-cards` | Cards |
| `aew-lines` | Lines & accents |
| `aew-secondary-bg` | Headers (H1–H2) |
| `aew-secondary-accent` | Secondary accent |
| `aew-misc-accent` | Misc accent |
| `aew-secondary-cards` | Secondary cards |
| `aew-gold-light` | Gold light |

Individual sites may append extra roles (e.g. `aew-black`, `aew-gold-tint`) to their own
palette — these 11 are the baseline every site is expected to define.
