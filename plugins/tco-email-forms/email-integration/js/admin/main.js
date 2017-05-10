// =============================================================================
// EMAIL-INTEGRATION/JS/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
// 01. General Settings Screen
// 02. Post Type Screen
// 03. Global Plugin Functionality
// 04. Contacts CRUD
// 05. Providers blocks specific
// =============================================================================

// General Settings Screen
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Show/hide new users opt-in list.
  //

  $row_new_users_list = $('#email_forms_opt_in_new_users_list').parents('tr');

  if ( $('input[name="email_forms[opt_in_new_users]"]:checked').val() === 'no' ) {
    $row_new_users_list.hide();
  }

  $('input[name="email_forms[opt_in_new_users]"]').change(function(){
    $row_new_users_list.toggle();
  });

});



// Post Type Screen
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Form: show/hide confirmation method.
  //

  $row_confirm_message  = $('#email_forms_confirmation_message').parents('tr');
  $row_confirm_redirect = $('#email_forms_confirmation_redirect').parents('tr');

  $('input[name="x_meta[email_forms_confirmation_type]"]').click(function() {
    if ( $(this).is(':checked') && $(this).val() === 'Message' ) {
      $row_confirm_message.show();
      $row_confirm_redirect.hide();
    } else {
      $row_confirm_message.hide();
      $row_confirm_redirect.show();
    }
  });

  $('input[name="x_meta[email_forms_confirmation_type]"]:checked').trigger('click');


  //
  // Form: show/hide title display.
  //

  $title_row = $('#email_forms_title').parents('tr');

  if ( $('input[name="x_meta[email_forms_show_title]"]:checked').val() === 'No' ) {
    $title_row.hide();
  }

  $('input[name="x_meta[email_forms_show_title]"]').change(function() {
    $title_row.toggle();
  });


  //
  // Form: show/hide name display.
  //

  $name_select    = $('#email_forms_name_display');
  $row_full_name  = $('#email_forms_full_name_placeholder').parents('tr');
  $row_first_name = $('#email_forms_first_name_placeholder').parents('tr');
  $row_last_name  = $('#email_forms_last_name_placeholder').parents('tr');

  toggle_name_placeholder_fields( $name_select.val() );

  $name_select.change(function() {
    toggle_name_placeholder_fields( $(this).val() );
  });

  function toggle_name_placeholder_fields( setting ) {
    switch( setting ) {
      case 'None':
        $row_first_name.hide();
        $row_last_name.hide();
        $row_full_name.hide();
        break;
      case 'First':
        $row_first_name.show();
        $row_last_name.hide();
        $row_full_name.hide();
        break;
      case 'Last':
        $row_first_name.hide();
        $row_last_name.show();
        $row_full_name.hide();
        break;
      case 'Full (Separate)':
        $row_first_name.show();
        $row_last_name.show();
        $row_full_name.hide();
        break;
      case 'Full (Combined)':
        $row_first_name.hide();
        $row_last_name.hide();
        $row_full_name.show();
        break;
    }
  }


  //
  // Form: show/hide label visibility.
  //

  $label_rows = $('#email_forms_email_label, #email_forms_first_name_label, #email_forms_last_name_label, #email_forms_full_name_label').parents('tr');

  if ( $('input[name="x_meta[email_forms_show_labels]"]:checked').val() === 'No' ) {
    $label_rows.hide();
  }

  $('input[name="x_meta[email_forms_show_labels]"]').change(function() {
    $label_rows.toggle();
  });


  //
  // Appearance (General): show/hide custom styling meta boxes.
  //

  $appearance_meta_boxes = $('#email-forms-appearance-form-container, #email-forms-appearance-form');

  if ( $('input[name="x_meta[email_forms_custom_styling]"]:checked').val() === 'No' ) {
    $appearance_meta_boxes.hide();
  }

  $('input[name="x_meta[email_forms_custom_styling]"]').change(function() {
    $appearance_meta_boxes.toggle();
  });


  //
  // Appearance (Form Container): show/hide background options.
  //

  $bg_select           = $('#email_forms_bg_option');
  $row_bg_color        = $('#email_forms_bg_color').parents('tr');
  $row_bg_pattern      = $('#email_forms_bg_pattern').parents('tr');
  $row_bg_image        = $('#email_forms_bg_image').parents('tr');
  $row_bg_parallax     = $('input[name="x_meta[email_forms_bg_parallax]"][value="Yes"]').parents('tr');
  $row_bg_video        = $('#email_forms_bg_video').parents('tr');
  $row_bg_video_poster = $('#email_forms_bg_video_poster').parents('tr');

  toggle_background_option_fields( $bg_select.val() );

  $bg_select.change(function() {
    toggle_background_option_fields( $(this).val() );
  });

  function toggle_background_option_fields( setting ) {
    switch( setting ) {
      case 'Transparent':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Color':
        $row_bg_color.show();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Pattern':
        $row_bg_color.hide();
        $row_bg_pattern.show();
        $row_bg_image.hide();
        $row_bg_parallax.show();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Image':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.show();
        $row_bg_parallax.show();
        $row_bg_video.hide();
        $row_bg_video_poster.hide();
        break;
      case 'Video':
        $row_bg_color.hide();
        $row_bg_pattern.hide();
        $row_bg_image.hide();
        $row_bg_parallax.hide();
        $row_bg_video.show();
        $row_bg_video_poster.show();
        break;
    }
  }


  //
  // Appearance (Form): show/hide button colors.
  //

  $button_style_select     = $('#email_forms_button_style');
  $row_button_text         = $('#email_forms_button_text_color').parents('tr');
  $row_button_bg           = $('#email_forms_button_bg_color').parents('tr');
  $row_button_border       = $('#email_forms_button_border_color').parents('tr');
  $row_button_bottom       = $('#email_forms_button_bottom_color').parents('tr');
  $row_button_text_hover   = $('#email_forms_button_text_color_hover').parents('tr');
  $row_button_bg_hover     = $('#email_forms_button_bg_color_hover').parents('tr');
  $row_button_border_hover = $('#email_forms_button_border_color_hover').parents('tr');
  $row_button_bottom_hover = $('#email_forms_button_bottom_color_hover').parents('tr');

  toggle_button_color_fields( $button_style_select.val() );

  $button_style_select.change(function() {
    toggle_button_color_fields( $(this).val() );
  });

  function toggle_button_color_fields( setting ) {
    switch( setting ) {
      case '3D':
        $row_button_text.show();
        $row_button_bg.show();
        $row_button_border.show();
        $row_button_bottom.show();
        $row_button_text_hover.show();
        $row_button_bg_hover.show();
        $row_button_border_hover.show();
        $row_button_bottom_hover.show();
        break;
      case 'Flat':
        $row_button_text.show();
        $row_button_bg.show();
        $row_button_border.show();
        $row_button_bottom.hide();
        $row_button_text_hover.show();
        $row_button_bg_hover.show();
        $row_button_border_hover.show();
        $row_button_bottom_hover.hide();
        break;
      case 'Transparent':
        $row_button_text.show();
        $row_button_bg.hide();
        $row_button_border.show();
        $row_button_bottom.hide();
        $row_button_text_hover.show();
        $row_button_bg_hover.hide();
        $row_button_border_hover.show();
        $row_button_bottom_hover.hide();
        break;
    }
  }


  //
  // Strip ID from email form option values.
  //

  $('#email_forms_list option').each(function(i, el) {
    item    = $(el);
    val     = item.val();
    content = val.split('**', 2);
    item.text(content[1]);
    item.val(val);
  });

});



