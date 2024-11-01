<?php

require_once 'access-check-wp.php';

class sr360AdminMenu extends srCURLMethods {

    private $credentials = array();

    function __construct() {
        add_action('admin_menu', array($this, 'sr_360_admin_menu'));
    }

    function get_credentials() {
        $this->credentials['license_key'] = $this->get_license_key();
        $this->credentials['secret_key'] = $this->get_license_secret();
        $this->credentials['access_token'] = $this->get_access_token();
        return $this->credentials;
    }

    function sr_360_admin_menu() {
        add_menu_page(
                __('SR Product 360&deg; View', 'sr-product-360-view'), // Page title
                'Product 360&deg; View', // Menu title
                'manage_options', // Capability
                _SR_360_ADMIN_PAGE_ITEMS, // Menu slug
                array($this, 'items_callback'), // Function to execute
                sr360Icon::get_dashboard_icon_url(), // Icon URL
                56 // Position
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('360&deg; Items', 'sr-product-360-view'),
                '360&deg; Views',
                'manage_options',
                _SR_360_ADMIN_PAGE_ITEMS,
                array($this, 'items_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('Settings', 'sr-product-360-view'),
                'Settings',
                'manage_options',
                _SR_360_ADMIN_PAGE_SETTINGS,
                array($this, 'settings_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('Bulk Import Images', 'sr-product-360-view'),
                'Bulk Import Images',
                'manage_options',
                _SR_360_ADMIN_PAGE_IMPORT_IMAGES,
                array($this, 'import_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('Restore Previous Data', 'sr-product-360-view'),
                'Restore Previous Views',
                'manage_options',
                _SR_360_ADMIN_PAGE_RESTORE,
                array($this, 'restore_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('Shortcodes', 'sr-product-360-view'),
                'Shortcodes',
                'manage_options',
                _SR_360_ADMIN_PAGE_SHORTCODES,
                array($this, 'shortcodes_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('Help', 'sr-product-360-view'),
                'Help',
                'manage_options',
                _SR_360_ADMIN_PAGE_HELP,
                array($this, 'help_callback')
        );

        add_submenu_page(
                _SR_360_ADMIN_PAGE_ITEMS, // Parent slug
                __('License Activation', 'sr-product-360-view'), // Page title
                'License Activation', // Menu title
                'manage_options', // Capability
                _SR_360_ADMIN_PAGE_LICENSE, // Menu slug
                array($this, 'license_callback') // Function to execute
        );
    }

    function license_callback() {
        include_once 'templates/license.php';
    }

    function settings_callback() {
        include_once 'templates/global-settings.php';
    }

    function import_callback() {
        include_once 'templates/import-images.php';
    }

    function items_callback() {
        include_once 'templates/items-360.php';
    }

    function shortcodes_callback() {
        include_once 'templates/shortcodes.php';
    }

    function help_callback() {
        include_once 'templates/help.php';
    }

    function restore_callback() {
        include_once 'templates/restore.php';
    }

}
