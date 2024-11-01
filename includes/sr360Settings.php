<?php

require_once 'access-check-wp.php';

class sr360Settings extends srCURLMethods {

    function __construct() {
        add_action('admin_post_sr360_save_settings', array($this, 'sr360_save_settings'));
    }

    function sr360_save_settings() {
        $url = $_SERVER['HTTP_REFERER'];
        if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_key($_POST['nonce']), $this->_nonce_strings[sanitize_key($_POST['action'])])) {
            $action = sanitize_text_field($_POST['action-for-request']);
            unset($_POST['action'], $_POST['nonce'], $_POST['action-for-request']);
            if ($action === 'save'):
                $complete = true;
                $data = array();
                foreach ($_POST as $key => $value):
                    $key = trim(sanitize_text_field($key));
                    $value = trim(sanitize_text_field($value));
                    if ($value === ""):
                        $complete = false;
                        break;
                    else:
                        $data[$key] = $value;
                    endif;
                endforeach;
                if ($complete):
                    $response = $this->save_settings(array('settings' => $data, 'license_key' => $this->get_license_key()));
                    if ($response):
                        if ($response->code):
                            wp_redirect($url . '&t="error"&m="' . $response->message . '"');
                        else:
                            if ($response->token):
                                update_option(_SR_360_TOKEN, $response->token, 'no');
                                wp_redirect($url . '&t="success"&m="Settings saved successfully."');
                            endif;
                        endif;
                    else:
                        wp_redirect($url . '&t="error"&m="Sorry, we were unable to save your settings."');
                    endif;
                else:
                    wp_redirect($url . '&t="error"&m="All fields are mandatory."');
                endif;
            else:
                if ($action === 'reset to default'):
                    $response = $this->reset_settings(array('license_key' => $this->get_license_key()));
                    if ($response):
                        if ($response->code):
                            wp_redirect($url . '&t="error"&m="' . $response->message . '"');
                        else:
                            if ($response->token):
                                update_option(_SR_360_TOKEN, $response->token, 'no');
                                wp_redirect($url . '&t="success"&m="Settings reset successfull."');
                            endif;
                        endif;
                    else:
                        wp_redirect($url . '&t="error"&m="Sorry, we were unable to reset your settings."');
                    endif;
                endif;
            endif;
        } else {
            wp_redirect($url . '&t="error"&m="Security check failed!"');
        }
    }

}

new sr360Settings();
