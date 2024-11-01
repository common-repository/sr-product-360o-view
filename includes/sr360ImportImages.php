<?php

require_once 'access-check-wp.php';

class sr360ImportImages extends srCURLMethods {

    private $available_extensions = array(
        0 => 'jpg',
        1 => 'jpeg',
        2 => 'png',
        3 => 'webp'
    );

    function __construct() {
        add_action('wp_ajax_sr360_import_images', array($this, 'sr360_import_images'));
        add_action('wp_ajax_sr360_process_import', array($this, 'sr360_process_import'));
        add_action('admin_enqueue_scripts', array($this, 'import_images_scripts'));
    }

    function import_images_scripts($hook) {
        if (strpos($hook, _SR_360_ADMIN_PAGE_IMPORT_IMAGES) !== false) {
            $all_items_ids = $this->get_products_variations_ids();
            $filtered_items_ids = $this->get_products_variations_ids(true);
            ksort($all_items_ids);
            ksort($filtered_items_ids);
            wp_enqueue_script('sr360-import-images', plugins_url('assets/js/import-images.js', __DIR__), array(), false, true);
            wp_localize_script('sr360-import-images', 'sr360ImportImages', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'action' => 'sr360_import_images',
                'action_1' => 'sr360_process_import',
                'nonce' => wp_create_nonce('sr360_import_images'),
                'nonce_1' => wp_create_nonce('sr360_process_import'),
                'all_product_ids' => $all_items_ids,
                'filtered_product_ids' => $filtered_items_ids
            ));
        }
    }

    function sr360_import_images() {
        $action = sanitize_text_field($_POST['action']);
        if (check_ajax_referer($action, 'nonce', false)) {
            $data['update_existing'] = intval($_POST['update_existing']);
            $data['lzero'] = intval($_POST['lzero']);
            $data['ext'] = sanitize_text_field($_POST['ext']);
            $data['baseurl'] = sanitize_text_field($_POST['baseurl']);
            unset($_POST['action'], $_POST['nonce'], $_POST['update_existing']);
            if ((!empty($data['baseurl']) && strlen($data['baseurl']) < 265) && ($data['lzero'] === 0 || $data['lzero'] === 1) && in_array($data['ext'], $this->available_extensions)) {
                if (filter_var($data['baseurl'], FILTER_VALIDATE_URL) !== false) {
                    $data['license_key'] = $this->get_license_key();
                    $response = $this->save_image_import_settings($data);
                    if (isset($response->code)) {
                        wp_send_json($response, 200);
                    } elseif (isset($response->token)) {
                        wp_send_json(array('code' => 'process_import', 'message' => '<span class="success"><img src="' . includes_url('images/spinner.gif') . '"/> Started images import process. Please do not close this page/window.</span>', 'data' => $data));
                    }
                } else {
                    wp_send_json(array('message' => 'Please enter valid Base URL.'), 200);
                }
            } else {
                wp_send_json(array('message' => 'Base URL is required.'), 200);
            }
        } else {
            wp_send_json(array('message' => 'Some error occured. Please refresh the page and try again.'), 200);
        }
        exit;
    }

    function sr360_process_import() {
        $action = sanitize_text_field($_POST['action']);
        if (check_ajax_referer($action, 'nonce', false)) {
            $is_variation = false;
            $product_id = intval($_POST['product_id']);
            $key = intval($_POST['key']);
            if ($product_id === 0 || $key === 0) {
                wp_send_json(array('error' => 'Invalid Product Or Variation'), 200);
            }
            if ($product_id !== $key) {
                $is_variation = true;
            }
            $baseurl = $this->processUrl(sanitize_text_field($_POST['data']['baseurl']), $product_id);
            $lzero = intval($_POST['data']['lzero']);
            $ext = sanitize_text_field($_POST['data']['ext']);
            if ($baseurl === null) {
                wp_send_json(array('error' => 'Could not find the SKU'), 200);
            } else {
                $image_import = new srPull360Images($baseurl, $lzero, $ext);
                $result = $image_import->checkImages();
                if (isset($result['message']) && isset($result['images_found'])) {
                    $message = sanitize_text_field($result['message']);
                    $images = intval($result['images_found']);
                    $has_images = $images > 0 ? 1 : 0;
                    $data['license_key'] = sanitize_text_field($_POST['data']['license_key']);
                    $data['images'] = $images;
                    $data['baseurl'] = $baseurl;
                    $data['lzero'] = $lzero;
                    $data['ext'] = $ext;
                    if ($is_variation) {
                        $data['product_id'] = $key;
                        $data['variation_id'] = $product_id;
                    } else {
                        $data['product_id'] = $product_id;
                        $data['variation_id'] = 0;
                    }
                    $response = $this->save_360_images($data);
                    if (isset($response->code)) {
                        update_post_meta($product_id, _SR_360_PRODUCT, 0);
                        wp_send_json(array('summary' => $response->message), 200);
                    } elseif (isset($response->success)) {
                        update_post_meta($product_id, _SR_360_PRODUCT, $has_images);
                        wp_send_json(array('summary' => $message), 200);
                    }
                } else {
                    wp_send_json(array('error' => 'Could not fetch images'), 200);
                }
            }
        } else {
            wp_send_json(array('error' => 'Authentication failed, security key expired'), 200);
        }
        exit;
    }

}

new sr360ImportImages();
