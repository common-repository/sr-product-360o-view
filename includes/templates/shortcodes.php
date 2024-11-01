<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
require_once 'header.php';
?>
<div id="sr-360-shortcodes" class="superrishi-setting-container">
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <h2 class="superrishi-title title-regular">Shortcodes</h2>
        </div>
    </div>
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <p class="superrishi-para para-note">
                <i class="dashicons dashicons-info-outline" style="vertical-align: middle"></i> <strong>Note:</strong> Shortcodes will only work on their respective product page (single product template/page).
            </p>
        </div>
        <div class="superrishi-setting-output">
            <section>
                <h4>Button Type:</h4>
                <p>When using button type, you can specify the button text and CSS classes:</p>
                <div class="code">[sr_360_view product_id="123" type="button" html="View in 360&deg;" class="btn btn-primary"] (Single Product)</div>
                <div class="code">[sr_360_view product_id="123" variation_id="456" type="button" html="View in 360&deg;" class="btn btn-primary"] (Variation)</div>

                <h4>Icon Type:</h4>
                <p>When using icon type, you can specify the icon URL and CSS classes:</p>
                <div class="code">[sr_360_view product_id="123" type="icon" url="http://example.com/path/to/icon.png" class="custom-icon-class"] (Single Product)</div>
                <div class="code">[sr_360_view product_id="123" variation_id="456" type="icon" url="http://example.com/path/to/icon.png" class="custom-icon-class"] (Variation)</div>
            </section>
        </div>
        <div class="superrishi-setting-output"></div>
    </div>
</div>