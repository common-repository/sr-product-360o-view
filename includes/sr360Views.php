<?php
require_once 'access-check-wp.php';

class sr360Views extends srCURLMethods {

    function __construct() {
        add_action('wp_enqueue_scripts', array($this, '_360_view_assets'));
        add_action('woocommerce_before_single_product', array($this, '_is_woocommerce_single_product'));
        add_action('init', array($this, 'register_sr_360_view_shortcode'));
    }

    function register_sr_360_view_shortcode() {
        add_shortcode('sr_360_view', array($this, 'sr_360_view_shortcode_callback'));
    }

    function sr_360_view_shortcode_callback($atts) {
        $atts = shortcode_atts(array(
            'product_id' => '',
            'variation_id' => '0',
            'type' => '',
            'html' => '',
            'url' => '',
            'class' => ''
                ), $atts, 'sr_360_view');

        if (empty($atts['product_id']) || empty($atts['type']) || empty($atts['class'])) {
            return '';
        }

        $onclick = 'onclick="_trigger360Popup(' . esc_attr(intval($atts['product_id'])) . ', ' . esc_attr(intval($atts['variation_id'])) . ');"';

        if ($atts['type'] === 'button') {
            if (empty($atts['html'])) {
                return '';
            }
            return '<button class="' . esc_attr($atts['class']) . '" ' . $onclick . '>' . esc_html($atts['html']) . '</button>';
        } elseif ($atts['type'] === 'icon') {
            if (empty($atts['url'])) {
                return '';
            }
            return '<img src="' . esc_url($atts['url']) . '" class="' . esc_attr($atts['class']) . '" ' . $onclick . ' />';
        }

        return '';
    }

    function _360_view_assets() {
        wp_enqueue_style('sr360-view', plugins_url('assets/css/icon-360-view.css', __DIR__));
        wp_enqueue_script('sr360-view', plugins_url('assets/js/three-sixty.min.js', __DIR__), array('jquery'), false, true);
    }

    function _is_woocommerce_single_product() {
        if (function_exists('is_product') && is_product()) {
            global $product;
            $product_id = $product->get_id();
            if ($product->is_type('simple')) {
                $this->_is_360($product_id);
            } elseif ($product->is_type('variable')) {
                $variations = $product->get_children();
                $this->_is_360($product_id, $variations);
            } else {
                //Process for other product types...
            }
        }
    }

    private function _is_360($product_id, $variations = array()) {
        if (empty($variations)) {
            $is_360 = intval(sanitize_meta(_SR_360_PRODUCT, get_post_meta($product_id, _SR_360_PRODUCT, true), 'post'));
            if ($is_360) {
                $this->_apply_360_view($product_id);
            }
        } else {
            foreach ($variations as $variation_id) {
                $is_360 = intval(sanitize_meta(_SR_360_PRODUCT, get_post_meta($variation_id, _SR_360_PRODUCT, true), 'post')) || intval(sanitize_meta(_SR_360_PRODUCT, get_post_meta($product_id, _SR_360_PRODUCT, true), 'post'));
                if ($is_360) {
                    $this->_apply_360_view($product_id, $variations);
                    break;
                }
            }
        }
    }

    private function _apply_360_view($product_id, $variations = array()) {
        $data['product_id'] = $product_id;
        $data['license_key'] = $this->get_license_key();
        $_360_view = $this->get_360_view($data);
        if (isset($_360_view->data->status) && $_360_view->data->status === 200) {
//            print_r(maybe_unserialize($_360_view->_360_data[0]->settings));
            $this->_360_view_append($_360_view->_360_data);
        }
    }

