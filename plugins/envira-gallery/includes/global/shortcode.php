<?php
/**
 * Shortcode class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author	Thomas Griffin
 */
class Envira_Gallery_Shortcode {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Holds the gallery data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Holds gallery IDs for init firing checks.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $done = array();

	/**
	 * Iterator for galleries on the page.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $counter = 1;

	/**
	 * Array of gallery ids on the page.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $gallery_ids = array();

	/**
	 * Array of gallery item ids on the page.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $gallery_item_ids = array();

	/**
	 * Holds image URLs for indexing.
	 *
	 * @since 1.0.0
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
	public $gallery_data = array();
	public $common;
	public $is_mobile;
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the base class object.
		$this->base =  Envira_Gallery::get_instance();
		$this->common = Envira_Gallery_Common::get_instance();
		$this->is_mobile = envira_mobile_detect()->isMobile();

		// Register main gallery style.
		wp_register_style( $this->base->plugin_slug . '-style', plugins_url( 'assets/css/envira.css', $this->base->file ), array(), $this->base->version );

		// if ( $this->get_config( 'columns', $data ) == 0 ) :
		wp_register_style( $this->base->plugin_slug . '-jgallery', plugins_url( 'assets/css/justifiedGallery.css', $this->base->file ), array(), $this->base->version );

		// Register main gallery script.
		wp_register_script( $this->base->plugin_slug . '-script', plugins_url( 'assets/js/min/envira-min.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );

		// Load hooks and filters.
		add_shortcode( 'envira-gallery', array( $this, 'shortcode' ) );
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'style_loader_tag', array( $this, 'add_stylesheet_property_attribute' ) );
		add_filter( 'envirabox_actions', array( $this, 'envirabox_actions' ), 101, 2 );
		add_filter( 'envirabox_inner_above', array( $this, 'envirabox_inner_above' ), 10, 2 );

		add_filter( 'envirabox_margin', array( $this, 'envirabox_margin' ), 10, 2 );
		add_filter( 'envirabox_padding', array( $this, 'envirabox_padding' ), 10, 2 );
		add_filter( 'envirabox_arrows', array( $this, 'envirabox_arrows' ), 10, 2 );
		add_filter( 'envirabox_gallery_thumbs_position', array( $this, 'envirabox_gallery_thumbs_position' ), 10, 2 );
		add_filter( 'envirabox_dynamic_margin', array( $this, 'envirabox_dynamic_margin' ), 10, 2 );
		add_filter( 'envirabox_dynamic_margin_amount', array( $this, 'envirabox_dynamic_margin_amount' ), 10, 2 );
		add_filter( 'envira_gallery_title_type', array( $this, 'envira_gallery_title_type' ), 10, 2 );
		add_filter( 'envira_always_show_title', array( $this, 'envira_always_show_title' ), 10, 2 );

		add_filter( 'envirabox_wrap_css_classes', array( $this, 'envira_supersize_wrap_css_class' ), 10, 2 );
	}

	/**
	 * Creates the shortcode for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @global object $post The current post object.
	 *
	 * @param array $atts Array of shortcode attributes.
	 * @return string	  The gallery output.
	 */
	public function shortcode( $atts ) {

		global $post;

		// If no attributes have been passed, the gallery should be pulled from the current post.
		$gallery_id = false;

		if ( empty( $atts ) ) {
			$gallery_id = $post->ID;
			$data		= is_preview() ? $this->base->_get_gallery( $gallery_id ) : $this->base->get_gallery( $gallery_id );
		} else if ( isset( $atts['id'] ) && !isset( $atts['dynamic'] ) ) {
			$gallery_id = (int) $atts['id'];
			$data		= is_preview() ? $this->base->_get_gallery( $gallery_id ) : $this->base->get_gallery( $gallery_id );
		} else if ( isset( $atts['slug'] ) ) {
			$gallery_id = $atts['slug'];
			$data		= is_preview() ? $this->base->_get_gallery_by_slug( $gallery_id ) : $this->base->get_gallery_by_slug( $gallery_id );
		} else {
			// A custom attribute must have been passed. Allow it to be filtered to grab data from a custom source.
			$data = apply_filters( 'envira_gallery_custom_gallery_data', false, $atts, $post );
			$gallery_id = $data['config']['id']; // was $data['config']['id']
		}

		// Lets check if this gallery has already been output on the page
		$data['gallery_id'] = $data['id'];

		if ( ! empty( $atts['counter'] ) ) {
			// we are forcing a counter so lets force the object in the gallery_ids
			$this->counter = $atts['counter'];
			$this->gallery_ids[] = $data['id'];
		}

		if ( ! in_array( $data['id'], $this->gallery_ids ) ) {
			$this->gallery_ids[] = $data['id'];
		}
		elseif( $this->counter > 1 ) {
			$data['id'] = $data['id'] . '_' . $this->counter;
		}

		if ( empty( $atts['presorted'] ) ) {
			$this->gallery_sort[ $data['id'] ] = false; // reset this to false, otherwise multiple galleries on the same page might get other ids, or other wackinesses
		}

		// Change the gallery order, if specified
		$data = $this->maybe_sort_gallery( $data, $data['id'] );

		// Limit the number of images returned, if specified
		// [envira-gallery id="123" limit="10"] would only display 10 images
		if ( isset( $atts['limit'] ) && is_numeric( $atts['limit'] ) ) {
			$images = array_slice( $data['gallery'], 0, absint( $atts['limit'] ), true );
			$data['gallery'] = $images;
		}

		// Allow the data to be filtered before it is stored and used to create the gallery output.
		$data = apply_filters( 'envira_gallery_pre_data', $data, $gallery_id );

		// If there is no data to output or the gallery is inactive, do nothing.
		if ( ! $data || empty( $data['gallery'] ) || isset( $data['status'] ) && 'inactive' == $data['status'] && ! is_preview() ) {
			return;
		}

		$this->gallery_data = $data;

		// Get rid of any external plugins trying to jack up our stuff where a gallery is present.
		$this->plugin_humility();

		// Prepare variables.
		$this->index[ $this->gallery_data['id'] ] = array();
		$gallery					= '';
		$i							= 1;

		// If this is a feed view, customize the output and return early.
		if ( is_feed() ) {
			return $this->do_feed_output( $this->gallery_data );
		}

		// Load scripts and styles.
		wp_enqueue_style( $this->base->plugin_slug . '-style' );
		wp_enqueue_style( $this->base->plugin_slug . '-jgallery' );
		wp_enqueue_script( $this->base->plugin_slug . '-script' );

		// Load custom gallery themes if necessary.
		if ( 'base' !== $this->get_config( 'gallery_theme', $this->gallery_data ) && $this->get_config( 'columns', $this->gallery_data ) > 0 ) {
			// if columns is zero, then it's automattic which means we do not load gallery themes because it will mess up the new javascript layout
			$this->load_gallery_theme( $this->get_config( 'gallery_theme', $this->gallery_data ) );
		}

		// Load custom lightbox themes if necessary, don't load if user hasn't enabled lightbox
		if ( $this->get_config( 'lightbox_enabled', $this->gallery_data ) ) {
			if ( 'base' !== $this->get_config( 'lightbox_theme', $this->gallery_data ) ) {
				$this->load_lightbox_theme( $this->get_config( 'lightbox_theme', $this->gallery_data ) );
			}
		}

		// Load gallery init code in the footer.
		add_action( 'wp_footer', array( $this, 'gallery_init' ), 1000 );

		// Run a hook before the gallery output begins but after scripts and inits have been set.
		do_action( 'envira_gallery_before_output', $this->gallery_data );

		// Apply a filter before starting the gallery HTML.
		$gallery = apply_filters( 'envira_gallery_output_start', $gallery, $this->gallery_data );

		// Build out the gallery HTML.
		$gallery .= '<div id="envira-gallery-wrap-' . sanitize_html_class( $this->gallery_data['id'] ) . '" class="' . $this->get_gallery_classes( $this->gallery_data ) . '" itemscope itemtype="http://schema.org/ImageGallery">';
			$gallery  = apply_filters( 'envira_gallery_output_before_container', $gallery, $this->gallery_data );

			// Description
			if ( isset( $this->gallery_data['config']['description_position'] ) && $this->gallery_data['config']['description_position'] == 'above' ) {
				$gallery = $this->description( $gallery, $this->gallery_data );
			}


			$opacity_insert = false;
			if ( $this->get_config( 'columns', $this->gallery_data ) == 0 ) {
				$opacity_insert = ' style="opacity: 0.0" ';
			}

			// add justified CSS?
			$extra_css = 'envira-gallery-justified-public';
			$row_height = false;
			$justified_gallery_theme = false;
			$justified_margins = false;

			if ( $this->get_config( 'columns', $this->gallery_data ) > 0 ) {

				$extra_css = false;
				// add isotope if the user has it enabled
				$isotope = $this->get_config( 'isotope', $this->gallery_data ) ? ' enviratope' : false;

			} else {

				if ( !$this->is_mobile ) {
					$row_height = $this->get_config( 'justified_row_height', $this->gallery_data ) ? $this->get_config( 'justified_row_height', $this->gallery_data ) : 150;
				} else {
					$row_height = $this->get_config( 'mobile_justified_row_height', $data ) ? $this->get_config( 'mobile_justified_row_height', $this->gallery_data ) : 80;
				}

				$justified_gallery_theme = $this->get_config( 'justified_gallery_theme', $this->gallery_data );
				$justified_margins = $this->get_config( 'justified_margins', $this->gallery_data ) ? $this->get_config( 'justified_margins', $this->gallery_data ) : '0';

				// this is a justified layout, no isotope even if it's selected in the DB
				$isotope = false;
			}
			$gallery .= '<div' .  $opacity_insert . ' data-row-height="'.$row_height.'" data-justified-margins="'.$justified_margins.'" data-gallery-theme="'.$justified_gallery_theme.'" id="envira-gallery-' . sanitize_html_class( $this->gallery_data['id'] ) . '" class="envira-gallery-public '.$extra_css.' envira-gallery-' . sanitize_html_class( $this->get_config( 'columns', $this->gallery_data ) ) . '-columns envira-clear' . $isotope . ( $this->get_config( 'css_animations', $this->gallery_data ) ? ' envira-gallery-css-animations' : '' ) . '" data-envira-columns="' . $this->get_config( 'columns', $this->gallery_data ) . '">';


				// Start image loop
				foreach ( $this->gallery_data['gallery'] as $id => $item ) {

					// Skip over images that are pending (ignore if in Preview mode).
					if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
						continue;
					}

					// Lets check if this gallery has already been output on the page
					if ( ! in_array( $id, $this->gallery_item_ids ) ) {
						$this->gallery_item_ids[] = $id;
					}

					// Add the gallery item to the markup
					$gallery = $this->generate_gallery_item_markup( $gallery, $this->gallery_data, $item, $id, $i );

					// Increment the iterator.
					$i++;

				}
				// End image loop

