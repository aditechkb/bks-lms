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