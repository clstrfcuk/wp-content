<?php

// =============================================================================
// VIEWS/SITE/FACEBOOK-COMMENTS.PHP
// -----------------------------------------------------------------------------
// Plugin site output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Options
//   02. Output
// =============================================================================

// Require Options
// =============================================================================

require( X_FACEBOOK_COMMENTS_PATH . '/functions/options.php' );



// Output
// =============================================================================

$data_mobile = wp_is_mobile() ? true : false;

?>

<div id="comments" class="x-comments-area">
  <div class="fb-comments" data-href="<?php the_permalink(); ?>" data-numposts="<?php echo $x_facebook_comments_number_posts; ?>" data-colorscheme="<?php echo $x_facebook_comments_color_scheme; ?>" data-order-by="<?php echo $x_facebook_comments_order_by; ?>" data-width="100%" data-mobile="<?php echo $data_mobile; ?>"></div>
</div>