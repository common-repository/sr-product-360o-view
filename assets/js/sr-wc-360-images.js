jQuery(function ($) {
    var file_frame;
    var image_url = '';
    $(document).on("click", "#sr-product-360-view a.images-add", function (event) {
        event.preventDefault();
        if (file_frame) {
            file_frame.close()
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data("uploader-title"),
            button: {
                text: $(this).data("uploader-button-text"),
            },
            multiple: true
        });
        file_frame.on("select", function () {
            var listIndex = $("#sr-product-360-view-images-list li").index($("#sr-product-360-view-images-list li:last")),
                    selection = file_frame.state().get("selection");
            selection.map(function (attachment, i) {
                attachment = attachment.toJSON(), index = listIndex + (i + 1);
                image_url = attachment.url;
                $("#sr-product-360-view-images-list").append('<li><input type="hidden" name="sr_product_360_view_images[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + image_url + '"><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>')
            })
        });
        makeSortable();
        file_frame.open()
    });
    $(document).on('click', '#sr-product-360-view-images-list a.change-image', function (e) {

        e.preventDefault();

        var that = $(this);

        if (file_frame)
            file_frame.close();

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader-title'),
            button: {
                text: $(this).data('uploader-button-text'),
            },
            multiple: false
        });

        file_frame.on('select', function () {
            attachment = file_frame.state().get('selection').first().toJSON();

            that.parent().find('input:hidden').attr('value', attachment.id);
            image_url = attachment.url;
            that.parent().find('img.image-preview').attr('src', image_url);
        });

        file_frame.open();

    });
    function resetIndex() {
        $("#sr-product-360-view-images-list li").each(function (i) {
            $(this).find("input:hidden").attr("name", "sr_product_360_view_images[" + i + "]")
        })
    }

    function makeSortable() {
        $("#sr-product-360-view-images-list").sortable({
            opacity: 0.6,
            stop: function () {
                resetIndex();
            }
        })
    }
    $(document).on("click", "#sr-product-360-view a.remove-image", function (e) {
        e.preventDefault();
        $(this).parents("li").animate({
            opacity: 0
        }, 200, function () {
            $(this).remove();
            resetIndex();
        })
    });
    makeSortable();
});