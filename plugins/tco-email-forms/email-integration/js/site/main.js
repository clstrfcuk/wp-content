// =============================================================================
// EMAIL-INTEGRATION/JS/SITE/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin site scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Handle Form Submissions
// =============================================================================

// Handle Form Submissions
// =============================================================================

jQuery(document).ready(function($) {

  function confirm_subscription( form ) {

    var confirm_type = form.data('x-email-confirm');

    if ( confirm_type === 'Message' ) {
      make_alert( form.data('x-email-message'), 'x-alert-success' ).appendTo(form);
    }

    if ( confirm_type === 'Redirect' ) {
      window.location.href = form.data('x-email-redirect');
    }

  }

  function make_alert( content, class_name ) {

    return $('<div class="x-subscribe-form-alert-wrap">' +
               '<div class="x-alert ' + ( class_name || 'x-alert-danger' ) + ' fade in man">' +
                 '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                 content +
               '</div>' +
             '</div>').alert();

  }

  function nameIntoArray( name, value ) {
    if ( typeof name === 'object' ) {
      return  nameIntoArray( name, value );
    }
    return value;
  }


  $('.x-subscribe-form').submit(function(e) {

    //
    // Prevent default behavior.
    //

    e.preventDefault();

    //
    // Remove old alerts
    //

    $('.x-subscribe-form-alert-wrap').remove();

    //
    // Make note of our form.
    //

    $form = $(this);


    //
    // Prevent extra submissions.
    //

    $form.find('input[type="submit"]').prop('disabled', true).addClass('btn-muted');


    //
    // Craft data for AJAX request.
    //

    var data = $form.serializeArray().reduce(function(obj, item) {

      var iname = item.name.match(/\[(.*?)\]/g);
      $.each(iname,  function ( index, value ) {
        iname[ index ] = value.replace('[', '').replace(']', '').replace('-', '_');
      });

      switch ( iname.length ) {

        case 4:
          if ( typeof obj[ iname[0] ] === 'undefined' ) {
            obj[ iname[0] ] = {};
          }
          if ( typeof obj[ iname[0] ][ iname[1] ] === 'undefined' ) {
            obj[ iname[0] ][ iname[1] ] = {};
          }
          if ( typeof obj[ iname[0] ][ iname[1] ] === 'undefined' ) {
            obj[ iname[0][ iname[1] ][ iname[2] ] ] = iname[3] === '' ? [] : {};
          }
          if ( iname[3] === '' ) {
            obj[ iname[0] ][ iname[1] ][ iname[2] ].push( item.value || '' );
          } else {
            obj[ iname[0] ][ iname[1] ][ iname[2] ][ iname[3] ]= item.value || '';
          }
          break;

        case 3:
          if ( typeof obj[ iname[0] ] === 'undefined' ) {
            obj[ iname[0] ] = {};
          }
          if ( typeof obj[ iname[0] ][ iname[1] ] === 'undefined' ) {
            obj[ iname[0] ][ iname[1] ] = iname[2] === '' ? [] : {};
          }
          if ( iname[2] === '' ) {
            obj[ iname[0] ][ iname[1] ].push( item.value || '' );
          } else {
            obj[ iname[0] ][ iname[1] ][ iname[2] ]= item.value || '';
          }
          break;

        case 2:
          if ( typeof obj[ iname[0] ] === 'undefined' ) {
            obj[ iname[0] ] = iname[1] === '' ? [] : {};
          }
          if ( iname[1] === '' ) {
            obj[ iname[0] ].push( item.value || '' );
          } else {
            obj[ iname[0] ][ iname[1] ]= item.value || '';
          }
          break;

        case 1:
          if (iname[0] === 'id') {
            iname[0] = 'form_id';
          }
          if (iname[0] === 'email') {
            iname[0] = 'email_address';
          }
          obj[ iname[0] ] = item.value || '';
          break;
      }

      return obj;
    }, {});

    postdata = {
      action : 'x_subscribe',
      data   : data
    };

    //
    // Submit form.
    //

    $.post(email_forms.ajax_url, postdata, function(response) {
      data = $.parseJSON(response);
      if ( data.error ) {
        make_alert(data.message).appendTo($form);
        $form.find('input[type="submit"]').prop('disabled', false).removeClass('btn-muted');
      } else {
        confirm_subscription($form);
      }
    });

  });

});
