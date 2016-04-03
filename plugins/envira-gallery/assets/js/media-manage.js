/**
* Handles selection, deselection and sorting of media in an Envira Gallery
*/
jQuery( document ).ready( function( $ ) {
	
	// Make gallery items sortable.
    var gallery = $('#envira-gallery-output');
    gallery.sortable({
        containment: '#envira-gallery-output',
        items: 'li',
        cursor: 'move',
        forcePlaceholderSize: true,
        placeholder: 'dropzone',
        helper: function( e, item ) {
            // Basically, if you grab an unhighlighted item to drag, it will deselect (unhighlight) everything else
            if (!item.hasClass('selected')) {
                item.addClass('selected').siblings().removeClass('selected');
            }
            
            // Clone the selected items into an array
            var elements = item.parent().children('.selected').clone();
            
            // Add a property to `item` called 'multidrag` that contains the 
            // selected items, then remove the selected items from the source list
            item.data('multidrag', elements).siblings('.selected').remove();
            
            // Now the selected items exist in memory, attached to the `item`,
            // so we can access them later when we get to the `stop()` callback
            
            // Create the helper
            var helper = $('<li/>');
            return helper.append(elements);
        },
        stop: function( e, ui ) {
            // Remove the helper so we just display the sorted items
            var elements = ui.item.data('multidrag');
            ui.item.after(elements).remove();
            
            // Send AJAX request to store the new sort order
            var opts = {
                url:      envira_gallery_metabox.ajax,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action:  'envira_gallery_sort_images',
                    order:   gallery.sortable('toArray').toString(),
                    post_id: envira_gallery_metabox.id,
                    nonce:   envira_gallery_metabox.sort
                },
                success: function(response) {
                    // Repopulate the Envira Gallery Image Collection
                    EnviraGalleryImagesUpdate( false );
                    return;
                },
                error: function(xhr, textStatus ,e) {
                    return;
                }
            };
            $.ajax( opts );
        }
    });

    // Select / deselect images
    var envira_gallery_shift_key_pressed = false,
        envira_gallery_last_selected_image = false;

    $( 'ul#envira-gallery-output' ).on( 'click', 'li.envira-gallery-image > img', function() {
        var gallery_item = $( this ).parent();

        if ( $( gallery_item ).hasClass( 'selected' ) ) {
            $( gallery_item ).removeClass( 'selected' );
            envira_gallery_last_selected_image = false;
        } else {
            
            // If the shift key is being held down, and there's another image selected, select every image between this clicked image
            // and the other selected image
            if ( envira_gallery_shift_key_pressed && envira_gallery_last_selected_image !== false ) {
                // Get index of the selected image and the last image
                var start_index = $( 'ul#envira-gallery-output li' ).index( $( envira_gallery_last_selected_image ) ),
                    end_index = $( 'ul#envira-gallery-output li' ).index( $( gallery_item ) ),
                    i = 0;

                // Select images within the range
                if ( start_index < end_index ) {
                    for ( i = start_index; i <= end_index; i++ ) {
                        $( 'ul#envira-gallery-output li:eq( ' + i + ')' ).addClass( 'selected' );
                    }
                } else {
                    for ( i = end_index; i <= start_index; i++ ) {
                        $( 'ul#envira-gallery-output li:eq( ' + i + ')' ).addClass( 'selected' );
                    }
                }
            }

            // Select the clicked image
            $( gallery_item ).addClass( 'selected' );
            envira_gallery_last_selected_image = $( gallery_item );

        }
        
        // Show/hide buttons depending on whether
        // any galleries have been selected
        if ( $( 'ul#envira-gallery-output > li.selected' ).length > 0 ) {
            $( 'a.envira-gallery-images-edit' ).css( 'display', 'inline-block' );
            $( 'a.envira-gallery-images-delete' ).css( 'display', 'inline-block' );
        } else {
            $( 'a.envira-gallery-images-edit' ).css( 'display', 'none' );
            $( 'a.envira-gallery-images-delete' ).css( 'display', 'none' );
        }
    } );

    // Determine whether the shift key is pressed or not
    $( document ).on( 'keyup keydown', function( e ) {
        envira_gallery_shift_key_pressed = e.shiftKey;
    } );

} );