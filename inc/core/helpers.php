<?php

function bks_get_product_id_by_course($course_id) {
    global $wpdb;

    return $wpdb->get_var(
        $wpdb->prepare(
            "SELECT product_id FROM {$wpdb->prefix}bks_product_course_mapping WHERE course_id = %d",
            $course_id
        )
    );
}