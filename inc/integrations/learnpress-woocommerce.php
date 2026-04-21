<?php
defined('ABSPATH') || exit;

/**
 * 1. BLOCK checkout at LearnPress engine level (SAFE)
 */
add_action('learn-press/before-checkout', function () {

    // Stop checkout cleanly
    learn_press_add_message([
        'status'  => 'error',
        'content' => 'LearnPress checkout is disabled. Please use WooCommerce checkout.'
    ]);

    // prevent further processing safely
    remove_all_actions('learn-press/process-checkout');

}, 1);


/**
 * 2. BLOCK payment methods (prevents bypass)
 */
add_filter('learn-press/payment-methods', function () {
    return [];
});


/**
 * 3. DISABLE checkout options UI
 */
add_filter('learn-press/checkout/enable-guest', '__return_false');
add_filter('learn-press/checkout/enable-login', '__return_false');
add_filter('learn-press/checkout/enable-register', '__return_false');