<?php

require_once 'access-check-wp.php';

class sr360Help {

    function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'sr360_faq_assets'));
    }

    function sr360_faq_assets($hook) {
        if (strpos($hook, _SR_360_ADMIN_PAGE_HELP) !== false) {
            wp_enqueue_style('sr-360-faq-page', plugins_url('assets/css/faq.css', __DIR__));
            wp_enqueue_script('sr-360-faq-page', plugins_url('assets/js/faq.js', __DIR__), array('jquery'), false, true);
        }
    }

}

new sr360Help();
