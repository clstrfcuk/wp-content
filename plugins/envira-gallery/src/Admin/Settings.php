<?php
/**
 * Settings class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author	Envira Gallery Team
 */
 
namespace Envira\Admin;

use Envira\Utils\Browser;

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	/**
	 * Holds the submenu pagehook.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $hook;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// Add custom settings submenu.
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 11 );

		// Add callbacks for settings tabs.
		add_action( 'envira_gallery_tab_settings_general', array( $this, 'settings_general_tab' ) );
		add_action( 'envira_gallery_tab_settings_standalone', array( $this, 'settings_standalone_tab' ) );
		add_action( 'envira_gallery_tab_settings_debug', array( $this, 'settings_debug_tab' ) );

		// Add the settings menu item to the Plugins table.
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'envira-gallery.php' ), array( $this, 'settings_link' ) );

		// Add items for debug
		add_action( 'envira_gallery_debug_screen_output', array( $this, 'debug_screen_output' ) );
		add_action( 'wp_ajax_download_system_info', array( $this, 'debug_download_info' ) );


	}

	/**
	 * Register the Settings submenu item for Envira.
	 *
	 * @since 1.7.0
	 */
	public function admin_menu() {

		// Register the submenu.
		$this->hook = add_submenu_page(
			'edit.php?post_type=envira',
			__( apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' Gallery Settings', 'envira-gallery' ),
			__( 'Settings', 'envira-gallery' ),
			apply_filters( 'envira_gallery_menu_cap', 'manage_options' ),
			ENVIRA_SLUG . '-settings',
			array( &$this, 'settings_page' )
		);

		// If successful, load admin assets only on that page and check for addons refresh.
		if ( ! $this->hook ) {
			return;
		}
		
		//Add all of our settings hooks
		add_action( 'load-' . $this->hook, array( $this, 'maybe_fix_migration' ) );
		add_action( 'load-' . $this->hook, array( $this, 'update_image_settings' ) );
		add_action( 'load-' . $this->hook, array( $this, 'standalone_settings_save' ) );
		add_action( 'load-' . $this->hook, array( $this, 'enqueue_admin_settings_styles' ) );
	
		//Add admin Scripts and Styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	
	}

	/**
	 * Saves images Settings:
	 * - Add New Images
	 * - Delete Images on Gallery Deletion
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if not fixing the broken migration
	 */
	public function update_image_settings() {

		// Check if user pressed the 'Update' button and nonce is valid
		if ( ! isset( $_POST['envira-gallery-settings-submit'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['envira-gallery-settings-nonce'], 'envira-gallery-settings-nonce' ) ) {
			return;
		}

		// Update settings
		envira_update_setting( 'media_position', $_POST['envira_media_position'] );
		envira_update_setting( 'image_delete', $_POST['envira_image_delete'] );
		envira_update_setting( 'media_delete', $_POST['envira_media_delete'] );

		// Output an admin notice so the user knows what happened
		add_action( 'envira_gallery_settings_general_tab_notice', array( $this, 'updated_settings' ) );

	}
	
	/**
	 * standalone_settings_save function.
	 * 
	 * @since 1.7.0
	 *
	 * @access public
	 * @return void
	 */
	public function standalone_settings_save() {
		
		// Check we saved some settings
		if ( !isset($_POST) ) {
			return;
		}

		// Check nonce exists
		if ( !isset($_POST['envira-standalone-nonce']) ) {
			return;
		}

		// Check nonce is valid
		if ( !wp_verify_nonce( $_POST['envira-standalone-nonce'], 'envira-standalone-nonce' ) ) {
			add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_nonce_notice' ) );
			return;
		}

		envira_update_setting( 'standalone_enabled', empty( $_POST['envira-standalone-enable'] ) ? 0 : 1 );

		// Get reserved slugs
		$slugs = envira_standalone_get_reserved_slugs();

		// Determine which slug(s) to check - include albums if the Albums addon is enabled
		$slugsToCheck = array(
			'gallery',
		);
		if ( isset ( $_POST['envira-albums-slug'] ) ) {
			$slugsToCheck[] = 'albums';
		}

		// Go through each slug
		foreach ( $slugsToCheck as $slug ) {

			// Check slug is valid
			if ( empty( $_POST['envira-' . $slug . '-slug']) ) {
				add_action( 'envira_gallery_settings_standalone_tab_notice', 'envira_standalone_settings_slug_notice' );
				return;
			}
			if ( !preg_match("/^[a-zA-Z0-9_\-]+$/", $_POST['envira-' . $slug . '-slug'] ) ) {
				add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_slug_notice' ) );
				return;
			}
			if ( $slug != 'albums' && ( strtolower( $_POST['envira-albums-slug'] ) == strtolower( $_POST['envira-' . $slug . '-slug'] ) ) ) {
				add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_unique_slugs' ) );
				return;
			}

			// Check slug is not reserved
			if ( !is_array($slugs) ) {
				add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_slug_notice' ) );
				return;
			}

			if ( in_array( $_POST['envira-' . $slug . '-slug'], $slugs) ) {
				add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_slug_notice' ) );
				return;
			}

			// If we reach this point, the slugs are good to use
			update_option( 'envira-' . $slug . '-slug', $_POST['envira-' . $slug . '-slug'] );

		}

		// Set envira-standalone-flushed = false, so on the next page load, rewrite
		// rules are flushed to prevent 404s
		update_option( 'envira-standalone-flushed', false );

		// Output success notice
		add_action( 'envira_gallery_settings_standalone_tab_notice', array( $this, 'standalone_settings_saved_notice' ) );
	}

	/**
	 * Outputs a message to tell the user that the nonce field is invalid
	 *
	 * @since 1.5.7.3
	 */
	public function standalone_settings_nonce_notice() {

		?>
		<div class="notice error below-h2">
			<p><?php echo ( __( 'The nonce field is invalid.', 'envira-standalone' ) ); ?></p>
		</div>
		<?php

	}

	/**
	 * Outputs a message to tell the user that the slug has been saved
	 *
	 * @since 1.5.7.3
	 */
	public function standalone_settings_saved_notice() {

		?>
		<div class="notice updated below-h2">
			<p><?php echo ( __( 'Slug updated successfully!', 'envira-standalone' ) ); ?></p>
		</div>
		<?php

	}

	/**
	 * Outputs a message to tell the user that the slugs must be unique
	 *
	 * @since 1.5.7.3
	 */
	public function standalone_settings_unique_slugs() {

		?>
		<div class="notice error below-h2">
			<p><?php echo ( __( 'The gallery slug and album link must be unique.', 'envira-standalone' ) ); ?></p>
		</div>
		<?php

	}

	/**
	 * Outputs a message to tell the user that the slug is missing, contains invalid characters or is already taken
	 *
	 * @since 1.5.7.3
	 */
	public function standalone_settings_slug_notice() {

		?>
		<div class="notice error below-h2">
			<p><?php echo ( __( 'The slug is either missing, contains invalid characters or used by a Post Type. Please enter a different slug.', 'envira-standalone' ) ); ?></p>
		</div>
		<?php

	}

	/**
	 * Outputs a WordPress style notification to tell the user settings were saved
	 *
	 * @since 1.7.0
	 */
	public function updated_settings() {
		
		?>
		<div class="notice updated below-h2">
			<p><strong><?php _e( 'Settings saved successfully.', 'envira-gallery' ); ?></strong></p>
		</div>
		<?php
			
	}


	/**
	 * Register and enqueue settings page specific CSS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_settings_styles() {

		wp_register_style( ENVIRA_SLUG . '-settings-style', plugins_url( 'assets/css/settings.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
		wp_enqueue_style( ENVIRA_SLUG . '-settings-style' );

		// Run a hook to load in custom styles.
		do_action( 'envira_gallery_settings_styles' );

	}


	/**
	 * Register and enqueue settings page specific CSS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_styles() {

		// Run a hook to load in custom styles.
		do_action( 'envira_gallery_admin_styles' );

	}

	/**
	 * Register and enqueue settings page specific JS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_scripts() {

		// Tabs
		wp_register_script( ENVIRA_SLUG . '-tabs-script', plugins_url( 'assets/js/tabs.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-tabs-script' );

		// Settings
		wp_register_script( ENVIRA_SLUG . '-settings-script', plugins_url( 'assets/js/settings.js', ENVIRA_FILE ), array( 'jquery', 'jquery-ui-tabs' ), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-settings-script' );
		wp_localize_script(
			ENVIRA_SLUG . '-settings-script',
			'envira_gallery_settings',
			array(
				'active'			 => __( 'Status: Active', 'envira-gallery' ),
				'activate'		   => __( 'Activate', 'envira-gallery' ),
				'activate_nonce'   => wp_create_nonce( 'envira-gallery-activate' ),
				'activating'		=> __( 'Activating...', 'envira-gallery' ),
				'ajax'				=> admin_url( 'admin-ajax.php' ),
				'deactivate'		=> __( 'Deactivate', 'envira-gallery' ),
				'deactivate_nonce' => wp_create_nonce( 'envira-gallery-deactivate' ),
				'deactivating'		  => __( 'Deactivating...', 'envira-gallery' ),
				'inactive'		   => __( 'Status: Inactive', 'envira-gallery' ),
				'install'			  => __( 'Install', 'envira-gallery' ),
				'install_nonce'	   => wp_create_nonce( 'envira-gallery-install' ),
				'installing'		=> __( 'Installing...', 'envira-gallery' ),
				'proceed'			  => __( 'Proceed', 'envira-gallery' )
			)
		);

		// Run a hook to load in custom scripts.
		do_action( 'envira_gallery_settings_scripts' );

	}

	/**
	 * Callback to output the Envira settings page.
	 *
	 * @since 1.7.0
	 */
	public function settings_page() {

		do_action('envira_head');

		?>

		<!-- Tabs -->
		<h2 id="envira-tabs-nav" class="envira-tabs-nav" data-container="#envira-gallery-settings" data-update-hashbang="1">
			<?php 
			$i = 0; 
			foreach ( (array) $this->get_envira_settings_tab_nav() as $id => $title ) {
				$class = ( 0 === $i ? 'envira-active' : '' ); 
				?>
				<a class="nav-tab <?php echo $class; ?>" href="#envira-tab-<?php echo $id; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
				<?php 
				$i++; 
			}
			?>
		</h2>

		<!-- Tab Panels -->
		<div id="envira-gallery-settings" class="wrap">
			<h1 class="envira-hideme"></h1>
			<div class="envira-gallery envira-clear">
				<div id="envira-tabs" class="envira-clear" data-navigation="#envira-tabs-nav">
					<?php 
					$i = 0; 
					foreach ( (array) $this->get_envira_settings_tab_nav() as $id => $title ) {
						$class = ( 0 === $i ? 'envira-active' : '' ); 
						?>
						<div id="envira-tab-<?php echo $id; ?>" class="envira-tab envira-clear <?php echo $class; ?>">
							<?php do_action( 'envira_gallery_tab_settings_' . $id ); ?>
						</div>
						<?php
						$i++;
					}
					?>
				</div>
			</div>
		</div>

		<?php

	}

	/**
	 * Callback for getting all of the settings tabs for Envira.
	 *
	 * @since 1.7.0
	 *
	 * @return array Array of tab information.
	 */
	public function get_envira_settings_tab_nav() {

		$tabs = array(
			'general'	 => __( 'General', 'envira-gallery' ), // This tab is required. DO NOT REMOVE VIA FILTERING.
			'standalone' => __( 'Standalone', 'envira-gallery' ),

		);
		$tabs = apply_filters( 'envira_gallery_settings_tab_nav', $tabs );

		//Make sure debug is always last
		$tabs['debug'] =  __( 'System Info', 'envira-gallery' );
		
		return $tabs;

	}

	/**
	 * Callback for displaying the UI for general settings tab.
	 *
	 * @since 1.7.0
	 */
	public function settings_general_tab() {

		// Get settings
		$media_position = envira_get_setting( 'media_position' );
		$image_delete 	= envira_get_setting( 'image_delete' );
		$media_delete 	= envira_get_setting( 'media_delete' );
		?>
		<div id="envira-settings-general">
			<?php 
			// Output any notices now
			do_action( 'envira_gallery_settings_general_tab_notice' );
			?>

			<table class="form-table">
				<tbody>
					<tr id="envira-settings-key-box">
						<th scope="row">
							<label for="envira-settings-key"><?php _e( apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' License Key', 'envira-gallery' ); ?></label>
						</th>
						<td>
							<form id="envira-settings-verify-key" method="post">
								<input type="password" name="envira-license-key" id="envira-settings-key" value="<?php echo ( envira_get_license_key() ? envira_get_license_key() : '' ); ?>" />
								<?php wp_nonce_field( 'envira-gallery-key-nonce', 'envira-gallery-key-nonce' ); ?>
								<?php submit_button( __( 'Verify Key', 'envira-gallery' ), 'primary', 'envira-gallery-verify-submit', false ); ?>
								<?php submit_button( __( 'Deactivate Key', 'envira-gallery' ), 'secondary', 'envira-gallery-deactivate-submit', false ); ?>
								<p class="description"><?php _e( 'License key to enable automatic updates for ' . apply_filters( 'envira_whitelabel_name', 'Envira' ), 'envira-gallery' ); ?></p>
							</form>
						</td>
					</tr>
					<?php $type = envira_get_license_key_type(); if ( ! empty( $type ) ) : ?>
					<tr id="envira-settings-key-type-box">
						<th scope="row">
							<label for="envira-settings-key-type"><?php _e( apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' License Key Type', 'envira-gallery' ); ?></label>
						</th>
						<td>
							<form id="envira-settings-key-type" method="post">
								<span class="envira-license-type"><?php printf( __( 'Your license key type for this site is <strong>%s.</strong>', 'envira-gallery' ), envira_get_license_key_type() ); ?>
								<input type="hidden" name="envira-license-key" value="<?php echo envira_get_license_key(); ?>" />
								<?php wp_nonce_field( 'envira-gallery-key-nonce', 'envira-gallery-key-nonce' ); ?>
								<?php submit_button( __( 'Refresh Key', 'envira-gallery' ), 'primary', 'envira-gallery-refresh-submit', false ); ?>
								<p class="description"><?php _e( 'Your license key type (handles updates and Addons). Click refresh if your license has been upgraded or the type is incorrect.', 'envira-gallery' ); ?></p>
							</form>
						</td>
					</tr>
					<?php endif; ?>

					<!-- Fix Broken Migration -->
					<tr id="envira-serialization-box">
						<th scope="row">
							<label for="envira-serialization"><?php _e( 'Fix Broken Migration', 'envira-gallery' ); ?></label>
						</th>
						<td>
							<form id="envira-serialization" method="post">
								<?php wp_nonce_field( 'envira-serialization-nonce', 'envira-serialization-nonce' ); ?>
								<?php submit_button( __( 'Fix', 'envira-gallery' ), 'primary', 'envira-serialization-submit', false ); ?>
								<p class="description"><?php _e( 'If you have changed the URL of your WordPress web site, and manually executed a search/replace query on URLs in your WordPress database, your galleries will probably no longer show any images.	If this is the case, click the button above to fix this. We recommend using a migration plugin or script next time :)', 'envira-gallery' ); ?></p>
							</form>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- <hr /> -->

			<!-- Settings Form -->
			<form id="envira-media-delete" method="post">
				<table class="form-table">
					<tbody>
						<!-- Media Position -->
						<tr id="envira-media-position-box">
							<th scope="row">
								<label for="envira-media-position"><?php _e( 'Add New Images', 'envira-gallery' ); ?></label>
							</th>
							<td>
								<select id="envira-media-position" name="envira_media_position">
									<?php foreach ( (array) envira_get_media_positions() as $i => $data ) : ?>
										<option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $media_position ); ?>><?php echo $data['name']; ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e( 'When adding media to a Gallery, choose whether to add this media before or after any existing images.', 'envira-gallery' ); ?></p>
							</td>
						</tr>

						<!-- Delete Media -->
						<tr id="envira-image-delete-box">
							<th scope="row">
								<label for="envira-image-delete"><?php _e( 'Delete Image on Gallery Image Deletion', 'envira-gallery' ); ?></label>
							</th>
							<td>
								<select id="envira-image-delete" name="envira_image_delete">
									<?php foreach ( (array) envira_get_media_delete_options() as $i => $data ) : ?>
										<option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $image_delete ); ?>><?php echo $data['name']; ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e( 'When deleting an Image from a Gallery, choose whether to delete all media associated with that image. Note: If image(s) in the Media Library are attached to other Posts, they will not be deleted.', 'envira-gallery' ); ?></p>
							</td>
						</tr>
						
						<tr id="envira-media-delete-box">
							<th scope="row">
								<label for="envira-media-delete"><?php _e( 'Delete Images on Gallery Deletion', 'envira-gallery' ); ?></label>
							</th>
							<td>
								<select id="envira-media-delete" name="envira_media_delete">
									<?php foreach ( (array) envira_get_media_delete_options() as $i => $data ) : ?>
										<option value="<?php echo $data['value']; ?>"<?php selected( $data['value'], $media_delete ); ?>><?php echo $data['name']; ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e( 'When deleting a Gallery, choose whether to delete all media associated with the gallery. Note: If image(s) in the Media Library are attached to other Posts, they will not be deleted.', 'envira-gallery' ); ?></p>
							</td>
						</tr>

						<?php do_action( 'envira_gallery_settings_general_box' ); ?>
					</tbody>
				</table>

				<?php wp_nonce_field( 'envira-gallery-settings-nonce', 'envira-gallery-settings-nonce' ); ?>
				<?php submit_button( __( 'Save Settings', 'envira-gallery' ), 'primary', 'envira-gallery-settings-submit', false ); ?>
			</form>
		</div>
		<?php

	}

	/**
	 * Callback for displaying the UI for standalone settings tab.
	 *
	 * @since 1.7.0
	 */
	public function settings_standalone_tab() {

		// Get slugs
		$enabled = envira_get_setting( 'standalone_enabled' );
		$slug = envira_standalone_get_slug( 'gallery' );
		$albumSlug = envira_standalone_get_slug( 'albums' );

		?>
		<div id="envira-settings-standalone">
			<?php
			// Output notices
			do_action( 'envira_gallery_settings_standalone_tab_notice' );
			?>

			<table class="form-table">
				<tbody>
					<form action="edit.php?post_type=envira&page=envira-gallery-settings#!envira-tab-standalone" method="post">
						<tr id="envira-settings-standalone-enable">
							<th scope="row">
								<label for="envira-standalone-enable"><?php _e( 'Enable Standalone', 'envira-standalone' ); ?></label>
							</th>
							<td>
								<p class="description">
									<label for="envira-standalone-enable">
										<input type="checkbox" name="envira-standalone-enable" id="envira-standalone-enable" value="1" <?php checked( true, $enabled ); ?> />
										<?php wp_nonce_field( 'envira-standalone-nonce', 'envira-standalone-nonce' ); ?>
										<?php _e( 'The standalone option allows you to access galleries created through the ' . apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' post type with unique URLs. Now your galleries can have dedicated gallery pages!', 'envira-standalone' ); ?>
									</label>
								</p>
							</td>
						</tr>

						<tr id="envira-settings-slug-box-gallery">
							<th scope="row">
								<label for="envira-gallery-slug"><?php _e( 'Gallery Slug ', 'envira-standalone' ); ?></label>
							</th>
							<td>
								<input type="text" name="envira-gallery-slug" id="envira-gallery-slug" value="<?php echo $slug; ?>" />
								<p class="description"><?php _e( 'The slug to prefix to all ' . apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' Galleries.', 'envira-standalone' ); ?></p>
							</td>
						</tr>

						<tr id="envira-settings-slug-box-albums">
							<th scope="row">
								<label for="envira-albums-slug"><?php _e( 'Album Slug ', 'envira-standalone' ); ?></label>
							</th>
							<td>
								<input type="text" name="envira-albums-slug" id="envira-albums-slug" value="<?php echo $albumSlug; ?>" />
								<p class="description"><?php _e( 'The slug to prefix to all ' . apply_filters( 'envira_whitelabel_name', 'Envira' ) . ' Albums.', 'envira-standalone' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php submit_button( __( 'Save', 'envira-gallery' ), 'primary', 'envira-gallery-verify-submit', false ); ?></th>
							<td>&nbsp;</td>
						</tr>
					</form>
				</tbody>
			</table>
		</div>
		<?php

	}

	/**
	 * Callback for displaying the UI for debug tab.
	 *
	 * @since 1.5.7.3
	 */
	public function settings_debug_tab() {

		// Get slugs
		$enabled = envira_get_setting( 'standalone_enabled' );
		// $slug = Envira_Gallery_Common::get_instance()->standalone_get_slug( 'gallery' );
		// $albumSlug = Envira_Gallery_Common::get_instance()->standalone_get_slug( 'albums' );

		?>
		<div id="envira-settings-debug">
			<?php
			// Output notices
			do_action( 'envira_gallery_settings_standalone_tab_notice' );
			?>

			<?php do_action( 'envira_gallery_debug_screen_output' ); ?>

		</div>
		<?php

	}

   /**
	 * Retrieve the plugin basename from the plugin slug.
	 *
	 * @since 1.7.0
	 *
	 * @param string $slug The plugin slug.
	 * @return string		  The plugin basename if found, else the plugin slug.
	 */
	public function get_plugin_basename_from_slug( $slug ) {

		$keys = array_keys( get_plugins() );

		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '|', $key ) ) {
				return $key;
			}
		}

		return $slug;

	}

	/**
	 * Add Settings page to plugin action links in the Plugins table.
	 *
	 * @since 1.7.0
	 *
	 * @param array $links	Default plugin action links.
	 * @return array $links Amended plugin action links.
	 */
	public function settings_link( $links ) {

		$settings_link = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => 'envira', 'page' => 'envira-gallery-settings' ), admin_url( 'edit.php' ) ) ), __( 'Settings', 'envira-gallery' ) );
		array_unshift( $links, $settings_link );

		return $links;

	}

	/**
	 * Maybe fixes the broken migration.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if not fixing the broken migration
	 */
	public function maybe_fix_migration() {
		
		// Check if user pressed 'Fix' button and nonce is valid
		if ( ! isset( $_POST['envira-serialization-submit'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['envira-serialization-nonce'], 'envira-serialization-nonce' ) ) {
			return;
		}
		
		// If here, fix potentially broken migration
		// Get WPDB and serialization class
		global $wpdb, $fixedGalleries;
		 
		// Keep count of the number of galleries that get fixed
		$fixedGalleries = 0;
		
		// Query to get all Envira CPTs
		$galleries = new \ WP_Query( array (
			'post_type'		=> 'envira',
			'post_status'	=> 'any',
			'posts_per_page'=> -1, 
		) );
		
		// Iterate through galleries
		if ( $galleries->posts ) {
			foreach ( $galleries->posts as $gallery ) {

				$fixed = false;
				
				// Attempt to get gallery data
				$gallery_data = get_post_meta( $gallery->ID, '_eg_gallery_data', true );

				// Skip empty galleries
				if ( empty( $gallery_data ) ) {
					continue;
				}

				// If gallery data isn't an array, something broke
				if ( ! is_array( $gallery_data ) ) { 
					// Need to fix the broken serialized string for this gallery
					// Get raw string from DB
					$query = $wpdb->prepare( "	SELECT meta_value
												FROM ".$wpdb->prefix."postmeta
												WHERE post_id = %d
												AND meta_key = %s
												LIMIT 1",
												$gallery->ID,
												'_eg_gallery_data' );
					$raw_gallery_data = $wpdb->get_row( $query );

					// Do the fix, which returns an unserialized array
					$gallery_data = envira_fix_serialized_string( $raw_gallery_data->meta_value );

					// Check we now have an array of unserialized data
					if ( ! is_array ( $gallery_data ) ) {
						continue;
					}

					// Mark as fixed
					$fixed = true;
				}

				// Next, check each gallery image has a valid URL
				// Some migrations seem to strip the leading HTTP URL, which causes us problems with thumbnail generation.
				if ( isset( $gallery_data['gallery'] ) ) {
					foreach ( $gallery_data['gallery'] as $id => $item ) {
						// Source
						if ( isset( $item['src'] ) ) {
							if ( ! empty( $item['src'] ) && ! filter_var( $item['src'], FILTER_VALIDATE_URL ) ) {
								// Image isn't a valid URL - fix
								$gallery_data['gallery'][ $id ]['src'] = get_bloginfo( 'url' ) . '/' . $item['src'];
								$fixed = true;
							}
						}

						// Link
						if ( isset( $item['link'] ) ) {
							if ( ! empty( $item['link'] ) && ! filter_var( $item['link'], FILTER_VALIDATE_URL ) ) {
								// Image isn't a valid URL - fix
								$gallery_data['gallery'][ $id ]['link'] = get_bloginfo( 'url' ) . '/' . $item['link'];
								$fixed = true;
							}
						}

						// Thumbnail
						if ( isset( $item['thumb'] ) ) {
							if ( ! empty( $item['thumb'] ) && ! filter_var( $item['thumb'], FILTER_VALIDATE_URL ) ) {
								// Thumbnail isn't a valid URL - fix
								$gallery_data['gallery'][ $id ]['thumb'] = get_bloginfo( 'url' ) . '/' . $item['thumb'];
								$fixed = true;
							}
						}
					}
				}
				
				// Finally, store the post meta if a fix was applied
				if ( $fixed ) {
					update_post_meta( $gallery->ID, '_eg_gallery_data', $gallery_data );
					$fixedGalleries++;
				}

			}
		}
		
		// Output an admin notice so the user knows what happened
		add_action( 'envira_gallery_settings_general_tab_notice', array( $this, 'fixed_migration' ) );
		
	}

	/**
	 * debug_screen_output function.
	 * 
	 * @access public
	 * @return void
	 */
	public function debug_screen_output() {
	
		$browser = new \Envira\Utils\Browser();
		
		if ( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme	   = $theme_data['Name'] . ' ' . $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme	   = $theme_data->Name . ' ' . $theme_data->Version;
		}

		// Try to identify the hosting provider
		$host = false;
		
		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		}

		$request['cmd'] = '_notify-validate';

		$params = array(
			'sslverify' => false,
			'timeout'   => 60,
			'body'	   => $request,
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$WP_REMOTE_POST = 'wp_remote_post() works' . "\n";
		} else {
			$WP_REMOTE_POST = 'wp_remote_post() does not work' . "\n";
		}
			
		?>

		<div class="wrap">
				<div id="templateside">
					<p class="instructions"><?php _e( 'The information provided on this screen is intended to be shared with Envira Gallery when opening a new support ticket.', 'send-system-info' ) ?></p>
					<p class="instructions"><?php _e( 'This information can be downloaded as a text file, then uploaded to the support ticket.', 'send-system-info' ) ?></p>
					<p class="instructions"><?php _e( '<a target="_blank" href="https://enviragallery.com/docs/">See our documentation</a> for more details.', 'send-system-info' ) ?></p>
				</div>
				<div id="template">
					<?php // Form used to download .txt file ?>
					<form action="<?php echo esc_url( self_admin_url( 'admin-ajax.php' ) ); ?>" method="post" enctype="multipart/form-data" >
						<input type="hidden" name="action" value="download_system_info" />
						<div>
						
						<?php
						
							envira_load_admin_partial('settings-debug-output', array(
								'instance'	=> $this,
								'browser'	=> $browser,
								'theme'		=> $theme,
								'host'		=> $host,
								'wp_remote'	=>	$WP_REMOTE_POST 
							
							) );
						?>							
						</div>
						<p class="submit">
							<input type="submit" class="button button-primary" value="<?php _e( 'Download System Info as Text File', 'send-system-info' ) ?>" />
						</p>
					</form>
				</div>
		</div>

		<?php
	}

	/**
	 * Generate Text file download
	 *
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function debug_download_info() {
		
		if ( ! isset( $_POST['send-system-info-textarea'] ) || empty( $_POST['send-system-info-textarea'] ) ) {
			return;
		}

		header( 'Content-type: text/plain' );

		// Text file name marked with Unix timestamp
		header( 'Content-Disposition: attachment; filename=system_info_' . time() . '.txt' );

		echo $_POST['send-system-info-textarea'];
		die();
	}

	/**
	 * Outputs a WordPress style notification to tell the user how many galleries were
	 * fixed after running the migration fixer
	 *
	 * @since 1.7.0
	 */
	public function fixed_migration() {
		global $fixedGalleries;
		
		?>
		<div class="notice updated below-h2">
			<p><strong><?php echo $fixedGalleries . __( ' galleries(s) fixed successfully.', 'envira-gallery' ); ?></strong></p>
		</div>
		<?php
			
	}	

}