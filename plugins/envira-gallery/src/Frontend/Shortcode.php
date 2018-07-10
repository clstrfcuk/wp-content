<?php
/**
 * Shortcode class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */
namespace Envira\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

class Shortcode {

	/**
	 * Holds the gallery data.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Iterator for galleries on the page.
	 *
	 * @since 1.7.0
	 *
	 * @var int
	 */
	public $counter = 1;

	/**
	 * Array of gallery ids on the page.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $gallery_ids = array();

	/**
	 * Array of gallery item ids on the page.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $gallery_item_ids = array();

	/**
	 * Holds image URLs for indexing.
	 *
	 * @since 1.7.0
	 *
	 * @var array
	 */
	public $index = array();

	/**
	 * Holds the sort order of the gallery for addons like Pagination
	 *
	 * @since 1.5.6
	 *
	 * @var array
	 */
	public $gallery_sort = array();

	/**
	 * gallery_data
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access public
	 */
	public $gallery_data = array();

	public $link_data = array();

	/**
	 * is_mobile
	 *
	 * @var mixed
	 * @access public
	 */
	public $is_mobile;

	/**
	 * item
	 *
	 * @var mixed
	 * @access public
	 */
	public $item;

	/**
	 * gallery_markup
	 *
	 * @var mixed
	 * @access public
	 */
	public $gallery_markup;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		$this->is_mobile = envira_mobile_detect()->isMobile();

		// Load hooks and filters.
		add_action( 'init', array( &$this, 'register_scripts' ) );

		add_shortcode( 'envira-gallery', array( &$this, 'shortcode' ) );
		add_shortcode( 'envira-link', array( &$this, 'shortcode_link' ) );

		add_filter( 'style_loader_tag', array( $this, 'add_stylesheet_property_attribute' ) );
		add_action( 'envira_gallery_output_caption', array( $this, 'gallery_image_caption_titles' ), 10, 5 );

