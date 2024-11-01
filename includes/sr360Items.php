<?php

require_once 'access-check-wp.php';

class srItems360 extends srCURLMethods {

	function __construct() {
		add_action('admin_enqueue_scripts', array($this, '_sr_admin_pages_style'));
		add_action('wp_ajax_sr360_get_individual_settings', array($this, 'sr360_get_individual_settings'));
		add_action('wp_ajax_sr360_save_individual_settings', array($this, 'sr360_save_individual_settings'));
		add_action('wp_ajax_sr360_import_individual', array($this, 'sr360_import_individual'));
		add_action('wp_ajax_sr360_delete_view', array($this, 'sr360_delete_view'));
		add_action('before_delete_post', array($this, 'delete_360_view_on_product_delete'));
	}

	function _sr_admin_pages_style($hook) {
		if (strpos($hook, _SR_360_ADMIN_PAGE_ITEMS) !== false) {
			wp_enqueue_style('sr-360-items-page', plugins_url('assets/css/items-360.css', __DIR__));
			wp_enqueue_style('sr360-datatables-style', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
			wp_enqueue_script('jquery');
			wp_enqueue_script('sr360-datatables-script', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);
			wp_enqueue_script('sr-360-items-page', plugins_url('assets/js/import-individual-settings.js', __DIR__), array('jquery'), false, true);
			wp_localize_script('sr-360-items-page', 'sr360Individual', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'action' => 'sr360_get_individual_settings',
				'action_1' => 'sr360_save_individual_settings',
				'nonce' => wp_create_nonce('sr360_get_individual_settings'),
				'nonce_1' => wp_create_nonce('sr360_save_individual_settings'),
				'default_message' => '<span><img style="vertical-align:sub;" src="' . includes_url('images/spinner.gif') . '"/> Please wait...</span>'
			));
			wp_enqueue_script('sr360-import-individual', plugins_url('assets/js/import-individual-images.js', __DIR__), array('jquery'), false, true);
			wp_localize_script('sr360-import-individual', 'sr360ImportIndividual', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'action' => 'sr360_import_individual',
				'nonce' => wp_create_nonce('sr360_import_individual'),
				'default_message' => '<span><img style="vertical-align:sub;" src="' . includes_url('images/spinner.gif') . '"/> Please wait...</span>'
			));
			wp_enqueue_script('sr360-delete-view', plugins_url('assets/js/delete-360-view.js', __DIR__), array('jquery'), false, true);
			wp_localize_script('sr360-delete-view', 'sr360DeleteView', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'action' => 'sr360_delete_view',
				'nonce' => wp_create_nonce('sr360_delete_view')
			));
		}
	}

	function sr360_get_individual_settings() {
		$action = sanitize_text_field($_POST['action']);
		if (check_ajax_referer($action, 'nonce', false)) {
			$data['product_id'] = intval($_POST['product_id']);
			$data['variation_id'] = intval($_POST['variation_id']);
			$data['license_key'] = $this->get_license_key();
			$response = $this->get_settings_data($data);
			if (isset($response->code)) {
				wp_send_json(array('error' => $response->message), 200);
			} elseif (isset($response->settings)) {
				wp_send_json($response->settings, 200);
			} elseif ($response === false || $response === null) {
				wp_send_json(array('error' => 'Not able to complete the request'), 200);
			}
		} else {
			wp_send_json(array('error' => 'Authentication failed, security key expired'), 200);
		}
		exit;
	}

	function sr360_save_individual_settings() {
		if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_key($_POST['nonce']), 'sr360_save_individual_settings')) {
			unset($_POST['action'], $_POST['nonce'], $_POST['action-for-request']);
			$complete = true;
			$data = array();
			foreach ($_POST as $key => $value):
				$key = trim(sanitize_text_field($key));
				$value = trim(sanitize_text_field($value));
				if ($value === ""):
					$complete = false;
					break;
				else:
					$data[$key] = $value;
				endif;
			endforeach;
			if ($complete):
				$response = $this->save_settings(array('settings' => $data, 'license_key' => $this->get_license_key()));
				if ($response):
					if ($response->code):
						wp_send_json(array('error' => "' . $response->message . '"), 200);
					else:
						if ($response->token):
							update_option(_SR_360_TOKEN, $response->token, 'no');
							wp_send_json(array('success' => 'Settings saved successfully.'), 200);
						endif;
					endif;
				else:
					wp_send_json(array('error' => 'Sorry, we were unable to save your settings.'), 200);
				endif;
			else:
				wp_send_json(array('error' => 'All fields are mandatory.'), 200);
			endif;
		} else {
			wp_send_json(array('error' => 'Security check failed!'), 200);
		}
	}

	function sr360_import_individual() {
		$action = sanitize_text_field($_POST['action']);
		if (check_ajax_referer($action, 'nonce', false)) {
			$is_variation = false;
			$product_id = intval($_POST['product_id']);
			$variation_id = intval($_POST['variation_id']);
			if ($product_id === 0) {
				wp_send_json(array('error' => 'Invalid Product Or Variation'), 200);
			}
			if ($variation_id !== 0) {
				$is_variation = true;
			}
			$license_key = $this->get_license_key();
			$import_settings = $this->get_import_settings(array('license_key' => $license_key));
			if ($import_settings && isset($import_settings->settings)) {
				$settings = $import_settings->settings;
				if ($is_variation):
					$baseurl = $this->processUrl(sanitize_text_field($settings->baseurl), $variation_id);
				else:
					$baseurl = $this->processUrl(sanitize_text_field($settings->baseurl), $product_id);
				endif;
				$lzero = intval($settings->lzero);
				$ext = sanitize_text_field($settings->ext);
				if ($baseurl === null) {
					wp_send_json(array('error' => 'Could not find the SKU. Please check &lsquo;baseurl&rsquo; settings in <strong>Bulk Import Images</strong>.'), 200);
				} else {
					$image_import = new srPull360Images($baseurl, $lzero, $ext);
					$result = $image_import->checkImages();
					if (isset($result['message']) && isset($result['images_found'])) {
						$message = sanitize_text_field($result['message']);
						$images = intval($result['images_found']);
						$has_images = $images > 0 ? 1 : 0;
						$data['license_key'] = $license_key;
						$data['images'] = $images;
						$data['baseurl'] = $baseurl;
						$data['lzero'] = $lzero;
						$data['ext'] = $ext;
						$data['product_id'] = $product_id;
						$data['variation_id'] = $variation_id;
						$response = $this->save_360_images($data);
						if (isset($response->code)) {
							if ($is_variation) {
								update_post_meta($variation_id, _SR_360_PRODUCT, 0);
							} else {
								update_post_meta($product_id, _SR_360_PRODUCT, 0);
							}
							wp_send_json(array('summary' => $response->message), 200);
						} elseif (isset($response->success)) {
							if ($is_variation) {
								update_post_meta($variation_id, _SR_360_PRODUCT, $has_images);
							} else {
								update_post_meta($product_id, _SR_360_PRODUCT, $has_images);
							}
							wp_send_json(array('summary' => $message), 200);
						}
					} else {
						wp_send_json(array('error' => 'Could not fetch images'), 200);
					}
				}
			} else {
				$message = isset($import_settings->message) ? sanitize_text_field($import_settings->message) : 'Sorry, you have not activated your subscription license yet.';
				wp_send_json(array('error' => $message, 'block' => 1), 200);
			}
		} else {
			wp_send_json(array('error' => 'Authentication failed, security key expired'), 200);
		}
		exit;
	}

	function sr360_delete_view() {
		$action = sanitize_text_field($_POST['action']);
		if (check_ajax_referer($action, 'nonce', false)) {
			$is_variation = false;
			$product_id = intval($_POST['product_id']);
			$variation_id = intval($_POST['variation_id']);
			if ($variation_id !== 0) {
				$is_variation = true;
			}
			$license_key = $this->get_license_key();
			if ($product_id === 0) {
				wp_send_json(array('error' => 'Invalid Product Or Variation'), 200);
			}
			$this->remove_360_view($license_key, $product_id, $variation_id);
		} else {
			wp_send_json(array('error' => 'Authentication failed, security key expired'), 200);
		}
		exit;
	}

	function delete_360_view_on_product_delete($post_id) {
		if (get_post_type($post_id) === 'product' && !wp_is_post_revision($post_id) && !wp_is_post_autosave($post_id)) {
			$license_key = $this->get_license_key();
			$product_id = intval($post_id);
			$variation_id = 0;
			$skip_response = true;
			$this->remove_360_view($license_key, $product_id, $variation_id, $skip_response);
			$variations = get_posts(array(
				'post_type' => 'product_variation',
				'posts_per_page' => -1,
				'post_status' => array('publish', 'trash'),
				'post_parent' => $product_id,
				'fields' => 'ids'
			));

			if ($variations) {
				foreach ($variations as $variation_id) {
					$this->remove_360_view($license_key, $product_id, $variation_id, $skip_response);
				}
			}
		}
	}

	private function remove_360_view($license_key, $product_id, $variation_id, $skip_response = false) {
		$data['license_key'] = $license_key;
		$data['images'] = 0;
		$data['baseurl'] = '';
		$data['lzero'] = '';
		$data['ext'] = '';
		$data['product_id'] = $product_id;
		$data['variation_id'] = $variation_id;
		$response = $this->delete_360_images($data);
		if (isset($response->success)) {
			if ($is_variation) {
				update_post_meta($product_id, _SR_360_PRODUCT, 0);
				update_post_meta($variation_id, _SR_360_PRODUCT, 0);
			} else {
				update_post_meta($product_id, _SR_360_PRODUCT, 0);
			}
			if (!$skip_response) {
				wp_send_json(array('success' => true), 200);
			}
		} else {
			if (!$skip_response) {
				wp_send_json(array('error' => 'Could not able delete 360 view. Please try after a few moments.'), 200);
			}
		}
	}

}

new srItems360();
