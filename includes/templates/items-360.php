<?php
if (!defined('ABSPATH')) :
    require_once '../access-check-wp.php';
endif;

require_once 'header.php';
$item_360 = 1;
$product_ids = $this->get_products_variations_ids();
?>
<!-- Admin Page Table -->
<h4 id="total-items-360"></h4>
<div class="product-table">
    <?php
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>S.NO</th>';
    echo '<th>Name</th>';
    echo '<th>ID</th>';
    echo '<th>SKU</th>';
    echo '<th>360&deg;</th>';
    echo '<th>Edit</th>';
    echo '<th>Delete</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($product_ids as $key => $value) {
        if (is_array($value)) {
            // Handle product variations
            $omit = true;
            foreach ($value as $variation_id) {
                if ($omit) {
                    $product = wc_get_product($key);
                    if ($product) {
                        $name = $product->get_name();
                        $sku = $product->get_sku();
                        $has_360_images = get_post_meta($key, _SR_360_PRODUCT, true);
                        $has_360 = $has_360_images ? '<span class="text-center success">Yes</span>' : '<span class="text-center error">No</span>';

                        echo '<tr>';
                        echo '<td>' . $item_360++ . '</td>';
                        echo '<td><a class="help-link" href="' . get_the_permalink($key) . '" target="_blank">' . esc_html($name) . ' &nearr;</a></td>';
                        echo '<td>' . esc_html($key) . '</td>';
                        echo '<td>' . esc_html($sku) . '</td>';
                        echo '<td>' . $has_360 . '</td>';
                        echo '<td><a href="#" class="edit-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($key) . '" data-variation-id="0">Edit</a></td>';
                        echo $has_360_images ? '<td><a href="#" class="delete-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($key) . '" data-variation-id="0">Delete</a></td>' : '<td>-</td>';
                        echo '</tr>';
                    }
                    $omit = false;
                    continue;
                }
                $variation = wc_get_product($variation_id);
                if ($variation) {
                    $name = $variation->get_name();
                    $sku = $variation->get_sku();
                    $has_360_images = get_post_meta($variation_id, _SR_360_PRODUCT, true);
                    $has_360 = $has_360_images ? '<span class="text-center success">Yes</span>' : '<span class="text-center error">No</span>';

                    echo '<tr>';
                    echo '<td>' . $item_360++ . '</td>';
                    echo '<td><a class="help-link" href="' . get_the_permalink($variation_id) . '" target="_blank">' . esc_html($name) . ' &nearr;</a></td>';
                    echo '<td>' . esc_html($variation_id) . '</td>';
                    echo '<td>' . esc_html($sku) . '</td>';
                    echo '<td>' . $has_360 . '</td>';
                    echo '<td><a href="#" class="edit-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($key) . '" data-variation-id="' . esc_attr($variation_id) . '">Edit</a></td>';
                    echo $has_360_images ? '<td><a href="#" class="delete-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($key) . '" data-variation-id="' . esc_attr($variation_id) . '">Delete</a></td>' : '<td>-</td>';
                    echo '</tr>';
                }
            }
        } else {
            // Handle simple products
            $product = wc_get_product($value);
            if ($product) {
                $name = $product->get_name();
                $sku = $product->get_sku();
                $has_360_images = get_post_meta($value, _SR_360_PRODUCT, true);
                $has_360 = $has_360_images ? '<span class="text-center success">Yes</span>' : '<span class="text-center error">No</span>';

                echo '<tr>';
                echo '<td>' . $item_360++ . '</td>';
                echo '<td><a class="help-link" href="' . get_the_permalink($value) . '" target="_blank">' . esc_html($name) . ' &nearr;</a></td>';
                echo '<td>' . esc_html($value) . '</td>';
                echo '<td>' . esc_html($sku) . '</td>';
                echo '<td>' . $has_360 . '</td>';
                echo '<td><a href="#" class="edit-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($value) . '" data-variation-id="0">Edit</a></td>';
                echo $has_360_images ? '<td><a href="#" class="delete-product" data-product-name="' . $name . '" data-product-id="' . esc_attr($value) . '" data-variation-id="0">Delete</a></td>' : '<td>-</td>';
                echo '</tr>';
            }
        }
    }

    echo '</tbody>';
    echo '</table>';
    ?>
