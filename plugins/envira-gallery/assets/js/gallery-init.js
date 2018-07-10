import enviraLazy from './lib/enviraLazy.js';

class Envira {

	/**
	 * Constructor function for Envira.
	 *
	 * @since 1.7.1
	 */
	constructor( id, data, images, lightbox) {

		var self = this;

		//Setup our Vars
		self.data = data;
		self.images = images;
		self.id = id;
		self.envirabox_config = lightbox;

		//Log if ENVIRA_DEBUG enabled
		self.log(self.data);
		self.log(self.images);
		self.log(self.envirabox_config);
		self.log(self.id);

		//self init
		self.init();

	}

	/**
	 * Initizlize the proper scripts based on settings.
	 *
	 * @since 1.7.1
	 */
	init() {

		var self = this;

		//Justified Gallery Setup
		if (self.get_config('columns') == 0) {

			self.justified();

			if (self.get_config('lazy_loading')) {

				$(document).on('envira_pagination_ajax_load_completed', function() {

					$('#envira-gallery-' + self.id).on('jg.complete', function(e) {

						e.preventDefault();

						self.load_images();

					});

				});

				self.load_images();

			}

			if (self.get_config('justified_gallery_theme')) {

				//self.overlay_themes();

			}

			$(document).trigger('envira_gallery_api_justified', self.data);

		}

		//Lazy loading setup
		if ( self.get_config('lazy_loading') ) {

			self.load_images();

			$(window).scroll(function(e) {

				self.load_images();

			});

		}

		//Enviratope Setup
		if ( parseInt( self.get_config('columns') ) > 0 && self.get_config('isotope') ) {

			self.enviratopes();
			//Lazy loading setup
			if (self.get_config('lazy_loading')) {

				$( '#envira-gallery-' + self.id ).one('layoutComplete', function(e, laidOutItems) {

					self.load_images();

				});

			}
		} else if (  parseInt( self.get_config('columns') ) > 0) {

			self.load_images();

		}

		//Lightbox setup
		if (self.get_config('lightbox_enabled') || self.get_config('lightbox') ) {

			self.lightbox();

		}

		$(document).trigger('envira_gallery_api_init', self);

	}

	/**
	 * LazyLoading
	 *
	 * @since 1.7.1
	 */
	load_images() {

		var self = this;

		self.log('running: ' + '#envira-gallery-' + self.id );

		enviraLazy.run('#envira-gallery-' + self.id );

		if ( $('#envira-gallery-' + self.id).hasClass('enviratope') ) {

			$('#envira-gallery-' + self.id).enviraImagesLoaded()
				.done(function() {

					setTimeout(
					  function()
					  {
					    $('#envira-gallery-' + self.id).enviratope('layout');
						self.log('done: ' + '#envira-gallery-' + self.id );
					  }, 500);


				})
				.progress(function() {
					$('#envira-gallery-' + self.id).enviratope('layout');
					self.log('progress: ' + '#envira-gallery-' + self.id );
				});

		}

	}

	/**
	 * Outputs the gallery init script in the footer.
	 *
	 * @since 1.7.1
	 */
	justified() {

		var self = this;

		$('#envira-gallery-' + self.id).enviraJustifiedGallery({
			rowHeight: self.is_mobile() ? this.get_config('mobile_justified_row_height') : this.get_config('justified_row_height'),
			maxRowHeight: -1,
			waitThumbnailsLoad: true,
			selector: '> div > div',
			lastRow: this.get_config('justified_last_row'),
			border: 0,
			margins: this.get_config('justified_margins'),


		});

		$(document).trigger('envira_gallery_api_start_justified', self );

		$('#envira-gallery-' + this.id).css('opacity', '1');

	}


