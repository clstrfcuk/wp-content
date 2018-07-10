<?php
/**
 * Frontend Container
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author	Envira Gallery Team
 */
 	
namespace Envira\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Frontend\Posttypes;
use Envira\Frontend\Standalone;
use Envira\Frontend\Shortcode;
use Envira\Frontend\Background;

use Envira\Widgets\Widget;
use Envira\Utils\Capabilities;

class Frontend_Container{
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		
		$posttypes 	= new Posttypes;
		$standalone = new Standalone;
		$shortcode 	= new Shortcode;
		$background = new Background;
		
		/* Yoast SEO */
		add_filter( 'wpseo_sitemap_urlimages', array( $this, 'filter_wpseo_sitemap_urlimages' ), 10, 2 );

		add_filter( 'widget_text', 'do_shortcode' );

		add_filter( 'envira_gallery_shortcode_start', array( $this, 'filter_shortcode_start' ), 10, 1 );

		// Load the plugin widget.
		add_action( 'widgets_init', array( $this, 'widget' ) );

	}
	
	/**
	 * Registers the Envira Gallery widget.
	 *
	 * @since 1.7.0
	 */
	public function widget() {

		register_widget( 'Envira\Widgets\Widget' );

	}
		
	/**
	 * Prevents Envira shortcodes from being rendered due to third-party conflicts
	 *
	 * @since 1.8.0
	 *
	 * @param array 		$att shortcode attributes
	 *
	 * @return array		Updated Array w/ action and reason
	 */
	function filter_shortcode_start( $atts ) {

		if ( !is_admin() ) {
			return;
		}

		// Prevent Yoast And Divi From Previewing, rendering JSON breaking / causing JS errors
		if ( isset( $_REQUEST['action'] ) ) {
			switch ( $_REQUEST['action'] ) {
				case 'wpseo_filter_shortcodes':
					// Yoast
					return ( array( 'action' => 'bail', 'reason' => $_REQUEST['action'] ) );
					break;
				case 'et_pb_execute_content_shortcodes':
					// Divi
					return ( array( 'action' => 'bail', 'reason' => $_REQUEST['action'] ) );
					break;
				case 'edit':
					// Divi + Yoast
					if ( defined('WPSEO_VERSION') && function_exists('et_divi_load_scripts_styles') ) {
						return ( array( 'action' => 'bail', 'reason' => $_REQUEST['action'] ) );
					}
					break;
			}
		}

	}


	/**
	 * Inserts images into Yoast SEO Sitexml.
	 *
	 * @since 1.7.0
	 *
	 * @param array 		$yoast_images Current incoming array of images.
	 * @param interger 		$post_id WP ID
	 *
	 * @return array		Updated Yoast Array.
	 */
	public function filter_wpseo_sitemap_urlimages( $yoast_images, $post_id ) {

		 // make filter magic happen here... if the post_id is an envira gallery or album, great. if not, go back.

		if ( ! get_post_type( $post_id ) == 'envira' && ! get_post_type( $post_id ) == 'envira_album' ) {
			return $yoast_images;
		}

		// If defaults addon is activated, make sure we returning a number of images for a dynamic or default gallery/album
		if ( class_exists('Envira_Defaults') && ( intval( $post_id ) === intval( get_option( 'envira_default_gallery' ) ) || intval( $post_id ) === intval( get_option( 'envira_default_album' ) ) ) ) {
			return $yoast_images;
		}

		// If defaults addon is activated, make sure we returning a number of images for a dynamic or default gallery/album
		if ( class_exists('Envira_Dynamic') && ( intval( $post_id ) === intval( get_option( 'envira_dynamic_gallery' ) ) || intval( $post_id ) === intval( get_option( 'envira_dynamic_album' ) ) ) ) {
			return $yoast_images;
		}

		if ( get_post_type( $post_id ) == 'envira' ) { // if this is a gallery get all the images and add them to the array

			$gallery = envira_get_gallery( $post_id );
			if ( $gallery && ! empty( $gallery['gallery'] ) ) {
				foreach ( $gallery['gallery'] as $image ) {
					if ( !empty ( $image['src'] ) ) {
						$yoast_images[] = array('src' => $image['src']);
					}
				}
			}

		} else { // if this is an album get all the gallerys, then images,  and add them to the array

			if ( !class_exists( 'Envira_Albums') ) {
				return $yoast_images;
			}

			$instance_albums = \ Envira_Albums::get_instance();
			$album = $instance_albums->_get_album( $post_id );

			// go through all the galleries, limit to 50 for now to ensure most sites don't timeout

			$counter = 0;
			if ( ! empty( $album['galleries'] ) ) {
				foreach ( $album['galleries'] as $album_gallery ) {
					if ( $counter <= 50 && !empty ( $album_gallery['id'] ) ) {

						$gallery = envira_get_gallery( $album_gallery['id'] );
						if ( $gallery && ! empty( $gallery['gallery'] ) ) {
							foreach ( $gallery['gallery'] as $image ) {
								if ( !empty ( $image['src'] ) ) {
									$yoast_images[] = array('src' => $image['src']);
								}
							}
							$counter++;
						}
					}
				}
			}

		}

		 return $yoast_images;
	}
		
}