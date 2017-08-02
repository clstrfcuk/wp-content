<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://pixpie.co
 * @since             0.0.1
 * @package           WP_Pixpie_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       WP Pixpie Plugin
 * Plugin URI:        https://pixpie.atlassian.net/wiki/display/DOC/Wordpress+plugin
 * Description:       Compress all images via Pixpie service
 * Version:           1.2.0
 * Author:            Pixpie.co
 * Author URI:        https://www.pixpie.co/
 * License:           LGPLv2.1
 * License URI:       https://www.gnu.org/licenses/lgpl-2.1.html
 * Text Domain:       wp-pixpie-plugin
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require_once plugin_dir_path( __FILE__ ).'utils/wppp_common.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_logging.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_emails.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_images.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_tables.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_create_tables.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_convert.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_logs_export.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_http_api.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_view_all_helpers.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_revert.php';
require_once plugin_dir_path( __FILE__ ).'utils/wppp_sign_up.php';




add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wppp_plugin_action_links' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-pixpie-plugin-activator.php
 */
function activate_wp_pixpie_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-pixpie-plugin-activator.php';
	WP_Pixpie_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-pixpie-plugin-deactivator.php
 */
function deactivate_wp_pixpie_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-pixpie-plugin-deactivator.php';
	WP_Pixpie_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_pixpie_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_wp_pixpie_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-pixpie-plugin.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_pixpie_plugin() {

	$plugin = new WP_Pixpie_Plugin();
	$plugin -> run();

}
run_wp_pixpie_plugin();

