<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. App Data
//   02. SDK
//   03. Template
//   04. Comments Number
//   05. Output
// =============================================================================

// App Data
// =============================================================================

if ( ! function_exists( 'x_facebook_app_data' ) ) :
  function x_facebook_app_data() {

    require( X_FACEBOOK_COMMENTS_PATH . '/functions/options.php' );

    echo '<meta property="fb:app_id" content="' . $x_facebook_comments_app_id . '">';

  }
endif;



// SDK
// =============================================================================

if ( ! function_exists( 'x_facebook_sdk' ) ) :
  function x_facebook_sdk() {

    require( X_FACEBOOK_COMMENTS_PATH . '/functions/options.php' ); ?>

    <div id="fb-root"></div>

    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $x_facebook_comments_app_id; ?>',
          xfbml   : true,
          version : 'v2.1'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>

  <?php }
endif;



// Template
// =============================================================================

function x_facebook_comments_template() {

  $template = X_FACEBOOK_COMMENTS_PATH . '/views/site/facebook-comments.php';

  return $template;

}



// Comments Number
// =============================================================================

function x_facebook_comments_number() {

  $url     = 'https://graph.facebook.com/v2.1/?fields=share{comment_count}&id=' . urlencode( get_the_permalink() );
  $request = wp_remote_get( $url );
  $data    = json_decode( $request['body'], true );
  $number  = $data['share']['comment_count'];

  return $number;

}



// Output
// =============================================================================

require( X_FACEBOOK_COMMENTS_PATH . '/functions/options.php' );

if ( isset( $x_facebook_comments_enable ) && $x_facebook_comments_enable == 1 ) {

  add_action( 'wp_head', 'x_facebook_app_data' );
  add_action( 'x_before_site_begin', 'x_facebook_sdk' );
  add_filter( 'x_entry_meta_comments_number', 'x_facebook_comments_number' );
  add_filter( 'comments_template', 'x_facebook_comments_template' );

}