// Global Plugin Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Accordion.
  //

  $('.accordion > .toggle').click(function() {
    var $this = $(this);
    if ( $this.hasClass('active') ) {
      $this.removeClass('active').next().slideUp();
    } else {
      $('.accordion > .panel').slideUp();
      $this.siblings().removeClass('active');
      $this.addClass('active').next().slideDown();
      return false;
    }
  });


  //
  // Save button.
  //

  $('#submit').click(function() {
    $(this).addClass('saving').val('Updating');
  });


  //
  // Color picker.
  //

  $('.wp-color-picker').wpColorPicker();


  //
  // Datepicker.
  //

  $('.datepicker').datepicker();


  //
  // Meta box toggle.
  //

  postboxes.add_postbox_toggles(pagenow);

});



// Contacts CRUD
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Field list
  //

  var email_forms_custom_fields_template_fields = [
    'name',
    'type',
    'label',
    'choices'
  ];

  //
  // Load function
  //

 function email_forms_custom_fields_load() {
    if ( typeof email_forms_custom_fields === 'undefined' ) {
      email_forms_custom_fields_watch();
      return;
    }

    var table = $( '#email-forms-custom-fields-list tbody' );
    table.html( '' );

    if ( email_forms_custom_fields.length === 1 && typeof email_forms_custom_fields[0].name === 'undefined' ) {
      email_forms_custom_fields.splice( 0, 1 );
    }

    if ( email_forms_custom_fields.length === 0 ) {
      var template = $( '#email-forms-custom-fields-empty-template' ).text();
      table.append( template );
      email_forms_custom_fields_watch();
      return;
    }

    email_forms_custom_fields.map( function ( custom_field, index ) {
      var template = $( '#email-forms-custom-fields-template' ).text();
      template = template.replace( /{id}/g, index );
      Object.keys( custom_field ).map( function ( key ) {
        var value = typeof custom_field[ key ] === 'object' || typeof custom_field[ key ] === 'array'
          ? custom_field[ key ].join('|')
          : custom_field[ key ];
        var value_label = ( value.length > 200 )
          ? value.split('|').join(' | ').substr(0, 200) + '...'
          : value;
        var search = new RegExp( '{' + key + '}',"g" );
        var search_label = new RegExp( '{' + key + '_label}',"g" );
        template = template.replace( search, value );
        template = template.replace( search_label, value_label );
      });

      table.append( template );
    });

    email_forms_custom_fields_watch();
  }

  $('#email_forms_list').change( function() {
    email_forms_update_custom_fields_options();
  });

  function email_forms_update_custom_fields_options() {
    var list_id = $('#email_forms_list').val();
    if (typeof list_id === 'undefined') {
      return;
    }
    list_id = list_id.split('**');
    list_id = list_id[0];
    $("#email-forms-custom-fields-add-select option").remove();
    if ( typeof email_forms_lists[ list_id ] !== 'undefined') {
      var option = new Option('-- Select field --', '');
      $('#email-forms-custom-fields-add-select').append($(option));
      $.each( email_forms_lists[ list_id ]['custom_fields'], function( index, opt ) {
        var option = new Option( opt.label, index );
        $('#email-forms-custom-fields-add-select').append($(option));
      });
    } else {
      var option = new Option('--  --', '');
      $('#email-forms-custom-fields-add-select').append($(option));
    }
  }

  //
  // Watch function to unbind/rebind
  //

  function email_forms_custom_fields_watch() {

    //
    // Add button.
    //

    $( '#email-forms-custom-fields-add' ).unbind( 'click' );
    $( '#email-forms-custom-fields-add' ).on( 'click', function ( e ) {
      e.preventDefault();
      console.log()
      var list_id = $('#email_forms_list').val();
      list_id = list_id.split('**');
      list_id = list_id[0];
      var field_id = $('#email-forms-custom-fields-add-select').val();
      if ( typeof email_forms_lists[ list_id ]['custom_fields'][ field_id ] !== 'undefined') {
        email_forms_custom_fields.push(email_forms_lists[ list_id ]['custom_fields'][ field_id ]);
      }
      email_forms_custom_fields_load();
    });


    //
    // Delete button.
    //

    $( '.email-forms-custom-field-delete' ).unbind( 'click' );
    $( '.email-forms-custom-field-delete' ).on( 'click', function ( e ) {
      e.preventDefault();
      var id = $( this ).data( 'id' );
      email_forms_custom_fields.splice( id, 1 );
      email_forms_custom_fields_load();
    });

  }

  email_forms_custom_fields_load();
  email_forms_update_custom_fields_options();

});

// Providers blocks specific
// =============================================================================

jQuery(document).ready(function($) {

  function toggle_provider_mailboxes() {
    var providers = [ 'mailchimp', 'convertkit', 'getresponse' ];
    var list_id = $('#email_forms_list').val();
    if (typeof list_id === 'undefined') {
      return;
    }
    list_id = list_id.split('**');
    list_id = list_id[0].split('_');
    var active_provider = list_id[0];
    $.each( providers, function( index, prv ) {
      if ( prv === active_provider ) {
        $('#email-forms-' + prv).show();
        $('#email-forms-custom-' + prv).show();
      } else {
        $('#email-forms-' + prv).hide();
        $('#email-forms-custom-' + prv).show();
      }
    });
  }

  $('#email_forms_list').change( function( e ) {
    toggle_provider_mailboxes();
  });

  toggle_provider_mailboxes();
});
