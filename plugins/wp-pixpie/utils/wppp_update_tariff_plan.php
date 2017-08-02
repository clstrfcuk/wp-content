<?php
require_once ( '../../../../wp-load.php' );
require_once ( ABSPATH . 'wp-admin/includes/file.php' );

$progress = $_POST['progress'];

update_option ( 'wppp_update_payment', $progress );

$api_url = 'http://' . WPPP_SAFE_REDIRECT_CLOUD_HOST . 'payment/upgrade_plan';

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
wppp_log_trace (
	'update: ' . print_r ( $args, true ),
	0, '', '', 'wppp_update_tarif_plan'
);

wppp_log_trace (
	'update_request_header : ' . print_r ( $headers, true ),
	0, '', '', 'wppp_update_tarif_plan'
);

$response = wp_remote_post ( $api_url, $args );

$code = $response['response']['code'];
$text_response = json_decode ( $response['body'] ) -> responseMessage;

wppp_log_trace (
	'update_response_status : ' . $code,
	0, '', '', 'wppp_update_tarif_plan'
);

wppp_log_trace (
	'update_response_status : ' . json_decode ( $response['body'] ) -> responseMessage
	,
	0, '', '', 'wppp_update_tarif_plan'
);

wppp_log_trace (
	'update_response : ' . print_r ( $response, true ),
	0, '', '', 'wppp_update_tarif_plan'
);


if ( $code == 200 ) {
	echo json_encode ( [ $code, '<div id="succespixpie" class="updated">' . $text_response . '</div>' ] );
} elseif ( $code == 404 ) {
	echo json_encode ( [ $code, '<div id="error" class="error">' . $text_response . '</div>' ] );
} elseif ( $code == 500 ) {
	echo json_encode ( [
		$code,
		'<div id="error" class="error"> Some error occurred on server side, try later</div>' ] );
} else {
	die();
}



