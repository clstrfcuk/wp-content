<?php

/*
Revert
*/


function wppp_revert_revert_all_sizes ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir ) {

	$new_meta_sizes = array();

	foreach ( $sizes as $size_name => $size_val ) {

		wppp_log_trace (
			'checking size: ' . $size_name,
			$image_id, $full_original_filename, $size_name, 'revert-image'
		);

		if ( ! wppp_ends_with ( $size_name, SIZE_UNCOMP ) ) {

			wppp_log_trace (
				'it is original size, proceed',
				$image_id, $full_original_filename, $size_name, 'revert-image'
			);

			$compressed_filename = $meta['sizes'][ $size_name ]['file'];

			wppp_log_trace (
				'compressed_filename = ' . $compressed_filename,
				$image_id, $full_original_filename, $size_name, 'revert-image'
			);

			if ( isset( $compressed_filename ) && ( count ( $compressed_filename ) > 0 ) ) {

				// if image not a full original image
				if ( $full_original_filename != $compressed_filename ) {

					wppp_log_trace (
						'not an original image name, proceed',
						$image_id, $full_original_filename, $size_name, 'revert-image'
					);

					$exploded_filepath = explode ( ".", $compressed_filename );
					$original_file_extension = end ( $exploded_filepath );
					$original_file_name =
						str_replace ( ( "." . $original_file_extension ), '', $compressed_filename );

					$uncompressed_filename =
						$original_file_name . FILENAME_UNCOMP . '.' .
						$original_file_extension;

					wppp_log_trace (
						'uncompressed_filename = ' . $uncompressed_filename,
						$image_id, $full_original_filename, $size_name, 'revert-image'
					);

					if ( file_exists ( $upload_dir['path'] . '/' . $uncompressed_filename ) ) {

						wppp_log_trace (
							'uncompressed_filename exists, proceed',
							$image_id, $full_original_filename, $size_name, 'revert-image'
						);

						// if it is not the same file
						if ( $uncompressed_filename != $compressed_filename ) {

							wppp_log_trace (
								'uncompressed_filename not same as compressed, proceed',
								$image_id, $full_original_filename, $size_name, 'revert-image'
							);

							wppp_log_debug (
								'deleting file: ' .
								$upload_dir['path'] . '/' . $compressed_filename,
								$image_id, $full_original_filename, $size_name, 'revert-image'
							);
							unlink ( $upload_dir['path'] . '/' . $compressed_filename );


							wppp_log_debug (
								'renaming FROM: ' .
								$upload_dir['path'] . '/' . $uncompressed_filename,
								$image_id, $full_original_filename, $size_name, 'revert-image'
							);
							wppp_log_debug (
								'renaming TO: ' .
								$upload_dir['path'] . '/' . $compressed_filename,
								$image_id, $full_original_filename, $size_name, 'revert-image'
							);
							$rename_result = rename (
								$upload_dir['path'] . '/' . $uncompressed_filename,
								$upload_dir['path'] . '/' . $compressed_filename );

							if ( $rename_result == null ) {
								wppp_log_debug (
									'renaming failed',
									$image_id, $full_original_filename, $size_name, 'revert-image'
								);
							}

							$meta['sizes'][ $size_name . SIZE_UNCOMP ]['file'] = '';

						} else {
							wppp_log_warning (
								'uncompressed_filename is same as compressed',
								$image_id, $full_original_filename, $size_name, 'revert-image'
							);
						}

					} else {
						wppp_log_debug (
							'uncompressed_filename - filed does not exist',
							$image_id, $full_original_filename, $size_name, 'revert-image'
						);

					}

				} else {
					wppp_log_trace (
						'this is an original image name, skip',
						$image_id, $full_original_filename, $size_name, 'revert-image'
					);
				}
			}

			// add size to the results
			$new_meta_sizes[ $size_name ] = $meta['sizes'][ $size_name ];
			// mark as noncompressed
			$new_meta_sizes[ $size_name ]['pixpie_compressed'] = false;

		} // end of 'compressed' size

	}

	return $new_meta_sizes;
}


function wppp_revert_revert_full_size ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir ) {

	wppp_log_trace (
		'processing full size image',
		$image_id, $full_original_filename, '', 'revert-image'
	);
	// original full file
	$original_filename = $meta['file'];
	$exploded_filepath = explode ( ".", $original_filename );
	$original_file_extension = end ( $exploded_filepath );
	$original_file_name =
		str_replace ( ( "." . $original_file_extension ), '', $original_filename );
	$original_file_name = str_replace ( ( $time . "/" ), '', $original_file_name );

	$original_filename = $original_file_name . '.' . $original_file_extension;
	wppp_log_trace (
		'original_filename = ' . $original_filename,
		$image_id,
		$full_original_filename, // file_name
		'full', // file_size
		'revert-image' // step
	);

	$uncompressed_filename = wppp_get_uncompressed_filename ( $original_filename );
	wppp_log_trace (
		'uncompressed_filename = ' . $uncompressed_filename,
		$image_id, $full_original_filename, 'full', 'revert-image'
	);

	if ( file_exists ( $upload_dir['path'] . '/' . $uncompressed_filename ) ) {

		wppp_log_debug (
			'deletting file - ' .
			$upload_dir['path'] . '/' . $original_filename,
			$image_id, $full_original_filename, 'full', 'revert-image'
		);
		unlink ( $upload_dir['path'] . '/' . $original_filename );

		wppp_log_debug (
			'rename FROM: ' .
			$upload_dir['path'] . '/' . $uncompressed_filename,
			$image_id, $full_original_filename, 'full', 'revert-image'
		);
		wppp_log_debug (
			'rename TO: ' .
			$upload_dir['path'] . '/' . $original_filename,
			$image_id, $full_original_filename, 'full', 'revert-image'
		);
		$rename_result = rename (
			$upload_dir['path'] . '/' . $uncompressed_filename,
			$upload_dir['path'] . '/' . $original_filename
		);

		if ( $rename_result == null ) {
			wppp_log_error (
				'rename failed',
				$image_id, $full_original_filename, 'full', 'revert-image'
			);
		}

	} else {
		wppp_log_warning (
			'uncompressed_filename - file does not exist: ' .
			$upload_dir['path'] . '/' . $uncompressed_filename,
			$image_id, $full_original_filename, 'full', 'revert-image'
		);

	}
}


