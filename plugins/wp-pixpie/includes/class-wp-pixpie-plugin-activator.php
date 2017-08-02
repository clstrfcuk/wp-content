<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 */


add_action ( 'plugins_loaded', 'wp_pixpie_update_db_check' );


/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 * @author     Your Name <email@example.com>
 */
class WP_Pixpie_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * @since    0.0.1
	 */
	public static function activate () {

		/*add SID to db -> table wp_options*/

		if ( WPPP_VERSION !== wppp_get_option_no_slashes ( 'wppp_db_version' ) && strlen ( wppp_get_option_no_slashes ( 'wppp_db_version' ) ) > 0 ) {

			$bodyUpdate = wppp_get_server_details_for ( 3 );
			wppp_log_info (
				'Plugin Update - ' . $bodyUpdate,
				0, '', '', 'activation'
			);
			wppp_update_plugin ( $bodyUpdate );
			update_option ( 'wppp_db_ver_stat', 'update' );
		} else {
			$bodyActivation = wppp_get_server_details_for ( 1 );
			wppp_activate_plugin ( $bodyActivation );
			update_option ( 'wppp_db_ver_stat', 'activate' );
		}

		wppp_update_database ();

		// DB install done - save version
		update_option ( 'wppp_db_version', WPPP_VERSION );
		wppp_log_trace (
			'Plugin Activation - New version: ' . WPPP_VERSION,
			0, '', '', 'activation'
		);

//		wppp_sent_activation_notification();

		// set option if not set at activation
		$keep_originals_settings = get_option ( WPPP_OPTION_NAME_KEEP_ORIGINAL );
		if ( empty( $keep_originals_settings ) ) {
			update_option ( WPPP_OPTION_NAME_KEEP_ORIGINAL, 1 );
		}

		$server_details = wppp_get_server_details ();
		$server_details_printed = print_r ( $server_details, true );
		wppp_log_info (
			'Plugin Activated - ' . $server_details_printed,
			0, '', '', 'activation'
		);

	}

}
