<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the wp pixpie plugin
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Pixpie_Plugin
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined ( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
Delete options
*/
delete_option ( 'wppp_option_bundle_id' );
delete_option ( 'wppp_option_secret_key' );
delete_option ( 'wppp_option_keep_original' );
delete_option ( 'wppp_option_status' );
delete_option ( 'wppp_option_imgs_size' );
delete_option ( 'wppp_db_version' );
delete_option ( 'wppp_option_sid' );
delete_option ( 'wppp_db_ver_stat' );
delete_option ( 'WPPP_OPTION_NAME_STATUS' );
delete_option ( 'wppp_action_available_status' );
delete_option ( 'wppp_hide_time' );


/*
Delete tables 
*/
global $wpdb;
$tables = array( 'wppp_log', 'wppp_converted_images', 'wppp_convert_all' );
foreach ( $tables as $key => $table_name ) {
	$table_name = $wpdb -> prefix . $table_name;
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb -> query ( $sql );
} ?>
