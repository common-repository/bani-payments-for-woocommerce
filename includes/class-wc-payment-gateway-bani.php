<?php

/**
 * Bani WooCommerce Payments.
 *
 * Provides a Bani Payments Payment Gateway.
 *
 * @class       WC_Gateway_Bani
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     WooCommerce/Classes/Payment
 */
class WC_Gateway_Bani extends WC_Payment_Gateway
{

	/**
	 * Constructor for the gateway.
	 */
	/**
	 * Is test mode active?
	 *
	 * @var bool
	 */
	public $testmode;

	/**
	 * Should orders be marked as complete after payment?
	 * 
	 * @var bool
	 */
	public $autocomplete_order;

	/**
	 * Should custom metadata be enabled?
	 *
	 * @var bool
	 */
	public $custom_metadata;


	/**
	 * Should show user full name be enabled?
	 *
	 * @var bool
	 */
	public $show_username;



	/**
	 * Should show user account name be enabled?
	 *
	 * @var bool
	 */
	public $show_user_account_name;



	/**
	 * Should the order id be sent as a custom metadata to Bani?
	 *
	 * @var bool
	 */
	public $meta_order_id;
	/**
	 * Should the order items be sent as a custom metadata to Bani?
	 *
	 * @var bool
	 */
	public $meta_products;

	/**
	 * Should the billing address be sent as a custom metadata to Bani?
	 *
	 * @var bool
	 */
	public $meta_billing_address;

	/**
	 * API public key
	 *
	 * @var string
	 */
	public $public_key;
	/**
	 * Should the shipping address be sent as a custom metadata to Bani?
	 *
	 * @var bool
	 */
	public $meta_shipping_address;


	public function __construct()
	{
		// Setup general properties.
		$this->setup_properties();
		$this->id                 = 'bani';

		// Load the settings.
		// Load the form fields
		$this->init_form_fields();
		// Load the settings
		$this->init_settings();

		// Get settings.
		$this->title              = $this->get_option('title');
		$this->description        = $this->get_option('description');
		$this->enabled            = $this->get_option('enabled');
		$this->testmode           = $this->get_option('testmode') === 'yes' ? true : false;
		$this->test_public_key = $this->get_option('test_public_key');
		$this->live_public_key = $this->get_option('live_public_key');
		$this->autocomplete_order = $this->get_option('autocomplete_order') === 'yes' ? true : false;
		$this->remove_cancel_order_button = $this->get_option('remove_cancel_order_button') === 'yes' ? true : false;

		$this->custom_metadata = $this->get_option('custom_metadata') === 'yes' ? true : false;
		$this->show_username = $this->get_option('show_username') === 'yes' ? true : false;
		$this->show_user_account_name = $this->get_option('show_user_account_name') === 'yes' ? true : false;

		$this->meta_order_id         = $this->get_option('meta_order_id') === 'yes' ? true : false;
		$this->meta_products         = $this->get_option('meta_products') === 'yes' ? true : false;
		$this->meta_billing_address  = $this->get_option('meta_billing_address') === 'yes' ? true : false;
		$this->meta_shipping_address = $this->get_option('meta_shipping_address') === 'yes' ? true : false;

		$this->public_key = $this->testmode ? $this->test_public_key : $this->live_public_key;

		// Hooks
		add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action('admin_notices', array($this, 'admin_notices'));
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
		// Payment listener/API hook.
		add_action('woocommerce_api_wc_gateway_bani', array($this, 'verify_bani_transaction'));
	}



	/**
	 * Check if Bani merchant details is filled.
	 */
	public function admin_notices()
	{

		if ($this->enabled == 'no') {
			return;
		}

		// Check required fields.
		if (!($this->public_key)) {
			echo '<div class="error"><p>' . sprintf(
				__(
					'Please enter your Bani merchant details <a href="%s">here</a> to be able to use the Bani WooCommerce plugin.',
					'bani-payments-for-woocommerce'
				),
				admin_url('admin.php?page=wc-settings&tab=checkout&section=bani')
			) . '</p></div>';
			return;
		}
	}

