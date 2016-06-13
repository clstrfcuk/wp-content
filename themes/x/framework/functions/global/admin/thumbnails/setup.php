<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/THUMBNAILS/SETUP.PHP
// -----------------------------------------------------------------------------
// Sets up entry thumbnail sizes based on Customizer options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Path
//   02. Require Files
//	 03. Disable WordPress 4.4 Responsive Images
// =============================================================================

// Set Path
// =============================================================================

$thmb_path = X_TEMPLATE_PATH . '/framework/functions/global/admin/thumbnails';



// Require Files
// =============================================================================

require_once( $thmb_path . '/width.php' );
require_once( $thmb_path . '/height.php' );


// Disable WordPress 4.4 Responsive Images
// =============================================================================

function x_disable_srcset( $source) {
	return false;
}
add_filter( 'wp_calculate_image_srcset', 'x_disable_srcset' );






