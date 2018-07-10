<?php
/**
 * Envira Image Functions.
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

/**
 * isImage function.
 *
 * @access public
 * @param mixed $url
 * @return void
 */
function envira_is_image( $url ) {

	$parse     = parse_url( $url );
	$filetypes = envira_get_supported_filetypes();

	//bail if its not an array
	if ( !is_array( $parse ) ){

		return false;

	}

	if ( !isset( $parse['path'] ) ) {
		return false;
	}

	$extension      = pathinfo( $parse['path'], PATHINFO_EXTENSION );

	$img_extensions = explode(",", $filetypes[0]['extensions']);


	if ( in_array( $extension, $img_extensions ) ){

		return true;

	}

	return false;
}

/**
 * get_image_sizes function.
 *
 * @access public
 * @return void
 */
function envira_get_image_sizes( $wordpress_only = false ){

	if ( ! $wordpress_only ) {
		$sizes = array(
			array(
				'value'  => 'default',
				'name'   => __( 'Default', 'envira-gallery' ),
			)
		);
	}

	global $_wp_additional_image_sizes;
	$wp_sizes = get_intermediate_image_sizes();
	foreach ( (array) $wp_sizes as $size ) {
		if ( isset( $_wp_additional_image_sizes[$size] ) ) {
			$width  = absint( $_wp_additional_image_sizes[$size]['width'] );
			$height = absint( $_wp_additional_image_sizes[$size]['height'] );
		} else {
			$width  = absint( get_option( $size . '_size_w' ) );
			$height = absint( get_option( $size . '_size_h' ) );
		}

		if ( ! $width && ! $height ) {
			$sizes[] = array(
				'value'  => $size,
				'name'   => ucwords( str_replace( array( '-', '_' ), ' ', $size ) ),
			);
		} else {
			$sizes[] = array(
				'value'  => $size,
				'name'   => ucwords( str_replace( array( '-', '_' ), ' ', $size ) ) . ' (' . $width . ' &#215; ' . $height . ')',
				'width'  => $width,
				'height' => $height,
			);
		}
	}
	// Add Option for full image
	$sizes[] = array(
		'value'  => 'full',
		'name'   => __( 'Original Image', 'envira-gallery' ),
	);

	// Add Random option
	if ( ! $wordpress_only ) {
		$sizes[] = array(
			'value'  => 'envira_gallery_random',
			'name'   => __( 'Random', 'envira-gallery' ),
		);
	}

	return apply_filters( 'envira_gallery_image_sizes', $sizes );
}

function envira_get_shortcode_image_sizes(){
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				if ( (bool) get_option( "{$_size}_crop" ) === true ){
					continue;
				}
				$sizes[ $_size ]['name']   = $_size;
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			if( $_wp_additional_image_sizes[ $_size ]['crop'] === true ){
				continue;
			}
			$sizes[ $_size ] = array(
				'name'  => $_size,
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}
		return $sizes;
}

/**
 * Helper method to return common information about an image.
 *
 * @since 1.7.0
 *
 * @param array $args // List of resizing args to expand for gathering info.
 * @return WP_Error|string Return WP_Error on error, array of data on success.
 */
function envira_get_image_info( $args ) {

	// Unpack arguments.
	list( $url, $width, $height, $crop, $align, $quality, $retina, $data ) = $args;

	// Return an error if no URL is present.
	if ( empty( $url ) ) {
		return new WP_Error( 'envira-gallery-error-no-url', __( 'No image URL specified for cropping.', 'envira-gallery' ) );
	}

	// Get the image file path.
	$urlinfo         = parse_url( $url );
	$wp_upload_dir   = wp_upload_dir();

	// Interpret the file path of the image.
	if ( preg_match( '/\/[0-9]{4}\/[0-9]{2}\/.+$/', $urlinfo['path'], $matches ) ) {

		$file_path = $wp_upload_dir['basedir'] . $matches[0];

	} else {

		$content_dir = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : '/wp-content/';
		$pathinfo    = parse_url( $url );
		$uploads_dir = is_multisite() ? '/files/' : $content_dir;
		$file_path   = trailingslashit( $wp_upload_dir['basedir'] ) . basename ( $urlinfo['path'] );
		$file_path   = preg_replace( '/(\/\/)/', '/', $file_path );

	}

	// Attempt to stream and import the image if it does not exist based on URL provided.
	if ( ! file_exists( $file_path ) ) {
		return new WP_Error( 'envira-gallery-error-no-file', __( 'No file could be found for the image URL specified.', 'envira-gallery' ) );
	}

	// Get original image size.
	$size = @getimagesize( $file_path );

	// If no size data obtained, return an error.
	if ( !is_array( $size ) ) {

		return new WP_Error( 'envira-gallery-error-no-size', __( 'The dimensions of the original image could not be retrieved for cropping.', 'envira-gallery' ) );

	}

	// Set original width and height.
	list( $orig_width, $orig_height, $orig_type ) = $size;

	// Generate width or height if not provided.
	if ( $width && ! $height ) {
		$height = floor( $orig_height * ($width / $orig_width) );
	} else if ( $height && ! $width ) {
		$width = floor( $orig_width * ($height / $orig_height) );
	} else if ( ! $width && ! $height ) {
		return new WP_Error( 'envira-gallery-error-no-size', __( 'The dimensions of the original image could not be retrieved for cropping.', 'envira-gallery' ) );
	}

	// Allow for different retina image sizes.
	$retina = $retina ? 2 : 1;

	// Destination width and height variables
	$dest_width  = $width * $retina;
	$dest_height = $height * $retina;

	// Some additional info about the image.
	$info = pathinfo( $file_path );
	$dir  = $info['dirname'];
	$ext  = $info['extension'];
	$name = wp_basename( $file_path, ".$ext" );

	// Suffix applied to filename
	$suffix = "${dest_width}x${dest_height}";

	// Set alignment information on the file.
	if ( $crop ) {
		$suffix .= ( $align ) ? "_${align}" : "_c";
	}

	// Get the destination file name
	$dest_file_name = "${dir}/${name}-${suffix}.${ext}";

	// Return the info.
	$info = array(
		'dir'            => $dir,
		'name'           => $name,
		'ext'            => $ext,
		'suffix'         => $suffix,
		'orig_width'     => $orig_width,
		'orig_height'    => $orig_height,
		'orig_type'      => $orig_type,
		'dest_width'     => $dest_width,
		'dest_height'    => $dest_height,
		'file_path'      => $file_path,
		'dest_file_name' => $dest_file_name,
	);

	return $info;

}

function envira_get_image_width( $id, $item, $data, $i, $output_src){

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

	} else if ( !empty( $item['width'] ) ) {

		$output_width = $item['width'];

	} else if ( !empty( $placeholder[1] ) ) {

		$output_width = $placeholder[1];

	} else {

		$output_width = false;

	}
	return apply_filters( 'envira_gallery_output_width', $output_width, $id, $item, $data, $i, $output_src );

}
function envira_get_item_height( $id, $item, $data, $i, $output_src ){

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

	} else if ( !empty( $item['height'] ) ) {

		$output_height = $item['height'];

	} else if ( !empty( $placeholder[2] ) ) {

		$output_height = $placeholder[2];

	} else {

		$output_height = false;

	}

	return apply_filters( 'envira_gallery_output_height', $output_height, $id, $item, $data, $i, $output_src );

}
function envira_get_output_src( $id, $item, $data, $i, $output_src ){

}