<?php
defined('ABSPATH') || exit;

/**
 * 🚫 HARD BLOCK LEARNPRESS CHECKOUT COMPLETELY
 * (UI + AJAX + API safe)
 */

add_action('init', function () {

    if (!class_exists('LP_Checkout')) {
        return;
    }

    /**
     * STEP 1: Block validation stage
     */
    add_action('learn-press/validate-checkout-fields', function () {
        wp_send_json([
            'result'  => 'fail',
            'message' => 'LearnPress checkout is disabled. Please use WooCommerce checkout.'
        ]);
        exit;
    }, 0);

    /**
     * STEP 2: Block payment validation
     */
    add_action('learn-press/validate-payment', function () {
        wp_send_json([
            'result'  => 'fail',
            'message' => 'Checkout disabled.'
        ]);
        exit;
    }, 0);

}, 1);