jQuery(function ($) {
  let bani_submit = false;
  $("#wc-bani-form").hide();
  $("body").block({
    message: null,
    overlayCSS: {
      background: "#fff",
      opacity: 0.6,
    },
    css: {
      cursor: "wait",
    },
  });

  wcPayWithBani();

  jQuery("#bani-payment-button").click(function () {
    return wcPayWithBani("ONCLICK");
  });

  jQuery("#bani_form form#order_review").submit(function () {
    return wcPayWithBani("ON SUBMIT");
  });
  function wcPayWithBani(msg) {
    $("body").unblock();

    if (bani_submit) {
      bani_submit = false;
      return true;
    }
    let $form = $("form#payment-form, form#order_review"),
      bani_txnref = $form.find("input.bani_txnref"),
      bani_txnstatus = $form.find("input.bani_txnstatus"),
      bani_txntype = $form.find("input.bani_txntype");
    bani_txnref.val("");
    bani_txnstatus.val("");
    bani_txntype.val("");
    let amount = Number(wc_bani_params.amount);
    amount = amount / 100;
    let resRef;
    let bani_callback = function (response) {
      resRef = response?.reference || "";
      let resStatus = response?.status || "";
      let resType = response?.type || "";
      let resExtRef = response?.external_ref || "";
      $form.append(
        `<input  type="hidden" class="bani_txnref" name="bani_txnref" value= ${resRef}/>`
      );
      $form.append(
        `<input  type="hidden" class="bani_txnstatus" name="bani_txnstatus" value= ${resStatus}/>`
      );
      $form.append(
        `<input  type="hidden" class="bani_txntype" name="bani_txntype" value= ${resType}/>`
      );
      $form.append(
        `<input  type="hidden" class="bani_txnextref" name="bani_txnextref" value= ${resExtRef}/>`
      );
      if (resRef) {
        bani_submit = true;
        $form.submit();

        $("body").block({
          message: null,
          overlayCSS: {
            background: "#fff",
            opacity: 0.6,
          },
          css: {
            cursor: "wait",
          },
        });
      } else {
        return false;
      }
    };

    let handler = baniPlugin.init({
      amount: amount,
      phoneNumber: wc_bani_params?.phoneNumber,
      merchantKey: wc_bani_params?.merchantKey,
      ref: wc_bani_params?.txnref,
      email: wc_bani_params?.email,
      firstName: wc_bani_params?.firstName,
      lastName: wc_bani_params?.lastName,
      showUserName: wc_bani_params?.showUserName,
      showUserAccountName: wc_bani_params?.showUserAccountName,
      onClose: (e) => {
        $("#wc-bani-form").show();
        $(this.el).unblock();
        bani_callback(e);
      },
      metadata: wc_bani_params?.metadata,
      callback: bani_callback,
    });
    handler;
    return false;
  }
});
