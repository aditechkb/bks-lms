<?php
/**
 * BKS LMS Child Theme functions
 * Safe baseline for GeneratePress child theme
 */

if (!defined('ABSPATH')) exit;

/**
 * 1. Enqueue styles (SAFE way for GeneratePress child theme)
 */
add_action('wp_enqueue_scripts', function () {

    // Load child theme stylesheet
    wp_enqueue_style(
        'bks-lms-child-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

}, 20);


/**
 * 2. WooCommerce compatibility (SAFE defaults)
 */
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
});


/**
 * 3. Prevent query conflicts (SAFE guard only)
 *    DO NOT modify product queries unless needed
 */
add_action('pre_get_posts', function ($query) {

    // Only touch frontend main query
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // SAFE PLACE: You can add filters later if needed
    // Example (DO NOT enable unless required):
    // if ($query->is_post_type_archive('product')) { }

});


/**
 * 4. Optional: Remove unwanted WooCommerce actions (SAFE empty baseline)
 * Keep empty unless you specifically want UI changes
 */
add_action('init', function () {
    // Example safe removals (uncomment only if needed)

    // remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
});


/**
 * 5. Developer helper (remove in production if you want)
 */
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_footer', function () {
        // echo '<!-- BKS LMS Child Theme Loaded -->';
    });
}

require_once get_stylesheet_directory() . '/inc/integrations/learnpress-woocommerce.php';