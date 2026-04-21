<?php

/**
 * 6. REMOVE ALL LEARNPRESS PRICE BUTTONS
 */
add_action('init', function () {

    // Remove LearnPress purchase UI
    remove_all_actions('learn-press/course-buttons');

    // Optional: remove course meta pricing blocks
    remove_all_actions('learn-press/course-meta-secondary-left');

});
