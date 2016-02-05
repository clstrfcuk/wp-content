<?php
/**
* Admin Config Functions
*
* Various functions relating to the various administration screens
*
* @package	Artiss-Transient-Cleaner
* 
*/

/**
* Add meta to plugin details
*
* Add options to plugin meta line
*
* @since	1.2.1
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   string			Links, now with settings added
*/

function atc_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'artiss-transient-cleaner.php' ) !== false ) {

		$links = array_merge( $links, array( '<a href="http://wordpress.org/support/plugin/artiss-transient-cleaner">' . __( 'Support', 'artiss-transient-cleaner' ) . '</a>' ) );
		$links = array_merge( $links, array( '<a href="http://www.artiss.co.uk/donate">' . __( 'Donate', 'artiss-transient-cleaner' ) . '</a>' ) );		
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'atc_set_plugin_meta', 10, 2 );

/**
* Add Settings link to plugin list
*
* Add a Settings link to the options listed against this plugin
*
* @since	1.3
*
* @param	string  $links	Current links
* @param	string  $file	File in use
* @return   string			Links, now with settings added
*/

function atc_add_settings_link( $links, $file ) {

	static $this_plugin;

	if ( !$this_plugin ) { $this_plugin = plugin_basename( __FILE__ ); }

	if ( strpos( $file, 'artiss-transient-cleaner.php' ) !== false ) {
		$settings_link = '<a href="tools.php?page=atc-options">' . __( 'Settings', 'artiss-transient-cleaner' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'atc_add_settings_link', 10, 2 );

/**
* Show Admin Messages
*
* Display messages on the administration screen
*
* @since	1.2
*
*/

function atc_show_admin_messages() {

	global $_wp_using_ext_object_cache;

	if ( $_wp_using_ext_object_cache ) {
		echo '<div id="message" class="error" style="font-weight: bold; text-align: center"><p>' . __( 'An external object cache is in use so Transient Cleaner is not required. Please disable the plugin!', 'artiss-transient-cleaner' ) . '</p></div>';
	}
}

add_action( 'admin_notices', 'atc_show_admin_messages' );

/**
* Admin Screen Initialisation
*
* Set up admin menu
*
* @since	1.2
*/

function atc_menu_initialise() {

	global $_wp_using_ext_object_cache;

	if ( !$_wp_using_ext_object_cache ) {

		global $atc_options_hook;

		$atc_options_hook = add_submenu_page( 'tools.php', __( 'Transient Cleaner Options', 'artiss-transient-cleaner' ),  __( 'Transients', 'artiss-transient-cleaner' ), 'install_plugins', 'atc-options', 'atc_options' );

		add_action( 'load-' . $atc_options_hook, 'atc_add_options_help' );
	}
}

add_action( 'admin_menu', 'atc_menu_initialise' );

/**
* Include options screen
*
* XHTML screen to prompt and update settings
*
* @since	1.2
*/

function atc_options() {

	include_once( WP_PLUGIN_DIR . '/artiss-transient-cleaner/includes/options-general.php' );

}

/**
* Add Options Help
*
* Add help tab to options screen
*
* @since	1.2
*
* @uses     atc_options_help    Return help text
*/

function atc_add_options_help() {

	global $atc_options_hook;
	$screen = get_current_screen();

	if ( $screen->id != $atc_options_hook ) { return; }

	$screen -> add_help_tab( array( 'id' => 'atc-options-help-tab', 'title'	=> __( 'Help', 'artiss-transient-cleaner' ), 'content' => atc_options_help() ) );
}

/**
* Options Help
*
* Return help text for options screen
*
* @since	1.2
*
* @return	string	Help Text
*/

function atc_options_help() {

	$help_text = '<p>' . __( 'This screen allows you to specify the default options for the Transient Cleaner plugin.', 'artiss-transient-cleaner' ) . '</p>';
	$help_text .= '<p>' . __( "In addition, details of recent transient cleans are shown. The 'Run Now' button will cause a clean (whether a full removal of transients or just the removal of expired tranients) to be performed.", 'artiss-transient-cleaner' ) . '</p>';
	$help_text .= '<p>' . __( 'Remember to click the Save Settings button at the bottom of the screen for new settings to take effect.', 'artiss-transient-cleaner' ) . '</p></h4>';

	return $help_text;
}
?>