	/**
	 * Check if Bani gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available()
	{

		if ('yes' == $this->enabled) {

			if (!($this->public_key)) {

				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options()
	{

?>

		<h2><?php _e('Bani', 'bani-payments-for-woocommerce'); ?>
			<?php
			if (function_exists('wc_back_link')) {
				wc_back_link(__('Return to payments', 'bani-payments-for-woocommerce'), admin_url('admin.php?page=wc-settings&tab=checkout'));
			}
			?>
		</h2>

		<h4 style="margin-bottom: 10px;">
			<strong><?php printf(__('To get started <a href="%1$s" target="_blank" rel="noopener noreferrer">Create your free</a> Bani account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'bani-payments-for-woocommerce'), 'https://bani.africa/', 'https://app.bani.africa/dashboard/settings'); ?></strong>
		</h4>
		<h4>
			<strong><?php printf(__('Optional: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%1$s" target="_blank" rel="noopener noreferrer">here</a>', 'bani-payments-for-woocommerce'), 'https://app.bani.africa/dashboard/settings'); ?></strong>
		</h4>

<?php

		echo '<table class="form-table">';
		$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Setup general properties for the gateway.
	 */
	protected function setup_properties()
	{
		$this->id                 = 'bani';
		$this->icon               = apply_filters('woocommerce_bani_icon', WC_HTTPS::force_https_url(plugins_url('assets/images/bani.svg', WC_BANI_MAIN_FILE)));
		$this->method_title       = __('Bani', 'bani-payments-for-woocommerce');
		$this->method_description =  sprintf(__('Start accepting bank transfer, cryptocurrency, and mobile money payments from customers in Africa today. <a href="%1$s" target="_blank">Create a free</a> Bani account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'bani-payments-for-woocommerce'), 'https://bani.africa/', 'https://app.bani.africa/dashboard/settings');
		$this->has_fields         = false;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'            => array(
				'title'       => __('Enable/Disable', 'bani-payments-for-woocommerce'),
				'label'       => __('Enable Bani Payments', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'description' => __('Enable Bani as a payment option on the checkout page.', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'              => array(
				'title'       => __('Title', 'bani-payments-for-woocommerce'),
				'type'        => 'text',
				'description' => __('Bani payments description that the customer will see on your checkout.', 'bani-payments-for-woocommerce'),
				'default'     => __('Bank transfer payments/Mobile money/Cryptocurrency.', 'bani-payments-for-woocommerce'),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __('Description', 'bani-payments-for-woocommerce'),
				'type'        => 'textarea',
				'description' => __('Bani payments description that the customer will see on your website.', 'bani-payments-for-woocommerce'),
				'default'     => __('Pay using bank transfer, mobile money, and cryptocurrency.', 'bani-payments-for-woocommerce'),
				'desc_tip'    => true,
			),
			'testmode'            => array(
				'title'       => __('Test Mode', 'bani-payments-for-woocommerce'),
				'label'       => __('Enable Test Mode', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'description' => __('Test mode enables you to test payments before going live. <br />Once the LIVE MODE is enabled on your Bani account uncheck this.', 'bani-payments-for-woocommerce'),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'test_public_key'                  => array(
				'title'       => __('Test Public Key', 'bani-payments-for-woocommerce'),
				'type'        => 'text',
				'description' => __('Enter your Test Merchant Public Key here.', 'bani-payments-for-woocommerce'),
				'default'     => '',
			),
			'live_public_key'                  => array(
				'title'       => __('Live Public Key', 'bani-payments-for-woocommerce'),
				'type'        => 'text',
				'description' => __('Enter your Live Merchant Public Key here.', 'bani-payments-for-woocommerce'),
				'default'     => '',
			),
			'remove_cancel_order_button'       => array(
				'title'       => __('Remove Cancel Order & Restore Cart Button', 'bani-payments-for-woocommerce'),
				'label'       => __('Remove the cancel order & restore cart button on the pay for order page', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'autocomplete_order'               => array(
				'title'       => __('Autocomplete Order After Payment', 'bani-payments-for-woocommerce'),
				'label'       => __('Autocomplete Order', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-paystack-autocomplete-order',
				'description' => __('If enabled, the order will be marked as complete after successful payment', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),

			'custom_metadata'                  => array(
				'title'       => __('Custom Metadata', 'bani-payments-for-woocommerce'),
				'label'       => __('Enable Custom Metadata', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-metadata',
				'description' => __('If enabled, you will be able to send more information about the order to Bani.', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'show_username'                  => array(
				'title'       => __("Show User's Full Name Instead of Phone Number", 'bani-payments-for-woocommerce'),
				'label'       => __("Show User's Full Name", 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-username',
				'description' => __("Check this box to show the user's full name in the checkout widget's header while hiding their phone number.", 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),

			'show_user_account_name'                  => array(
				'title'       => __('Show User Account Name', 'bani-payments-for-woocommerce'),
				'label'       => __('Show User Account Name', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-user-account-name',
				'description' => __("Selecting this checkbox will display the user's full name exclusively within the bank transfer account number field.", 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_order_id'                    => array(
				'title'       => __('Order ID', 'bani-payments-for-woocommerce'),
				'label'       => __('Send Order ID', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-meta-order-id',
				'description' => __('If checked, the Order ID will be sent to Bani', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_billing_address'             => array(
				'title'       => __('Order Billing Address', 'bani-payments-for-woocommerce'),
				'label'       => __('Send Order Billing Address', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-meta-billing-address',
				'description' => __('If checked, the order billing address will be sent to Bani', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_shipping_address'            => array(
				'title'       => __('Order Shipping Address', 'bani-payments-for-woocommerce'),
				'label'       => __('Send Order Shipping Address', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-meta-shipping-address',
				'description' => __('If checked, the order shipping address will be sent to Bani', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_products'                    => array(
				'title'       => __('Product(s) Purchased', 'bani-payments-for-woocommerce'),
				'label'       => __('Send Product(s) Purchased', 'bani-payments-for-woocommerce'),
				'type'        => 'checkbox',
				'class'       => 'wc-bani-meta-products',
				'description' => __('If checked, the product(s) purchased will be sent to Bani', 'bani-payments-for-woocommerce'),
				'default'     => 'no',
				'desc_tip'    => true,
			),


		);
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields()
	{

		if ($this->description) {
			$desc = wpautop(esc_html(wptexturize($this->description)));
			$desc = str_replace('<p>', '', $desc);
			$desc = str_replace('</p>', '', $desc);
			echo esc_html($desc);
		}

		if (!is_ssl()) {
			return;
		}
	}

	/**
	 * Outputs scripts used for bani payment.
	 */

	public function payment_scripts()
	{
		if (!is_checkout_pay_page()) {
			return;
		}

		if ($this->enabled === 'no') {
			return;
		}

		$order_key = sanitize_url(urldecode($_GET['key']));
		$order_key = str_replace("http://", "", $order_key);
		$order_id  = absint(get_query_var('order-pay'));

		$order = wc_get_order($order_id);
		$payment_method = method_exists($order, 'get_payment_method') ? $order->get_payment_method() : $order->payment_method;

		if ($this->id !== $payment_method) {
			return;
		}


		wp_enqueue_script('jquery');

		wp_enqueue_script('bani', plugins_url('assets/js/app.min.js', WC_BANI_MAIN_FILE), array('jquery'), WC_BANI_VERSION, true);

		wp_enqueue_script('wc_bani', plugins_url('assets/js/bani.min' . '.js', WC_BANI_MAIN_FILE), array('jquery', 'bani'), WC_BANI_VERSION, true);


		$bani_params = array(
			'merchantKey' => $this->public_key,
		);


		if (is_checkout_pay_page() && get_query_var('order-pay')) {

			$email         = method_exists($order, 'get_billing_email') ? $order->get_billing_email() : $order->billing_email;
			$amount        = $order->get_total() * 100;
			$txnref        = $order_id . '_' . time();
			$the_order_id  = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
			$the_order_key = method_exists($order, 'get_order_key') ? $order->get_order_key() : $order->order_key;
			$currency      = method_exists($order, 'get_currency') ? $order->get_currency() : $order->order_currency;
			$first_name = method_exists($order, 'get_billing_first_name') ? $order->get_billing_first_name() : $order->billing_first_name;
			$last_name  = method_exists($order, 'get_billing_last_name') ? $order->get_billing_last_name() : $order->billing_last_name;
			$billing_phone = method_exists($order, 'get_billing_phone') ? $order->get_billing_phone() : $order->billing_phone;
			$display_username        = $this->show_username;
			$display_user_account_name        =  $this->show_user_account_name;

			if ($the_order_id == $order_id && $the_order_key == $order_key) {
				$bani_params['email']    = sanitize_email($email);
				$bani_params['amount']   = $amount;
				$bani_params['txnref']   = $txnref;
				$bani_params['currency'] = $currency;
				$bani_params['phoneNumber'] = $billing_phone;
				$bani_params['firstName'] = $first_name;
				$bani_params['lastName'] = $last_name;
				$bani_params['showUserName'] = $display_username;
				$bani_params['showUserAccountName'] = $display_user_account_name;
			}

			$custom_metadata_data = array();
			if ($this->custom_metadata) {

				if ($this->meta_order_id) {

					$custom_metadata_data['meta_order_id'] = $order_id;
				}
				if ($this->meta_billing_address) {
					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));
					$custom_metadata_data['meta_billing_address'] = $billing_address;
				}

				if ($this->meta_shipping_address) {
					$shipping_address = $order->get_formatted_shipping_address();
					$shipping_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $shipping_address));

					if (empty($shipping_address)) {

						$billing_address = $order->get_formatted_billing_address();
						$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

						$shipping_address = $billing_address;
					}


					$custom_metadata_data['meta_shipping_address'] = $shipping_address;
				}



				if ($this->meta_products) {

					$line_items = $order->get_items();

					$products = '';

					foreach ($line_items as $item_id => $item) {
						$name      = $item['name'];
						$quantity  = $item['qty'];
						$products .= $name . ' (Qty: ' . $quantity . ')';
						$products .= ' | ';
					}

					$products = rtrim($products, ' | ');

					$custom_metadata_data['meta_products'] = $products;
				}
			}
			$custom_metadata_data['order_ref'] = $txnref;
			$bani_params['metadata'] = $custom_metadata_data;
			update_post_meta($order_id, '_bani_txn_ref', $txnref);
		}



		wp_localize_script('wc_bani', 'wc_bani_params', $bani_params);
	}

	/**
	 * Load admin scripts.
	 */
	public function admin_scripts()
	{

		if ('woocommerce_page_wc-settings' !== get_current_screen()->id) {
			return;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		$bani_admin_params = array(
			'plugin_url' => WC_BANI_URL,
		);

		wp_enqueue_script('wc_bani_admin', plugins_url('assets/js/bani-admin' . $suffix . '.js', WC_BANI_MAIN_FILE), array(), WC_BANI_VERSION, true);

		wp_localize_script('wc_bani_admin', 'wc_bani_admin_params', $bani_admin_params);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array|void
	 */
	public function process_payment($order_id)
	{
		$order = wc_get_order($order_id);
		if ($order->get_total() > 0) {
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url(true),
			);
		}
	}



	/**
	 * Displays the payment page.
	 *
	 * @param $order_id
	 */
	public function receipt_page($order_id)
	{

		$order = wc_get_order($order_id);

		echo '<div id="wc-bani-form">';

		echo '<p>' . __('Thank you for your order, please click the button below to pay with Bani.', 'bani-payments-for-woocommerce') . '</p>';

		echo '<div id="bani_form"><form id="order_review" method="post" action="' . WC()->api_request_url('WC_Gateway_Bani') . '"></form><button class="button" id="bani-payment-button">' . __('Pay Now', 'bani-payments-for-woocommerce') . '</button>';

		if (!$this->remove_cancel_order_button) {
			echo '  <a class="button cancel" id="bani-cancel-payment-button" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', 'bani-payments-for-woocommerce') . '</a></div>';
		}

		echo '</div>';
	}

	/**
	 * Verify Bani payment.
	 */
	public function verify_bani_transaction()
	{

		if (isset($_REQUEST['bani_txnref'])) {
			$bani_txn_ref = sanitize_text_field($_REQUEST['bani_txnref']);
			$bani_txn_status = sanitize_text_field($_REQUEST['bani_txnstatus']);
			$bani_txn_type = sanitize_text_field($_REQUEST['bani_txntype']);
		} elseif (isset($_REQUEST['reference'])) {
			$bani_txn_ref = sanitize_text_field($_REQUEST['reference']);
		} else {
			$bani_txn_ref = '';
			$bani_txn_status = '';
			$bani_txn_type = '';
		}

		function RemoveSpecialChar($str)
		{

			// Using str_replace() function 
			// to replace the word 
			$res = str_replace('/', '', $str);

			// Returning the result 
			return $res;
		}
		$bani_txn_ref =  RemoveSpecialChar($bani_txn_ref);
		$bani_txn_status =  RemoveSpecialChar($bani_txn_status);
		$bani_txn_type =  RemoveSpecialChar($bani_txn_type);

		@ob_clean();

		if ($bani_txn_ref) {
			$cryptoUrl = "/partner/collection/coin_payment_status/" . $bani_txn_ref . "/";

			$bani_sub_url = $bani_txn_type === 'crypto' ? $cryptoUrl : '/partner/collection/pay_status_check/';
			$bani_env_url = $this->testmode ? 'stage' : 'live';
			$bani_url = 'https://' . $bani_env_url . '.getbani.com/api/v1' . $bani_sub_url;

			$headers = array(
				'MERCHANT-PUB-KEY' =>  $this->public_key,
			);

			$argsFiat = array(
				'headers' => $headers,
				'body'        => array(
					'pay_ref' => $bani_txn_ref,
				),
				'timeout' => 60,
			);
			$args = array(
				'headers' => $headers,
				'timeout' => 60,
			);

			$request = $bani_txn_type === 'crypto' ? wp_remote_get($bani_url, $args) : wp_remote_post($bani_url, $argsFiat);

			$bani_response = json_decode(wp_remote_retrieve_body($request));
			if (!is_wp_error($request) && 200 === wp_remote_retrieve_response_code($request)) {

				$bani_response = json_decode(wp_remote_retrieve_body($request));
				$pay_ext_ref = $bani_txn_type === 'crypto' ?   $bani_response->custom_data->order_ref : $bani_response->data->custom_data->order_ref;
				$pay_status = $bani_txn_type === 'crypto' ?   $bani_response->transaction_status : $bani_response->data->pay_status;

				if ('completed' == $pay_status || 'paid' == $pay_status) {

					$order_details = explode('_', $pay_ext_ref);
					$order_id      = (int) $order_details[0];
					$order         = wc_get_order($order_id);

					if (in_array($order->get_status(), array('processing', 'completed', 'on-hold', 'paid'))) {

						wp_redirect($this->get_return_url($order));

						exit;
					}

					$order->payment_complete($bani_txn_ref);
					$order->add_order_note(__('Payment via Bani successful (Transaction Reference: ', 'bani-payments-for-woocommerce')
						. sprintf('%s)', $bani_txn_ref));

					if ($this->is_autocomplete_order_enabled($order)) {
						$order->update_status('completed');
					}

					WC()->cart->empty_cart();
				} else {


					$order_details = explode('_', $pay_ext_ref);
					$order_id      = (int) $order_details[0];
					$order         = wc_get_order($order_id);
					$order->add_order_note(__('Payment via Bani pending (Transaction Reference: ', 'bani-payments-for-woocommerce')
						. sprintf('%s)', $bani_txn_ref));
					$order->update_status('on-hold', '');
				}
			}

			wp_redirect($this->get_return_url($order));


			exit;
		}

		wp_redirect(wc_get_page_permalink('cart'));


		exit;
	}
	/**
	 * Checks if WC version is less than passed in version.
	 *
	 * @param string $version Version to check against.
	 *
	 * @return bool
	 */
	public function is_wc_lt($version)
	{
		return version_compare(WC_VERSION, $version, '<');
	}

	/**
	 * Checks if autocomplete order is enabled for the payment method.
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_autocomplete_order_enabled($order)
	{
		$autocomplete_order = false;

		$payment_method = $order->get_payment_method();

		$paystack_settings = get_option('woocommerce_' . $payment_method . '_settings');

		if (isset($paystack_settings['autocomplete_order']) && 'yes' === $paystack_settings['autocomplete_order']) {
			$autocomplete_order = true;
		}

		return $autocomplete_order;
	}


	/**
	 * Change payment complete order status to completed for bani orders.
	 *
	 * @since  3.1.0
	 * @param  string         $status Current order status.
	 * @param  int            $order_id Order ID.
	 * @param  WC_Order|false $order Order object.
	 * @return string
	 */
	public function change_payment_complete_order_status($status, $order_id = 0, $order = false)
	{
		if ($order && 'bani' === $order->get_payment_method()) {
			$status = 'completed';
		}
		return $status;
	}
}
