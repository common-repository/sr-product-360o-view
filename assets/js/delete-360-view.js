function sr360Delete360View() {
    var message = "Would you like to delete the 360 view for " + sr360Active['product_name'] + "?\nThis operation will not delete the 360 images!";
    if (confirm(message)) {
        var formData = new FormData();
        formData.append('product_id', sr360Active['product_id']);
        formData.append('variation_id', sr360Active['variation_id']);
        formData.append('action', sr360DeleteView.action);
        formData.append('nonce', sr360DeleteView.nonce);
        fetch(sr360DeleteView.ajax_url, {
            method: 'POST',
            body: formData
        })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                })
                .finally(() => {
                });
    }
}