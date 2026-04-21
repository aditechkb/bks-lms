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


// 6. Remove the default LearnPress price
add_filter( 'learn-press/course-price-html', 'bks_replace_lp_price_with_woo', 100, 2 );

function bks_replace_lp_price_with_woo( $price_html, $course ) {
    // Get the WooCommerce Product ID from your custom mapping table
    $woo_product_id = bks_get_mapped_woo_id( $course->get_id() );

    if ( $woo_product_id ) {
        $product = wc_get_product( $woo_product_id );
        if ( $product ) {
            // Return the WooCommerce price HTML instead
            return $product->get_price_html();
        }
    }
    return $price_html;
}

// 7. Replace the Purchase/Enroll Button
remove_action( 'learn-press/course-buttons', 'learn_press_course_purchase_button', 10 );
add_action( 'learn-press/course-buttons', 'bks_add_woo_buy_button', 10 );

function bks_add_woo_buy_button() {
    $course = learn_press_get_course();
    $woo_id = bks_get_mapped_woo_id( $course->get_id() );

    if ( $woo_id ) {
        // Output the WooCommerce Add to Cart button/shortcode
        echo do_shortcode( '[add_to_cart id="' . $woo_id . '"]' );
    }
}