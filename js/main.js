const qrcode = require('qrcode');
const generatePayload = require('promptpay-qr');
const Promise = require('promise');

const $body = jQuery('body');
const isAdmin = $body.hasClass('wp-admin');
const isTest = $body.hasClass('ppy-test');
const attrKey = {
  promptpayId: 'promptpay-id',
  amount: 'amount',
  showPromptpayLogo: 'show-promptpay-logo',
  showPromptpayId: 'show-promptpay-id',
  accountName: 'account-name',
  shopName: 'shop-name',
  cardStyle: 'card-style'
};

/**
 * @param {any} e
 */
function dump(e) {
  console.log('========');
  console.log('typeof e', e);
  console.log('e.constructor.name', e.constructor.name);
  console.log('e', e);
}

/** ================================================================ common
 */

/** ================================================================ test
 */

const testIdentityNo = '1100400404123';
const testMobileNumber = '0846612456';
const testItems = [
  // empty
  {},
  // promptpayId: identity no
  {
    promptpayId: testIdentityNo
  },
  // promptpayId: mobile phone
  {
    promptpayId: testMobileNumber
  },
  // amount: integer
  {
    amount: 100.10
  },
  // amount: float
  {
    amount: 100.256478
  },
  // amount: zero
  {
    amount: 0
  },
  // promptpayLogo: true
  {
    showPromptpayLogo: true
  },
  // promptpayLogo: false
  {
    showPromptpayLogo: false
  },
  // showPromptpayId: true
  {
    showPromptpayId: true
  },
  // showPromptpayId: false
  {
    showPromptpayId: false
  },
  // accountName: 'Nathachai Thongniran'
  {
    accountName: 'Nathachai Thongniran'
  },
  // accountName: 'very long'
  {
    accountName: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ipsum laboriosam nam quod vero voluptate. Accusamus autem beatae dolorum ea ipsa ipsam iure magni nostrum obcaecati, quo quos tempora tempore?'
  },
  // accountName: ''
  {
    accountName: ''
  },
  // shopName: 'Jojoee shop'
  {
    shopName: 'Jojoee shop'
  },
  // shopName: ''
  {
    shopName: ''
  },
  // cardStyle: 1
  {
    cardStyle: 1
  },
  // cardStyle: 2
  {
    cardStyle: 2
  },
  // cardStyle: 3
  {
    cardStyle: 3
  },
  // all meta
  {
    showPromptpayId: true,
    accountName: 'Nathachai Thongniran',
    shopName: 'Jojoee shop'
  },
  // all meta (long)
  {
    showPromptpayId: true,
    accountName: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ipsum laboriosam nam quod vero voluptate. Accusamus autem beatae dolorum ea ipsa ipsam iure magni nostrum obcaecati, quo quos tempora tempore?',
    shopName: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium ipsum laboriosam nam quod vero voluptate. Accusamus autem beatae dolorum ea ipsa ipsam iure magni nostrum obcaecati, quo quos tempora tempore'
  },
  // style 1, all (default card style)
  {
    amount: 20.50,
    showPromptpayLogo: true,
    showPromptpayId: true,
    accountName: 'Nathachai Thongniran',
    shopName: 'Jojoee shop'
  },
  // style 1, all
  {
    amount: 20.50,
    showPromptpayLogo: true,
    showPromptpayId: true,
    accountName: 'Nathachai Thongniran',
    shopName: 'Jojoee shop',
    cardStyle: 1
  },
  // style 2, all
  {
    amount: 20.50,
    showPromptpayLogo: true,
    showPromptpayId: true,
    accountName: 'Nathachai Thongniran',
    shopName: 'Jojoee shop',
    cardStyle: 2
  },
  // style 3, all
  {
    amount: 20.50,
    showPromptpayLogo: true,
    showPromptpayId: true,
    accountName: 'Nathachai Thongniran',
    shopName: 'Jojoee shop',
    cardStyle: 3
  },
  // style 1, only qrcode (default card style)
  {

  },
  // style 1, only qrcode
  {
    cardStyle: 1
  },
  // style 2, only qrcode
  {
    cardStyle: 2
  },
  // style 3, only qrcode
  {
    cardStyle: 3
  }
];

/**
 * @see https://stackoverflow.com/questions/11023293/jquery-quickly-dump-objects-members
 * @see https://stackoverflow.com/questions/27168451/jquery-data-not-stored-in-the-dom
 */
