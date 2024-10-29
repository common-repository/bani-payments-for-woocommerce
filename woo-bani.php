<?php

/**
 * Plugin Name: Bani Payments for WooCommerce
 * Plugin URI: https://docs.getbani.com/bani-pop/
 * Author: Bani
 * Author URI: https://bani.africa/
 * Description: Start accepting mobile money, cryptocurrency, and bank transfer payments from customers in Africa today.
 * Version: 1.0.4
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 4.7
 * WC tested up to: 6.0.1
 * text-domain: bani-payments-for-woocommerce
 * 
 * Class WC_Gateway_Bani file.
 *
 * @package WooCommerce\Bani
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
define('WC_BANI_MAIN_FILE', __FILE__);
define('WC_BANI_URL', untrailingslashit(plugins_url('/', __FILE__)));

define('WC_BANI_VERSION', '1.0.4');

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;




function bani_payment_init()
{
	if (!class_exists('WC_Payment_Gateway')) {
		add_action('admin_notices', 'jcm_wc_bani_wc_missing_notice');
		return;
	}
	add_action('admin_notices', 'jcm_wc_bani_testmode_notice');

	require_once plugin_dir_path(__FILE__) . '/includes/class-wc-payment-gateway-bani.php';

	add_filter('woocommerce_payment_gateways', 'add_to_woo_bani_payment_gateway');
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jcm_woo_bani_plugin_action_links');
}
add_action('plugins_loaded', 'bani_payment_init', 99);
/**
 * Add Settings link to the plugin entry in the plugins menu.
 *
 * @param array $links Plugin action links.
 *
 * @return array
 **/
function jcm_woo_bani_plugin_action_links($links)
{

	$settings_link = array(
		'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=bani') . '" title="' . __('View Paystack WooCommerce Settings', 'bani-payments-for-woocommerce') . '">' . __('Settings', 'bani-payments-for-woocommerce') . '</a>',
	);

	return array_merge($settings_link, $links);
}


/**
 * Display a notice if WooCommerce is not installed
 */
function jcm_wc_bani_wc_missing_notice()
{
	echo '<div class="error"><p><strong>' . sprintf(__('Bani requires WooCommerce to be installed and active. Click %s to install WooCommerce.', 'bani-payments-for-woocommerce'), '<a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=539') . '" class="thickbox open-plugin-details-modal">here</a>') . '</strong></p></div>';
}
function add_to_woo_bani_payment_gateway($gateways)
{
	$gateways[] = 'WC_Gateway_Bani';
	return $gateways;
}


/**
 * Display the test mode notice.
 **/
function jcm_wc_bani_testmode_notice()
{

	if (!current_user_can('manage_options')) {
		return;
	}

	$bani_settings = get_option('woocommerce_bani_settings');
	$test_mode         = isset($bani_settings['testmode']) ? $bani_settings['testmode'] : '';

	if ('yes' === $test_mode) {
		/* translators: 1. Bani settings page URL link. */
		echo '<div class="error"><p>' . sprintf(__('Bani test mode is still enabled, Click <strong><a href="%s">here</a></strong> to disable it when you want to start accepting live payment on your site.', 'bani-payments-for-woocommerce'), esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=bani'))) . '</p></div>';
	}
}
