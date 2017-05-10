<?php

// =============================================================================
// VIEWS/SITE/BYPASS.PHP
// -----------------------------------------------------------------------------
// Plugin bypass feature.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output
// =============================================================================

// Output
// =============================================================================
?>
<div id="x-under-construction-bypass">
  <div id="x-under-construction-bypass-toggle">
  <span class="dashicons dashicons-admin-network"></span>
  </div>
  <div id="x-under-construction-bypass-form">
    <input type="password" class="" id="x-under-construction-bypass-password"/>
    <button type="button"><?php _e( 'Login', '__x__'); ?></button>
  </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function($) {

  $('#x-under-construction-bypass-toggle').click( function( e ) {
    e.preventDefault();
    $('#x-under-construction-bypass-form').show();
  });

  $('#x-under-construction-bypass-form button').click( function ( e ) {
    e.preventDefault();
    var pass = $('#x-under-construction-bypass-form input' ).val();
    $( this ).prop( 'disabled', true );
    var data = {
      'action': 'x_under_construction_bypass',
      'x_under_construction_bypass_password': pass,
      'x_under_construction_ajax_nonce': '<?php echo wp_create_nonce( 'x_under_construction_bypass' ) ?>'
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    $.post(
      '<?php echo admin_url( 'admin-ajax.php' ); ?>',
      data,
      function( response ) {
        $('#x-under-construction-bypass-form button').prop( 'disabled', false );
        if ( response == 'error' ) {
          alert( '<?php _e( 'Incorrect Password', '__x__' ) ?>' );
        } else {
          location.reload(true);
        }
      }
    );

  });

});

</script>
