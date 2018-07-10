<?php
/**
 * Plugin Name: Envira Gallery
 * Plugin URI:  http://enviragallery.com
 * Description: Envira Gallery is best responsive WordPress gallery plugin.
 * Author:      Envira Gallery Team
 * Author URI:  http://enviragallery.com
 * Version:     1.8.2.1
 * Text Domain: envira-gallery
 * Domain Path: languages
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Envira
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If the user does not have a sufficient version of PHP, exit.
if ( version_compare( phpversion(), '5.3', '<' ) ) {

	wp_die( sprintf( __( 'Envira Gallery requires PHP 5.3 or higher. You\'re still on PHP %s. Please contact your web host for more information on how to upgrade your PHP version.', 'Envira Gallery' ), get_admin_url() ) );

}

use Envira\Admin\Admin_Container;
use Envira\Frontend\Frontend_Container;
use Envira\Utils\Updater;

//Register the installation/uninstall hooks
register_activation_hook( __FILE__, 'Envira_Gallery::activate' );
register_deactivation_hook( __FILE__, 'Envira_Gallery::deactivate' );
register_uninstall_hook( __FILE__, 'Envira_Gallery::uninstall' );

if ( !class_exists( 'Envira_Gallery' ) ):

	/**
	 * Main plugin class.
	 *
	 * @since 1.7.0
	 *
	 * @package Envira_Gallery
	 * @author  Thomas Griffin
	 */
	class Envira_Gallery {

		/**
		 * Holds the class object.
		 *
		 * @since 1.7.0
		 *
		 * @var object
		 */
		public static $instance = null;

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since 1.7.0
		 *
		 * @var string
		 */
		public $version = '1.8.2.1';

		/**
		 * The name of the plugin.
		 *
		 * @since 1.7.0
		 *
		 * @var string
		 */
		public $plugin_name = 'Envira Gallery';

		/**
		 * Unique plugin slug identifier.
		 *
		 * @since 1.7.0
		 *
		 * @var string
		 */
		public $plugin_slug = 'envira-gallery';

		/**
		 * Plugin file.
		 *
		 * @since 1.7.0
		 *
		 * @var string
		 */
		public $file = __FILE__;

		/**
		 * Primary class constructor.
		 *
		 * @since 1.7.0
		 */
		public function __construct() {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( is_plugin_active( 'envira-albums/envira-albums.php' ) ) {

				// We need to define a constant so Albums can still work
				if ( ! defined( 'ENVIRA_STANDALONE_PLUGIN_NAME' ) ) {

					define( 'ENVIRA_STANDALONE_PLUGIN_NAME', 'Envira Gallery - Standalone Integrated' );

				}

			}

			// Fire a hook before the class is setup.
			do_action( 'envira_gallery_pre_init' );

			// Load the plugin textdomain.
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			// Load the plugin.
			add_action( 'init', array( $this, 'init' ), 0 );

		}

		/**
		 * Setup enviromental constants for Envira.
		 *
		 * @since 1.7.0
		 *
		 * @access public
		 * @return void
		 */
		public function setup_constants(){

			if ( ! defined( 'ENVIRA_VERSION' ) ){

				define( 'ENVIRA_VERSION', $this->version );

			}

			if ( ! defined( 'ENVIRA_SLUG' ) ){

				define( 'ENVIRA_SLUG', $this->plugin_slug );

			}

			if ( ! defined( 'ENVIRA_FILE' ) ){

				define( 'ENVIRA_FILE', $this->file );

			}

			if ( ! defined( 'ENVIRA_DIR' ) ){

				define( 'ENVIRA_DIR', plugin_dir_path( __FILE__ ) );


			}

			if ( ! defined( 'ENVIRA_URL' ) ){

				define( 'ENVIRA_URL', plugin_dir_url( __FILE__ ) );

			}
		}

		/**
		 * Loads the plugin textdomain for translation.
		 *
		 * @since 1.7.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( ENVIRA_FILE ) ) . '/languages/' );

		}

		/**
		 * Loads the plugin into WordPress.
		 *
		 * @since 1.7.0
		 */
		public function init() {

			//Lets check the install.
			$can_run = $this->check_installation();

			//If we cant run lets bail.
			if ( false == $can_run ){

				return;

			}

			//Check if we need to run a update routine.
			$this->maybe_run_update();

			//Load the legacy fallbacks.
			$this->load_legacy();

			//Kick off the frontend
			$public = new Envira\Frontend\Frontend_Container();

			// Load admin only components.
			if ( is_admin() ) {

				$this->require_admin();

				//Kick off the Admin
				$admin = new Envira\Admin\Admin_Container();

				$this->require_updater();

			}

			// Run hook once Envira has been initialized.
			do_action( 'envira_gallery_init' );

			// Add hook for when Envira has loaded.
			do_action( 'envira_gallery_loaded' );

			if ( is_admin() ) {
	            // Retrieve the license key. If it is not set, return early.
	            $key = $this->get_license_key();
	            if ( ! $key ) {
	                return;
	            }

	            // If there are any errors with the key itself, return early.
	            if ( $this->get_license_key_errors() ) {
	                return;
	            }

	            // Fire a hook for Addons to register their updater since we know the key is present.
	            do_action( 'envira_gallery_updater', $key );
	        }

		}

		/**
		 * Display a nag notice if the user still has Lite activated, or they're on PHP < 5.3
		 *
		 * @since 1.3.8.2
		 */
		public function check_installation() {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( class_exists( 'Envira_Gallery_Lite' ) ) {

				add_action( 'admin_notices', array( $this, 'lite_notice' ) );

				// Make sure Envira Lite, if activated is deactivated
				deactivate_plugins( 'envira-gallery-lite/envira-gallery-lite.php' );

				return false;

			}

			// Check if supersize plugin is active
			if ( is_plugin_active( 'envira-supersize/envira-supersize.php' ) ) {
				set_transient( 'envira_supersize_notice', true, 12 * HOUR_IN_SECONDS );
				deactivate_plugins( 'envira-supersize/envira-supersize.php' );
			}

			if ( ! empty( $_REQUEST['close_supersize_notice'] ) ) {
				delete_transient( 'envira_supersize_notice' );
			}

			if ( get_transient( 'envira_supersize_notice' ) ) {
				add_action( 'admin_notices', array( $this, 'supersize_notice' ) );
			}

			// Check if standalone plugin is active
			if ( is_plugin_active( 'envira-standalone/envira-standalone.php' ) ) {
				update_option( 'envira_gallery_standalone_enabled', true );
				set_transient( 'envira_standalone_notice', true, 12 * HOUR_IN_SECONDS );
				deactivate_plugins( 'envira-standalone/envira-standalone.php' );
			}

			if ( ! empty( $_REQUEST['close_standalone_notice'] ) ) {
				delete_transient( 'envira_standalone_notice' );
			}

			if ( get_transient( 'envira_standalone_notice' ) ) {
				add_action( 'admin_notices', array( $this, 'standalone_notice' ) );
			}

			return true;

		}
		public function maybe_run_update(){

			$version = get_option( 'envira_version' );

			if ( version_compare( $version, ENVIRA_VERSION, '<') ){

				envira_flush_all_cache();

				update_option( 'envira_version', ENVIRA_VERSION, 'no' );

			}
		}

		/**
		 * Output a nag notice if the user has supersize activated
		 *
		 * @since 1.5.7.2
		 */
		public function supersize_notice() {

			?>
			<div class="notice notice-error" style="position: relative;padding-right: 38px;">
				<p><?php _e( 'The Supersize addon was detected on your system. All features have been merged directly into Envira Gallery, so it is no longer necessary. It has been deactivated.', 'envira-gallery' ); ?></p>
				<a href="<?php echo add_query_arg( 'close_supersize_notice', 'true' ); ?>"><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'envira-gallery' ); ?></span></button></a>
			</div>
			<?php

		}

		/**
		 * Output a nag notice if the user has standalone activated
		 *
		 * @since 1.5.7.2
		 */
		public function standalone_notice() {

			?>
			<div class="notice notice-error" style="position: relative;padding-right: 38px;">
				<p><?php _e( 'The Standalone addon was detected on your system. All features have been merged directly into Envira Gallery, so it is no longer necessary. It has been deactivated.', 'envira-gallery' ); ?></p>
				<a href="<?php echo add_query_arg( 'close_standalone_notice', 'true' ); ?>"><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></a>
			</div>
			<?php

		}

		/**
		 * Output a nag notice if the user has both Lite and Pro activated
		 *
		 * @since 1.3.8.2
		 */
		public function lite_notice() {

			?>
			<div class="error">
				<p><?php _e( 'Please <a href="plugins.php">deactivate</a> the Envira Lite Plugin. Your premium version of Envira Gallery may not work as expected until the Lite version is deactivated.', 'envira-gallery' ); ?></p>
			</div>
			<?php

		}

		/**
		 * Loads all admin related files into scope.
		 *
		 * @since 1.7.0
		 */
		public function require_admin() {

			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/admin.php' );

		}

		/**
		 * Loads all updater related files and functions into scope.
		 *
		 * @since 1.7.0
		 *
		 * @return null Return early if the license key is not set or there are key errors.
		 */
		public function require_updater() {

			// Retrieve the license key. If it is not set, return early.
			$key = $this->get_license_key();
			if ( ! $key ) {
				return;
			}

			// If there are any errors with the key itself, return early.
			if ( $this->get_license_key_errors() ) {
				return;
			}

			// Go ahead and initialize the updater.
			$args = array(
				'plugin_name' => $this->plugin_name,
				'plugin_slug' => $this->plugin_slug,
				'plugin_path' => plugin_basename( __FILE__ ),
				'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . $this->plugin_slug,
				'remote_url'  => 'https://enviragallery.com/',
				'version'     => $this->version,
				'key'         => $key,
			);

			$updater = new Envira\Utils\Updater( $args );

		}

		public function load_legacy(){

			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-common.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-settings.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-shortcode.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-updater.php' );

			if ( is_admin() ){

				require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-metaboxes.php' );
				require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-import.php' );
				require_once( trailingslashit( ENVIRA_DIR ) . 'src/Legacy/class-envira-gallery-license.php' );

			}

		}

		/**
		 * Loads all global files into scope.
		 *
		 * @since 1.7.0
		 */
		public function require_global() {

			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/ajax.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/gallery.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/image.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/themes.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/cropping.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/common.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/cache.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/utility.php' );
			require_once( trailingslashit( ENVIRA_DIR ) . 'src/Functions/serialization.php' );

		}

		/**
		 * Returns a gallery based on ID.
		 *
		 * @since 1.7.0
		 *
		 * @param int $id     The gallery ID used to retrieve a gallery.
		 * @return array|bool Array of gallery data or false if none found.
		 */
		public function get_gallery( $id ) {

			// Return the gallery data.
			return envira_get_gallery( $id );

		}

		/**
		 * Internal method that returns a gallery based on ID.
		 *
		 * @since 1.7.0
		 *
		 * @param int $id     The gallery ID used to retrieve a gallery.
		 * @return array|bool Array of gallery data or false if none found.
		 */
		public function _get_gallery( $id ) {

			return _envira_get_gallery( $id );

		}

		/**
		 * Returns a gallery based on slug.
		 *
		 * @since 1.7.0
		 *
		 * @param string $slug The gallery slug used to retrieve a gallery.
		 * @return array|bool  Array of gallery data or false if none found.
		 */
		public function get_gallery_by_slug( $slug ) {

			return envira_get_gallery_by_slug( $slug );

		}

		/**
		 * Internal method that returns a gallery based on slug.
		 *
		 * @since 1.7.0
		 *
		 * @param string $slug The gallery slug used to retrieve a gallery.
		 * @return array|bool  Array of gallery data or false if none found.
		 */
		public function _get_gallery_by_slug( $slug ) {

			return _envira_get_gallery_by_slug( $slug );

		}

		/**
		 * Returns all galleries created on the site.
		 *
		 * @since 1.7.0
		 *
		 * @param   bool        $skip_empty         Skip empty sliders.
		 * @param   bool        $ignore_cache       Ignore Transient cache.
		 * @param   string      $search_terms       Search for specified Galleries by Title
		 *
		 * @return array|bool                   Array of gallery data or false if none found.
		 */
		public function get_galleries( $skip_empty = true, $ignore_cache = false, $search_terms = '' ) {

			return envira_get_galleries( $skip_empty, $ignore_cache, $search_terms );

		}

		/**
		 * Internal method that returns all galleries created on the site.
		 *
		 * @since 1.7.0
		 *
		 * @param bool      $skip_empty     Skip Empty Galleries.
		 * @param string    $search_terms   Search for specified Galleries by Title
		 * @return mixed                    Array of gallery data or false if none found.
		 */
		public function _get_galleries( $skip_empty = true, $search_terms = '' ) {

			return _envira_get_galleries( $skip_empty = true, $search_terms = '' );
		}


		/**
		 * Loads the default plugin options.
		 *
		 * @since 1.7.0
		 *
		 * @return array Array of default plugin options.
		 */
		public static function default_options() {

			$ret = array(
				'key'         => '',
				'type'        => '',
				'is_expired'  => false,
				'is_disabled' => false,
				'is_invalid'  => false,
			);

			return apply_filters( 'envira_gallery_default_options', $ret );

		}

		/**
		 * replaces mb_substr
		 *
		 * @access public
		 * @static
		 * @param mixed $class
		 * @return void
		 */
        public static function envira_mb_substr( $string, $offset, $length ) {

            $arr = preg_split( '//u', $string );
            $slice = array_slice( $arr, $offset + 1, $length );
            return implode( '', $slice) ;

        }

		/**
		 * autoload function.
		 *
		 * @access public
		 * @static
		 * @param mixed $class
		 * @return void
		 */
		public static function autoload( $class ){

			// Prepare variables.
			$prefix  = 'Envira';
			$baseDir = __DIR__ . '/src/';
			$length  = mb_strlen( $prefix );


			// If the class is not using the namespace prefix, return.
			if ( 0 !== strncmp( $prefix, $class, $length ) ) {
				return;
			}

			// Prepare classes to be autoloaded.
			$relativeClass = Envira_Gallery::envira_mb_substr( $class, 0, strlen($class) );
			$relativeClass = str_replace('Envira\\', '', $relativeClass);
			$file          = $baseDir . str_replace( '\\', '/', $relativeClass ) . '.php';

			// If the file exists, load it.
			if ( file_exists( $file ) ) {
				require $file;
			}

		}


		/**
		 * Fired when the plugin is activated.
		 *
		 * @since 1.7.0
		 *
		 * @global int $wp_version      The version of WordPress for this install.
		 * @global object $wpdb         The WordPress database object.
		 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
		 */
		public static function activate( $network_wide ){

			global $wp_version;

			if ( version_compare( $wp_version, '4.4.0', '<' ) && ! defined( 'ENVIRA_FORCE_ACTIVATION' ) ) {

				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die( sprintf( __( 'Sorry, but your version of WordPress does not meet Envira Gallery\'s required version of <strong>4.0.0</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>.', 'envira-gallery' ), get_admin_url() ) );
			}

			if ( is_multisite() && $network_wide ) {
				$site_list = wp_get_sites();
				foreach ( (array) $site_list as $site ) {
					switch_to_blog( $site['blog_id'] );

					// Set default license option.
					$option = get_option( 'envira_gallery' );
					if ( ! $option || empty( $option ) ) {
						update_option( 'envira_gallery', Envira_Gallery::default_options() );
					}

					restore_current_blog();
				}
			} else {
				// Set default license option.
				$option = get_option( 'envira_gallery' );
				if ( ! $option || empty( $option ) ) {
					update_option( 'envira_gallery', Envira_Gallery::default_options() );
				}
			}

		}

		/**
		 * Fired when the plugin is deactivated to clear flushed permalinks flag and flush the permalinks.
		 *
		 * @since 1.5.7.2
		 *
		 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
		 */
		public static function deactivate( $network_wide ){
			// Flush rewrite rules
			flush_rewrite_rules();
			envira_flush_all_cache();

			// Set flag = false in options
			update_option( 'envira-standalone-flushed', false );

		}

		/**
		 * Fired when the plugin is uninstalled.
		 *
		 * @since 1.7.0
		 *
		 * @global object $wpdb The WordPress database object.
		 */
		function uninstall( $network_wide ){

			if ( is_multisite() ) {
				$site_list = wp_get_sites();
				foreach ( (array) $site_list as $site ) {
					switch_to_blog( $site['blog_id'] );
					delete_option( 'envira_gallery' );
					restore_current_blog();
				}
			} else {
				delete_option( 'envira_gallery' );
			}

		}

		/**
		 * Returns the singleton instance of the class.
		 *
		 * @since 1.7.0
		 *
		 * @return object The Envira_Gallery object.
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery ) ) {

				self::$instance = new self();
				self::$instance->setup_constants();
				self::$instance->require_global();

			}

			return self::$instance;

		}

		///DEPRICATED
		/**
		 * Returns the license key for Envira.
		 *
		 * @since 1.7.0
		 *
		 * @return string $key The user's license key for Envira.
		 */
		public function get_license_key() {

			return envira_get_license_key();

		}

		/**
		 * Returns the license key type for Envira.
		 *
		 * @since 1.7.0
		 *
		 * @return string $type The user's license key type for Envira.
		 */
		public function get_license_key_type() {

			return envira_get_license_key_type();

		}

		/**
		 * Returns possible license key error flag.
		 *
		 * @since 1.7.0
		 *
		 * @return bool True if there are license key errors, false otherwise.
		 */
		public function get_license_key_errors() {

			$option = get_option( 'envira_gallery' );
			return isset( $option['is_expired'] ) && $option['is_expired'] || isset( $option['is_disabled'] ) && $option['is_disabled'] || isset( $option['is_invalid'] ) && $option['is_invalid'];

		}

		/**
		 * Returns the number of images in a gallery.
		 *
		 * @since 1.7.0
		 *
		 * @param int $id The gallery ID used to retrieve a gallery.
		 * @return int    The number of images in the gallery.
		 */
		public function get_gallery_image_count( $id ) {

			return envira_get_gallery_image_count( $id );

		}

	}

	spl_autoload_register( 'Envira_Gallery::autoload' );

	/**
	 * envira_gallery_init function.
	 *
	 * @access public
	 * @return object
	 * @since 1.7.0
	 */
	function envira(){

		return Envira_Gallery::get_instance();

	}

	//Getting things started
	envira();

endif;