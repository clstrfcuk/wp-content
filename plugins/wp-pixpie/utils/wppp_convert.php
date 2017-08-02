<?php

function wppp_get_uncompressed_filename ( $original_filename ) {
	$exploded_filepath = explode ( ".", $original_filename );
	$original_file_extension = end ( $exploded_filepath );
	$original_file_name = str_replace ( ( "." . $original_file_extension ), '', $original_filename );
	$uncompressed_filename =
		$original_file_name . FILENAME_UNCOMP . '.' . $original_file_extension;

	return $uncompressed_filename;
}

function wppp_get_tmp_filename ( $original_filename ) {
	wppp_log_trace (
		'original_filename: ' . $original_filename,
		0, '', '', '_get_tmp_filename'
	);
	$exploded_filepath = explode ( ".", $original_filename );
	$original_file_extension = end ( $exploded_filepath );
	wppp_log_trace (
		'original_file_extension: ' . $original_file_extension,
		0, '', '', '_get_tmp_filename'
	);
	$original_file_name = str_replace ( ( "." . $original_file_extension ), '', $original_filename );
	wppp_log_trace (
		'original_file_name: ' . $original_file_name,
		0, '', '', '_get_tmp_filename'
	);
	$uncompressed_filename =
		$original_file_name . FILENAME_TMP . '.' . $original_file_extension;
	wppp_log_trace (
		'uncompressed_filename: ' . $uncompressed_filename,
		0, '', '', '_get_tmp_filename'
	);

	return $uncompressed_filename;
}

function wppp_get_api_call_url ( $in__filename, $upload_dir ) {
	wppp_log_trace (
		'in__filename: ' . $in__filename,
		0, '', '', 'wppp_get_api_call_url'
	);
	$reverse_url = wppp_get_option_no_slashes ( WPPP_OPTION_NAME_BUNDLE_ID );
	$pixpie_api_url = WPPP_API_URL . WPPP_API_CONVERT_IMAGE_PREFIX;

	$upload_dir = $upload_dir['path'];
	$home_path = get_home_path ();
	$upload_dir = str_replace ( $home_path, '', $upload_dir ); // get relative URL
	wppp_log_trace (
		'upload_dir: ' . $upload_dir,
		0, '', '', 'wppp_get_api_call_url'
	);

	// encode filename
	$encoded_filename = rawurlencode ( $in__filename );
	wppp_log_trace (
		'encoded filename: ' . $encoded_filename,
		0, '', '', 'wppp_get_api_call_url'
	);

	$filename = trailingslashit ( $upload_dir ) . $encoded_filename;

	$pixpie_prefix = trailingslashit ( $pixpie_api_url . $reverse_url );
	wppp_log_trace (
		'pixpie_prefix: ' . $pixpie_prefix,
		0, '', '', 'wppp_get_api_call_url'
	);
	$download_url = $pixpie_prefix . $filename;

	return $download_url;
}

