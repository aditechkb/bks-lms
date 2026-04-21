<?php
/**
 * BKS LMS - HARD BLOCK LearnPress Checkout (WooCommerce Only)
 */

defined('ABSPATH') || exit;


/**
 * BLOCK 1: Catch request BEFORE LearnPress processes checkout
 */
add_action('init', function () {

    if (is_admin()) return;

    // Detect LearnPress checkout POST or AJAX requests
    $is_lp_checkout =
        (isset($_POST['lp-ajax']) || isset($_POST['learn-press-checkout-nonce']))
        || (isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'learn-press') !== false);

    if ($is_lp_checkout) {
        wp_send_json_error([
            'message' => 'LearnPress checkout is disabled. Please use WooCommerce checkout.'
        ], 403);
    }

}, 1);


/**
 * BLOCK 2: Stop checkout processing inside LearnPress engine
 */
add_action('learn-press/before-checkout', function () {

    throw new Exception(
        'LearnPress checkout is disabled. Use WooCommerce checkout instead.',
        403
    );

}, 1);


/**
 * BLOCK 3: Prevent validation stage bypass
 */
add_action('learn-press/validate-checkout-fields', function () {

    throw new Exception(
        'LearnPress checkout is disabled.',
        403
    );

}, 1);


/**
 * BLOCK 4: Prevent order creation completion
 */
add_action('learn-press/checkout-order-processed', function () {

    throw new Exception(
        'LearnPress checkout is disabled.',
        403
    );

}, 1, 2);


/**
 * BLOCK 5: Kill payment gateways completely
 */
add_filter('learn-press/payment-methods', function () {
    return [];
});


/**
 * BLOCK 6: Disable checkout options UI
 */
add_filter('learn-press/checkout/enable-guest', '__return_false');
add_filter('learn-press/checkout/enable-login', '__return_false');
add_filter('learn-press/checkout/enable-register', '__return_false');