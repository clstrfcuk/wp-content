<?php

// =============================================================================
// VIEWS/ADMIN/TAB-MAIN-METABOXES.PHP
// -----------------------------------------------------------------------------
// Main content used for general settings and provider settings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Metaboxes
// =============================================================================

// Metaboxes
// =============================================================================

?>

<div id="post-body-content">

  <?php if ( isset( $meta_boxes ) && ! empty( $meta_boxes ) ) : ?>

    <div class="meta-box-sortables ui-sortable">

      <?php foreach ( $meta_boxes as $key => $meta_box ) : ?>

        <div id="meta-box-settings-<?php echo $current_tab; ?>-<?php echo $key; ?>" class="postbox"<?php echo $meta_box['hide'] ? ' style="display: none;"' : ''; ?>>
          <div class="handlediv" title="<?php _e( 'Click to toggle', '__x__' ); ?>"><br></div>
          <h3 class="hndle"><span><?php _e( $meta_box['title'], '__x__' ); ?></span></h3>
          <div class="inside">
            <?php echo $meta_box['content']; ?>
          </div>
        </div>

      <?php endforeach; ?>

    </div>

  <?php endif; ?>

</div>
