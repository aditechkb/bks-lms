<?php

/**
 * 6. INJECT WOOCOMMERCE PRICE INTO COURSE PAGE
 */
add_action('learn-press/course-content-summary', function () {

    $course_id = get_the_ID();
    $product_id = bks_get_product_id_by_course($course_id);

    if (!$product_id) {
        return;
    }

    $product = wc_get_product($product_id);

    if (!$product) {
        return;
    }

    echo '<div class="bks-course-purchase-box">';

    // PRICE
    echo '<div class="price">';
    echo $product->get_price_html();
    echo '</div>';

    // ADD TO CART BUTTON
    echo '<div class="add-to-cart">';

    echo apply_filters(
        'woocommerce_loop_add_to_cart_link',
        sprintf(
            '<a href="%s" data-quantity="1" class="button add_to_cart_button" data-product_id="%d">%s</a>',
            esc_url($product->add_to_cart_url()),
            $product_id,
            __('Add to cart', 'woocommerce')
        ),
        $product
    );

    echo '</div>';

    echo '</div>';

}, 5);