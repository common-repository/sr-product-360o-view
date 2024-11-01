function sr360IndividualSettings() {
    var form = document.getElementById('superrishi-individual-settings');
    var messageDisp = document.getElementById('individual-settings-response');
    messageDisp.innerHTML = sr360Individual.default_message;
    document.getElementById('individual-settings-response-bottom').innerHTML = '';
    var formData = new FormData();
    formData.append('action', sr360Individual.action);
    formData.append('nonce', sr360Individual.nonce);
    formData.append('product_id', sr360Active['product_id']);
    formData.append('variation_id', sr360Active['variation_id']);
    fetch(sr360Individual.ajax_url, {
        method: 'POST',
        body: formData
    })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    messageDisp.innerHTML = data.error;
                    sr360Active['completed'] = true;
                } else {
                    messageDisp.innerHTML = '';
                    form.style.visibility = 'visible';
                    sr360Active['completed'] = true;
                    setTimeout(sr360PopulateForm, 50, data);
                }
            })
            .catch(error => {
                messageDisp.innerHTML = 'Error: ' + error;
            })
            .finally(() => {
            });
}

// Function to set input values
function setInputValue(selector, value, min, max) {
    var input = document.querySelector(selector);
    if (input) {
        input.value = value;
        if (min !== undefined)
            input.min = min;
        if (max !== undefined)
            input.max = max;
    }
}

// Function to set checkbox values
function setCheckboxValue(selector, value) {
    if (value === true || value === false) {
        document.querySelector(selector).checked = value
    } else {
        document.querySelector(selector).checked = parseInt(value);
    }
}

// Function to populate select options
function populateSelectOptions(selector, options, activeValue) {
    var select = document.querySelector(selector);
    if (select) {
        select.innerHTML = '';
        for (var key in options) {
            if (options.hasOwnProperty(key) && key !== 'active') {
                var option = document.createElement('option');
                option.value = key;
                option.innerHTML = options[key];
                option.selected = key === activeValue;
                select.appendChild(option);
            }
        }
    }
}