    function _360_view_append($data) {
        $product_id = intval($data[0]->product_id);
        $variation_id = intval($data[0]->variation_id);
        $settings = maybe_unserialize($data[0]->settings);
        $popup_rule = $settings['popup_rule']['active'] === 'pr' ? '%' : 'px';
        $views = array();
        echo '<script> ';
        echo 'var _sr360_views = Array(); ';
        foreach ($data as $view) {
            echo ' _sr360_views[' . $view->product_id . '] = Array();';
        }
        foreach ($data as $view) {
            echo '_sr360_views[' . $view->product_id . '][' . $view->variation_id . '] = [';
            if (isset($view->m_gallery) && $view->m_gallery !== null) {
                $attachment_ids = maybe_unserialize($view->m_gallery);
                $image_urls = array_map(function ($id) {
                    return wp_get_attachment_url($id);
                }, $attachment_ids);
                $last_index = count($image_urls) - 1;
                $i = 0;

                foreach ($image_urls as $img_url) {
                    echo "'" . $img_url . "'";
                    if ($i < $last_index) {
                        echo ", ";
                    }
                    $i++;
                }
            } else {
                for ($frame = 1; $frame <= $view->images; $frame++) {
                    $formattedFrame = str_pad($frame, $view->lzero == '1' ? '2' : '1', '0', STR_PAD_LEFT);
                    echo "'" . $view->baseurl . $formattedFrame . "." . $view->ext . "'";
                    if ($frame < $view->images) {
                        echo ', ';
                    }
                }
            }
            echo ']; ';
        }
        echo '</script>';
//        echo '<pre>';
//        print_r($data);
//        exit;
        ?>
        <div id="sr-360-view-container">
            <div id="sr-360-view-popup">
                <div id="sr-360-view-canvas">
                </div>
                <span id="sr360_view_close_popup" title="close"></span>
                <span id="sr360_view_controls">
                    <span id="sr360_view_zoomer" title="click to zoom in/out"></span>
                    <span id="sr360_view_play" title="click to play/pause"></span>
                </span>
            </div>
        </div>
        <script>
            var sr360imageUrl = '<?= $settings['custom_360_icon_url']; ?>';
            var sr360imageWidth = '<?= $settings['icon_size_360']['active']; ?>px';
            var sr360position = '<?= $settings['icon_position_360']['active']; ?>';
            var sr360productId = <?= $product_id; ?>;
            var sr360variationId = <?= $variation_id; ?>;
        <?php
        $product_gallery = get_post_meta($product_id, '_product_image_gallery', true);
        $product_gallery_array = explode(',', $product_gallery);
        $sr_360_icon = intval(sanitize_option(_SR_360_GALLERY_ICON, get_option(_SR_360_GALLERY_ICON)));
        if ($settings['icon_position_360']['active'] === 'gallery_thumb_first' || $settings['icon_position_360']['active'] === 'gallery_thumb_last' || $settings['icon_position_360']['active'] === 'gallery'):
            if (array_search($sr_360_icon, $product_gallery_array) === false):
                array_push($product_gallery_array, $sr_360_icon);
                update_post_meta($product_id, '_product_image_gallery', implode(',', $product_gallery_array));
            endif;
            ?>
                window.addEventListener('load', function () {
                    var iconIdentifier = 'sr-attachment-icon-360_';
                    var galleryContainer = document.querySelector('.woocommerce-product-gallery');

                    function handleIconInteraction(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        // Assuming _trigger360Popup is defined and correctly handles the popup logic
                        _trigger360Popup(sr360productId, sr360variationId)
                    }

                    function checkAttributesAndBind(element) {
                        // Check if the element is an <img> with 'src' containing the identifier
                        if (element.tagName === 'IMG' && element.src.includes(iconIdentifier)) {
                            element.addEventListener('click', handleIconInteraction);
                            element.addEventListener('touchstart', handleIconInteraction);
                        }
                        // Check if the element is an <a> with 'href' containing the identifier
                        else if (element.tagName === 'A' && element.href.includes(iconIdentifier)) {
                            element.addEventListener('click', handleIconInteraction);
                            element.addEventListener('touchstart', handleIconInteraction);
                        }
                        // Check for any data-* attributes containing the identifier
                        else {
                            Array.from(element.attributes).forEach(attr => {
                                if (attr.name.startsWith('data-') && attr.value.includes(iconIdentifier)) {
                                    element.addEventListener('click', handleIconInteraction);
                                    element.addEventListener('touchstart', handleIconInteraction);
                                }
                            });
                        }
                    }

                    if (galleryContainer) {
                        // Iterates over all child elements within the gallery container
                        var allElements = galleryContainer.querySelectorAll('*');
                        allElements.forEach(element => {
                            checkAttributesAndBind(element);
                        });
                    }
                });
            <?php
        else:
            if (array_search($sr_360_icon, $product_gallery_array) !== false):
                $product_gallery_array = $this->remove_stack_element($product_gallery_array, $sr_360_icon);
                update_post_meta($product_id, '_product_image_gallery', implode(',', $product_gallery_array));
            endif;
            ?>
                document.addEventListener("DOMContentLoaded", function () {
                    !function e() {
                        if ("above_product_gallery" !== sr360position && "none" !== sr360position) {
                            var t = document.querySelector(".woocommerce-product-gallery");
                            if (t) {
                                var o = document.createElement("div");
                                o.id = "_sr360_icon_container", o.style.position = "absolute", o.style.zIndex = "100", function e(t, o) {
                                    switch (o) {
                                        case"over_product_top_left":
                                            t.style.top = "0", t.style.left = "0";
                                            break;
                                        case"over_product_top_right":
                                            t.style.top = "0", t.style.right = "0";
                                            break;
                                        case"over_product_bottom_left":
                                            t.style.bottom = "0", t.style.left = "0";
                                            break;
                                        case"over_product_bottom_right":
                                            t.style.bottom = "0", t.style.right = "0"
                                    }
                                }(o, sr360position);
                                var r = document.createElement("img");
                                r.id = "_sr360_icon", r.src = sr360imageUrl, r.style.width = sr360imageWidth, o.appendChild(r), t.style.position = "relative", t.appendChild(o), r.addEventListener("click", function () {
                                    _trigger360Popup(sr360productId, sr360variationId)
                                })
                            }
                        } else if ("above_product_gallery" === sr360position) {
                            var i = document.querySelector(".woocommerce-product-gallery"), o = document.createElement("div");
                            o.id = "_sr360_icon_container";
                            var n = document.createElement("img");
                            n.id = "_sr360_icon", n.src = sr360imageUrl, n.style.width = sr360imageWidth, o.appendChild(n), i.parentNode.insertBefore(o, i), n.addEventListener("click", function () {
                                _trigger360Popup(sr360productId, sr360variationId)
                            })
                        }
                    }()
                });
        <?php
        endif;
        ?>
            document.addEventListener("DOMContentLoaded", function () {
                jQuery(".variations_form").on("found_variation", function (e, t) {
                    sr360variationId = t.variation_id
                })
            });
            document.addEventListener('DOMContentLoaded', function () {
                var container = document.getElementById('sr-360-view-container');
                var closePopup = document.getElementById('sr360_view_close_popup');

                function closeThePopup() {
                    jQuery("#sr-360-view-canvas").sr360view('destroy');
                    container.style.display = 'none';
                }

                container.addEventListener('click', function (event) {
                    if (event.target === container) {
                        closeThePopup();
                    }
                });

                closePopup.addEventListener('click', function () {
                    closeThePopup();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeThePopup();
                    }
                });
            });

