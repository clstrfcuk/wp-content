<?php


/*
 * To be called from on delete action
 */
function wppp_delete_original_files($image_id ) {

	wppp_log_debug('Started',$image_id,'','','delete-origianls');

	if ( wp_attachment_is_image( $image_id ) ) {

		$meta = wp_get_attachment_metadata( $image_id );

		wppp_log_trace(
			'File is image, meta: ' . print_r( $meta, true ),
			$image_id,'','','delete-origianls');

		$time = substr( $meta['file'], 0, 7 ); // Extract the date in form "2015/04"
		$upload_dir = wp_upload_dir( $time );

		$sizes = wppp_get_image_sizes();

		$original_filename = $meta['file'];

		$exploded_filepath = explode( ".", $original_filename );
		$original_file_extension = end( $exploded_filepath );
		$original_file_name =
			str_replace( ( "." . $original_file_extension ), '', $original_filename );
		$original_file_name =
			str_replace( ( $time . "/" ), '', $original_file_name );
		$original_filename = $original_file_name . '.' . $original_file_extension;

		wppp_log_trace(
			'original_filename = ' . $original_filename,
			$image_id, $original_filename, 'full', 'delete-origianls');

		$uncompressed_filename = wppp_get_uncompressed_filename( $original_filename );
		wppp_log_trace(
			'uncompressed_filename = ' . $uncompressed_filename,
			$image_id, $original_filename, 'full', 'delete-origianls'
			);

		if ( file_exists( $upload_dir['path'] . '/' . $uncompressed_filename ) ) {

			wppp_log_debug(
				'deletting file - ' .
				$upload_dir['path'] . '/' . $uncompressed_filename,
				$image_id, $original_filename, 'full', 'delete-origianls');
			unlink( $upload_dir['path'] . '/' . $uncompressed_filename );

		} else {
			wppp_log_debug(
				'uncompressed_filename - file does not exist: ' .
				$upload_dir['path'] . '/' . $uncompressed_filename,
				$image_id,
                $original_filename, // file_name
				'full', // file_size
				'delete-origianls' // step
				);

		}
	}
}


/*
 * Adds link to plugins page. To be called on init
 */
function wppp_plugin_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ) . '">' . 'Settings' . '</a>'
	), $links );
	return $links;
}



/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the wp pixpie plugin, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Pixpie_Plugin
 * @subpackage WP_Pixpie_Plugin/admin
 * @author     Your Name <email@example.com>
 */
