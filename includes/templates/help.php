<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
require_once 'header.php';
?>
<div id="sr-360-shortcodes" class="superrishi-setting-container">
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <h2 class="superrishi-title title-regular">FAQ's</h2>
        </div>
    </div>
    <div class="superrishi-setting-row">
        <div class="superrishi-setting-output">
            <div id="faq-container">
                <button class="accordion">How can I activate plugin using the &ldquo;License Key &amp; Secret Key&rdquo;?</button>
                <div class="panel">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/BFRKTcCzZ0w" frameborder="0" allowfullscreen></iframe>
                </div>

                <button class="accordion">How can I get my &ldquo;License Key&rdquo;?</button>
                <div class="panel">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Uw5se7shEM8" frameborder="0" allowfullscreen></iframe>
                </div>

                <button class="accordion">How can I get my &ldquo;Secret Key&rdquo;?</button>
                <div class="panel">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/W7aBxjUcOKw" frameborder="0" allowfullscreen></iframe>
                </div>

                <button class="accordion">Have more questions? Contact &ldquo;Support&rdquo;&excl;</button>
                <div class="panel">
                    <p>Please, reach out to us at support@superrishi.com</p>
                    <p>Or you can have a direct chat with us at <a href='<?= SUPER_RISHI_WEBSITE; ?>' target="_blank" class='help-link' rel="noopener">superrishi.com &nearr;</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';