function wppp_process_file ( $upload_dir, $original_filename, $size_name, $raw_sizes, $compressed_sizes,
	&$meta_old_size, &$meta_new_size ) {

	$raw_size = - 1;
	$compressed_size = - 1;
	$has_compression_errors = false;
	$save_result = false;

	$full_original_filename = $upload_dir['path'] . '/' . $original_filename;
	wppp_log_trace (
		'full_original_filename: ' . $full_original_filename,
		0, $original_filename, $size_name, 'compression'
	);
	if ( file_exists ( $full_original_filename ) ) {

		/*
		Check tmp filename
		*/
		$tmp_filename = wppp_get_tmp_filename ( $original_filename );
		wppp_log_trace (
			'tmp file: ' . $tmp_filename,
			0, $original_filename, $size_name, 'compression'
		);
		if ( file_exists ( $upload_dir['path'] . '/' . $tmp_filename ) ) {
			wppp_log_warning (
				'warning: tmp file already exist: ' . $upload_dir['path'] . '/' . $tmp_filename,
				0, $original_filename, $size_name, 'compression'
			);

			unlink ( $upload_dir['path'] . '/' . $tmp_filename );
			wppp_log_trace (
				'existing tmp file deleted: ' . $upload_dir['path'] . '/' . $tmp_filename,
				0, $original_filename, $size_name, 'compression'
			);
		}

		/*
		Save to temporary file
		*/

		// save size to raw
		$download_url_local = $upload_dir['url'] . '/' . $original_filename;
		$raw_size = filesize ( $upload_dir['path'] . '/' . $original_filename );
		wppp_log_trace (
			'size of original file (' . $download_url_local . '): ' . $raw_size,
			0, $original_filename, $size_name, 'compression'
		);

		// get from this path
		$file_to_send_path = $upload_dir['path'] . '/' . $original_filename;
		wppp_log_debug (
			'will get and send file from this path: ' . $file_to_send_path,
			0, $original_filename, $size_name, 'compression'
		);

		$api_call_url = wppp_get_api_call_url ( $original_filename, $upload_dir );
		wppp_log_debug (
			'will call this API URL: ' . $api_call_url,
			0, $original_filename, $size_name, 'compression'
		);

		// save to this place
		$uploadfile = $upload_dir['path'] . '/' . $tmp_filename;
		wppp_log_debug (
			'will download to this tmp file: ' . $uploadfile,
			0, $original_filename, $size_name, 'compression'
		);


		/*
		Download
		*/
		wppp_log_trace (
			'started downloadiwppp_call_convert_aping...',
			0, $original_filename, $size_name, 'compression'
		);


		$save_result = wppp_call_convert_api (
			$api_call_url,
			$file_to_send_path,
			$uploadfile,
			$original_filename,
			$size_name,
			0
		);

		wppp_log_trace (
			'download done',
			0, $original_filename, $size_name, 'compression'
		);

	} else {
		$save_result = false;
		wppp_log_error (
			'full_original_filename does not exist ',
			0, $original_filename, $size_name, 'compression'
		);
		$error_message = 'Error while converting image - original file does not exist<br/>';

		/*wppp_send_error_by_email_with_logs(
			'Converting error ' . $original_filename, $error_message );*/
	}

	/*
	Check download results
	*/
	if ( $save_result == true ) {

		wppp_log_trace (
			'download OK',
			0, $original_filename, $size_name, 'compression'
		);

		// save size to compressed
		$compressed_url = $upload_dir['url'] . '/' . $tmp_filename;
		$compressed_size = filesize ( $upload_dir['path'] . '/' . $tmp_filename );

		wppp_log_debug (
			'size of downloaded file (' . $compressed_url . '): ' . $compressed_size,
			0, $original_filename, $size_name, 'compression'
		);

		$tmp_file_path = $upload_dir['path'] . '/' . $tmp_filename;
		if ( file_exists ( $tmp_file_path ) && ( $compressed_size > 0 ) ) {

			wppp_log_trace (
				'downloaded file OK and not empty',
				0, $original_filename, $size_name, 'compression'
			);

			/*
			Get uncomp file name
			*/
			$uncompressed_filename =
				wppp_get_uncompressed_filename ( $original_filename );
			wppp_log_trace (
				'uncompressed filename: ' . $uncompressed_filename,
				0, $original_filename, $size_name, 'compression'
			);

			/*
			Check if uncompressed exists
			*/
			if ( file_exists ( $upload_dir['path'] . '/' . $uncompressed_filename ) ) {

				wppp_log_warning (
					'uncompressed filename already exists',
					0, $original_filename, $size_name, 'compression'
				);

			}

			/*
			Rename original file to uncomp
			*/

			$keep_originals = get_option ( WPPP_OPTION_NAME_KEEP_ORIGINAL );
			$availeble = get_option( 'wppp_action_available_status' ) == 'AVAILEBEL';

			$rename_result = false;
			if ( isset( $keep_originals ) && ( $keep_originals == 1 )  ) {

				wppp_log_debug (
					'renaming original FROM: ' . $upload_dir['path'] . '/' . $original_filename,
					0, $original_filename, $size_name, 'compression'
				);
				wppp_log_debug (
					'renaming original TO: ' . $upload_dir['path'] . '/' . $uncompressed_filename,
					0, $original_filename, $size_name, 'compression'
				);
				$rename_result = rename (
					$upload_dir['path'] . '/' . $original_filename,
					$upload_dir['path'] . '/' . $uncompressed_filename
				);

				if ( $rename_result ) {
					wppp_log_trace (
						'renaming OK',
						0, $original_filename, $size_name, 'compression'
					);
				}

				/*
				Save renamed origial to _uncomp size
				*/
				wppp_log_trace (
					'$meta_new_size: ' . print_r ( $meta_new_size, true ),
					0, $original_filename, $size_name, 'compression'
				);

				if ( $meta_new_size != null ) {
					$meta_new_size['file'] = $uncompressed_filename;
				}

				wppp_log_trace (
					'$meta_new_size: ' . print_r ( $meta_new_size, true ),
					0, $original_filename, $size_name, 'compression'
				);

			} else {
				wppp_log_trace (
					'keep originals not set, skip renaming originals to uncompressed',
					0, $original_filename, $size_name, 'compression'
				);

				$rename_result = true;

				if ( $meta_new_size != null ) {
					$meta_new_size['file'] = null;
				}

			}

			if ( $rename_result ) {

				/*
				Rename from tmp to original
				*/
				wppp_log_debug (
					'renaming tmp FROM: ' . $upload_dir['path'] . '/' .
					$tmp_filename,
					0, $original_filename, $size_name, 'compression'
				);
				wppp_log_debug (
					'renaming tmp TO: ' . $upload_dir['path'] . '/' .
					$original_filename,
					0, $original_filename, $size_name, 'compression'
				);
				$rename_result = rename (
					$upload_dir['path'] . '/' . $tmp_filename,
					$upload_dir['path'] . '/' . $original_filename
				);

				if ( $rename_result ) {
					wppp_log_trace (
						'renamed tmp to original OK',
						0, $original_filename, $size_name, 'compression'
					);

				} else {

					wppp_log_error (
						'error while renaming tmp to original',
						0, $original_filename, $size_name, 'compression'
					);

				}

			} else {
				wppp_log_error (
					'error while renaming original to uncomp',
					0, $original_filename, $size_name, 'compression'
				);
			}

		} else {

			wppp_log_error (
				'downloaded file is broken',
				0, $original_filename, $size_name, 'compression'
			);

		}

	} else {
		error_log ( 'WPPP - converted file was not saved' );
		if ( $meta_old_size != null ) {
			$meta_old_size['pixpie_compressed'] = false;
			$meta_old_size['errors'] = 'Error while compressing file';
		}
		$has_compression_errors = true;
	}

	return array(
		'raw_size'               => $raw_size,
		'compressed_size'        => $compressed_size,
		'has_compression_errors' => $has_compression_errors,
	);

}

