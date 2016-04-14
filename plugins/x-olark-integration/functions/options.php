<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $x_olark_integration_options;

if ( isset( $_POST['x_olark_integration_form_submitted'] ) ) {
  if ( strip_tags( $_POST['x_olark_integration_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $x_olark_integration_options['x_olark_integration_enable']  = ( isset( $_POST['x_olark_integration_enable'] ) ) ? strip_tags( $_POST['x_olark_integration_enable'] ) : '';
    $x_olark_integration_options['x_olark_integration_site_id'] = strip_tags( $_POST['x_olark_integration_site_id'] );

    update_option( 'x_olark_integration', $x_olark_integration_options );

  }
}



// Get Options
// =============================================================================

$x_olark_integration_options = apply_filters( 'x_olark_integration_options', get_option( 'x_olark_integration' ) );

if ( $x_olark_integration_options != '' ) {

  $x_olark_integration_enable  = $x_olark_integration_options['x_olark_integration_enable'];
  $x_olark_integration_site_id = $x_olark_integration_options['x_olark_integration_site_id'];

}