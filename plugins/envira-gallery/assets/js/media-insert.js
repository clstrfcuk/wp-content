/**
* Creates and handles a wp.media instance for Envira Galleries, allowing
* the user to insert images from the Media Library into their Gallery
*/
jQuery( document ).ready( function( $ ) {

    // Add Images
    $( 'a.envira-media-library' ).on( 'click', function( e ) {

        // Prevent default action
        e.preventDefault();

        // If the wp.media.frames.envira instance already exists, reopen it
        if ( wp.media.frames.envira ) {
            wp.media.frames.envira.open();
            return;
        } else {
            // Create the wp.media.frames.envira instance (one time)
            wp.media.frames.envira = wp.media( {
                frame: 'post',
                title:  wp.media.view.l10n.insertIntoPost,
                button: {
                    text: wp.media.view.l10n.insertIntoPost,
                },
                multiple: true
            } );
        }

        // Mark existing Gallery images as selected when the modal is opened
        wp.media.frames.envira.on( 'open', function() {
            // Get any previously selected images
            var selection = wp.media.frames.envira.state().get( 'selection' );

            // Get images that already exist in the gallery, and select each one in the modal
            $( 'ul#envira-gallery-output li' ).each( function() {
                var attachment = wp.media.attachment( $( this ).attr( 'id' ) );
                selection.add( attachment ? [ attachment ] : [] );
            } );
        } );

        // Insert into Gallery Button Clicked
        wp.media.frames.envira.on( 'insert', function() {
            // Get all selected images in JSON format
            var images = wp.media.frames.envira.state().get( 'selection' ).toJSON();

            // Send the ajax request with our data to be processed.
            $.post(
                envira_gallery_metabox.ajax,
                {
                    action:     'envira_gallery_insert_images',
                    nonce:      envira_gallery_metabox.insert_nonce,
                    post_id:    envira_gallery_metabox.id,
                    images:     images,
                },
                function( response ) {
                    // Response should be a JSON success with the HTML for the image grid
                    if ( response && response.success ) {
                        // Set the image grid to the HTML we received
                        $( '#envira-gallery-output' ).html( response.success );

                        // Repopulate the Envira Gallery Image Collection
                        EnviraGalleryImagesUpdate();
                    }
                },
                'json'
            );

        } );

        // Open the media frame
        wp.media.frames.envira.open();

        // Remove the 'Create Gallery' left hand menu item in the modal, as we don't
        // want users inserting galleries!
        $( 'div.media-menu a.media-menu-item:nth-child(2)' ).addClass( 'hidden' );
        $( 'div.media-menu a.media-menu-item:nth-child(6)' ).addClass( 'hidden' );
        

        return;

    } );

} );