<?php

require_once 'access-check-wp.php';

class sr360Icon {

    static function get_dashboard_icon_url() {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/dashicon-sr.png';
    }

    static function get_zoom_in_icon_url() {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/zoom-in.png';
    }

    static function get_zoom_out_icon_url() {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/zoom-out.png';
    }

    static function get_play_icon_url() {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/play.png';
    }

    static function get_pause_icon_url() {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/pause.png';
    }

    static function get_gallery_thumb_icon_url($img) {
        return plugin_dir_url(dirname(__FILE__)) . 'assets/img/360-' . $img . '.png';
    }

}
