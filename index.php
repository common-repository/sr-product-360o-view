<?php
/*
  Plugin Name: SR Product 360&deg; View
  Plugin URI: https://wordpress.org/plugins/sr-product-360o-view/
  Description: Enhance your WooCommerce store with immersive 360&deg; product views. Engage customers, boost conversions, and showcase your products like never before. Try it now!
  Version: 5.6.0
  Author: Super Rishi
  Author URI: https://superrishi.com/
  Text Domain: sr-product-360-view
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
  Tested up to: 6.5.3
 */


require_once 'includes/access-check-wp.php';

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

require_once 'includes/constants/plugin_constants.php';

require_once 'includes/sr360Commons.php';

require_once 'includes/srCURLMethods.php';

require_once 'includes/sr360Icon.php';

require_once 'includes/sr360AdminMenu.php';

require_once 'includes/sr360Settings.php';

require_once 'includes/sr360ImportImages.php';

require_once 'includes/srPull360Images.php';

require_once 'includes/sr360Items.php';

require_once 'includes/sr360Restore.php';

require_once 'includes/sr360Help.php';

require_once 'includes/sr360GalleryImages.php';

require_once 'includes/sr360Views.php';

$_sr360AdminMenu = new sr360AdminMenu();

register_activation_hook(__FILE__, '_sr_product_360_view_activation_function');

add_action('admin_init', '_sr_product_360_view_activation_function');

add_action('admin_enqueue_scripts', '_sr_admin_pages_style');

function _sr_admin_pages_style($hook) {
	wp_enqueue_style('sr-360-admin-notice', plugins_url('assets/css/notice.css', __FILE__));
	if (strpos($hook, 'sr-product-360-view') !== false) {
		wp_enqueue_style('sr-360-admin-style', plugins_url('assets/css/style.css', __FILE__));
		wp_enqueue_style('sr-360-admin-forms', plugins_url('assets/css/forms.css', __FILE__));
		wp_enqueue_style('sr-360-admin-popup', plugins_url('assets/css/popup.css', __FILE__));
		wp_enqueue_style('sr-360-admin-misc', plugins_url('assets/css/misc.css', __FILE__));
	}
}

function _sr_product_360_view_activation_function() {
	require_once 'includes/sr360License.php';
	$_sr360License = new sr360License();
	if ($_sr360License->check_license_status() === false) {
		add_action('admin_notices', array($_sr360License, 'admin_notice_activate_license'));
	} elseif (!is_plugin_active('woocommerce/woocommerce.php')) {
		add_action('admin_notices', '_sr_WooCommerce_not_active');
	} elseif (!function_exists('curl_init')) {
		add_action('admin_notices', '_sr_cURLAbsent');
	} else {
		sr_check_conflicts_and_deactivate();
	}
}

register_deactivation_hook(__FILE__, '_sr_product_360_view_deactivation_function');

function _sr_product_360_view_deactivation_function() {
	// Deactivation code comes here, if needed.
}

function _sr_WooCommerce_not_active() {
	?>
	<div class="sr-notice-container notice notice-warning is-dismissible">
		<div class="sr-section-left">
			<a class="navbar-brand" href="<?= SUPER_RISHI_WEBSITE; ?>" rel="noopener"><?= __('SUPER RISHI', 'sr-product-360-view'); ?></a>
		</div>
		<div class="sr-section-right">
			<h3><?= __('Thank you for installing &ldquo;SR Product 360&deg; View&rdquo;.', 'sr-product-360-view'); ?></h3>
			<p>To start using this plugin you need to install and activate the <strong>WooCommerce</strong> plugin.</p>
			<a href="<?= SUPER_RISHI_WEBSITE; ?>" class="help-link" rel="noopener" target="_blank">Learn more &nearr;</a>
		</div>
	</div>
	<?php
}

function _sr_cURLAbsent() {
	?>
	<div class="sr-notice-container notice notice-warning is-dismissible">
		<div class="sr-section-left">
			<a class="navbar-brand" href="<?= SUPER_RISHI_WEBSITE; ?>" rel="noopener"><?= __('SUPER RISHI', 'sr-product-360-view'); ?></a>
		</div>
		<div class="sr-section-right">
			<h3><?= __('Thank you for installing &ldquo;SR Product 360&deg; View&rdquo;.', 'sr-product-360-view'); ?></h3>
			<p>To start using this plugin you need to install the <strong>PHP-cURL</strong> extension on your server.</p>
			<a href="<?= SUPER_RISHI_WEBSITE; ?>" class="help-link" rel="noopener" target="_blank">Learn more &nearr;</a>
		</div>
	</div>
	<?php
}

// Check if the conflicting plugin 'sr-product-360o-view-pro' is active
function sr_check_conflicts_and_deactivate() {
	if (is_plugin_active('sr-product-360o-view-pro/sr.php')) {
		// Deactivate the conflicting plugin
		deactivate_plugins('sr-product-360o-view-pro/sr.php');

		// Display an admin notice to notify the user
		add_action('admin_notices', 'sr_conflict_notice');
	}
}

function sr_conflict_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php _e('The &rdquo;SR Product 360&deg; View Pro&ldquo; plugin has been deactivated because it is no longer supported and maintained. - Super Rishi', 'sr-product-360-view'); ?></p>
	</div>
	<?php
}
