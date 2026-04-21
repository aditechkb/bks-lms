<?php

    /**
     * Get all LearnPress courses
     */
    function bks_get_all_courses() {
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
    function bks_get_course_by_product($product_id) {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT course_id FROM " . $wpdb->prefix . 'bks_product_course_mapping' . " WHERE product_id = %d",
                $product_id
            )
        );

        return $result ?: '';
    }

    /**
     * Get product by course (optional use)
     */
    function bks_get_product_by_course($course_id) {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT product_id FROM " . $wpdb->prefix . 'bks_product_course_mapping' . " WHERE course_id = %d",
                $course_id
            )
        );
    }