<?php
add_action('wp_enqueue_scripts', function () {
    // Version by file mtime so edits to style.css bust the browser cache.
    $css = get_stylesheet_directory() . '/style.css';
    $ver = is_readable($css) ? (string) filemtime($css) : null;
    wp_enqueue_style('notched-style', get_stylesheet_uri(), [], $ver);
});