</div>

<!-- Edit Popup Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" title="close">&times;</span>
        <div class="tab">
            <button class="tablinks sr-tab-shortcodes" onclick="sr360OpenTab(event, 'sr360Shortcodes')">Shortcodes</button>
            <button class="tablinks" onclick="sr360OpenTab(event, 'sr360ImportImages')">Import Images</button>
            <button class="tablinks" onclick="sr360OpenTab(event, 'sr360Settings')">Settings</button>
        </div>

        <?php
        require_once 'items-360-settings.php';
        require_once 'items-360-import.php';
        require_once 'items-360-shortcodes.php';
        ?>
    </div>
</div>
<script type="text/javascript">
    var sr360Active = [];
    sr360Active['product_name'] = '';
    sr360Active['product_id'] = 0;
    sr360Active['variation_id'] = 0;
    sr360Active['completed'] = false;
    var filter360Html = '<label style="margin-left:25px;">Filter 360&deg; <select id="filter-360deg" class="filter-360deg"><option value="">All</option><option value="Yes">Yes</option><option value="No">No</option></select></label>';
    jQuery(document).ready(function ($) {
        var table = $('.product-table table').DataTable({
            "pageLength": 25,
            "drawCallback": function (settings) {
                var api = this.api();
                var count = api.rows({
                    search: 'applied'
                }).count();
                $('#total-items-360').html('All Items (Simple + Variation): ' + count);
            }
        });
        // Insert the filter after the "Show entries" dropdown
        $(filter360Html).appendTo(".dataTables_length");

        // Custom filter for 360deg column
        $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var filterValue = $('#filter-360deg').val();
                    var columnValue = data[4]; // Index of the 360deg column

                    if (filterValue === "" || columnValue === filterValue) {
                        return true;
                    }
                    return false;
                }
        );

        // Event listener for the dropdown filter
        $('#filter-360deg').on('change', function () {
            table.draw();
        });
        $(document).on('click', '.edit-product', function (event) {
            $('#editModal').show();
            sr360Active['product_name'] = this.getAttribute('data-product-name');
            sr360Active['product_id'] = this.getAttribute('data-product-id');
            sr360Active['variation_id'] = this.getAttribute('data-variation-id');
            sr360Active['completed'] = false;
            sr360OpenTab(event, 'sr360Shortcodes');
            $('.replace_product_id').text('product_id="' + sr360Active['product_id'] + '"');
            if (parseInt(sr360Active['variation_id']) > 0) {
                $('.replace_variation_id').text(' variation_id="' + sr360Active['variation_id'] + '"');
            } else {
                $('.replace_variation_id').text('');
            }
            $('.sr-tab-shortcodes').addClass('active');
            $('.superrishi-dynamic-title').text(sr360Active['product_name']);
        });
        $(document).on('click', '.delete-product', function (event) {
            $('#editModal').hide();
            sr360Active['product_name'] = this.getAttribute('data-product-name');
            sr360Active['product_id'] = this.getAttribute('data-product-id');
            sr360Active['variation_id'] = this.getAttribute('data-variation-id');
            setTimeout(sr360Delete360View, 100);
        });
    });

    function sr360OpenTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName('tabcontent');
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = 'none';
        }
        tablinks = document.getElementsByClassName('tablinks');
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
        if (tabName === 'sr360Settings') {
            if (sr360Active['completed'] === false) {
                document.getElementById('superrishi-individual-settings').style.visibility = 'collapse';
                setFormDisabledState('superrishi-individual-settings', true);
                setTimeout(sr360LoadSettings, 50);
            }
        } else if (tabName === 'sr360ImportImages') {
            document.getElementById('superrishi-ajax-response').innerHTML = '';
        }
    }

    function sr360LoadSettings() {
        setTimeout(sr360IndividualSettings, 50);
    }

    var modal = document.getElementById('editModal');

    var span = document.getElementsByClassName("close")[0];

    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<?php
require_once 'footer.php';
