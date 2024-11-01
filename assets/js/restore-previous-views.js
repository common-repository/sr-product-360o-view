document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('superrishi-restore-views');
    var responseP = document.getElementById('superrishi-ajax-response');
    var submitButton = document.getElementById('restore-previous-views-button');
    var currentLog = document.getElementById('current-logs-flow');
    var logContainer = document.getElementById('restoreLogs');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        responseP.innerHTML = sr360RestorePreviousViews.default_message;
        submitButton.disabled = true;

        jQuery("#sr360-download-import-log").css({'opacity': .2, 'pointer-events': 'none'});
        logContainer.style.visibility = 'visible';
        currentLog.innerHTML = '';

        // Create an array to hold all the fetch promises
        var fetchPromises = sr360ProductsToRestore.map(function (productID) {
            var formData = new FormData(form);
            formData.append('action', sr360RestorePreviousViews.action);
            formData.append('nonce', sr360RestorePreviousViews.nonce);
            formData.append('product_id', productID);
            formData.append('meta_key', sr360ViewsMetaKey);

            // Return the fetch promise
            return fetch(sr360RestorePreviousViews.ajax_url, {
                method: 'POST',
                body: formData
            })
                    .then(response => response.json())
                    .then(data => {
                        currentLog.innerHTML += '<li>' + data.message + '</li>';
                    })
                    .catch(error => {
                        currentLog.innerHTML += 'Error for product ' + productID + ': ' + error + '<br/>';
                    });
        });

        // Use Promise.all to wait for all fetch requests to complete
        Promise.all(fetchPromises).then(() => {
            submitButton.disabled = false;
            responseP.innerHTML = '';
            jQuery("#sr360-download-import-log").css({'opacity': 1, 'pointer-events': 'all'});
        });
    });
});

document.getElementById('sr360-download-import-log').addEventListener('click', function () {
    var logContent = document.getElementById('current-logs-flow').innerText;
    var blob = new Blob([logContent], {type: 'text/plain'});
    var downloadLink = document.createElement('a');
    downloadLink.href = window.URL.createObjectURL(blob);
    downloadLink.download = 'restore_log.txt';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
});
