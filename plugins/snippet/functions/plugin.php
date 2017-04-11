<?php

// =============================================================================
// FUNCTIONS/PLUGIN.PHP
// -----------------------------------------------------------------------------
// Inherits from base plugin. This is the core plugin class where feature
// specific code is handled.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Class Setup
// =============================================================================

// Class Setup
// =============================================================================

if ( ! class_exists( 'Snippet' ) ) {
  class Snippet extends Snippet_Base {

    //
    // Initialize plugin.
    //

    function init() {

    }


    //
    // Admin setup.
    //

    function admin_init() {

      $this->set_transport( 'plugin_admin_url', 'admin.php?page=x-extensions-snippet' );

    }

    //
    // Load options page.
    //

    function admin_controller() {
      include( $this->path . '/functions/admin/controller.php' );
    }

  }
}
