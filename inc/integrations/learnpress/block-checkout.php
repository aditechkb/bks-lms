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

add_action('wp', 'bks_init_learnpress_woo_bridge');

function bks_init_learnpress_woo_bridge() {
    // Only run this on single course pages
    if ( ! is_singular( 'lp_course' ) ) {
        return;
    }

    // 1. Hide the native LearnPress price
    // We use a high priority (100) to ensure we override LP defaults
    add_filter( 'learn-press/course-price-html', '__return_empty_string', 100 );

    // 2. Remove the standard LearnPress buttons
    remove_action( 'learn-press/course-buttons', 'learn_press_course_purchase_button', 10 );
    remove_action( 'learn-press/course-buttons', 'learn_press_course_enroll_button', 10 );

    // 3. Add our Custom WooCommerce Button
    add_action( 'learn-press/course-buttons', 'bks_render_woo_buy_button', 10 );
}

function bks_render_woo_buy_button() {
    $course_id = get_the_ID();
    
    // Fetch from your custom mapping table
    $woo_product_id = bks_get_woo_product_id_by_course( $course_id );

    if ( $woo_product_id ) {
        $product = wc_get_product( $woo_product_id );
        if ( $product ) {
            echo '<div class="bks-woo-wrapper">';
            echo '<p class="price">' . $product->get_price_html() . '</p>';
            echo do_shortcode( '[add_to_cart id="' . $woo_product_id . '"]' );
            echo '</div>';
        }
    } else {
        echo '<p>Course not currently available for purchase.</p>';
    }
}