function generateTestItems() {
  for (let i = 0; i < testItems.length; i++) {
    const item = testItems[i];
    const opt = new CardOption(
      item.promptpayId || testMobileNumber,
      item.amount,
      item.showPromptpayLogo,
      item.showPromptpayId,
      item.accountName,
      item.shopName,
      item.cardStyle
    );

    // need to using attr
    const $card = jQuery('<div>', {class: 'ppy-card'})
      .attr('data-' + attrKey.promptpayId, opt.promptpayId)
      .attr('data-' + attrKey.amount, opt.amount)
      .attr('data-' + attrKey.showPromptpayLogo, opt.showPromptpayLogo)
      .attr('data-' + attrKey.showPromptpayId, opt.showPromptpayId)
      .attr('data-' + attrKey.accountName, opt.accountName)
      .attr('data-' + attrKey.shopName, opt.shopName)
      .attr('data-' + attrKey.cardStyle, opt.cardStyle);

    $body.append($card);
  }
}

/** ================================================================ backend
 */

/** ================================================================ frontend
 */

class CardOption {
  constructor(
    promptpayId = '',
    amount = 0,
    showPromptpayLogo = false,
    showPromptpayId = false,
    accountName = '',
    shopName = '',
    cardStyle = 1
  ) {
    // required
    this.promptpayId = promptpayId;

    this.amount = amount;
    this.showPromptpayLogo = showPromptpayLogo;
    this.showPromptpayId = showPromptpayId;
    this.accountName = accountName;
    this.shopName = shopName;
    this.cardStyle = cardStyle;
  }
}

/**
 * @param {string} id
 * @param {number} amount
 * @returns {Promise}
 */
function generateQRCodeSvg(id, amount) {
  const payload = generatePayload(id, {amount: amount});
  return new Promise(function (res, rej) {
    qrcode.toString(payload, {type: 'svg'}, function (err, svg) {
      if (err) rej(err);
      res(svg);
    });
  });
}

/**
 * @todo refactor html template
 * @param {jQuery} $card
 * @param {CardOption} opt
 */
function render($card, opt) {
  // only test
  const $head = jQuery('<pre>', {class: 'ppy-head'})
    .html(JSON.stringify(opt, null, 2));

  // update card style
  $card.addClass('ppy-style' + opt.cardStyle)

  const $qrcode = jQuery('<div>', {class: 'ppy-qrcode'});
  const $logo = jQuery('<div>', {class: 'ppy-logo'});
  const $meta = jQuery('<div>', {class: 'ppy-meta'});
  let metaHtml = '';

  generateQRCodeSvg(opt.promptpayId, opt.amount)
    .then(function (svg) {
      // head (test)
      $card.append($head);

      // logo
      if (opt.showPromptpayLogo) {
        $card.append($logo);
      }

      // qrcode
      $qrcode.html(svg);
      $card.append($qrcode);

      // promptpay id
      if (opt.showPromptpayId) {
        metaHtml += '<span class="ppy-meta-line">PromptPay ID: ' + opt.promptpayId + '</span>';
      }

      // account name
      if (opt.accountName) {
        metaHtml += '<span class="ppy-meta-line">Account name: ' + opt.accountName + '</span>';
      }

      // shop name
      if (opt.shopName) {
        metaHtml += '<span class="ppy-meta-line">Shop name: ' + opt.shopName + '</span>';
      }

      if (metaHtml !== '') {
        $meta.html(metaHtml);
        $card.append($meta);
      }
    })
    .catch(function (err) {
      console.log('err', err);
    })
    .finally(function () {
      // https://stackoverflow.com/questions/24059268/removing-all-dynamic-data-attributes-of-an-element
      // remove all data attrs
      jQuery.each($card.data(), function (i) {
        $card.removeAttr('data-' + i);
      });
    })
}

/**
 * @see https://stackoverflow.com/questions/263965/how-can-i-convert-a-string-to-boolean-in-javascript
 * @param {jQuery} $card
 * @returns {CardOption}
 */
function getCardOption($card) {
  return new CardOption(
    $card.data(attrKey.promptpayId).toString(),
    parseFloat($card.data(attrKey.amount)),
    ($card.data(attrKey.showPromptpayLogo) === true),
    ($card.data(attrKey.showPromptpayId) === true),
    $card.data(attrKey.accountName).toString(),
    $card.data(attrKey.shopName).toString(),
    parseInt($card.data(attrKey.cardStyle))
  )
}

function initFrontend() {
  /** @type {jQuery} */
  const $cards = jQuery('.ppy-card');

  // proceed
  jQuery.each($cards, function () {
    const $card = jQuery(this);
    const opt = getCardOption($card);
    if (opt.promptpayId) render($card, opt);
  });
}

jQuery(document).ready(function () {
  if (isTest) {
    generateTestItems();
    initFrontend();
  } else if (isAdmin) {
    initBackend();
  } else {
    initFrontend();
  }
});
