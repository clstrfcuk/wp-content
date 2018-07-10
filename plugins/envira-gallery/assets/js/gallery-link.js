class Envira_Link {

    constructor( data, images, lightbox) {

        var self = this;

		//Setup our Vars
		self.data = data;
		self.images = images;
		self.id = this.get_config('gallery_id');
		self.envirabox_config = lightbox;

		//Log if ENVIRA_DEBUG enabled
		self.log(self.data);
		self.log(self.images);
		self.log(self.envirabox_config);
		self.log(self.id);

        self.init();

    }

    init() {

		var self = this;

		self.lightbox();

    }

	/**
	 * Outputs the gallery init script in the footer.
	 *
	 * @since 1.7.1
	 */
	lightbox() {

		var self            = this,
			touch           = self.get_config('mobile_touchwipe') ? { vertical: true, momentum: true } : false,
			thumbs          = self.get_config('thumbnails') ? { autoStart: true, hideOnClose: true, position: self.get_lightbox_config('thumbs_position') } : false,
			slideshow       = self.get_config('slideshow') ? { autoStart: self.get_config('autoplay'),speed: self.get_config('ss_speed') } : false,
			fullscreen      = self.get_config('fullscreen') && self.get_config('open_fullscreen') ? { autoStart: true } : true,
			lightbox_images = [];
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
				animationEffect:    self.get_config('lightbox_open_close_effect'),
				animationDuration:  300, // Duration in ms for open/close animation
			    btnTpl : {
			        smallBtn   :        self.get_lightbox_config('small_btn_template'),
				},
				zoomOpacity:        'auto',
				transitionEffect:   self.get_config('effect'), // Transition effect between slides
				transitionDuration: 200, // Duration in ms for transition animation
				baseTpl:            self.get_lightbox_config('base_template'), // Base template for layout
				spinnerTpl:         '<div class="envirabox-loading"></div>', // Loading indicator template
				errorTpl:           self.get_lightbox_config('error_template'), // Error message template
				fullScreen:         self.get_config('fullscreen') ? fullscreen : false,
				touch:              touch, // Set `touch: false` to disable dragging/swiping
				hash:               false,
				insideCap:          self.get_lightbox_config('inner_caption'),
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

		$(document).trigger( 'envirabox_options', self );

		// Mobile Overrides
		if ( self.is_mobile() ){

			if ( self.get_config('mobile_thumbnails') !== 1 ) {
				self.lightbox_options.thumbs = false;
			}

		}

		$.each( self.images, function(i){

			lightbox_images.push( this );

		});

		$('#envira-links-' + self.id ).on( 'click', function(e){

			e.preventDefault();

			$.envirabox.open( lightbox_images, self.lightbox_options );

		});

	}

	/**
	 * Get a config option based off of a key.
	 *
	 * @since 1.7.1
	 */
	get_config(key) {

		var self = this;

		return self.data[key];

	}

	/**
	 * Helper method to get config by key.
	 *
	 * @since 1.7.1
	 */
	get_lightbox_config(key) {

		var self = this;

		return self.envirabox_config[key];

	}

	/**
	 * Helper method to get image from id
	 *
	 * @since 1.7.1
	 */
	get_image(id) {

		var self = this;

		return self.images[id];

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

module.exports = Envira_Link;