function sr360PopulateForm(settings) {

    // Populate checkboxes
    setCheckboxValue('#superrishi-setting-auto-rotate', settings.auto_rotate);
    setCheckboxValue('#superrishi-setting-loop-auto-rotate', settings.loop_auto_rotate);
    setCheckboxValue('#superrishi-setting-auto-rotation-reverse', settings.auto_rotation_reverse);
    setCheckboxValue('#superrishi-setting-play-pause-button', settings.play_pause_button);
    setCheckboxValue('#superrishi-setting-zoom-button', settings.zoom_button);
    setCheckboxValue('#superrishi-setting-rotation-reverse', settings.rotation_reverse);
    setCheckboxValue('#superrishi-setting-popup-full', settings.popup_full);

    // Populate input ranges and numbers
    setInputValue('#superrishi-setting-auto-rotation-speed', settings.auto_rotation_speed.active, settings.auto_rotation_speed.min, settings.auto_rotation_speed.max);
    setInputValue('#superrishi-setting-auto-rotation-speed', settings.auto_rotation_speed.active, settings.auto_rotation_speed.min, settings.auto_rotation_speed.max);
    setInputValue('#superrishi-setting-play-button-size', settings.play_button_size.active, settings.play_button_size.min, settings.play_button_size.max);
    setInputValue('#superrishi-setting-zoom-button-size', settings.zoom_button_size.active, settings.zoom_button_size.min, settings.zoom_button_size.max);
    setInputValue('#superrishi-setting-mouse-sensitivity', settings.mouse_sensitivity.active, settings.mouse_sensitivity.min, settings.mouse_sensitivity.max);
    setInputValue('#superrishi-setting-mouse-sensitivity', settings.mouse_sensitivity.active, settings.mouse_sensitivity.min, settings.mouse_sensitivity.max);
    setInputValue('#superrishi-setting-popup-background', settings.popup_background);
    setInputValue('#superrishi-setting-popup-background', settings.popup_background);
    setInputValue('#superrishi-setting-popup-width', settings.popup_width.active, settings.popup_width.min, settings.popup_width.max);
    setInputValue('#superrishi-setting-popup-height', settings.popup_height.active, settings.popup_height.min, settings.popup_height.max);
    setInputValue('#superrishi-setting-custom-360-icon-url', settings.custom_360_icon_url);
    setInputValue('#superrishi-setting-360-icon-size', settings.icon_size_360.active, settings.icon_size_360.min, settings.icon_size_360.max);
    setInputValue('#superrishi-setting-custom-close-icon-url', settings.custom_close_icon_url);
    setInputValue('#superrishi-setting-close-icon-size', settings.icon_size_close.active, settings.icon_size_close.min, settings.icon_size_close.max);

    // Populate select options
    populateSelectOptions('#superrishi-setting-rotation-control', settings.rotation_control, settings.rotation_control.active);
    populateSelectOptions('#superrishi-setting-popup-rule', settings.popup_rule, settings.popup_rule.active);

    settings.icon_position_360.gallery = 'As gallery thumbnail';
    if (settings.icon_position_360.active === 'gallery_thumb_first' || settings.icon_position_360.active === 'gallery_thumb_last') {
        settings.icon_position_360.active = 'gallery';
    }

    populateSelectOptions('#superrishi-setting-360-icon-position', settings.icon_position_360, settings.icon_position_360.active);
    populateSelectOptions('#superrishi-setting-close-icon-position', settings.icon_position_close, settings.icon_position_close.active);
    populateSelectOptions('#superrishi-setting-zoom-play-icon-position', settings.icon_position_zoom_play, settings.icon_position_zoom_play.active);
    setFormDisabledState('superrishi-individual-settings', false);

    // Populate images
    document.getElementById('default-360-icon').src = settings.custom_360_icon_url;
    document.getElementById('default-close-icon').src = settings.custom_close_icon_url;
}

function setFormDisabledState(formId, isDisabled) {
    var form = document.getElementById(formId);
    if (form) {
        var elements = form.elements;
        for (var i = 0; i < elements.length; i++) {
            elements[i].disabled = isDisabled;
        }
    }
}

function sr360SaveIndividualSettings() {
    if (sr360Active['product_id'] !== 0 && sr360Active['product_id'] !== undefined) {
        var formElement = document.getElementById('superrishi-individual-settings');
        setFormDisabledState('superrishi-individual-settings', true);
        var messageDisp = document.getElementById('individual-settings-response-bottom');
        messageDisp.innerHTML = sr360Individual.default_message;

        // Manually collect the form data
        var formData = new URLSearchParams();
        Array.from(formElement.elements).forEach(function (element) {
            // Check if the element is a checkbox
            if (element.type === 'checkbox') {
                // For checkboxes, use 'checked' property instead of 'value'
                formData.append(element.name, element.checked ? '1' : '0');
            } else if (element.name && element.value) {
                // For other elements, use their 'value'
                formData.append(element.name, element.value);
            }
        });

        // Append additional data
        formData.append('action', sr360Individual.action_1);
        formData.append('nonce', sr360Individual.nonce_1);
        formData.append('product_id', sr360Active['product_id']);
        formData.append('variation_id', sr360Active['variation_id']);

        // Fetch request
        fetch(sr360Individual.ajax_url, {
            method: 'POST',
            body: formData
        })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        messageDisp.innerHTML = '<span class="error">' + data.error + '</span>';
                    } else {
                        messageDisp.innerHTML = '<span class="success">' + data.success + '</span>';
                    }
                })
                .catch(error => {
                    messageDisp.innerHTML = 'Error: ' + error;
                })
                .finally(() => {
                    setFormDisabledState('superrishi-individual-settings', false);
                });
    }
    return false;
}