class WP_Pixpie_Plugin_Admin {

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
	 * @param      string    $wp_pixpie_plugin       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_pixpie_plugin, $version ) {

		$this->wp_pixpie_plugin = $wp_pixpie_plugin;
		$this->version = $version;

	}



	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->wp_pixpie_plugin, plugin_dir_url( __FILE__ ) . 'css/wp-pixpie-plugin-admin.css?v11', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->wp_pixpie_plugin, plugin_dir_url( __FILE__ ) . 'js/wp-pixpie-plugin-admin.js?v4', array( 'jquery' ), $this->version, false );

	}

	public function menu_wp_pixpie_plugin () {

	    add_menu_page(
	        'WP Pixpie Plugin',
	        'WP Pixpie Plugin',
	        8,
	        WPPP_PLUGIN_ID,
	        'render_wp_pixpie_plugin_dashboard_page',
	        'dashicons-format-image'
	    );

	    add_submenu_page(
	        WPPP_PLUGIN_ID,
	        'WP Pixpie Plugin - Settings',
	        'Settings',
	        8,
	        WPPP_PLUGIN_PAGE_ID_SETTINGS,
	        'render_wp_pixpie_plugin_settings_page'
	        );

	    add_submenu_page(
	        WPPP_PLUGIN_ID,
	        'WP Pixpie Plugin - View All Images',
	        'View All Images',
	        8,
	        WPPP_PLUGIN_ID . '-view-all',
	        'render_wp_pixpie_plugin_view_all_page'
	        );

	    add_submenu_page(
	        WPPP_PLUGIN_ID,
	        'WP Pixpie Plugin - Convert All Images',
	        'Convert All Images',
	        8,
	        WPPP_PLUGIN_PAGE_ID_CONVERT_ALL,
	        'render_wp_pixpie_plugin_convert_all_page'
	        );

	    add_submenu_page(
	        WPPP_PLUGIN_ID,
	        'WP Pixpie Plugin - Revert All Images',
	        'Revert All Images',
	        8,
	        WPPP_PLUGIN_PAGE_ID_REVERT_ALL,
	        'render_wp_pixpie_plugin_revert_all_page'
	        );

	    add_submenu_page(
	        WPPP_PLUGIN_ID,
	        'WP Pixpie Plugin - View Log',
	        'View Log',
	        8,
	        WPPP_PLUGIN_PAGE_ID_VIEW_LOG,
	        'render_wp_pixpie_plugin_view_log_page'
	        );

        add_submenu_page(
            null, // hide from menu
            'WP Pixpie Plugin - Sign Up',
            'Sign Up',
            8,
            WPPP_PLUGIN_PAGE_ID_SIGN_UP,
            'render_wp_pixpie_plugin_sign_up_page'
        );



        function render_wp_pixpie_plugin_dashboard_page() {
			include plugin_dir_path( __FILE__ ) . 'dashboard.php';
		};

		function render_wp_pixpie_plugin_settings_page() {
			include plugin_dir_path( __FILE__ ) . 'settings.php';
		};

		function render_wp_pixpie_plugin_convert_all_page() {
			include plugin_dir_path( __FILE__ ) . 'convert_all.php';
		};

		function render_wp_pixpie_plugin_revert_all_page() {
			include plugin_dir_path( __FILE__ ) . 'revert_all.php';
		};

		function render_wp_pixpie_plugin_view_all_page() {
			include plugin_dir_path( __FILE__ ) . 'view_all.php';
		};

		function render_wp_pixpie_plugin_view_log_page() {
			include plugin_dir_path( __FILE__ ) . 'view_log.php';
		};

		function render_wp_pixpie_plugin_sign_up_page() {
			include plugin_dir_path( __FILE__ ) . 'sign_up.php';
		};

	}





	public function wppp_set_admin_notifications() {

		if ( ! wppp_is_plugin_activated() ) {
			  add_action( 'admin_notices', 'wppp_plugin_not_configured_admin_notice' );
		}

		function wppp_plugin_not_configured_admin_notice() {
			if( current_user_can( 'administrator' ) ) {

		    ?>

		    <div class="notice error plugin_not_configured_admin_notice" >
		        <p>WP Pixpie Plugin is not configured,
		        <a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ); ?>">
				fix it
		        </a></p>
		    </div>

		    <?php

			}
		}


	}


	public function wppp_on_delete_image($post_id ) {
		if ( wppp_exists_image( $post_id ) ) {
			wppp_log_trace(
				'Image exists in converted - ' . $post_id,
				0,'','','delete-hook'
				);
			wppp_unlist_image( $post_id );
			wppp_log_trace(
				'Image deleted from converted - ' . $post_id,
				0,'','','delete-hook');

			wppp_log_trace(
				'Deleting origianl files',
				0,'','','delete-hook'	);

            wppp_delete_original_files( $post_id );
		}
		wppp_log_debug(
			'An image was deleted - ' . $post_id,
            0,'','','delete-hook'
			);
	}


	public function wppp_check_versions() {

		$versions_ok = true;

		if ( version_compare( PHP_VERSION, WPPP_REQUIRED_PHP_VERSION ) < 0 ) {
			$versions_ok = false;
			add_action( 'admin_notices', 'wppp_php_incompartible_admin_notice' );
		}

		global $wp_version;
		if( $wp_version < WPPP_REQUIRED_WP_VERSION ) {
			$versions_ok = false;
			add_action( 'admin_notices', 'wppp_wp_incompartible_admin_notice' );
		}

		if ( ! $versions_ok ) {
			update_option( WPPP_OPTION_NAME_STATUS, 'inactive' );
		}

		function wppp_php_incompartible_admin_notice() {
			if( current_user_can( 'administrator' ) ) {
		    ?>

		    <div class="notice error wp_pixpie_plugin_php_incompartible_admin_notice" >
		        <p>WP Pixpie Plugin is compatible with PHP versions that are higher than <b><?php echo WPPP_REQUIRED_PHP_VERSION; ?></b>. Please, update your PHP.</p>
		    </div>

		    <?php
			}
		}

		function wppp_wp_incompartible_admin_notice() {
			if( current_user_can( 'administrator' ) ) {

		    ?>

		    <div class="notice error wp_pixpie_plugin_wp_incompartible_admin_notice" >
		        <p>WP Pixpie Plugin is compatible with PHP versions that are higher than <b><?php echo WPPP_REQUIRED_WP_VERSION; ?></b>. Please, update your Wordpress.</p>
		    </div>

		    <?php
			}
		}

	}


    public function wppp_on_submit_sign_up_form() {
        wppp_log_trace(
            'started',
            0, '', '', 'sign-up-page'
        );
        if(
            isset ( $_POST['email'] ) &&
            isset ( $_POST['password'] )
        )
        {
            wppp_log_trace(
                'post params set up',
                0, '', '', 'sign-up-page'
            );
            if ( isset( $_POST['_wpnonce'] ) && ( wp_verify_nonce($_POST['_wpnonce'], 'sign_up_form') ) ) {
                wppp_log_trace(
                    'nonce OK',
                    0, '', '', 'sign-up-page'
                );
                $sanitized_email = sanitize_text_field( $_POST['email'] );
                $sanitized_password = sanitize_text_field( $_POST['password'] );
                wppp_process_sign_up_form( $sanitized_email, $sanitized_password );
            } else {
                wppp_log_error(
                    'nonce error',
                    0, '', '', 'sign-up-page'
                );
            }
        } else {
            wppp_log_warning(
                'post params empty',
                0, '', '', 'sign-up-page'
            );
        }
        // in case of any errors
        wp_safe_redirect( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS );
        exit();
    }



}
