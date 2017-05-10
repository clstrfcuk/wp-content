// =============================================================================
// EMAIL-MAILCHIMP/JS/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Mailchimp specifics on editing form
// =============================================================================

// Mailchimp specifics on editing form
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Toggle send welcome email visibility.
  //

  $('#email_forms_list').change( function() {
    email_forms_update_mailchimp_groups_options();
  });

  function email_forms_update_mailchimp_groups_options() {
    var list_id = $('#email_forms_list').val();
    if (typeof list_id === 'undefined') {
      return;
    }
    list_id = list_id.split('**');
    list_id = list_id[0];
    $("#email-forms-mailchimp-groups-add-select option").remove();
    if ( typeof email_forms_lists[ list_id ] !== 'undefined') {
      var option = new Option('-- Select a group --', '');
      $('#email-forms-mailchimp-groups-add-select').append($(option));
      $.each( email_forms_lists[ list_id ]['groups'], function( index, opt ) {
        var option = new Option( opt.title, index );
        $('#email-forms-mailchimp-groups-add-select').append($(option));
      });
    } else {
      var option = new Option('--  --', '');
      $('#email-forms-mailchimp-groups-add-select').append($(option));
    }
  }

  function email_forms_mailchimp_groups_load() {

    if ( typeof email_forms_mailchimp_groups === 'undefined' ) {
      email_forms_mailchimp_groups_watch();
      return;
    }

    var table = $( '#email-forms-mailchimp-groups-list tbody' );
    table.html( '' );

    if ( email_forms_mailchimp_groups.length === 1 && typeof email_forms_mailchimp_groups[0].title === 'undefined' ) {
      email_forms_mailchimp_groups.splice( 0, 1 );
    }

    if ( email_forms_mailchimp_groups.length === 0 ) {
      var template = $( '#email-forms-mailchimp-groups-empty-template' ).text();
      table.append( template );
      email_forms_mailchimp_groups_watch();
      return;
    }

    email_forms_mailchimp_groups.map( function ( group, index ) {

      var interests_id    = [];
      var interests_label = [];
      var interest_select = $( '<select></select>' )
        .attr( 'name', 'x_meta[email_forms_mailchimp_groups]['+index+'][default]' );
      if ( group.type !== 'hidden' ) {
        interest_select.append( new Option( 'DEFAULT: Display ' + group.type +  ' on form --', 'display-on-form' ) );
      }

      $.each( group.interests, function ( index2, interest ) {
        interests_id.push(
          '<input type="hidden" name="x_meta[email_forms_mailchimp_groups]['+index+'][interests]['+index2+'][id]" value="'+interest.id+'" />'
          +'<input type="hidden" name="x_meta[email_forms_mailchimp_groups]['+index+'][interests]['+index2+'][name]" value="'+interest.name+'" />'
        );
        var option = $( '<option></option>' )
          .attr( 'value', interest.id )
          .html( interest.name );
        if (group.default === interest.id) {
          option.attr( 'selected', true );
        }
        interest_select.append( option );
      });

      interest_select.val(group.default);
      var interest_div = $('<div></div>').append(interest_select);

      var template = $( '#email-forms-mailchimp-groups-template' ).text();
      template = template.replace( /{index}/g, index );
      template = template.replace( /{id}/g, group.id );
      template = template.replace( /{type}/g, group.type );
      template = template.replace( /{title}/g, group.title );
      template = template.replace( /{title_label}/g, group.title );
      template = template.replace( /{interests}/g, interest_div.html() + interests_id.join('') );

      table.append( template );
    });

    email_forms_mailchimp_groups_watch();
  }

  //
  // Watch function to unbind/rebind
  //

  function email_forms_mailchimp_groups_watch() {

    //
    // Add button.
    //

    $( '#email-forms-mailchimp-groups-add' ).unbind( 'click' );
    $( '#email-forms-mailchimp-groups-add' ).on( 'click', function ( e ) {
      e.preventDefault();
      var list_id = $('#email_forms_list').val();
      list_id = list_id.split('**');
      list_id = list_id[0];
      var group_id = $('#email-forms-mailchimp-groups-add-select').val();
      if ( typeof email_forms_lists[ list_id ]['groups'][ group_id ] !== 'undefined') {
        email_forms_mailchimp_groups.push( email_forms_lists[ list_id ]['groups'][ group_id ] );
      }
      email_forms_mailchimp_groups_load();
    });


    //
    // Delete button.
    //

    $( '.email-forms-mailchimp-group-delete' ).unbind( 'click' );
    $( '.email-forms-mailchimp-group-delete' ).on( 'click', function ( e ) {
      e.preventDefault();
      var id = $( this ).data( 'id' );
      var splice = email_forms_mailchimp_groups.splice( id, 1 );
      email_forms_mailchimp_groups_load();
    });

  }


  email_forms_mailchimp_groups_load();
  email_forms_update_mailchimp_groups_options();

});
