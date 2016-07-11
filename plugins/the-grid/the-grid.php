<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 *
 * @wordpress-plugin
 * Plugin Name:  The Grid
 * Plugin URI:   http://www.theme-one.com/the-grid/
 * Description:  The Grid - Create advanced grids for any post type with endless possibilities (no programming knowledge required)
 * Version:      1.6.0
 * Author:       Themeone
 * Author URI:   http://www.theme-one.com/
 * Text Domain:  tg-text-domain
 * Domain Path:  /langs
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

// Initialize if The Grid Plugin does not exist
if (!class_exists('The_Grid_Plugin')) {

	class The_Grid_Plugin {
		
		/**
		* Plugin Version
		*
		* @since 1.0.0
		* @access public
		*
		* @var string
		*/
		public $plugin_version = '1.6.0';
		
		/**
		* Plugin Prefix
		*
		* @since 1.0.0
		* @access public
		*
		* @var string
		*/
		public $plugin_prefix = 'the_grid_';
		
		/**
		* Plugin Slug
		*
		* @since 1.0.0
		* @access public
		*
		* @var string
		*/
		public $plugin_slug = 'the_grid';	
		
		/**
		* Cloning disabled
		* @since 1.0.0
		*/
		private function __clone() {
		}
	
		/**
		* Serialization disabled
		* @since 1.0.0
		*/
		private function __sleep() {
		}
	
		/**
		* De-serialization disabled
		* @since 1.0.0
		*/
		private function __wakeup() {
		}
	
		/**
	 	* The Grid Constructor
		* @since 1.0.0
	 	*/
		public function __construct() {
			
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
			
		}

		/**
		* Define The Grid Constants
		* @since 1.0.0
		*/
		public function define_constants() {
			
			define('TG_PLUGIN', __FILE__ );
			define('TG_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
			define('TG_PLUGIN_URL', str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
			define('TG_VERSION', $this->plugin_version);
			define('TG_SLUG',$this->plugin_slug);
			define('TG_PREFIX', $this->plugin_prefix);
			
			// for themeone metabox framework (TOMB)
			if (!defined('TOMB_DIR')) {
				define('TOMB_DIR', TG_PLUGIN_PATH . 'includes/metabox/');
			}
			if (!defined('TOMB_URL')) {
				define('TOMB_URL', TG_PLUGIN_URL . 'includes/metabox/');
			}
			
		}
		
		/**
		* Include required core files for Backend/Frontend.
		* @since 1.0.0
		*/
		public function includes() {

			// Aqua Resizer Class
			require_once(TG_PLUGIN_PATH . '/includes/aqua-resizer.class.php');
			// Attachment taxonomy
			require_once(TG_PLUGIN_PATH . '/includes/media-taxonomies.php');
			// Grid base Class (main functionnalities)
			require_once(TG_PLUGIN_PATH . '/includes/the-grid-base.class.php');
			// Grid custom table Class
			require_once(TG_PLUGIN_PATH . '/includes/custom-table.class.php');
			// Load skins classes
			require_once(TG_PLUGIN_PATH . '/includes/item-skin.class.php');
			require_once(TG_PLUGIN_PATH . '/includes/preloader-skin.class.php');
			require_once(TG_PLUGIN_PATH . '/includes/navigation-skin.class.php');
			require_once(TG_PLUGIN_PATH . '/includes/item-animation.class.php');
			// post like class
			require_once(TG_PLUGIN_PATH . '/includes/post-like/post-like.php');
			// deprecated class to retrieve item element
			require_once(TG_PLUGIN_PATH . '/includes/deprecated/the-grid-element.class.php');
			// Load frontend classes
			require_once(TG_PLUGIN_PATH . '/frontend/the-grid-init.class.php');
			require_once(TG_PLUGIN_PATH . '/frontend/the-grid-item.class.php');
			require_once(TG_PLUGIN_PATH . '/includes/first-media.class.php');
			// Load backend classes
			if (is_admin()) {
				require_once(TG_PLUGIN_PATH . '/includes/element-animation.class.php');
				require_once(TG_PLUGIN_PATH . '/includes/envato-api.class.php');
				require_once(TG_PLUGIN_PATH . '/includes/update-plugin.class.php');
				require_once(TG_PLUGIN_PATH . '/includes/custom-fields.class.php');
				require_once(TG_PLUGIN_PATH . '/backend/admin-init.php');
				require_once(TG_PLUGIN_PATH . '/includes/wpml.class.php');	
			}
			// Register shortcode & add Tinymce button/popup & add Visual Composer element
			require_once(TG_PLUGIN_PATH . '/backend/admin-shortcode.php');

		}
		
		/**
		* Hook into actions and filters
		* @since 1.0.0
		* @modified 1.3.0
		*/
		public function init_hooks() {

			// Load plugin text domain
			add_action( 'plugins_loaded', array( &$this, 'localize_plugin' ) );
			// Register The Grid post type
			add_action( 'init', array( &$this, 'register_post_type' ) );
			// Add post format for any kind of post type
			add_action( 'current_screen', array( &$this, 'post_formats' ) );
			// Register The Grid additionnal image sizes
			add_action( 'after_setup_theme', array( &$this, 'add_image_size' ) );
			// Add plugin edit button in plugin list page
			add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), array( &$this, 'action_links'), 10, 4 );
			// Make changes after important update on plugin activation
			register_activation_hook( __FILE__, array( &$this, 'plugin_activated' ) );
			// Create custom table on plugin activation
			register_activation_hook( __FILE__, array( 'The_Grid_Custom_Table', 'create_tables' ) );
			// Remove uncessary data on plugin deactivation
			register_deactivation_hook( __FILE__, array( &$this, 'plugin_deactivated' ) );
			
		}
		
		/**
		* Localize_plugin
		* @since 1.0.0
		*/
		public function localize_plugin() {
			
			load_plugin_textdomain(
				'tg-text-domain',
				false,
				plugin_basename( dirname( __FILE__ ) ) . '/langs'
			);
			
		}
		
		/**
		* Register post type
		* @since 1.0.0
		* @modified 1.5.0
		*/
		public function register_post_type() {	
			
			// Set labels for The_Grid post type
			$labels = array(
				'name'          => __( 'The Grid', 'taxonomy general name', 'tg-text-domain'),
				'singular_name' => __( 'The_Grid', 'tg-text-domain'),
				'search_items'  => __( 'Search The_Grid', 'tg-text-domain'),
				'all_items'     => __( 'All The_Grid', 'tg-text-domain'),
				'parent_item'   => __( 'Parent The_Grid', 'tg-text-domain'),
				'edit_item'     => false,
				'update_item'   => false,
				'add_new_item'  => false,
				'menu_name'     => __( 'The Grid', 'tg-text-domain')
			 );
			 
			 // Set main arguments for The_Grid post type 
			 $args = array(
					'labels'          => $labels,
					'singular_label'  => __('The Grid', 'tg-text-domain'),
					'public'          => false,
					'capability_type' => 'post',
					'query_var'       => false,
					'rewrite'         => false,
					'show_ui'         => false,
					'show_in_menu'    => false,
					'hierarchical'    => false,
					'menu_position'   => 10,
					'menu_icon'       => 'dashicons-slides',
					'supports'        => false,
					'rewrite'         => array(
						'slug' => $this->plugin_slug,
						'with_front' => false
					),
			);
			
			// register The_Grid post type
			register_post_type( $this->plugin_slug, $args );
			// remove unecessary post type field
			remove_post_type_support( $this->plugin_slug, 'title' );
			remove_post_type_support( $this->plugin_slug, 'editor' );
			
		}
		
		/**
		* Add post formats to any post types
		* @since 1.0.5
		*/
		public function post_formats() {
			
			$post_format = get_option('the_grid_post_formats', false);
			// add post formats support if option enable in global settings
			if ($post_format == true) {
				// post formats supported by The Grid Plugin
				add_theme_support('post-formats', array('gallery', 'video', 'audio', 'quote', 'link'));
				// retireve all post types
				$post_types = The_Grid_Base::get_all_post_types();
				// remove post format for attacment post type
				unset($post_types['attachment']);
				foreach ($post_types as $slug => $name) {
					add_post_type_support($slug, 'post-formats');
				}
			}
			
		}
		
		/**
		* Add image sizes to Wordpress
		* @since 1.0.0
		* @modified 1.0.7
		*/
		public function add_image_size() {
			
			// default image sizes
			$def = array(
				'w' => array(500, 500, 1000, 1000, 500),
				'h' => array(500, 1000, 500, 1000, 99999),
				'c' => array(true, true, true, true, '')
			);
			
			// add image sizes with values from global settings
			for ($i = 0; $i <= 4; $i++) {
				$w = get_option('the_grid_size'. ($i+1) .'_width', $def['w'][$i]);
				$h = get_option('the_grid_size'. ($i+1) .'_height', $def['h'][$i]);
				$c = get_option('the_grid_size'. ($i+1) .'_crop', $def['c'][$i]);
				add_image_size('the_grid_size'. ($i+1), $w, $h, $c);
			}
			
		}

		/**
		* Add edit link on plugin activation
		* @since 1.0.0
		* @modified 1.1.0
		*/
		public function action_links($links) {
			
			unset($links['edit']);
			$mylinks = array(
 				'<a href="' . admin_url( 'admin.php?page=the_grid' ) . '">'. __('Edit', 'tg-text-domain') .'</a>',
 			);
			return array_merge($links, $mylinks);
			
		}

		/**
		* Make changes after important update on plugin activation
		* @since 1.2.0
		*/
		public function plugin_activated() {
			
			// delete The Grid cache to prevent any issues due to changes
			$base = new The_Grid_Base();
			$base->delete_transient('tg_grid');
			
		}
		
		/**
		* Make changes after important update on plugin activation
		* @since 1.2.0
		*/
		public function plugin_deactivated() {
			
			// delete The Grid cache to prevent any issues due to changes
			$base = new The_Grid_Base();
			$base->delete_transient('tg_grid');
			
		}

	}
	
	// Initialize The Grid Plugin
	new The_Grid_Plugin;

}