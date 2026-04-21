<?php

defined('ABSPATH') || exit;

/**
 * BKS Product ↔ Course Mapping (WooCommerce → LearnPress)
 * 1:1 mapping stored in custom DB table
 */

class BKS_Product_Course_Mapping {

    private static $table;

    public static function init() {
        global $wpdb;
        self::$table = $wpdb->prefix . 'bks_product_course_mapping';

        // Admin UI
        add_action('woocommerce_product_options_general_product_data', [self::class, 'add_course_dropdown']);
        add_action('woocommerce_process_product_meta', [self::class, 'save_mapping']);

        // Ensure table exists
        add_action('init', [self::class, 'maybe_create_table']);
    }
 

    /**
     * Add LearnPress course dropdown in WooCommerce product edit page
     */
    public static function add_course_dropdown() {
        global $post;

        $selected_course = self::get_course_by_product($post->ID);
        $courses = self::get_all_courses();

        echo '<div class="options_group">';

        woocommerce_wp_select([
            'id'          => '_bks_lp_course_id',
            'label'       => __('Linked LearnPress Course', 'bks-lms'),
            'options'     => $courses,
            'value'       => $selected_course,
            'desc_tip'    => true,
            'description' => 'Select LearnPress course mapped to this product (1:1 mapping).'
        ]);

        echo '</div>';
    }
}

BKS_Product_Course_Mapping::init();