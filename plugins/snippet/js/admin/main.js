// =============================================================================
// JS/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. General Settings Screen
//   02. Global Plugin Functionality
//   03. Output toggle
//   04. Contacts CRUD
//   05. Organization Toggle
//   06. Contacts CRUD
//   07. Hours Available CRUD
// =============================================================================


// General Settings Screen
// =============================================================================

jQuery(document).ready(function($) {

});


// Global Plugin Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Accordion.
  //

  $( '.accordion > .toggle' ).click( function() {
    var $this = $( this );
    if ( $this.hasClass( 'active' ) ) {
      $this.removeClass( 'active' ).next().slideUp();
    } else {
      $( '.accordion > .panel' ).slideUp();
      $this.siblings().removeClass( 'active' );
      $this.addClass( 'active' ).next().slideDown();
      return false;
    }
  });


  //
  // Save button.
  //

  $( '#submit' ).click(function() {
    $( this ).addClass( 'saving' ).val( 'Updating' );
  });


  //
  // Meta box toggle.
  //

  postboxes.add_postbox_toggles( pagenow );

});

// Output toggle
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Toggle all
  //

  $( '#snippet_output_all' ).click( function( e ) {
    if ( $( this ).attr( 'checked' ) ) {
      $( '.snippet-output' ).attr( 'checked', $( this ).attr( 'checked' ) );
    } else {
      $( '.snippet-output' ).prop( 'checked', false )
    }
  });

  //
  // Check/uncheck all checkbox on loading
  //

  function snippet_output_check () {
    if ( typeof $( '#snippet_output_all' ) === 'undefined' ) {
      return;
    }
    var checked = true;
    $( '.snippet-output' ).each( function ( ) {
      if ( typeof $( this ).attr( 'checked' ) === 'undefined' ) {
        checked = false;
      }
    });
    $( '#snippet_output_all' ).attr( 'checked', checked );
  }

  $( '.snippet-output' ).change( function( e ) {
    snippet_output_check ();
  });

  snippet_output_check ();
});


// Organization Toggle
// =============================================================================

jQuery(document).ready(function($) {

  function snippet_organization_toggle() {
    var description = $( '#snippet_organization_type option:selected' ).data( 'description' );
    $('#organization_type_description').text(description);
  }

  $( '#snippet_organization_type' ).change(function() {
    snippet_organization_toggle();
  });

  snippet_organization_toggle();

});

