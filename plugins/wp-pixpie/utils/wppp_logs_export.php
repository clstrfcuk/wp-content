<?php


function wppp_logs_export_get_header_row()
{
    $header_row = array(
        0 => 'ID',
        1 => 'Time',
        2 => 'Attachment ID',
        3 => 'File name',
        4 => 'Size name',
        5 => 'Step',
        6 => 'Level',
        7 => 'Message',
    );
    return $header_row;
}

function wppp_logs_export_get_data_rows( $limit ) {

    $data_rows = array();

    global $wpdb;

    $table_name = $wpdb->prefix . WPPP_LOG_TABLE_NAME;

    $query =
        "
		SELECT * 
		FROM $table_name
		ORDER BY id DESC
		LIMIT $limit
		";

    $all_log = $wpdb->get_results(
        $query,
        OBJECT
    );

    foreach ( $all_log as $log_record ) {
        $row = array();
        $row[0] = $log_record->id;
        $row[1] = $log_record->time;
        $row[2] = $log_record->attachment_id;
        $row[3] = $log_record->file_name;
        $row[4] = $log_record->size_name;
        $row[5] = $log_record->step;
        $row[6] = $log_record->level;
        $row[7] = $log_record->message;

        $data_rows[] = $row;
    }

    return $data_rows;
}


function wppp_print_logs_csv() {

	if ( ! is_super_admin() ) {
		return;
	}

	if ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'wppp_print_logs' ) ) {
		return;
	}	

	$filename = 'wppp-logs-' . time() . '.csv';

    $header_row = wppp_logs_export_get_header_row();

    $data_rows = wppp_logs_export_get_data_rows( 40000 );

	$fh = @fopen( 'php://output', 'w' );
	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );
	fputcsv( $fh, $header_row );
	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}
	fclose( $fh );
	die();
}



function wppp_save_logs_to_file() {

    wppp_log_trace(
        'started writting logs to file',
        0, '', '', 'wppp_save_logs_to_file'
    );

    try {

        $upload_dir = wp_upload_dir();
        $file_path = trailingslashit( $upload_dir['path'] ) . 'wppp-logs-' . time() . '.csv';

        wppp_log_trace(
            'file_path: ' . $file_path,
            0, '', '', 'wppp_save_logs_to_file'
        );

        $header_row = wppp_logs_export_get_header_row();
        $data_rows = wppp_logs_export_get_data_rows( 500 );

        $fh = @fopen( $file_path, 'w' );
        fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
        fputcsv( $fh, $header_row );
        foreach ( $data_rows as $data_row ) {
            fputcsv( $fh, $data_row );
        }
        fclose( $fh );
        wppp_log_trace(
            'writting logs to file done',
            0, '', '', 'wppp_save_logs_to_file'
        );
        return $file_path;

    } catch (Exception $e) {

        wppp_log_error(
            'cannot write logs to file',
            0, '', '', 'wppp_save_logs_to_file'
        );
        return null;
    }
}
