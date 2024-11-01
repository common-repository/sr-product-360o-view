<?php
if (!defined('ABSPATH')):
    require_once '../access-check-wp.php';
endif;
require_once 'header.php';
$settings = $this->get_settings_data(array('license_key' => $this->get_license_key()));
if ($settings && isset($settings->settings)):
    $settings = $settings->settings;
    $updatedIconPositions = new stdClass();
    foreach ($settings->icon_position_360 as $key => $value) {
        if ($key === 'active') {
            $updatedIconPositions->gallery = 'As gallery thumbnail';
            if ($value === 'gallery_thumb_first' || $value === 'gallery_thumb_last') {
                $value = 'gallery';
            }
        }
        $updatedIconPositions->$key = $value;
    }
    $gallery_icon_id['one'] = sanitize_option(_SR_360_GALLERY_ICON, get_option(_SR_360_GALLERY_ICON));
    $gallery_icon_id['two'] = sanitize_option(_SR_360_GALLERY_ICON_SECOND, get_option(_SR_360_GALLERY_ICON_SECOND));
    foreach ($gallery_icon_id as $key => $value) {
        if (!$value) {
            $image_path = sr360Icon::get_gallery_thumb_icon_url($key);
            $image_name = 'new_' . $key;
            $attach_id = $this->sr_add_image_to_media_gallery($image_path, $image_name);
            if ($attach_id && $key === 'one') {
                update_option(_SR_360_GALLERY_ICON, $attach_id, 'no');
            } elseif ($attach_id) {
                update_option(_SR_360_GALLERY_ICON_SECOND, $attach_id, 'no');
            }
        }
    }
    ?>

    <div class="superrishi-setting-container">
        <div class="superrishi-setting-row">
            <div class="superrishi-setting-output">
                <h2 class="superrishi-title title-regular">Global Settings</h2>
            </div>
        </div>
        <div class="superrishi-setting-row">
            <div class="superrishi-setting-output">
                <div class="superrishi-form-container">
                    <form id="superrishi-global-settings" class="superrishi-settings-form" method="POST" action="<?= admin_url('admin-post.php'); ?>">
                        <input type="hidden" id="superrishi-action" name="action" value="<?= $this->_sr360_actions['sr360_save_settings']; ?>"/>
                        <input type="hidden" id="superrishi-settings-nonce" name="nonce" value="<?= wp_create_nonce($this->_nonce_strings['sr360_save_settings']); ?>"/>
                        <fieldset>
                            <legend>Animation</legend>
                            <label for="superrishi-setting-auto-rotate">
                                <input type="hidden" name="auto_rotate" value="0"/>
                                Auto Rotate On Popup <input type="checkbox" id="superrishi-setting-auto-rotate" name="auto_rotate" value="1" <?= $settings->auto_rotate ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-loop-auto-rotate">
                                <input type="hidden" name="loop_auto_rotate" value="0"/>
                                Continuous Auto Rotation <input type="checkbox" id="superrishi-setting-loop-auto-rotate" name="loop_auto_rotate" value="1" <?= $settings->loop_auto_rotate ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-auto-rotation-speed">
                                Auto Rotation Speed
                                <div class="range-container">
                                    <input id="superrishi-setting-auto-rotation-speed" type="range" name="auto_rotation_speed" min="<?= $settings->auto_rotation_speed->min; ?>" max="<?= $settings->auto_rotation_speed->max; ?>" value="<?= $settings->auto_rotation_speed->active; ?>" required />
                                    <span class="least-value">Slow</span>
                                    <span class="default-value default-animation-value" ontouchstart="default_animation_speed();" onclick="default_animation_speed();">Best</span>
                                    <span class="max-value">Fast</span>
                                </div>
                            </label>
                            <label for="superrishi-setting-auto-rotation-reverse">
                                <input type="hidden" name="auto_rotation_reverse" value="0"/>
                                Reverse Auto Rotation <input type="checkbox" id="superrishi-setting-auto-rotation-reverse" name="auto_rotation_reverse" value="1" <?= $settings->auto_rotation_reverse ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-play-pause-button">
                                <input type="hidden" name="play_pause_button" value="0"/>
                                Show Play/Pause Button <input type="checkbox" id="superrishi-setting-play-pause-button" name="play_pause_button" value="1" <?= $settings->play_pause_button ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-play-button-size">
                                Play/Pause Button Size <input type='number' min="<?= $settings->play_button_size->min; ?>" max="<?= $settings->play_button_size->max; ?>" id="superrishi-setting-play-button-size" name="play_button_size" value="<?= $settings->play_button_size->active; ?>" required />px
                            </label>
                            <label for="superrishi-setting-zoom-icon">
                                <input type="hidden" name="zoom_button" value="0"/>
                                Show Zoom Button <input type="checkbox" id="superrishi-setting-zoom-button" name="zoom_button" value="1" <?= $settings->zoom_button ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-zoom-button-size">
                                Zoom Button Size <input type='number' min="<?= $settings->zoom_button_size->min; ?>" max="<?= $settings->zoom_button_size->max; ?>" id="superrishi-setting-zoom-button-size" name="zoom_button_size" value="<?= $settings->zoom_button_size->active; ?>" required />px
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend>Rotation &amp; Popup</legend>
                            <label for="superrishi-setting-rotation-control">
                                Rotation Control
                                <select id="superrishi-setting-rotation-control" name="rotation_control" required>
                                    <?php
                                    $selected = '';
                                    $active_value = $settings->rotation_control->active;
                                    foreach ($settings->rotation_control as $value => $label):
                                        if ($value === 'active'):
                                            break;
                                        endif;
                                        if (!$selected && $value === $active_value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label for="superrishi-setting-rotation-reverse">
                                <input type="hidden" name="rotation_reverse" value="0"/>
                                Reverse Rotation <input type="checkbox" id="superrishi-setting-rotation-reverse" name="rotation_reverse" value="1" <?= $settings->rotation_reverse ? 'checked' : '' ?>/>
                            </label>
                            <label for="superrishi-setting-mouse-sensitivity">
                                Mouse Sensitivity
                                <div class="range-container">
                                    <input id="superrishi-setting-mouse-sensitivity" name="mouse_sensitivity"  type="range" min="<?= $settings->mouse_sensitivity->min; ?>" max="<?= $settings->mouse_sensitivity->max; ?>" value="<?= $settings->mouse_sensitivity->active; ?>" required />
                                    <span class="least-value">Normal</span>
                                    <span class="max-value">Extreme</span>
                                </div>
                            </label>
                            <label for="superrishi-setting-popup-background">
                                Popup Background Color <input type="color" id="superrishi-setting-popup-background" name="popup_background" value="<?= $settings->popup_background; ?>" required />
                            </label>
                            <label for="superrishi-setting-popup-size-full">
                                <input type="hidden" name="popup_full" value="0"/>
                                Full Screen Popup <input type="checkbox" id="superrishi-setting-popup-full" name="popup_full" value="1" <?= $settings->popup_full ? 'checked' : ''; ?> />
                                <span class="superrishi-para para-note">(will override Popup width &amp; height)</span>
                            </label>
                            <label for="superrishi-setting-popup-width">
                                Popup Width <input type='number' min="<?= $settings->popup_width->min; ?>" max="<?= $settings->popup_width->max; ?>" id="superrishi-setting-popup-width" name="popup_width" value="<?= $settings->popup_width->active; ?>" required />
                            </label>
                            <label for="superrishi-setting-popup-height">
                                Popup Height <input type='number' min="<?= $settings->popup_height->min; ?>" max="<?= $settings->popup_height->max; ?>" id="superrishi-setting-popup-height" name="popup_height" value="<?= $settings->popup_height->active; ?>" required />
                            </label>
                            <label for="superrishi-setting-popup-size-rule">
                                Popup Width/Height Rule
                                <select id="superrishi-setting-popup-rule" name="popup_rule">
                                    <?php
                                    $selected = '';
                                    $active_value = $settings->popup_rule->active;
                                    foreach ($settings->popup_rule as $value => $label):
                                        if ($value === 'active'):
                                            break;
                                        endif;
                                        if (!$selected && $value === $active_value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend>Icons</legend>
                            <label for="default-360-icon">
                                360 Icon <img id="default-360-icon" class="icon-thumb" src="<?= $settings->custom_360_icon_url; ?>"/>
                            </label>
                            <label for="superrishi-setting-custom-360-icon-url">
                                360 Icon URL <input type="text" id="superrishi-setting-custom-360-icon-url" name="custom_360_icon_url" value="<?= $settings->custom_360_icon_url; ?>" placeholder="https://exmaple.com/wp-content/uploads/2024/12/360-icon.png" required/>
                            </label>
                            <label for="superrishi-setting-360-icon-size">
                                360 Icon Size <input type='number' min="<?= $settings->icon_size_360->min; ?>" max="<?= $settings->icon_size_360->max; ?>" id="superrishi-setting-360-icon-size" name="icon_size_360" value="<?= $settings->icon_size_360->active; ?>" required />px
                            </label>
                            <label for="default-360-icon">
                                Close Icon <img id="default-close-icon" class="icon-thumb-small" src="<?= $settings->custom_close_icon_url; ?>"/>
                            </label>
                            <label for="superrishi-setting-custom-close-icon-url">
                                Close Icon URL <input type="text" id="superrishi-setting-custom-close-icon-url" name="custom_close_icon_url" value="<?= $settings->custom_close_icon_url; ?>" placeholder="https://exmaple.com/wp-content/uploads/2024/12/close-icon.png" required/>
                            </label>
                            <label for="superrishi-setting-close-icon-size">
                                Close Icon Size <input type='number' min="<?= $settings->icon_size_close->min; ?>" max="<?= $settings->icon_size_close->max; ?>" id="superrishi-setting-close-icon-size" name="icon_size_close" value="<?= $settings->icon_size_close->active; ?>" required />px
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend>Icon Positioning</legend>
                            <label for="superrishi-setting-360-icon-position">
                                360 Icon Position On Product Page
                                <select id="superrishi-setting-360-icon-position" name="icon_position_360">
                                    <?php
                                    $selected = '';
                                    $active_value = $updatedIconPositions->active;
                                    foreach ($updatedIconPositions as $value => $label):
                                        if ($value === 'active'):
                                            break;
                                        endif;
                                        if (!$selected && $value === $active_value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label for="superrishi-setting-close-icon-position">
                                Close Icon Position On 360 Popup
                                <select id="superrishi-setting-close-icon-position" name="icon_position_close">
                                    <?php
                                    $selected = '';
                                    $active_value = $settings->icon_position_close->active;
                                    foreach ($settings->icon_position_close as $value => $label):
                                        if ($value === 'active'):
                                            break;
                                        endif;
                                        if (!$selected && $value === $active_value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label for="superrishi-setting-zoom-play-icon-position">
                                Play/Pause Icon Position On 360 Popup
                                <select id="superrishi-setting-zoom-play-icon-position" name="icon_position_zoom_play">
                                    <?php
                                    $selected = '';
                                    $active_value = $settings->icon_position_zoom_play->active;
                                    foreach ($settings->icon_position_zoom_play as $value => $label):
                                        if ($value === 'active'):
                                            break;
                                        endif;
                                        if (!$selected && $value === $active_value) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                        </fieldset>
                        <fieldset style="border:0">
                            <input id="superrishi-setting-submit" name="action-for-request" class="button button-primary left" type="submit" value="save" />
                            <input id="superrishi-setting-reset" name="action-for-request" class="button button-secondary right" type="submit" value="reset to default" />
                        </fieldset>
                        <fieldset style="border:0">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function default_animation_speed() {
            document.getElementById("superrishi-setting-auto-rotation-speed").value = 160;
        }
        document.addEventListener('DOMContentLoaded', function () {
            var popupWidthInput = document.getElementById('superrishi-setting-popup-width');
            var popupHeightInput = document.getElementById('superrishi-setting-popup-height');
            var popupRuleSelect = document.getElementById('superrishi-setting-popup-rule');

            function validatePopupSize() {
                var width = parseInt(popupWidthInput.value);
                var height = parseInt(popupHeightInput.value);
                var rule = popupRuleSelect.value;

                if (rule === 'pr' && (width > 100 || height > 100)) {
                    alert('Popup width and height value cannot be more than 100 when percentage option is selected. Changing rule to pixel!');
                    popupRuleSelect.value = 'px';
                }
            }

            popupWidthInput.addEventListener('change', validatePopupSize);
            popupHeightInput.addEventListener('change', validatePopupSize);
            popupRuleSelect.addEventListener('change', validatePopupSize);
        });

        function showMessageFromURL() {
            var params = new URLSearchParams(window.location.search);
            var type = params.get('t');
            var message = params.get('m');

            if (message) {
                message = message.replace(/\+/g, ' ');

                var messageDiv = document.createElement('div');
                messageDiv.textContent = message;

                if (type === 'success') {
                    messageDiv.style.backgroundColor = '#d4edda';
                    messageDiv.style.color = '#155724';
                } else if (type === 'error') {
                    messageDiv.style.backgroundColor = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                }

                messageDiv.style.padding = '10px';
                messageDiv.style.marginBottom = '10px';
                messageDiv.style.borderRadius = '5px';

                var form = document.getElementById('superrishi-global-settings');
                if (form) {
                    form.parentNode.insertBefore(messageDiv, form);

                    setTimeout(function () {
                        messageDiv.style.display = 'none';
                    }, 10000);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', showMessageFromURL);

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var resetButton = document.getElementById('superrishi-setting-reset');
            if (resetButton) {
                resetButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    var userConfirmed = confirm('Are you sure you want to reset the settings?');
                    if (userConfirmed) {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = resetButton.name;
                        hiddenInput.value = resetButton.value;
                        resetButton.form.appendChild(hiddenInput);
                        resetButton.form.submit();
                    }
                });
            }
        });
    </script>

    <?php
else:
    $message = isset($settings->message) ? sanitize_text_field($settings->message) : 'Sorry, you have not activated your subscription license yet.';
    echo '<div style="background-color: rgb(248, 215, 218); color: rgb(114, 28, 36); padding: 10px; margin-bottom: 10px; border-radius: 5px;">' . $message . '</div>';
endif;
require_once 'footer.php';
