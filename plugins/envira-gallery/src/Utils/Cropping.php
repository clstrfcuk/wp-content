<?php

namespace Envira\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

use Envira\Frontend\Background;

class Cropping{

	/**
	 * API method for cropping images.
	 *
	 * @since 1.0.0
	 *
	 * @global object $wpdb The $wpdb database object.
	 *
	 * @param string $url      The URL of the image to resize.
	 * @param int $width       The width for cropping the image.
	 * @param int $height      The height for cropping the image.
	 * @param bool $crop       Whether or not to crop the image (default yes).
	 * @param string $align    The crop position alignment.
	 * @param bool $retina     Whether or not to make a retina copy of image.
	 * @param array $data      Array of gallery data (optional).
	 * @param bool $force_overwrite      Forces an overwrite even if the thumbnail already exists (useful for applying watermarks)
	 * @return WP_Error|string Return WP_Error on error, URL of resized image on success.
	 */
	public function resize_image( $url, $width = null, $height = null, $crop = true, $align = 'c', $quality = 100, $retina = false, $data = array(), $force_overwrite = false ) {

		global $wpdb;

		// Get common vars.
		$args   = array( $url, $width, $height, $crop, $align, $quality, $retina, $data );

		// Filter args
		$args = apply_filters( 'envira_gallery_resize_image_args', $args );

		// Don't resize images that don't belong to this site's URL
		// Strip ?lang=fr from blog's URL - WPML adds this on
		// and means our next statement fails
		if ( is_multisite() ) {
			$blog_id = get_current_blog_id();
			// doesn't use network_site_url because this will be incorrect for remapped domains
			if ( is_main_site( $blog_id ) ) {
				$site_url = preg_replace( '/\?.*/', '', network_site_url() );
			} else {
				$site_url = preg_replace( '/\?.*/', '', site_url() );
			}
		} else {
			$site_url = preg_replace( '/\?.*/', '', get_bloginfo( 'url' ) );
		}

		// WPML check - if there is a /fr or any domain in the url, then remove that from the $site_url
		if ( defined('ICL_LANGUAGE_CODE') ) {
			if ( strpos( $site_url, '/'.ICL_LANGUAGE_CODE ) !== false ) {
				$site_url = str_replace( '/'.ICL_LANGUAGE_CODE, '', $site_url );
			}
		}

		if ( function_exists( 'qtrans_getLanguage' ) ){

		   $lang = qtrans_getLanguage();

		   if ( !empty( $lang ) ) {
			   if ( strpos( $site_url, '/'. $lang ) !== false ) {
				   $site_url = str_replace( '/'. $lang, '', $site_url );
			   }
		   }

	   }

		if ( strpos( $url, $site_url ) === false ) {
			return $url;
		}

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

			// We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
			$get_attachment = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE guid='%s'", $url ) );

			// Load the WordPress image editor.
			$editor = wp_get_image_editor( $file_path );

			// Set the image editor quality.
			$editor->set_quality( $quality );

			// If cropping, process cropping.
			if ( $crop ) {

				$src_x = $src_y = 0;
				$src_w = $orig_width;
				$src_h = $orig_height;

				$cmp_x = $orig_width / $dest_width;
				$cmp_y = $orig_height / $dest_height;

				// Calculate x or y coordinate and width or height of source
				if ( $cmp_x > $cmp_y ) {
					$src_w = round( $orig_width / $cmp_x * $cmp_y );
					$src_x = round( ($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2 );
				} else if ( $cmp_y > $cmp_x ) {
					$src_h = round( $orig_height / $cmp_y * $cmp_x );
					$src_y = round( ($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2 );
				}

				// Positional cropping.
				if ( $align && $align != 'c' ) {
					if ( strpos( $align, 't' ) !== false || strpos( $align, 'tr' ) !== false || strpos( $align, 'tl' ) !== false ) {
						$src_y = 0;
					}

					if ( strpos( $align, 'b' ) !== false || strpos( $align, 'br' ) !== false || strpos( $align, 'bl' ) !== false ) {
						$src_y = $orig_height - $src_h;
					}

					if ( strpos( $align, 'l' ) !== false ) {
						$src_x = 0;
					}

					if ( strpos ( $align, 'r' ) !== false ) {
						$src_x = $orig_width - $src_w;
					}
				}

				// Crop the image.
				$editor->crop( $src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height );

			} else {

				// Just resize the image.
				$editor->resize( $dest_width, $dest_height );

			}

			// Save the image.
			$saved = $editor->save( $dest_file_name );

			// Print possible out of memory errors.
			if ( is_wp_error( $saved ) ) {
				@unlink( $dest_file_name );
				return $saved;
			}

			// Add the resized dimensions and alignment to original image metadata, so the images
			// can be deleted when the original image is delete from the Media Library.
			if ( $get_attachment ) {

				$metadata = wp_get_attachment_metadata( $get_attachment[0]->ID );

				if ( isset( $metadata['image_meta'] ) ) {
					$md = $saved['width'] . 'x' . $saved['height'];

					$md .= $align ? "_${align}" : "_c";

					$metadata['image_meta']['resized_images'][] = $md;
					wp_update_attachment_metadata( $get_attachment[0]->ID, $metadata );
				}
			}

			// Set the resized image URL.
			$resized_url = str_replace( basename( $url ), basename( $saved['path'] ), $url );
		} else {
			// Set the resized image URL.
			$resized_url = str_replace( basename( $url ), basename( $dest_file_name ), $url );
		}

		// Return the resized image URL.
		return apply_filters( 'envira_gallery_resize_image_resized_url', $resized_url );

	}

	/**
	 * crop function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function crop( $post_id ){

		$background = new Background();

		$settings = envira_get_gallery( $post_id );
//
//      // Generate additional image sizes based on the lightbox theme
//      if ( !empty( $settings['gallery'] ) ) {
//
//
//          foreach ( $settings['gallery'] as $image_id => $image ) {
//              $attachment_url     = wp_get_attachment_url( $image_id );
//              $thumbnail_width    = apply_filters( 'envira_gallery_lightbox_thumbnail_width', $settings['config']['thumbnails_width'], $settings['config']['lightbox_theme'] );
//              $thumbnail_height   = apply_filters( 'envira_gallery_lightbox_thumbnail_height', $settings['config']['thumbnails_height'], $settings['config']['lightbox_theme'] );
//
//              $data = array(
//
//              );
//
//              $src = envira_resize_image( $attachment_url, $thumbnail_width, $thumbnail_height, true );
//
//              // add the custom size to image meta_data
//              $attach_data = wp_get_attachment_metadata( $image_id );
//              if ( isset( $attach_data['image_meta']['resized_images'] ) ) {
//                  $resized_images = $attach_data['image_meta']['resized_images'];
//              }
//              if ( empty( $resized_images ) || !in_array( $thumbnail_width . 'x' . $thumbnail_height, $resized_images ) ) {
//                  $attach_data['image_meta']['resized_images'][] = $thumbnail_width . 'x' . $thumbnail_height;
//                  wp_update_attachment_metadata( $image_id,  $attach_data );
//              }
//
//          }
//      }
//
		// If the thumbnails option is checked, crop images accordingly.
		if ( isset( $settings['config']['thumbnails'] ) && $settings['config']['thumbnails'] ) {

			// get the proper size of thumbnails, to make sure we have the thumbnails created upon save and NOT generated on the front-end
			// this will override width and height

			$thumbnail_width    = apply_filters( 'envira_gallery_lightbox_thumbnail_width', $settings['config']['thumbnails_width'], $settings );
			$thumbnail_height   = apply_filters( 'envira_gallery_lightbox_thumbnail_height', $settings['config']['thumbnails_height'], $settings );

			$args = array(
				'align'    => envira_get_config( 'crop_position', $settings ),
				'width'    => $thumbnail_width, // envira_get_config( 'thumbnails_width', envira_get_config_default( 'thumbnails_width' ) ),
				'height'   => $thumbnail_height, // envira_get_config( 'thumbnails_height', envira_get_config_default( 'thumbnails_height' ) ),
				'quality'  => 100,
				'retina'   => true
			);
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$crop_data = array(
				'id' => $post_id,
				'args' => $args
			);
			$background->background_request( $crop_data, 'crop-images' );
		}

		// If the mobile thumbnails option is checked, crop images accordingly.
		if ( isset( $settings['config']['mobile_thumbnails'] ) && $settings['config']['mobile_thumbnails'] ) {

			// get the proper size of thumbnails, to make sure we have the thumbnails created upon save and NOT generated on the front-end
			// this will override width and height

			$mobile_thumbnail_width    = apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_width', $settings['config']['mobile_thumbnails_width'], $settings );
			$mobile_thumbnail_height   = apply_filters( 'envira_gallery_mobile_lightbox_thumbnail_height', $settings['config']['mobile_thumbnails_height'], $settings );

			$args = array(
				'align'    => envira_get_config( 'crop_position', $settings ),
				'width'    => $mobile_thumbnail_width, // envira_get_config( 'thumbnails_width', envira_get_config_default( 'thumbnails_width' ) ),
				'height'   => $mobile_thumbnail_height, // envira_get_config( 'thumbnails_height', envira_get_config_default( 'thumbnails_height' ) ),
				'quality'  => 100,
				'retina'   => true
			);
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$crop_data = array(
				'id' => $post_id,
				'args' => $args
			);
			$background->background_request( $crop_data, 'crop-images' );

		}

		// If the crop option is checked, crop images accordingly.
		if ( isset( $settings['config']['crop'] ) && $settings['config']['crop'] ) {
			$args = array(
				'align'    => envira_get_config( 'crop_position', $settings ),
				'width'    => envira_get_config( 'crop_width', $settings ),
				'height'   => envira_get_config( 'crop_height', $settings ),
				'quality'  => 100,
				'retina'   => false
			);

			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$test = envira_get_config( 'crop_position', $settings );

			$crop_data = array(
				'id' => $post_id,
				'args' => $args
			);
			$background->background_request( $crop_data, 'crop-images' );

		}

		// If the mobile option is checked, crop images accordingly.
		if ( isset( $settings['config']['mobile'] ) && $settings['config']['mobile'] ) {
			$args = array(
				'align'    => envira_get_config( 'crop_position', $settings ),
				'width'    => envira_get_config( 'mobile_width', $settings ),
				'height'   => envira_get_config( 'mobile_height', $settings ),
				'quality'  => 100,
				'retina'   => false
			);
			$args = apply_filters( 'envira_gallery_crop_image_args', $args );
			$crop_data = array(
				'id' => $post_id,
				'args' => $args
			);

			$background->background_request( $crop_data, 'crop-images' );

		}
	}

}