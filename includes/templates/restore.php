<?php
if (!defined('ABSPATH')) :
    require_once '../access-check-wp.php';
endif;

require_once 'header.php';

$settings = $this->get_views_count();
if ($settings && isset($settings->views)):
    $views = $settings->views;
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'sr_pro_has_360_images',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    $product_ids = get_posts($args);

    if (!empty($product_ids)):
        $_360_images_meta_key = 'sr_pro_360_view_images';
    else:
        $_360_images_meta_key = 'sr_product_360_view_images';
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'sr_has_360',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        $product_ids = get_posts($args);
    endif;

    $_360_to_restore = count($product_ids);
    if ($_360_to_restore > 0):
        ?>
        <script>
            var sr360ProductsToRestore = [<?= implode(',', $product_ids); ?>];
            var sr360ViewsMetaKey = '<?= $_360_images_meta_key; ?>';
        </script>
        <div class="superrishi-setting-container">
            <div class="superrishi-setting-row">
                <div class="superrishi-setting-output">
                    <h2 class="superrishi-title title-regular">Restore Previous Views</h2>
                </div>
            </div>
            <div class="superrishi-setting-row">
                <div class="superrishi-setting-output">
                    <div class="superrishi-form-container">
                        <div id="restore-360-views">
                            <h2>Restore Your Previous 360 Views</h2>

                            <p class="info">Total 360 Views Found: <?= $_360_to_restore; ?></p>
                            <p class="info <?php echo ($views >= $_360_to_restore) ? 'success' : 'error'; ?>">
                                Available 360 Views In Your Subscription: <?= $views; ?>
                            </p>

                            <form id="superrishi-restore-views" class="superrishi-settings-form" method="POST">
                                <button type="submit" id="restore-previous-views-button" class="button button-primary">Restore Previous 360 Views</button>
                            </form>
                            <p id="superrishi-ajax-response"></p>
                            <table id="restoreLogs" style="visibility:collapse;">
                                <tbody>
                                    <tr>
                                        <td style="width:60%;"><h3>Current Restore Log</h3><p class="superrishi-para para-note">(You can download the restore log once the process is complete. Be aware that this log is temporary and will disappear when the page is refreshed or a new process begins)</p><button id="sr360-download-import-log" type="button"><i class="dashicons dashicons-download"></i> Download Log</button></td>
                                        <td><ol id="current-logs-flow" reversed></ol></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    else:
        ?>
        <p class="superrishi-para"><i class="dashicons dashicons-dismiss"></i> No previous views are available to be restored.</p>
    <?php
    endif;
else:
    $message = isset($settings->message) ? sanitize_text_field($settings->message) : 'Sorry, you have not activated your subscription license yet.';
    echo '<div style="background-color: rgb(248, 215, 218); color: rgb(114, 28, 36); padding: 10px; margin-bottom: 10px; border-radius: 5px;">' . $message . '</div>';
endif;
require_once 'footer.php';