function wppp_convert_do_convert_sizes ( $sizes, &$meta, $full_original_filename, $upload_dir,
	&$raw_sizes, &$compressed_sizes, &$has_compression_errors ) {

	foreach ( $sizes as $size_name => $size_val ) {

		wppp_log_trace (
			'size ' . print_r ( $size_name, true ),
			0, $meta['file'], $size_name, 'compression'
		);

		// if compressed image
		if ( ! wppp_ends_with ( $size_name, SIZE_UNCOMP ) ) {

			$original_filename = $meta['sizes'][ $size_name ]['file'];

			wppp_log_trace (
				'uncompressed filename: ' . $original_filename,
				0, $original_filename, $size_name, 'compression'
			);

			if ( isset( $original_filename ) && ( count ( $original_filename ) > 0 ) ) {

				// if size is not the full original file
				if ( $full_original_filename != $original_filename ) {

					/*
					Save original file meta
					*/
					$new_size_name = $size_name . SIZE_UNCOMP;
					$meta['sizes'][ $new_size_name ] = $meta['sizes'][ $size_name ];
					wppp_log_trace (
						'original file meta copied to size ' . $new_size_name,
						0, $original_filename, $size_name, 'compression'
					);

					$result_sizes = wppp_process_file (
						$upload_dir,
						$original_filename,
						$size_name,
						$raw_sizes,
						$compressed_sizes,
						$meta['sizes'][ $size_name ],
						$meta['sizes'][ $new_size_name ]
					);

					$raw_size = $result_sizes['raw_size'];
					$compressed_size = $result_sizes['compressed_size'];

					if ( ( $raw_size >= 0 ) && ( $compressed_size >= 0 ) ) {
						$raw_sizes = $raw_sizes + $raw_size;
						$compressed_sizes = $compressed_sizes + $compressed_size;
					} else {
						wppp_log_error (
							'one of result sizes is zero',
							0, $original_filename, $size_name, 'compression'
						);
					}

					// only fail if there are errors
					$has_compression_errors_result = $result_sizes['has_compression_errors'];
					if ( ! $has_compression_errors_result ) {
						$has_compression_errors = false;
					} else {
						$meta['sizes'][ $size_name ]['pixpie_compressed'] = true;
					}

				} else {
					wppp_log_trace (
						'file is same as full image, skip',
						0, $original_filename, $size_name, 'compression'
					);
				}
			}

		} else {
			wppp_log_trace (
				'not an original file size, skip',
				0, $meta['file'], $size_name, 'compression'
			);
		}

	} // end of all sizes

}

