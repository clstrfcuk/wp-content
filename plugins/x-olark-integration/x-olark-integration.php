<?php

/*

Plugin Name: X &ndash; Olark Integration
Plugin URI: http://theme.co/x/
Description: Sign up for an Olark account and experience the easiest way to boost your sales, help solve issues, and understand your customers with live chat.
Version: 1.0.0
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-olark-integration

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   02. Setup Menu
//   03. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

define( 'X_OLARK_INTEGRATION_VERSION', '1.0.0' );
define( 'X_OLARK_INTEGRATION_URL', plugins_url( '', __FILE__ ) );
define( 'X_OLARK_INTEGRATION_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$x_olark_integration_options = array();



// Setup Menu
// =============================================================================

function x_olark_integration_options_page() {
  require( 'views/admin/options-page.php' );
}

function x_olark_integration_menu() {
  add_submenu_page( 'x-addons-home', __( 'Olark Integration', '__x__' ), __( 'Olark Integration', '__x__' ), 'manage_options', 'x-extensions-olark-integration', 'x_olark_integration_options_page' );
}

add_action( 'admin_menu', 'x_olark_integration_menu', 100 );



// Initialize
// =============================================================================

function x_olark_integration_init() {

  //
  // Textdomain.
  //

  load_plugin_textdomain( '__x__', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );


  //
  // Styles and scripts.
  //

  require( 'functions/enqueue/styles.php' );
  require( 'functions/enqueue/scripts.php' );


  //
  // Notices.
  //

  require( 'functions/notices.php' );


  //
  // Output.
  //

  require( 'functions/output.php' );

}

add_action( 'init', 'x_olark_integration_init' );