<?php

/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;

use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', env('WP_DEBUG_LOG') ?? true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);
/*
 * The Ploi staging server runs with WP_ENV=development, so this flag governs
 * it too. Indexing is allowed so PageSpeed/Lighthouse SEO audits pass
 * (requested 2026-06-10); flip back to `true` to noindex staging again.
 * Local (notched.test) is not publicly reachable, so this is moot locally.
 */
Config::define('DISALLOW_INDEXING', false);

ini_set('display_errors', '1');

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);
