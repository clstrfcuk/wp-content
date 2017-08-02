<?php

/*
Send emails
*/
function wppp_send_error_by_email( $subject_details, $error_text ) {
	$subject = 'WP PixPie Plugin Error - ' . $subject_details;
	$message = 'Error: ' . $error_text;
	$server_details = wppp_get_server_details();
	$server_details_printed = print_r( $server_details, true );
	$message = 'Server details: ' . $server_details_printed . ' ' . $message;
	wp_mail( WPPP_SUPPORT_EMAIL, $subject , $message );
}

function wppp_send_error_by_email_with_logs( $subject_details, $error_text ) {
	$subject = 'WP PixPie Plugin Error - ' . $subject_details;
	$message = 'Error: ' . $error_text;
	$server_details = wppp_get_server_details();
	$server_details_printed = print_r( $server_details, true );
	$message = 'Server details: ' . $server_details_printed . ' ' . $message;
	$attachment_file = wppp_save_logs_to_file();
	$attachment = array( $attachment_file );
	wp_mail( WPPP_SUPPORT_EMAIL, $subject , $message, '', $attachment );
}

function wppp_sent_activation_notification() {
	$server_address = wppp_get_ip();
	$subject = 'WP Pixpie Plugin Activated - ' . $server_address;
	$message = 'The plugin has been activated';
	$server_details = wppp_get_server_details();
	$server_details_printed = print_r( $server_details, true );
	$message = 'Server details: ' . $server_details_printed . ' ' . $message;
	wp_mail( WPPP_SUPPORT_EMAIL, $subject, $message );
}

function wppp_sent_deactivation_notification() {
	$server_address = wppp_get_ip();
	$subject = 'WP Pixpie Plugin Deactivated - ' . $server_address;
	$message = 'The plugin has been deactivated';
	$server_details = wppp_get_server_details();
	$server_details_printed = print_r( $server_details, true );
	$message = 'Server details: ' . $server_details_printed . ' ' . $message;
	wp_mail( WPPP_SUPPORT_EMAIL, $subject, $message );
}

function wppp_sent_auth_success() {
    $server_address = wppp_get_ip();
    $subject = 'WP Pixpie Plugin Auth Success - ' . $server_address;
    $message = 'The plugin authentication success';
    $server_details = wppp_get_server_details();
    $server_details_printed = print_r( $server_details, true );
    $message = 'Server details: ' . $server_details_printed . ' ' . $message;
    wp_mail( WPPP_SUPPORT_EMAIL, $subject, $message );
}

function wppp_sent_auth_failure($subject_details, $error_text) {
    $server_address = wppp_get_ip();
    $subject = 'WP Pixpie Plugin Auth Failed - ' . $server_address . ' - ' . $subject_details;
    $message = 'Error: ' . $error_text;
    $server_details = wppp_get_server_details();
    $server_details_printed = print_r( $server_details, true );
    $message = 'Server details: ' . $server_details_printed . ' ' . $message;
    wp_mail( WPPP_SUPPORT_EMAIL, $subject , $message );
}

function wppp_sent_signup_attempt( $message ) {
    $server_address = wppp_get_ip();
    $subject = 'WP Pixpie Plugin Sign-up Attempt - ' . $server_address;
    $server_details = wppp_get_server_details();
    $server_details_printed = print_r( $server_details, true );
    $message = 'Server details: ' . $server_details_printed . ' ' . $message;
    wp_mail( WPPP_SUPPORT_EMAIL, $subject, $message );
}



