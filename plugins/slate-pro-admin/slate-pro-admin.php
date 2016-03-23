<?php
/*
Plugin Name: Slate Pro Admin (shared on wplocker.com)
Plugin URI: http://sevenbold.com/wordpress/slate-pro/
Description: Slate Pro is a powerful WordPress admin theme plugin that reimagines WordPress with a clean and simplified design. White label your WordPress install with custom colors, a custom login screen, custom admin branding, and more.
Author: Seven Bold
Version: 1.1.1
Author URI: http://sevenbold.com/
*/

if ( ! defined( 'SLATE_PRO_VERSION' ) ) {
	define( 'SLATE_PRO_VERSION', '1.1.1' );
}
if ( ! defined( 'SLATE_PRO_DB' ) ) {
	define( 'SLATE_PRO_DB', '8' );
}

// Import
if ( is_admin() && isset( $GLOBALS['_GET']['page'] ) && 'slate_pro_import_export' == $GLOBALS['_GET']['page'] ) {

	if ( isset( $_POST['slate_pro_import'] ) ) {

		global $slate_pro_import_success;

		$import = esc_sql( @unserialize( stripslashes( $_POST['slate_pro_import_settings'] ) ) );

		if ( false !== $import && is_array( $import ) ) {
			if ( is_multisite() && is_main_site() ) {
				update_site_option( 'slate_pro_settings', $import );
			} else {
				update_option( 'slate_pro_settings', $import );
			}
			$slate_pro_import_success = true;
		} else {
			$slate_pro_import_success = false;
		}
	}
}

// Global Settings
if ( is_admin() || slate_pro_is_login_page() ) {
	$slate_pro_settings = slate_pro_get_settings();
}
function slate_pro_get_settings() {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {
		return $slate_pro_settings = get_site_option( 'slate_pro_settings' );
	} else {
		return $slate_pro_settings = get_option( 'slate_pro_settings' );
	}
}
function slate_pro_is_login_page() {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {

		$slate_pro_settings = get_site_option( 'slate_pro_settings' );

		if ( 'on' === $slate_pro_settings['customLogin'] ) {
			if ( preg_match( $slate_pro_settings['customLoginURL'] . '/', $GLOBALS['path'] ) ) {
				return true;
			}
		} else {
			if ( preg_match( '/wp-login.php/', $GLOBALS['path'] ) || preg_match( '/wp-register.php/', $GLOBALS['path'] ) ) {
				return true;
			}
		}
	} else {

		$slate_pro_settings = get_option( 'slate_pro_settings' );

		if ( 'on' === $slate_pro_settings['customLogin'] ) {
			if ( $slate_pro_settings['customLoginURL'] === $_SERVER['REQUEST_URI'] || $slate_pro_settings['customLoginURL'] . '?loggedout=true' === $_SERVER['REQUEST_URI'] || $slate_pro_settings['customLoginURL'] . '/?loggedout=true' === $_SERVER['REQUEST_URI'] ) {
				return true;
			}
		} else {
			return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
		}
	}
}

// Setup the Settings Menu and Page
if ( is_admin() ) {
	if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {
		add_action( 'network_admin_menu', 'slate_pro_admin_branding' );
		add_action( 'network_admin_menu', 'slate_pro_plugin_menu' );
		add_action( 'admin_menu', 'slate_pro_admin_branding' );
	} else {
		add_action( 'admin_menu', 'slate_pro_admin_branding' );
		add_action( 'admin_menu', 'slate_pro_plugin_menu' );
	}
}
function slate_pro_admin_branding() {
	global $slate_pro_settings;

	if ( '' !== $slate_pro_settings['adminLogo'] ) {
		add_menu_page(
			'Slate Pro Admin Logo',
			'Slate Pro Admin Logo',
			'read',
			'slate_pro_admin_logo',
			'',
			esc_url( $slate_pro_settings['adminLogo'] ),
			'-0.0001'
		);
	}
	if ( '' !== $slate_pro_settings['adminLogoFolded'] ) {
		add_menu_page(
			'Slate Pro Admin Logo Folded',
			'Slate Pro Admin Logo Folded',
			'read',
			'slate_pro_admin_logo_folded',
			'',
			esc_url( $slate_pro_settings['adminLogoFolded'] ),
			'-0.0002'
		);
	}
	// Override Jetpack trying to be above Dashboard
	if ( '' !== $slate_pro_settings['adminLogo'] && '' !== $slate_pro_settings['adminLogoFolded'] ) {
		add_filter( 'custom_menu_order', '__return_true', 11 );
		add_filter( 'menu_order', 'slate_pro_menu_order', 11 );
	}
}

function slate_pro_plugin_menu() {
	add_menu_page(
		'Slate Pro Settings',
		'Slate Pro',
		'manage_options',
		'slate_pro_color_schemes',
		'slate_pro_color_schemes',
		plugins_url( 'images/slate_pro_plugin_icon.png', __FILE__ ),
		'98.2481'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Color Schemes',
		'Color Schemes',
		'manage_options',
		'slate_pro_color_schemes',
		'slate_pro_color_schemes'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Branding',
		'Branding',
		'manage_options',
		'slate_pro_branding',
		'slate_pro_branding'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'slate_pro_dashboard',
		'slate_pro_dashboard'
	);
	if ( is_multisite() && is_main_site() ) {

	} else {
		add_submenu_page(
			'slate_pro_color_schemes',
			'Admin Menu',
			'Admin Menu',
			'manage_options',
			'slate_pro_admin_menu',
			'slate_pro_admin_menu'
		);
	}
	add_submenu_page(
		'slate_pro_color_schemes',
		'Admin Bar &amp; Footer',
		'Admin Bar &amp; Footer',
		'manage_options',
		'slate_pro_admin_bar_footer',
		'slate_pro_admin_bar_footer'
	);

	add_submenu_page(
		'slate_pro_color_schemes',
		'Content &amp; Notices',
		'Content &amp; Notices',
		'manage_options',
		'slate_pro_content_notices',
		'slate_pro_content_notices'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Permissions',
		'Permissions',
		'manage_options',
		'slate_pro_permissions',
		'slate_pro_permissions'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Settings',
		'Settings',
		'manage_options',
		'slate_pro_settings',
		'slate_pro_settings'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'License',
		'License',
		'manage_options',
		'slate_pro_license',
		'slate_pro_license'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'About',
		'About',
		'manage_options',
		'slate_pro_about',
		'slate_pro_about'
	);
	add_submenu_page(
		'slate_pro_color_schemes',
		'Import / Export',
		'Import / Export',
		'manage_options',
		'slate_pro_import_export',
		'slate_pro_import_export'
	);
}

function slate_pro_menu_order( $menu_order ) {
	$sp_menu_order = array();
	foreach ( $menu_order as $index => $item ) {
		if ( 'slate_pro_admin_logo_folded' !== $item ) {
			$sp_menu_order[] = $item;
		}

		if ( 0 === $index ) {
			$sp_menu_order[] = 'slate_pro_admin_logo_folded';
		}
	}

	return $sp_menu_order;
}

// admin_init
add_action( 'admin_init', 'slate_pro_admin_init' );
function slate_pro_admin_init() {

	if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {
		add_action( 'network_admin_edit_slate_pro_network', 'slate_pro_save_settings_network', 10, 0 );
	} else {
		register_setting(
			'slate_pro_settings',
			'slate_pro_settings',
			'slate_pro_sanitize'
		);
	}

}

// Add Settings Link on Plugin Page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'slate_pro_plugin_link' );
function slate_pro_plugin_link( $links ) {
	$settings_link = '<a href="admin.php?page=slate_pro_color_schemes">Settings</a>';
	array_push( $links, $settings_link );
	return $links;
}

// DB Updates
function slate_pro_check_db() {
	if ( is_multisite() && is_main_site() ) {
		if ( get_site_option( 'slate_pro_db' ) >= SLATE_PRO_DB ) {
			return;
		}
	} else {
		if ( get_option( 'slate_pro_db' ) >= SLATE_PRO_DB ) {
			return;
		}
	}

	require_once( __DIR__ . '/inc/update_db.php' );
	slate_pro_update_db();
}

// Version Check
function slate_pro_check_version() {
	if ( is_multisite() && is_main_site() ) {
		if ( get_site_option( 'slate_pro_version' ) >= SLATE_PRO_VERSION ) {
			return;
		} else {
			update_site_option( 'slate_pro_version', SLATE_PRO_VERSION );
		}
	} else {
		if ( get_option( 'slate_pro_version' ) >= SLATE_PRO_VERSION ) {
			return;
		} else {
			update_option( 'slate_pro_version', SLATE_PRO_VERSION );
		}
	}

	// Update info on License server
	slate_pro_initial_license();
}

// Licensing
function slate_pro_initial_license() {
	global $wp_version;
	if ( is_multisite() ) {
		$multisite = '1';
	} else {
		$multisite = '0';
	}
	$server = 'http://licenses.sevenbold.com/license.php';
	$args = array(
		'useragent' => $wp_version,
		'email'     => get_bloginfo( 'admin_email' ),
		'website'   => home_url(),
		'version'   => SLATE_PRO_VERSION,
		'multisite' => $multisite,
	);
	$response = wp_remote_post( $server, array(
			'method'   => 'POST',
			'timeout'  => 5,
			'blocking' => false,
			'body'     => $args,
		)
	);
}

function slate_pro_licensing( $key, $remove ) {
	global $wp_version;
	if ( is_multisite() ) {
		$multisite = '1';
	} else {
		$multisite = '0';
	}
	$server = 'http://licenses.sevenbold.com/license.php';
	$args = array(
		'useragent' => $wp_version,
		'email'     => get_bloginfo( 'admin_email' ),
		'website'   => home_url(),
		'key'       => $key,
		'version'   => SLATE_PRO_VERSION,
		'remove'    => $remove,
		'multisite' => $multisite,
	);
	$response = wp_remote_post( $server, array(
			'method'      => 'POST',
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $args,
			'cookies'     => array()
		)
	);

	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		$licenseReply = array( 'body' => 'server' );

		return $licenseReply;
	} else {
		$licenseReply = $response;

		return $licenseReply;
	}
}

// plugins_loaded
add_action( 'plugins_loaded', 'slate_pro_plugins_loaded' );
function slate_pro_plugins_loaded() {
	global $slate_pro_settings;

	// Translations
	load_plugin_textdomain( 'slate-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Update DB
	slate_pro_check_db();

	// Update Version
	slate_pro_check_version();

	// Auto Update
	require( __DIR__ . '/inc/plugin-update-checker.php' );
	$slateUpdateCheck = PucFactory::buildUpdateChecker(
		'http://updates.sevenbold.com/update.php',
		__FILE__
	);
	function slate_license_key( $query ) {
		if ( is_multisite() && is_plugin_active_for_network( 'slate-pro-admin/slate-pro-admin.php' ) ) {
			$slate_pro_license = get_site_option( 'slate_pro_license' );
		} else {
			$slate_pro_license = get_option( 'slate_pro_license' );
		}
		$query['key'] = esc_attr( $slate_pro_license['licenseKey'] );
		$query['email'] = get_bloginfo( 'admin_email' );
		$query['website'] = home_url();

		return $query;
	}

	$slateUpdateCheck->addQueryArgFilter( 'slate_license_key' );

	// Admin Menu Permissions
	$menu_permission = slate_pro_get_user_permission();
	if ( ! empty( $menu_permission ) ) {
		if ( ! empty( $slate_pro_settings['adminMenuPermissions'][ $menu_permission ] ) && ( 'on' === $slate_pro_settings['adminMenuPermissions'][ $menu_permission ] ) ) {
			add_action( 'admin_menu', 'slate_pro_hide_admin_menus', 999 );
		}
	}

	// Slate Pro Plugin Permissions
	$plugin_permission = slate_pro_get_user_permission();
	if ( ! empty( $plugin_permission ) ) {
		if ( ! empty( $slate_pro_settings['userPermissions'][ $plugin_permission ] ) && ( 'on' === $slate_pro_settings['userPermissions'][ $plugin_permission ] ) ) {
			add_action( 'admin_menu', 'slate_pro_hide_plugin_menu' );
			add_action( 'admin_head', 'slate_pro_hide_plugin' );
		}
	}
}

// admin_enqueue_scripts
add_action( 'admin_enqueue_scripts', 'slate_pro_admin_enqueue' );
function slate_pro_admin_enqueue( $page ) {
	global $slate_pro_settings;

	wp_enqueue_style( 'slate-pro-admin', plugins_url( 'css/slate_pro.css', __FILE__ ) );
	wp_enqueue_script( 'slate-pro', plugins_url( 'js/slate_pro.js', __FILE__ ), array( 'jquery' ), SLATE_PRO_VERSION );

	// Branding Page
	if ( 'slate-pro_page_slate_pro_branding' === $page ) {
		wp_enqueue_media();
	}

	// Color Schemes Page
	if ( 'toplevel_page_slate_pro_color_schemes' === $page ) {
		wp_enqueue_style( 'spectrum-css', plugins_url( 'css/spectrum.css', __FILE__ ) );
		wp_enqueue_script( 'spectrum-js', plugins_url( 'js/spectrum.js', __FILE__ ), array( 'jquery' ), SLATE_PRO_VERSION );
	}

	// Admin Logo Present
	if ( $adminLogo = $slate_pro_settings['adminLogo'] ) {
		wp_localize_script( 'slate-pro', 'slate_adminLogo', esc_url( $slate_pro_settings['adminLogo'] ) );
	}

	// Hide User Profile Colors
	if ( 'on' === $slate_pro_settings['colorsHideUserProfileColors'] ) {
		wp_localize_script( 'slate-pro', 'slate_colorsHideUserProfileColors', esc_attr( $slate_pro_settings['colorsHideUserProfileColors'] ) );
	}

}

// login_enqueue_scripts
add_action( 'login_enqueue_scripts', 'slate_pro_login_enqueue' );
function slate_pro_login_enqueue() {
	wp_enqueue_style( 'slate-pro-admin', plugins_url( 'css/slate_pro.css', __FILE__ ) );
}

// wp_head
// Add Admin Bar styles to front end
add_action( 'wp_head', 'slate_pro_wp_head' );
function slate_pro_wp_head() {
	if ( is_admin_bar_showing() ) {
		$slate_pro_settings = slate_pro_get_settings();

		// Color Schemes and Options
		include( __DIR__ . '/css/dynamic_css_adminbar.php' );

		// Hide Admin Bar
		if ( 'on' === $slate_pro_settings['adminBarHide'] ) { ?>
			<style type="text/css" media="screen">
				/* Admin Bar */
				#wpadminbar {
					display: none;
				}

				#wpbody,
				.folded #wpbody {
					padding-top: 0;
				}

				@media only screen and (max-width: 782px) {
					#wpadminbar {
						display: block;
						visibility: hidden;
					}

					#wp-admin-bar-menu-toggle {
						visibility: visible;
					}

					#wpadminbar #adminbarsearch:before, #wpadminbar .ab-icon:before, #wpadminbar .ab-item:before, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon {
						color: #333;
					}

					.wp-responsive-open #wpadminbar #wp-admin-bar-menu-toggle a {
						background: #fff;
					}

					#wpbody,
					.folded #wpbody {
						padding-top: 46px;
					}
				}
			</style>
		<?php }

		// Hide the WP Logo from the Admin Bar
		if ( 'on' === $slate_pro_settings['adminBarHideWP'] ) { ?>
			<style type="text/css" media="screen">
				/* Admin Bar WordPress Logo */
				#wpadminbar li#wp-admin-bar-wp-logo {
					display: none;
				}
			</style>
		<?php }
	}
}
// admin_head
add_action( 'admin_head', 'slate_pro_admin_head' );
function slate_pro_admin_head() {
	global $slate_pro_settings;

	// Color Schemes and Options
	include( __DIR__ . '/css/dynamic_css.php' );
	include( __DIR__ . '/css/dynamic_css_adminbar.php' );

	// Favicon
	if ( $adminFavicon = $slate_pro_settings['adminFavicon'] ) {
		echo '<link rel="shortcut icon" href="' . esc_url( $adminFavicon ) . '">';
	}

	// Admin Menu
	if ( '' !== $slate_pro_settings['adminLogo'] ) { ?>
		<style type="text/css" media="screen">
			/* Admin Bar WordPress Logo */
			#adminmenu {
				margin: 0 !important;
			}
		</style>
	<?php }
	if ( '' !== $slate_pro_settings['adminLogoFolded'] ) { ?>
		<style type="text/css" media="screen">
			/* Admin Bar WordPress Logo */
			#adminmenu .folded {
				margin: 0 0 12px 0 !important;
			}
		</style>
	<?php }

	// Hide User Profile Colors
	if ( 'on' === $slate_pro_settings['colorsHideUserProfileColors'] ) { ?>
		<style type="text/css" media="screen">
			/* User Profile Color Options */
			.profile-php #color-picker {
				display: none;
			}
		</style>
	<?php }

	// Hide Admin Bar
	if ( 'on' === $slate_pro_settings['adminBarHide'] ) { ?>
		<style type="text/css" media="screen">
			/* Admin Bar */
			#wpadminbar {
				display: none;
			}

			#wpbody,
			.folded #wpbody {
				padding-top: 0;
			}

			@media only screen and (max-width: 782px) {
				#wpadminbar {
					display: block;
					visibility: hidden;
				}

				#wp-admin-bar-menu-toggle {
					visibility: visible;
				}

				#wpadminbar #adminbarsearch:before, #wpadminbar .ab-icon:before, #wpadminbar .ab-item:before, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon {
					color: #333;
				}

				.wp-responsive-open #wpadminbar #wp-admin-bar-menu-toggle a {
					background: #fff;
				}

				#wpbody,
				.folded #wpbody {
					padding-top: 46px;
				}
			}
		</style>
	<?php }

	// Hide the WP Logo from the Admin Bar
	if ( 'on' === $slate_pro_settings['adminBarHideWP'] ) { ?>
		<style type="text/css" media="screen">
			/* Admin Bar WordPress Logo */
			#wpadminbar li#wp-admin-bar-wp-logo {
				display: none;
			}
		</style>
	<?php }

	// Hide Footer
	if ( 'on' === $slate_pro_settings['footerHide'] ) { ?>
		<style type="text/css" media="screen">
			/* Footer */
			#wpfooter {
				display: none;
			}
		</style>
	<?php }

	// Hide Help Tab
	if ( 'on' === $slate_pro_settings['contentHideHelp'] ) { ?>
		<style type="text/css" media="screen">
			/* Help Tab */
			#contextual-help-link-wrap {
				display: none;
			}
		</style>
	<?php }

	// Hide Screen Options Tab
	if ( 'on' === $slate_pro_settings['contentHideScreenOptions'] ) { ?>
		<style type="text/css" media="screen">
			/* Screen Options Tab */
			#screen-options-link-wrap {
				display: none;
			}
		</style>
	<?php }

	// Hide Updates
	if ( 'on' === $slate_pro_settings['noticeHideAllUpdates'] ) { ?>
		<style type="text/css" media="screen">
			#wp-admin-bar-updates,
			.theme-update,
			.update-message,
			.update-nag,
			.update-plugins,
			#menu-update {
				display: none !important;
			}
		</style>
	<?php }
}

// admin_title
add_filter( 'admin_title', 'slate_pro_admin_title', 10, 2 );
function slate_pro_admin_title( $admin_title, $title ) {
	global $slate_pro_settings;

	if ( 'on' === $slate_pro_settings['contentHideWPTitle'] ) {
		return $title . ' &lsaquo; ' . get_bloginfo( 'name' );
	} else {
		return $admin_title;
	}
}

// login_head
add_action( 'login_head', 'slate_pro_login_head' );
function slate_pro_login_head() {
	global $slate_pro_settings;

	// Color Schemes and Options
	include( __DIR__ . '/css/dynamic_css.php' );

	// Favicon
	if ( '' !== $slate_pro_settings['adminFavicon'] ) {
		echo '<link rel="shortcut icon" href="' . esc_url( $slate_pro_settings['adminFavicon'] ) . '">';
	}

	// Login Logo
	if ( '' !== $slate_pro_settings['loginLogo'] ) { ?>
		<style type="text/css" media="screen">
			/* Login Logo */
			body.login div#login h1 a {
				background-image: url('<?php echo esc_url( $slate_pro_settings['loginLogo'] ); ?>');
				background-size: contain;
				width: 100%;
			}
		</style>
	<?php }

	// Hide Login Logo
	if ( '' !== $slate_pro_settings['loginLogoHide'] ) { ?>
		<style type="text/css" media="screen">
			/* Login Logo */
			body.login div#login h1 {
				display: none;
			}
		</style>
	<?php }

	// Login Background
	if ( '' !== $slate_pro_settings['loginBgImage'] ) { ?>
		<style type="text/css" media="screen">
			/* Login Background Image */
			body.login {
				background-image: url('<?php echo esc_url( $slate_pro_settings['loginBgImage'] ); ?>');
				background-position: <?php echo esc_attr( $slate_pro_settings['loginBgPosition'] ); ?>;
				background-repeat: <?php echo esc_attr( $slate_pro_settings['loginBgRepeat'] ); ?>;
				width: 100%;
			<?php if ( 'on' === $slate_pro_settings['loginBgFull' ] ) { ?> background-attachment: fixed;
				background-size: cover;
			<?php } ?>
			}
		</style>
	<?php }
}

// Login Link Text and Address
add_filter( 'login_headerurl', 'slate_pro_login_url' );
function slate_pro_login_url() {
	global $slate_pro_settings;

	$loginUrl = $slate_pro_settings['loginLinkUrl'];

	return $loginUrl;
}
add_filter( 'login_headertitle', 'slate_pro_login_title' );
function slate_pro_login_title() {
	global $slate_pro_settings;

	$loginTitle = $slate_pro_settings['loginLinkTitle'];

	return $loginTitle;
}

// Get Current User Admin Color
function slate_pro_get_user_admin_color() {
	$user_id = get_current_user_id();
	$user_info = get_userdata( $user_id );
	if ( ! ( $user_info instanceof WP_User ) ) {
		return;
	}
	$user_admin_color = $user_info->admin_color;

	return $user_admin_color;
}

// Footer Options
add_filter( 'admin_footer_text', 'slate_pro_admin_footer_text' );
function slate_pro_admin_footer_text() {
	global $slate_pro_settings;

	if ( 'on' === $slate_pro_settings['footerTextShow'] ) {
		$footerText = ( $slate_pro_settings['footerText'] ) ? $slate_pro_settings['footerText'] : '';
		$footerText = wp_kses_post( force_balance_tags( $footerText ) );
	} else {
		$footerText = 'Admin Theme <a href="http://sevenbold.com/wordpress/slate-pro/" target="_blank">Slate Pro</a> by <a href="http://sevenbold.com/wordpress/" target="_blank">Seven Bold</a>';
	}

	return $footerText;
}

add_action( 'admin_menu', 'slate_pro_footer_hide_ver' );
function slate_pro_footer_hide_ver() {
	global $slate_pro_settings;

	if ( 'on' === $slate_pro_settings['footerVersionHide'] ) {
		remove_filter( 'update_footer', 'core_update_footer' );
	}
}

// Admin Menu
function slate_pro_admin_menus() {
	global $menu;

	$i = 1;
	foreach ( $menu as $menuOrder => $menuItem ) {
		if ( 'Slate Pro Admin Logo' !== $menuItem[0] && 'Slate Pro Admin Logo Folded' !== $menuItem[0] ) {
			if ( ! empty( $menuItem[0] ) ) {
				$getJustName = explode( ' ', $menuItem[0] );
				if ( ( 'Plugins' == $getJustName[0] ) || ( 'Comments' == $getJustName[0] ) || ( 'Themes' === $getJustName[0] ) || ( 'Updates' === $getJustName[0] ) ) {
					$menuTitle = $getJustName[0];
				} else {
					$menuTitle = $menuItem[0];
				}
			} else {
				$menuTitle = 'Menu Separator ' . $i;
				$i ++;
			}
			$theMenu[] = array(
				'Sort'  => $menuOrder,
				'Title' => $menuTitle,
				'Slug'  => $menuItem[2],
				'Hide'  => '0',
			);
		}
	}

	return $theMenu;
}

function slate_pro_hide_admin_menus() {
	global $slate_pro_settings;
	if ( ! isset( $slate_pro_settings['adminMenu'] ) ) {
		return;
	} else {
		foreach ( $slate_pro_settings['adminMenu'] as $menuItem => $menuHide ) {

			$menuItem = unserialize( base64_decode( $menuItem ) );

			if ( 'on' === $menuHide ) {
				remove_menu_page( $menuItem['Slug'] );
			}
		}
	}
}

// User Permissions
function slate_pro_get_user_permission() {
	$user_id = get_current_user_id();
	$user_info = get_userdata( $user_id );
	if ( ! ( $user_info instanceof WP_User ) ) {
		return;
	}
	$username = $user_info->user_login;

	return $username;
}

function slate_pro_hide_plugin_menu() {
	remove_menu_page( 'slate_pro_color_schemes' );
}

function slate_pro_hide_plugin() { ?>
	<style type="text/css" media="screen">
		/* Admin Bar */
		#slate-pro-admin {
			display: none;
		}
	</style>
<?php }

// Dashboard
// Display Custom Widget
add_action( 'wp_dashboard_setup', 'slate_pro_dashboard_setup' );
function slate_pro_dashboard_setup() {
	global $wp_meta_boxes;
	global $slate_pro_settings;

	// Dashboard Welcome Message
	if ( 'on' === $slate_pro_settings['dashboardHideWelcome'] ) {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	if ( 'on' === $slate_pro_settings['dashboardCustomWidget'] ) {
		$widgetTitle = $slate_pro_settings['dashboardCustomWidgetTitle'];
		wp_add_dashboard_widget( 'slate_pro_dashboard_widget', esc_attr( $widgetTitle ), 'slate_pro_dashboard_widget_display' );

		// Move custom widget to top
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$example_widget_backup = array( 'slate_pro_dashboard_widget' => $normal_dashboard['slate_pro_dashboard_widget'] );
		unset( $normal_dashboard['slate_pro_dashboard_widget'] );
		$sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
}
function slate_pro_dashboard_widget_display() {
	global $slate_pro_settings;
	$widgetText = $slate_pro_settings['dashboardCustomWidgetText'];
	echo '<div class="slate__customWidget">' . wp_kses_post( force_balance_tags( $widgetText ) ) . '</div>';
}

// Disabled Dashboard Widgets
add_action( 'admin_init', 'slate_pro_disabled_widgets' );
function slate_pro_disabled_widgets() {
	global $slate_pro_settings;

	if ( isset( $slate_pro_settings['dashboardWidgets'] ) ) {
		foreach ( $slate_pro_settings['dashboardWidgets'] as $key => $value ) {
			if ( 'on' === $value ) {
				add_action( 'wp_dashboard_setup', 'slate_pro_' . $key );
			}
		}
	}
}

function slate_pro_dashboardHideActivity() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
}

function slate_pro_dashboardHideNews() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
}

function slate_pro_dashboardRightNow() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
}

function slate_pro_dashboardRecentComments() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
}

function slate_pro_dashboardQuickPress() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
}

function slate_pro_dashboardRecentDrafts() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts'] );
}

function slate_pro_dashboardIncomingLinks() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
}

function slate_pro_dashboardPlugins() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
}

// Content
// Remove the hyphen before the post state
add_filter( 'display_post_states', 'slate_pro_post_state' );
function slate_pro_post_state( $post_states ) {
	if ( ! empty( $post_states ) ) {
		$state_count = count( $post_states );
		$i = 0;
		foreach ( $post_states as $state ) {
			++ $i;
			( $i === $state_count ) ? $sep = '' : $sep = '';
			echo '<span class="post-state">' . esc_attr( $state ) . esc_attr( $sep ) . '</span>';
		}
	}
}

// Notices
add_action( 'after_setup_theme', 'slate_pro_wp_notices' );
function slate_pro_wp_notices() {
	global $slate_pro_settings;

	$slate_pro_remove_notices = function ( $a ) {
		global $wp_version;
		return (object) array(
			'last_checked' => time(),
			'version_checked' => $wp_version,
			);
	};

	// Disable Core Updates
	if ( 'on' === $slate_pro_settings['noticeWPUpdate'] ) {
		add_filter( 'pre_site_transient_update_core', $slate_pro_remove_notices );
	}

	// Disable Theme Updates
	if ( 'on' === $slate_pro_settings['noticeThemeUpdate'] ) {
		add_filter( 'pre_site_transient_update_themes', $slate_pro_remove_notices );
	}

	// Disable Plugin Updates
	if ( 'on' === $slate_pro_settings['noticePluginUpdate'] ) {
		add_filter( 'pre_site_transient_update_plugins', $slate_pro_remove_notices );
	}
}
// Hide All Updates (Alternative to Disabling)
add_action( 'admin_menu', 'slate_pro_hide_all_updates' );
function slate_pro_hide_all_updates() {
	global $slate_pro_settings;
	global $menu;
	global $submenu;

	if ( 'on' === $slate_pro_settings['noticeHideAllUpdates'] ) {
		if ( is_multisite() && is_main_site() ) {
			remove_action( 'network_admin_notices', 'update_nag', 3 );
			remove_filter( 'update_footer', 'core_update_footer' );
			$menu[65][0] = 'Plugins';
			$submenu['index.php'][10][0] = 'Updates';
		} else {
			remove_action( 'admin_notices', 'update_nag', 3 );
			remove_filter( 'update_footer', 'core_update_footer' );
			$menu[65][0] = 'Plugins';
			$submenu['index.php'][10][0] = 'Updates';
		}
	}
}

// After Setup
add_action( 'after_setup_theme', 'slate_pro_add_editor_styles' );
function slate_pro_add_editor_styles() {
	add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ) );
}

// Activate Plugin
register_activation_hook( __FILE__, 'slate_pro_activate' );
register_activation_hook( __FILE__, 'slate_pro_initial_license' );
register_activation_hook( __FILE__, 'slate_pro_check_db' );
register_activation_hook( __FILE__, 'slate_pro_check_version' );
function slate_pro_activate() {

	date_default_timezone_set( 'America/Los_Angeles' );
	$date = date( 'Y-m-d H:i:s' );

	if ( is_multisite() ) {
		global $blog_id;
		$current_blog_details = get_blog_details( array( 'blog_id' => $blog_id ) );
		$loginLinkTitle = $current_blog_details->blogname;
		$loginLinkUrl = $current_blog_details->siteurl;
	} else {
		$loginLinkTitle = get_bloginfo( 'name' );
		$loginLinkUrl = get_bloginfo( 'url' );
	}

	$default_option = array(
		// Color Schemes
		'colorScheme'                 => 'default',
		'colorSchemeCustomColors'     => array(
			'loginBgColor'                                => '#444343',
			'loginFormBgColor'                            => '#eeecec',
			'loginFormTextColor'                          => '#777777',
			'loginFormInputBgColor'                       => '#fbfbfb',
			'loginFormInputTextColor'                     => '#333333',
			'loginFormInputFocusColor'                    => '#5b9dd9',
			'loginButtonBgColor'                          => '#2ea2cc',
			'loginButtonTextColor'                        => '#ffffff',
			'loginButtonHoverBgColor'                     => '#ffffff',
			'loginButtonHoverTextColor'                   => '#2ea2cc',
			'loginFormLinkColor'                          => '#eeebeb',
			'loginFormLinkHoverColor'                     => '#ffffff',
			'adminMenuBgColor'                            => '#302d2d',
			'adminMenuDividerColor'                       => '#262323',
			'adminNoticeColor'                            => '#ffffff',
			'adminNoticeBgColor'                          => '#d54e21',
			'adminTopLevelTextColor'                      => '#888888',
			'adminTopLevelTextHoverColor'                 => '#2ea2cc',
			'adminTopLevelSelectedTextColor'              => '#ea5340',
			'adminFloatingSubmenuBgColor'                 => '#2ea2cc',
			'adminFloatingSubmenuTextColor'               => '#ffffff',
			'adminFloatingSubmenuTextHoverColor'          => '#b9ecff',
			'adminOpenSubmenuTextColor'                   => '#bbbbbb',
			'adminOpenSubmenuTextHoverColor'              => '#ffffff',
			'adminOpenSubmenuTextSelectedColor'           => '#ffffff',
			'adminTopLevelSelectedFoldedBg'               => '#ea5340',
			'adminTopLevelFoldedTextColor'                => '#ffffff',
			'adminTopLevelSelectedFoldedTextColor'        => '#ffffff',
			'adminTopLevelSelectedFoldedIconColor'        => '#ffffff',
			'adminFoldedFloatingSubmenuTextColor'         => '#ffffff',
			'adminFoldedFloatingSubmenuTextHoverColor'    => '#ffd8d3',
			'adminFoldedFloatingSubmenuSelectedTextColor' => '#ffffff',
			'adminBarBgColor'                             => '#444343',
			'adminBarBgHoverColor'                        => '#333333',
			'adminBarTopLevelColor'                       => '#888888',
			'adminBarTopLevelHoverColor'                  => '#2ea2cc',
			'adminBarSubmenuTextColor'                    => '#eeeeee',
			'adminBarSubmenuTextHoverColor'               => '#2ea2cc',
			'footerBgColor'                               => '#444343',
			'footerTextColor'                             => '#999999',
			'footerLinkColor'                             => '#ffffff',
			'footerLinkHoverColor'                        => '#ffffff',
			'contentTextColor'                            => '#555555',
			'contentHeadingTextColor'                     => '#222222',
			'contentLinkColor'                            => '#0074a2',
			'contentLinkHoverColor'                       => '#2ea2cc',
			'contentTableRowBgHoverColor'                 => '#eeecec',
			'contentDividerColor'                         => '#eeecec',
			'contentPrimaryButtonBgColor'                 => '#2ea2cc',
			'contentPrimaryButtonTextColor'               => '#ffffff',
			'contentPrimaryButtonBgHoverColor'            => '#1e8cbe',
			'contentPrimaryButtonTextHoverColor'          => '#ffffff',
			'contentStandardButtonBgColor'                => '#dcd7d7',
			'contentStandardButtonTextColor'              => '#555555',
			'contentStandardButtonBgHoverColor'           => '#7d7878',
			'contentStandardButtonTextHoverColor'         => '#ffffff',
			'contentMetaBgColor'                          => '#eeecec',
			'contentMetaTextColor'                        => '#777777',
			'contentMetaBgHoverColor'                     => '#eeecec',
			'contentMetaTextHoverColor'                   => '#333333',
			'sidebarBgColor'                              => '#eeecec',
			'sidebarTextColor'                            => '#555555',
			'sidebarHeadingColor'                         => '#222222',
			'sidebarLinkColor'                            => '#0074a2',
			'sidebarLinkHoverColor'                       => '#2ea2cc',
			'sidebarIconColor'                            => '#555555',
			'sidebarDividerColor'                         => '#dad8d8',
			'sidebarPrimaryButtonBgColor'                 => '#2ea2cc',
			'sidebarPrimaryButtonTextColor'               => '#ffffff',
			'sidebarPrimaryButtonBgHoverColor'            => '#1e8cbe',
			'sidebarPrimaryButtonTextHoverColor'          => '#ffffff',
			'sidebarStandardButtonBgColor'                => '#dcd7d7',
			'sidebarStandardButtonTextColor'              => '#555555',
			'sidebarStandardButtonBgHoverColor'           => '#7d7878',
			'sidebarStandardButtonTextHoverColor'         => '#ffffff',
		),
		'colorsHideUserProfileColors' => '',
		'colorsHideShadows'           => '',
		// Login Page
		'loginLinkTitle'              => $loginLinkTitle,
		'loginLinkUrl'                => $loginLinkUrl,
		'loginLogoHide'               => '',
		'loginLogo'                   => plugins_url( '/images/slate_pro_login_logo.png', __FILE__ ),
		'loginBgImage'                => plugins_url( '/images/slate_pro_background.jpg', __FILE__ ),
		'loginBgPosition'             => 'center top',
		'loginBgRepeat'               => 'no-repeat',
		'loginBgFull'                 => 'on',
		// Admin Branding
		'adminLogo'                   => plugins_url( '/images/slate_pro_admin_logo.png', __FILE__ ),
		'adminLogoFolded'             => plugins_url( '/images/slate_pro_admin_logo_folded.png', __FILE__ ),
		'adminFavicon'                => plugins_url( '/images/slate_pro_favicon.png', __FILE__ ),
		// Admin Menu
		'adminMenu'                   => array(),
		'adminMenuPermissions'        => array(),
		// Admin Bar
		'adminBarHide'                => '',
		'adminBarHideWP'              => '',
		// Footer
		'footerTextShow'              => '',
		'footerVersionHide'           => '',
		'footerText'                  => '',
		'footerHide'                  => '',
		// Dashboard
		'dashboardHideWelcome'        => '',
		'dashboardWidgets'            => array(
			'dashboardHideActivity'   => '0',
			'dashboardHideNews'       => '0',
			'dashboardRightNow'       => '0',
			'dashboardRecentComments' => '0',
			'dashboardQuickPress'     => '0',
			'dashboardRecentDrafts'   => '0',
			'dashboardIncomingLinks'  => '0',
			'dashboardPlugins'        => '0',
		),
		'dashboardCustomWidget'       => '',
		'dashboardCustomWidgetTitle'  => '',
		'dashboardCustomWidgetText'   => '',
		// Content and Notices
		'noticeWPUpdate'              => '',
		'noticeThemeUpdate'           => '',
		'noticePluginUpdate'          => '',
		'noticeHideAllUpdates'        => '',
		'contentHideHelp'             => '',
		'contentHideScreenOptions'    => '',
		'contentHideWPTitle'          => '',
		// Permissions
		'userPermissions'             => array(),
		// Settings
		'customLogin'                 => '',
		'customLoginURL'              => '',
		// License
		'licenseDate'                 => $date,
	);

	if ( is_multisite() && is_main_site() ) {
		add_site_option( 'slate_pro_settings', $default_option );
	} else {
		add_option( 'slate_pro_settings', $default_option );
	}

	$license_options = array(
		'licenseKey'    => '',
		'licenseStatus' => '',
	);
	if ( is_multisite() && is_main_site() ) {
		add_site_option( 'slate_pro_license', $license_options );
	} else {
		add_option( 'slate_pro_license', $license_options );
	}

	if ( is_multisite() && is_main_site() ) {
		add_site_option( 'slate_pro_db', SLATE_PRO_DB );
	} else {
		add_option( 'slate_pro_db', SLATE_PRO_DB );
	}

	if ( is_multisite() && is_main_site() ) {
		add_site_option( 'slate_pro_version', SLATE_PRO_VERSION );
	} else {
		add_option( 'slate_pro_version', SLATE_PRO_VERSION );
	}
}

// Deactivate Plugin
register_deactivation_hook( __FILE__, 'slate_pro_deactivate' );
function slate_pro_deactivate() {

	if ( is_multisite() && is_main_site() ) {
		delete_site_option( 'slate_pro_settings' );
		delete_site_option( 'slate_pro_license' );
		delete_site_option( 'slate_pro_version' );
		delete_site_option( 'slate_pro_db' );
	} else {
		delete_option( 'slate_pro_settings' );
		delete_option( 'slate_pro_license' );
		delete_option( 'slate_pro_version' );
		delete_option( 'slate_pro_db' );
	}
}

// Sanitization
function slate_pro_save_settings_network() {
	$option = slate_pro_sanitize( $_POST['slate_pro_settings'] );

	if ( ! empty( $option ) ) {
		update_site_option( 'slate_pro_settings', $option );
	}

	wp_redirect( esc_url_raw( add_query_arg( array(
		'page'    => $option['currentPage'],
		'updated' => 'true',
		), network_admin_url( 'admin.php' ) ) ) );
	exit();
}
function slate_pro_sanitize( $input ) {

	// Color Schemes
	$input['colorScheme'] = ( empty( $input['colorScheme'] ) ) ? '' : esc_attr( $input['colorScheme'] );
	$input['colorsHideUserProfileColors'] = ( empty( $input['colorsHideUserProfileColors'] ) ) ? '' : 'on';
	$input['colorsHideShadows'] = ( empty( $input['colorsHideShadows'] ) ) ? '' : 'on';
	foreach ( $input['colorSchemeCustomColors'] as $key => $value ) {
		$input['colorSchemeCustomColors'][ $key ] = ( empty( $input['colorSchemeCustomColors'][ $key ] ) ) ? '' : slate_pro_sanitize_hex( $input['colorSchemeCustomColors'][ $key ] );
	}

	// Login Page
	$input['loginLinkTitle'] = ( empty( $input['loginLinkTitle'] ) ) ? '' : esc_attr( $input['loginLinkTitle'] );
	$input['loginLinkUrl'] = ( empty( $input['loginLinkUrl'] ) ) ? '' : esc_url( $input['loginLinkUrl'] );
	$input['loginLogo'] = ( empty( $input['loginLogo'] ) ) ? '' : esc_url( $input['loginLogo'] );
	$input['loginLogoHide'] = ( empty( $input['loginLogoHide'] ) ) ? '' : 'on';
	$input['loginBgPosition'] = ( empty( $input['loginBgPosition'] ) ) ? '' : esc_attr( $input['loginBgPosition'] );
	$input['loginBgRepeat'] = ( empty( $input['loginBgRepeat'] ) ) ? '' : esc_attr( $input['loginBgRepeat'] );
	$input['loginBgImage'] = ( empty( $input['loginBgImage'] ) ) ? '' : esc_url( $input['loginBgImage'] );
	$input['loginBgFull'] = ( empty( $input['loginBgFull'] ) ) ? '' : 'on';

	// Admin Branding
	$input['adminLogo'] = ( empty( $input['adminLogo'] ) ) ? '' : esc_url( $input['adminLogo'] );
	$input['adminLogoFolded'] = ( empty( $input['adminLogoFolded'] ) ) ? '' : esc_url( $input['adminLogoFolded'] );
	$input['adminFavicon'] = ( empty( $input['adminFavicon'] ) ) ? '' : esc_url( $input['adminFavicon'] );

	// Dashboard
	$input['dashboardHideWelcome'] = ( empty( $input['dashboardHideWelcome'] ) ) ? '' : 'on';
	$input['dashboardCustomWidget'] = ( empty( $input['dashboardCustomWidget'] ) ) ? '' : 'on';
	$input['dashboardCustomWidgetTitle'] = ( empty( $input['dashboardCustomWidgetTitle'] ) ) ? '' : esc_attr( $input['dashboardCustomWidgetTitle'] );
	$input['dashboardCustomWidgetText'] = ( empty( $input['dashboardCustomWidgetText'] ) ) ? '' : wp_kses_post( force_balance_tags( $input['dashboardCustomWidgetText'] ) );
	foreach ( $input['dashboardWidgets'] as $key => $value ) {
		$input['dashboardWidgets'][ $key ] = ( '0' == $input['dashboardWidgets'][ $key ] ) ? '' : 'on';
	}

	// Footer Settings
	$input['footerTextShow'] = ( empty( $input['footerTextShow'] ) ) ? '' : 'on';
	$input['footerVersionHide'] = ( empty( $input['footerVersionHide'] ) ) ? '' : 'on';
	$input['footerText'] = ( empty( $input['footerText'] ) ) ? '' : wp_kses_post( force_balance_tags( $input['footerText'] ) );
	$input['footerHide'] = ( empty( $input['footerHide'] ) ) ? '' : 'on';

	// Admin Bar Settings
	$input['adminBarHide'] = ( empty( $input['adminBarHide'] ) ) ? '' : 'on';
	$input['adminBarHideWP'] = ( empty( $input['adminBarHideWP'] ) ) ? '' : 'on';

	// Permission Settings
	foreach ( $input['userPermissions'] as $key => $value ) {
		$input['userPermissions'][ $key ] = ( '0' == $input['userPermissions'][ $key ] ) ? '' : 'on';
	}

	// Admin Menu
	if ( isset( $input['adminMenu'] ) ) {
		foreach ( $input['adminMenu'] as $menuItem => $menuHide ) {
			$menuHide = ( '0' === $value ) ? '' : 'on';
			$menuItem = unserialize( base64_decode( $menuItem ) );
			foreach ( $menuItem as $key => $value ) {
				$key = ( empty( $key ) ) ? '' : esc_attr( $key );
				$value = ( empty( $value ) ) ? '' : esc_attr( $value );
			}
		}
	}

	foreach ( $input['adminMenuPermissions'] as $key => $value ) {
		$input['adminMenuPermissions'][ $key ] = ( '0' === $input['adminMenuPermissions'][ $key ] ) ? '' : 'on';
	}

	// Notices
	$input['noticeWPUpdate'] = ( empty( $input['noticeWPUpdate'] ) ) ? '' : 'on';
	$input['noticeThemeUpdate'] = ( empty( $input['noticeThemeUpdate'] ) ) ? '' : 'on';
	$input['noticePluginUpdate'] = ( empty( $input['noticePluginUpdate'] ) ) ? '' : 'on';
	$input['noticeHideAllUpdates'] = ( empty( $input['noticeHideAllUpdates'] ) ) ? '' : 'on';
	$input['contentHideHelp'] = ( empty( $input['contentHideHelp'] ) ) ? '' : 'on';
	$input['contentHideScreenOptions'] = ( empty( $input['contentHideScreenOptions'] ) ) ? '' : 'on';
	$input['contentHideWPTitle'] = ( empty( $input['contentHideWPTitle'] ) ) ? '' : 'on';

	// Settings
	$input['customLogin'] = ( empty( $input['customLogin'] ) ) ? '' : 'on';
	$input['customLoginURL'] = ( empty( $input['customLoginURL'] ) ) ? '' : esc_url( $input['customLoginURL'] );

	// Hidden Inputs
	$input['licenseDate'] = ( empty( $input['licenseDate'] ) ) ? '' : esc_attr( $input['licenseDate'] );
	$input['currentPage'] = ( empty( $input['currentPage'] ) ) ? '' : esc_attr( $input['currentPage'] );

	return $input;

}

// Sanitize Hex Colors
function slate_pro_sanitize_hex( $color ) {
	if ( '' === $color ) {
		return '';
	}

	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return null;
}

// Settings Pages
function slate_pro_color_schemes() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_color_schemes';
	include( __DIR__ . '/inc/content.php' );

}

function slate_pro_branding() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_branding';
	include( __DIR__ . '/inc/content.php' );

}

function slate_pro_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_dashboard';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_admin_menu() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_admin_menu';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_admin_bar_footer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_admin_bar_footer';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_content_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_content_notices';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_permissions() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_permissions';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_settings';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_about() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_about';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_license() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_license';
	include( __DIR__ . '/inc/content.php' );
}

function slate_pro_import_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'slate-pro' ) );
	}

	$page = 'slate_pro_import_export';
	include( __DIR__ . '/inc/content.php' );
}