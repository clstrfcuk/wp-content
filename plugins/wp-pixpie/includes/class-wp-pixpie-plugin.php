<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://pixpie.co
 * @since      0.0.1
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 */


define ( 'WPPP_PLUGIN_ID', 'wp-pixpie-plugin' );
define ( 'WPPP_PLUGIN_NAME', 'WP Pixpie Plugin' );

define ( 'WPPP_PLUGIN_PAGE_ID_CONVERT_ALL', WPPP_PLUGIN_ID . '-convert-all' );
define ( 'WPPP_PLUGIN_PAGE_ID_SETTINGS', WPPP_PLUGIN_ID . '_settings' );
define ( 'WPPP_PLUGIN_PAGE_ID_REVERT_ALL', WPPP_PLUGIN_ID . '-revert-all' );
define ( 'WPPP_PLUGIN_PAGE_ID_VIEW_LOG', WPPP_PLUGIN_ID . '-view-log' );
define ( 'WPPP_PLUGIN_PAGE_ID_SIGN_UP', WPPP_PLUGIN_ID . '-sign-up' );
define ( 'WPPP_PLUGIN_PAGE_ID_INVOICE', WPPP_PLUGIN_ID . '-invoice' );

# visible options
define ( 'WPPP_OPTION_NAME_BUNDLE_ID', 'wppp_option_bundle_id' );
define ( 'WPPP_OPTION_NAME_SECRET_KEY', 'wppp_option_secret_key' );
define ( 'WPPP_OPTION_NAME_KEEP_ORIGINAL', 'wppp_option_keep_original' );

# imgs_size visible options
define ( 'WPPP_OPTION_IMGS_SIZE', 'wppp_option_imgs_size' );

# invisible options
define ( 'WPPP_OPTION_NAME_STATUS', 'wppp_option_status' );

define ( 'WPPP_API_AUTH_SALT', 'yuuRiesahs3niet7thac' );
define( 'WPPP_API_URL', 'https://api.pixpie.co/' );
define ( 'WPPP_API_CONVERT_IMAGE_PREFIX', 'images/convert/' ); // for POST compression

define ( 'WPPP_SUPPORT_EMAIL', 'support@pixpie.co' );

define ( 'SIZE_UNCOMP', '_uncomp' );
define ( 'FILENAME_UNCOMP', '__uncomp__' );
define ( 'FILENAME_TMP', '__tmp__' );

define ( 'WPPP_REQUIRED_PHP_VERSION', '5.5.0' );
define ( 'WPPP_REQUIRED_WP_VERSION', 4.4 );

define ( 'WPPP_VERSION', '1.2.0' );

define ( 'WPPP_LOG_TABLE_NAME', 'wppp_log' );
define ( 'WPPP_IMAGES_TABLE_NAME', 'wppp_converted_images' );
define ( 'WPPP_CONVERT_ALL_TABLE_NAME', 'wppp_convert_all' );

define ( 'WPPP_SELECT_ALL_IMAGES_PAGE_SIZE', 1000 );

define( 'WPPP_SAFE_REDIRECT_CLOUD_HOST', 'cloud.pixpie.co/');


/*
Sizes should be added first and be available for other parts of code
*/
add_action ( 'init', 'wppp_register_new_sizes' );


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/includes
 * @author     Your Name <email@example.com>
 */
class WP_Pixpie_Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Pixpie_Plugin_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $wp_pixpie_plugin The string used to uniquely identify this plugin.
	 */
	protected $wp_pixpie_plugin;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the wp pixpie plugin and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct () {

		$this -> wp_pixpie_plugin = 'wp-pixpie-plugin';
		$this -> version = '1.0.0';

		$this -> load_dependencies ();
		$this -> set_locale ();
		$this -> define_admin_hooks ();
		$this -> define_public_hooks ();

	}


	/*
	 * Hide all uncompressed image sizes so they cannot be chosen for srcset
	 */

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wp_pixpie_plugin () {
		return $this -> wp_pixpie_plugin;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version () {
		return $this -> version;
	}

	function wppp_filter_wp_calculate_image_srcset_meta ( $image_meta, $size_array, $image_src, $attachment_id ) {
		return $image_meta;
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run () {
		$this -> loader -> run ();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Pixpie_Plugin_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader () {
		return $this -> loader;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Pixpie_Plugin_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Pixpie_Plugin_i18n. Defines internationalization functionality.
	 * - WP_Pixpie_Plugin_Admin. Defines all hooks for the admin area.
	 * - WP_Pixpie_Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {



		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-pixpie-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-pixpie-plugin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-pixpie-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-pixpie-plugin-public.php';

		$this->loader = new WP_Pixpie_Plugin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Pixpie_Plugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale () {

		$plugin_i18n = new WP_Pixpie_Plugin_i18n();

		$this -> loader -> add_action ( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks () {

		$plugin_admin = new WP_Pixpie_Plugin_Admin( $this -> get_wp_pixpie_plugin (), $this -> get_version () );

		$this -> loader -> add_action ( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this -> loader -> add_action ( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this -> loader -> add_action ( 'admin_menu', $plugin_admin, 'menu_wp_pixpie_plugin' );

		$this -> loader -> add_action ( 'init', $plugin_admin, 'wppp_check_versions' );

		$this -> loader -> add_action ( 'delete_attachment', $plugin_admin, 'wppp_on_delete_image' );

		add_action ( 'admin_init', 'wppp_print_logs_csv' );

		$plugin_admin -> wppp_set_admin_notifications ();

		add_filter ( 'allowed_redirect_hosts', 'wppp_allowed_redirect_hosts', 10 );

		$this -> loader -> add_action (
			'admin_post_submit-sign-up-form', $plugin_admin, 'wppp_on_submit_sign_up_form' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks () {

		$plugin_public = new WP_Pixpie_Plugin_Public( $this -> get_wp_pixpie_plugin (), $this -> get_version () );

		$this -> loader -> add_action ( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this -> loader -> add_action ( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_filter ( 'wp_generate_attachment_metadata', 'wppp_generate_compressed_images' );

		add_filter ( 'wp_calculate_image_srcset_meta', array( $this, 'wppp_filter_wp_calculate_image_srcset_meta' ), 1, 4 );

	}


}
