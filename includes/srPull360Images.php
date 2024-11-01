<?php

require_once 'access-check-wp.php';

class srPull360Images {

    private $base_url;
    private $lzero;
    private $ext;

    function __construct($baseurl, $lzero, $ext) {
        $this->base_url = $baseurl;
        $this->lzero = $lzero;
        $this->ext = $ext;
    }

    function checkImages() {
        $result['message'] = '';
        $result['images_found'] = 0;
        for ($i = 1; $i <= _SR_360_ITEM_MAX; $i++) {
            $imageNumber = $this->lzero ? str_pad($i, 2, '0', STR_PAD_LEFT) : $i;
            $imageUrl = $this->base_url . $imageNumber . '.' . $this->ext;
            $response = $this->fileExistsAtUrl($imageUrl);
            if (!$response['isValid']) {
                if ($response['error'] && $i === 1) {
                    $result['message'] = $response['error'];
                } else {
                    $result['images_found'] = $i - 1;
                    $result['message'] = 'Total images found: ' . $result['images_found'];
                }
                break;
            }
        }
        return $result;
    }

    private function fileExistsAtUrl($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_exec($ch);

        $response = [
            'isValid' => false,
            'error' => ''
        ];

        if (curl_errno($ch)) {
            $response['error'] = curl_error($ch);
        } else {
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($responseCode == 200) {
                $response['isValid'] = true;
            }
        }

        curl_close($ch);
        return $response;
    }

}
