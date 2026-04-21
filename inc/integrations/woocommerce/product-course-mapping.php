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
     * Create custom table if not exists
     */
    public static function maybe_create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS " . self::$table . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT UNSIGNED NOT NULL UNIQUE,
            course_id BIGINT UNSIGNED NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY course_id (course_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
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

    /**
     * Save mapping into custom table
     */
    public static function save_mapping($product_id) {
        if (!isset($_POST['_bks_lp_course_id'])) return;

        global $wpdb;

        $course_id = intval($_POST['_bks_lp_course_id']);

        if (!$course_id) return;

        // Upsert logic (1:1 enforced)
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::$table . " WHERE product_id = %d",
                $product_id
            )
        );

        if ($existing) {
            $wpdb->update(
                self::$table,
                ['course_id' => $course_id],
                ['product_id' => $product_id]
            );
        } else {
            $wpdb->insert(
                self::$table,
                [
                    'product_id' => $product_id,
                    'course_id'  => $course_id
                ]
            );
        }
    }

    /**
     * Get all LearnPress courses
     */
    public static function get_all_courses() {
        $courses = get_posts([
            'post_type'      => 'lp_course',
            'post_status'    => 'publish',
            'numberposts'    => -1
        ]);

        $list = ['' => '— Select Course —'];

        foreach ($courses as $course) {
            $list[$course->ID] = $course->post_title;
        }

        return $list;
    }

    /**
     * Get course by product
     */
    public static function get_course_by_product($product_id) {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT course_id FROM " . self::$table . " WHERE product_id = %d",
                $product_id
            )
        );

        return $result ?: '';
    }

    /**
     * Get product by course (optional use)
     */
    public static function get_product_by_course($course_id) {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT product_id FROM " . self::$table . " WHERE course_id = %d",
                $course_id
            )
        );
    }
}

BKS_Product_Course_Mapping::init();