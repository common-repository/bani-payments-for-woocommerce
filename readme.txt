=== Bani Payments for WooCommerce ===

Tags: bani, woocommerce, payment gateway, mobile money payments, crypto, cryptocurrency, bank transfer, naira, cedi
Requires at least: 4.7
Tested up to: 6.3
Stable tag: 1.0.5
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bani for WooCommerce allows merchants seamlessly accept cardless payments from their customers whether that be mobile money payments across Sub-Saharan Africa or crypto payments globally we got you covered.

== Description ==

Seamless cardless payments at a low cost. Collect mobile money payments from customers in Ghana, Cameroon, Kenya, Uganda, Rwanda, Mali, Tanzania, and Senegal. Also, easily allow customers to pay with cryptocurrency (stable and unstable coins).

With Bani for WooCommerce, you can accept payments via:

* Mobile money – Ghana, Cameroon, Kenya, Uganda, Rwanda, Mali, Tanzania, and Senegal
* Bank transfer (Nigeria)
* Cryptocurrency (Globally)
* Many more coming soon

= Why Bani? =

* Start receiving cardless payments instantly. Go from signup to your first real transaction in as little as 15 minutes
* Collect mobile money payments in 8+ African countries
* Collect stablecoins (BUSD / USDC / USDT / DAI) and non stablecoins (Bitcoin / Litecoin / Bitcoin Cash / Ethereum / Dodge Coin / Tron / BNB)
* Simple, transparent pricing. No hidden charges or setup fees
* Modern, seamless cardless payment experience via the Bani Checkout – [ Try the demo! ](https://demo-checkout.getbani.com)
* Understand your customers better through a simple and elegant dashboard
* Access to attentive, empathetic customer support 24/7
* Free updates as we launch new features and payment options
* Clearly documented APIs to build your custom payment experiences

Thousands of businesses of all sizes rely on Bani's suite of products to receive cardless payments and make payouts seamlessly. Sign up [here](https://app.bani.africa/signup) to get started.


= Note =

This plugin is meant to be used by merchants who want to collect payments from customers in Ghana, Cameroon, Kenya, Uganda, Rwanda, Mali, Tanzania, and Senegal. Also, allow customers to pay with cryptocurrency (stable and unstable coins).

= Plugin Features =

*   __Accept payment__ via Mobile Money, Bank Transfer, and Cryptocurrency.
*   __Seamless integration__ into the WooCommerce checkout page. Accept payment directly on your site
*   __Refunds__ from the WooCommerce order details page. Refund an order directly from the order details page

= Suggestions / Feature Request =

If you have suggestions or a new feature request, feel free to get in touch with us via [here](https://bani.africa/)

You can also follow us on Twitter! **[@getbaniHQ](https://twitter.com/getbaniHQ)**


== Installation ==

*   Go to __WordPress Admin__ > __Plugins__ > __Add New__ from the left-hand menu
*   In the search box type __Bani Payments for WooCommerce__
*   Click on Install now when you see __Bani Payments for WooCommerce__ to install the plugin
*   After installation, __activate__ the plugin.


= Bani Setup and Configuration =
*   Go to __WooCommerce > Settings__ and click on the __Payments__ tab
*   You'll see Bani listed along with your other payment methods. Click __Set Up__
*   On the next screen, configure the plugin. There is a selection of options on the screen. Read what each one does below.

1. __Enable/Disable__ - Check this checkbox to Enable Bani on your store's checkout
2. __Title__ - This will represent Bani on your list of Payment options during checkout. It guides users to know which option to select to pay with Bani. __Title__ is set to "Debit/Credit Cards" by default, but you can change it to suit your needs.
3. __Description__ - This controls the message that appears under the payment fields on the checkout page. Use this space to give more details to customers about what Bani is and what payment methods they can use with it.
4. __Test Mode__ - Check this to enable test mode. When selected, the fields in step six will say "Test" instead of "Live." Test mode enables you to test payments before going live. The orders process with test payment methods, no money is involved so there is no risk. You can uncheck this when your store is ready to accept real payments.
5. __API Keys__ - The next two text boxes are for your Bani API keys, which you can get from your Bani Dashboard. If you enabled Test Mode in step four, then you'll need to use your test API keys here. Otherwise, you can enter your live keys.
6. __Additional Settings__ - While not necessary for the plugin to function, there are some extra configuration options you have here. You can do things like add custom metadata to your transactions (the data will show up on your Bani dashboard).  The tooltips next to the options provide more information on what they do.
7. Click on __Save Changes__ to update the settings.

To account for poor network connections, which can sometimes affect order status updates after a transaction, we __strongly__ recommend that you set a Webhook URL on your Bani dashboard. This way, whenever a transaction is complete on your store, we'll send a notification to the Webhook URL, which will update the order and mark it as paid. To set the webhook URL, go to your Bani dashboard under __Settings > API Keys & Webhooks__ tab.

If you do not find Bani on the Payment method options, please go through the settings again and ensure that:

*   You've checked the __"Enable/Disable"__ checkbox
*   You've entered your __API Keys__ in the appropriate field
*   You've clicked on __Save Changes__ during setup

== Frequently Asked Questions ==

= What Do I Need To Use The Plugin =

*   A Bani merchant account—use an existing account or [create an account here](https://app.bani.africa/signup)
*   An active [WooCommerce installation](https://docs.woocommerce.com/document/installing-uninstalling-woocommerce/)
*   A valid [SSL Certificate](https://docs.woocommerce.com/document/ssl-and-https/)

== Changelog ==

= 1.0.0 - July 15, 2022 =
*   First release

= 1.0.1 - September 22, 2022 =
*   Live merchantKey error fix, plugin desc.

= 1.0.2 - October 4, 2022 =
*   Merchant keys link added to settings page, text content change (crypto)

= 1.0.3 - May 4, 2023 =
*   Bani payment widget now launches immediately on checkout.
*   Bani website link updated from https://getbani.com/ to https://bani.africa/

= 1.0.4 - July 11, 2023 =
*   You can now choose to show user's full name instead of phone number.
*   You can now choose to display the user's full name exclusively within the bank transfer account number field.

= 1.0.5 - September 3, 2023 =
*   Tested up to version updated.


== Screenshots ==

1. Bani displayed as a payment method on the WooCommerce payment methods page

2. Bani Payments for WooCommerce settings page

3. Bani on WooCommerce Checkout
