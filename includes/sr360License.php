<?php

require_once 'access-check-wp.php';

class sr360License extends srCURLMethods {

    private $license_form_nonce = 'license_form_nonce';

    function __construct() {
        add_action('admin_enqueue_scripts', array($this, '_sr_admin_pages_style'));
        add_action('wp_ajax_sr360_activate_license', array($this, 'sr360_activate_license'));
    }

    function _sr_admin_pages_style($hook) {
        if (strpos($hook, _SR_360_ADMIN_PAGE_LICENSE) !== false) {
            wp_enqueue_script('sr360-license-activation', plugins_url('assets/js/license-activate.js', __DIR__), array(), false, true);
            wp_localize_script('sr360-license-activation', 'sr360licenseFormObj', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce($this->license_form_nonce),
                'action' => 'sr360_activate_license'
            ));
        }
    }

    function check_license_status() {
        $license = $this->get_license_key();
        $secret = $this->get_license_secret();
        $token = $this->get_access_token();
        if ($license && $secret && $token) {
            $license_status = true;
        } else {
            $license_status = false;
        }
        return $license_status;
    }

    function admin_notice_activate_license() {
        ?>
        <div class="sr-notice-container notice notice-error is-dismissible">
            <div class="sr-section-left">
                <a class="navbar-brand" href="<?= SUPER_RISHI_WEBSITE; ?>" rel="noopener"><?= __('SUPER RISHI', 'sr-product-360-view'); ?></a>
            </div>
            <div class="sr-section-right">
                <h3><?= __('Thank you for installing &ldquo;SR Product 360&deg; View&rdquo;.', 'sr-product-360-view'); ?></h3>
                <p>
                    <a class="button" href="<?= admin_url('admin.php?page=' . _SR_360_ADMIN_PAGE_LICENSE); ?>">Activate License</a>
                </p>
                <p>
                    Please activate your license for this website. For both free and paid subscriptions, You can get your license key <a href="<?= _SR_360_LICENSE_URL; ?>" class="help-link" rel="noopener" target="_blank">here &nearr;</a>.
                </p>
                <a href="<?= SUPER_RISHI_WEBSITE; ?>" class="help-link" rel="noopener" target="_blank">Learn more &nearr;</a>
            </div>
        </div>
        <?php
    }

    function sr360_activate_license() {
        if (!check_ajax_referer($this->license_form_nonce, 'nonce', false)) {
            wp_send_json('<p class="superrishi-para para-small error">License activation form is expired. Please refresh page and try again. Thank you.</p>', 200);
        } else {
            unset($_POST['action']);
            unset($_POST['nonce']);
            $data['security'] = trim(sanitize_text_field($_POST['security']));
            $data['license'] = trim(sanitize_text_field($_POST['license_key']));
            $data['secret'] = trim(sanitize_text_field($_POST['secret']));
            $success_fail_1 = '<p class="superrishi-para para-small error">Please fill all the fields before submitting the form.</p>';
            $success_fail_2 = '<p class="superrishi-para para-small error">Sorry, we were not able to activate your license. Please try again later.</p>';
            $success_fail_3 = '<p class="superrishi-para para-small error">Sorry, your website doesn\'t qualify for a valid domain/host name.</p>';
            if ($data['security'] && $data['license'] && $data['secret']) {
                $response = $this->activate_subscription($data);
                if ($response) {
                    $response_type = $response->data->status;
                    if ($response_type === 200) {
                        update_option(_SR_360_LICENSE_KEY, $data['license'], 'no');
                        update_option(_SR_360_SECRET, $data['secret'], 'no');
                        update_option(_SR_360_TOKEN, $response->token, 'no');
                        wp_send_json('<p class="superrishi-para para-small success">' . $response->message . '</p>', 200);
                    } else {
                        wp_send_json('<p class="superrishi-para para-small error">' . $response->message . '</p>', 200);
                    }
                } else {
                    wp_send_json($success_fail_2, 200);
                }
            } else {
                if (empty($data['security'])) {
                    wp_send_json($success_fail_3, 200);
                } else {
                    wp_send_json($success_fail_1, 200);
                }
            }
        }
        exit;
    }

}
