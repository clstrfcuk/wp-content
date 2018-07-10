<?php
/**
 * Envira Theme Functions.
 *
 * @since 1.8.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Helper method for retrieving lightbox themes.
 *
 * @since 1.8.0
 *
 * @return array Array of lightbox theme data.
 */
function envira_get_lightbox_themes() {

	$themes = array(
		array(
			'value' => 'base_dark',
			'name'  => __( 'Base (Dark)', 'envira-gallery' ),
			'file'  => ENVIRA_FILE,
			 'config' => array(
				'arrows'          => 'true',
				'margins'         => array( 220, 0 ), //top/bottom, left/right
				'gutter'          => '100',
				'thumbs_position' => 'bottom',
				'base_template'   => 'envirabox_default_template',
			 ),
		),
//      array(
//          'value' => 'infinity_light',
//          'name'  => __( 'Infinity (Light)', 'envira-gallery' ),
//          'file'  => ENVIRA_FILE,
//           'config' => array(
//              'arrows'          => 'true',
//              'margins'         => array( 60, 0 ),  //top/bottom, left/right
//              'gutter'          => '50',
//              'thumbs_position' => 'bottom',
//              'base_template'   => 'envirabox_default_template',
//           ),
//      )
	);

	$themes = apply_filters( 'envira_gallery_lightbox_themes', $themes );

	$themes[] = array(
		'value' => 'base',
		'name'  => __( 'Legacy', 'envira-gallery' ),
		'file'  => ENVIRA_FILE,
		'config' => array(
				'arrows'          => 'true',
				'margins'         => array( 220, 0 ),  //top/bottom, left/right
				'gutter'          => '50',
				'base_template'   => 'envirabox_legecy_template',
		),
	);

	return $themes;

}

/**
 * Helper method for retrieving gallery themes.
 *
 * @since 1.8.0
 *
 * @return array Array of gallery theme data.
 */
function envira_get_gallery_themes(){

	$themes = array(
		 array(
			 'value'  => 'base',
			 'name'   => __( 'Base', 'envira-gallery' ),
			 'file'   => ENVIRA_FILE
		 )
	);

	return apply_filters( 'envira_gallery_gallery_themes', $themes );

}

