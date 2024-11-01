<div id="sr360Settings" class="tabcontent">
    <div class="superrishi-setting-container">
        <div class="superrishi-setting-row">
            <div class="superrishi-setting-output">
                <h2 class="superrishi-title title-regular"><span class="superrishi-dynamic-title"></span> : <?= __('Settings', ''); ?></h2>
            </div>
            <div class="superrishi-setting-output">
                <p id="individual-settings-response" class="superrishi-para"></p>
            </div>
        </div>
        <form id="superrishi-individual-settings" class="superrishi-settings-form" method="POST" action="#" onsubmit="return sr360SaveIndividualSettings();">
            <div class="responsive-table">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend>Animation</legend>
                                    <label for="superrishi-setting-auto-rotate">
                                        Auto Rotate On Popup <input type="checkbox" id="superrishi-setting-auto-rotate" name="auto_rotate" value="1" />
                                    </label>
                                    <label for="superrishi-setting-loop-auto-rotate">
                                        Continuous Auto Rotation <input type="checkbox" id="superrishi-setting-loop-auto-rotate" name="loop_auto_rotate" value="1" />
                                    </label>
                                    <label for="superrishi-setting-auto-rotation-speed">
                                        Auto Rotation Speed
                                        <div class="range-container">
                                            <input id="superrishi-setting-auto-rotation-speed" type="range" name="auto_rotation_speed" min="" max="" value="" required />
                                            <span class="least-value">Slow</span>
                                            <span class="default-value default-animation-value" ontouchstart="default_animation_speed();" onclick="default_animation_speed();">Best</span>
                                            <span class="max-value">Fast</span>
                                        </div>
                                    </label>
                                    <label for="superrishi-setting-auto-rotation-reverse">
                                        Reverse Auto Rotation <input type="checkbox" id="superrishi-setting-auto-rotation-reverse" name="auto_rotation_reverse" value="1" />
                                    </label>
                                    <label for="superrishi-setting-play-pause-button">
                                        Show Play/Pause Button <input type="checkbox" id="superrishi-setting-play-pause-button" name="play_pause_button" value="1" />
                                    </label>
                                    <label for="superrishi-setting-play-button-size">
                                        Play/Pause Button Size <input type='number' min="" max="" id="superrishi-setting-play-button-size" name="play_button_size" value="" required />px
                                    </label>
                                    <label for="superrishi-setting-zoom-icon">
                                        Show Zoom Button <input type="checkbox" id="superrishi-setting-zoom-button" name="zoom_button" value="1" />
                                    </label>
                                    <label for="superrishi-setting-zoom-button-size">
                                        Zoom Button Size <input type='number' min="" max="" id="superrishi-setting-zoom-button-size" name="zoom_button_size" value="" required />px
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend>Rotation &amp; Popup</legend>
                                    <label for="superrishi-setting-rotation-control">
                                        Rotation Control
                                        <select id="superrishi-setting-rotation-control" name="rotation_control" required>
                                        </select>
                                    </label>
                                    <label for="superrishi-setting-rotation-reverse">
                                        Reverse Rotation <input type="checkbox" id="superrishi-setting-rotation-reverse" name="rotation_reverse" value="1" />
                                    </label>
                                    <label for="superrishi-setting-mouse-sensitivity">
                                        Mouse Sensitivity
                                        <div class="range-container">
                                            <input id="superrishi-setting-mouse-sensitivity" name="mouse_sensitivity" type="range" min="" max="" value="" required />
                                            <span class="least-value">Normal</span>
                                            <span class="max-value">Extreme</span>
                                        </div>
                                    </label>
                                    <label for="superrishi-setting-popup-background">
                                        Popup Background Color <input type="color" id="superrishi-setting-popup-background" name="popup_background" value="" required />
                                    </label>
                                    <label for="superrishi-setting-popup-size-full">
                                        Full Screen Popup <input type="checkbox" id="superrishi-setting-popup-full" name="popup_full" value="1" />
                                        <span class="superrishi-para para-note">(will override Popup width &amp; height)</span>
                                    </label>
                                    <label for="superrishi-setting-popup-width">
                                        Popup Width <input type='number' min="" max="" id="superrishi-setting-popup-width" name="popup_width" value="" required />
                                    </label>
                                    <label for="superrishi-setting-popup-height">
                                        Popup Height <input type='number' min="" max="" id="superrishi-setting-popup-height" name="popup_height" value="" required />
                                    </label>
                                    <label for="superrishi-setting-popup-size-rule">
                                        Popup Width/Height Rule
                                        <select id="superrishi-setting-popup-rule" name="popup_rule">
                                        </select>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend>Icons</legend>
                                    <label for="default-360-icon">
                                        360 Icon <img id="default-360-icon" class="icon-thumb" src="" />
                                    </label>
                                    <label for="superrishi-setting-custom-360-icon-url">
                                        360 Icon URL <input type="text" id="superrishi-setting-custom-360-icon-url" name="custom_360_icon_url" value="" placeholder="https://exmaple.com/wp-content/uploads/2024/12/360-icon.png" required />
                                    </label>
                                    <label for="superrishi-setting-360-icon-size">
                                        360 Icon Size <input type='number' min="" max="" id="superrishi-setting-360-icon-size" name="icon_size_360" value="" required />px
                                    </label>
                                    <label for="default-360-icon">
                                        Close Icon <img id="default-close-icon" class="icon-thumb-small" src="" />
                                    </label>
                                    <label for="superrishi-setting-custom-close-icon-url">
                                        Close Icon URL <input type="text" id="superrishi-setting-custom-close-icon-url" name="custom_close_icon_url" value="" placeholder="https://exmaple.com/wp-content/uploads/2024/12/close-icon.png" required />
                                    </label>
                                    <label for="superrishi-setting-close-icon-size">
                                        Close Icon Size <input type='number' min="" max="" id="superrishi-setting-close-icon-size" name="icon_size_close" value="" required />px
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend>Icon Positioning</legend>
                                    <label for="superrishi-setting-360-icon-position">
                                        360 Icon Position On Product Page
                                        <select id="superrishi-setting-360-icon-position" name="icon_position_360">
                                        </select>
                                    </label>
                                    <label for="superrishi-setting-close-icon-position">
                                        Close Icon Position On 360 Popup
                                        <select id="superrishi-setting-close-icon-position" name="icon_position_close">
                                        </select>
                                    </label>
                                    <label for="superrishi-setting-zoom-play-icon-position">
                                        Play/Pause Icon Position On 360 Popup
                                        <select id="superrishi-setting-zoom-play-icon-position" name="icon_position_zoom_play">
                                        </select>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <fieldset style="border:0">
                                    <input id="superrishi-setting-submit" name="action-for-request" class="button button-primary left" type="submit" value="save" />
                                </fieldset>
                                <p id="individual-settings-response-bottom" class="superrishi-para"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
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