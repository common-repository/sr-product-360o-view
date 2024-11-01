<?php

require_once 'access-check-wp.php';

class srCURLMethods extends sr360Commons {

    private $url = array(
        'url' => 'https://api.superrishi.com/wp-json/',
        'url2' => 'https://tsv.superrishi.com/wp-json/',
        'unauthorized_token' => 'unauthorized-transaction/v1/transaction-security-key',
        'activate_subscription' => 'unauthorized-transaction/v1/activate-domain',
        'settings' => 'transaction/v1/settings',
        'save_settings' => 'transaction/v1/save-settings',
        'save_import_settings' => 'transaction/v1/save-import-settings',
        'import_settings' => 'transaction/v1/import-settings',
        'save_360' => 'transaction/v1/save-import',
        'delete_360' => 'transaction/v1/delete-import',
        'm_gallery' => 'transaction/v1/save-m-gallery',
        'reset_settings' => 'transaction/v1/reset-settings',
        'views_count' => 'transaction/v1/views-count',
        'get_m_gallery' => 'transaction/v1/get-m-gallery',
        'clear_m_gallery' => 'transaction/v1/clear-m-gallery',
        'get_360_view' => 'transaction/v1/get-360-view'
    );

    function __construct() {
        
    }

    protected function get_unauthorize_token() {
        $token = json_decode($this->sendRequest($this->url['url'] . $this->url['unauthorized_token'], site_url()));
        if (isset($token->token)) {
            return $token->token;
        }
        return false;
    }

    private function update_token($response) {
        if (isset($response->token)) {
            update_option(_SR_360_TOKEN, $response->token, 'no');
        }
    }

    protected function activate_subscription($data) {
        if (isset($data['license']) && isset($data['secret']) && isset($data['security'])) {
            $authHeader = 'Basic ' . base64_encode(trim(sanitize_text_field($data['license'])) . ':' . trim(sanitize_text_field($data['secret'])));
            unset($data['license'], $data['secret']);
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['activate_subscription'], site_url(), 'POST', $data, $authHeader));
            if (isset($response->code) || isset($response->token)) {
                return $response;
            }
        }
        return false;
    }

    protected function save_settings($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['save_settings'], site_url(), 'POST', $data, $authHeader));
            $this->update_token($response);
            if (isset($response->code) || isset($response->token)) {
                return $response;
            }
        }
        return false;
    }

    protected function save_image_import_settings($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['save_import_settings'], site_url(), 'POST', $data, $authHeader));
            $this->update_token($response);
            if (isset($response->code) || isset($response->token)) {
                return $response;
            }
        }
        return false;
    }

    protected function save_360_images($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['save_360'], site_url(), 'POST', $data, $authHeader));
            if (isset($response->code) || isset($response->success)) {
                return $response;
            }
        }
        return false;
    }

    protected function delete_360_images($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['delete_360'], site_url(), 'POST', $data, $authHeader));
            if (isset($response->code) || isset($response->success)) {
                return $response;
            }
        }
        return false;
    }

    protected function save_m_gallery($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['m_gallery'], site_url(), 'POST', $data, $authHeader));
            if (isset($response->code) || isset($response->success)) {
                return $response;
            }
        }
        return false;
    }

    protected function clear_m_gallery($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['clear_m_gallery'], site_url(), 'POST', $data, $authHeader));
            if (isset($response->code) || isset($response->success)) {
                return $response;
            }
        }
        return false;
    }

    protected function get_m_gallery($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['get_m_gallery'], site_url(), 'GET', $data, $authHeader));
            if (isset($response->code) || isset($response->m_gallery)) {
                return $response;
            }
        }
        return false;
    }

    protected function reset_settings($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['reset_settings'], site_url(), 'POST', $data, $authHeader));
            $this->update_token($response);
            if (isset($response->code) || isset($response->token)) {
                return $response;
            }
        }
        return false;
    }

    protected function get_settings_data($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['settings'], site_url(), 'GET', $data, $authHeader));
            return $response;
        }
        return false;
    }

    protected function get_import_settings($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['import_settings'], site_url(), 'GET', $data, $authHeader));
            return $response;
        }
        return false;
    }

    protected function get_views_count() {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url'] . $this->url['views_count'], site_url(), 'GET', array(), $authHeader));
            return $response;
        }
        return false;
    }

    protected function get_360_view($data) {
        $token = $this->get_access_token();
        if ($token) {
            $authHeader = 'Bearer ' . $token;
            $response = json_decode($this->sendRequest($this->url['url2'] . $this->url['get_360_view'], site_url(), 'GET', $data, $authHeader));
            return $response;
        }
        return false;
    }

    private function sendRequest($url, $referrer = '', $method = 'GET', $params = [], $authorizationHeader = '') {
        // Initialize cURL session
        $ch = curl_init();

        // Set the request method and related options
        switch (strtoupper($method)) {
            case 'POST':
                $options = [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($params),
                ];
                break;
            case 'PUT':
            case 'DELETE':
                $options = [
                    CURLOPT_CUSTOMREQUEST => strtoupper($method),
                    CURLOPT_POSTFIELDS => http_build_query($params),
                ];
                break;
            case 'GET':
            default:
                // If there are parameters, append them to the URL for a GET request
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                $options = [];
                break;
        }

        // Set common cURL options
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_FOLLOWLOCATION] = true; // Follow redirects
        $options[CURLOPT_HTTPHEADER] = ['Accept: application/json']; // Expect JSON
        // Set the referrer if provided
        if (!empty($referrer)) {
            $options[CURLOPT_REFERER] = $referrer;
        }

        // Set the authorization header if provided
        if (!empty($authorizationHeader)) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: ' . $authorizationHeader;
        }

        curl_setopt_array($ch, $options);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            return 'cURL Error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Return the response
        return $response;
    }

}