/**
* Helper method to retrieve the gallery lightbox template
*
* @since 1.8.0
*
* @param array $data Array of gallery data
* @return string String template for the gallery lightbox
*/
function envirabox_default_template( $data ){

	// Build out the lightbox template
	$envirabox_wrap_css_classes = apply_filters( 'envirabox_wrap_css_classes', 'envirabox-wrap', $data );

	$envirabox_theme = apply_filters( 'envirabox_theme', 'envirabox-theme-' . envira_get_config( 'lightbox_theme', $data ), $data );

	$template = '<div id="envirabox-'.$data['id'].'" data-envirabox-id="'.$data['id'].'" class="envirabox-container '. $envirabox_theme . ' ' . $envirabox_wrap_css_classes . '" role="dialog" tabindex="-1">';

		$template .= '<div class="envirabox-bg"></div>';
		$template .= '<div class="envirabox-outer"><div class="envirabox-inner">';

			$template = apply_filters( 'envirabox_inner_above', $template, $data );

				$template .= '<div class="envirabox-caption-wrap">';
				$template .= '<div class="envirabox-caption' . ( envira_get_config( 'image_counter', $data ) ? ' with-counter' : false ) . '"></div>';

				if ( envira_get_config( 'image_counter', $data ) ) {
					$template .= ' <div class="envirabox-image-counter">' . __( 'Image', 'envira-gallery' ) . ' <span data-envirabox-index></span> ' . __( 'of', 'envira-gallery' ) . ' <span data-envirabox-count></span></div>';
				}

				$template .= '</div>';
				$template .= '<div class="envirabox-toolbar">';

					$template = apply_filters( 'envirabox_actions', $template, $data );

					if( envira_get_config( 'thumbnails', $data ) && envira_get_config( 'thumbnails_toggle', $data ) ){

						$template .= '<div class="envira-thumbs-button"><a data-envirabox-thumbs class="envirabox-item envira-thumbs-button envirabox-button--thumbs" title="' . __('Toggle Thumbnails', 'envira-gallery' ).'" href="#"></a></div>';

					}

					$template .= '<div class="envira-close-button"><a data-envirabox-close class="envirabox-item envirabox-close envirabox-button--close" title="' . __('Close', 'envira-gallery' ).'" href="#"></a></div>';

				$template .= '</div>';

				$template .= '<div class="envirabox-navigation">';
					$template .= '<a data-envirabox-prev title="'. __( 'Prev', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--left envirabox-nav envirabox-prev" href="#"><span></span></a>';
					$template .= '<a data-envirabox-next title="'. __( 'Next', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--right envirabox-nav envirabox-next" href="#"><span></span></a>';
				$template .= '</div>';

			$template .= '<div class="envirabox-stage"></div>';

			$template = apply_filters( 'envirabox_inner_below', $template, $data );

			$template .='</div></div></div>';

	return str_replace( "\n", '', $template );

}

/**
 * envirabox_infinity_templatee function.
 *
 * @since 1.8.0
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function envirabox_infinity_template( $data ){

	// Build out the lightbox template
	$envirabox_wrap_css_classes = apply_filters( 'envirabox_wrap_css_classes', 'envirabox-wrap', $data );

	$envirabox_theme = apply_filters( 'envirabox_theme', 'envirabox-theme-' . envira_get_config( 'lightbox_theme', $data ), $data );

	$template = '<div id="envirabox-'.$data['id'].'" data-envirabox-id="'.$data['id'].'" class="envirabox-container '. $envirabox_theme . ' ' . $envirabox_wrap_css_classes . '" role="dialog" tabindex="-1">';

		$template .= '<div class="envirabox-bg"></div>';
		$template .= '<div class="envirabox-outer"><div class="envirabox-inner">';

			$template = apply_filters( 'envirabox_inner_above', $template, $data );

				$template .= '<div class="envirabox-toolbar">';

					$template = apply_filters( 'envirabox_actions', $template, $data );

					$template .= '<div class="envira-thumbs-button"><a data-envirabox-thumbs class="envirabox-item envira-thumbs-button envirabox-button--thumbs" title="' . __('Toggle Thumbnails', 'envira-gallery' ).'" href="#"></a></div>';
					$template .= '<div class="envira-close-button"><a data-envirabox-close class="envirabox-item envirabox-close envirabox-button--close" title="' . __('Close', 'envira-gallery' ).'" href="#"></a></div>';

				$template .= '</div>';

				$template .= '<div class="envirabox-navigation">';
					$template .= '<a data-envirabox-prev title="'. __( 'Prev', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--left envirabox-nav envirabox-prev" href="#"><span></span></a>';
					$template .= '<a data-envirabox-next title="'. __( 'Next', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--right envirabox-nav envirabox-next" href="#"><span></span></a>';
				$template .= '</div>';

			$template .= '<div class="envirabox-stage"></div>';

			$template .= '<div class="envirabox-caption-wrap"><div class="envirabox-caption"></div></div>';

			$template = apply_filters( 'envirabox_inner_below', $template, $data );

			$template .='</div></div></div>';

	return str_replace( "\n", '', $template );

}

/**
 * Loads a custom gallery lightbox theme.
 *
 * @since 1.8.0
 *
 * @param string $theme The custom theme slug to load.
 */
function envira_load_lightbox_theme( $theme ) {

	$lightbox_themes = envira_get_lightbox_themes();
	$key             = array_search( $theme , array_column( $lightbox_themes, 'value'));
	$current_theme   = $lightbox_themes[$key];

	if ( file_exists( get_stylesheet_directory() . '/envira-gallery/lightbox-themes/' . $theme . '/style.css'  ) ) {

        wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-lightbox-theme', get_stylesheet_directory_uri() . '/envira-gallery/lightbox-themes/' . $theme . '/style.css' , array( ENVIRA_SLUG . '-style' ) );

		return;

    } elseif ( file_exists( get_template_directory() . '/envira-gallery/lightbox-themes/' . $theme . '/style.css'  ) ) {

        wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-lightbox-theme', get_template_directory_uri() . '/envira-gallery/lightbox-themes/' . $theme . '/style.css', array( ENVIRA_SLUG . '-style' ) );

        return;

    } elseif ( file_exists( plugin_dir_path( $current_theme['file'] ) . 'envira-gallery/lightbox-themes/' . $theme . '/css/style.css' )  ) {

		wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-lightbox-theme', plugins_url( 'envira-gallery/lightbox-themes/' . $theme . '/css/style.css', $current_theme['file'] ), array( ENVIRA_SLUG . '-style' ) );

		return;

	} else {

		wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-lightbox-theme', plugins_url( 'envira-gallery/lightbox-themes/' . $theme . '/css/style.css', ENVIRA_FILE ), array( ENVIRA_SLUG . '-style' ) );

		return;

	}

}

/**
 * envira_load_lightbox_config function.
 *
 * @since 1.8.0
 *
 * @access public
 * @param mixed $theme
 * @return void
 */
function envira_load_lightbox_config( $gallery_id, $raw = false ){

	$data            = envira_get_gallery( $gallery_id );
	$lightbox_themes = envira_get_lightbox_themes();
	$key             = array_search( envira_get_config( 'lightbox_theme', $data ) , array_column( $lightbox_themes, 'value'));
	$legacy_themes   = envirabox_legecy_themes();
	$current_theme   = $lightbox_themes[$key];

	if ( !empty( $current_theme['config'] ) && is_array( $current_theme['config'] ) ){

		$current_theme['config']['base_template'] = function_exists( $current_theme['config']['base_template'] ) ? call_user_func( $current_theme['config']['base_template'], $data ) : envirabox_default_template( $data );

		$config = $current_theme['config'];

	} else {

		$config = envirabox_default_config( $gallery_id );

	}

	$config['load_all']         = apply_filters( 'envira_load_all_images_lightbox', false, $data );
	$config['error_template']   = envirabox_error_template( $data );

	// If supersize is enabled lets override settings
	if ( envira_get_config( 'supersize', $data ) == 1 ){

		$config['margins'] = is_admin_bar_showing() ? array( 100,0 ) : array( 100 , 0 );

	}

	$config['thumbs_position']    = in_array( $current_theme['value'], $legacy_themes ) ? envira_get_config( 'thumbnails_position', $data ) : 'lock';
	$config['arrow_position']     = in_array( $current_theme['value'], $legacy_themes ) ? envira_get_config( 'arrows_position', $data ) : false;
	$config['arrows']             = in_array( $current_theme['value'], $legacy_themes ) ? envira_get_config( 'arrows', $data ) : true;
	$config['toolbar']            = in_array( $current_theme['value'], $legacy_themes ) ? false : true;
	$config['infobar']            = in_array( $current_theme['value'], $legacy_themes ) ? true : false;
	$config['show_smallbtn']      = in_array( $current_theme['value'], $legacy_themes ) ? true : false;
	$config['inner_caption']      = in_array( $current_theme['value'], $legacy_themes ) ? true : false;
	$config['caption_position']   = in_array( $current_theme['value'], $legacy_themes ) ? envira_get_config( 'title_display', $data ) : false;
	$config['small_btn_template'] = '<a data-envirabox-close class="envirabox-item envirabox-close envirabox-button--close" title="' . __('Close', 'envira-gallery' ).'" href="#"></a>';

	return json_encode( $config );

}

/**
 * envirabox_legecy_themes function.
 *
 * @since 1.8.0
 *
 * @access public
 * @return void
 */
function envirabox_legecy_themes(){

	$legecy = array(
		'base',
		'captioned',
		'polaroid',
		'showcase',
		'sleek',
		'subtle'
	);

	return $legecy;
}

/**
 * envira_default_lightbox_config function.
 *
 * @since 1.8.0
 *
 * @access public
 * @return void
 */
function envirabox_default_config( $gallery_id ){

	$data = envira_get_gallery( $gallery_id );

	$config = array(
		'arrows'    => 'true',
		'margins'   => array( 220, 0 ), //top/bottom, left/right
		'template'  => envirabox_default_template( $data ),
		'thumbs_position' => 'bottom',
	);

	return apply_filters( 'envirabox_default_config', $config, $data, $gallery_id );

}

/**
 * envirabox_error_template function.
 *
 * @since 1.8.0
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function envirabox_error_template( $data ){

	$template = '<div class="envirabox-error"><p>{{ERROR}}<p></div>';

	return apply_filters( 'envirabox_default_error_template', $template, $data );
}

/**
 * Loads a custom gallery display theme.
 *
 * @since 1.8.0
 *
 * @param string $theme The custom theme slug to load.
 */
function envira_load_gallery_theme( $theme ) {

	$gallery_themes = envira_get_gallery_themes();
	$key            = array_search( $theme , array_column( $gallery_themes, 'value'));
	$current_theme  = $gallery_themes[$key];

	if ( file_exists( get_stylesheet_directory() . '/envira-gallery/gallery-themes/' . $theme . '/style.css'  ) ) {

        wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-gallery-theme', get_stylesheet_directory_uri() . '/envira-gallery/gallery-themes/' . $theme . '/style.css' , array( ENVIRA_SLUG . '-style' ) );

		return;

    } elseif ( file_exists( get_template_directory() . '/envira-gallery/gallery-themes/' . $theme . '/style.css'  ) ) {

        wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-gallery-theme', get_template_directory_uri() . '/envira-gallery/gallery-themes/' . $theme . '/style.css', array( ENVIRA_SLUG . '-style' ) );

        return;

    } elseif ( file_exists( plugin_dir_path( $current_theme['file'] ) . 'envira-gallery/lightbox-themes/' . $theme . '/css/style.css' )  ) {

		wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-gallery-theme', plugins_url( 'envira-gallery/gallery-themes/' . $theme . '/css/style.css', $current_theme['file'] ), array( ENVIRA_SLUG . '-style' ) );

		return;

	} else {

		$last_resort = apply_filters( 'envira_load_gallery_theme_url', plugins_url( 'envira-gallery/gallery-themes/' . $theme . '/css/style.css', ENVIRA_FILE ), $theme );

		wp_enqueue_style( ENVIRA_SLUG . '-' . $theme . '-gallery-theme', $last_resort, array( ENVIRA_SLUG . '-style' ) );

		return;

	}

}

/**
 * envira_get_layout_template function.
 *
 * @since 1.8.0
 *
 * @access public
 * @param mixed $file
 * @param array $data (default: array())
 * @return void
 */
function envira_get_layout_template( $file, $data = array() ){

	if ( empty( $data ) || !is_array( $data ) ){
		return false;
	}

	ob_start();

	if ( file_exists( get_stylesheet_directory() . '/envira-gallery/templates/' . $file . '.php' ) ) {

		include( get_stylesheet_directory() . '/envira-gallery/templates/' . $file . '.php' );

	} elseif ( file_exists( get_template_directory() . '/envira-gallery/templates/' . $file . '.php' ) ) {

		include( get_template_directory() . '/envira-gallery/templates/' . $file . '.php' );

	} else {

		include(  plugins_url( 'envira-gallery/templates/' . $file . '.php', ENVIRA_FILE ) );

	}

	$template = ob_get_clean();

	return $template;

}

/**
 * envirabox_legecy_template function.
 *
 * @since 1.8.0
 *
 * @access public
 * @param mixed $data
 * @return void
 */
function envirabox_legecy_template( $data ){

	// Build out the lightbox template
	$envirabox_wrap_css_classes = apply_filters( 'envirabox_wrap_css_classes', 'envirabox-wrap', $data );

	$envirabox_theme = apply_filters( 'envirabox_theme', 'envirabox-theme-' . envira_get_config( 'lightbox_theme', $data ), $data );

	$template = '<div id="envirabox-'.$data['id'].'" data-envirabox-id="'.$data['id'].'" class="envirabox-container '. $envirabox_theme . ' ' . $envirabox_wrap_css_classes . '" role="dialog" tabindex="-1">';

		$template .= '<div class="envirabox-bg"></div>';
		$template .= '<div class="envirabox-outer"><div class="envirabox-inner">';

			$template = apply_filters( 'envirabox_inner_above', $template, $data );

			if ( envira_get_config( 'toolbar', $data ) && envira_get_config( 'toolbar_position', $data ) === 'top' ){
				$template .= envira_get_toolbar_template( $data );
			}

			if ( envira_get_config( 'arrows', $data ) && envira_get_config( 'arrows_position', $data ) != 'inside' ){

				$template .= '<div class="envirabox-navigation">';
					$template .= '<a data-envirabox-prev title="'. __( 'Prev', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--left envirabox-nav envirabox-prev" href="#"><span></span></a>';
					$template .= '<a data-envirabox-next title="'. __( 'Next', 'envira-gallery' ) .'" class="envirabox-arrow envirabox-arrow--right envirabox-nav envirabox-next" href="#"><span></span></a>';
				$template .= '</div>';

			}

			// Top Left box      +
			$template .= '<div class="envirabox-position-overlay envira-gallery-top-left">';
			$template    = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'top-left' );
			$template .= '</div>';

			// Top Right box
			$template .= '<div class="envirabox-position-overlay envira-gallery-top-right">';
			$template    = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'top-right' );
			$template .= '</div>';

			// Bottom Left box
			$template .= '<div class="envirabox-position-overlay envira-gallery-bottom-left">';
			$template    = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'bottom-left' );
			$template .= '</div>';

			// Bottom Right box
			$template .= '<div class="envirabox-position-overlay envira-gallery-bottom-right">';
			$template    = apply_filters( 'envirabox_output_dynamic_position', $template, $data, 'bottom-right' );
			$template .= '</div>';

			$template .= '<div class="envirabox-stage"></div>';

			if ( envira_get_config( 'toolbar', $data ) && envira_get_config( 'toolbar_position', $data ) === 'bottom' ){
				$template .= envira_get_toolbar_template( $data );
			}

			$template = apply_filters( 'envirabox_inner_below', $template, $data );

			$template .='</div></div></div>';

	return str_replace( "\n", '', $template );

}
/**
 * Helper method to retrieve the proper gallery toolbar template.
 *
 * @since 1.8.0
 *
 * @param array $data Array of gallery data.
 * @return string        String template for the gallery toolbar.
 */