function wppp_convert_do_convert_full ( &$meta, $full_original_filename, $upload_dir, &$raw_sizes,
	&$compressed_sizes, &$has_compression_errors ) {

	// Process full file to comp
	$original_filename = $meta['file'];
	$time = substr ( $meta['file'], 0, 7 ); // Extract the date in form "2015/04"
	$exploded_filepath = explode ( ".", $original_filename );
	$original_file_extension = end ( $exploded_filepath );
	$original_file_name =
		str_replace ( ( "." . $original_file_extension ), '', $original_filename );
	// do some more and cut-off time
	$original_file_name = str_replace ( ( $time . "/" ), '', $original_file_name );
	$original_filename = $original_file_name . '.' . $original_file_extension;

	wppp_log_trace (
		'processing full size original filename: ' . $original_filename,
		0, $meta['file'], 'full', 'compression'
	);

	$result_sizes = wppp_process_file (
		$upload_dir,
		$original_filename,
		'full',
		$raw_sizes,
		$compressed_sizes,
		$null = null,
		$null = null
	);

	$raw_size = $result_sizes['raw_size'];
	$compressed_size = $result_sizes['compressed_size'];

	if ( ( $raw_size >= 0 ) && ( $compressed_size >= 0 ) ) {
		$raw_sizes = $raw_sizes + $raw_size;
		$compressed_sizes = $compressed_sizes + $compressed_size;
	} else {
		wppp_log_error (
			'one of result sizes is zero',
			0, $original_filename, 'full', 'compression'
		);
	}

	// only fail if there are errors
	$has_compression_errors_result = $result_sizes['has_compression_errors'];
	if ( ! $has_compression_errors_result ) {
		$has_compression_errors = false;
	}

}

