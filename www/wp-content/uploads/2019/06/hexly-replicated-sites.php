<?php 

/**
 * Plugin Name: Hexly - Replicated Sites
 * Plugin URI: https://hexly.io/
 * Description: Replicated Sites via Hexly 
 * Author: Hexly, LLC
 * Author URI: https://hexly.io/
 * Version: 0.0.2
 *
 */


/**
 * Notes: Needs .htaccess =>  RewriteRule ^^store/([a-zA-Z0-9]+)/(.*) /$matches[2] [QSA,L]
 */

require 'plugin-update-checker/plugin-update-checker.php';

define('HEXLY_STORE_PREFIX', 'store');
define('HEXLY_STORE_PATTERN', '/' . HEXLY_STORE_PREFIX . '\\/([a-zA-Z0-9_]+)(.*)/');
define('HEXLY_STORE_COOKIE', 'hexly_slug');

// register our plugin when activated, and verify we've got it
register_activation_hook( __FILE__, 'hexly_replicated_activate' );
function hexly_replicated_activate() {
  if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }  
  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'Hexly' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WordPress admin console.
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' 
      . esc_html__( 'This plugin requires the ', 'hexly-replicated' ) 
      . '<a href="' . esc_url( 'https://hexly.cloud/' ) . '">Hexly – Common</a>' 
      . esc_html__( ' plugin to be active.', 'hexly-replicated' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  }
  include_once('inc/db.php');
  include_once('inc/replicated.php');
  try {
    HexlyReplicatedDb::getInstance()->install();
  }catch(Exception $err){
    global $hexly;
    $hexly->log('errored', $err);
    deactivate_plugins( plugin_basename( __FILE__ ) );
    $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' 
      . esc_html__( 'Failed creating database for replicated sites. Please contact support', 'hexly-replicated' );
    die( $error_message ); // WPCS: XSS ok.
  }
}

add_action( 'plugins_loaded', 'hexly_replicated_loaded' );
function hexly_replicated_loaded(){
  include_once('inc/db.php');
  include_once('inc/replicated.php');
}


$UC = Puc_v4_Factory::buildUpdateChecker(
  'https://s3-us-west-2.amazonaws.com/plugins.hexly.cloud/hexly-replicated-update-meta.json',
  __FILE__,
  'hexly-replicated'
);

class ReplicatedSites extends Hexly {
  public $sections;
  public $fields;

  function __construct(){
    $this->sections = array(
      'first_section' => array(
        'id' => 'first_section',
        'title' => 'Common Settings',
        'callback' => array( $this, 'section_callback' ),
        'page' => 'wp_commerce'
      ),
      'second_section' => array(
        'id' => 'second_section',
        'title' => 'Replicated Settings',
        'callback' => array( $this, 'section_callback' ),
        'page' => 'wp_commerce'
      ),
      'third_section' => array(
        'id' => 'third_section',
        'title' => 'Commerce Settings',
        'callback' => array( $this, 'section_callback' ),
        'page' => 'wp_commerce'
      )
    );

    $this->fields = array(
      'field1' => array(
        'id' => 'hexly_base_url',
        'title' => 'Base URL',
        'callback' => array( $this, 'field_callback' ),
        'page' => 'wp_commerce',
        'section' => 'first_section',
        'args'=> array('label'=>'hexly_base_url')
      ),
      'field2' => array(
        'id' => 'hexly_tenant_id',
        'title' => 'Tenant ID',
        'callback' => array( $this, 'field_callback' ),
        'page' => 'wp_commerce',
        'section' => 'first_section',
        'args' => array('label'=>'hexly_tenant_id')
      ),
      'field3' => array(
        'id' => 'hexly_wp_authorization_header',
        'title' => 'Authorization Header',
        'callback' => array( $this, 'field_callback' ),
        'page' => 'wp_commerce',
        'section' => 'first_section',
        'args' => array('label'=>'hexly_wp_authorization_header')
      ),
      'field4' => array(
        'id' => 'hexly_wp_unsent_order_email',
        'title' => 'Unsent Order Email',
        'callback' => array( $this, 'field_callback' ),
        'page' => 'wp_commerce',
        'section' => 'third_section',
        'args' => array('label'=>'hexly_wp_unsent_order_email')
      ),
      'field5' => array(
        'id' => 'hexly_wp_unsent_order_cron_interval',
        'title' => 'Unsent Order Cron Interval (minutes)',
        'callback' => array( $this, 'field_callback' ),
        'page' => 'wp_commerce',
        'section' => 'third_section',
        'args' => array('label'=>'hexly_wp_unsent_order_cron_interval')
      )
    );
  }
}




// TODO Verify we don't actually need this to 
// add_filter('redirect_canonical', 'my_redirect_canonical', 10, 2);
// function my_redirect_canonical($redirect_url, $requested_url) {
//   // if (is_singular('phone') {
//   //   return $requested_url;
//   // } else {
//   //   return $redirect_url;
//   // }
//   error_log('CANNONICAL: ' . print_r(array($redirect_url, $requested_url), true));
//   return $requested_url;
// }