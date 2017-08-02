<?php

require_once ( '../../../../wp-load.php' );
require_once ( ABSPATH . 'wp-admin/includes/file.php' );



$auth_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/status';
wppp_log_trace (
	'payment auth url: ' . $auth_url,
	0, '', '', 'payment_check'
);

$headers = wppp_get_common_headers ();



$args = array(
	'body'        => '',
	'timeout'     => '120',
	'redirection' => '10',
	'httpversion' => '1.0',
	'blocking'    => true,
	'headers'     => $headers,
	'cookies'     => array()
);

$response = wp_remote_post ( $auth_url, $args );


$action_available_status = json_decode ( $response['body'] ) -> actionAvailableStatus;

$response_status = json_decode ( $response['body'] ) -> responseMessage;

wppp_log_trace (
	'change action status : ' . $action_available_status,
	0, '', '', 'settings_payment_check'
);

if ( $action_available_status == 'AVAILABLE' ){
	delete_option( 'wppp_update_payment' );
}

	echo json_encode( [ $action_available_status, $response_status ] );