function wppp_convert_do_convert_img ( $time, $original_filename, &$meta, $sizes, $full_original_filename, $upload_dir ) {

	$raw_sizes = 0;
	$compressed_sizes = 0;

	$has_compression_errors = false;

	$sizesMeta = [];

	$arrB = get_option ( WPPP_OPTION_IMGS_SIZE );

	foreach ( $meta['sizes'] as $size_name => $size_val ) {

		if ( strrpos ( $size_name, '_uncomp' ) !== false ) {

			$cor_size_name = substr ( $size_name, 0, strlen ( $size_name ) - 7 );

			if ( ! in_array ( $cor_size_name, $arrB ) ) {
				continue;
			}
		}

		$sizesMeta[ $size_name ] = $size_val;
	}

	$meta['sizes'] = $sizesMeta;

	wppp_log_trace (
		'sizes' . print_r ( $sizes, true ),
		0, $meta['file'], '', 'compression'
	);

	wppp_convert_do_convert_sizes ( $sizes, $meta, $full_original_filename, $upload_dir, $raw_sizes,
		$compressed_sizes, $has_compression_errors );

	wppp_convert_do_convert_full ( $meta, $full_original_filename, $upload_dir, $raw_sizes, $compressed_sizes,
		$has_compression_errors );

	$meta['pixpie_raw_sizes'] = $raw_sizes;
	$meta['pixpie_compressed_sizes'] = $compressed_sizes;

	if ( $has_compression_errors ) {
		$meta['pixpie_compressed'] = false;

		wppp_log_warning (
			'compressed with errors',
			0, $meta['file'], '', 'compression'
		);

	} else {

		$meta['pixpie_compressed'] = true;

		wppp_log_info (
			'compressed with no errors, ' . $raw_sizes . ' -> ' . $compressed_sizes,
			0, $meta['file'], '', 'compression'
		);

		if ( $raw_sizes <= $compressed_sizes ) {
			wppp_log_warning (
				'Size of compressed file is not smaller than original',
				0, $original_filename, '', 'compression'
			);
		}

		$attachment_url =
			$upload_dir['url'] . '/' .
			str_replace ( ( $time . "/" ), '', $full_original_filename );
		wppp_log_trace (
			'attachment successfully processed, attachment_url = ' . $attachment_url,
			0, $meta['file'], '', 'compression'
		);

		$attachment_id = attachment_url_to_postid ( $attachment_url );

		if ( $attachment_id > 0 ) {

			wppp_add_image (
				$attachment_id,
				$full_original_filename,
				$raw_sizes,
				$compressed_sizes );

			wppp_log_trace (
				'attachment successfully processed, attachment_id = ' . $attachment_id,
				0, $meta['file'], '', 'compression'
			);

		} else {

			wppp_log_error (
				'attachment_id was not found by URL = ' . $attachment_url,
				0, $meta['file'], '', 'compression'
			);

			$meta['pixpie_compressed'] = false;

		}

	}

	return $meta;
}

function wppp_generate_compressed_images ( $meta ) {

	// if plugin set up
	if ( wppp_is_plugin_activated () ) {


		if ( get_option ( 'wppp_action_available_status' ) == 'AVAILABLE' ) {
			set_time_limit ( 0 );

			$sizes = [];
				$arrA = wppp_get_image_sizes ();
				$arrB = get_option ( WPPP_OPTION_IMGS_SIZE );

				foreach ( $arrA as $size_name => $size_val ) {
					if ( in_array ( $size_name, $arrB ) ) {
						$sizes[ $size_name ] = $size_val;
					}
				}


			wppp_log_info (
				'generate_compressed_images started: ' . print_r ( $meta, true ),
				0, $meta['file'], '', 'compression'
			);

			$time = substr ( $meta['file'], 0, 7 ); // Extract the date in form "2015/04"
			$upload_dir = wp_upload_dir ( $time );


			// Full size
			$original_filename = $meta['file'];
			$exploded_filepath = explode ( ".", $original_filename );
			$original_file_extension = end ( $exploded_filepath );
			$original_file_name =
				str_replace ( ( "." . $original_file_extension ), '', $original_filename );

			// save FULL
			$full_original_filename = $original_file_name . '.' . $original_file_extension;

			wppp_log_trace (
				'full_original_filename: ' . $full_original_filename,
				0, $meta['file'], '', 'compression'
			);

			$extensions = array( 'jpg', 'jpeg', 'png' );
			if ( in_array ( strtolower ( $original_file_extension ), $extensions ) ) {
				// file is supported image

				wppp_log_trace (
					'file extension is supported',
					0, $meta['file'], '', 'compression'
				);

				$meta = wppp_convert_do_convert_img ( $time, $original_filename, $meta, $sizes, $full_original_filename,
					$upload_dir );

			} else {
				wppp_log_trace (
					'file extension is NOT supported, skip',
					0, $meta['file'], '', 'compression'
				);
			}
		} else {

			return $meta;

		}

	} else {
		wppp_log_warning (
			'plugin is not set up',
			0, $meta['file'], '', 'compression'
		);
	}

	wppp_log_trace (
		'finished, meta: ' . print_r ( $meta, true ),
		0, $meta['file'], '', 'compression'
	);

	if( strlen( wppp_get_option_no_slashes( WPPP_OPTION_NAME_KEEP_ORIGINAL ) ) == 0 ) {

		$sizesEnd = [];
		$arrC = $meta['sizes'];
		$arrD = get_option ( WPPP_OPTION_IMGS_SIZE );

		foreach ( $arrC as $size_name => $size_val ) {
			if ( in_array ( $size_name, $arrD ) ) {
				$sizesEnd[ $size_name ] = $size_val;
			}
		}

		$meta['sizes'] = $sizesEnd;

		wppp_log_trace (
			'finished, size: ' . print_r ( $sizesEnd, true ),
			0, $meta['file'], '', 'compression'
		);

	}


	return $meta;
}

