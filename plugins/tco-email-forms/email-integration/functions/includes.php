<?php

// =============================================================================
// EMAIL-INTEGRATION/FUNCTIONS/X-META-FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Load any dependant files for this plugin.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
// =============================================================================

// TABLE OF CONTENTS
// 1. Custom Fields metabox
// 2. Widgets
// 3. Shortcodes
// 4. Email Providers
// 5. Admin
// -----------------------------------------------------------------------------
// =============================================================================


// Custom Fields metabox
// =============================================================================

require_once( $this->path . '/functions/custom-fields-metabox.php' );



// Providers metabox
// =============================================================================

require_once( $this->path . '/../email-mailchimp/functions/metabox.php' );
require_once( $this->path . '/../email-convertkit/functions/metabox.php' );
require_once( $this->path . '/../email-getresponse/functions/metabox.php' );



// Widgets
// =============================================================================

require_once( $this->path . '/functions/widgets/subscribe.php' );



// Shortcodes
// =============================================================================

require_once( $this->path . '/functions/shortcodes/subscribe.php' );



// Email Providers
// =============================================================================

require_once( $this->path . '/functions/email-provider.php' );



// Admin
// =============================================================================

if ( is_admin() ) {
  require_once( $this->path . '/functions/admin/email-forms-list-table.php' );
}
