/**
* Handles showing and hiding fields conditionally, based on
* HTML data- attributes
*/
jQuery( document ).ready( function( $ ) {

    // Show/hide elements as necessary when a conditional field is changed
    $( 'input[data-envira-conditional], select[data-envira-conditional]' ).change( function() {

        // data-envira-conditional may have multiple element IDs specified
        var conditional_elements = $( this ).data( 'envira-conditional' ).split( ',' );
        var display_elements = false;
        
        // Determine whether to display relational elements or not
        switch ( $( this ).attr('type')) {
            case 'checkbox':
                display_elements = $( this ).is( ':checked' );
                break;
            default:
                display_elements = ( ( $( this ).val() == '' || $( this ).val() == 0 ) ? false : true );
                break;
        } 
        
        // Show/hide elements
        for ( var i = 0; i < conditional_elements.length; i++ ) {
            if ( display_elements ) {
                $( '#' + conditional_elements[ i ] ).fadeIn( 300 );
            } else {
                $( '#' + conditional_elements[ i ] ).fadeOut( 300 );
            }
        }
    } );

    // Trigger a change on each conditional element so elements are shown/hidden as necessary on load
    $( 'input[data-envira-conditional], select[data-envira-conditional]' ).trigger( 'change' );

} );