        // Filter output before starting this gallery item.
        $gallery  = apply_filters( 'envira_gallery_output_before_item', $gallery, $id, $item, $data, $i );

			$gallery .= '</div>';
			// Description
			if ( isset( $this->gallery_data['config']['description_position'] ) && $this->gallery_data['config']['description_position'] == 'below' ) {
				$gallery = $this->description( $gallery, $this->gallery_data );
			}

			$gallery  = apply_filters( 'envira_gallery_output_after_container', $gallery, $this->gallery_data );

		$gallery .= '</div>';
		$gallery  = apply_filters( 'envira_gallery_output_end', $gallery, $this->gallery_data );

		// Increment the counter.
		$this->counter++;

		// Remove any contextual filters so they don't affect other galleries on the page.
		if ( $this->get_config( 'mobile', $this->gallery_data ) ) {
			remove_filter( 'envira_gallery_output_image_attr', array( $this, 'mobile_image' ), 999, 4 );
		}

		// Add no JS fallback support.
		$no_js	  = '<noscript>';
		$no_js	 .= $this->get_indexable_images( $data['id'] );
		$no_js	 .= '</noscript>';
		$gallery .= apply_filters( 'envira_gallery_output_noscript', $no_js, $this->gallery_data );

		$this->data[ $data['id'] ]	= $this->gallery_data;

		// If supersize is enabled, lets go ahead and enqueue the CSS for it
		if ( $this->get_config( 'supersize', $this->gallery_data ) ) {
			wp_register_style( $this->base->plugin_slug . '-supersize-style', plugins_url( 'assets/css/envira-supersize.css', plugin_basename( $this->base->file ) ), array(), $this->base->version );
			wp_enqueue_style( $this->base->plugin_slug . '-supersize-style' );
		}


