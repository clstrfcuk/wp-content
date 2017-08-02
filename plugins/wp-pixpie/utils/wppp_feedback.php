<?php
require_once ( '../../../../wp-load.php' );
require_once ( ABSPATH . 'wp-admin/includes/file.php' );

$message = $_POST['description'];

wppp_log_trace (
	'wppp_feedback $_POST: ' . print_r ( $_POST['description'], true ),
	0, '', '', 'wppp_deactivation_feedback'
);

$deactivation = wppp_get_server_details_for_feedback( 4, $message );

$response = wppp_deactivation_feedback( $deactivation );