            function _trigger360Popup(pid, vid) {
                document.getElementById('sr-360-view-container').style.display = 'flex';
                var options = {
                    pid: pid,
                    vid: vid,
                    width: '<?= $settings['popup_width']['active'] . $popup_rule; ?>',
                    height: '<?= $settings['popup_height']['active'] . $popup_rule; ?>',
                    fullScreen: <?= $settings['popup_full'] ? 'true' : 'false'; ?>,
                    backgroundColor: '<?= $settings['popup_background']; ?>',
                    closeIcon: '<?= $settings['custom_close_icon_url']; ?>',
                    zoomIcon: '<?= sr360Icon::get_zoom_in_icon_url(); ?>',
                    playIcon: '<?= sr360Icon::get_play_icon_url(); ?>',
                    showZoomIcon: '<?= $settings['zoom_button'] ? 'block' : 'none'; ?>',
                    showPlayIcon: '<?= $settings['play_pause_button'] ? 'block' : 'none'; ?>',
                    closeIconPosition: '<?= $settings['icon_position_close']['active']; ?>',
                    closeIconWidth: '<?= $settings['icon_size_close']['active']; ?>px',
                    zoomIconWidth: '<?= $settings['zoom_button_size']['active']; ?>px',
                    playIconWidth: '<?= $settings['play_button_size']['active']; ?>px'
                };
                setupPopup(options);
            }
            function setupPopup(options) {
                var popup = document.getElementById('sr-360-view-popup');
                var screenWidth = window.innerWidth;
                var screenHeight = window.innerHeight;
                popup.style.backgroundColor = options.backgroundColor;
                document.querySelector('#sr360_view_close_popup').classList.add(options.closeIconPosition);
                document.querySelector('#sr360_view_close_popup').style.backgroundImage = `url(${options.closeIcon})`;
                document.querySelector('#sr360_view_zoomer').classList.remove('sr360_zoom_out');
                document.querySelector('#sr360_view_zoomer').style.backgroundImage = `url(${options.zoomIcon})`;
                document.querySelector('#sr360_view_zoomer').style.display = options.showZoomIcon;
                document.querySelector('#sr360_view_play').style.backgroundImage = `url(${options.playIcon})`;
                document.querySelector('#sr360_view_play').style.display = options.showPlayIcon;
                document.querySelector('#sr360_view_close_popup').style.width = options.closeIconWidth;
                document.querySelector('#sr360_view_zoomer').style.width = options.zoomIconWidth;
                document.querySelector('#sr360_view_play').style.width = options.playIconWidth;
                document.querySelector('#sr360_view_close_popup').style.height = options.closeIconWidth;
                document.querySelector('#sr360_view_zoomer').style.height = options.zoomIconWidth;
                document.querySelector('#sr360_view_play').style.height = options.playIconWidth;
                var popupWidth = options.width.endsWith('%') ? (screenWidth * parseInt(options.width) / 100) : parseInt(options.width);
                var popupHeight = options.height.endsWith('%') ? (screenHeight * parseInt(options.height) / 100) : parseInt(options.height);
                if (options.fullScreen || popupWidth >= screenWidth * 0.99 || popupHeight >= screenHeight * 0.99) {
                    popup.classList.add('full-screen');
                    popup.style.borderRadius = '0';
                    popupWidth = screenWidth;
                    popupHeight = screenHeight;
                } else {
                    popup.classList.remove('full-screen');
                    popup.style.borderRadius = '4px';
                    popup.style.width = `${popupWidth}px`;
                    popup.style.height = `${popupHeight}px`;
                }
                setTimeout(__360_canvas, 100, options.pid, options.vid, popupWidth, popupHeight);
            }
            function __360_canvas(pid, vid, width, height) {
                var pluginsData = '<?= $settings['rotation_control']['active']; ?>,progress,zoom'.split(',');
                var animation = <?= $settings['auto_rotate'] === true || $settings['auto_rotate'] === '1' ? 'true' : 'false'; ?>;
                var sense = <?= $settings['mouse_sensitivity']['active']; ?>;
                var rotation_reverse = <?= $settings['rotation_reverse'] === true || $settings['rotation_reverse'] === '1' ? 'true' : 'false'; ?>;
                if (rotation_reverse) {
                    sense = -sense;
                }
                var loop = <?= $settings['loop_auto_rotate'] === true || $settings['loop_auto_rotate'] === '1' ? 'true' : 'false'; ?>;
                var reverse = <?= $settings['auto_rotation_reverse'] === true || $settings['auto_rotation_reverse'] === '1' ? 'true' : 'false'; ?>;
                var animationSpeed = (200 - parseInt(<?= $settings['auto_rotation_speed']['active']; ?>));
                jQuery("#sr-360-view-canvas").sr360view({
                    source: _sr360_views[pid][vid],
                    sense: sense,
                    animate: animation,
                    loop: loop,
                    reverse: reverse,
                    frameTime: animationSpeed,
                    width: width,
                    height: height,
                    sizeMode: 'fit',
                    plugins: pluginsData,
                    zoomUseWheel: false,
                    zoomUseClick: false
                });
            }
            document.addEventListener('DOMContentLoaded', function () {
                var playpause = document.getElementById('sr360_view_play');
                var zoomer = document.getElementById('sr360_view_zoomer');
                zoomer.addEventListener("click", function () {
                    zoomer.classList.toggle('sr360_zoom_out');
                    jQuery("#sr-360-view-canvas").sr360view('api').toggleZoom();
                    jQuery("#sr-360-view-canvas").sr360view('api').stopAnimation();
                });
                playpause.addEventListener('click', function () {
                    jQuery("#sr-360-view-canvas").sr360view('api').toggleAnimation();
                });
            });
        </script>
        <?php
    }

}

new sr360Views();