		// Return the gallery HTML.
		return apply_filters( 'envira_gallery_output', $gallery, $this->gallery_data );

	}

	/**
	* Outputs an individual gallery item in the grid
	*
	* @since 1.4.2.1
	*
	* @param	string	$gallery	Gallery HTML
	* @param	array	$data		Gallery Config
	* @param	array	$item		Gallery Item (Image)
	* @param	int		$id			Gallery Image ID
	* @param	int		$i			Index
	* @return	string				Gallery HTML
	*/
	public function generate_gallery_item_markup( $gallery, $data, $item, $id, $i ) {

		// Get some config values that we'll reuse for each image
		$padding = absint( round( $this->get_config( 'gutter', $data ) / 2 ) );
		$html5_attribute = ( ( $this->get_config( 'html5', $data ) == '1' ) ? 'data-envirabox-group' : 'rel' );
		$thumbnail_start_url = get_bloginfo( 'url' ) . '/' . get_bloginfo( 'url' );

		// Skip over images that are pending (ignore if in Preview mode).
		if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
			return $gallery;
		}

		$item	  = apply_filters( 'envira_gallery_output_item_data', $item, $id, $data, $i );

		// Get image and image retina URLs
		// These calls will generate thumbnails as necessary for us.
		$imagesrc = $this->get_image_src( $id, $item, $data );
		$image_src_retina = $this->get_image_src( $id, $item, $data, false, true );
		$placeholder = wp_get_attachment_image_src( $id, 'medium' );

        // If we don't get an imagesrc, it's likely because of an error w/ dynamic
        // So to prevent JS errors or not rendering the gallery at all, return the gallery HTML because we can't render without it
        if ( !$imagesrc ) { return $gallery; }

		// Filter output before starting this gallery item.
		$gallery  = apply_filters( 'envira_gallery_output_before_item', $gallery, $id, $item, $data, $i );

		// Maybe change the item's link if it is an image and we have an image size defined for the Lightbox
		$item = $this->maybe_change_link( $id, $item, $data );

		// Non-ASCII filenames fail when FILTER_VALIDATE_URL is applied to them when saving a gallery to generate thumbs
		// This resulted in the blog URL being prepended to the URL, therefore breaking the thumbnail URL
		// This reverts that change for the few edge cases where this happened
		if ( strpos( $item['thumb'], $thumbnail_start_url ) !== false ) {
			$item['thumb'] = str_replace( $thumbnail_start_url, get_bloginfo( 'url' ) . '/', $item['thumb'] );
		}

		if ( ! empty( $item['mobile_thumb'] ) && strpos( $item['mobile_thumb'], $thumbnail_start_url ) !== false ) {
			$item['mobile_thumb'] = str_replace( $thumbnail_start_url, get_bloginfo( 'url' ) . '/', $item['mobile_thumb'] );
		}

        // Schema.org microdata ( Itemscope, etc. ) interferes with Google+ Sharing... so we are adding this via filter rather than hardcoding
        $schema_microdata = apply_filters( 'envira_gallery_output_schema_microdata', 'itemscope itemtype="http://schema.org/ImageObject"', $gallery, $id, $item, $data, $i );

		$output	  = '<div id="envira-gallery-item-' . sanitize_html_class( $id ) . '" class="' . $this->get_gallery_item_classes( $item, $i, $data ) . '" style="padding-left: ' . $padding . 'px; padding-bottom: ' . $this->get_config( 'margin', $data ) . 'px; padding-right: ' . $padding . 'px;" ' . apply_filters( 'envira_gallery_output_item_attr', '', $id, $item, $data, $i ) . ' ' . $schema_microdata . '>';

			$output .= '<div class="envira-gallery-item-inner">';
			$output	 = apply_filters( 'envira_gallery_output_before_link', $output, $id, $item, $data, $i );

			$css_class = false; // no css classes yet

			// Top Left box
			//$css_class = apply_filters( 'envira_gallery_output_dynamic_position_css', $css_class, $output, $id, $item, $data, $i, 'top-left' );

			$output .= '<div class="envira-gallery-position-overlay ' . $css_class . ' envira-gallery-top-left">';
			$output	 = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'top-left' );
			$output .= '</div>';

			// Top Right box
			//$css_class = apply_filters( 'envira_gallery_output_dynamic_position_css', $css_class, $output, $id, $item, $data, $i, 'top-right' );

			$output .= '<div class="envira-gallery-position-overlay ' . $css_class . ' envira-gallery-top-right">';
			$output	 = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'top-right' );
			$output .= '</div>';

			// Bottom Left box
			//$css_class = apply_filters( 'envira_gallery_output_dynamic_position_css', $css_class, $output, $id, $item, $data, $i, 'bottom-left' );

			$output .= '<div class="envira-gallery-position-overlay ' . $css_class . ' envira-gallery-bottom-left">';
			$output	 = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'bottom-left' );
			$output .= '</div>';

			// Bottom Right box
			//$css_class = apply_filters( 'envira_gallery_output_dynamic_position_css', $css_class, $output, $id, $item, $data, $i, 'bottom-right' );

			$output .= '<div class="envira-gallery-position-overlay ' . $css_class . ' envira-gallery-bottom-right">';
			$output	 = apply_filters( 'envira_gallery_output_dynamic_position', $output, $id, $item, $data, $i, 'bottom-right' );
			$output .= '</div>';

			$caption = isset( $item['caption'] ) ? do_shortcode( str_replace( "\n", '<br />', esc_attr( $item['caption'] ) ) ) : false;

			// Link Target
			$target = ! empty( $item['target'] ) ? 'target="' . $item['target'] . '"' : false;

			// If there is an instagram link and the user has chosen to link to instagram, override the $link
			if ( ! empty( $item['instagram_link'] ) ) {

				$item['link'] = $item['instagram_link'];

			}

			// Determine if we create a link.
			// If this is a mobile device and the user has disabled lightbox, there should not be a link
			// "Turn off the lightbox then the image shouldn't be clicked"

			if ( $this->is_mobile && !$this->get_config( 'lightbox_enabled', $data ) ) {
				$create_link = false;
			} else if ( $data['config']['type'] == 'instagram' && ! $data['config']['instagram_link'] ) {
				$create_link = false;
			} else if ( ! empty( $item['link'] ) ) {
				$create_link = true;
			} else {
				$create_link = false;
			}


			if ( $create_link ) {
				$this->is_mobile_thumb = ! empty( $item['mobile_thumb'] ) ? esc_url( $item['mobile_thumb'] ) : '';
				$output .= '<a ' . $target . ' href="' . esc_url( $item['link'] ) . '" class="envira-gallery-' . sanitize_html_class( $data['id'] ) . ' envira-gallery-link " ' . $html5_attribute . '="enviragallery' . sanitize_html_class( $data['id'] ) . '" title="' . strip_tags( esc_attr( $item['title'] ) ) . '" data-envira-caption="' . $caption . '" data-envira-retina="' . ( isset( $item['lightbox_retina_image'] ) ? $item['lightbox_retina_image'] : '' ) . '" data-thumbnail="' . esc_url( $item['thumb'] ) . '" data-mobile-thumbnail="' . $this->is_mobile_thumb . '"' . ( ( isset($item['link_new_window']) && $item['link_new_window'] == 1 ) ? ' target="_blank"' : '' ) . ' ' . apply_filters( 'envira_gallery_output_link_attr', '', $id, $item, $data, $i ) . ' itemprop="contentUrl">';
			}

				$output	 = apply_filters( 'envira_gallery_output_before_image', $output, $id, $item, $data, $i );
				$gallery_theme = $this->get_config( 'columns', $data ) == 0 ? ' envira-' . $this->get_config( 'justified_gallery_theme', $data ) : '';
				// Build the image and allow filtering
				$output_item = '<img id="envira-gallery-image-' . sanitize_html_class( $id ) . '" class="envira-gallery-image envira-lazy envira-gallery-image-' . $i . $gallery_theme . '" data-envira-index="' . $i . '" src="' . esc_url( $imagesrc ) . '"' . ( $this->get_config( 'dimensions', $data ) ? ' width="' . $this->get_config( 'crop_width', $data ) . '" height="' . $this->get_config( 'crop_height', $data ) . '"' : '' ) . ' data-envira-src="' . esc_url($imagesrc ) . '" data-envira-gallery-id="' . $data['id'] . '" data-envira-item-id="' . $id . '" data-envira-caption="' . $caption . '" alt="' . esc_attr( $item['alt'] ) . '" title="' . strip_tags( esc_attr( $item['title'] ) ) . '" ' . apply_filters( 'envira_gallery_output_image_attr', '', $id, $item, $data, $i ) . ' itemprop="thumbnailUrl" data-envira-srcset="' . esc_url( $imagesrc ) . ' 1x,' . esc_url( $image_src_retina ) . ' 2x" srcset="' . esc_url( $imagesrc ) . ' 1x,' . esc_url( $placeholder[0] ) . '" />';
				$output_item = apply_filters( 'envira_gallery_output_image', $output_item, $id, $item, $data, $i );

				// Add image to output
				$output .= $output_item;
				$output	 = apply_filters( 'envira_gallery_output_after_image', $output, $id, $item, $data, $i );

			if ( $create_link ) {
				$output .= '</a>';
			}

			$output	 = apply_filters( 'envira_gallery_output_after_link', $output, $id, $item, $data, $i );
			$output .= '</div>';

		$output .= '</div>';
		$output	 = apply_filters( 'envira_gallery_output_single_item', $output, $id, $item, $data, $i );

		// Append the image to the gallery output
		$gallery .= $output;

		// Filter the output before returning
		$gallery  = apply_filters( 'envira_gallery_output_after_item', $gallery, $id, $item, $data, $i );

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
	 * Maybe sort the gallery images, if specified in the config
	 *
	 * Note: To ensure backward compat with the previous 'random' config
	 * key, the sorting parameter is still stored in the 'random' config
	 * key.
	 *
	 * @since 1.3.8
	 *
	 * @param	array	$data		Gallery Config
	 * @param	int		$gallery_id Gallery ID
	 * @return	array				Gallery Config
	 */
	public function maybe_sort_gallery( $data, $gallery_id ) {

		if ( ! empty( $this->gallery_sort[ $gallery_id ] ) && ! empty( $data['gallery'] ) ) {
			// sort using the gallery_sort order
			$data['gallery'] = array_replace( array_flip( $this->gallery_sort[ $gallery_id ] ), $data['gallery'] );
			return $data;
		}

		//Return if gallery is empty
		if ( empty( $data['gallery'] ) ){

			return $data;

		}

		// Get sorting method
		$sorting_method		= (string) $this->get_config( 'random', $data );
		$sorting_direction	= $this->get_config( 'sorting_direction', $data );

		// Sort images based on method
		switch ( $sorting_method ) {

			/**
			* Random
			* - Again, by design, to ensure backward compat when upgrading from 1.3.7.x or older
			* where we had a 'random' key = 0 or 1. Sorting was introduced in 1.3.8
			*/
			case '1':
				// Shuffle keys
				$keys = array_keys( $data['gallery'] );
				shuffle( $keys );

				// Rebuild array in new order
				$new = array();
				foreach( $keys as $key ) {
					$new[ $key ] = $data['gallery'][ $key ];
				}

				// Assign back to gallery
				$data['gallery'] = $new;

				break;

			/**
			* Image Meta
			*/
			case 'src':
			case 'title':
			case 'caption':
			case 'alt':
			case 'link':
				// Get metadata
				$keys = array();
				foreach ( $data['gallery'] as $id => $item ) {
					$keys[ $id ] = strip_tags( $item[ $sorting_method ] );
				}

				// Sort titles / captions
				if ( $sorting_direction == 'ASC' ) {
					asort( $keys );
				} else {
					arsort( $keys );
				}

				// Iterate through sorted items, rebuilding gallery
				$new = array();
				foreach( $keys as $key => $title ) {
					$new[ $key ] = $data['gallery'][ $key ];
				}

				// Assign back to gallery
				$data['gallery'] = $new;
				break;

			/**
			* Published Date
			*/
			case 'date':
				// Get published date for each
				$keys = array();
				foreach ( $data['gallery'] as $id => $item ) {
					// If the attachment isn't in the Media Library, we can't get a post date - assume now
					if ( ! is_numeric( $id ) || ( false === ( $attachment = get_post( $id ) ) ) ) {
						$keys[ $id ] = date( 'Y-m-d H:i:s' );
					} else {
						$keys[ $id ] = $attachment->post_date;
					}
				}

				// Sort titles / captions
				if ( $sorting_direction == 'ASC' ) {
					asort( $keys );
				} else {
					arsort( $keys );
				}

				// Iterate through sorted items, rebuilding gallery
				$new = array();
				foreach( $keys as $key => $title ) {
					$new[ $key ] = $data['gallery'][ $key ];
				}

				// Assign back to gallery
				$data['gallery'] = $new;
				break;

			/**
			* None
			* - Do nothing
			*/
			case '0':
			case '':
				break;

			/**
			* If developers have added their own sort options, let them run them here
			*/
			default:
				$data = apply_filters( 'envira_gallery_sort_gallery', $data, $sorting_method, $gallery_id );
				break;

		}

		// Set the sort order
		if ( ! empty( $data['gallery'] ) ) {
			foreach ( $data['gallery'] as $id => $d ) {
				$this->gallery_sort[ $gallery_id ][] = $id;
			}
		}

		return $data;

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

		$gallery .= '<div class="envira-gallery-description envira-gallery-description-above" style="padding-bottom: ' . $this->get_config( 'margin', $data ) . 'px;">';
			$gallery  = apply_filters( 'envira_gallery_output_before_description', $gallery, $data );

			// Get description.
			$description = $data['config']['description'];

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

			// Requires WordPress 4.4+
			if ( function_exists( 'wp_make_content_images_responsive' ) ) {
				$description = wp_make_content_images_responsive( $description );
			}

			// Append the description to the gallery output.
			$gallery .= $description;

			// Filter the gallery HTML.
			$gallery  = apply_filters( 'envira_gallery_output_after_description', $gallery, $data );
		$gallery .= '</div>';

		return $gallery;
	}

   /**
     * Outputs the gallery init script in the footer.
     *
     * @since 1.0.0
     */
    public function gallery_init() {

        // envira_galleries stores all Fancybox instances
        // envira_isotopes stores all Isotope instances
        // envira_isotopes_config stores Isotope configs for each Gallery
        $envira_gallery_sort = json_encode( $this->gallery_sort );

        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $envira_gallery_sort = json_encode( $this->gallery_sort, JSON_FORCE_OBJECT );
        }

        ?>

        <script type="text/javascript">
            <?php ob_start(); ?>

            var envira_galleries = [],
                envira_gallery_images = {},
                envira_isotopes = [],
                envira_isotopes_config = [],
                envira_gallery_sort = <?php echo $envira_gallery_sort; ?>,
                envira_gallery_options = {};

            jQuery(document).ready(function($){

                <?php
                do_action( 'envira_gallery_api_start_global' );
                foreach ( $this->data as $data ) {
                    // Prevent multiple init scripts for the same gallery ID.
                    if ( in_array( $data['id'], $this->done ) ) {
                        continue;
                    }
                    $this->done[] = (string) $data['id'];

			        // Detect if user is on a mobile device - if so, override $mobile flag which may be manually set
			        // by out of date addons or plugins
			        if ( $this->get_config( 'mobile', $data ) ) {

			            $mobile = envira_mobile_detect()->isMobile();

			        }

                    do_action( 'envira_gallery_api_start', $data );

                    // Define container
                    ?>
                    var envira_container_<?php echo $data['id']; ?> = '';

                    <?php if ( $this->get_config( 'columns', $data ) == 0 ) : ?>


                    <?php

                        // if the user has selected a custom theme, only output the needed JS
                        $gallery_theme = $this->get_config( 'justified_gallery_theme', $data );

                        // in some cases, previous gallery using the old automattic layout aren't showing a row height, so just in case...
                        if ( !$mobile ) {
                            $justified_row_height = $this->get_config( 'justified_row_height', $data ) ? $this->get_config( 'justified_row_height', $data ) : 150;
                        } else {
                            $justified_row_height = $this->get_config( 'mobile_justified_row_height', $data ) ? $this->get_config( 'mobile_justified_row_height', $data ) : 80;
                        }

                    ?>

                        $('#envira-gallery-<?php echo $data["id"]; ?>').enviraJustifiedGallery({
                            rowHeight : <?php echo $justified_row_height; ?>,
                            maxRowHeight: -1,
                            selector: '> div > div',
                            lastRow: '<?php echo $this->get_config( 'justified_last_row', $data ) ? $this->get_config( 'justified_last_row', $data ) : 'nojustify'; ?>',
                            border: 0,
                            margins: <?php echo null !== $this->get_config( 'justified_margins', $data )  ? $this->get_config( 'justified_margins', $data ) : '1'; ?>,

							<?php do_action( 'envira_gallery_api_start_justified' ); ?>

                        });

                        <?php if ( $gallery_theme == 'js-desaturate' || $gallery_theme == 'js-threshold' || $gallery_theme == 'js-blur' || $gallery_theme == 'js-vintage' ) : ?>

                        $('#envira-gallery-<?php echo $data["id"]; ?>').on('jg.complete', function (e) {
                            if( navigator.userAgent.match(/msie/i) || $.browser.msie || navigator.appVersion.indexOf('Trident/') > 0 ) {
                                $('#envira-gallery-<?php echo $data["id"]; ?> img').each(function() {
                                    var keep_id = $(this).attr('id');
                                    $(this).attr('id', keep_id + '-effects' );
                                    $(this).wrap('<div class="effect-wrapper" style="display:inline-block;width:' + this.width + 'px;height:' + this.height + 'px;">').clone().addClass('gotcolors').css({'position': 'absolute', 'opacity' : 0, 'z-index' : 1 }).attr('id', keep_id).insertBefore(this);
                                    <?php

                                        switch ($gallery_theme) {
                                            case 'js-desaturate':
                                                echo 'this.src = jg_effect_desaturate($(this).attr("src"));';
                                                break;
                                            case 'js-threshold':
                                                echo 'this.src = jg_effect_threshold(this.src);';
                                                break;
                                            case 'js-blur':
                                                echo 'this.src = jg_effect_blur(this.src);';
                                                break;
                                            case 'js-vintage':
                                                echo 'jg_effect_vintage( this );';
                                                break;
                                        }

                                    ?>
                                });
                                $('#envira-gallery-<?php echo $data["id"]; ?> img').hover(
                                    function() {
                                        $(this).stop().animate({opacity: 1}, 200);
                                    },
                                    function() {
                                        $(this).stop().animate({opacity: 0}, 200);
                                    }
                                );
                            }
                            else {
                                /*$('#envira-gallery-<?php echo $data["id"]; ?> img').each(function() {
                                    $(this).addClass('envira-<?php echo $gallery_theme; ?>');
                                });*/

                                $('#envira-gallery-<?php echo $data["id"]; ?> img').hover(
                                    function() {
                                        $(this).removeClass('envira-<?php echo $gallery_theme; ?>');
                                    },
                                    function() {
                                        $(this).addClass('envira-<?php echo $gallery_theme; ?>');
                                    }
                                );
                            }


                        });

                        <?php endif; ?>

                        $('#envira-gallery-<?php echo $data["id"]; ?>').css('opacity', '1');

                    <?php endif; ?>

                    <?php
                    // Isotope: Start
                    if ( $this->get_config( 'columns', $data ) > 0 && $this->get_config( 'isotope', $data ) ) {
                        // Define config for this Isotope Gallery
                        ?>
                        envira_isotopes_config['<?php echo $data['id']; ?>'] = {
                            <?php do_action( 'envira_gallery_api_enviratope_config', $data ); ?>
                            itemSelector: '.envira-gallery-item',
                            <?php
                            // If columns = 0, use fitRows
                            // if ( $this->get_config( 'columns', $data ) > 0 ) {
                                ?>
                                masonry: {
                                    columnWidth: '.envira-gallery-item'
                                }
                                <?php /*
                            } else {
                                ?>
                                layoutMode: 'fitRows'
                                <?php
                             } */
                            ?>
                        };
                        <?php
                        // Initialize Isotope
                        ?>
                        envira_isotopes['<?php echo $data['id']; ?>'] = envira_container_<?php echo $data['id']; ?>
                                                                    = $('#envira-gallery-<?php echo $data['id']; ?>').enviratope(envira_isotopes_config['<?php echo $data['id']; ?>']);
                        <?php
                        // Re-layout Isotope when each image loads
                        ?>
                        envira_isotopes['<?php echo $data['id']; ?>'].enviraImagesLoaded()
                            .done(function() {
                                envira_isotopes['<?php echo $data['id']; ?>'].enviratope('layout');
                            })
                            .progress(function() {
                                envira_isotopes['<?php echo $data['id']; ?>'].enviratope('layout');
                            });
                        <?php
                        do_action( 'envira_gallery_api_enviratope', $data );
                    }
                    // Isotope: End

                    // CSS Animations: Start
                    if ( $this->get_config( 'css_animations', $data ) ) {
                        $opacity = $this->get_config( 'css_opacity', $data );

                        // Defaults Addon Gallery may not have been saved since opacity introduction, so force a value if one doesn't exist.
                        if ( empty( $opacity ) ) {
                            $opacity = 100;
                        }

                        // Reduce to factor of 1
                        $opacity = ( $opacity / 100 );
                        ?>
                        envira_container_<?php echo $data['id']; ?> = $('#envira-gallery-<?php echo $data['id']; ?>').enviraImagesLoaded( function() {
                            $('.envira-gallery-item img').fadeTo( 'slow', <?php echo $opacity; ?> );
                        });
                        <?php
                    }
                    // CSS Animations: End

                    // Fancybox: Start
                    if ( $this->get_config( 'lightbox_enabled', $data ) ) {
                        // By default, we'll use the images in the Gallery DOM to populate the lightbox.
                        // However, Addons (e.g. Pagination) may require us to give access to all images
                        // in a Gallery, not just the paginated subset on screen.
                        // Those Addons can populate this array now which will tell envirabox which images to use.
                        $lightbox_images = apply_filters( 'envira_gallery_lightbox_images', false, $data );

                        if ( is_array( $lightbox_images ) && ! empty( $this->gallery_sort[ $data['id'] ] ) ) {
                            $lightbox_images = array_replace( array_flip( $this->gallery_sort[ $data['id'] ] ), $lightbox_images );
                        }

                        $theme = $this->get_config( 'lightbox_theme', $data );

                        ?>
                        envira_gallery_options['<?php echo $data['id']; ?>'] = {
                            lightboxTheme: '<?php echo empty( $theme ) ? "base" : $theme; ?>',
                            <?php do_action( 'envira_gallery_api_config', $data ); // Depreciated ?>
                            <?php do_action( 'envira_gallery_api_envirabox_config', $data ); ?>
                            <?php if ( ! $this->get_config( 'keyboard', $data ) ) : ?>
                            keys: 0,
                            <?php endif; ?>
                            margin: <?php echo apply_filters( 'envirabox_margin', 30, $data ); ?>,
                            padding: <?php echo apply_filters( 'envirabox_padding', 15, $data ); ?>,
                            <?php if ( $this->get_config( 'supersize', $data ) ): ?>
                            autoCenter: true,
                            <?php endif; ?>
                            arrows: <?php echo apply_filters( 'envirabox_arrows', $this->get_config( 'arrows', $data ), $data ); ?>,
                            aspectRatio: <?php echo $this->get_config( 'aspect', $data ); ?>,
                            loop: <?php echo $this->get_config( 'loop', $data ); ?>,
                            mouseWheel: <?php echo $this->get_config( 'mousewheel', $data ); ?>,
                            preload: 1,

                            <?php
                            /* Get open and transition effects */
                            $lightbox_open_close_effect = $this->get_config( 'lightbox_open_close_effect', $data );
                            $lightbox_transition_effect = $this->get_config( 'effect', $data );

                            /* Get standard effects */
                            $lightbox_standard_effects =  $this->common->get_transition_effects_values();

                            /* If open/close is standard, use openEffect, closeEffect */
                            if ( in_array( $lightbox_open_close_effect, $lightbox_standard_effects ) ) {
                                ?>
                                openEffect: '<?php echo $lightbox_open_close_effect; ?>',
                                closeEffect: '<?php echo $lightbox_open_close_effect; ?>',
                                <?php
                            } else {

                                // easing effects have been depreciated, and will default back to 'swing'

                                ?>
                                openEffect: 'elastic', /* FancyBox default is fade, should be elastic for the openEffect/closeEffect functions */
                                closeEffect: 'elastic',
                                openEasing: 'swing', /* <?php echo ( $lightbox_open_close_effect == "Swing" ? "swing" : "easeIn" . $lightbox_open_close_effect ); ?>', */
                                closeEasing: 'swing', /* <?php echo ( $lightbox_open_close_effect == "Swing" ? "swing" : "easeOut" . $lightbox_open_close_effect ); ?>', */
                                openSpeed: 500,
                                closeSpeed: 500,
                                <?php
                            }

                            /* If transition effect is standard, use nextEffect, prevEffect */
                            if ( in_array( $lightbox_transition_effect, $lightbox_standard_effects ) ) {
                                ?>
                                nextEffect: '<?php echo $lightbox_transition_effect; ?>',
                                prevEffect: '<?php echo $lightbox_transition_effect; ?>',
                                <?php
                            } else {

                                // easing effects have been depreciated, and will default back to 'swing'

                                ?>
                                nextEasing: 'swing', /* '<?php echo ( $lightbox_transition_effect == "Swing" ? "swing" : "easeIn" . $lightbox_transition_effect ); ?>', */
                                prevEasing: 'swing', /* '<?php echo ( $lightbox_transition_effect == "Swing" ? "swing" : "easeOut" . $lightbox_transition_effect ); ?>', */
                                nextSpeed: 600,
                                prevSpeed: 600,
                                <?php
                            }
                            ?>
                            tpl: {
                                wrap     : '<?php echo $this->get_lightbox_template( $data ); ?>',
                                image    : '<img class="envirabox-image" src="{href}" alt="" data-envira-title="" data-envira-caption="" data-envira-index="" data-envira-data="" />',
                                iframe   : '<iframe id="envirabox-frame{rnd}" name="envirabox-frame{rnd}" class="envirabox-iframe" frameborder="0" vspace="0" hspace="0" allowtransparency="true" wekitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
                                error    : '<p class="envirabox-error"><?php echo __( 'The requested content cannot be loaded.<br/>Please try again later.</p>', 'envira-gallery' ); ?>',
                                closeBtn : '<a title="<?php echo __( 'Close', 'envira-gallery' ); ?>" class="envirabox-item envirabox-close" href="#"></a>',
                                next     : '<a title="<?php echo __( 'Next', 'envira-gallery' ); ?>" class="envirabox-nav envirabox-next envirabox-arrows-<?php echo $this->get_config( 'arrows_position', $data ); ?> envirabox-nav-<?php echo $this->get_config( 'lightbox_theme', $data ); ?> <?php echo isset( $data['config']['supersize'] ) && $data['config']['supersize'] == 1 ? 'supersize' : ''; ?>" href="#"><span></span></a>',
                                prev     : '<a title="<?php echo __( 'Previous', 'envira-gallery' ); ?>" class="envirabox-nav envirabox-prev envirabox-arrows-<?php echo $this->get_config( 'arrows_position', $data ); ?> envirabox-nav-<?php echo $this->get_config( 'lightbox_theme', $data ); ?> <?php echo isset( $data['config']['supersize'] ) && $data['config']['supersize'] == 1 ? 'supersize' : ''; ?>" href="#"><span></span></a>'
                                <?php do_action( 'envira_gallery_api_templates', $data ); ?>
                            },
                            helpers: {
                                <?php
                                do_action( 'envira_gallery_api_helper_config', $data );
                                // Grab title display
                                $title_display = $this->get_config( 'title_display', $data );
                                if ( $title_display == 'float_wrap' ) {
                                    $title_display = 'float';
                                }
                                ?>
                                title: {
                                    <?php do_action( 'envira_gallery_api_title_config', $data ); ?>
                                    type: '<?php echo apply_filters( 'envira_gallery_title_type', $title_display, $data ); ?>',
                                    alwaysShow: '<?php echo apply_filters( 'envira_always_show_title', false, $data ); ?>',
                                },
                                <?php if ( $this->get_config( 'thumbnails', $data ) ) : ?>
                                <?php
                                $mobile_thumbnails_width = $this->get_config( 'mobile_thumbnails_width', $data ) ? $this->get_config( 'mobile_thumbnails_width', $data ) : 75;
                                $mobile_thumbnails_height = $this->get_config( 'mobile_thumbnails_height', $data ) ? $this->get_config( 'mobile_thumbnails_height', $data ) : 50;
                                ?>
                                thumbs: {
                                    width: <?php echo apply_filters( 'envira_gallery_lightbox_thumbnail_width', $this->get_config( 'thumbnails_width', $data ), $data ); ?>,
                                    height: <?php echo apply_filters( 'envira_gallery_lightbox_thumbnail_height', $this->get_config( 'thumbnails_height', $data ), $data ); ?>,
                                    mobile_thumbs: <?php echo apply_filters( 'envira_gallery_mobile_lightbox_thumbnails', $this->get_config( 'mobile_thumbnails', $data ), $data ); ?>,
                                    mobile_width: <?php echo apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_width', $mobile_thumbnails_width, $data ); ?>,
                                    mobile_height: <?php echo apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_height', $mobile_thumbnails_height, $data ); ?>,
                                    source: function(current) {
                                        if ( typeof current.element == 'undefined' ) {
                                            return current.thumbnail;
                                        } else {
                                            return $(current.element).data('thumbnail');
                                        }
                                    },
                                    mobileSource: function(current) {
                                        if ( typeof current.element == 'undefined' ) {
                                            return current.mobile_thumbnail;
                                        } else {
                                            return $(current.element).data('mobile-thumbnail');
                                        }
                                    },
                                    dynamicMargin: <?php echo apply_filters( 'envirabox_dynamic_margin', 'false', $data ); ?>,
                                    dynamicMarginAmount: <?php echo apply_filters( 'envirabox_dynamic_margin_amount', 0, $data ); ?>,
                                    position: '<?php echo apply_filters( 'envirabox_gallery_thumbs_position', $this->get_config( 'thumbnails_position', $data ), $data ); ?>',
                                },
                                <?php endif; ?>
                                <?php if ( $this->get_config( 'toolbar', $data ) ) : ?>
                                buttons: {
                                    tpl: '<?php echo $this->get_toolbar_template( $data ); ?>',
                                    position: '<?php echo $this->get_config( 'toolbar_position', $data ); ?>',
                                    padding: '<?php echo ( ( $this->get_config( 'toolbar_position', $data ) == 'bottom' && $this->get_config( 'thumbnails', $data ) && $this->get_config( 'thumbnails_position', $data ) == 'bottom' ) ? true : false ); ?>'
                                },
                                <?php else: ?>
                                slideshow: {
                                    skipSingle: true
                                },
                                <?php endif; ?>
                                navDivsRoot: <?php echo apply_filters( 'envirabox_nav_divs_root', 'false', $data ); ?>,
                                actionDivRoot: <?php echo apply_filters( 'envirabox_action_divs_root', 'false', $data ); ?>,
                            },
                            <?php do_action( 'envira_gallery_api_config_callback', $data ); ?>
                            beforeLoad: function(){

                                <?php

                                if ( ! $lightbox_images ) {

                                ?>
                                this.title = $(this.element).attr('data-envira-caption');
                                <?php

                                } else {

                                ?>
                                this.title = this.group[ this.index ].caption;
                                <?php

                                }

                                ?>

                                <?php do_action( 'envira_gallery_api_before_load', $data ); ?>
                            },
                            afterLoad: function(){
                                $('.envirabox-overlay-fixed').on({
                                    'touchmove' : function(e){
                                        e.preventDefault();
                                    }
                                });

                                <?php do_action( 'envira_gallery_api_after_load', $data ); ?>

                                <?php if ( $this->get_config( 'supersize', $data ) ): ?>
                                /*$.extend(this, {
                                    width       : '100%',
                                    height      : '100%'
                                });*/
                                <?php endif; ?>
                            },
                            beforeShow: function(){
                                $(window).on({
                                    'resize.envirabox' : function(){
                                        $.envirabox.update();
                                    }
                                });

                                <?php
                                // Set data attributes on the lightbox image, based on either
                                // the image in the DOM or (if $lightbox_images defined) the image
                                // from $lightbox_images

                                // Another issue: index will show wrong image if there is a random sort

                                ?>
                                if ( typeof this.element === 'undefined' ) {
                                    <?php
                                    // Using $lightbox_images
                                    ?>
                                    var gallery_id = this.group[ this.index ].gallery_id;
                                    var gallery_item_id = this.group[ this.index ].id;
                                    var alt = this.group[ this.index ].alt;
                                    var title = this.group[ this.index ].title;
                                    var caption = this.group[ this.index ].caption;
                                    var index = this.index;

                                } else {
                                    <?php
                                    // Using image from DOM
                                    // Get a bunch of data attributes from clicked image link
                                    ?>
                                    var gallery_id = this.element.find('img').data('envira-gallery-id');
                                    var gallery_item_id = this.element.find('img').data('envira-item-id');
                                    var alt = this.element.find('img').attr('alt');
                                    var title = this.element.find('img').parent().attr('title');
                                    var caption = this.element.find('img').parent().data('envira-caption');
                                    var retina_image = this.element.find('img').parent().data('envira-retina');
                                    var index = this.element.find('img').data('envira-index');
                                    var src = this.element.find('img').attr('src');

                                }

                                <?php
                                // Set alt, data-envira-title, data-envira-caption and data-envira-index attributes on Lightbox image
                                ?>
                                this.inner.find('img').attr('alt', alt)
                                                      .attr('data-envira-gallery-id', gallery_id)
                                                      .attr('data-envira-item-id', gallery_item_id)
                                                      .attr('data-envira-title', title)
                                                      .attr('data-envira-caption', caption)
                                                      .attr('data-envira-index', index);

                                <?php
                                // Problem w/ above: there might not BE an image if we are working with embedded videos, so
                                // proposing appending this information to envirabox-overlay
                                ?>

                                $('.envirabox-wrap').attr('alt', alt)
                                                    .attr('data-envira-gallery-id', gallery_id)
                                                    .attr('data-envira-item-id', gallery_item_id)
                                                    .attr('data-envira-title', title)
                                                    .attr('data-envira-caption', caption)
                                                    .attr('data-envira-index', index)
                                                    .attr('data-envira-src', src);

                                <?php
                                // Set retina image srcset if specified
                                ?>
                                if ( typeof retina_image !== 'undefined' && retina_image !== '' ) {
                                    this.inner.find('img').attr('srcset', retina_image + ' 2x');
                                }

                                <?php
                                // Custom lightbox themes
                                // -- Insert theme slug into overlay <div> css

                                ?>

                                $('.envirabox-overlay').addClass( 'overlay-<?php echo $this->get_config( 'lightbox_theme', $data ); ?>' );

                                <?php
                                // Using Video Addon?
                                // -- Insert slug into overlay <div> css

                                ?>

                                $('.envirabox-overlay').addClass( 'overlay-video' );


                                <?php if ( $this->get_config( 'thumbnails', $data ) ) : ?>
                                $('.envirabox-overlay').addClass('envirabox-thumbs');
                                <?php endif; ?>

                                <?php if ( $this->get_config( 'dynamic', $data ) ) : ?>
                                $('.envirabox-wrap').addClass('envira-dynamic-gallery');
                                <?php endif; ?>

                                <?php do_action( 'envira_gallery_api_before_show', $data ); ?>

                                var overlay_supersize = <?php echo $this->get_config( 'supersize', $data ) ? 'true' : 'false'; ?>;
                                if(overlay_supersize) {
                                    $('.envirabox-overlay').addClass( 'overlay-supersize' );
                                    $('#envirabox-thumbs').addClass( 'thumbs-supersize' );
                                }
                                $('.envira-close').click(function(event) {
                                    event.preventDefault();
                                    $.envirabox.close();
                                });
                            },
                            afterShow: function(){
                                <?php
                                if ( $this->get_config( 'mobile_touchwipe', $data ) ) {
                                    ?>

                                    if ( $('#envirabox-thumbs ul li').length > 0 ) {

                                        $('#envirabox-thumbs').swipe( {
                                            excludedElements:".noSwipe",
                                            swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                                                if (direction === 'left' && fingerCount <= 1 ) {
                                                    $.envirabox.next( direction );
                                                } else if (direction === 'left' && fingerCount > 1 ) {
                                                    $.envirabox.jumpto( 0 );
                                                } else if (direction === 'right' && fingerCount <= 1 ) {
                                                    $.envirabox.prev( direction );
                                                } else if (direction === 'right' && fingerCount > 1 ) {
                                                    $.envirabox.jumpto( sizeof( $('#envirabox-thumbs ul li').length ) );
                                                }
                                            }
                                        } );

                                    }

                                    $('.envirabox-wrap, .envirabox-wrap a.envirabox-nav').swipe( {
                                        excludedElements:"label, button, input, select, textarea, .noSwipe",
                                        swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
                                            if (direction === 'left') {
                                                $.envirabox.next(direction);
                                            } else if (direction === 'right') {
                                                $.envirabox.prev(direction);
                                            } else if (direction === 'up') {
                                                <?php
                                                if ( $this->get_config( 'mobile_touchwipe_close', $data ) ) {
                                                    ?>
                                                    $.envirabox.close();
                                                    <?php
                                                }
                                                ?>
                                            }
                                        }
                                    } );
                                    <?php
                                }

                                ?>


                                <?php

                                // If title helper = float_wrap, add a CSS class so we can disable word-wrap
                                if ( $this->get_config( 'title_display', $data ) == 'float_wrap' ) {
                                    ?>
                                    if ( typeof this.helpers.title !== 'undefined' ) {
                                        if ( ! $( 'div.envirabox-title' ).hasClass( 'envirabox-title-text-wrap' ) ) {
                                            $( 'div.envirabox-title' ).addClass( 'envirabox-title-text-wrap' );
                                        }
                                    }
                                    <?php
                                }

                                do_action( 'envira_gallery_api_after_show', $data ); ?>

                                var overlay_supersize = <?php echo $this->get_config( 'supersize', $data ) ? 'true' : 'false'; ?>;
                                if(overlay_supersize) {
                                    $('#envirabox-thumbs').addClass( 'thumbs-supersize' );
                                }
                            },
                            beforeClose: function(){
                                <?php do_action( 'envira_gallery_api_before_close', $data ); ?>
                            },
                            afterClose: function(){
                                $(window).off('resize.envirabox');
                                <?php do_action( 'envira_gallery_api_after_close', $data ); ?>
                            },
                            onUpdate: function(){
                                <?php
                                if ( $this->get_config( 'toolbar', $data ) ) : ?>
                                var envira_buttons_<?php echo $data['id']; ?> = $('#envirabox-buttons li').map(function(){
                                    return $(this).width();
                                }).get(),
                                    envira_buttons_total_<?php echo $data['id']; ?> = 0;
                                $.each(envira_buttons_<?php echo $data['id']; ?>, function(i, val){
                                    envira_buttons_total_<?php echo $data['id']; ?> += parseInt(val, 10);
                                });

                                envira_buttons_total_<?php echo $data['id']; ?> += 1;

                                $('#envirabox-buttons ul').width(envira_buttons_total_<?php echo $data['id']; ?>);

                                <?php

                                // Position based on lightbox theme

                                $lightbox_theme = $this->get_config( 'lightbox_theme', $data );
                                if ( $lightbox_theme == 'modern-dark' || $lightbox_theme == 'modern-light' ) {

                                ?>

                                $('#envirabox-buttons').width(envira_buttons_total_<?php echo $data['id']; ?>).css('left', ($(window).width() - envira_buttons_total_<?php echo $data['id']; ?>) - 100);

                                <?php } else { ?>

                                $('#envirabox-buttons').width(envira_buttons_total_<?php echo $data['id']; ?>).css('left', ($(window).width() - envira_buttons_total_<?php echo $data['id']; ?>)/2);

                                <?php } ?>

                                <?php endif; ?>

                                <?php do_action( 'envira_gallery_api_on_update', $data ); ?>
                            },
                            onCancel: function(){
                                <?php do_action( 'envira_gallery_api_on_cancel', $data ); ?>
                            },
                            onPlayStart: function(){
                                <?php do_action( 'envira_gallery_api_on_play_start', $data ); ?>
                            },
                            onPlayEnd: function(){
                                <?php do_action( 'envira_gallery_api_on_play_end', $data ); ?>
                            }
                        };
                        <?php

                        if ( ! $lightbox_images ) {
                            // No lightbox images specified, so use images from DOM
                            ?>
                            envira_galleries['<?php echo $data['id']; ?>'] = $('.envira-gallery-<?php echo $data['id']; ?>').envirabox( envira_gallery_options['<?php echo $data['id']; ?>'] );
                            <?php

                        } else {
                            // Use images from $lightbox_images
                            add_filter( 'envira_minify_strip_double_forward_slashes', '__return_false' );
                            ?>
                            envira_gallery_images['<?php echo $data['id']; ?>'] = [];
                            <?php
                            // Build a JS array of all images
                            $count = 0;
                            foreach ( $lightbox_images as $image_id => $image ) {
                                // If no image ID exists, skip
                                if ( empty( $image_id ) ) {
                                    continue;
                                }
                                ?>
                                envira_gallery_images['<?php echo $data['id']; ?>'].push({
                                    href: '<?php echo $image['src']; ?>',
                                    gallery_id: '<?php echo $data['id']; ?>',
                                    id: <?php echo $image_id; ?>,
                                    alt: '<?php echo addslashes( str_replace( "\n", '<br />', $image['alt'] ) ); ?>',
                                    caption: '<?php echo addslashes( str_replace( "\n", '<br />', $image['caption'] ) ); ?>',
                                    title: '<?php echo addslashes( str_replace( "\n", '<br />', $image['title'] ) ); ?>',
                                    index: <?php echo $count; ?>,
                                    thumbnail: '<?php echo ( isset( $image['thumb'] ) ? $image['thumb'] : '' ); ?>',
                                    mobile_thumbnail: '<?php echo ( isset( $image['mobile_thumb'] ) ? $image['mobile_thumb'] : '' ); ?>'
                                    <?php do_action( 'envira_gallery_api_lightbox_image_attributes', $image, $image_id, $lightbox_images, $data ); ?>
                                });
                                <?php
                                $count++;
                            }

                            // Open envirabox when an image is clicked, telling envirabox which images are available to it.
                            ?>
                            envira_galleries['<?php echo $data['id']; ?>'] = $('.envira-gallery-<?php echo $data['id']; ?>').envirabox( envira_gallery_options['<?php echo $data['id']; ?>'], envira_gallery_images['<?php echo $data['id']; ?>'] );
                            $('#envira-gallery-wrap-<?php echo $data['id']; ?>').on('click', 'a.envira-gallery-link', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                $.envirabox.close();
                                if ( $('#envira-gallery-wrap-<?php echo $data['id']; ?> div.envira-pagination').length > 0 ) { /* this exists, perhaps pagination doesn't display nav because it's only one page? */
                                    var envirabox_page = ( $('#envira-gallery-wrap-<?php echo $data['id']; ?> div.envira-pagination').data('page') - 1 );
                                } else {
                                    var envirabox_page = 0;
                                }

                                if ( $('#envira-gallery-wrap-<?php echo $data['id']; ?> div.envira-pagination').length > 0 ) { /* this exists, perhaps pagination doesn't display nav because it's only one page? */
                                    var envirabox_per_page = $('#envira-gallery-wrap-<?php echo $data['id']; ?> div.envira-pagination').data('per-page');
                                } else {
                                    var envirabox_per_page = $('.envira-gallery-image').length;
                                }

                                var envirabox_index = ( Number($('img', $(this)).data('envira-index')) - 1 );
                                envira_gallery_options['<?php echo $data['id']; ?>'].index = ((envirabox_page * envirabox_per_page) + envirabox_index);
                                envira_galleries['<?php echo $data['id']; ?>'] = $.envirabox( envira_gallery_images['<?php echo $data['id']; ?>'], envira_gallery_options['<?php echo $data['id']; ?>'] );
                            });
                            <?php
                        }

                        do_action( 'envira_gallery_api_lightbox', $data );
                        // Fancybox: End
                    }

                    do_action( 'envira_gallery_api_end', $data );
                } // foreach

                do_action( 'envira_gallery_api_end_global', $this->data );

                ?>
            });

            <?php
            // Minify before outputting to improve page load time.
            if ( defined( 'ENVIRA_DEBUG' ) && ENVIRA_DEBUG  ){

	        	echo ob_get_clean();

            } else {

            	echo $this->minify( ob_get_clean() );

            } ?>

        </script>
        <?php

    }

	/**
	 * Loads a custom gallery display theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $theme The custom theme slug to load.
	 */
	public function load_gallery_theme( $theme ) {

		// Loop through the available themes and enqueue the one called.
		foreach ( $this->common->get_gallery_themes() as $array => $data ) {
			if ( $theme !== $data['value'] ) {
				continue;
			}

			if ( file_exists( plugin_dir_path( $data['file'] ) . 'themes/' . $theme . '/style.css' ) ) {
				wp_enqueue_style( $this->base->plugin_slug . $theme . '-theme', plugins_url( 'themes/' . $theme . '/style.css', $data['file'] ), array( $this->base->plugin_slug . '-style' ) );
			}
			else {
				wp_enqueue_style( $this->base->plugin_slug . $theme . '-theme', plugins_url( 'themes/' . $theme . '/css/style.css', $data['file'] ), array( $this->base->plugin_slug . '-style' ) );
			}
			break;
		}

	}

	/**
	 * Set the title display per theme
	 *
	 * @since 1.0.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envira_gallery_title_type( $title_display, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$title_display = 'fixed';
				break;

		}

		return $title_display;

	}

	/**
	 * Set the title display per theme
	 *
	 * @since 1.0.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envira_always_show_title( $show, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$show = true;
				break;

		}

		return $show;

	}

	/**
	 * Set the margin
	 *
	 * @since 1.0.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envirabox_margin( $margin, $data ) {


		if ( $this->get_config( 'supersize', $data ) ) {
			$margin = 0;
		}

		if ( $this->get_config( 'arrows_position', $data ) == 'outside' ) {
			$margin = $margin + 50;
		}



		return $margin;

	}

	/**
	 * Set the margin
	 *
	 * @since 1.0.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envirabox_padding( $padding, $data ) {

		if ( $this->get_config( 'supersize', $data ) ) {
			$padding = 0;
		}

		return $padding;

	}

	/**
	 * Set the arrows
	 *
	 * @since 1.0.0
	 *
	 * @param string|int this is either a string or an integer and can be set accordingly.
	 */
	public function envirabox_arrows( $arrows, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$arrows = 'true';
				break;

		}

		return $arrows;

	}

	public function envirabox_gallery_thumbs_position( $position, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$position = 'bottom';
				break;

		}

		return $position;
	}

	/**
	 * Enable the dynamic margin based on the theme
	 *
	 * @since 1.0.0
	 *
	 * @param string $margin This must be a string, not a boolean since its going directly into JS.
	 */
	public function envirabox_dynamic_margin( $margin, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$margin = 'true';
				break;

		}

		return $margin;

	}

	/**
	 * Set the Dynamic Margin amount per theme
	 *
	 * @since 1.0.0
	 *
	 * @param int $amount the dynamic margin amount.
	 */
	public function envirabox_dynamic_margin_amount( $amount, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		switch ( $lightbox_theme ) {

			case 'base_dark':
				$amount = 0;
				break;

		}

		return $amount;

	}

	public function envirabox_inner_above( $template, $data ) {

		// Get gallery theme
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );
		$class = false;
		if ( ( isset( $data['config']['supersize'] ) && $data['config']['supersize'] == 1 ) ) {
			$class = 'supersize';
		}

		return $template .= '<div class="envirabox-actions ' . $lightbox_theme . ' ' . $class . '">' . apply_filters( 'envirabox_actions', '', $data ) . '</div>';
	}

	public function envirabox_actions( $template, $data ) {

		// Check if Supersize is enabled
		if ( ! in_array( $this->get_config( 'lightbox_theme', $data ), array( 'base_dark' ) ) ) {
			return $template;
		}

		// Build Button
		$button = '<div class="envira-close-button"><a title="' . __( 'Close', 'envira-gallery' ) . '" class="envirabox-item envira-close" href="#"></a></div>';

		// Return
		return $template . $button;
	}

    /**
     * adds the supersize class to the template.
     *
     * @since 1.1.2
     *
     * @param string $css_classes the classes near envirabox-wrap.
     * @param array $data Data for the Envira gallery.
     * @return string added envira-supersize to the classes.
     */
    public function envira_supersize_wrap_css_class( $css_classes, $data ) {

        if ( empty( $data['config']['supersize'] ) ) {
            return $css_classes;
        }

        return $css_classes . ' envira-supersize';

    }

	/**
	 * Loads a custom gallery lightbox theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $theme The custom theme slug to load.
	 */
	public function load_lightbox_theme( $theme ) {

		// Loop through the available themes and enqueue the one called.
		foreach ( $this->common->get_lightbox_themes() as $array => $data ) {
			if ( $theme !== $data['value'] ) {
				continue;
			}

			if ( file_exists( plugin_dir_path( $data['file'] ) . 'themes/' . $theme . '/style.css' ) ) {
				wp_enqueue_style( $this->base->plugin_slug . $theme . '-theme', plugins_url( 'themes/' . $theme . '/style.css', $data['file'] ), array( $this->base->plugin_slug . '-style' ) );
			}
			else {
				wp_enqueue_style( $this->base->plugin_slug . $theme . '-theme', plugins_url( 'themes/' . $theme . '/css/style.css', $data['file'] ), array( $this->base->plugin_slug . '-style' ) );
			}
			break;
		}

	}

	/**
	 * Helper method for adding custom gallery classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The gallery data to use for retrieval.
	 * @return string	  String of space separated gallery classes.
	 */
	public function get_gallery_classes( $data ) {

		// Set default class.
		$classes   = array();
		$classes[] = 'envira-gallery-wrap';

		// Add custom class based on data provided.
		$classes[] = 'envira-gallery-theme-' . $this->get_config( 'gallery_theme', $data );
		$classes[] = 'envira-lightbox-theme-' . $this->get_config( 'lightbox_theme', $data );

		// If we have custom classes defined for this gallery, output them now.
		foreach ( (array) $this->get_config( 'classes', $data ) as $class ) {
			$classes[] = $class;
		}

		// If the gallery has RTL support, add a class for it.
		if ( $this->get_config( 'rtl', $data ) ) {
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
	 * @param int $i	  The current position in the gallery.
	 * @param array $data The gallery data to use for retrieval.
	 * @return string	  String of space separated gallery item classes.
	 */
	public function get_gallery_item_classes( $item, $i, $data ) {

		// Set default class.
		$classes   = array();
		$classes[] = 'envira-gallery-item';
		$classes[] = 'enviratope-item';
		$classes[] = 'envira-gallery-item-' . $i;

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
	 * @param int $id	   The image attachment ID to use.
	 * @param array $item  Gallery item data.
	 * @param array $data  The gallery data to use for retrieval.
	 * @return array	   Image array
	 */
	public function maybe_change_link( $id, $item, $data ) {

		// Check gallery config
		$image_size = $this->get_config( 'lightbox_image_size', $data );

		// Return if we are serving a full size image
		if ( $image_size == 'default' || $image_size == 'full_width' ) {
			return $item;
		}

		// Check the link is a well formed URL
		// If it isn't, it'll be a video, which we don't need to do anything with
		if ( ! filter_var( $item['link'], FILTER_VALIDATE_URL ) ) {
			return $item;
		}

		// Return if the link isn't an image
		// This ensures images with links set to webpages remain that way
		if ( ! @getimagesize( $item['link'] ) ) {
			return $item;
		}

		// Get media library attachment at requested size
		$image = wp_get_attachment_image_src( $id, $image_size );
		if ( ! is_array( $image ) ) {
			return $item;
		}

		// Inject new image size into $item
		$item['link'] = $image[0];

		// Return
		return $item;

	}

	/**
	 * Helper method to retrieve the proper image src attribute based on gallery settings.
	 *
	 * @since 1.0.0
	 *
	 * @param int		$id			The image attachment ID to use.
	 * @param array		$item		Gallery item data.
	 * @param array		$data		The gallery data to use for retrieval.
	 * @param bool		$this->is_mobile		Whether or not to retrieve the mobile image.
	 * @param bool		$retina		Whether to return a retina sized image.
	 * @return string				The proper image src attribute for the image.
	 */
	public function get_image_src( $id, $item, $data, $mobile = false, $retina = false ) {

		// Define variable
		$src = false;

		// If this image is an instagram, we grab the src and don't use the $id
		// otherwise using the $id refers to a postID in the database and has been known
		// at times to pull up the wrong thumbnail instead of the instagram one

		$instagram = false;

		if ( !empty($item['src']) && strpos( $item['src'], 'cdninstagram' ) !== false ) :
			// using 'cdninstagram' because it seems all urls contain it - but be watchful in the future
			$instagram	= true;
			$src		= $item['src'];
			$image		= $item['src'];
		endif;

		$image_size = $this->get_config( 'image_size', $data );

		if ( !$src ) :

			// Check if this Gallery uses a WordPress defined image size
			if ( $image_size != 'default' && ! $retina ) {
				// If image size is envira_gallery_random, get a random image size.
				if ( $image_size == 'envira_gallery_random' ) {

					// Get random image sizes that have been chosen for this Gallery.
					$image_sizes_random = (array) $this->get_config( 'image_sizes_random', $data );

					if ( count( $image_sizes_random ) == 0 ) {
						// The user didn't choose any image sizes - use them all.
						$wordpress_image_sizes = $this->common->get_image_sizes( true );
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

	            $row_height = $retina ? intval( $this->get_config( 'justified_row_height', $data ) ) * 2 : intval( $this->get_config( 'justified_row_height', $data ) );

	            if ( $row_height <= 300 ){

               	 // Get the full image
			   	$src = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'medium' ), $id, $item, $data, $this->is_mobile );

                } elseif ( $row_height <= 640 && ! $retina ){

	            	$src = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'large' ), $id, $item, $data, $this->is_mobile );

                }else{

		           $src = apply_filters( 'envira_gallery_retina_image_src', wp_get_attachment_image_src( $id, 'full' ), $id, $item, $data, $this->is_mobile );

                }

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
		if ( $this->get_config( 'columns', $data ) == 0 || ( $image_size != 'default' && ! $retina ) ) {
		// if ( ( $image_size != 'default' && ! $retina ) ) {
			// Return the image
			return apply_filters( 'envira_gallery_image_src', $image, $id, $item, $data );
		}

		// If the image size is default (i.e. the user has input their own custom dimensions in the Gallery),
		// we may need to resize the image now
		// This is safe to call every time, as resize_image() will check if the image already exists, preventing thumbnails
		// from being generated every single time.
		$type = $this->is_mobile ? 'mobile' : 'crop'; // 'crop' is misleading here - it's the key that stores the thumbnail width + height
		$args = array(
			'position' => 'c',
			'width'	   => $this->get_config( $type . '_width', $data ),
			'height'   => $this->get_config( $type . '_height', $data ),
			'quality'  => 100,
			'retina'   => $retina,
		);

		// If we're requesting a retina image, and the gallery uses a registered WordPress image size,
		// we need use the width and height of that registered WordPress image size - not the gallery's
		// image width and height, which are hidden settings.
		if ( $image_size != 'default' && $retina ) {
			// Find WordPress registered image size
			$wordpress_image_sizes = $this->common->get_image_sizes( true ); // true = WordPress only image sizes (excludes random)

			foreach ( $wordpress_image_sizes as $size ) {
				if ( $size['value'] !== $image_size ) {
					continue;
				}

				// We found the image size. Use its dimensions
				$args['width'] = $size['width'];
				$args['height'] = $size['height'];
				break;

			}
		}

		// Filter
		$args	= apply_filters( 'envira_gallery_crop_image_args', $args);

		$resized_image = $this->common->resize_image( $image, $args['width'], $args['height'], $this->get_config( 'crop', $data ), $args['position'], $args['quality'], $args['retina'], $data );

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
			return apply_filters( 'envira_gallery_image_src', $image, $id, $item, $data );
		} else {
			return apply_filters( 'envira_gallery_image_src', $resized_image, $id, $item, $data );
		}

	}

	/**
	 * Helper method to retrieve the proper gallery toolbar template.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Array of gallery data.
	 * @return string	  String template for the gallery toolbar.
	 */
	public function get_toolbar_template( $data ) {

		global $post;

		$title = false;
		$lightbox_theme = $this->get_config( 'lightbox_theme', $data );

		// Build out the custom template based on options chosen.
		$template  = '<div id="envirabox-buttons"';

		if ( $lightbox_theme ) {
			$template .= ' class="envirabox-buttons-'.$lightbox_theme.'" ';
		}

		$template .= '>';

			$template .= '<ul>';
				$template  = apply_filters( 'envira_gallery_toolbar_start', $template, $data );

				// Prev
				$template .= '<li><a class="btnPrev" title="' . __( 'Previous', 'envira-gallery' ) . '" href="javascript:;"></a></li>';
				$template  = apply_filters( 'envira_gallery_toolbar_after_prev', $template, $data );

				// Next
				$template .= '<li><a class="btnNext" title="' . __( 'Next', 'envira-gallery' ) . '" href="javascript:;"></a></li>';
				$template  = apply_filters( 'envira_gallery_toolbar_after_next', $template, $data );

				// Title
				if ( $this->get_config( 'toolbar_title', $data ) ) {
                    // to get the title, don't grab title from $post first
                    // because you'll be grabbing the title of the page
                    // the gallery is embedded on.

                    if ( $this->get_config( 'title', $data ) ) {
                        $title = $this->get_config( 'title', $data );
                    } else if ( isset($post->post_title) ) {
                        // there should ALWAYS be a title, but just in case revert to grabbing from $post
                        $title = $post->post_title;
                    }

                    // add a filter in case title needs to be manipulated for the toolbar
                    $title = apply_filters( 'envira_gallery_toolbar_title', $title, $data );

                    $template .= '<li id="envirabox-buttons-title"><span>' . htmlentities( $title, ENT_QUOTES ) . '</span></li>';
                    $template  = apply_filters( 'envira_gallery_toolbar_after_title', $template, $data );
                }


				// Close
				$template .= '<li><a class="btnClose" title="' . __( 'Close', 'envira-gallery' ) . '" href="javascript:;"></a></li>';
				$template  = apply_filters( 'envira_gallery_toolbar_after_close', $template, $data );

				$template  = apply_filters( 'envira_gallery_toolbar_end', $template, $data );
			$template .= '</ul>';
		$template .= '</div>';

		// Return the template, filters applied and all.
		return apply_filters( 'envira_gallery_toolbar', $template, $data );

	}

	/**
	* Helper method to retrieve the gallery lightbox template
	*
	* @since 1.3.1.4
	*
	* @param array $data Array of gallery data
	* @return string String template for the gallery lightbox
	*/
	public function get_lightbox_template( $data ) {

		// Build out the lightbox template
		$envirabox_wrap_css_classes = apply_filters( 'envirabox_wrap_css_classes', 'envirabox-wrap', $data );

		$envirabox_theme = apply_filters( 'envirabox_theme', 'envirabox-theme-' . $this->get_config( 'lightbox_theme', $data ), $data );

		$template = '<div class="' . $envirabox_wrap_css_classes . '" tabIndex="-1"><div class="envirabox-skin ' . $envirabox_theme . '"><div class="envirabox-outer"><div class="envirabox-inner">';

		// Lightbox Inner above
		$template = apply_filters( 'envirabox_inner_above', $template, $data );

		// Top Left box
		$template .= '<div class="envirabox-position-overlay envira-gallery-top-left">';
		$template  = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'top-left' );
		$template .= '</div>';

		// Top Right box
		$template .= '<div class="envirabox-position-overlay envira-gallery-top-right">';
		$template  = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'top-right' );
		$template .= '</div>';

		// Bottom Left box
		$template .= '<div class="envirabox-position-overlay envira-gallery-bottom-left">';
		$template  = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'bottom-left' );
		$template .= '</div>';

		// Bottom Right box
		$template .= '<div class="envirabox-position-overlay envira-gallery-bottom-right">';
		$template  = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'bottom-right' );
		$template .= '</div>';

		// Lightbox Inner below
		$template = apply_filters( 'envirabox_inner_below', $template, $data );

		$template .= '</div></div></div></div>';

		// Return the template, filters applied
		return apply_filters( 'envira_gallery_lightbox_template', str_replace( "\n", '', $template ), $data );

	}

	/**
	 * Helper method for retrieving config values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The config key to retrieve.
	 * @param array $data The gallery data to use for retrieval.
	 * @return string	  Key value on success, default if not set.
	 */
	public function get_config( $key, $data ) {

		// If we are on a mobile device, some config keys have mobile equivalents, which we need to check instead
		if ( $this->is_mobile ) {
			$this->is_mobile_keys = array(
				'lightbox_enabled'	 => 'mobile_lightbox',
				'arrows'			 => 'mobile_arrows',
				'toolbar'			 => 'mobile_toolbar',
				'thumbnails'		 => 'mobile_thumbnails',
				'thumbnails_width'	 => 'mobile_thumbnails_width',
				'thumbnails_height'  => 'mobile_thumbnails_height',
			);

			$this->is_mobile_keys = apply_filters( 'envira_gallery_get_config_mobile_keys', $this->is_mobile_keys );

			// When on mobile, use used to blindly look at the mobile_social option to determine social sharing button output
			// However, what we need to do is look to see if the settings are active on the addon first
			if ( array_key_exists( 'social', $this->is_mobile_keys ) || array_key_exists( 'social_lightbox', $this->is_mobile_keys ) ) {

				if ( empty( $data['config']['social'] ) ) {
					unset( $this->is_mobile_keys['social'] );
				}
				if ( empty( $data['config']['social_lightbox'] ) ) {
					unset( $this->is_mobile_keys['social_lightbox'] );
				}
			}

			if ( array_key_exists( $key, $this->is_mobile_keys ) ) {
				// Use the mobile array key to get the config value
				$key = $this->is_mobile_keys[ $key ];
			}

		}

		// We need supersize for the base dark theme, so we are forcing it here
		if ( $key == 'supersize' && $data['config']['lightbox_theme'] == 'base_dark' ) {
			$data['config'][ $key ] = 1;
		}

		// The toolbar is not needed for base dark so lets disable it
		if ( $key == 'toolbar' && $data['config']['lightbox_theme'] == 'base_dark' ) {
			$data['config'][ $key ] = 0;
		}

        if ( isset( $data['config'] ) ) {
		    $data['config'] = apply_filters( 'envira_gallery_get_config', $data['config'], $key );
        } else {
            $data['config'][$key] = false;
        }

		return isset( $data['config'][$key] ) ? $data['config'][$key] : $this->common->get_config_default( $key );

	}

	/**
	 * Helper method to minify a string of data.
	 *
	 * @since 1.0.4
	 *
	 * @param string $string  String of data to minify.
	 * @return string $string Minified string of data.
	 */
	public function minify( $string, $stripDoubleForwardslashes = true ) {

		// Added a switch for stripping double forwardslashes
		// This can be disabled when using URLs in JS, to ensure http:// doesn't get removed
		// All other comment removal and minification will take place
		$stripDoubleForwardslashes = apply_filters( 'envira_minify_strip_double_forward_slashes', $stripDoubleForwardslashes );

		if ( $stripDoubleForwardslashes ) {
			$clean = preg_replace( '/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $string );
		} else {
			// Use less aggressive method
			$clean = preg_replace( '!/\*.*?\*/!s', '', $string );
			$clean = preg_replace( '/\n\s*\n/', "\n", $clean );
		}

		$clean = str_replace( array( "\r\n", "\r", "\t", "\n", '  ', '	  ', '	   ' ), '', $clean );

		return apply_filters( 'envira_gallery_minified_string', $clean, $string );

	}

	/**
	 * I'm sure some plugins mean well, but they go a bit too far trying to reduce
	 * conflicts without thinking of the consequences.
	 *
	 * 1. Prevents Foobox from completely borking envirabox as if Foobox rules the world.
	 *
	 * @since 1.0.0
	 */
	public function plugin_humility() {

		if ( class_exists( 'fooboxV2' ) ) {
			remove_action( 'wp_footer', array( $GLOBALS['foobox'], 'disable_other_lightboxes' ), 200 );
		}

	}

	/**
	 * Outputs only the first image of the gallery inside a regular <div> tag
	 * to avoid styling issues with feeds.
	 *
	 * @since 1.0.5
	 *
	 * @param array $data	   Array of gallery data.
	 * @return string $gallery Custom gallery output for feeds.
	 */
	public function do_feed_output( $data ) {

		$gallery = '<div class="envira-gallery-feed-output">';
			foreach ( $data['gallery'] as $id => $item ) {
				// Skip over images that are pending (ignore if in Preview mode).
				if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
					continue;
				}

				$imagesrc = $this->get_image_src( $id, $item, $data );
				$gallery .= '<img class="envira-gallery-feed-image" src="' . esc_url( $imagesrc ) . '" title="' . trim( esc_html( $item['title'] ) ) . '" alt="' .trim( esc_html( $item['alt'] ) ) . '" />';
				break;
			 }
		$gallery .= '</div>';

		return apply_filters( 'envira_gallery_feed_output', $gallery, $data );

	}

	/**
	 * Returns a set of indexable image links to allow SEO indexing for preloaded images.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $id		  The slider ID to target.
	 * @return string $images String of indexable image HTML.
	 */
	public function get_indexable_images( $id ) {

		// If there are no images, don't do anything.
		$images = '';
		$i		= 1;
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
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Envira_Gallery_Shortcode object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Shortcode ) ) {
			self::$instance = new Envira_Gallery_Shortcode();
		}

		return self::$instance;

	}

}

// Load the shortcode class.
$envira_gallery_shortcode = Envira_Gallery_Shortcode::get_instance();
