<?php
/*
Plugin Name: PromptPay
Plugin URI: https://wordpress.org/plugins/promptpay/
Description: PromptPay integration for WordPress
Version: 1.0.0
Author: Nathachai Thongniran
Author URI: http://jojoee.com/
Text Domain: ppy
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'PPY_BASE_FILE', plugin_basename( __FILE__ ) );
define( 'PPY_PLUGIN_NAME', 'PromptPay' );

// hack
// @todo refactor
class PromptPayFieldKey {
  public function __construct() {
    $this->field_promptpay_id = 'field_promptpay_id';
  }
}

class PromptPay {

  public function __construct() {
    $this->is_debug           = true;
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
  }

  /** ================================================================ shortcode
   */

  public function shortcode_qrcode() {
    $options      = $this->options;
    $promptpay_id = $options['field_promptpay_id'];
    $amount       = 0;
    $html         = sprintf( '<div class="ppy-card" data-promptpay-id="%s" data-amount="%f"></div>',
      $promptpay_id,
      $amount
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
    add_settings_field(
      $this->field_key->field_promptpay_id,
      'PromptPay ID',
      array( $this, 'field_promptpay_id_callback' ),
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
    //   'field_promptpay_id' => ''
    // ]

    $options = $this->options;

    if ( ! isset( $options[$this->field_key->field_promptpay_id] ) ) {
      $options[$this->field_key->field_promptpay_id] = '';
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
    );
    foreach ( $text_input_ids as $text_input_id ) {
      $result[ $text_input_id ] = isset( $input[ $text_input_id ] )
        ? sanitize_text_field( $input[ $text_input_id ] )
        : '';
    }

    return $result;
  }

  public function print_section_info() {
    printf('%s<br>%s<pre>%s</pre>',
      'Enter your settings below',
      'and using display it by shortcode',
      '[promptpayqr]'
    );
  }

  /** ================================================================ backend: field
   */

  public function field_promptpay_id_callback() {
    $field_id    = $this->field_key->field_promptpay_id;
    $field_name  = $this->option_field_name . "[$field_id]";
    $field_value = $this->options[ $field_id ];

    printf( '<input type="text" id="%s" placeholder="PromptPay ID" name="%s" value="%s" />',
      $field_id,
      $field_name,
      $field_value
    );
    printf( '<br><span class="ppy-input-desc">e.g. 1234567891234, 0841234567</span>' );
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