function wppp_revert_do_revert_img ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir ) {

	// All sizes
	$new_meta_sizes = wppp_revert_revert_all_sizes ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir );

	wppp_log_trace (
		'all sizes processed: ' . print_r ( $new_meta_sizes, true ),
		$image_id, $full_original_filename, '', 'revert-image'
	);

	$meta['sizes'] = $new_meta_sizes;

	// Full size
	wppp_revert_revert_full_size ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir );

	// save meta
	wppp_log_trace (
		'marking file as not compressed',
		$image_id, '', '', 'revert-image'
	);

	$meta['pixpie_compressed'] = false;
	wp_update_attachment_metadata ( $image_id, $meta );
	wppp_unlist_image ( $image_id );

	wppp_log_debug (
		'file revert done',
		$image_id, '', '', 'revert-image'
	);
}


function wppp_revert_try_to_revert ( $image_id, $full_original_filename, $meta, $time, $upload_dir, $sizes ) {

	wppp_log_trace (
		'valid extension',
		$image_id, $full_original_filename, '', 'revert-image'
	);

	wppp_log_trace (
		'check if image has originals',
		$image_id, $full_original_filename, '', 'revert-image'
	);

	$has_originals = false;

	// original full file
	$original_filename = $meta['file'];
	$exploded_filepath = explode ( ".", $original_filename );
	$original_file_extension = end ( $exploded_filepath );
	$original_file_name =
		str_replace ( ( "." . $original_file_extension ), '', $original_filename );
	$original_file_name = str_replace ( ( $time . "/" ), '', $original_file_name );
	$original_filename = $original_file_name . '.' . $original_file_extension;
	wppp_log_trace (
		'original_filename = ' . $original_filename,
		$image_id, $full_original_filename, 'full', 'revert-image'
	);
	$uncompressed_filename = wppp_get_uncompressed_filename ( $original_filename );
	wppp_log_trace (
		'uncompressed_filename = ' . $uncompressed_filename,
		$image_id, $full_original_filename, 'full', 'revert-image'
	);
	if ( file_exists ( $upload_dir['path'] . '/' . $uncompressed_filename ) ) {
		$has_originals = true;
	}

	wppp_log_trace (
		'has originals? - ' . $has_originals,
		$image_id, $full_original_filename, '', 'revert-image'
	);

	if ( $has_originals ) {

		wppp_revert_do_revert_img ( $image_id, $full_original_filename, $sizes, $meta, $time, $upload_dir );

	} else {

		wppp_log_debug (
			'file does not have originals, cannot revert, but will mark as a reverted',
			$image_id, '', '', 'revert-image'
		);

		// ... just set as uncompressed
		$meta['pixpie_compressed'] = false;
		wp_update_attachment_metadata ( $image_id, $meta );
		wppp_unlist_image ( $image_id );

	}
}


function wppp_revert_image ( $image_id ) {

	wppp_log_debug (
		'Revert started',
		$image_id, '', '', 'revert-image'
	);

	if ( wp_attachment_is_image ( $image_id ) ) {

		$meta = wp_get_attachment_metadata ( $image_id );

		wppp_log_trace (
			'File is image, meta: ' . print_r ( $meta, true ),
			$image_id, '', '', 'revert-image'
		);

		$time = substr ( $meta['file'], 0, 7 ); // Extract the date in form "2015/04"
		$upload_dir = wp_upload_dir ( $time );

		$sizes = wppp_get_image_sizes ();

		$original_filename = $meta['file'];
		$exploded_filepath = explode ( ".", $original_filename );
		$original_file_extension = end ( $exploded_filepath );
		$original_file_name =
			str_replace ( ( "." . $original_file_extension ), '', $original_filename );

		$full_original_filename = $original_file_name . '.' . $original_file_extension;

		wppp_log_trace (
			'full_original_filename: ' . $full_original_filename,
			$image_id, $full_original_filename, '', 'revert-image'
		);

		// is supported extension
		$extensions = array( 'jpg', 'jpeg', 'png' );
		if ( in_array ( strtolower ( $original_file_extension ), $extensions ) ) {

			wppp_revert_try_to_revert ( $image_id, $full_original_filename, $meta, $time, $upload_dir, $sizes );

			// end of is supported resolution
		} else {
			wppp_log_trace (
				'not a supported extension, skip',
				$image_id, '', '', 'revert-image'
			);
		}
		// end of is image
	} else {
		wppp_log_trace (
			'not an image, skip',
			$image_id, '', '', 'revert-image'
		);
	}

}
