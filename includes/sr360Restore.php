<?php

require_once 'access-check-wp.php';

class sr360Restore extends srCURLMethods {

    function __construct() {
        add_action('admin_enqueue_scripts', array($this, '_sr_admin_pages_style'));
        add_action('wp_ajax_sr360_restore_previous_views', array($this, 'sr360_restore_previous_views'));
    }

    function _sr_admin_pages_style($hook) {
        if (strpos($hook, _SR_360_ADMIN_PAGE_RESTORE) !== false) {
            wp_enqueue_script('sr-360-restore-previous-views-page', plugins_url('assets/js/restore-previous-views.js', __DIR__), array('jquery'), false, true);
            wp_localize_script('sr-360-restore-previous-views-page', 'sr360RestorePreviousViews', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'action' => 'sr360_restore_previous_views',
                'nonce' => wp_create_nonce('sr360_restore_previous_views'),
                'default_message' => '<span class="success"><img src="' . includes_url('images/spinner.gif') . '"/> Started restore process. Please do not close this page/window.</span>'
            ));
            $this->delete_gallery_360_icon();
        }
    }

    function sr360_restore_previous_views() {
        $action = sanitize_text_field($_POST['action']);
        if (check_ajax_referer($action, 'nonce', false)) {
            $product_id = intval($_POST['product_id']);
            $meta_key = sanitize_text_field($_POST['meta_key']);
            $unserialized_array = sanitize_meta($meta_key, get_post_meta($product_id, $meta_key, true), 'post');
            if (is_array($unserialized_array) && !empty($unserialized_array)) {
                $serialized_array = serialize(array_slice($unserialized_array, 0, 250));
                $data['product_id'] = $product_id;
                $data['serialized'] = $serialized_array;
                $data['license_key'] = $this->get_license_key();
                $response = $this->save_m_gallery($data);
                if (isset($response->code)) {
                    update_post_meta($product_id, _SR_360_PRODUCT, 0);
                    wp_send_json(array('message' => $response->message . ' | For product id: ' . $product_id), 200);
                } elseif (isset($response->success)) {
                    update_post_meta($product_id, _SR_360_PRODUCT, 1);
                    wp_send_json(array('message' => 'Restored 360 view for product id: ' . $product_id), 200);
                }
            } else {
                wp_send_json(array('error' => 'No images found for product id: ' . $product_id), 200);
            }
        } else {
            wp_send_json(array('error' => 'Authentication failed, security key expired'), 200);
        }
        exit;
    }

    private function delete_gallery_360_icon() {
        $meta_key = 'sr_has_360';
        $meta_value = 1;
        $product_ids = get_posts([
            'meta_key' => $meta_key,
            'meta_value' => $meta_value,
            'post_type' => 'product',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        foreach ($product_ids as $product_id):
            $product_gallery = get_post_meta($product_id, '_product_image_gallery', true);
            $product_gallery = explode(',', $product_gallery);
            $sr_360_icon_1 = intval(sanitize_option('sr_360_icon_1', get_option('sr_360_icon_1')));
            $sr_360_icon_2 = intval(sanitize_option('sr_360_icon_2', get_option('sr_360_icon_2')));
            $sr_360_icon_custom = intval(sanitize_option('sr_360_icon_custom', get_option('sr_360_icon_custom')));
            $product_gallery = $this->remove_stack_element($product_gallery, $sr_360_icon_1);
            $product_gallery = $this->remove_stack_element($product_gallery, $sr_360_icon_2);
            if ($sr_360_icon_custom)
                $product_gallery = $this->remove_stack_element($product_gallery, $sr_360_icon_custom);
            update_post_meta($product_id, '_product_image_gallery', implode(',', $product_gallery));
        endforeach;
    }

}

new sr360Restore();
