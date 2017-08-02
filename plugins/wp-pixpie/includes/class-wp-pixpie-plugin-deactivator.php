<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 * @author     Your Name <email@example.com>
 */
class WP_Pixpie_Plugin_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
//		wppp_sent_deactivation_notification();
		$bodyDeactivation = wppp_get_server_details_for( 2 );
		wppp_deactivation_plugin( $bodyDeactivation );

		$server_details = wppp_get_server_details();
		$server_details_printed = print_r( $server_details, true );
		wppp_log_info(
			'Plugin Deactivated - ' . $server_details_printed,
			0,'','','activation'
		);

	}

}
