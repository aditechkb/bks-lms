<?php

    /**
     * Create custom table if not exists
    */
    function maybe_create_table() {
        global $wpdb;

        $table = $wpdb->prefix . 'bks_product_course_mapping';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT UNSIGNED NOT NULL,
            course_id BIGINT UNSIGNED NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            PRIMARY KEY (id),
            UNIQUE KEY uniq_product (product_id),
            UNIQUE KEY uniq_course (course_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }