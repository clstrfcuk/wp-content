<?php
/**
 * Addons class.
 *
 * @since 1.7.0
 *
 * @package Envira_Gallery
 * @author	Envira Gallery Team
 */
 
namespace Envira\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Envira\Admin\License;

class Addons {

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

		// Add custom addons submenu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );

		// Add callbacks for addons tabs.
		add_action( 'envira_gallery_addons_section', array( $this, 'addons_content' ) );

		// Add the addons menu item to the Plugins table.
		add_filter( 'plugin_action_links_' . plugin_basename( ENVIRA_FILE ), array( $this, 'addons_link' ) );

	}

	/**
	 * Register the Addons submenu item for Envira.
	 *
	 * @since 1.7.0
	 */
	public function admin_menu() {

		// Check and see if whitelabeling is active... by default this screen shouldn't be accessible when whitelabeling is on
		if ( apply_filters('envira_whitelabel', false )	 ) {
			if ( !apply_filters('envira_whitelabel_addon_screen', false )  ) {
				return;
			}
		}

		// Register the submenu.
		$this->hook = add_submenu_page(
			'edit.php?post_type=envira',
			__( ( apply_filters('envira_whitelabel', false ) ? '' : 'Envira Gallery ' ) . 'Addons', 'envira-gallery' ),
			'<span style="color:#7cc048"> ' . __( 'Addons', 'envira-gallery' ) . '</span>',
			apply_filters( 'envira_gallery_menu_cap', 'manage_options' ),
			ENVIRA_SLUG . '-addons',
			array( $this, 'addons_page' )
		);

		// If successful, load admin assets only on that page and check for addons refresh.
		if ( $this->hook ) {
			add_action( 'load-' . $this->hook, array( $this, 'maybe_refresh_addons' ) );
			add_action( 'load-' . $this->hook, array( $this, 'addons_page_assets' ) );
		}

	}

	/**
	 * Maybe refreshes the addons page.
	 *
	 * @since 1.7.0
	 *
	 * @return null Return early if not refreshing the addons.
	 */
	public function maybe_refresh_addons() {

		if ( ! $this->is_refreshing_addons() ) {
			return;
		}

		if ( ! $this->refresh_addons_action() ) {
			return;
		}

		$this->get_addons_data( envira_get_license_key() );

	}

	/**
	 * Loads assets for the addons page.
	 *
	 * @since 1.7.0
	 */
	public function addons_page_assets() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

	}

	/**
	 * Register and enqueue addons page specific CSS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_styles() {

		wp_register_style( ENVIRA_SLUG . '-addons-style', plugins_url( 'assets/css/addons.css', ENVIRA_FILE ), array(), ENVIRA_VERSION );
		wp_enqueue_style( ENVIRA_SLUG . '-addons-style' );

		// Run a hook to load in custom styles.
		do_action( 'envira_gallery_addons_styles' );

	}

	/**
	 * Register and enqueue addons page specific JS.
	 *
	 * @since 1.7.0
	 */
	public function enqueue_admin_scripts() {

		// List.js
		wp_register_script( ENVIRA_SLUG . '-list-script', plugins_url( 'assets/js/min/list-min.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-list-script' );

		// Addons
		wp_register_script( ENVIRA_SLUG . '-addons-script', plugins_url( 'assets/js/addons.js', ENVIRA_FILE ), array( 'jquery' ), ENVIRA_VERSION, true );
		wp_enqueue_script( ENVIRA_SLUG . '-addons-script' );
		wp_localize_script(
			ENVIRA_SLUG . '-addons-script',
			'envira_gallery_addons',
			array(
				'activate_nonce'	  => wp_create_nonce( 'envira-gallery-activate' ),
				'active'		   => __( 'Status: Active', 'envira-gallery' ),
				'activate'		   => __( 'Activate', 'envira-gallery' ),
				'get_addons_nonce'	 => wp_create_nonce( 'envira-gallery-get-addons' ),
				'activating'	   => __( 'Activating...', 'envira-gallery' ),
				'ajax'			   => admin_url( 'admin-ajax.php' ),
				'deactivate'	   => __( 'Deactivate', 'envira-gallery' ),
				'deactivate_nonce' => wp_create_nonce( 'envira-gallery-deactivate' ),
				'deactivating'	   => __( 'Deactivating...', 'envira-gallery' ),
				'inactive'		   => __( 'Status: Inactive', 'envira-gallery' ),
				'install'		   => __( 'Install', 'envira-gallery' ),
				'install_nonce'	   => wp_create_nonce( 'envira-gallery-install' ),
				'installing'	   => __( 'Installing...', 'envira-gallery' ),
				'proceed'		   => __( 'Proceed', 'envira-gallery' )
			)
		);

		// Run a hook to load in custom scripts.
		do_action( 'envira_gallery_addons_scripts' );

	}

	/**
	 * Callback to output the Envira addons page.
	 *
	 * @since 1.7.0
	 */
	public function addons_page() {

		do_action('envira_head');
		?>

		<div id="addon-heading" class="subheading clearfix">
			<h2><?php _e( ( apply_filters('envira_whitelabel', false ) ? '' : 'Envira Gallery ' ) . 'Addons', 'envira-gallery' ); ?></h2>
			<form id="add-on-search">
				<span class="spinner"></span>
				<input id="add-on-searchbox" name="envira-addon-search" value="" placeholder="<?php _e( 'Search ' . (apply_filters('envira_whitelabel', false ) ? '' : 'Envira ') . 'Addons', 'envira-gallery' ); ?>" />
				<select id="envira-filter-select">
					<option value="asc"><?php _e( 'Sort Ascending (A-Z)', 'envira-gallery' ); ?></option>
					<option value="desc"><?php _e( 'Sort Descending (Z-A)', 'envira-gallery' ); ?></option>
				</select>
			</form>
		</div>

		<div id="envira-gallery-addons" class="wrap">
		  	<h1 class="envira-hideme"></h1>
			<div class="envira-gallery envira-clear">
				<?php do_action( 'envira_gallery_addons_section' ); ?>
			</div>
		</div>
		<?php

	}

	/**
	 * Callback for displaying the UI for Addons.
	 *
	 * @since 1.7.0
	 */
	public function addons_content() {

		// If error(s) occured during license key verification, display them and exit now.
		if ( false !== envira_get_license_key_errors() ) {
			?>
			<div class="error below-h2">
				<p>
					<?php _e( 'In order to get access to Addons, you need to resolve your license key errors.', 'envira-gallery' ); ?>
				</p>
			</div>
			<?php
			return;
		}

		// Get Addons
		$addons = $this->get_addons();

		// If no Addon(s) were returned, our API call returned an error.
		// Show an error message with a button to reload the page, which will trigger another API call.
		if ( ! $addons ) {
			?>
			<form id="envira-addons-refresh-addons-form" method="post">
				<p>
					<?php _e( 'There was an issue retrieving the addons for this site. Please click on the button below the refresh the addons data.', 'envira-gallery' ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" class="button button-primary"><?php _e( 'Refresh Addons', 'envira-gallery' ); ?></a>
				</p>
			</form>
			<?php
			return;
		}

		// If here, we have Addons to display, so let's output them now.
		// Get installed plugins and upgrade URL
		$installed_plugins = get_plugins();
		$upgrade_url = envira_get_upgrade_link();
		?>
		<div id="envira-addons">
			<?php
			// Output Addons the User is licensed to use.
			if ( count( $addons['licensed'] )> 0 ) {
				?>
				<div class="envira-addons-area licensed" class="envira-clear">
					<h3><?php _e( 'Available Addons', 'envira-gallery' ); ?></h3>

					<div id="envira-addons-licensed" class="envira-addons">
						<!-- list container class required for list.js -->
						<div class="list">
							<?php
							foreach ( (array) $addons['licensed'] as $i => $addon ) {
								$this->get_addon_card( $addon, $i, true, $installed_plugins );
							}
							?>
						</div>
					</div>
				</div>
				<?php
			} // Close licensed addons

			// Output Addons the User isn't licensed to use.
			if ( count( $addons['unlicensed'] )> 0 ) {
				?>
				<div class="envira-addons-area unlicensed" class="envira-clear">
					<h3><?php _e( 'Unlock More Addons', 'envira-gallery' ); ?></h3>
					<p><?php echo sprintf( __( '<strong>Want even more addons?</strong> <a href="%s">Upgrade your ' . (apply_filters('envira_whitelabel', false ) ? '' : 'Envira Gallery ') . 'account</a> and unlock the following addons.', 'envira-gallery' ), $upgrade_url ); ?></p>

					<div id="envira-addons-unlicensed" class="envira-addons">
						<!-- list container class required for list.js -->
						<div class="list">
							<?php
							foreach ( (array) $addons['unlicensed'] as $i => $addon ) {
								$this->get_addon_card( $addon, $i, false, $installed_plugins );
							}
							?>
						</div>
					</div>
				</div>
				<?php
			} // Close unlicensed addons
			?>
		</div>
		<?php

	}

	/**
	 * Retrieves addons from the stored transient or remote server.
	 *
	 * @since 1.7.0
	 *
	 * @return bool | array	   false | Array of licensed and unlicensed Addons.
	 */
	public function get_addons() {

		// Get license key and type.
		$key 	= envira_get_license_key();
		$type 	= envira_get_license_key_type();

		// Get addons data from transient or perform API query if no transient.
		if ( false === ( $addons = get_transient( '_eg_addons' ) ) ) {
			$addons = $this->get_addons_data( $key );
		}

		// If no Addons exist, return false
		if ( ! $addons ) {
			return false;
		}

		// Iterate through Addons, to build two arrays:
		// - Addons the user is licensed to use,
		// - Addons the user isn't licensed to use.
		$results = array(
			'licensed'	=> array(),
			'unlicensed'=> array(),
		);
		
		foreach ( (array) $addons as $i => $addon ) {
			
			//Skip over addons that have been rolled into the core.
			if( $addon->slug == 'envira-supersize' || $addon->slug == 'envira-standalone' ) {
				continue;
			}
			
			// Determine whether the user is licensed to use this Addon or not.
			if (
				empty( $type ) ||
				( in_array( 'advanced', $addon->categories ) && $type != 'gold' && $type != 'platinum' &&  $type != 'pro' && $type != 'ultimate'  && $type != 'agency' ) ||
				( in_array( 'basic', $addon->categories ) && ( $type != 'silver' && $type != 'gold' && $type != 'platinum' && $type != 'plus' && $type != 'pro' && $type != 'ultimate' && $type != 'agency' ) )
			) {
				// Unlicensed
				$results['unlicensed'][] = $addon;
				continue;
			}

			// Licensed
			$results['licensed'][] = $addon;

		}

		// Return Addons, split by licensed and unlicensed.
		return $results;

	}

	/**
	 * Pings the remote server for addons data.
	 *
	 * @since 1.7.0
	 *
	 * @param	string		$key	The user license key.
	 * @return	array				Array of addon data otherwise.
	 */
	public function get_addons_data( $key ) {

		// Get Addons
		// If the key is valid, we'll get personalised upgrade URLs for each Addon (if necessary) and plugin update information.
		$license = new License;
		$addons = $license->perform_remote_request( 'get-addons-data-v15', array( 'tgm-updater-key' => $key ) );

		// If there was an API error, set transient for only 10 minutes.
		if ( ! $addons ) {
			set_transient( '_eg_addons', false, 10 * MINUTE_IN_SECONDS );
			return false;
		}

		// If there was an error retrieving the addons, set the error.
		if ( isset( $addons->error ) ) {
			set_transient( '_eg_addons', false, 10 * MINUTE_IN_SECONDS );
			return false;
		}

		// Otherwise, our request worked. Save the data and return it.
		set_transient( '_eg_addons', $addons, DAY_IN_SECONDS );
		return $addons;

	}

	/**
	 * Flag to determine if addons are being refreshed.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if being refreshed, false otherwise.
	 */
	public function is_refreshing_addons() {

		return isset( $_POST['envira-gallery-refresh-addons-submit'] );

	}

	/**
	 * Verifies nonces that allow addon refreshing.
	 *
	 * @since 1.7.0
	 *
	 * @return bool True if nonces check out, false otherwise.
	 */
	public function refresh_addons_action() {

		return isset( $_POST['envira-gallery-refresh-addons-submit'] ) && wp_verify_nonce( $_POST['envira-gallery-refresh-addons'], 'envira-gallery-refresh-addons' );

	}

	/**
	 * Retrieve the plugin basename from the plugin slug.
	 *
	 * @since 1.7.0
	 *
	 * @param string $slug The plugin slug.
	 * @return string	   The plugin basename if found, else the plugin slug.
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
	 * Add Addons page to plugin action links in the Plugins table.
	 *
	 * @since 1.7.0
	 *
	 * @param	array	$links	  Default plugin action links.
	 * @return	array	$links	  Amended plugin action links.
	 */
	public function addons_link( $links ) {

		$addons_link = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => 'envira', 'page' => ( class_exists( 'Envira_Gallery' ) ? 'envira-gallery-addons' : 'envira-gallery-lite-addons' ) ), admin_url( 'edit.php' ) ) ), __( 'Addons', 'envira-gallery' ) );
		array_unshift( $links, $addons_link );

		return $links;

	}

	/**
	 * Outputs the addon "box" on the addons page.
	 *
	 * @since 1.7.0
	 *
	 * @param	object	$addon				Addon data from the API / transient call
	 * @param	int		$counter			Index of this Addon in the collection
	 * @param	bool	$is_licensed		Whether the Addon is licensed for use
	 * @param	array	$installed_plugins	Installed WordPress Plugins
	 */
	public function get_addon_card( $addon, $counter = 0, $is_licensed = false, $installed_plugins = false ) {

		// Setup some vars
		$plugin_basename   = $this->get_plugin_basename_from_slug( $addon->slug );
		$categories = implode( ',', $addon->categories );
		if ( ! $installed_plugins ) {
			$installed_plugins = get_plugins();
		}

		// If the Addon doesn't supply an upgrade_url key, it's because the user hasn't provided a license
		// get_upgrade_link() will return the Lite or Pro link as necessary for us.
		if ( ! isset( $addon->upgrade_url ) ) {
			$addon->upgrade_url = envira_get_upgrade_link();
		}

		// Output the card
		?>
		<div class="envira-addon">
			<h3 class="envira-addon-title"><?php echo esc_html( $addon->title ); ?></h3>
			<?php
			if ( ! empty( $addon->image ) ) {
				?>
				<img class="envira-addon-thumb" src="<?php echo esc_url( $addon->image ); ?>" alt="<?php echo esc_attr( $addon->title ); ?>" />
				<?php
			}
			?>

			<p class="envira-addon-excerpt"><?php echo esc_html( $addon->excerpt ); ?></p>

			<?php
			// If the Addon is unlicensed, show the upgrade button
			if ( ! $is_licensed ) {
				?>
				<div class="envira-addon-active envira-addon-message">
					<div class="interior">
						<div class="envira-addon-upgrade">
							<a href="<?php echo esc_url( $addon->upgrade_url ); ?>" target="_blank" class="button button-primary envira-addon-upgrade-button"  rel="<?php echo esc_attr( $plugin_basename ); ?>">
								<?php _e( 'Upgrade Now', 'envira-gallery' ); ?>
							</a>
							<span class="spinner envira-gallery-spinner"></span>
						</div>
					</div>
				</div>
				<?php
			} else {
				// Addon is licensed

				// If the plugin is not installed, display an install message and button.
				if ( ! isset( $installed_plugins[ $plugin_basename ] ) ) {
					?>
					<div class="envira-addon-not-installed envira-addon-message">
						<div class="interior">
							<span class="addon-status"><?php _e( 'Status: <span>Not Installed</span>', 'envira-gallery' ); ?></span>
							<div class="envira-addon-action">
								<a class="button button-primary envira-addon-action-button envira-install-addon" href="#" rel="<?php echo esc_url( $addon->url ); ?>">
									<i class="envira-cloud-download"></i>
									<?php _e( 'Install', 'envira-gallery' ); ?>
								</a>
								<span class="spinner envira-gallery-spinner"></span>
							</div>
						</div>
					</div>
					<?php
				} else {
					// Plugin is installed.
					if ( is_plugin_active( $plugin_basename ) ) {
						// Plugin is active. Display the active message and deactivate button.
						?>
						<div class="envira-addon-active envira-addon-message">
							<div class="interior">
								<span class="addon-status"><?php _e( 'Status: <span>Active</span>', 'envira-gallery' ); ?></span>
								<div class="envira-addon-action">
									<a class="button button-primary envira-addon-action-button envira-deactivate-addon" href="#" rel="<?php echo esc_attr( $plugin_basename ); ?>">
										<i class="envira-toggle-on"></i>
										<?php _e( 'Deactivate', 'envira-gallery' ); ?>
									</a>
									<span class="spinner envira-gallery-spinner"></span>
								</div>
							</div>
						</div>
						<?php
					} else {
						// Plugin is inactivate. Display the inactivate mesage and activate button.
						?>
						<div class="envira-addon-inactive envira-addon-message">
							<div class="interior">
								<span class="addon-status"><?php _e( 'Status: <span>Inactive</span>', 'envira-gallery' ); ?></span>
								<div class="envira-addon-action">
									<a class="button button-primary envira-addon-action-button envira-activate-addon" href="#" rel="<?php echo esc_attr( $plugin_basename ); ?>">
										<i class="envira-toggle-on"></i>
										<?php _e( 'Activate', 'envira-gallery' ); ?>
									</a>
									<span class="spinner envira-gallery-spinner"></span>
								</div>
							</div>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
		<?php

	}

}