<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Olark Integration
//   02. Output
// =============================================================================

// Olark Integration
// =============================================================================

function x_olark_integration_output() {

  require( X_OLARK_INTEGRATION_PATH . '/views/site/olark-integration.php' );

}



// Output
// =============================================================================

require( X_OLARK_INTEGRATION_PATH . '/functions/options.php' );

if ( isset( $x_olark_integration_enable ) && $x_olark_integration_enable == 1 ) {

  add_action( 'wp_footer', 'x_olark_integration_output' );

}