// Contacts CRUD
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Field list
  //

  var snippet_contacts_fields = [
    [ 'type', 'type' ],
    [ 'telephone', 'telephone' ],
    [ 'option', 'option' ],
    [ 'areaServed', 'area_served' ],
    [ 'availableLanguage', 'available_language' ],
    [ 'hoursAvailable', 'hours_available' ]
  ];

  //
  // Load function
  //

 function snippet_contacts_load() {
    if ( typeof snippet_contacts === 'undefined' ) {
      return;
    }
    var table = $( '#snippet-contacts-list tbody' );
    table.html( '' );
    var new_array = [];
    snippet_contacts.map( function ( contact, index ) {
      new_array.push( contact );
    });
    snippet_contacts = new_array;
    snippet_hours = [];
    snippet_contacts.map( function ( contact, index ) {
      var template = $( '#snippet-contacts-template' ).text();
      template = template.replace( /{id}/g, index );
      Object.keys( contact ).map( function ( key ) {
        var search = new RegExp( '{' + key + '}',"g" );
        template = template.replace( search, contact[ key ] );
        if ( key == 'hoursAvailable' ) {
          value = contact[ key ].split( '|' ).join( '<br/>' );
          var search = new RegExp( '{hoursAvailableDisplay}',"g" );
          template = template.replace( search, value );
        }
      });
      table.append( template );
    });

    snippet_contacts_watch();
  }

  //
  // Watch function to unbind/rebind
  //

  function snippet_contacts_watch() {

    //
    // Add button.
    //

    $( '#snippet-contacts-add' ).unbind( 'click' );
    $( '#snippet-contacts-add' ).on( 'click', function ( e ) {
      e.preventDefault();
      $( '#snippet_contact_id' ).val( '' );
      snippet_contacts_fields.map( function ( field ) {
        var elem_id = '#snippet_contact_' + field[1];
        $( elem_id ).val( '' );
      });
      $( '#meta-box-settings-contacts-1' ).show();
      $( '#meta-box-settings-contacts-2' ).show();
      $( '#snippet-contact-save-div' ).show();
    });

    //
    // Edit button.
    //

    $( '.snippet-contact-edit' ).unbind( 'click' );
    $( '.snippet-contact-edit' ).on( 'click', function ( e ) {
      e.preventDefault();
      var id      = $( this ).data( 'id' );
      var contact = snippet_contacts[ id ];
      $( '#snippet_contact_id' ).val( id );
      snippet_hours = [];
      snippet_contacts_fields.map( function ( field ) {
        var elem_id = '#snippet_contact_' + field[1];
        var value   = contact[ field[0] ];
        if ( field[0] == 'hoursAvailable' ) {
          snippet_hours = contact[ field[0] ].split( '|' );
        }
        $( elem_id ).val( value );
      });
      snippet_hours_load();
      $( '#meta-box-settings-contacts-1' ).show();
      $( '#meta-box-settings-contacts-2' ).show();
      $( '#snippet-contact-save-div' ).show();
    });

    //
    // Delete button.
    //

    $( '.snippet-contact-delete' ).unbind( 'click' );
    $( '.snippet-contact-delete' ).on( 'click', function ( e ) {
      e.preventDefault();
      var id = $( this ).data( 'id' );
      delete( snippet_contacts[ id ] );
      snippet_contacts_load();
    });

    //
    // Save button.
    //

    $( '#snippet-contact-save' ).unbind( 'click' );
    $( '#snippet-contact-save' ).on( 'click', function ( e ) {
      e.preventDefault();
      var id = $( '#snippet_contact_id' ).val();
      if ( id === '') {
        id = snippet_contacts.length;
        snippet_contacts[ id ] = {};
      }
      snippet_contacts_fields.map( function ( field ) {
        var elem_id = '#snippet_contact_' + field[1];
        snippet_contacts[ id ][ field[0] ] = $( elem_id ).val();
        console.log(
          elem_id,
          $( elem_id ).val(),
          snippet_contacts[ id ][ field[0] ]
        )
      });
      $( '#meta-box-settings-contacts-1' ).hide();
      $( '#meta-box-settings-contacts-2' ).hide();
      $( '#snippet-contact-save-div' ).hide();
      snippet_contacts_load();
    });

  }

  snippet_contacts_load();

});


// Hours Available CRUD

// =============================================================================

//
// Load function
//

function snippet_hours_load() {
  var $ = jQuery;

  if ( typeof snippet_hours === 'undefined' ) {
   return;
  }
  var list = $( '#snippet_hours_list' );
  list.html( '' );
  var new_array = [];
  snippet_hours.map( function ( item, index ) {
   list.append(
     '<li>' + item +
     // ' <a href="#" class="snippet_hours_edit" data-id="' + index + '">Edit</a>' +
     ' <a href="#" class="snippet_hours_delete" data-id="' + index + '">Delete</a></li>'
   )
  });
  console.log(snippet_hours_field);
  $( '#' + snippet_hours_field ).val( snippet_hours.join( '|' ) );
  snippet_hours_watch();
}

function snippet_hours_watch() {
  var $ = jQuery;
  //
  // Add button.
  //

  $( '#snippet_hours_add' ).unbind( 'click' );
  $( '#snippet_hours_add' ).on( 'click', function ( e ) {
    e.preventDefault();
    var result = '';
    var weekdays = [];
    $( '.snippet_hours_weekday:checked' ).each( function ( ) {
      weekdays.push( $( this ).val() );
    });
    result = ( ( weekdays.length === 7 ) ? 'Mo-Su' : weekdays.join(',') ) +
             ' ' + $( '#snippet_hours_start' ).val() +
             '-' + $( '#snippet_hours_end' ).val();
    snippet_hours.push( result );
    snippet_hours_load();
  });

  //
  // Delete button.
  //

  $( '.snippet_hours_delete' ).unbind( 'click' );
  $( '.snippet_hours_delete' ).on( 'click', function ( e ) {
    e.preventDefault();
    var id = $( this ).data( 'id' );
    snippet_hours.splice( id, 1 );
    snippet_hours_load();
  });

}

jQuery(document).ready(function($) {

  //
  // Watch function to unbind/rebind
  //

  snippet_hours_load();

  //
  // jquery-timepicker initialization
  //

  $( '.snippet-time' ).timepicker({
    show2400: true,
    timeFormat: "H:i",
    step: 15
  });

});
