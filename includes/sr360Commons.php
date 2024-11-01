<?php

require_once 'access-check-wp.php';

class sr360Commons {

	protected $_sr360_actions = array(
		'sr360_save_settings' => 'sr360_save_settings'
	);
	protected $_nonce_strings = array(
		'sr360_save_settings' => 'sr360-save-settings'
	);

	protected function get_domain_name() {
		return parse_url(site_url(), PHP_URL_HOST);
	}

	protected function get_license_key() {
		return sanitize_option(_SR_360_LICENSE_KEY, get_option(_SR_360_LICENSE_KEY));
	}

	protected function get_license_secret() {
		return sanitize_option(_SR_360_SECRET, get_option(_SR_360_SECRET));
	}

	protected function get_access_token() {
		return sanitize_option(_SR_360_TOKEN, get_option(_SR_360_TOKEN));
	}

	protected function get_products_variations_ids($filtered = false) {
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => array('publish', 'trash'),
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => array('simple', 'variable'),
				),
			),
		);

		if ($filtered) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key' => _SR_360_PRODUCT,
					'value' => '1',
					'compare' => '!='
				),
				array(
					'key' => _SR_360_PRODUCT,
					'compare' => 'NOT EXISTS'
				)
			);
		}

		$products_query = new WP_Query($args);
		$product_ids = $products_query->posts;

		$products_with_variations = array();

		foreach ($product_ids as $product_id) {
			$product = wc_get_product($product_id);

			if ($product->is_type('variable')) {
				$variation_ids = $product->get_children(); // Get variations
				$filtered_variations = array();

				foreach ($variation_ids as $variation_id) {
					if ($filtered) {
						$has_sr360_images = get_post_meta($variation_id, _SR_360_PRODUCT, true);
						if ($has_sr360_images !== '1') {
							$filtered_variations[] = $variation_id;
						}
					} else {
						$filtered_variations[] = $variation_id;
					}
				}
				if (!empty($filtered_variations)) {
					$products_with_variations[$product_id] = $filtered_variations;
				}
				array_unshift($products_with_variations[$product_id], $product_id);
			} else {
				$products_with_variations[$product_id] = $product_id;
			}
		}

		return $products_with_variations;
	}

	protected function getProductSKU($product_id) {
		$product = wc_get_product($product_id);
		return $product ? $product->get_sku() : null;
	}

	protected function processUrl($rawUrl, $product_id) {
		$urlWithProductId = str_replace('{product_or_variation_id}', $product_id, $rawUrl);
		if (strpos($urlWithProductId, '{sku}') !== false) {
			$sku = $this->getProductSKU($product_id);
			if ($sku === null) {
				return null;
			}
			$finalUrl = str_replace('{sku}', $sku, $urlWithProductId);
		} else {
			$finalUrl = $urlWithProductId;
		}
		return $finalUrl;
	}

	protected function sr_add_image_to_media_gallery($image_address, $new_filename) {
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents($image_address);
		$filename = basename($image_address);
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$filename = __('sr-attachment-icon-360_', 'sr-product-360-view') . $new_filename . '.' . $ext;
		if (wp_mkdir_p($upload_dir['path'])) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		file_put_contents($file, $image_data);
		$wp_filetype = wp_check_filetype($filename, null);
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment, $file);
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata($attach_id, $file);
		wp_update_attachment_metadata($attach_id, $attach_data);
		return $attach_id;
	}

	protected function remove_stack_element($array, $value) {
		return array_diff($array, (is_array($value) ? $value : array($value)));
	}

}
