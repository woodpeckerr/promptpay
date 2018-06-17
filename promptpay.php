<?php
/*
Plugin Name: PromptPay
Plugin URI: https://wordpress.org/plugins/promptpay/
Description: PromptPay integration for WordPress
Version: 1.2.2
Author: Nathachai Thongniran
Author URI: http://jojoee.com/
Text Domain: ppy
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

define( 'PPY_BASE_FILE', plugin_basename( __FILE__ ) );
define( 'PPY_PLUGIN_NAME', 'PromptPay' );

// hack
// @todo refactor
class PromptPayFieldKey {
  public function __construct() {
    $this->field_promptpay_id        = 'field_promptpay_id';
    $this->field_show_promptpay_logo = 'field_show_promptpay_logo';
    $this->field_show_promptpay_id   = 'field_show_promptpay_id';
    $this->field_account_name        = 'field_account_name';
    $this->field_shop_name           = 'field_shop_name';
  }
}

class PromptPay {

  public function __construct() {
    $this->is_debug           = false;
    $this->menu_page          = 'promptpay';
    $this->option_group_name  = 'ppy_option_group';
    $this->option_field_name  = 'ppy_option_field';
    $this->setting_section_id = 'ppy_setting_section_id';

    $this->field_key = new PromptPayFieldKey();
    $this->options   = get_option( $this->option_field_name );

    // set default prop
    // for only
    // - first time
    // - no submitting form
    $this->set_default_prop();

    // backend: menu
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );

    // backend: plugin
    add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 4 );

    // script
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    // shortcode
    add_shortcode( 'promptpayqr', array( $this, 'shortcode_qrcode' ) );

    // WooCommerce
    add_action( 'woocommerce_order_details_after_order_table', array( $this, 'order_details_after_order_table' ) );
  }

  /** ================================================================ WooCommerce
   */

  /**
   * @todo refactor
   *
   * @param WC_Order $order
   */
  public function order_details_after_order_table( $order ) {
    $payment_method = $order->get_payment_method();
    $total          = $order->get_total();
    if ( $payment_method === 'bacs' ) {
      // temporary
      echo $this->shortcode_qrcode();
    }
  }

  /** ================================================================ shortcode
   */

  /**
   * @see https://codex.wordpress.org/Shortcode_API
   *
   * @param array $atts
   *
   * @return array|string
   */
  public function shortcode_qrcode( $atts = [] ) {
    $options = $this->options;

    // custom param
    $custom = shortcode_atts( array(
      'id'     => $options[ $this->field_key->field_promptpay_id ],
      'amount' => 0
    ), $atts );

    $html = sprintf( '<div class="ppy-card"
      data-promptpay-id="%s"
      data-amount="%f"
      data-show-promptpay-logo="%s"
      data-show-promptpay-id="%s"
      data-account-name="%s"
      data-shop-name="%s"
      data-card-style="%s"
      ></div>',
      $custom['id'],
      $custom['amount'],
      $options[ $this->field_key->field_show_promptpay_logo ],
      $options[ $this->field_key->field_show_promptpay_id ],
      $options[ $this->field_key->field_account_name ],
      $options[ $this->field_key->field_shop_name ],
      1
    );

    return $html;
  }

  /** ================================================================ backend: menu
   */

  public function admin_menu() {
    add_options_page(
      PPY_PLUGIN_NAME,
      PPY_PLUGIN_NAME,
      'manage_options',
      $this->menu_page,
      array( $this, 'admin_page' )
    );
  }

  public function admin_page() {
    $this->dump(); ?>
    <div class="wrap">
      <h1><?= PPY_PLUGIN_NAME; ?></h1>
      <form method="post" action="options.php">
        <?php
        settings_fields( $this->option_group_name );
        do_settings_sections( $this->menu_page );
        submit_button();
        ?>
      </form>
    </div>
    <style>
    <?php
  }

  public function admin_init() {
    register_setting(
      $this->option_group_name,
      $this->option_field_name,
      array( $this, 'sanitize' )
    );

    // section
    add_settings_section(
      $this->setting_section_id,
      'Settings',
      array( $this, 'print_section_info' ),
      $this->menu_page
    );

    // option field(s)
    // - field_promptpay_id
    // - field_show_promptpay_logo
    // - field_show_promptpay_id
    // - field_account_name
    // - field_shop_name
    add_settings_field(
      $this->field_key->field_promptpay_id,
      'PromptPay ID',
      array( $this, $this->field_key->field_promptpay_id . '_callback' ),
      $this->menu_page,
      $this->setting_section_id
    );
    add_settings_field(
      $this->field_key->field_show_promptpay_logo,
      'Show PromptPay logo',
      array( $this, $this->field_key->field_show_promptpay_logo . '_callback' ),
      $this->menu_page,
      $this->setting_section_id
    );
    add_settings_field(
      $this->field_key->field_show_promptpay_id,
      'Show PromptPay ID',
      array( $this, $this->field_key->field_show_promptpay_id . '_callback' ),
      $this->menu_page,
      $this->setting_section_id
    );
    add_settings_field(
      $this->field_key->field_account_name,
      'Account name',
      array( $this, $this->field_key->field_account_name . '_callback' ),
      $this->menu_page,
      $this->setting_section_id
    );
    add_settings_field(
      $this->field_key->field_shop_name,
      'Shop name',
      array( $this, $this->field_key->field_shop_name . '_callback' ),
      $this->menu_page,
      $this->setting_section_id
    );
  }

  /** ================================================================ backend: page
   */

  /** ================================================================ backend: field
   */

  public function set_default_prop() {
    // default
    // [
    //   'field_promptpay_id'         => ''
    //   'field_show_promptpay_logo'  => 1
    //   'field_show_promptpay_id'    => 1
    //   'field_account_name'         => ''
    //   'field_shop_name'            => ''
    // ]
    $options = $this->options;

    if ( ! isset( $options[ $this->field_key->field_promptpay_id ] ) ) {
      $options[ $this->field_key->field_promptpay_id ] = '';
    }

    if ( ! isset( $options[ $this->field_key->field_show_promptpay_logo ] ) ) {
      $options[ $this->field_key->field_show_promptpay_logo ] = 1;
    }

    if ( ! isset( $options[ $this->field_key->field_show_promptpay_id ] ) ) {
      $options[ $this->field_key->field_show_promptpay_id ] = 1;
    }

    if ( ! isset( $options[ $this->field_key->field_account_name ] ) ) {
      $options[ $this->field_key->field_account_name ] = '';
    }

    if ( ! isset( $options[ $this->field_key->field_shop_name ] ) ) {
      $options[ $this->field_key->field_shop_name ] = '';
    }

    $this->options = $options;
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   *
   * @return array[]
   */
  public function sanitize( $input ) {
    $result = array();

    // text
    $text_input_ids = array(
      $this->field_key->field_promptpay_id,
      $this->field_key->field_account_name,
      $this->field_key->field_shop_name
    );
    foreach ( $text_input_ids as $text_input_id ) {
      $result[ $text_input_id ] = isset( $input[ $text_input_id ] )
        ? sanitize_text_field( $input[ $text_input_id ] )
        : '';
    }

    // number
    $number_input_ids = array(
      $this->field_key->field_show_promptpay_logo,
      $this->field_key->field_show_promptpay_id
    );
    foreach ( $number_input_ids as $number_input_id ) {
      $result[ $number_input_id ] = isset( $input[ $number_input_id ] )
        ? sanitize_text_field( $input[ $number_input_id ] )
        : 0;
    }

    return $result;
  }

  public function print_section_info() {
    print( 'Enter your settings below and display it by shortcode<br>
      - default settings e.g. <code>[promptpayqr]</code><br>
      - override default settings e.g. <code>[promptpayqr id="1100400404123"]</code>, <code>[promptpayqr amount="50.80"]</code>'
    );
  }

  /** ================================================================ backend: field
   */

  public function field_promptpay_id_callback() {
    $field_id    = $this->field_key->field_promptpay_id;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = $this->options[ $field_id ];

    printf( '<input type="text" id="%s" placeholder="e.g. 1234567891234, 0841234567" name="%s" value="%s" />',
      $field_id,
      $field_name,
      $field_value
    );
  }

  public function field_show_promptpay_logo_callback() {
    $field_id    = $this->field_key->field_show_promptpay_logo;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = 1;
    $check_attr  = checked( 1, $this->options[ $field_id ], false );

    printf(
      '<input type="checkbox" id="%s" name="%s" value="%s" %s />',
      $field_id,
      $field_name,
      $field_value,
      $check_attr
    );
  }

  public function field_show_promptpay_id_callback() {
    $field_id    = $this->field_key->field_show_promptpay_id;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = 1;
    $check_attr  = checked( 1, $this->options[ $field_id ], false );

    printf(
      '<input type="checkbox" id="%s" name="%s" value="%s" %s />',
      $field_id,
      $field_name,
      $field_value,
      $check_attr
    );
  }

  public function field_account_name_callback() {
    $field_id    = $this->field_key->field_account_name;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = $this->options[ $field_id ];

    printf( '<input type="text" id="%s" placeholder="Account name" name="%s" value="%s" />',
      $field_id,
      $field_name,
      $field_value
    );
  }

  public function field_shop_name_callback() {
    $field_id    = $this->field_key->field_shop_name;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = $this->options[ $field_id ];

    printf( '<input type="text" id="%s" placeholder="Shop name" name="%s" value="%s" />',
      $field_id,
      $field_name,
      $field_value
    );
  }

  /** ================================================================ backend: plugin
   */

  /**
   * @param string[] $links
   * @param string $plugin_file
   *
   * @return array
   */
  public function plugin_action_links( $links = [], $plugin_file = '' ) {
    $plugin_link = array();
    if ( $plugin_file === PPY_BASE_FILE ) {
      $plugin_link[] = sprintf( '<a href="%s">%s</a>',
        admin_url( 'options-general.php?page=' . $this->menu_page ),
        'Settings'
      );
    }

    return array_merge( $links, $plugin_link );
  }

  /** ================================================================ debug
   */

  /**
   * @param null $var
   * @param bool $is_die
   *
   * @return bool
   */
  private function dd( $var = null, $is_die = true ) {
    if ( ! $this->is_debug ) {
      return false;
    } else {
      echo '<pre>';
      print_r( $var );
      echo '</pre>';

      if ( $is_die ) {
        die();
      }
    }
  }

  private function da( $var = null, $is_die = false ) {
    $this->dd( $var, $is_die );
  }

  private function dump( $is_die = false ) {
    $this->da( $this->options, $is_die );
  }

  private function reset() {
    update_option( $this->option_field_name, array() );
  }

  /** ================================================================ frontend
   */

  public function enqueue_scripts() {
    wp_enqueue_style( 'ppy-main-style', plugins_url( 'css/main.css', __FILE__ ) );
    wp_enqueue_script( 'ppy-main-script', plugins_url( 'js/main.min.js', __FILE__ ), array( 'jquery' ) );
  }
}

$prompt_pay = new PromptPay();
