// @codekit-prepend "conditional-fields-legacy.js";
// @codekit-prepend "conditions.js";
/**
* Handles showing and hiding fields conditionally
*/
jQuery( document ).ready( function( $ ) {

	// Show/hide elements as necessary when a conditional field is changed
	$( '#envira-gallery-settings input:not([type=hidden]), #envira-gallery-settings select' ).conditions( 
		[

			{	// Main Theme Elements
				conditions: {
					element: '[name="_envira_gallery[lightbox_theme]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-lightbox-title-display-box, #envira-config-lightbox-arrows-box, #envira-config-lightbox-toolbar-box',
							action: 'show'
						}
					]
				}
			},
			{
				conditions: {
					element: '[name="_envira_gallery[lightbox_theme]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'base_dark' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-lightbox-title-display-box, #envira-config-lightbox-arrows-box, #envira-config-lightbox-toolbar-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery arrows Dependant on Theme
				conditions: [
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_envira_gallery[arrows]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#envira-config-lightbox-arrows-position-box',
						action: 'show'
					},
					else: {
						element: '#envira-config-lightbox-arrows-position-box',
						action: 'hide'
					}
				}
			},
			{	// Items that are dependent on dark and new themes
				conditions: [
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base_dark' ]
					}
				],
				actions: {
					if: {
						element: '#envira-config-image-counter',
						action: 'show'
					},
					else: {
						element: '#envira-config-image-counter',
						action: 'hide'
					}
				}
			},
			{	// Gallery Toolbar
				conditions: [
					{
						element: '[name="_envira_gallery[toolbar]"]',
						type: 'checked',
						operator: 'is'
					},
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					}
				],
				actions: {
					if: [
						{
							element: '#envira-config-lightbox-toolbar-title-box, #envira-config-lightbox-toolbar-position-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-lightbox-toolbar-title-box, #envira-config-lightbox-toolbar-position-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Mobile Elements Dependant on Theme
				conditions: [
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_envira_gallery[mobile_lightbox]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#envira-config-mobile-arrows-box, #envira-config-mobile-toolbar-box',
						action: 'show'
					},
					else: {
						element: '#envira-config-mobile-arrows-box, #envira-config-mobile-toolbar-box',
						action: 'hide'
					}
				}
			},
			{	// Mobile Elements Independant of Theme
				conditions: {
					element: '[name="_envira_gallery[mobile_lightbox]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: {
						element: '#envira-config-mobile-touchwipe-box, #envira-config-mobile-touchwipe-close-box, #envira-config-mobile-thumbnails-box, #envira-config-mobile-thumbnails-width-box, #envira-config-mobile-thumbnails-height-box',
						action: 'show'
					},
					else: {
						element: '#envira-config-mobile-touchwipe-box, #envira-config-mobile-touchwipe-close-box, #envira-config-mobile-thumbnails-box, #envira-config-mobile-thumbnails-width-box, #envira-config-mobile-thumbnails-height-box',
						action: 'hide'
					}
				}
			},
			{	// Mobile Elements Independant of Theme
				conditions: {
					element: '[name="_envira_gallery[mobile_lightbox]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: {
						element: '#envira-config-lightbox-mobile-enable-links',
						action: 'hide'
					},
					else: {
						element: '#envira-config-lightbox-mobile-enable-links',
						action: 'show'
					}
				}
			},
			{	// Thumbnail Elements Dependant on Theme
				conditions: [
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_envira_gallery[thumbnails]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#envira-config-thumbnails-position-box',
						action: 'show'
					},
					else: {
						element: '#envira-config-thumbnails-position-box',
						action: 'hide'
					}
				}
			},
			{	// Thumbnail Elements Independant of Theme
				conditions: [
					{
						element: '[name="_envira_gallery[thumbnails]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#envira-config-thumbnails-height-box, #envira-config-thumbnails-width-box',
						action: 'show'
					},
					else: {
						element: '#envira-config-thumbnails-height-box, #envira-config-thumbnails-width-box',
						action: 'hide'
					}
				}
			},
			{	// Thumbnail Elements Dependant on Base Theme
				conditions: [
					{
						element: '[name="_envira_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base_dark', 'base_light' ]
					},
					{
						element: '[name="_envira_gallery[thumbnails]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#envira-config-thumbnail-button',
						action: 'show'
					},
					else: {
						element: '#envira-config-thumbnail-button',
						action: 'hide'
					}
				}
			},
			{	// Justified Gallery
				conditions: {
					element: '[name="_envira_gallery[columns]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-standard-settings-box, #envira-config-additional-copy-box',
							action: 'hide'
						},
						{
							element: '#envira-config-justified-settings-box, #envira-config-mobile-justified-row-height, #envira-config-additional-copy-box-automatic',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-standard-settings-box, #envira-config-additional-copy-box',
							action: 'show'
						},
						{
							element: '#envira-config-justified-settings-box, #envira-config-mobile-justified-row-height, #envira-config-additional-copy-box-automatic',
							action: 'hide'
						}
					]
				}
			},
			{	
				conditions: {
					element: '[name="_envira_gallery[justified_gallery_theme]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'normal' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-gallery-justified-theme-hover',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#envira-config-gallery-justified-theme-hover',
							action: 'show'
						}
					]
				}
			},
			{	// Gallery Description
				conditions: {
					element: '[name="_envira_gallery[description_position]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-description-box',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#envira-config-description-box',
							action: 'show'
						}
					]
				}
			},
			{	// Gallery Sorting
				conditions: {
					element: '[name="_envira_gallery[random]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-sorting-direction-box',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#envira-config-sorting-direction-box',
							action: 'show'
						}
					]
				}
			},
			{	// Gallery CSS animations
				conditions: {
					element: '[name="_envira_gallery[css_animations]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#envira-config-css-opacity-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-css-opacity-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery image size
				conditions: {
					element: '[name="_envira_gallery[image_size]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'default' ]
				},
				actions: {
					if: [
						{
							element: '#envira-config-crop-size-box, #envira-config-crop-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-crop-size-box, #envira-config-crop-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery Lightbox
				conditions: {
					element: '[name="_envira_gallery[lightbox_enabled]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#envira-lightbox-settings',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-lightbox-settings',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery Lightbox
				conditions: {
					element: '[name="_envira_gallery[lightbox_enabled]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#envira-config-lightbox-enabled-link',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#envira-config-lightbox-enabled-link',
							action: 'show'
						}
					]
				}
			},
			{	// Album Mobile Images
				conditions: {
					element: '[name="_envira_gallery[mobile]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#envira-config-mobile-size-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-mobile-size-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery Mobile Crop
				conditions: [
					{
						element: '[name="_envira_gallery[mobile_thumbnails]"]',
						type: 'checked',
						operator: 'is'
					},
					{
						element: '[name="_envira_gallery[mobile_lightbox]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: [
						{
							element: '#envira-config-mobile-thumbnails-width-box, #envira-config-mobile-thumbnails-height-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-mobile-thumbnails-width-box, #envira-config-mobile-thumbnails-height-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Album Mobile Touchwipe
				conditions: {
					element: '[name="_envira_gallery[lazy_loading]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#envira-config-lazy-loading-delay',
							action: 'show'
						}
					],
					else: [
						{
							element: '#envira-config-lazy-loading-delay',
							action: 'hide'
						}
					]
				}
			},

		]
	);

} );