const licenseActivateForm = document.getElementById('superrishi-license-activation');
const linkPopupContainer = document.getElementById('superrishi-domain-link-popup');
const responsePopupContainer = document.getElementById('superrishi-response-popup');
const elementToChange = responsePopupContainer.querySelector('.superrishi-popup-message');
const cancelLinkDomain = document.getElementById('cancel-link-domain');
const acceptLinkDomain = document.getElementById('accept-link-domain');
const closeResponsePopup = document.getElementById('close-response-popup');
licenseActivateForm.addEventListener('submit', function (event) {
    event.preventDefault();
    linkPopupContainer.classList.remove('blur-no-click');
    linkPopupContainer.style.display = 'block';
});
acceptLinkDomain.addEventListener('click', function (event) {
    if (event.target === acceptLinkDomain) {
        linkPopupContainer.classList.add('blur-no-click');
        setTimeout(_sr_activate_subscription, 100);
    }
});
cancelLinkDomain.addEventListener('click', function (event) {
    if (event.target === cancelLinkDomain) {
        linkPopupContainer.style.display = 'none';
    }
});
linkPopupContainer.addEventListener('click', function (event) {
    if (event.target === linkPopupContainer) {
        linkPopupContainer.style.display = 'none';
    }
});
responsePopupContainer.addEventListener('click', function (event) {
    if (event.target === responsePopupContainer) {
        responsePopupContainer.style.display = 'none';
    }
});
closeResponsePopup.addEventListener('click', function (event) {
    if (event.target === closeResponsePopup) {
        responsePopupContainer.style.display = 'none';
    }
});

function _sr_activate_subscription() {

    var data = {
        'action': sr360licenseFormObj.action,
        'nonce': sr360licenseFormObj.nonce,
        'security': jQuery('#superrishi-security').val(),
        'license_key': jQuery('#superrishi-license-key').val(),
        'secret': jQuery('#superrishi-secret').val()
    };
    jQuery.ajax({
        url: sr360licenseFormObj.ajax_url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (response) {
            elementToChange.innerHTML = response;
            linkPopupContainer.style.display = 'none';
            responsePopupContainer.style.display = 'block';
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (errorThrown && errorThrown !== undefined) {
                alert('HTTP Error: ' + errorThrown);
            }
            linkPopupContainer.style.display = 'none';
        }
    });
}