function envira_get_toolbar_template( $data ) {

	global $post;

	$title = false;
	$supersize = empty( $data['config']['supersize'] ) ? '' : ' envira-supersize';

	// Build out the custom template based on options chosen.
	$template  = '<div id="envirabox-buttons" class="envirabox-infobar '. envira_get_config( 'toolbar_position', $data ) .'">';

		$template .= '<ul>';

			$template  = apply_filters( 'envira_gallery_toolbar_start', $template, $data );

			// Prev
			$template .= '<li><a data-envirabox-prev class="btnPrev" title="' . __( 'Previous', 'envira-gallery' ) . '" href="#"></a></li>';
			$template  = apply_filters( 'envira_gallery_toolbar_after_prev', $template, $data );

			// Next
			$template .= '<li><a data-envirabox-prev class="btnNext" title="' . __( 'Next', 'envira-gallery' ) . '" href="#"></a></li>';
			$template  = apply_filters( 'envira_gallery_toolbar_after_next', $template, $data );

			// Title
			if ( envira_get_config( 'toolbar_title', $data ) ) {
				// to get the title, don't grab title from $post first
				// because you'll be grabbing the title of the page
				// the gallery is embedded on.

				if ( envira_get_config( 'title', $data ) ) {
					$title = envira_get_config( 'title', $data );
				} else if ( isset($post->post_title) ) {
					// there should ALWAYS be a title, but just in case revert to grabbing from $post
					$title = $post->post_title;
				}

				// add a filter in case title needs to be manipulated for the toolbar
				$title = apply_filters( 'envira_gallery_toolbar_title', $title, $data );

				$template .= '<li id="envirabox-buttons-title"><span>' . htmlentities( $title, ENT_QUOTES ) . '</span></li>';
				$template   = apply_filters( 'envira_gallery_toolbar_after_title', $template, $data );
			}


			// Close
			$template .= '<li><a data-envirabox-close class="btnClose" title="' . __( 'Close', 'envira-gallery' ) . '" href="javascript:;"></a></li>';

			$template  = apply_filters( 'envira_gallery_toolbar_after_close', $template, $data );

			$template  = apply_filters( 'envira_gallery_toolbar_end', $template, $data );
		$template .= '</ul>';
	$template .= '</div>';

	// Return the template, filters applied and all.
	return apply_filters( 'envira_gallery_toolbar', $template, $data );

}
