<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the wp pixpie plugin, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/public
 * @author     Your Name <email@example.com>
 */
class WP_Pixpie_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_pixpie_plugin    The ID of this plugin.
	 */
	private $wp_pixpie_plugin;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wp_pixpie_plugin       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_pixpie_plugin, $version ) {

		$this->wp_pixpie_plugin = $wp_pixpie_plugin;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Pixpie_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Pixpie_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wp_pixpie_plugin, plugin_dir_url( __FILE__ ) . 'css/wp-pixpie-plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Pixpie_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Pixpie_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wp_pixpie_plugin, plugin_dir_url( __FILE__ ) . 'js/wp-pixpie-plugin-public.js', array( 'jquery' ), $this->version, false );

	}

}
