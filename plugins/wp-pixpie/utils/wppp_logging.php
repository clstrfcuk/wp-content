<?php



/*
Logging
*/
function wppp_log( $level, $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	
    try {

		global $wpdb;

		$table_name = $wpdb->prefix . WPPP_LOG_TABLE_NAME;

		$insert_res = $wpdb->insert( 
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ), 
				'attachment_id' => $attachment_id, 
				'file_name' => $file_name, 
				'size_name' => $size_name, 
				'step' => $step, 
				'level' => $level, 
				'message' => $message, 
			) 
		);

		if ( ( $insert_res == false ) || ( $insert_res == 0 ) ) {
			error_log( "Error in wppp_log, insert returned zero value" );
//			wppp_send_error_by_email( 'Log writing error - cannot write logs',
//				'Insert returned zero value. Original message: ' . $message );
		}

		return $insert_res;

    } catch ( Exception $e ) {
        error_log( "Error in wppp_log: " . print_r( $e, true ) );
		/*wppp_send_error_by_email( 'Log writing error - cannot write logs',
			'Exception: ' . print_r( $e, true ) . '   Original message: ' . $message );        */
    }	

}

function wppp_log_info( $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	return wppp_log( 'info', $message, $attachment_id, $file_name, $size_name, $step );
}
function wppp_log_debug( $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	return wppp_log( 'debug', $message, $attachment_id, $file_name, $size_name, $step );
}
function wppp_log_trace( $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	return wppp_log( 'trace', $message, $attachment_id, $file_name, $size_name, $step );
}
function wppp_log_warning( $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	return wppp_log( 'warning', $message, $attachment_id, $file_name, $size_name, $step );
}
function wppp_log_error( $message, $attachment_id=0, $file_name='', $size_name='', $step='' ) {
	return wppp_log( 'error', $message, $attachment_id, $file_name, $size_name, $step );
}
