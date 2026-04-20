<?php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'generatepress-child-style',
        get_stylesheet_uri(),
        ['generatepress'],
        wp_get_theme()->get('Version')
    );
});