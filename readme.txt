=== PromptPay ===

Contributors: jojoee
Donate link:
Tags: payment, PromptPay, qrcode, woocommerce
Requires at least: 3.0.1
Tested up to: 5.4.2
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

PromptPay integration for WordPress,  contract creator if any

Features:

* Shortcode
* Support all Thai banks
* Support all themes
* Support all layouts / devices
* Integration with BACS WooCommerce
* No tracking

Compatible with all browsers:

* [Google Chrome](https://www.google.com/chrome/) 19+
* [Mozilla Firefox](https://www.mozilla.org/firefox/) 3.6+
* [Safari](http://www.apple.com/safari/) 3+
* [Internet Explorer](https://www.microsoft.com/en-us/download/internet-explorer.aspx) 9+
* [Opera](http://www.opera.com/) 10+

== Installation ==

1. Install the plugin via admin plguins screen or download and upload this plugin into `wp-content/plugins` directory
2. Activate the plugin through the **Plugins** menu in WordPress
3. Setup plugin via `Settings > PromptPay`

== Frequently Asked Questions ==

= How to use it =
activate the plugin on your plugin dashboard (`/wp-admin/plugins.php`)

== Screenshots ==

1. Shortcode (screenshot-1.jpg)

2. WooCommerce (screenshot-2.jpg)

== Upgrade Notice ==

Not available

== Changelog ==

= 1.2.2 (17 Jun 2018) =
* Support "id" attribute for Shortcode
* Support "amount" attribute for Shortcode

= 1.2.0 (13 Sep 2017) =
* Integration with BACS WooCommerce

= 1.1.0 (13 Sep 2017) =
* Add more options

= 1.0.0 (07 Sep 2017) =
* First release

== TODO ==

* [ ] Update screenshot
* [x] Integration with BACS WooCommerce
* [x] Option: promptpay id
* [ ] Option: amount
* [x] Option: show promptpay logo
* [x] Option: show promptpay id
* [x] Option: account name
* [x] Option: shop name
* [x] Option: card style
* [x] Option: integration with WooCommerce
* [x] Shortcode
* [x] Shortcode: amount
* [x] Shortcode: promptpay id
* [ ] Shortcode: show promptpay logo
* [ ] Shortcode: show promptpay id
* [ ] Shortcode: account name
* [ ] Shortcode: shop name
* [ ] Shortcode: 3 card styles
* [ ] Shortcode: bank logo
* [ ] Shortcode: customer logo
* [ ] Pre-fill total (amount) on order-received page (WooCommerce)
* [ ] Create demo shortcode page
* [ ] Localization: Thai
* [ ] Live preview Shortcode on admin page
* [ ] Live preview: debounce when it's changed
* [ ] Live preview: live validate
* [ ] Support on IE 7-8
* [ ] Unit test: PHP
* [ ] Unit test: Javascript
* [ ] E2E test

== Notes (dev) ==

* [WordPress Coding Standards](https://codex.wordpress.org/WordPress_Coding_Standards)
* 2 spaces for indent
* [Repository on Github](https://github.com/woodpeckerr/promptpay)
* `yarn build.watch` to dev
* `yarn build.prod && yarn zip` to build package

= Thank you =
* [WordPress Plugin readme.txt Validator](https://wordpress.org/plugins/about/validator/)
* [Autoprefixer CSS online](https://autoprefixer.github.io/)
* [PHP code syntax check](https://www.piliapp.com/php-syntax-check/)
* [dtinth/promptpay-qr](https://github.com/dtinth/promptpay-qr)
* Image and design from [Digio Merchant QR Generator](https://qr-generator.digio.co.th/)
