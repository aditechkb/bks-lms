<?php
/**
 * BKS LMS - Disable LearnPress Checkout
 * We use WooCommerce as the only payment system.
 */

defined('ABSPATH') || exit;

/**
 * Block LearnPress checkout at the earliest entry point
 */
add_action('learn-press/before-checkout', function () {

    throw new Exception(
        'LearnPress checkout is disabled. Please complete purchase using WooCommerce checkout.',
        403
    );

}, 1);


/**
 * Block checkout validation layer (extra safety)
 */
add_action('learn-press/validate-checkout-fields', function () {

    throw new Exception(
        'LearnPress checkout is disabled. Use WooCommerce for purchasing courses.',
        403
    );

}, 1);


/**
 * Block order processing stage (final safety net)
 */
add_action('learn-press/checkout-order-processed', function () {

    throw new Exception(
        'LearnPress checkout is disabled.',
        403
    );

}, 1, 2);


/**
 * Disable all checkout account options (UI safety)
 */
add_filter('learn-press/checkout/enable-guest', '__return_false');
add_filter('learn-press/checkout/enable-login', '__return_false');
add_filter('learn-press/checkout/enable-register', '__return_false');


/**
 * Remove payment methods (prevents silent bypass)
 */
add_filter('learn-press/payment-methods', function ($methods) {
    return [];
});