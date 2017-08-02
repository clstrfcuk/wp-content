<?php

function wppp_get_display_file_size( $file_size ) {
	$display_size = $file_size / 1024;
	if ( $display_size > 512 ) {
		$display_size = number_format( (float) ( $display_size / 1024 ), 2, '.', '' ) . ' Mb';
	} else {
		$display_size = number_format( (float) $display_size, 2, '.', '' ) . ' Kb';
	}

	return $display_size;
}

/*
Working with options
*/

function wppp_is_option_empty( $option_name ) {
	$opt_val = get_option( $option_name );

	return empty( $opt_val );
}

function wppp_is_option_equals( $option_name, $value ) {
	$opt_val = get_option( $option_name );

	return $value == $opt_val;
}

function wppp_ends_with( $haystack, $needle ) {
	// search forward starting from end minus needle length characters
	return $needle === "" || ( ( $temp = strlen( $haystack ) - strlen( $needle ) ) >= 0 && strpos( $haystack, $needle, $temp ) !== false );
}

function wppp_get_option_no_slashes( $the_option ) {
	return stripslashes( get_option( $the_option ) );
}

function wppp_is_plugin_activated() {
	$result =
		( ! wppp_is_option_empty( WPPP_OPTION_NAME_BUNDLE_ID ) ) &&
		( ! wppp_is_option_empty( WPPP_OPTION_NAME_SECRET_KEY ) ) &&
		( ! wppp_is_option_empty( WPPP_OPTION_NAME_STATUS ) ) &&
		wppp_is_option_equals( WPPP_OPTION_NAME_STATUS, 'active' );

	return $result;
}

function wppp_is_plugin_activated_without_auth() {

	$result =
		( ! wppp_is_option_empty( 'wppp_db_ver_stat' ) ) &&
		wppp_is_option_equals( 'wppp_db_ver_stat', 'activate' ) || wppp_is_option_equals( 'wppp_db_ver_stat', 'update' );

	return $result;

}


/*
Get server details
*/

function wppp_get_ip() {
	if ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
	} else {
		$headers = $_SERVER;
	}
	if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		$the_ip = $headers['X-Forwarded-For'];
	} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
	) {
		$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
	} else {
		$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}

	return $the_ip;
}

function wppp_get_server_details() {
	$server_address = wppp_get_ip();
	$server_port    = $_SERVER['SERVER_PORT'];
	$php_version    = PHP_VERSION;
	global $wp_version;
	$wordpress_version = $wp_version;
	$admin_email       = get_option( 'admin_email' );
	$blogname          = get_option( 'blogname' );
	$blog_charset      = get_option( 'blog_charset' );
	$siteurl           = get_option( 'siteurl' );
	$bundle_id         = wppp_get_option_no_slashes( WPPP_OPTION_NAME_BUNDLE_ID );
	$secret_key        = wppp_get_option_no_slashes( WPPP_OPTION_NAME_SECRET_KEY );
	$wppp_version      = WPPP_VERSION;

	return array(
		'server_address'    => $server_address,
		'server_port'       => $server_port,
		'php_version'       => $php_version,
		'wordpress_version' => $wordpress_version,
		'admin_email'       => $admin_email,
		'blogname'          => $blogname,
		'blog_charset'      => $blog_charset,
		'siteurl'           => $siteurl,
		'bundle_id'         => $bundle_id,
		'wppp_version'      => $wppp_version,
		'secret_key'        => $secret_key,
	);
}
//	$active_plugins    = print_r( wppp_get_active_plugins(), true );

//		'active_plugins'    => $active_plugins,

function wppp_get_server_details_for( $notification ) {
	$server_address = wppp_get_ip();
	$server_port    = $_SERVER['SERVER_PORT'];
	$php_version    = PHP_VERSION;
	global $wp_version;
	$wordpress_version = $wp_version;
	$admin_email       = get_option( 'admin_email' );
	$blogname          = get_option( 'blogname' );
	$siteurl           = get_option( 'siteurl' );
	$wppp_version      = WPPP_VERSION;

	return array(
		'notificationId'  => $notification,
		'serverAddress'   => $server_address,
		'serverPort'      => $server_port,
		'phpVersion'      => $php_version,
		'platformVersion' => $wordpress_version,
		'adminEmail'      => $admin_email,
		'blogName'        => $blogname,
		'siteUrl'         => $siteurl,
		'pluginType'      => 1,
		'pluginVersion'   => $wppp_version
	);
}


function wppp_get_server_details_for_feedback( $notification, $feedback ) {
	$server_address = wppp_get_ip();
	$server_port    = $_SERVER['SERVER_PORT'];
	$php_version    = PHP_VERSION;
	global $wp_version;
	$wordpress_version = $wp_version;
	$admin_email       = get_option( 'admin_email' );
	$blogname          = get_option( 'blogname' );
	$siteurl           = get_option( 'siteurl' );
	$wppp_version      = WPPP_VERSION;

	return array(
		'notificationId'  => $notification,
		'serverAddress'   => $server_address,
		'serverPort'      => $server_port,
		'phpVersion'      => $php_version,
		'platformVersion' => $wordpress_version,
		'adminEmail'      => $admin_email,
		'blogName'        => $blogname,
		'siteUrl'         => $siteurl,
		'pluginType'      => 1,
		'pluginVersion'   => $wppp_version,
		'description' => $feedback
	);
}

function wppp_get_active_plugins() {
	$results   = array();
	$the_plugs = get_option( 'active_plugins' );
	foreach ( $the_plugs as $key => $value ) {
		$string = explode( '/', $value );
		array_push( $results, $string[0] );
	}

	return $results;
}


/*
 * For Redirects
 */
function wppp_allowed_redirect_hosts( $content ) {
	$content[] = WPPP_SAFE_REDIRECT_CLOUD_HOST;

	return $content;
}