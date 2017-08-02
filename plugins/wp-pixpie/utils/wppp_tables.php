<?php


/*
Working with images table
*/

function wppp_exists_image ( $attachment_id ) {

	global $wpdb;

	$table_name = $wpdb -> prefix . WPPP_IMAGES_TABLE_NAME;

	// if exists
	$existing_image =
		$wpdb -> get_row ( "SELECT * FROM $table_name WHERE attachment_id = $attachment_id" );

	return ( $existing_image != null );

}

function wppp_add_image ( $attachment_id, $file_name, $size_before, $size_after ) {

	global $wpdb;

	$table_name = $wpdb -> prefix . WPPP_IMAGES_TABLE_NAME;

	if ( wppp_exists_image ( $attachment_id ) ) {

		wppp_log_warning (
			'Image already in the converted images table',
			0,
			'',
			'',
			'wppp_add_image'
		);

		// alreedy exists
		$update_res = $wpdb -> update (
			$table_name,
			array(
				'attachment_id' => $attachment_id,
				'file_name'     => $file_name,
				'size_before'   => $size_before,
				'size_after'    => $size_after,
				'time'          => current_time ( 'mysql' )
			),
			array( 'attachment_id' => $attachment_id )
		);
		if ( ( $update_res == false ) || ( $update_res != 1 ) ) {
			wppp_log_error (
				'Cannot update image in converted table',
				0, '', '', 'wppp_add_image'
			);
		}

	} else {

		// new
		$insert_res = $wpdb -> insert (
			$table_name,
			array(
				'time'          => current_time ( 'mysql' ),
				'attachment_id' => $attachment_id,
				'file_name'     => $file_name,
				'size_before'   => $size_before,
				'size_after'    => $size_after,
			)
		);
		if ( ( $insert_res == false ) || ( $insert_res != 1 ) ) {
			wppp_log_error (
				'Cannot add image to converted table',
				0, '', '', 'wppp_add_image'
			);
		}

	}

}

function wppp_unlist_image ( $attachment_id ) {

	global $wpdb;

	$table_name = $wpdb -> prefix . WPPP_IMAGES_TABLE_NAME;

	if ( wppp_exists_image ( $attachment_id ) ) {

		$wpdb -> delete ( $table_name, array( 'attachment_id' => $attachment_id ) );

	} else {

		wppp_log_warning (
			'Trying to delete image from converted table, which is not there',
			0, '', '', 'wppp_unlist_image'
		);

	}

}

/*
Returns all processed images
*/
function wppp_get_all_images () {

	global $wpdb;

	$table_name = $wpdb -> prefix . WPPP_IMAGES_TABLE_NAME;

	$query = "SELECT * FROM $table_name ORDER BY id DESC LIMIT 10000";

	$all_images = $wpdb -> get_results ( $query, OBJECT );

	return $all_images;

}

function wppp_get_all_images_ids () {
	$all_images = wppp_get_all_images ();
	$all_images_ids = array();
	foreach ( $all_images as $image ) {
		array_push ( $all_images_ids, $image -> attachment_id );
	}

	return $all_images_ids;
}


/*
Work with convert all db table 
*/
function wppp_convert_all_delete_all () {
	global $wpdb;
	$table_name = $wpdb -> prefix . WPPP_CONVERT_ALL_TABLE_NAME;
	$delete = $wpdb -> query ( "TRUNCATE TABLE $table_name" );
}

function wppp_convert_all_add_image ( $attachment_id ) {
	global $wpdb;
	$table_name = $wpdb -> prefix . WPPP_CONVERT_ALL_TABLE_NAME;
	$insert_res = $wpdb -> insert (
		$table_name,
		array(
			'attachment_id' => $attachment_id,
		)
	);
	if ( ( $insert_res == false ) || ( $insert_res != 1 ) ) {
		wppp_log_error (
			'Cannot add image to convert all table',
			0, '', '', 'wppp_convert_all_add_image'
		);
	}
}

/*
returns attachment_id of first record and delete record
*/
function wppp_convert_all_pop () {
	global $wpdb;
	$table_name = $wpdb -> prefix . WPPP_CONVERT_ALL_TABLE_NAME;
	wppp_log_trace (
		'Convert All Table - selecting next image',
		0, '', '', 'convert-all-pop'
	);
	$existing_image =
		$wpdb -> get_row ( "SELECT * FROM $table_name LIMIT 1" );
	wppp_log_trace (
		'Convert All Table - existing_image: ' . print_r ( $existing_image, true ),
		0, '', '', 'convert-all-pop'
	);

	$result_id = - 1;

	if ( $existing_image != null ) {
		$record_id = $existing_image -> id;
		$result_id = $existing_image -> attachment_id;
		wppp_log_trace (
			'Convert All Table - existing_image id: ' . $result_id,
			0, '', '', 'convert-all-pop'
		);
		$wpdb -> delete ( $table_name, array( 'id' => $record_id ) );
		wppp_log_trace (
			'Convert All Table - existing_image deleted',
			0, '', '', 'convert-all-pop'
		);
	} else {
		wppp_log_trace (
			'Convert All Table - image not found',
			0, '', '', 'convert-all-pop'
		);
	}

	return $result_id;
}
