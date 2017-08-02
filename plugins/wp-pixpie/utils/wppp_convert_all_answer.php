<?php
//define('WP_USE_THEMES', false);
require_once ( '../../../../wp-load.php' );
require_once ( ABSPATH . 'wp-admin/includes/file.php' );

if ( isset( $_POST["action"] ) ) {

	$action = sanitize_text_field ( $_POST["action"] );

	wppp_log_trace ( 'sanitized action: ' . $action, 0, '', '', 'convert-all' );
	if ( isset( $action ) && ( 'convert' == $action ) ) {

		$total_todo = sanitize_text_field ( $_POST["total_todo"] );
		wppp_log_trace ( 'sanitized total_todo: ' . $total_todo, 0, '', '', 'convert-all' );


		if ( isset( $total_todo ) ) {

			// safe
			$total_todo = intval ( $total_todo );
			if ( $total_todo <= 0 ) {
				wppp_log_error ( 'sanitized total_todo <= 0: ' . $total_todo, 0, '', '', 'convert-all' );
				die();
			}

			/*
			kind of initialize section
			*/
			$total_done = sanitize_text_field ( $_GET["total_done"] );
			wppp_log_trace ( 'sanitized total_done: ' . $total_done, 0, '', '', 'convert-all' );
			if ( ! isset( $total_done ) || ( ! $total_done ) ) {
				$total_done = 0;

				// Initialize
				wppp_log_info ( 'Convert All started', 0, '', '', 'convert-all' );
				wppp_init_convert_all ();
			} else {
				// safe
				$total_done = intval ( $total_done );
				if ( $total_done < 0 ) {
					wppp_log_error ( 'sanitized total_done < 0: ' . $total_done, 0, '', '', 'convert-all' );
					die();
				}
			}

			if ( $total_done > $total_todo ) {
				wppp_log_error ( 'sanitized total_done > total_todo', 0, '', '', 'convert-all' );
				die();
			}

			$total_size_raw = sanitize_text_field ( $_GET["total_size_raw"] );
			if ( ! isset( $total_size_raw ) || ( ! $total_size_raw ) ) {
				$total_size_raw = 0;
			} else {
				// safe
				$total_size_raw = intval ( $total_size_raw );
				if ( $total_size_raw < 0 ) {
					wppp_log_error ( 'sanitized total_size_raw < 0: ' . $total_size_raw, 0, '', '', 'convert-all' );
					die();
				}
			}

			$total_size_comp = sanitize_text_field ( $_GET["total_size_comp"] );
			if ( ! isset( $total_size_comp ) || ( ! $total_size_comp ) ) {
				$total_size_comp = 0;
			} else {
				// safe
				$total_size_comp = intval ( $total_size_comp );
				if ( $total_size_comp < 0 ) {
					wppp_log_error ( 'sanitized total_size_comp < 0: ' . $total_size_comp, 0, '', '', 'convert-all' );
					die();
				}
			}

			// Process
			$image_id = wppp_convert_all_pop ();

			wppp_log_trace (
				'Convert All - processing next image: ' . $image_id,
				0, '', '', 'convert-all'
			);

			if ( isset( $image_id ) && ( $image_id != - 1 ) ) {

				$meta = wp_get_attachment_metadata ( $image_id );

				// if it was already compressed but not in our table
				$pixpie_compressed = $meta['pixpie_compressed'];
				if ( isset( $pixpie_compressed ) && ( $pixpie_compressed == true ) ) {
					$filename = $meta['file'];
					$size_before = $meta['pixpie_raw_sizes'];
					$size_after = $meta['pixpie_compressed_sizes'];
					wppp_log_warning (
						'image not present in converted images, but has pixpie_compressed true, just add to converted images',
						$image_id, '', '', 'convert-all'
					);
					wppp_add_image ( $image_id, $filename, $size_before, $size_after );
				} else {
					$meta = wppp_generate_compressed_images ( $meta );
					wp_update_attachment_metadata ( $image_id, $meta );
				}

				// Recalculate
				$total_done = $total_done + 1;

				$filesizes_before = $meta['pixpie_raw_sizes'];
				$total_size_raw = $total_size_raw + $filesizes_before;

				$filesizes_after = $meta['pixpie_compressed_sizes'];
				$total_size_comp = $total_size_comp + $filesizes_after;

				$total_size_raw_display = wppp_get_display_file_size ( $total_size_raw );
				$total_size_comp_display = wppp_get_display_file_size ( $total_size_comp );

				if ( $total_size_raw > 0 ) {

					$size_reduced = 100 - ( $total_size_comp * 100 / $total_size_raw );
					$size_reduced = number_format ( (float) $size_reduced, 2, '.', '' );

				}

				$continue = get_option ( 'wppp_action_available_status' ) == 'AVAILABLE';

				$return = [ $total_done, $total_todo, $continue ];
				$return['json'] = json_encode ( $return );

				echo json_encode ( $return );

			} else {
				wppp_log_error ( 'image_id not set or incorrect', 0, '', '', 'convert-all' );
				die();
			}

		} else {
			wppp_log_error ( 'total_todo not set or incorrect', 0, '', '', 'convert-all' );
			die();
		}

		?>

		<?php

	}
};

