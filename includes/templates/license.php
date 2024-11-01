<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
require_once 'header.php';
?>
<div class="superrishi-setting-container">
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <h2 class="superrishi-title title-regular">Activate your license</h2>
        </div>
        <div class="superrishi-setting-output">
            <?php
            $credentials = $this->get_credentials();
            if ($credentials['license_key'] && $credentials['secret_key'] && $credentials['access_token']) {
                echo '<p class = "superrishi-para para-small success">No action required: <span class="superrishi-para para-note">You have already activated your subscription license.</span></p>';
            }
            ?>
            <p class="superrishi-para para-med">You need to get a license key &amp; secret to activate this plugin on
                your website. If you not already have a license key &amp; secret <a class="help-link help-link-external"
                                                                                    href="<?= _SR_360_LICENSE_URL; ?>" target="_blank">Get your license key and subcription secret here. &nearr;</a>
            </p>
        </div>
    </div>
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <div class="superrishi-form-container">
                <form id="superrishi-license-activation" class="superrishi-form" method="POST" action="#">
                    <input type="hidden" id="superrishi-security" name="superrishi-security" value="<?= $this->get_unauthorize_token(); ?>" />
                    <label for="superrishi-license-key">
                        License Key: <input type="text" id="superrishi-license-key" name="license-key" value="<?= $credentials['license_key']; ?>" placeholder="Ex: ABCDE8989YUIOP3A22P090984X32V1C5" required/>
                    </label>
                    <label for="superrishi-secret">
                        Secret: <input type="password" id="superrishi-secret" name="active-host" value="<?= $credentials['secret_key']; ?>" placeholder="**********************************************" required />
                    </label>
                    <input type="submit" value="submit" />
                </form>
            </div>
        </div>
        <div class="superrishi-setting-output">
            <p class="superrishi-para para-note">
                License key &amp; secret is required for both &quot;free &amp; paid&quot; subscriptions. To get your license key &amp; secret kindly visit your account at <a class="help-link help-link-external" href="<?= SUPER_RISHI_WEBSITE; ?>" target="_blank">superrishi.com &nearr;</a>.
            </p>
        </div>
    </div>
</div>
<div class="superrishi-popups">
    <div id="superrishi-domain-link-popup" class="superrishi-popup-container">
        <div class="superrishi-popup">
            <div class="superrishi-popup-title">
                <h3 class="superrishi-title title-alert">Confirm to proceed following action:</h3>
            </div>
            <div class="superrishi-popup-message">
                <p class="superrishi-para para-small">
                    You are about to activate your license key for
                </p>
                <p>
                    <samp>Plugin: SR PRODUCT 360&deg; VIEW</samp>
                </p>
                <p>on <samp>Domain: <?= $this->get_domain_name(); ?></samp></p>
                <p class="superrishi-para para-small">
                    By clicking on confirm you are agree to the &quot;SUPERRISHI.COM&quot; <a class="help-link help-link-external" href="<?= _SR_360_PLUGIN_USAGE_POLICY; ?>" target="_blank">Plugin Usage Policy &nearr;</a> &amp; <a class="help-link help-link-external" href="<?= _SR_360_PLUGIN_TERMS_CONDITIONS; ?>" target="_blank">Terms &amp; Condition &nearr;</a>
                </p>
            </div>
            <div class="superrishi-buttons">
                <button id="accept-link-domain" class="superrishi-button-left">Confirm</button>
                <button id="cancel-link-domain" class="superrishi-button-right">Cancel</button>
            </div>
        </div>
    </div>
    <div id="superrishi-response-popup" class="superrishi-popup-container">
        <div class="superrishi-popup">
            <div class="superrishi-popup-message">
                <!--a response message will be displayed here.-->
            </div>
            <div class="superrishi-buttons">
                <button id="close-response-popup" class="superrishi-button-left">Ok</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'footer.php';
