<?php

/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

/**
 * You should try to keep staging as close to production as possible. However,
 * should you need to, you can always override production configuration values
 * with `Config::define`.
 *
 * Example: `Config::define('WP_DEBUG', true);`
 * Example: `Config::define('DISALLOW_FILE_MODS', false);`
 */

/*
 * Indexing is allowed on staging so PageSpeed/Lighthouse SEO audits pass
 * (requested 2026-06-10). The nip.io staging URL is therefore crawlable —
 * flip back to `true` if staging should disappear from search engines again.
 */
Config::define('DISALLOW_INDEXING', false);
