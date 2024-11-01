document.addEventListener('DOMContentLoaded', function () {
    var responseP = document.getElementById('superrishi-ajax-response');
    var submitButton = document.getElementById('import-360-settings-save-button');
    submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        responseP.innerHTML = sr360ImportIndividual.default_message;
        submitButton.disabled = true;
        var button = document.createElement("button");
        button.textContent = "Refresh records? Page will be refreshed...";
        button.classList.add("button");
        button.style.display = 'block';
        button.style.marginTop = '20px';
        button.onclick = refreshPage;
        var formData = new FormData();
        formData.append('product_id', sr360Active['product_id']);
        formData.append('variation_id', sr360Active['variation_id']);
        formData.append('action', sr360ImportIndividual.action);
        formData.append('nonce', sr360ImportIndividual.nonce);
        fetch(sr360ImportIndividual.ajax_url, {
            method: 'POST',
            body: formData
        })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        responseP.innerHTML = data.error;
                        if (data.block !== undefined && data.block === 1) {
                            submitButton.removeAttribute('id');
                        }
                    } else {
                        responseP.innerHTML = data.summary;
                        if (data.summary.indexOf("Total images found:") !== -1) {
                            responseP.appendChild(button);
                        }
                    }
                })
                .catch(error => {
                    responseP.innerHTML = 'Error: ' + error;
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
    });
});

function refreshPage() {
    window.location.reload();
}