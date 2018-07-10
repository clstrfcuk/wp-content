/**
 * Handles:
 * - Dismissable Notices
 *
 * @since 1.8.1
 */
jQuery( document ).ready( function( $ ) {

    /**
    * Dismissable Notices
    * - Sends an AJAX request to mark the notice as dismissed
    */
    $( 'div.envira-notice' ).on( 'click', '.notice-dismiss', function( e ) {

        e.preventDefault();

        $( this ).closest( 'div.envira-notice' ).fadeOut();

        // If this is a dismissible notice, it means we need to send an AJAX request
        if ( $( this ).parent().hasClass( 'is-dismissible' ) ) {
            $.post(
                envira_gallery_admin.ajax,
                {
                    action: 'envira_gallery_ajax_dismiss_notice',
                    nonce:  envira_gallery_admin.dismiss_notice_nonce,
                    notice: $( this ).parent().data( 'notice' ),
                    seconds: $( this ).parent().data( 'seconds' ),
                },
                function( response ) {
                },
                'json'
            );
        }

    } );

});