		add_filter( 'envirabox_gallery_thumbs_position', array( $this, 'envirabox_gallery_thumbs_position' ), 10, 2 );
		add_filter( 'envirabox_dynamic_margin', array( $this, 'envirabox_dynamic_margin' ), 10, 2 );
		add_filter( 'envira_gallery_title_type', array( $this, 'envira_gallery_title_type' ), 10, 2 );

	}

	/**
	 * register_scripts function.
	 *
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function register_scripts(){

		// Register main gallery style.
		//wp_register_style( ENVIRA_SLUG . '-style', plugins_url( 'assets/css/envira.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
		wp_register_style( ENVIRA_SLUG . '-style', plugins_url( 'assets/css/envira.css', ENVIRA_FILE ), array(), time() );

		// Register Justified Gallery style.
		wp_register_style( ENVIRA_SLUG . '-jgallery', plugins_url( 'assets/css/justifiedGallery.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );

		// Register main gallery script.
		//wp_register_script( ENVIRA_SLUG . '-script', plugins_url( 'assets/js/min/envira-min.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION, true );
		wp_register_script( ENVIRA_SLUG . '-script', plugins_url( 'assets/js/min/envira-min.js', ENVIRA_FILE ), array( 'jquery' ), time(), true );

	}

	/**
	 * I'm sure some plugins mean well, but they go a bit too far trying to reduce
	 * conflicts without thinking of the consequences.
	 *
	 * 1. Prevents Foobox from completely borking envirabox as if Foobox rules the world.
	 *
	 * @since 1.7.0
	 */
	public function plugin_humility() {

		if ( class_exists( 'fooboxV2' ) ) {

			remove_action( 'wp_footer', array( $GLOBALS['foobox'], 'disable_other_lightboxes' ), 200 );

		}

	}

	/**
	 * Creates the shortcode for the plugin.
	 *
	 * @since 1.7.0
	 *
	 * @global object $post The current post object.
	 *
	 * @param array $atts Array of shortcode attributes.
	 * @return string        The gallery output.
	 */
	public function shortcode( $atts ) {

		// hook that would allow any initial checks and bails (such as yoast snippet previews)
		$envira_shortcode_start = apply_filters( 'envira_gallery_shortcode_start', true, $atts );
		if ( $envira_shortcode_start['action'] == 'bail' ) {
			if ( defined('ENVIRA_DEBUG') && ENVIRA_DEBUG == 'true' ) {
				error_log( 'envira_shortcode_start - bail' );
				error_log( print_r ( $envira_shortcode_start, true ) );
			}
			return;
		}

		global $post;
		// If no attributes have been passed, the gallery should be pulled from the current post.
		$gallery_id = false;

		if ( empty( $atts ) ) {

			$gallery_id = $post->ID;
			$data       = is_preview() ? _envira_get_gallery( $gallery_id ) : envira_get_gallery( $gallery_id );

		} else if ( isset( $atts['id'] ) && !isset( $atts['dynamic'] ) ) {

			$gallery_id = (int) $atts['id'];
			$data       = is_preview() ? _envira_get_gallery( $gallery_id ) : envira_get_gallery( $gallery_id );

		} else if ( isset( $atts['slug'] ) ) {

			$gallery_id = $atts['slug'];
			$data       = is_preview() ? _envira_get_gallery_by_slug( $gallery_id ) : envira_get_gallery_by_slug( $gallery_id );
			// we have the gallery data, now just translate slug into the ID
			if ( intval($data['id']) ) {
				$gallery_id = intval( $data['id'] );
			}

		} else {

			// A custom attribute must have been passed. Allow it to be filtered to grab data from a custom source.
			$data       = apply_filters( 'envira_gallery_custom_gallery_data', false, $atts, $post );
			$gallery_id = $data['config']['id'];
			$this->dynamic_images = $data['gallery'];

		}


		// Allow the data to be filtered before it is stored and used to create the gallery output.
		$data = apply_filters( 'envira_gallery_pre_data', $data, $gallery_id );

		// If there is no data to output or the gallery is inactive, do nothing.
		if ( ! $data || empty( $data['gallery'] ) || isset( $data['status'] ) && 'inactive' == $data['status'] && ! is_preview() ) {

			return;

		}

		// Lets check if this gallery has already been output on the page
		$data['gallery_id'] = ( isset( $data['config']['type'] ) && $data['config']['type'] == 'dynamic' && isset( $data['config']['id'] ) ) ? $data['config']['id'] : $data['id'];
		$main_id            = ( isset( $data['config']['type'] ) && $data['config']['type'] == 'dynamic' && isset( $data['dynamic_id'] ) ) ? $data['dynamic_id'] : $data['id'];

		if ( ! empty( $atts['counter'] ) ) {

			// we are forcing a counter so lets force the object in the gallery_ids
			$this->counter = $atts['counter'];
			$this->gallery_ids[] = $data['id'];

		}

		if ( ! empty( $data['id'] ) && ! in_array( $data['id'], $this->gallery_ids ) ) {

			$this->gallery_ids[] = $data['id'];

		} elseif( $this->counter > 1 && !empty( $data['id'] ) ) {

			$data['id'] = $data['id'] . '_' . $this->counter;

		}

		if ( ! empty( $data['id'] ) && empty( $atts['presorted'] ) ) {

			$this->gallery_sort[ $data['id'] ] = false; // reset this to false, otherwise multiple galleries on the same page might get other ids, or other wackinesses

		}

		// Limit the number of images returned, if specified
		// [envira-gallery id="123" limit="10"] would only display 10 images
		if ( isset( $atts['limit'] ) && is_numeric( $atts['limit'] ) ) {

			// check for existence of gallery, if there's nothing it could be an instagram or blank gallery
			if ( ! empty( $data['gallery'] ) ) {

				$images          = array_slice( $data['gallery'], 0, absint( $atts['limit'] ), true );
				$data['gallery'] = $images;

			}

		}

		// This filter detects if something needs to be displayed BEFORE a gallery is displayed, such as a password form
		$pre_gallery_html = apply_filters( 'envira_abort_gallery_output', false, $data, $gallery_id, $atts );

		if ( $pre_gallery_html !== false ) {

			// If there is HTML, then we stop trying to display the gallery and return THAT HTML
			return apply_filters( 'envira_gallery_output', $pre_gallery_html, $data );

		}

		$this->gallery_data = $data;

		// If this is a feed view, customize the output and return early.
		if ( is_feed() ) {

			return $this->do_feed_output( $this->gallery_data );

		}

		// Get rid of any external plugins trying to jack up our stuff where a gallery is present.
		$this->plugin_humility();

		// Prepare variables.
		$this->index[ $this->gallery_data['id'] ]   = array();
		$this->gallery_markup                       = '';
		$i                                          = 1;

		// Load scripts and styles.
		wp_enqueue_style( ENVIRA_SLUG . '-style' );

		wp_enqueue_style( ENVIRA_SLUG . '-jgallery' );

		wp_enqueue_script( ENVIRA_SLUG . '-script' );

		wp_localize_script( ENVIRA_SLUG . '-script', 'envira_gallery', array(
			'debug'         => ( defined( 'ENVIRA_DEBUG' ) && ENVIRA_DEBUG ? true : false ),
			'll_delay'      => isset( $this->gallery_data['config']['lazy_loading_delay'] ) ? intval($this->gallery_data['config']['lazy_loading_delay']) : 500,
			'll_initial'    => 'false',
			'll'            => envira_get_config( 'lazy_loading', $data ) == 1 ? 'true' : 'false',
			'mobile'        => $this->is_mobile

		) );

		// Load custom gallery themes if necessary.
		if ( 'base' !== envira_get_config( 'gallery_theme', $this->gallery_data ) && envira_get_config( 'columns', $this->gallery_data ) > 0 ) {

			// if columns is zero, then it's automattic which means we do not load gallery themes because it will mess up the new javascript layout
			envira_load_gallery_theme( envira_get_config( 'gallery_theme', $this->gallery_data ) );

		}

		// Load custom lightbox themes if necessary, don't load if user hasn't enabled lightbox
		if ( envira_get_config( 'lightbox_enabled', $this->gallery_data ) ) {

			envira_load_lightbox_theme( envira_get_config( 'lightbox_theme', $this->gallery_data ) );

		}

		// Run a hook before the gallery output begins but after scripts and inits have been set.
		do_action( 'envira_gallery_before_output', $this->gallery_data );

		$markup = apply_filters( 'envira_gallery_get_transient_markup', get_transient( '_eg_fragment_' . $data['gallery_id'] ), $this->gallery_data );

		if ( $markup && ( !defined('ENVIRA_DEBUG') || !ENVIRA_DEBUG ) ) {

			$this->gallery_markup = $markup;

		} else {

			$this->gallery_markup   = apply_filters( 'envira_gallery_output_start', $this->gallery_markup, $this->gallery_data ); // Apply a filter before starting the gallery HTML.
			$schema_microdata       = apply_filters( 'envira_gallery_output_shortcode_schema_microdata', 'itemscope itemtype="http://schema.org/ImageGallery"', $this->gallery_data ); // Schema.org microdata ( Itemscope, etc. ) interferes with Google+ Sharing... so we are adding this via filter rather than hardcoding

			// Build out the gallery HTML.
			$this->gallery_markup  .= '<div id="envira-gallery-wrap-' . sanitize_html_class( $this->gallery_data['id'] ) . '" class="' . $this->get_gallery_classes( $this->gallery_data ) . '" ' . $schema_microdata . '>';
			$this->gallery_markup   = apply_filters( 'envira_gallery_output_before_container', $this->gallery_markup, $this->gallery_data );
			$temp_gallery_markup    = apply_filters( 'envira_gallery_temp_output_before_container', '', $this->gallery_data );

			// Description
			if ( isset( $this->gallery_data['config']['description_position'] ) && $this->gallery_data['config']['description_position'] == 'above' ) {

				$temp_gallery_markup = $this->description( $temp_gallery_markup, $this->gallery_data );

			}

			$extra_css                  = envira_get_config( 'columns', $this->gallery_data ) > 0  ? false : 'envira-gallery-justified-public'; // add justified CSS?
			$row_height                 = false;
			$justified_gallery_theme    = false;
			$justified_margins          = false;

			if ( envira_get_config( 'columns', $this->gallery_data ) > 0 ) {

				// add isotope if the user has it enabled
				$isotope = envira_get_config( 'isotope', $this->gallery_data ) ? ' enviratope' : false;

			} else {

				$row_height = !$this->is_mobile ? envira_get_config( 'justified_row_height', $this->gallery_data ) : envira_get_config( 'mobile_justified_row_height', $this->gallery_data );
				$justified_gallery_theme = envira_get_config( 'justified_gallery_theme', $this->gallery_data );
				$justified_margins = envira_get_config( 'justified_margins', $this->gallery_data );

				// this is a justified layout, no isotope even if it's selected in the DB
				$isotope = false;

			}

			$extra_css        = apply_filters( 'envira_gallery_output_extra_css', $extra_css, $this->gallery_data );

			//Grab the raw data
			if ( $data['config']['type'] == 'dynamic' ){
				$data['gallery'] = $this->dynamic_images;
			}

			// Make sure were grabbing the proper settings
			// Experiment: For performance reasons, pull the raw gallery image instead of calling envira_get_gallery_images twice
			$gallery_images_raw = envira_get_gallery_images( $gallery_id, true, $data );
			$gallery_images_json = json_encode( $gallery_images_raw );

			$options_id       = $data['config']['type'] == 'dynamic' ? $data['dynamic_id'] : $gallery_id;
			$gallery_config   = "data-gallery-config='". envira_get_gallery_config( $options_id, false, $data )."'";
			$gallery_images   = "data-gallery-images='". $gallery_images_json ."'";
			$lb_theme_options = "data-lightbox-theme='". envira_load_lightbox_config( $main_id  ) ."'"; //using main id for Dynamic to make sure we load the proper data
			$gallery_id       = 'data-envira-id="'.$gallery_id.'"';

			$temp_gallery_markup .= '<div '.$gallery_id .' '.$gallery_config .' '.$gallery_images.' '.$lb_theme_options.' data-row-height="'.$row_height.'" data-justified-margins="'.$justified_margins.'" data-gallery-theme="'.$justified_gallery_theme.'" id="envira-gallery-' . sanitize_html_class( $this->gallery_data['id'] ) . '" class="envira-gallery-public '.$extra_css.' envira-gallery-' . sanitize_html_class( envira_get_config( 'columns', $this->gallery_data ) ) . '-columns envira-clear' . $isotope . '" data-envira-columns="' . envira_get_config( 'columns', $this->gallery_data ) . '">';

			// $images = envira_get_gallery_images( $data['id'], true, $data );

			// Start image loop
			foreach ( (array) $this->gallery_data['gallery'] as $id => $item ) {

				// Skip over images that are pending (ignore if in Preview mode).
				if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
					continue;
				}

				// Lets check if this gallery has already been output on the page
				if ( ! in_array( $id, $this->gallery_item_ids ) ) {
					$this->gallery_item_ids[] = $id;
				}

				// Add the gallery item to the markup
				$temp_gallery_markup = $this->generate_gallery_item_markup( $temp_gallery_markup, $this->gallery_data, $item, $id, $i, $gallery_images_raw );

				// Check the counter - if we are an instagram gallery AND there's a limit, then stop here
				if ( isset( $atts['limit'] ) && is_numeric( $atts['limit'] ) && $this->gallery_data['config']['type'] == 'instagram' && $i >= $atts['limit'] ) {
					break;
				}

				// Increment the iterator.
				$i++;

			}
			// End image loop

			// Filter output before starting this gallery item.
			$temp_gallery_markup  = apply_filters( 'envira_gallery_output_before_item', $temp_gallery_markup, $id, $item, $data, $i );
			$temp_gallery_markup .= '</div>';

			// Description
			if ( isset( $this->gallery_data['config']['description_position'] ) && $this->gallery_data['config']['description_position'] == 'below' ) {

				$temp_gallery_markup = $this->description( $temp_gallery_markup, $this->gallery_data );

			}

			$temp_gallery_markup    = apply_filters( 'envira_gallery_temp_output_after_container', $temp_gallery_markup, $this->gallery_data );
			$this->gallery_markup   = apply_filters( 'envira_gallery_output_after_container', $this->gallery_markup .= $temp_gallery_markup, $this->gallery_data );
			$this->gallery_markup  .= '</div>';
			$this->gallery_markup   = apply_filters( 'envira_gallery_output_end', $this->gallery_markup, $this->gallery_data );

			// Remove any contextual filters so they don't affect other galleries on the page.
			if ( envira_get_config( 'mobile', $this->gallery_data ) ) {
				remove_filter( 'envira_gallery_output_image_attr', array( $this, 'mobile_image' ), 999, 4 );
			}

			// Add no JS fallback support.
			$no_js  = '<noscript>';
			$no_js .= $this->get_indexable_images( $data['id'] );
			$no_js .= '</noscript>';

			$this->gallery_markup .= apply_filters( 'envira_gallery_output_noscript', $no_js, $this->gallery_data );

			$transient = set_transient( '_eg_fragment_' . $data['gallery_id'] , $this->gallery_markup, DAY_IN_SECONDS );

			// Increment the counter.
			$this->counter++;

		}

		$this->data[ $data['id'] ]  = $this->gallery_data;

		// Return the gallery HTML.
		return apply_filters( 'envira_gallery_output', $this->gallery_markup, $this->gallery_data );

	}

	/**
	 * shortcode_link function.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	public function shortcode_link( $atts, $content = null ){

		global $post;

		// If no attributes have been passed, the gallery should be pulled from the current post.
		$gallery_id = false;

		if ( empty( $atts ) ) {

			$gallery_id = $post->ID;
			$data       = is_preview() ? _envira_get_gallery( $gallery_id ) : envira_get_gallery( $gallery_id );

		} else if ( isset( $atts['id'] ) && !isset( $atts['dynamic'] ) ) {

			$gallery_id = (int) $atts['id'];
			$data       = is_preview() ? _envira_get_gallery( $gallery_id ) : envira_get_gallery( $gallery_id );

		} else if ( isset( $atts['slug'] ) ) {

			$gallery_id = $atts['slug'];
			$data       = is_preview() ? _envira_get_gallery_by_slug( $gallery_id ) : envira_get_gallery_by_slug( $gallery_id );

		} else {

			// A custom attribute must have been passed. Allow it to be filtered to grab data from a custom source.
			$data       = apply_filters( 'envira_gallery_custom_gallery_data', false, $atts, $post );
			$gallery_id = $data['config']['id'];

		}

		$this->link_data = $data;

		// Load custom lightbox themes if necessary, don't load if user hasn't enabled lightbox
		if ( envira_get_config( 'lightbox_enabled', $this->link_data ) ) {

			if ( 'base' !== envira_get_config( 'lightbox_theme', $this->link_data ) ) {

				envira_load_lightbox_theme( envira_get_config( 'lightbox_theme', $this->link_data ) );

			}

		}
		$lazy_loading_delay = isset( $this->link_data['config']['lazy_loading_delay'] ) ? intval($this->link_data['config']['lazy_loading_delay']) : 500;

		// Load scripts and styles.
		wp_enqueue_style( ENVIRA_SLUG . '-style' );
		wp_enqueue_script( ENVIRA_SLUG . '-script' );
		wp_localize_script( ENVIRA_SLUG . '-script', 'envira_gallery', array(
			'debug'         => ( defined( 'ENVIRA_DEBUG' ) && ENVIRA_DEBUG ? true : false ),
			'll_delay'      => (string) $lazy_loading_delay,
			'll_initial'    => 'false',
			'll'            => envira_get_config( 'lazy_loading', $data ) == 1 ? 'true' : 'false',
			'mobile'        => $this->is_mobile

		) );

		$gallery_config   = "data-gallery-config='". envira_get_gallery_config( $gallery_id )."'";
		$gallery_images   = "data-gallery-images='". envira_get_gallery_images( $gallery_id ) ."'";
		$lb_theme_options = "data-lightbox-theme='". envira_load_lightbox_config( $gallery_id ) ."'";
		// Run a hook before the gallery output begins but after scripts and inits have been set.
		do_action( 'envira_link_before_output', $this->link_data );

		$output = '<a id="envira-links-' . $gallery_id . '" class="envira-gallery-links" href="#" ' . $gallery_config . ' ' . $gallery_images . ' ' . $lb_theme_options . ' >';

		$output .= $content;

		$output .='</a>';

		return apply_filters( 'envira_link_shortcode_output', $output );

	}

	/**
	* Outputs an individual gallery item in the grid
	*
	* @since 1.7.1
	*
	* @param    string  $gallery    Gallery HTML
	* @param    array   $data       Gallery Config
	* @param    array   $item       Gallery Item (Image)
	* @param    int     $id         Gallery Image ID
	* @param    int     $i          Index
	* @return   string              Gallery HTML
	*/
	public function generate_gallery_item_markup( $gallery, $data, $item, $id, $i, $images = false ) {

		// Skip over images that are pending (ignore if in Preview mode).
		if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {

			return $gallery;

		}

		//Grab the raw data
		if ( $data['config']['type'] == 'dynamic' ){
			$data['gallery'] = $this->dynamic_images;
		}

		if ( !$images ) {
			$images = envira_get_gallery_images( $data['id'], true, $data );
		}
		$raw    = false;
		$layout = isset ( $data['config']['gallery_layout'] ) ? $data['config']['layout'] :$data['config']['columns'] ;

		if ( is_array( $images ) && isset( $images[ $id ] ) ) {

			$raw = $images[ $id ];

		} else if ( is_object( $images ) && isset( $images->$id ) ) {

			$raw = get_object_vars($images->$id);

		}

		if ( isset( $data['config']['sort_order'] ) && $data['config']['sort_order'] === '1' && false != $raw ){
			$raw['index'] = $i;
		}

		$item             = apply_filters( 'envira_gallery_output_item_data', $item, $id, $data, $i );
		$imagesrc         = $this->get_image_src( $id, $item, $data ); // Get image and image retina URLs
		$image_src_retina = $this->get_image_src( $id, $item, $data, false, true );
		$placeholder      = wp_get_attachment_image_src( $id, 'medium' ); // $placeholder is null because $id is 0 for instagram?
		$output_item      = '';

		// If we don't get an imagesrc, it's likely because of an error w/ dynamic
		// So to prevent JS errors or not rendering the gallery at all, return the gallery HTML because we can't render without it
		if ( !$imagesrc ) {

			return $gallery;

		}

		// Get some config values that we'll reuse for each image
		$padding          = absint( round( envira_get_config( 'gutter', $data ) / 2 ) );
		$gallery          = apply_filters( 'envira_gallery_output_before_item', $gallery, $id, $item, $data, $i ); // Filter output before starting this gallery item.
		$item             = $this->maybe_change_link( $id, $item, $data ); // Maybe change the item's link if it is an image and we have an image size defined for the Lightbox
		$schema_microdata = apply_filters( 'envira_gallery_output_schema_microdata_imageobject', 'itemscope itemtype="http://schema.org/ImageObject"', $data ); // Schema.org microdata ( Itemscope, etc. ) interferes with Google+ Sharing... so we are adding this via filter rather than hardcoding

		$output  = '<div id="envira-gallery-item-' . sanitize_html_class( $id ) . '" class="' . $this->get_gallery_item_classes( $item, $i, $data ) . '" style="padding-left: ' . $padding . 'px; padding-bottom: ' . envira_get_config( 'margin', $data ) . 'px; padding-right: ' . $padding . 'px;" ' . apply_filters( 'envira_gallery_output_item_attr', '', $id, $item, $data, $i ) . ' ' . $schema_microdata . '>';

		$output .= '<div class="envira-gallery-item-inner">';
		$output  = apply_filters( 'envira_gallery_output_before_link', $output, $id, $item, $data, $i );

		// Top Left box
		$output .= '<div class="envira-gallery-position-overlay envira-gallery-top-left">';
		$output  = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'top-left' );
		$output .= '</div>';

		// Top Right box
		$output .= '<div class="envira-gallery-position-overlay envira-gallery-top-right">';
		$output  = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'top-right' );
		$output .= '</div>';

		// Bottom Left box
		$output .= '<div class="envira-gallery-position-overlay envira-gallery-bottom-left">';
		$output  = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'bottom-left' );
		$output .= '</div>';

		// Bottom Right box
		$output .= '<div class="envira-gallery-position-overlay envira-gallery-bottom-right">';
		$output  = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'bottom-right' );
		$output .= '</div>';

		// check and see if we are using a certain gallery theme, so we can determine if caption is sent
		$gallery_theme = envira_get_config( 'gallery_theme', $data );

		if ( ( $gallery_theme == 'captioned' || $gallery_theme == 'polaroid' ) && ( envira_get_config( 'additional_copy_caption', $data ) && ( envira_get_config( 'columns', $data ) != 0 ) ) ) {

			// don't display the caption, because the gallery theme will take care of this
			$caption = false;

		} else {

			$caption_array = array();

            if ( envira_get_config( 'additional_copy_automatic_title', $data ) && isset( $item['title'] ) ) {

                $caption_array[] = strip_tags( htmlspecialchars( $item['title'] ) );

            }

            if ( envira_get_config( 'additional_copy_automatic_caption', $data ) && isset( $item['caption'] ) ) {

                $caption_array[] = str_replace( array( "\r\n","\r","\n","\\r","\\n","\\r\\n" ), '<br />', strip_tags( esc_attr( $item['caption'] ) ) );

            }

            $caption = implode( ' - ', $caption_array );

		}

		// Show caption BUT if the user has overrided with the title, show that instead
		if ( !envira_get_config( 'lightbox_title_caption', $data ) || envira_get_config( 'lightbox_title_caption', $data ) != 'title' ) {

			$lightbox_caption = isset( $item['caption'] ) ? do_shortcode( str_replace( array("\r\n","\r","\n","\\r","\\n","\\r\\n"), '<br />', $item['caption'] ) ) : false;

		} else {

			$lightbox_caption = isset( $item['title'] ) ? do_shortcode( str_replace( array("\r\n","\r","\n","\\r","\\n","\\r\\n"), '<br />', strip_tags( htmlspecialchars( $item['title'] ) ) ) ) : false;

		}

		// Allow changes to lightbox caption
		$lightbox_caption = apply_filters( 'envira_gallery_output_lightbox_caption', $lightbox_caption, $data, $id, $item, $i );

		// Cap the length of the lightbox caption for light/dark themes
		if ( envira_get_config( 'lightbox_theme', $data ) == 'base_dark' || envira_get_config( 'lightbox_theme', $data ) == 'base_light' ) {

			$lightbox_caption_limit = apply_filters( 'envira_gallery_output_lightbox_caption_limit', 100, $data, $id, $item, $i );

			if ( strlen($lightbox_caption) > $lightbox_caption_limit ) {

				$lightbox_caption = substr($lightbox_caption, 0, strrpos(substr($lightbox_caption, 0, $lightbox_caption_limit), ' ')) . '...';

			}

		}

		$lightbox_caption       = htmlentities( $lightbox_caption );
		$envira_gallery_class   = 'envira-gallery-' . sanitize_html_class( $data['id'] );

		// Link Target
		$external_link_array    = false;

		// If this is a dynamic gallery and the user has passed in a comma-delimited list of URLS via "external" we should use these, otherwise go with normal
		if ( isset( $data['config']['external'] ) && !empty( $data['config']['external'] ) ) {

			$external_link_array = explode("," , $data['config']['external'] );

			if ( is_array( $external_link_array ) ) {

				// determine where we are in the queue, override link if there's one
				if ( isset( $external_link_array[$i - 1] ) ) {

					$item['link']           = $external_link_array[$i - 1]; // esc_url is filtered below
					$item['link_type']      = 'external';
					$envira_gallery_class   = false;

				}

			}

		}

		// add $item filter for any last minute adjustments, such as overriding
		// $item['link'] with instagram or change link_type

		$item = apply_filters( 'envira_gallery_item_before_link', $item, $data, $id, $i, $this->is_mobile );

		// dynamic gallery isn't happening and there's an instagram_link, override
		if ( !$external_link_array && ! empty( $item['instagram_link'] ) ) {

			$item['link'] = $item['instagram_link'];

		}

		$target = ! empty( $item['target'] ) ? 'target="' . $item['target'] . '"' : false;

		$create_link = ! empty( $item['link'] ) && ( envira_get_config( 'gallery_link_enabled', $data ) || envira_get_config( 'lightbox_enabled', $data ) ) ? true : false;

		// Determine if we create a link.
		// If this is a mobile device and the user has disabled lightbox, there should not be a link
		if ( $this->is_mobile && ( !envira_get_config( 'mobile_gallery_link_enabled', $data ) && !envira_get_config( 'lightbox_enabled', $data ) ) || ( isset( $data['config']['type']) && $data['config']['type'] == 'instagram' && ! $data['config']['instagram_link'] ) ) {
			$create_link = false;
		}

		$create_link        = apply_filters( 'envira_gallery_create_link', $create_link, $data, $id, $item, $i, $this->is_mobile ); // Filter the ability to create a link
		$schema_microdata   = apply_filters( 'envira_gallery_output_schema_microdata_itemprop_contenturl', 'itemprop="contentUrl"', $data, $id, $item, $i ); // Schema.org microdata ( itemprop, etc. ) interferes with Google+ Sharing... so we are adding this via filter rather than hardcoding

		if ( $create_link != false ) {

			$this->is_mobile_thumb = ( isset( $item['mobile_thumb'] ) && ! empty( $item['mobile_thumb'] ) && !is_wp_error(  $item['mobile_thumb'] ) ) ? $item['mobile_thumb'] : esc_attr( $item['src'] );

			// fallback to src if the item thumb isnt set.
			$thumb = $item['thumb'] ? $item['thumb'] : $item['src'];

			if ( isset( $data['config']['mobile_thumbnails_width'] ) && isset( $data['config']['mobile_thumbnails_width'] ) ) {

				$item['mobile_thumb'] = envira_resize_image($item['src'], $data['config']['mobile_thumbnails_width'], $data['config']['mobile_thumbnails_height'], true, envira_get_config( 'crop_position', $data ), 100, false, $data, false);

			}

			$thumb = $this->is_mobile && !is_wp_error( $item['mobile_thumb'] ) ? $item['mobile_thumb'] : $thumb;

			$title = str_replace('<', '&lt;', $item['title']); // not sure why this was not encoded
			$ig = envira_get_config('type', $data) == 'instagram' ? ' data-src="'.$item['src'].'"' : '';

			// allow addons to change the link
			$link_href = apply_filters( 'envira_gallery_link_href', $item['link'], $data, $id, $item, $i, $this->is_mobile );
			$output .= '<a ' . ( ( isset($item['link_type']) && $item['link_type'] == 'external' ) ? '' : 'data-envirabox="'. sanitize_html_class( $data['id'] ). '"' ) . $target . ' href="' . esc_url( $item['link'] ) . '" class="envira-gallery-' . sanitize_html_class( $data['id'] ) . ' envira-gallery-link " title="' . strip_tags( htmlspecialchars( $title ) ) . '" data-envira-item-id="'. $id .'" data-caption="' . $lightbox_caption . '" data-envira-retina="' . ( isset( $item['lightbox_retina_image'] ) ? $item['lightbox_retina_image'] : '' ) . '" data-thumb="' . esc_attr( $thumb ). '"' . ( ( isset($item['link_new_window']) && $item['link_new_window'] == 1 ) ? ' target="_blank"' : '' ) . ' ' . apply_filters( 'envira_gallery_output_link_attr', '', $id, $item, $data, $i ) . ' '.$schema_microdata.' ' .$ig.'>';

		}

		$output                 = apply_filters( 'envira_gallery_output_before_image', $output, $id, $item, $data, $i );
		$gallery_theme          = envira_get_config( 'columns', $data ) == 0 ? ' envira-' . envira_get_config( 'justified_gallery_theme', $data ) : '';
		$gallery_theme_suffix   = ( envira_get_config( 'justified_gallery_theme_detail', $data ) ) === 'hover' ? '-hover' : false;
		$schema_microdata       = apply_filters( 'envira_gallery_output_schema_microdata_itemprop_thumbnailurl', 'itemprop="thumbnailUrl"', $data, $id, $item, $i ); // Schema.org microdata ( itemprop, etc. ) interferes with Google+ Sharing... so we are adding this via filter rather than hardcoding
		$envira_lazy_load       = envira_get_config( 'lazy_loading', $data ) == 1 ? 'envira-lazy' : ''; // Check if user has lazy loading on - if so, we add the css class

		// Determine/confirm the width/height of the image
		// $placeholder should hold it but not for instagram
		if ( $this->is_mobile == 'mobile' && !envira_get_config( 'mobile', $data ) ) { // if the user is viewing on mobile AND user unchecked 'Create Mobile Gallery Images?' in mobile tab

			$output_src = $item['src'];

		} else if ( envira_get_config( 'crop', $data ) ) { // the user has selected the image to be cropped

			$output_src = $imagesrc;

		} else if ( envira_get_config( 'image_size', $data ) && $imagesrc ) { // use the image being provided thanks to the user selecting a unique image size

			$output_src = $imagesrc;

		} else if ( !empty( $item['src'] ) ) {

			$output_src = $item['src'];

		} else if ( !empty( $placeholder[0] ) ) {

			$output_src = $placeholder[0];

		} else {

			$output_src = false;

		}


		if ( envira_get_config( 'crop', $data ) && envira_get_config( 'crop_width', $data ) && envira_get_config( 'image_size', $data ) != 'full' ) {

			$output_width = envira_get_config( 'crop_width', $data );

		} else if ( envira_get_config( 'columns', $data ) != 0 && envira_get_config( 'image_size', $data ) && envira_get_config( 'image_size', $data ) != 'full' && envira_get_config( 'crop_width', $data ) && envira_get_config( 'crop_height', $data ) ) {

			$output_width = envira_get_config( 'crop_width', $data );

		} else if ( isset( $data['config']['type'] ) && $data['config']['type'] == 'instagram' && strpos($imagesrc, 'cdninstagram' ) !== false ) {

			// if this is an instagram image, @getimagesize might not work
			// therefore we should try to extract the size from the url itself
			if ( strpos( $imagesrc , '150x150' ) ) {

				$output_width = '150';

			} else if ( strpos( $imagesrc , '640x640' ) ) {

				$output_width = '640';

			} else {

				$output_width = '150';

			}


		} else if ( !empty( $placeholder[1] ) ) {

			$output_width = $placeholder[1];

		} else if ( !empty( $item['width'] ) ) {

			$output_width = $item['width'];

		} else {

			$output_width = false;

		}

		if ( envira_get_config( 'crop', $data ) && envira_get_config( 'crop_width', $data ) && envira_get_config( 'image_size', $data ) != 'full' ) {

			$output_height = envira_get_config( 'crop_height', $data );

		} else if ( envira_get_config( 'columns', $data ) != 0 && envira_get_config( 'image_size', $data ) && envira_get_config( 'image_size', $data ) != 'full' && envira_get_config( 'crop_width', $data ) && envira_get_config( 'crop_height', $data ) ) {

			$output_height = envira_get_config( 'crop_height', $data );

		} else if ( isset( $data['config']['type'] ) && $data['config']['type'] == 'instagram' && strpos($imagesrc, 'cdninstagram' ) !== false ) {

			// if this is an instagram image, @getimagesize might not work
			// therefore we should try to extract the size from the url itself
			if ( strpos( $imagesrc , '150x150' ) ) {

				$output_height = '150';

			} else if ( strpos( $imagesrc , '640x640' ) ) {

				$output_height = '640';

			} else {
				$output_height = '150';

			}

		} else if ( !empty( $placeholder[2] ) ) {

			$output_height = $placeholder[2];

		} else if ( !empty( $item['height'] ) ) {

			$output_height = $item['height'];

		} else {

			$output_height = false;

		}

		/* add filters for width and height, primarily so dynamic can add width and height */
		$output_width   = apply_filters( 'envira_gallery_output_width', $output_width, $id, $item, $data, $i, $output_src );
		$output_height  = apply_filters( 'envira_gallery_output_height', $output_height, $id, $item, $data, $i, $output_src );

		/* if $raw is an object, convert to array, although this shouldn't be possible? */
		if ( is_object( $raw ) && isset( $raw->index ) ) {
			$raw = get_object_vars($raw);
		}

		if ( envira_get_config( 'columns', $data ) == 0 ) {

			// Automatic
			$output_item = '<img id="envira-gallery-image-' . sanitize_html_class( $id ) . '" class="envira-gallery-image envira-gallery-image-' . $i . $gallery_theme . $gallery_theme_suffix . ' '.$envira_lazy_load.'" data-envira-index="' . $raw['index'] . '" src="' . esc_url( $output_src ) . '"' . ' width="' . envira_get_config( 'crop_width', $data ) . '" height="' . envira_get_config( 'crop_height', $data ) . '" tabindex="0" data-envira-src="' . esc_url( $output_src ) . '" data-envira-gallery-id="' . $data['id'] . '" data-envira-item-id="' . $id . '" data-caption="' . $lightbox_caption . '"  alt="' . esc_attr( $item['alt'] ) . '" title="' . strip_tags( esc_attr( $item['title'] ) ) . '" ' . apply_filters( 'envira_gallery_output_image_attr', '', $id, $item, $data, $i ) . ' ' . $schema_microdata. ' data-envira-srcset="' . esc_url( $output_src ) . ' 400w,' . esc_url( $output_src ) . ' 2x" data-envira-width="'.$output_width.'" data-envira-height="'.$output_height.'" srcset="' . ( ( $envira_lazy_load ) ? 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' : esc_url( $image_src_retina ) . ' 2x' ) . '" data-safe-src="'. ( ( $envira_lazy_load ) ? 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' : esc_url( $output_src ) ) . '" />';

		} else {

			if ( $envira_lazy_load ) {

				if ( $output_height > 0 && $output_width > 0 ) {
					$padding_bottom = ( $output_height / $output_width ) * 100;
				} else {
					// this shouldn't be happening, but this avoids a debug message
					$padding_bottom = 100;
				}
				//  $output_item .= '<div >';
				$output_item .= '<div class="envira-lazy" data-envira-changed="false" data-width="'.$output_width.'" data-height="'.$output_height.'" style="padding-bottom:'.$padding_bottom.'%;">';

			}

			// If the user has asked to set width and height dimensions to images, let's determine what those values should be
			if ( envira_get_config( 'image_size', $data ) && envira_get_config( 'image_size', $data ) == 'default' ) {

				$width_attr_value   = envira_get_config( 'crop_width', $data );
				$height_attr_value  = envira_get_config( 'crop_height', $data );

			} else if ( envira_get_config( 'image_size', $data ) && envira_get_config( 'image_size', $data ) == 'full' ) {

				// is there a way to get the oringial width/height outside of this?
				$src                = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'full' ), $id, $item, $data );
				$width_attr_value   = $src[1];
				$height_attr_value  = $src[2];

			} else if ( $output_width && $output_height ) {

				$width_attr_value   = $output_width;
				$height_attr_value  = $output_height;

			} else {

				$width_attr_value = $height_attr_value = false;

			}

			$output_item .= '<img id="envira-gallery-image-' . sanitize_html_class( $id ) . '" tabindex="0" class="envira-gallery-image envira-gallery-image-' . $i . $gallery_theme . $gallery_theme_suffix . ' '.$envira_lazy_load.'" data-envira-index="' . $raw['index'] . '" src="' . esc_url( $output_src ) . '" width="' . $width_attr_value . '" height="' . $height_attr_value . '" data-envira-src="' . esc_url( $output_src ) . '" data-envira-gallery-id="' . $data['id'] . '" data-envira-item-id="' . $id . '" data-caption="' . $lightbox_caption . '"  alt="' . esc_attr( $item['alt'] ) . '" title="' . strip_tags( esc_attr( $item['title'] ) ) . '" ' . apply_filters( 'envira_gallery_output_image_attr', '', $id, $item, $data, $i ) . ' ' . $schema_microdata . '" data-envira-srcset="' . esc_url( $output_src ) . ' 400w,' . esc_url( $output_src ) . ' 2x" srcset="' . ( ( $envira_lazy_load ) ? 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' : esc_url( $image_src_retina ) . ' 2x' ) . '" />';

			if ( $envira_lazy_load ) {

				$output_item .= '</div>';

			}


		}

		$output_item = apply_filters( 'envira_gallery_output_image', $output_item, $id, $item, $data, $i );

		// Add image to output
		$output .= $output_item;
		$output  = apply_filters( 'envira_gallery_output_after_image', $output, $id, $item, $data, $i );


		if ( $create_link ) {
			$output .= '</a>';

		}
		$output .= apply_filters( 'envira_gallery_output_caption', $output, $id, $item, $data, $i );

		$output = apply_filters( 'envira_gallery_output_after_link', $output, $id, $item, $data, $i );

		$output .= '</div>';

		$output .= '</div>';

		$output  = apply_filters( 'envira_gallery_output_single_item', $output, $id, $item, $data, $i );

		// Append the image to the gallery output
		$gallery .= $output;

		// Filter the output before returning
		$gallery    = apply_filters( 'envira_gallery_output_after_item', $gallery, $id, $item, $data, $i );

		return $gallery;

	}

	/**
	* Add the 'property' tag to stylesheets enqueued in the body
	*
	* @since 1.4.1.1
	*/
	public function add_stylesheet_property_attribute( $tag ) {

		// If the <link> stylesheet is any Envira-based stylesheet, add the property attribute
		if ( strpos( $tag, "id='envira-" ) !== false ) {

			$tag = str_replace( '/>', 'property="stylesheet" />', $tag );

		}

		return $tag;

	}

	/**
	* Builds HTML for the Gallery Description
	*
	* @since 1.3.0.2
	*
	* @param string $gallery Gallery HTML
	* @param array $data Data
	* @return HTML
	*/
	public function description( $gallery, $data ) {

		// Get description.
		$description = $data['config']['description'];

		$gallery    .= '<div class="envira-gallery-description envira-gallery-description-above" style="padding-bottom: ' . envira_get_config( 'margin', $data ) . 'px;">';
		$gallery     = apply_filters( 'envira_gallery_output_before_description', $gallery, $data );

		// If the WP_Embed class is available, use that to parse the content using registered oEmbed providers.
		if ( isset( $GLOBALS['wp_embed'] ) ) {

			$description = $GLOBALS['wp_embed']->autoembed( $description );

		}

		// Get the description and apply most of the filters that apply_filters( 'the_content' ) would use
		// We don't use apply_filters( 'the_content' ) as this would result in a nested loop and a failure.
		$description = wptexturize( $description );
		$description = convert_smilies( $description );
		$description = wpautop( $description );
		$description = prepend_attachment( $description );
		$description = wp_make_content_images_responsive( $description );

		// Append the description to the gallery output.
		$gallery .= $description;

		// Filter the gallery HTML.
		$gallery     = apply_filters( 'envira_gallery_output_after_description', $gallery, $data );

		$gallery .= '</div>';

		return $gallery;

	}

	/**
	 * Set the title display per theme
	 *
	 * @since 1.7.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envira_gallery_title_type( $title_display, $data ) {

		// Get gallery theme
		$lightbox_theme = envira_get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$title_display = 'fixed';
				break;

		}

		return $title_display;

	}

	/**
	 * Helper method for adding custom gallery classes.
	 *
	 * @since 1.7.0
	 *
	 * @param array $data The gallery data to use for retrieval.
	 * @return string        String of space separated gallery classes.
	 */
	public function get_gallery_classes( $data ) {

		// Set default class.
		$classes     = array(
			'envira-gallery-wrap',
			'envira-gallery-theme-' . envira_get_config( 'gallery_theme', $data ),
		);

		// If we have custom classes defined for this gallery, output them now.
		foreach ( (array) envira_get_config( 'classes', $data ) as $class ) {
			$classes[] = $class;
		}

		// If the gallery has RTL support, add a class for it.
		if ( envira_get_config( 'rtl', $data ) ) {
			$classes[] = 'envira-gallery-rtl';
		}

		// Allow filtering of classes and then return what's left.
		$classes = apply_filters( 'envira_gallery_output_classes', $classes, $data );

		return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );

	}

	/**
	 * Helper method for adding custom gallery classes.
	 *
	 * @since 1.0.4
	 *
	 * @param array $item Array of item data.
	 * @param int $i        The current position in the gallery.
	 * @param array $data The gallery data to use for retrieval.
	 * @return string        String of space separated gallery item classes.
	 */
	public function get_gallery_item_classes( $item, $i, $data ) {

		// Set default classes.
		$classes = array(
			'envira-gallery-item',
			'envira-gallery-item-' . $i,
		);

		if ( isset( $item['video_in_gallery'] ) && $item['video_in_gallery'] == 1 ) {

			$classes[] = 'envira-video-in-gallery';

		}

		// If istope exists, add that
		if ( isset( $data['config']['isotope'] ) && $data['config']['isotope'] == 1 ) {

			$classes[] = 'enviratope-item';

		}

		// If lazy load exists, add that
		if ( isset( $data['config']['lazy_loading'] ) && $data['config']['lazy_loading'] ) {

			$classes[] = 'envira-lazy-load';

		}

		// Allow filtering of classes and then return what's left.
		$classes = apply_filters( 'envira_gallery_output_item_classes', $classes, $item, $i, $data );

		return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );

	}

	/**
	 * Changes the link attribute of an image, if the Lightbox config
	 * requires a different sized image to be displayed.
	 *
	 * @since 1.3.6
	 *
	 * @param int $id       The image attachment ID to use.
	 * @param array $item   Gallery item data.
	 * @param array $data   The gallery data to use for retrieval.
	 * @return array        Image array
	 */
	public function maybe_change_link( $id, $item, $data ) {

		// Check gallery config
		$image_size = envira_get_config( 'lightbox_image_size', $data );

		//check if the url is a valid image if not return it
		if (! envira_is_image( $item['link'] ) ) {
			return $item;
		}

		// Get media library attachment at requested size
		$image = wp_get_attachment_image_src( $id, $image_size );

		// Determine if the image url resides on a third-party site
        $url = preg_replace('/(?:https?:\/\/)?(?:www\.)?(.*)\/?$/i', '$1', network_site_url() );
        if ( strpos($item['link'], $url) === false  ){
            return $item;
        }

        // Determine if the image is entered by the user as an overide in the image modal
        // We are doing this by comparing the first few characters to see if the filename is a possible match
        // This covers the scenario of a filename being in the same upload folder as the oringial image, something probably rare.
        // We can't compare the entire string because the end of the string might contain misc characters, dropping, etc.

        $filename_image = basename( $image[0] );
        $filename_link = basename( $item['link'] );
        $pos = strspn($filename_image ^ $filename_link, "\0");

        // First few characters don't match, likely this is a different image in the same upload directory
        // The number can be changed to literally anything

        if ( $pos <= apply_filters( 'envira_gallery_check_image_file_name', 4, $filename_image, $filename_link, $data ) ) {
            return $item;
        }

		if ( ! is_array( $image ) ) {
			return $item;
		}

		// Inject new image size into $item

		$item['link'] = $image[0];

		return $item;

	}

	/**
	 * Helper method to retrieve the proper image src attribute based on gallery settings.
	 *
	 * @since 1.7.0
	 *
	 * @param int       $id         The image attachment ID to use.
	 * @param array     $item       Gallery item data.
	 * @param array     $data       The gallery data to use for retrieval.
	 * @param bool      $this->is_mobile        Whether or not to retrieve the mobile image.
	 * @param bool      $retina     Whether to return a retina sized image.
	 * @return string               The proper image src attribute for the image.
	 */
	public function get_image_src( $id, $item, $data, $mobile = false, $retina = false ) {

		// Define variable
		$src = false;

		//Check for mobile and mobile setting
		$type = $this->is_mobile && envira_get_config( 'mobile', $data ) ? 'mobile' : 'crop'; // 'crop' is misleading here - it's the key that stores the thumbnail width + height

		// If this image is an instagram, we grab the src and don't use the $id
		// otherwise using the $id refers to a postID in the database and has been known
		// at times to pull up the wrong thumbnail instead of the instagram one

		$instagram = false;

		if ( !empty( $item['src']) && strpos( $item['src'], 'cdninstagram' ) !== false ) :
			// using 'cdninstagram' because it seems all urls contain it - but be watchful in the future
			$instagram  = true;
			$src        = $item['src'];
			$image      = $item['src'];
		endif;

		$image_size = envira_get_config( 'image_size', $data );

		if ( !$src ) :

			if ( ( envira_get_config( 'crop', $data ) && $image_size == 'default' ) || $image_size == 'full' ){

				$src = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'full' ), $id, $item, $data, $this->is_mobile );

			}   elseif ( $image_size != 'full' && ! $retina ) {

				// Check if this Gallery uses a WordPress defined image size
				if ( $image_size != 'default'  ) {
					// If image size is envira_gallery_random, get a random image size.
					if ( $image_size == 'envira_gallery_random' ) {

						// Get random image sizes that have been chosen for this Gallery.
						$image_sizes_random = (array) envira_get_config( 'image_sizes_random', $data );

						if ( count( $image_sizes_random ) == 0 ) {
							// The user didn't choose any image sizes - use them all.
							$wordpress_image_sizes = envira_get_image_sizes( true );
							$wordpress_image_size_random_key = array_rand( $wordpress_image_sizes, 1 );
							$image_size = $wordpress_image_sizes[ $wordpress_image_size_random_key ]['value'];
						} else {
							$wordpress_image_size_random_key = array_rand( $image_sizes_random, 1 );
							$image_size = $image_sizes_random[ $wordpress_image_size_random_key ];
						}

						// Get the random WordPress defined image size
						$src = wp_get_attachment_image_src( $id, $image_size );
					} else {
						// Get the requested WordPress defined image size
						$src = wp_get_attachment_image_src( $id, $image_size );
					}
				} else {

					$isize = $this->find_clostest_size( $this->gallery_data ) != '' ? $this->find_clostest_size( $data ) : 'full';
					$src = apply_filters( 'envira_gallery_default_image_src', wp_get_attachment_image_src( $id, $isize ), $id, $item, $data, $this->is_mobile );

				}

			} else{

				$src = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'full' ), $id, $item, $data, $this->is_mobile );

			}

		endif;


		// Check if this returned an image
		if ( ! $src ) {
			// Fallback to the $item's image source
			$image = $item['src'];
		} else if ( ! $instagram ) {
			$image = $src[0];
		}


		// If we still don't have an image at this point, something went wrong
		if ( ! isset( $image ) ) {
			return apply_filters( 'envira_gallery_no_image_src', $item['link'], $id, $item, $data );
		}

		// Prep our indexable images.
		if ( $image && ! $this->is_mobile ) {
			$this->index[ $data['id'] ][ $id ] = array(
				'src' => $image,
				'alt' => ! empty( $item['alt'] ) ? $item['alt'] : ''
			);
		}

		// If the current layout is justified/automatic
		// if the image size is a WordPress size and we're not requesting a retina image we don't need to resize or crop anything.
		if ( $image_size != 'default' && ! $retina && $type != 'mobile' ) {
		// if ( ( $image_size != 'default' && ! $retina ) ) {
			// Return the image
			return apply_filters( 'envira_gallery_image_src', $image, $id, $item, $data );
		}
		$crop = envira_get_config( 'crop', $data );

		if( $crop || $type == 'mobile' ){

			// If the image size is default (i.e. the user has input their own custom dimensions in the Gallery),
			// we may need to resize the image now
			// This is safe to call every time, as resize_image() will check if the image already exists, preventing thumbnails
			// from being generated every single time.
			$args = array(
				'position'      => envira_get_config( 'crop_position', $data ),
				'width'         => envira_get_config( $type . '_width', $data ),
				'height'        => envira_get_config( $type . '_height', $data ),
				'quality'       => 100,
				'retina'        => $retina,
			);

			// If we're requesting a retina image, and the gallery uses a registered WordPress image size,
			// we need use the width and height of that registered WordPress image size - not the gallery's
			// image width and height, which are hidden settings.

			// if this is mobile, go with the mobile image settings, otherwise proceed?
			if ( $image_size != 'default' && $retina && $type != 'mobile' ) {
				// Find WordPress registered image size
				$wordpress_image_sizes = envira_get_image_sizes( true ); // true = WordPress only image sizes (excludes random

				foreach ( $wordpress_image_sizes as $size ) {

					if ( $size['value'] !== $image_size ) {
						continue;
					}

					// We found the image size. Use its dimensions
					if ( !empty( $size['width'] ) ) {
						$args['width'] = $size['width'];
					}
					if ( !empty( $size['height'] ) ) {
						$args['height'] = $size['height'];
					}
					break;

				}
			}

			// Filter
			$args   = apply_filters( 'envira_gallery_crop_image_args', $args );

			//Make sure we're grabbing the full image to crop.
			$image_to_crop = apply_filters( 'envira_gallery_crop_image_src', wp_get_attachment_image_src( $id, 'full' ), $id, $item, $data, $this->is_mobile );
			// Check if this returned an image
			if ( ! $image_to_crop ) {
				// Fallback to the $item's image source
				$image_to_crop = $item['src'];
			} else if ( ! $instagram ) {
				$image_to_crop = $src[0];
			}
			$resized_image = envira_resize_image( $image_to_crop, $args['width'], $args['height'], true, envira_get_config( 'crop_position', $data ), $args['quality'], $args['retina'], $data );

			// If there is an error, possibly output error message and return the default image src.
			if ( is_wp_error( $resized_image ) ) {
				// If WP_DEBUG is enabled, and we're logged in, output an error to the user
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG && is_user_logged_in() ) {
					echo '<pre>Envira: Error occured resizing image (these messages are only displayed to logged in WordPress users):<br />';
					echo 'Error: ' . $resized_image->get_error_message() . '<br />';
					echo 'Image: ' . $image . '<br />';
					echo 'Args: ' . var_export( $args, true ) . '</pre>';
				}

				// Return the non-cropped image as a fallback.
			} else {

				return apply_filters( 'envira_gallery_image_src', $resized_image, $id, $item, $data );

			}
		}
		//return full image
		return apply_filters( 'envira_gallery_image_src', $image, $id, $item, $data );

	}

	/**
	 * find_clostest_size function.
	 *
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function find_clostest_size( $data ){

		$image_sizes = envira_get_shortcode_image_sizes();
		$dimensions  = envira_get_config( 'dimensions', $data );
		$width       = envira_get_config( 'crop_width', $data );
		$height      = envira_get_config( 'crop_height', $data );
		$match       = false;

		usort( $image_sizes, array( $this, 'usort_callback' ) );

		foreach( $image_sizes as $num ) {

			$num['width']  = (int) $num['width'];
			$num['height'] = (int) $num['height'];

			//skip over sizes that are smaller
			if ( $num['height'] < $height || $num['width'] < $width ){
				continue;
			}

			if ( $num['width'] > $width && $num['height'] > $height ) {

				if ( $match === false ) {

					$match = true;
					$size = $num['name'];

					return $size;
				}

			}

		}

		return false;

	}

	/**
	 * Helper function for usort and php 5.3 >.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	function usort_callback( $a, $b ) {

		return intval( $a['width'] ) - intval( $b['width'] );

	}

	/**
	 * Outputs only the first image of the gallery inside a regular <div> tag
	 * to avoid styling issues with feeds.
	 *
	 * @since 1.0.5
	 *
	 * @param array $data         Array of gallery data.
	 * @return string $gallery Custom gallery output for feeds.
	 */
	public function do_feed_output( $data ) {

		$gallery = '<div class="envira-gallery-feed-output">';

			$gallery_data = (array) $data['gallery'];

			foreach ( $gallery_data as $id => $item ) {

				// Skip over images that are pending (ignore if in Preview mode).
				if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {

					continue;

				}

				$imagesrc = $this->get_image_src( $id, $item, $data );
				$gallery .= '<img class="envira-gallery-feed-image" tabindex="0" src="' . esc_url( $imagesrc ) . '" title="' . trim( esc_html( $item['title'] ) ) . '" alt="' .trim( esc_html( $item['alt'] ) ) . '" />';
				break;

			 }

		$gallery .= '</div>';

		return apply_filters( 'envira_gallery_feed_output', $gallery, $data );

	}

	/**
	 * Returns a set of indexable image links to allow SEO indexing for preloaded images.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $id         The slider ID to target.
	 * @return string $images String of indexable image HTML.
	 */
	public function get_indexable_images( $id ) {

		// If there are no images, don't do anything.
		$images = '';
		$i      = 1;

		if ( empty( $this->index[$id] ) ) {

			return $images;

		}

		foreach ( (array) $this->index[$id] as $attach_id => $data ) {

			$images .= '<img src="' . esc_url( $data['src'] ) . '" alt="' . esc_attr( $data['alt'] ) . '" />';
			$i++;

		}

		return apply_filters( 'envira_gallery_indexable_images', $images, $this->index, $id );

	}

	/**
     * Allow users to add a title or caption under an image for legacy galleries
	 *
	 * @since 1.7.0
	 *
	 * @param string    $output Output HTML
	 * @param int $id Image Attachment ID
	 * @param array $item Image Data
	 * @param array $data Gallery Data
	 * @param int $i Image Count
	 * @return string Output HTML
	*/
	public function gallery_image_caption_titles( $output, $id, $item, $data, $i ) {

		// for some reason - probably ajax - the $this->gallery_data is
		// empty on "load more" ajax pagination but $data comes through...
		if ( !$this->gallery_data ) {

			$this->gallery_data = $data;

		}

		// this only applies to legacy, not dark/light themes, etc.
		if ( envira_get_config( 'columns', $this->gallery_data ) == 0 ) {

			return false;

		}

		// get the gallery theme
		$gallery_theme = envira_get_config( 'gallery_theme', $data );

		// start the revised output
		$revised_output = '<div class="envira-gallery-captioned-data envira-gallery-captioned-data-' . $gallery_theme = envira_get_config( 'gallery_theme', $data ) . '">';

		$allowed_html_tags = apply_filters( 'envira_gallery_image_caption_allowed_html_tags', array(
			'a'  => array(
				'href'   => array(),
				'title'  => array(),
				'class'  => array(),
				'target' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'p'      => array(),
			'strike' => array(),
			'object' => array(),
		), $id, $item, $data, $i );

		// has user checked off title and is there a title in $item?
		if ( envira_get_config( 'additional_copy_title', $this->gallery_data ) == 1 && ! empty( $item['title'] ) ) {

			$revised_output .= '<span class="envira-title envira-gallery-captioned-text title-' . $id . '">' . wp_kses( $item['title'], $allowed_html_tags ) . '</span>';

		}

		// has user checked off title and is there a title in $item?
		if ( envira_get_config( 'additional_copy_caption', $this->gallery_data ) == 1 && ! empty( $item['caption'] ) ) {

				$revised_output .= '<span class="envira-caption envira-gallery-captioned-text caption-' . $id . '">' . wp_kses( $item['caption'], $allowed_html_tags ) . '</span>';

		}

		$revised_output .= '</div>';

		// check for line breaks, convert them to <br/>
		$revised_output = nl2br( $revised_output );

		return apply_filters( 'envira_gallery_image_caption_titles', $revised_output, $id, $item, $data, $i );

	}

}