	justified_norewind() {

		$('#envira-gallery-' + self.id).enviraJustifiedGallery('norewind');

	}
	/**
	 * Outputs the gallery init script in the footer.
	 *
	 * @since 1.7.1
	 */
	enviratopes() {

			var self = this;


			var envira_isotopes_config = {

				itemSelector: '.envira-gallery-item',
				masonry: {
					columnWidth: '.envira-gallery-item'
				}

			};
			$(document).trigger('envira_gallery_api_enviratope_config', [ self ] );

			// Initialize Isotope
			$('#envira-gallery-' + self.id).enviratope( envira_isotopes_config );
			// Re-layout Isotope when each image loads
			$('#envira-gallery-' + self.id).enviraImagesLoaded()
				.done(function() {
					$('#envira-gallery-' + self.id).enviratope('layout');
				})
				.progress(function() {
					$('#envira-gallery-' + self.id).enviratope('layout');
				});
			$(document).trigger('envira_gallery_api_enviratope', [ self ] );

		}
	/**
	 * Outputs the gallery init script in the footer.
	 *
	 * @since 1.7.1
	 */
	lightbox() {

		var self             = this,
			thumbs           = self.get_config('thumbnails') ? { autoStart: true, hideOnClose: true, position: self.get_lightbox_config('thumbs_position') } : false,
			slideshow        = self.get_config('slideshow') ? { autoStart: self.get_config('autoplay'),speed: self.get_config('ss_speed') } : false,
			fullscreen       = self.get_config('fullscreen') && self.get_config('open_fullscreen') ? { autoStart: true } : true,
			animationEffect  = self.get_config('lightbox_open_close_effect') == 'zomm-in-out' ? 'zoom-in-out' : self.get_config('lightbox_open_close_effect'),
			transitionEffect = self.get_config('effect') == 'zomm-in-out' ? 'zoom' : self.get_config('effect'),
			lightbox_images  = [];
			self.lightbox_options = {
				selector:           '[data-envirabox="' + self.id + '"]',
				loop:               self.get_config('loop'), // Enable infinite gallery navigation
				margin:             self.get_lightbox_config('margins'), // Space around image, ignored if zoomed-in or viewport width is smaller than 800px
				gutter:             self.get_lightbox_config('gutter'), // Horizontal space between slides
				keyboard:           self.get_config('keyboard'), // Enable keyboard navigation
				arrows:             self.get_lightbox_config('arrows'), // Should display navigation arrows at the screen edges
				arrow_position:     self.get_lightbox_config('arrow_position'),
				infobar:            self.get_lightbox_config('infobar'), // Should display infobar (counter and arrows at the top)
				toolbar:            self.get_lightbox_config('toolbar'), // Should display toolbar (buttons at the top)
				idleTime:           60, // Detect "idle" time in seconds
				smallBtn:           self.get_lightbox_config('show_smallbtn'),
				protect:            false, // Disable right-click and use simple image protection for images
				image:              { preload: false },
				animationEffect:    animationEffect,
				animationDuration:  300, // Duration in ms for open/close animation
			    btnTpl : {
			        smallBtn   :        self.get_lightbox_config('small_btn_template'),
				},
				zoomOpacity:        'auto',
				transitionEffect:   transitionEffect, // Transition effect between slides
				transitionDuration: 200, // Duration in ms for transition animation
				baseTpl:            self.get_lightbox_config('base_template'), // Base template for layout
				spinnerTpl:         '<div class="envirabox-loading"></div>', // Loading indicator template
				errorTpl:           self.get_lightbox_config('error_template'), // Error message template
				fullScreen:         self.get_config('fullscreen') ? fullscreen : false,
				touch:              { vertical: true, momentum: true }, // Set `touch: false` to disable dragging/swiping
				hash:               false,
				insideCap:          self.get_lightbox_config('inner_caption'),
				capPosition:        self.get_lightbox_config('caption_position'),
				media : {
			        youtube : {
			            params : {
			                autoplay : 0
			            }
			        }
			    },
			    wheel:              self.get_config('mousewheel') ? 'auto' : false,
				slideShow:          slideshow,
				thumbs:             thumbs,
		        mobile : {
		            clickContent : function( current, event ) {
		                return current.type === 'image' ? 'toggleControls' : false;
		            },
		            clickSlide : function( current, event ) {
		                return current.type === 'image' ? 'toggleControls' : 'close';
		            },
		            dblclickContent : false,
		            dblclickSlide :false,
		        },
				// Clicked on the content
				clickContent: false,
				clickSlide: 'toggleControls', // Clicked on the slide
				clickOutside: 'close', // Clicked on the background (backdrop) element

		        // Same as previous two, but for double click
		        dblclickContent : false,
		        dblclickSlide   : false,
		        dblclickOutside : false,

				// Callbacks
				//==========
				onInit: function(instance, current) {

					$( document ).trigger( 'envirabox_api_on_init', [ self, instance, current ]  );
				},

				beforeLoad: function(instance, current) {

					$(document).trigger('envirabox_api_before_load', [ self, instance, current ]  );

				},
				afterLoad: function(instance, current) {

					$(document).trigger('envirabox_api_after_load', [ self, instance, current ]  );

				},

				beforeShow: function(instance, current) {

					$(document).trigger('envirabox_api_before_show', [ self, instance, current ]  );

				},
				afterShow: function(instance, current) {

					if ( prepend == undefined || prepend_cap == undefined){

						var prepend     = false,
							prepend_cap = false;

					}

					if ( prepend != true ){

						$('.envirabox-position-overlay').each(function(){
							$(this).prependTo( current.$content );
						});

						prepend = true;
					}

					if( ! self.get_config('keyboard') ){
						$(window).on('keydown', function(e) {
							if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
						        e.preventDefault();
						    }
						}, false);
					}

					/* legacy theme we hide certain elements initially to prevent user seeing them for a second in the upper left until the CSS fully loads */
					$('.envirabox-caption').show();
					$('.envirabox-navigation').show();
					$('.envirabox-navigation-inside').show();

					$(document).trigger('envirabox_api_after_show', [ self, instance, current ] );

				},

				beforeClose: function(instance, current) {

					$(document).trigger('envirabox_api_before_close', [ self, instance, current ]  );

				},
				afterClose: function(instance, current) {

					$(document).trigger('envirabox_api_after_close', [ self, instance, current ]  );

				},

				onActivate: function(instance, current) {

					$( document ).trigger('envirabox_api_on_activate', [ self, instance, current ] );

				},
				onDeactivate: function( instance, current ) {

					$( document ).trigger('envirabox_api_on_deactivate', [ self, instance, current ] );

				},

			};
		// Mobile Overrides
		if ( self.is_mobile() ){

			if ( self.get_config('mobile_thumbnails') !== 1 ) {
				self.lightbox_options.thumbs = false;
			}

		}
		// Load from json object if load all images is ture
		if ( self.get_lightbox_config( 'load_all') ) {

			$.each( self.images, function(i){

				lightbox_images.push( this );

			});

			$('#envira-gallery-wrap-' + self.id + ' .envira-gallery-link' ).on("click", function(e){

				e.preventDefault();

				var index 	= $(this).find('img').data('envira-index'),
					src 	= $(this).find('img').attr('src'),
					found   = false;

				// Override index if sorting is random or pagination is on
				if ( self.get_config( 'pagination') === 1 || self.get_config( 'sort_order' ) == "1" || index === 0 ) {

					Object.entries(lightbox_images).forEach((entry) => {
					    const [key, value] = entry;				    
					    if ( value.src == src ) {
					    	index = key;
					    	found = true;
					    }
					    
					});

					if ( found !== true ) {

						Object.entries(lightbox_images).forEach((entry) => {
						    const [key, value] = entry;
						    if ( value.src == $(this).attr('href') ) {
						    	index = key;
						    }
						    
						});

					}


				}

				// the below code used to be
				// $.envirabox.open( lightbox_images, self.lightbox_options, index );
				// until we started hitting issues with multiple boxes opening on "display all images" in pagination
				$(this).envirabox( self.lightbox_options );

			});

		} else {

			// console.log('testing!');

			$('.envira-gallery-' + self.id ).envirabox( self.lightbox_options );

			/* below code as an experiment to for ticket #1868, commented out because this pushes images to lightbox which wont' work for videos */

			// var content = $('.envira-gallery-' + self.id );
			// var seen = {};

			// content.each(function() {
			//     var txt = $(this)['0']['attributes']['data-envira-item-id'].value;
			//     console.log('seen:')
			//     console.log(seen);
			//     if (seen[txt]) {
			//         // $(this).remove();
			//     } else {
			//         seen[txt] = true;
			//         var obj_to_push = self.images[txt];
			//         obj_to_push.src = obj_to_push.link;
			//     	lightbox_images.push( obj_to_push );
			//     	console.log('pushed: ');
			//     	console.log(self.images[txt]);
			//     	console.log(self);
			//     }
			// });

			// $('.envira-gallery-' + self.id ).on( 'click', function(e){

			// 	e.preventDefault();

			// 	var index 	= $(this).find('img').data('envira-index'),
			// 		src 	= $(this).find('img').attr('src');

			// 	// Override index if sorting is random or pagination is on
			// 	if ( self.get_config( 'pagination') === 1 || self.get_config( 'sort_order' ) == "1" || index === 0 ) {
			// 		Object.entries(lightbox_images).forEach((entry) => {
			// 		    const [key, value] = entry;
			// 		    if ( value.src == src ) {
			// 		    	index = key;
			// 		    }
			// 		});
			// 	}

			// 	$.envirabox.open( lightbox_images, self.lightbox_options, index );

			// });

		}

		$(document).trigger('envirabox_lightbox_api', self );

	}

	/**
	 * Get a config option based off of a key.
	 *
	 * @since 1.7.1
	 */
	get_config(key) {

		return this.data[key];

	}

	/**
	 * Helper method to get config by key.
	 *
	 * @since 1.7.1
	 */
	get_lightbox_config(key) {

		return this.envirabox_config[key];

	}

	/**
	 * Helper method to get image from id
	 *
	 * @since 1.7.1
	 */
	get_image(id) {

		return this.images[id];

	}
	is_mobile(){
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			return true;
		}
		return false;
	}
	/**
	 * Helper method for logging if ENVIRA_DEBUG is true.
	 *
	 * @since 1.7.1
	 */
	log(log) {

		//Bail if debug or log is not set.
		if (envira_gallery.debug == undefined || !envira_gallery.debug || log == undefined) {

			return;

		}
		console.log(log);

	}

}

module.exports = Envira;