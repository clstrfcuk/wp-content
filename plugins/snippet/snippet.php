<?php

/*

Plugin Name: Snippet
Plugin URI: http://theme.co/x/
Description: Add Schema.Org for SEO improvement
Version: 1.0.3
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: snippet

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   03. Setup Menu
//   04. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

if (!defined('SNIPPET_VERSION')) define( 'SNIPPET_VERSION', '1.0.3' );
if (!defined('SNIPPET_IS_LOADED')) define( 'SNIPPET_IS_LOADED', true );
if (!defined('SNIPPET_URL')) define( 'SNIPPET_URL', plugins_url( '', __FILE__ ) );
if (!defined('SNIPPET_PATH')) define( 'SNIPPET_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

GLOBAL $snippet;



// Setup Menu
// =============================================================================

if ( ! function_exists( 'snippet_admin_menu' ) ) {

  function snippet_admin_menu() {
    GLOBAL $snippet;
    add_submenu_page( 'x-addons-home', __( 'Snippet', '__x__' ), __( 'Snippet', '__x__' ), 'manage_options', 'x-extensions-snippet', array( $snippet, 'admin_controller' ) );
  }

}

add_action( 'admin_menu', 'snippet_admin_menu', 100 );



// Initialize
// =============================================================================

require( SNIPPET_PATH . '/functions/framework/init.php' );
require( SNIPPET_PATH . '/functions/plugin.php' );

$snippet = new Snippet( __FILE__, 'snippet' );
