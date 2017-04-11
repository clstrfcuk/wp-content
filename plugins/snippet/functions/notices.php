<?php

// =============================================================================
// FUNCTIONS/NOTICES.PHP
// -----------------------------------------------------------------------------
// Plugin notices.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Notices
//   02. Adding Actions
// =============================================================================

// Notices
// =============================================================================

function snippet_admin_notices() { ?>

  <?php if ( isset( $_POST['snippet_form_submitted'] ) ) : ?>
    <?php if ( sanitize_text_field( $_POST['snippet_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) : ?>

      <div class="updated">
        <p><?php _e( '<strong>Huzzah!</strong> All settings have been successfully saved.', '__x__' ); ?></p>
      </div>

    <?php endif; ?>
  <?php endif; ?>

<?php }



// Adding Actions
// =============================================================================

add_action( 'admin_notices', 'snippet_admin_notices' );
