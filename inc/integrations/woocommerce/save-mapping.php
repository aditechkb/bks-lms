<?php

    /** 
     * Save mapping into custom table
     */
    function save_mapping($product_id) {
        if (!isset($_POST['_bks_lp_course_id'])) return;

        global $wpdb;

        $course_id = intval($_POST['_bks_lp_course_id']);

        if (!$course_id) return;

        // Upsert logic (1:1 enforced)
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . 'bks_product_course_mapping' . " WHERE product_id = %d",
                $product_id
            )
        );

        if ($existing) {
            $wpdb->update(
                $wpdb->prefix . 'bks_product_course_mapping',
                ['course_id' => $course_id],
                ['product_id' => $product_id]
            );
        } else {
            $wpdb->insert(
                $wpdb->prefix . 'bks_product_course_mapping',
                [
                    'product_id' => $product_id,
                    'course_id'  => $course_id
                ]
            );
        }
    }