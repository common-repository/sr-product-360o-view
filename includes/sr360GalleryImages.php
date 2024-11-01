<?php
require_once 'access-check-wp.php';

class sr360GalleryImages extends srCURLMethods {

    private $add_script_into = array(
        'page' => array('post.php', 'post-new.php'),
        'type' => array('product')
    );

    function __construct() {
        add_action('admin_enqueue_scripts', array($this, '_sr_admin_pages_style'));
        add_action('add_meta_boxes', array($this, 'sr_product_360_view_metabox'));
        add_action('save_post', array($this, 'sr_product_360_view_metabox_save'));
    }

    function _sr_admin_pages_style($hook) {
        global $typenow;
        if (in_array($hook, $this->add_script_into['page']) && in_array($typenow, $this->add_script_into['type'])) {
            wp_enqueue_script('sr-wc-p360v-images', plugins_url('/assets/js/sr-wc-360-images.js', __DIR__), array('jquery', 'jquery-ui-sortable'));
            wp_enqueue_style('sr-wc-p360v-images', plugins_url('/assets/css/sr-wc-360-images.css', __DIR__));
        }
    }

    function sr_product_360_view_metabox($post_type) {
        if (in_array($post_type, $this->add_script_into['type'])) {
            add_meta_box(
                    'sr-product-360-view', 'Product 360&#176; View', array($this, 'sr_product_360_view_callback'), $post_type, 'normal', 'core'
            );
        }
    }

    function sr_product_360_view_callback($post) {
        wp_nonce_field(basename(__FILE__), 'sr_product_360_view_images_nonce');
        $data['product_id'] = $post->ID;
        $data['license_key'] = $this->get_license_key();
        $result = $this->get_m_gallery($data);
        ?>
        <table class="form-table">
            <tr>
                <td>
                    <?php
                    if (isset($result->m_gallery) || (isset($result->code) && $result->code === 'not_found')) {
                        ?>
                        <a class="images-add button" href="#" data-uploader-title="Choose product 360&#176; images in progressive order" data-uploader-button-text="Select Images">Add 360&#176; images</a>
                        <?php
                    } else {
                        echo '<p>' . $result->message . '</p>';
                    }
                    ?>
                    <ul id="sr-product-360-view-images-list">
                        <?php
                        if (isset($result->m_gallery)) {
                            $ids = maybe_unserialize($result->m_gallery);
                            if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value);
                                    ?>
                                    <li>
                                        <input type="hidden" name="sr_product_360_view_images[<?php echo $key; ?>]" value="<?php echo $value; ?>">
                                        <img class="image-preview" src="<?php echo $image[0]; ?>">
                                        <a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image"><?= __('Change image', 'sr-product-360-view'); ?></a><br>
                                        <small><a class="remove-image" href="#">Remove image</a></small>
                                    </li>
                                    <?php
                                endforeach;
                            endif;
                        }
                        ?>
                    </ul>
                </td>
            </tr>
        </table>
        <?php
    }

    function sr_product_360_view_metabox_save($post_id) {
        if (!isset($_POST['sr_product_360_view_images_nonce']) || (empty($_POST['sr_product_360_view_images_nonce']) || !wp_verify_nonce($_POST['sr_product_360_view_images_nonce'], basename(__FILE__))))
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (isset($_POST['sr_product_360_view_images'])) {
            $images_id = array_map('intval', $_POST['sr_product_360_view_images']);
            if (is_array($images_id) && !empty($images_id)) {
                $serialized_array = serialize(array_slice($images_id, 0, 250));
                $data['product_id'] = $post_id;
                $data['serialized'] = $serialized_array;
                $data['license_key'] = $this->get_license_key();
                $response = $this->save_m_gallery($data);
                if (isset($response->code)) {
                    update_post_meta($post_id, _SR_360_PRODUCT, 0);
                } elseif (isset($response->success)) {
                    update_post_meta($post_id, _SR_360_PRODUCT, 1);
                }
            } else {
                $data['product_id'] = $post_id;
                $data['license_key'] = $this->get_license_key();
                $response = $this->clear_m_gallery($data);
                if (isset($response->success)) {
                    update_post_meta($post_id, _SR_360_PRODUCT, 0);
                }
            }
        } else {
            $data['product_id'] = $post_id;
            $data['license_key'] = $this->get_license_key();
            $response = $this->clear_m_gallery($data);
            if (isset($response->success)) {
                update_post_meta($post_id, _SR_360_PRODUCT, 0);
            }
        }
    }

}

new sr360GalleryImages();
