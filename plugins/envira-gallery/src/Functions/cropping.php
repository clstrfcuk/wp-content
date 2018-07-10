<?php
/**
 * Envira Cropping Functions.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Frontend\Background;

/**
 * Crop all images in a background procces.
 *
 * @since 1.7.0
 *
 * @access public
 * @param mixed $gallery_id
 * @return void
 */
function envira_crop_images( $gallery_id ){
	//Bail if no gallery ID
	if ( !isset( $gallery_id ) ){
		return false;
	}

	$background = new Envira\Frontend\Background;

	$crop_data = array(
		'id' => $gallery_id
	);

	$background->background_request( $crop_data, 'crop-images' );

	return true;

}

/**
 * envira_resize_image function.
 *
 * @since 1.7.0
 *
 * @access public
 * @param mixed $url
 * @param mixed $width (default: null)
 * @param mixed $height (default: null)
 * @param bool $crop (default: true)
 * @param string $align (default: 'c')
 * @param int $quality (default: 100)
 * @param bool $retina (default: false)
 * @param array $data (default: array())
 * @param bool $force_overwrite (default: false)
 * @return void
 */
function envira_resize_image( $url, $width = null, $height = null, $crop = true, $align = 'c', $quality = 100, $retina = false, $data = array(), $force_overwrite = false  ){

	// Get common vars.
	$args   = array( $url, $width, $height, $crop, $align, $quality, $retina, $data );

	// Filter args
	$args = apply_filters( 'envira_gallery_resize_image_args', $args );

	// Get image info
	$common = envira_get_image_info( $args );

	// Unpack variables if an array, otherwise return WP_Error.
	if ( is_wp_error( $common ) ) {
		return $common;
	} else {
		extract( $common );
	}

	// If the destination width/height values are the same as the original, don't do anything.
	if ( !$force_overwrite && $orig_width === $dest_width && $orig_height === $dest_height ) {
		return $url;
	}

	// If the file doesn't exist yet, we need to create it.
	if ( ! file_exists( $dest_file_name ) || ( file_exists( $dest_file_name ) && $force_overwrite ) ) {

		$common = \ Envira_Gallery_Common::get_instance();

		$resized_image = $common->resize_image( $url, $width, $height, $crop, $align, $quality, $retina, $data );

	}

	// Set the resized image URL.
	$resized_url = str_replace( basename( $url ), basename( $dest_file_name ), $url );

	return apply_filters( 'envira_gallery_resize_image_resized_url', $resized_url );

}