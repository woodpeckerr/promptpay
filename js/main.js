const qrcode = require('qrcode');
const generatePayload = require('promptpay-qr');

const opt = {
  type: 'svg'
};

jQuery(document).ready(function () {
  const $body = jQuery('body');
  const isAdmin = $body.hasClass('wp-admin');

  if (isAdmin) {
    // do live edit
  } else {
    const $qrcodes = jQuery('.ppy-qrcode');
    jQuery.each($qrcodes, function () {
      const $ele = jQuery(this);
      /** @type string */
      const id = jQuery(this).data('promptpay-id').toString();
      /** @type number */
      const amount = parseFloat(jQuery(this).data('amount'));

      if (id) {
        const payload = generatePayload(id, {
          amount: 0
        });

        qrcode.toString(payload, opt, function (err, svg) {
          if (err) {
            console.log('err', err);
          } else {
            $ele.html(svg);
          }
        })
      }
    });
  }
});
