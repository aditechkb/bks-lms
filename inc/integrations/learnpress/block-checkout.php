<?php
defined('ABSPATH') || exit;

/**
 * 🚫 BLOCK LEARNPRESS CHECKOUT COMPLETELY
 * WooCommerce-only checkout enforcement
 */

/**
 * 1. BLOCK CHECKOUT PAGE ACCESS (UI layer)
 */
add_action('template_redirect', function () {

    if (!function_exists('is_learnpress')) {
        return;
    }

    if (function_exists('learn_press_is_checkout') && learn_press_is_checkout()) {

        wp_die(
            'LearnPress checkout is disabled. Please use WooCommerce checkout.',
            'Checkout Disabled',
            ['response' => 403]
        );
    }
});


/**
 * 2. BLOCK CHECKOUT PROCESS (server layer)
 */
add_action('init', function () {

    if (!class_exists('LP_Checkout')) {
        return;
    }

    add_action('learn-press/checkout-order-processed', function () {
        wp_send_json([
            'result'  => 'fail',
            'message' => 'LearnPress checkout disabled. Use WooCommerce.'
        ]);
        exit;
    }, 0);
});


/**
 * 3. BLOCK AJAX CHECKOUT CALLS (critical)
 */
add_action('wp_ajax_learnpress_checkout', function () {
    wp_send_json([
        'result'  => 'fail',
        'message' => 'Checkout disabled.'
    ]);
    exit;
});

add_action('wp_ajax_nopriv_learnpress_checkout', function () {
    wp_send_json([
        'result'  => 'fail',
        'message' => 'Checkout disabled.'
    ]);
    exit;
});


/**
 * 4. REMOVE PAYMENT METHODS (UI safety)
 */
add_filter('learn-press/payment-methods', function () {
    return [];
});


/**
 * 5. DISABLE GUEST/LOGIN/REGISTER CHECKOUT OPTIONS
 */
add_filter('learn-press/checkout/enable-guest', '__return_false');
add_filter('learn-press/checkout/enable-login', '__return_false');
add_filter('learn-press/checkout/enable-register', '__return_false');

/**
 * Integration to bridge LearnPress and WooCommerce
 */



// Use the 'wp' hook to start the buffer only on course pages
add_action('wp', 'bks_start_course_buffer');

function bks_start_course_buffer() {
    if (is_singular('lp_course')) {
        ob_start('bks_modify_course_output');
    }
}

// This function processes the entire HTML of the page before it's sent to the browser
function bks_modify_course_output($html) {
    $course_id = get_the_ID();
    $woo_product_id = bks_get_woo_product_id_by_course($course_id);

    if (!$woo_product_id) {
        return $html;
    }

    $product = wc_get_product($woo_product_id);
    if (!$product) {
        return $html;
    }

    // 1. Define what we want to replace (LP price and buttons)
    // We look for common LearnPress CSS classes
    $woo_price = '<span class="woo-price">' . $product->get_price_html() . '</span>';
    $woo_button = do_shortcode('[add_to_cart id="' . $woo_product_id . '"]');
    
    $replacement_html = '<div class="bks-custom-checkout">' . $woo_price . $woo_button . '</div>';

    // 2. Use Regex to find and replace the LearnPress price/purchase container
    // This targets the most common LP container for buttons and prices
    $pattern = '/<div class="course-payment">.*?<\/div>/s'; 
    
    // If the above class doesn't match your version, try the general 'lp-course-buttons'
    if (!preg_match($pattern, $html)) {
        $pattern = '/<div class="lp-course-buttons">.*?<\/div>/s';
    }

    $html = preg_replace($pattern, $replacement_html, $html);

    return $html;
}

// Ensure the buffer is flushed
add_action('shutdown', function() {
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
}, 0);