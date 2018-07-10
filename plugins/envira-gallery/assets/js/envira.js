import './lib/mousewheel.js';
import './gallery-themes.js';
import './lib/jquery.justifiedGallery.js';
import './lib/enviraJustifiedGallery-extensions.js';
import './lib/isotope.js';
import './lib/imagesloaded.js';
import './lib/envirabox.js';
import './lib/envirabox-fullscreen.js';
import './lib/envirabox-media.js';
import './lib/envirabox-wheel.js';
import './lib/envirabox-guestures.js';
import './lib/envirabox-thumbs.js';
import './lib/envirabox-slideshow.js';

import Envira from './gallery-init.js';
import Envira_Link from './gallery-link.js';

var envira_galleries = window.envira_galleries || {},
	envira_links = window.envira_links || {};

;(function ( $, window, document, Envira, Envira_Link, envira_gallery, envira_galleries ) {

	$(function() {

		window.envira_galleries = envira_galleries;
		window.envira_links = envira_links

		$( document ).on( 'envira_load', function(e){
			e.stopPropagation();
			envira_galleries = {};
			envira_links = {};
			$('.envira-gallery-public').each(function() {

				let $this             = $( this ),
					$id               = $this.data('envira-id'),
					$envira_galleries = $this.data('gallery-config'),
					$envira_images     = $this.data('gallery-images'),
					$envira_lightbox  = $this.data('lightbox-theme');

				envira_galleries[ $envira_galleries[ 'gallery_id' ] ] = new Envira( $id, $envira_galleries, $envira_images, $envira_lightbox );

			});
			$( '.envira-gallery-links' ).each(function( ) {

				let $this = $( this ),
					$envira_galleries = $this.data('gallery-config'),
					$envira_images = $this.data('gallery-images'),
					$envira_lightbox = $this.data('lightbox-theme');

				envira_links[ $envira_galleries[ 'gallery_id' ] ] = new Envira_Link( $envira_galleries, $envira_images, $envira_lightbox );

			});

			$(document).trigger('envira_loaded', [ envira_galleries, envira_links] );

		});

		$(document).trigger('envira_load');

		if( envira_gallery.debug !== undefined && envira_gallery.debug ){

			console.log( envira_links );
			console.log( envira_galleries );

		}

		$( 'body' ).on( 'click', 'div.envirabox-title a[href*="#"]:not([href="#"])', function( e ) {

			if ( location.pathname.replace( /^\//, '' ) == this.pathname.replace( /^\//, '' ) && location.hostname == this.hostname ) {
				$.envirabox.close();
				return false;
			}

		} );

		/* setup lazy load event */
		$( document ).on( "envira_image_lazy_load_complete", function( event ) {

			if ( event !== undefined && event.image_id !== undefined && event.image_id !== null ) {

				let envira_container = $('#envira-gallery-wrap-' + event.gallery_id).find('img#' + event.image_id);

				if ( $('#envira-gallery-wrap-' + event.gallery_id).find('div.envira-gallery-public').hasClass('envira-gallery-0-columns') ) {

					/* this is an automatic gallery */
					$( envira_container ).closest('div.envira-gallery-item-inner').find( 'div.envira-gallery-position-overlay' ).delay( 100 ).show();

				} else {

					/* this is a legacy gallery */
					$( envira_container ).closest('div.envira-gallery-item-inner').find( 'div.envira-gallery-position-overlay' ).delay( 100 ).show();

					/* re-do the padding bottom */
					/* $padding_bottom = ( $output_height / $output_width ) * 100; */

					var envira_lazy_width = $( envira_container ).closest('div.envira-gallery-item-inner').find('.envira-lazy').width();
					var ratio1 = ( event.naturalHeight / event.naturalWidth );
					var ratio2 = ( event.naturalHeight / envira_lazy_width );

					if ( ratio2 < ratio1 ) {
						var ratio = ratio2;
					} else {
						var ratio = ratio1;
					}

					var padding_bottom = ratio * 100;
					if ( envira_container.closest('div.envira-gallery-public').parent().hasClass('envira-gallery-theme-sleek') ) {
						// add additional padding for this theme
						padding_bottom = padding_bottom + 2;
					}

				    var div_envira_lazy = $( envira_container ).closest('div.envira-gallery-item-inner').find('div.envira-lazy');
				    var caption_height = div_envira_lazy.closest('div.envira-gallery-item-inner').find('.envira-gallery-captioned-data').height();
				    if ( $( envira_container ).closest('div.envira-gallery-item').hasClass('enviratope-item') ) {
				        div_envira_lazy.css('padding-bottom', padding_bottom + '%').attr('data-envira-changed', 'true');
				        var div_overlay = $( envira_container ).closest('div.envira-gallery-item-inner').find('.envira-gallery-position-overlay.envira-gallery-bottom-right');
				        div_overlay.css('bottom', caption_height);
				        div_overlay = $( envira_container ).closest('div.envira-gallery-item-inner').find('.envira-gallery-position-overlay.envira-gallery-bottom-left');
				        div_overlay.css('bottom', caption_height);
				    } else {
				        div_envira_lazy.css('height', 'auto').css('padding-bottom', '10px').attr('data-envira-changed', 'true');
				        var div_overlay = $( envira_container ).closest('div.envira-gallery-item-inner').find('.envira-gallery-position-overlay.envira-gallery-bottom-right');
				        div_overlay.css('bottom', caption_height + 10);
				        div_overlay = $( envira_container ).closest('div.envira-gallery-item-inner').find('.envira-gallery-position-overlay.envira-gallery-bottom-left');
				        div_overlay.css('bottom', caption_height + 10);
				    }

				    // div_envira_lazy.addClass('changed');
					$( envira_container ).closest('div.envira-gallery-item-inner').find( 'span.envira-title' ).delay( 1000 ).css('visibility', 'visible');
					$( envira_container ).closest('div.envira-gallery-item-inner').find( 'span.envira-caption' ).delay( 1000 ).css('visibility', 'visible');

					if ( window["envira_container_" + event.gallery_id] !== undefined ) {

						if ( $('#envira-gallery-' + event.gallery_id).hasClass('enviratope') ) {

							window["envira_container_" + event.gallery_id].on( 'layoutComplete',
							  function( event, laidOutItems ) {

								$( envira_container ).closest('div.envira-gallery-item-inner').find( 'span.envira-title' ).delay( 1000 ).css('visibility', 'visible');
								$( envira_container ).closest('div.envira-gallery-item-inner').find( 'span.envira-caption' ).delay( 1000 ).css('visibility', 'visible');


							  }
							);

						} else {

						}

					}

				}

			}
		});

	} );

})( jQuery , window, document, Envira, Envira_Link, envira_gallery, envira_galleries );