function wppp_get_all_images_to_convert () {
	$images_to_convert = array();
	$paginate_by = WPPP_SELECT_ALL_IMAGES_PAGE_SIZE;
	$offset = 0;
	$has_more_images = true;
	while ( $has_more_images ) {
		$args = array(
			'posts_per_page' => $paginate_by,
			'offset'         => $offset,
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'order'          => 'ASC'
		);
		$the_query = new WP_Query( $args );
		if ( $the_query -> have_posts () ) {
			while ( $the_query -> have_posts () ) {
				$the_query -> the_post ();
				$image_id = get_the_ID ();
				if ( wp_attachment_is_image ( $image_id ) ) {
					$meta = wp_get_attachment_metadata ( $image_id );
					if ( isset( $meta ) && is_array ( $meta ) ) {
						if ( array_key_exists ( 'file', $meta ) ) {
							$original_filename = $meta['file'];
							$exploded_filepath = explode ( ".", $original_filename );
							$original_file_extension = end ( $exploded_filepath );
							// is supported extension
							$extensions = array( 'jpg', 'jpeg', 'png' );
							if ( in_array ( strtolower ( $original_file_extension ), $extensions ) ) {
								// if not converted already
								if ( ! wppp_exists_image ( $image_id ) ) {
									array_push ( $images_to_convert, $image_id );
								}
							}
						} else {
							wppp_log_error (
								'meta file is absent: ' . print_r ( $meta, true ),
								0, '', '', 'get-all-images-to-convert'
							);
						}
					} else {
						wppp_log_error (
							'canot get image meta: ' . $image_id,
							0, '', '', 'get-all-images-to-convert'
						);
					}
				}
			}
		} else {
			$has_more_images = false; // STOP
		}

		$offset = $offset + $paginate_by;
	}
	wp_reset_postdata ();

	return $images_to_convert;
}

function wppp_init_convert_all () {

	wppp_log ( 'info', 'Convert All started',
		0, '', '', 'wppp_init_convert_all' );

	wppp_log_trace (
		'Convert All - collecting all images to process',
		0, '', '', 'wppp_init_convert_all' );

	$images_to_convert = wppp_get_all_images_to_convert ();
	$to_convert_count = count ( $images_to_convert );
	wppp_log_trace (
		'Convert All - total ' . $to_convert_count . ' images to do',
		0, '', '', 'wppp_init_convert_all'
	);

	wppp_log_trace (
		'Convert All - deleting convert all table',
		0, '', '', 'wppp_init_convert_all'
	);


	// Clean & fill the table
	wppp_convert_all_delete_all ();
	wppp_log_trace (
		'Convert All - filling convert all table',
		0, '', '', 'wppp_init_convert_all'
	);

	foreach ( $images_to_convert as $image_id ) {
		wppp_convert_all_add_image ( $image_id );
	}
	wppp_log_trace (
		'Convert All - convert all table populated',
		0, '', '', 'wppp_init_convert_all'
	);

}

/**
 * Calculate how many real images (maximum) will be processed for a single media item
 */
function count_original_resolutions ( $all_sizes ) {
	$original_resolutions = 1;  // FULL IMAGE
	foreach ( $all_sizes as $size_name => $size_val ) {
		// if compressed image
		if ( ! wppp_ends_with ( $size_name, SIZE_UNCOMP ) ) {
			$original_resolutions ++;
		}
	}

	return $original_resolutions;
}