<?php

/*

Plugin Name: Email Forms
Plugin URI: http://theme.co/x/
Description: Creating custom opt-in forms has never been this easy...or fun! Carefully craft every detail of your forms using this plugin and subscribe users to a provider email list.
Version: 1.0.3
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: email-forms

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants
//   02. Initialize
// =============================================================================

// Define Constants
// =============================================================================

define( 'EMAIL_FORMS_VERSION', '1.0.3' );
define( 'EMAIL_FORMS_ROOT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


// Initialize
// =============================================================================

//
// Framework. Only loaded once across all email form plugins.
//

if ( ! defined( 'X_EMAIL_INTEGRATION_IS_LOADED' ) ) {
  require( EMAIL_FORMS_ROOT_PATH . '/email-integration/setup.php' );
}


//
// Providers.
//

require( EMAIL_FORMS_ROOT_PATH . '/email-mailchimp/setup.php' );
require( EMAIL_FORMS_ROOT_PATH . '/email-convertkit/setup.php' );
require( EMAIL_FORMS_ROOT_PATH . '/email-getresponse/setup.php' );


//
// Textdomain.
//

function email_forms_textdomain() {
  load_plugin_textdomain( '__x__', false, EMAIL_FORMS_ROOT_PATH . '/lang/' );
}

add_action( 'plugins_loaded', 'email_forms_textdomain' );
