jQuery(function ($) {
  "use strict";

  /**
   * Object to handle Paystack admin functions.
   */
  var wc_bani_admin = {
    /**
     * Initialize.
     */
    init: function () {
      // Toggle api key settings.
      $(document.body).on("change", "#woocommerce_bani_testmode", function () {
        var test_public_key = $("#woocommerce_bani_test_public_key")
            .parents("tr")
            .eq(0),
          live_public_key = $("#woocommerce_bani_live_public_key")
            .parents("tr")
            .eq(0);

        if ($(this).is(":checked")) {
          test_public_key.show();
          live_public_key.hide();
        } else {
          test_public_key.hide();
          live_public_key.show();
        }
      });

      $("#woocommerce_bani_testmode").change();

      // Toggle Custom Metadata settings.
      $(".wc-bani-metadata")
        .change(function () {
          if ($(this).is(":checked")) {
            $(
              ".wc-bani-meta-order-id, .wc-bani-meta-billing-address, .wc-bani-meta-shipping-address, .wc-bani-meta-products"
            )
              .closest("tr")
              .show();
          } else {
            $(
              ".wc-bani-meta-order-id, .wc-bani-meta-billing-address, .wc-bani-meta-shipping-address, .wc-bani-meta-products"
            )
              .closest("tr")
              .hide();
          }
        })
        .change();
    },
  };

  wc_bani_admin.init();
});
