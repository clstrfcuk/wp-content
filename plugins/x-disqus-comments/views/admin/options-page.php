<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE.PHP
// -----------------------------------------------------------------------------
// Plugin options page.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Permissions Check
//   02. Require Options
//   03. Options Page Output
// =============================================================================

// Permissions Check
// =============================================================================

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( 'You do not have sufficient permissions to access this page.' );
}



// Require Options
// =============================================================================

require( X_DISQUS_COMMENTS_PATH . '/functions/options.php' );



// Options Page Output
// =============================================================================

$x_disqus_comments_post_types_list = array();
$post_types_list = get_post_types(
  array(
    'public'  => true,
    'show_ui' => true
  ),
  'object'
);
foreach ( $post_types_list as $post_type ) {
  if ( $post_type->name !== 'attachment' ) {
    $x_disqus_comments_post_types_list[ $post_type->name ] = $post_type->label;
  }
}

?>

<div class="wrap x-plugin x-disqus-comments">
  <h2><?php _e( 'Disqus Comments', '__x__' ); ?></h2>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <form name="x_disqus_comments_form" method="post" action="">
        <input name="x_disqus_comments_form_submitted" type="hidden" value="submitted">

        <?php require( 'options-page-main.php' ); ?>
        <?php require( 'options-page-sidebar.php' ); ?>

      </form>
    </div>
    <br class="clear">
  </div>
</div>
