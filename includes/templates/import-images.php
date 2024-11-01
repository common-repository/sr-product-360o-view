<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
require_once 'header.php';
$settings = $this->get_import_settings(array('license_key' => $this->get_license_key()));
if ($settings && isset($settings->settings)):
    $settings = $settings->settings;
    ?>
    <div class="superrishi-setting-container">
        <div class="superrishi-setting-row">
            <div class="superrishi-setting-output">
                <h2 class="superrishi-title title-regular">Bulk Import Images</h2>
            </div>
            <div class="superrishi-setting-output">
                <div class="wildcards-info">
                    <h2>URL Wildcards</h2>
                    <p>When setting up the base URL for your images, you can use the following wildcards to dynamically generate the complete URL:</p>
                    <ul>
                        <li><code>{product_or_variation_id}</code> - This wildcard will be replaced with the product or variation ID of the item.</li>
                        <li><code>{sku}</code> - This wildcard will be replaced with the Stock Keeping Unit (SKU) of the product.</li>
                    </ul>
                    <p class="superrishi-para para-note">For example, if your base URL is <code>https://example.com/wp-content/uploads/360/{product_or_variation_id}/images/</code>, it will dynamically change to include the specific product's or variation's ID.</p>
                    <p class="superrishi-para para-note">For example, if your base URL is <code>https://example.com/wp-content/uploads/360/{sku}/images/</code>, it will dynamically change to include the specific product's or variation's SKU.</p>
                    <p class="superrishi-para para-note">You can use both the `{product_or_variation_id}` and `{sku}` wildcards together in a URL, and multiple times if necessary.</p>
                </div>
            </div>
        </div>
        <div class="superrishi-setting-row">
            <div class="superrishi-setting-output">
                <div class="superrishi-form-container">
                    <form id="superrishi-import-images" class="superrishi-import-images-form" method="POST">
                        <table class="import-images-table">
                            <tbody>
                                <tr>
                                    <td>
                                        <h3>Base URL</h3>
                                    </td>
                                    <td>
                                        <input type="text" name="baseurl" maxlength="264" placeholder="https://example.com/wp-content/uploads/360/{sku}/image_" value="<?= $settings->baseurl; ?>" required>
                                        <select name="lzero"><option value="1" <?= $settings->lzero == '1' ? 'selected' : ''; ?>>01</option><option value="0" <?= $settings->lzero == '0' ? 'selected' : ''; ?>>1</option></select><strong> . </strong><select name="ext"><option value="jpg" <?= $settings->ext == 'jpg' ? 'selected' : ''; ?>>jpg</option><option value="jpeg" <?= $settings->ext == 'jpeg' ? 'selected' : ''; ?>>jpeg</option><option value="png" <?= $settings->ext == 'png' ? 'selected' : ''; ?>>png</option><option value="webp" <?= $settings->ext == 'webp' ? 'selected' : ''; ?>>webp</option></select>
                                        <code>First Image URL: <span class="url-first-image"><i class="base-url"><?= $settings->baseurl ? $settings->baseurl : 'https://example.com/wp-content/uploads/360/{sku}/image_'; ?></i><i class="image-first"><?= $settings->lzero ? '0' : ''; ?>1</i>.<i class="image-ext"><?= $settings->ext ? $settings->ext : 'jpg'; ?></i></span></code>
                                        <code>Second Image URL: <span class="url-second-image"><i class="base-url"><?= $settings->baseurl ? $settings->baseurl : 'https://example.com/wp-content/uploads/360/{sku}/image_'; ?></i><i class="image-second"><?= $settings->lzero ? '0' : ''; ?>2</i>.<i class="image-ext"><?= $settings->ext ? $settings->ext : 'jpg'; ?></i></span></code>
                                        <code>Up-to Image URL <span class="superrishi-para para-note">(If Found)</span>: <span class="url-last-image"><i class="base-url"><?= $settings->baseurl ? $settings->baseurl : 'https://example.com/wp-content/uploads/360/{sku}/image_'; ?></i><i><?= _SR_360_ITEM_MAX; ?></i>.<i class="image-ext"><?= $settings->ext ? $settings->ext : 'jpg'; ?></i></span></code>
                                    </td>
                                </tr>
                                <tr><td></td><td></td></tr>
                                <tr><td></td><td></td></tr>
                                <tr>
                                    <td>
                                        <h3>Update existing 360&deg; items</h3>
                                    </td>
                                    <td>
                                        <input type="hidden" name="update_existing" value="0" />
                                        <input type="checkbox" name="update_existing" value="1" />
                                        <p class="superrishi-para para-note error">Note: If checked, this option will overwrite the 360&deg; images URLs for existing products and variations. Only check and continue if you understood this and agree for it.</p>
                                    </td>
                                </tr>
                                <tr><td></td><td></td></tr>
                                <tr>
                                    <td><button type="submit" class="button" id="import-360-settings-save-button">Save Settings &amp; Import Images</button> <span class="save-import-settings-response"></span></td>
                                    <td><p id="superrishi-ajax-response" class="import-action-response error"></p></td>
                                </tr>
                                <tr class="live_logs_printing">
                                    <td><h3>Current Import Log</h3><p class="superrishi-para para-note">(You can download the import log once the process is complete. Be aware that this log is temporary and will disappear when the page is refreshed or a new process begins)</p><button id="sr360-download-import-log" type="button"><i class="dashicons dashicons-download"></i> Download Log</button></td>
                                    <td><ol id="current-logs-flow" reversed></ol></td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Elements
            var baseUrlInput = document.querySelector('input[name="baseurl"]');
            var lzeroSelect = document.querySelector('select[name="lzero"]');
            var extSelect = document.querySelector('select[name="ext"]');
            var firstImageUrlSpan = document.querySelector('.url-first-image .base-url');
            var firstImageLzeroSpan = document.querySelector('.url-first-image .image-first');
            var secondImageUrlSpan = document.querySelector('.url-second-image .base-url');
            var secondImageLzeroSpan = document.querySelector('.url-second-image .image-second');
            var commonImageExtSpan = document.querySelectorAll('.image-ext');
            var lastImageUrlSpan = document.querySelector('.url-last-image .base-url');

            // Validate URL
            function isValidHttpUrl(string) {
                let url;

                try {
                    url = new URL(string);
                } catch (_) {
                    return false;
                }

                return url.protocol === "http:" || url.protocol === "https:";
            }

            function updateUrls() {
                var baseUrl = baseUrlInput.value;
                var lzero = lzeroSelect.value;
                var ext = extSelect.value;

                if (!isValidHttpUrl(baseUrl)) {
                    alert("Please enter a valid URL.");
                    return;
                }

                var secondImageNumber;
                if (lzero === "1") {
                    secondImageNumber = '02';
                } else {
                    secondImageNumber = '2';
                }

                firstImageUrlSpan.textContent = baseUrl;
                firstImageLzeroSpan.textContent = lzero === "1" ? '01' : '1';
                secondImageUrlSpan.textContent = baseUrl;
                secondImageLzeroSpan.textContent = secondImageNumber;
                commonImageExtSpan.forEach(span => span.textContent = ext);
                lastImageUrlSpan.textContent = baseUrl;
            }

            baseUrlInput.addEventListener('change', updateUrls);
            lzeroSelect.addEventListener('change', updateUrls);
            extSelect.addEventListener('change', updateUrls);
        });
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var checkbox = document.querySelector('input[name="update_existing"][type="checkbox"]');
            checkbox.addEventListener('change', function (event) {
                if (this.checked) {
                    var confirmAction = confirm("This action will overwrite the 360-degree images URLs for existing products and variations. If you know and agree, then continue to check this box.");
                    if (!confirmAction) {
                        event.preventDefault();
                        this.checked = false;
                    }
                }
            });
        });
    </script>
    <?php
else:
    $message = isset($settings->message) ? sanitize_text_field($settings->message) : 'Sorry, you have not activated your subscription license yet.';
    echo '<div style="background-color: rgb(248, 215, 218); color: rgb(114, 28, 36); padding: 10px; margin-bottom: 10px; border-radius: 5px;">' . $message . '</div>';
endif;
require_once 'footer.php';
