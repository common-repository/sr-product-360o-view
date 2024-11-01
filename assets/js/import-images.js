document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('superrishi-import-images');
    var responseP = document.getElementById('superrishi-ajax-response');
    var submitButton = document.getElementById('import-360-settings-save-button');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        responseP.innerHTML = '';
        submitButton.disabled = true;
        var formData = new FormData(form);
        formData.append('action', sr360ImportImages.action);
        formData.append('nonce', sr360ImportImages.nonce);
        fetch(sr360ImportImages.ajax_url, {
            method: 'POST',
            body: formData
        })
                .then(response => response.json())
                .then(data => {
                    responseP.innerHTML = data.message;
                    if (data.code === 'process_import') {
                        setTimeout(runsr360ImportProcess, 15, data.data);
                    }
                })
                .catch(error => {
                    responseP.innerHTML = 'Error: ' + error;
                })
                .finally(() => {
                    setTimeout(srScrollToBottom, 100);
                    submitButton.disabled = false;
                });
    });
});

function runsr360ImportProcess(data) {
    var unfiltered = parseInt(data.update_existing);
    delete data.update_existing;
    var submitButton = document.getElementById('import-360-settings-save-button');
    submitButton.disabled = true;
    jQuery("#sr360-download-import-log").css({'opacity': .2, 'pointer-events': 'none'});
    var product_ids = sr360ImportImages.filtered_product_ids;
    if (unfiltered === 1) {
        product_ids = sr360ImportImages.all_product_ids;
    }
    setTimeout(sr360Import, 500, product_ids, data);
}

async function sr360Import(product_ids, data) {
    var currentLog = jQuery('#current-logs-flow');
    var submitButton = document.getElementById('import-360-settings-save-button');
    var responseP = document.getElementById('superrishi-ajax-response');
    jQuery(".live_logs_printing").show(500);
    var current_log = '';
    currentLog.html(current_log);
    if (typeof product_ids === 'object' && Object.keys(product_ids).length > 0) {
        for (var key in product_ids) {
            if (product_ids.hasOwnProperty(key)) {
                var ids = product_ids[key];

                if (Array.isArray(ids)) {
                    for (var i = 0; i < ids.length; i++) {
                        await processProduct(ids[i], key, currentLog, data);
                    }
                } else {
                    await processProduct(ids, key, currentLog, data);
                }
            }
        }
    } else {
        currentLog.html("<li><i>No products found to import 360&deg; images for.</i></li>");
    }
    responseP.innerHTML = '';
    submitButton.disabled = false;
    jQuery("#sr360-download-import-log").css({'opacity': 1, 'pointer-events': 'all'});
}

async function processProduct(id, key, currentLog, data) {
    var data = {
        action: sr360ImportImages.action_1,
        nonce: sr360ImportImages.nonce_1,
        product_id: id,
        key: key,
        data: data
    };
    var current_log = '';

    try {
        var jResponse = await postData(sr360ImportImages.ajax_url, data);
        if (key == id) {
            current_log = "<li><i>Starting import for product_id:" + id + "</i><br/>";
        } else {
            current_log = "<li><i>Starting import for product_id:" + key + " &amp; variation_id:" + id + "</i><br/>";
        }
        if (jResponse.error !== undefined) {
            current_log += '<i>' + jResponse.error + '</i></li>';
        } else {
            current_log += '<i>' + jResponse.summary.replace(/\n/g, '') + '</i></li>';
        }
    } catch (error) {
        current_log = '<li><i>' + error + '</i></li>';
    }
    currentLog.prepend(current_log);
}


function srScrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}

function postData(url, data) {
    return new Promise(function (resolve, reject) {
        jQuery.ajax({
            type: "POST",
            url: url,
            data: data,
            success: resolve,
            error: reject
        });
    });
}

document.getElementById('sr360-download-import-log').addEventListener('click', function () {
    var logContent = document.getElementById('current-logs-flow').innerText;
    var blob = new Blob([logContent], {type: 'text/plain'});
    var downloadLink = document.createElement('a');
    downloadLink.href = window.URL.createObjectURL(blob);
    downloadLink.download = 'import_log.txt';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
});
