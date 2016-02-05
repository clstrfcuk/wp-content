<?php

/*

Plugin Name: X &ndash; Facebook Comments
Plugin URI: http://theme.co/x/
Description: Take advantage of powerful and unique features by integrating Facebook comments on your website instead of the standard WordPress commenting system.
Version: 1.0.1
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-facebook-comments

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

define( 'X_FACEBOOK_COMMENTS_VERSION', '1.0.1' );
define( 'X_FACEBOOK_COMMENTS_URL', plugins_url( '', __FILE__ ) );
define( 'X_FACEBOOK_COMMENTS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$x_facebook_comments_options = array();



// Setup Menu
// =============================================================================

function x_facebook_comments_options_page() {
  require( 'views/admin/options-page.php' );
}

function x_facebook_comments_menu() {
  add_submenu_page( 'x-addons-home', __( 'Facebook Comments', '__x__' ), __( 'Facebook Comments', '__x__' ), 'manage_options', 'x-extensions-facebook-comments', 'x_facebook_comments_options_page' );
}

add_action( 'admin_menu', 'x_facebook_comments_menu', 100 );



// Initialize
// =============================================================================

function x_facebook_comments_init() {

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

add_action( 'init', 'x_facebook_comments_init' );