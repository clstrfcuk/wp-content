<?php

define( 'UBERMENU_EDD_UPDATES_URL', 'http://sevenspark.com' ); 
define( 'UBERMENU_ICONS_EDD_NAME', 'UberMenu Icons Extension' );
define( 'UBERMENU_STICKY_EDD_NAME', 'UberMenu Sticky Extension' );
define( 'UBERMENU_CONDITIONALS_EDD_NAME', 'UberMenu Conditionals Extension' );

//Define constants that will later be defined in UberMenu Condtionals Extension
if( !defined( 'UBERMENU_CONDITIONALS_PLUGIN_FILE' ) )
	define( 'UBERMENU_CONDITIONALS_PLUGIN_FILE' , dirname( UBERMENU_FILE ).'-conditionals/ubermenu-conditionals.php' );
if( !defined( 'UBERMENU_CONDITIONALS_VERSION' ) )
	define( 'UBERMENU_CONDITIONALS_VERSION' , '3.0' );


if( !defined( 'UBERMENU_EXT_ICONS_UPDATES_URL' ) )
	define( 'UBERMENU_EXT_ICONS_UPDATES_URL' , 'http://updates.sevenspark.com/ubermenu-icons' );	//TODO

if( !defined( 'UBERMENU_EXT_ICONS_UPDATE_NOTICES_KEY' ) ) 
	define( 'UBERMENU_EXT_ICONS_UPDATE_NOTICES_KEY' , '_ubermenu_ext_icons_update_errors' );


if( !defined( 'UBERMENU_EXT_STICKY_UPDATES_URL' ) )
	define( 'UBERMENU_EXT_STICKY_UPDATES_URL' , 'http://updates.sevenspark.com/ubermenu-sticky' );	//TODO

if( !defined( 'UBERMENU_EXT_STICKY_UPDATE_NOTICES_KEY' ) ) 
	define( 'UBERMENU_EXT_STICKY_UPDATE_NOTICES_KEY' , '_ubermenu_ext_sticky_update_errors' );


if( !defined( 'UBERMENU_EXT_CONDITIONALS_UPDATES_URL' ) )
	define( 'UBERMENU_EXT_CONDITIONALS_UPDATES_URL' , 'http://updates.sevenspark.com/ubermenu-conditionals' );	//TODO
if( !defined( 'UBERMENU_EXT_CONDITIONALS_UPDATE_NOTICES_KEY' ) ) 
	define( 'UBERMENU_EXT_CONDITIONALS_UPDATE_NOTICES_KEY' , '_ubermenu_ext_conditionals_update_errors' );


//Load EDD Updater
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/edd/EDD_SL_Plugin_Updater.php' );
}


if( ubermenu_extension_active( 'ubermenu-icons' ) ){
	ubermenu_register_extension(
		'ubermenu-icons',
		array(
			'license_field'	=> 'extensions_icons_license',
			'edd_name'		=> 'UberMenu Icons Extension',
			'version'		=> UM_ICONS_VERSION,
			'plugin_file'	=> UM_ICONS_PLUGIN_FILE,
		));
}
if( ubermenu_extension_active( 'ubermenu-sticky' ) ){
	ubermenu_register_extension(
		'ubermenu-sticky',
		array(
			'license_field'	=> 'extensions_sticky_license',
			'edd_name'		=> 'UberMenu Sticky Extension',
			'version'		=> UM_STICKY_VERSION,
			'plugin_file'	=> UM_STICKY_PLUGIN_FILE,
		));
}
if( ubermenu_extension_active( 'ubermenu-conditionals' ) ){
	ubermenu_register_extension(
		'ubermenu-conditionals',
		array(
			'license_field'	=> 'extensions_conditionals_license',
			'edd_name'		=> 'UberMenu Conditionals Extension',
			'version'		=> UBERMENU_CONDITIONALS_VERSION,
			'plugin_file'	=> UBERMENU_CONDITIONALS_PLUGIN_FILE,
		));
}


function ubermenu_update_extensions_setup(){


	$update_settings = ubermenu_updates_get_account_settings();


	//ICONS EXTENSION
	if( ubermenu_extension_active( 'ubermenu-icons' ) && isset( $update_settings['extensions_icons_license'] ) ){

		$license = trim( $update_settings['extensions_icons_license'] );

		switch( ubermenu_license_source( $license ) ){
			case 'envato':
				//PUC

				$icons_update_checker = new PluginUpdateChecker_3_2 (
					//$url,
					UBERMENU_EXT_ICONS_UPDATES_URL,
				    UM_ICONS_PLUGIN_FILE,
				    'ubermenu-icons',
				    UBERMENU_UPDATES_CHECK_PERIOD
				);

				//Just make sure we don't dump errors to the end user if the URL fails
				$icons_update_checker->debugMode = false;

				//Increase check interval if update alert is already present
				$icons_update_checker->throttleRedundantChecks = true;
				$icons_update_checker->throttledCheckPeriod = 120;

				$icons_update_checker->addQueryArgFilter( 'ubermenu_ext_icons_filter_update_checks' );
				$icons_update_checker->addResultFilter( 'ubermenu_ext_icons_filter_update_results' );

				break;

			case 'sevenspark':
				//EDD
				$edd_updater = new EDD_SL_Plugin_Updater( UBERMENU_EDD_UPDATES_URL, UM_ICONS_PLUGIN_FILE, array( 
						'version' 	=> UM_ICONS_VERSION, 	// current version number
						'license' 	=> $license, 			// license key (used get_option above to retrieve from DB)
						'item_name' => UBERMENU_ICONS_EDD_NAME, 		// name of this plugin
						'author' 	=> 'Chris Mavricos, SevenSpark',  	// author of this plugin
						'url'		=> home_url(),
					)
				);

				break;
		}

	}







	//STICKY EXTENSION
	if( ubermenu_extension_active( 'ubermenu-sticky' ) && isset( $update_settings['extensions_sticky_license'] ) ){
		$license = $update_settings['extensions_sticky_license'];

		switch( ubermenu_license_source( $license ) ){
			case 'envato':
				//PUC

				$sticky_update_checker = new PluginUpdateChecker_3_2 (
					//$url,
					UBERMENU_EXT_STICKY_UPDATES_URL,
				    UM_STICKY_PLUGIN_FILE,
				    'ubermenu-sticky',
				    UBERMENU_UPDATES_CHECK_PERIOD
				);

				//Just make sure we don't dump errors to the end user if the URL fails
				$sticky_update_checker->debugMode = false;

				//Increase check interval if update alert is already present
				$sticky_update_checker->throttleRedundantChecks = true;
				$sticky_update_checker->throttledCheckPeriod = 120;

				$sticky_update_checker->addQueryArgFilter( 'ubermenu_ext_sticky_filter_update_checks' );
				$sticky_update_checker->addResultFilter( 'ubermenu_ext_sticky_filter_update_results' );

				break;

			case 'sevenspark':
				//EDD
				$edd_updater = new EDD_SL_Plugin_Updater( UBERMENU_EDD_UPDATES_URL, UM_STICKY_PLUGIN_FILE, array( 
						'version' 	=> UM_STICKY_VERSION, 	// current version number
						'license' 	=> $license, 			// license key (used get_option above to retrieve from DB)
						'item_name' => UBERMENU_STICKY_EDD_NAME, 		// name of this plugin
						'author' 	=> 'Chris Mavricos, SevenSpark',  	// author of this plugin
						'url'		=> home_url(),
					)
				);
				break;
		}

	}



	//CONDITIONALS EXTENSION
	if( ubermenu_extension_active( 'ubermenu-conditionals' ) && isset( $update_settings['extensions_conditionals_license'] ) ){
		$license = $update_settings['extensions_conditionals_license'];

		switch( ubermenu_license_source( $license ) ){
			case 'envato':
				//PUC

				$conditionals_update_checker = new PluginUpdateChecker_3_2 (
					//$url,
					UBERMENU_EXT_CONDITIONALS_UPDATES_URL,
				    UBERMENU_CONDITIONALS_PLUGIN_FILE, // because it's not currently defined
				    'ubermenu-conditionals',
				    UBERMENU_UPDATES_CHECK_PERIOD
				);

				//Just make sure we don't dump errors to the end user if the URL fails
				$conditionals_update_checker->debugMode = false;

				//Increase check interval if update alert is already present
				$conditionals_update_checker->throttleRedundantChecks = true;
				$conditionals_update_checker->throttledCheckPeriod = 120;

				$conditionals_update_checker->addQueryArgFilter( 'ubermenu_ext_conditionals_filter_update_checks' );
				$conditionals_update_checker->addResultFilter( 'ubermenu_ext_conditionals_filter_update_results' );

				break;

			case 'sevenspark':
				//EDD
				$edd_updater = new EDD_SL_Plugin_Updater( UBERMENU_EDD_UPDATES_URL, UBERMENU_CONDITIONALS_PLUGIN_FILE, array( 
						'version' 	=> UBERMENU_CONDITIONALS_VERSION, 	// current version number
						'license' 	=> $license, 			// license key (used get_option above to retrieve from DB)
						'item_name' => UBERMENU_CONDITIONALS_EDD_NAME, 		// name of this plugin
						'author' 	=> 'Chris Mavricos, SevenSpark',  	// author of this plugin
						'url'		=> home_url(),
					)
				);
				break;
		}

	}



	//For now, not doing flat skins


}
//ubermenu_update_extensions_setup();
add_action( 'admin_init', 'ubermenu_update_extensions_setup', 0 );

function ubermenu_license_source( $license_key ){

	if( $license_key == '' ){
		return 'none';			//no license passed
	}

	$source = 'envato';
	//No hyphen, it's from sevenspark.com
	if( false === strpos( $license_key , '-' ) ){
		$source = 'sevenspark';
	}

	return $source;
}


function ubermenu_extension_active( $slug ){

	$active = false;

	switch( $slug ){

		case 'ubermenu-icons':
			if( defined( 'UM_ICONS_VERSION' ) ) $active = true;
			break;

		case 'ubermenu-sticky':
			if( defined( 'UM_STICKY_VERSION' ) ) $active = true;
			break;

		case 'ubermenu-conditionals':
			if( function_exists( 'ubermenu_conditionals' ) ) $active = true;
			break;

		case 'ubermenu-flat-skin-pack':
			if( function_exists( 'ubermenu_skins_flat_register_ubermenu_skins' ) ) $active = true;
			break;

	}

	return apply_filters( 'ubermenu_extension_active' , $active , $slug );

}


add_filter( 'ubermenu_updates_section' , 'ubermenu_updates_extensions_section' , 110 );
add_filter( 'ubermenu_settings_panel_fields' , 'ubermenu_updates_extensions_fields' , 110 );
function ubermenu_updates_extensions_section( $section ){
	$section['sub_sections']['extensions'] = array(
		'title'	=> __( 'Extensions' , 'ubermenu' ),
	);
	return $section;	
}
function ubermenu_updates_extensions_fields( $fields = array() ){

	$section = UBERMENU_PREFIX.'updates';

	$fields[$section][] = array(
			'name'	=> 'extensions_header',
			'label' => __( 'Extensions Licenses' , 'ubermenu' ),
			'desc'	=> __( 'Enter licenses for any extensions installed below to receive automatic updates.  ', 'ubermenu' ) . '<a target="_blank" href="http://sevenspark.com/goods/category/wordpress-plugins/ubermenu-extensions?src=plugin">View Extensions</a>',
			'type'	=> 'header',
			'group'	=> 'extensions',
		);

	//UBERMENU ICONS EXTENSION
	if( ubermenu_extension_active( 'ubermenu-icons' ) ){

		$notices = get_option( UBERMENU_EXT_ICONS_UPDATE_NOTICES_KEY , array() );
		$update_error = $desc = '';
		if( isset( $notices['errors'] ) ){
			foreach ( $notices['errors'] as $e ){
				if( is_string( $e ) ) $update_error.= "<div class='ubermenu-license-error'>UberMenu Icons update check error: $e</div>";
			}
			$desc.= $update_error;
		}
		$license_data = get_option( 'ubermenu-icons_license_data' );
		//uberp( $license_data );
		if( $license_data ){
			$license_status = $license_data->license;
			switch( $license_status ){
				case 'invalid':
					$desc.= '<div class="ubermenu-license-error">'.$license_data->error;
					if( $license_data->error == 'expired' ){
						$desc.= ' '.$license_data->expires;
					}
					$desc.= '</div>';
				case 'valid':
					//$desc = __( 'License is valid' , 'ubermenu' );
			}
		}

		$fields[$section][] = array(
			'name'	=> 'extensions_icons_license',
			'label' => __( 'Icons Extension License' , 'ubermenu' ),
			'desc'	=> $desc, //__( '', 'ubermenu' ),
			'type'	=> 'text',
			'group'	=> 'extensions',
		);
	}

	//UBERMENU STICKY EXTENSION
	if( ubermenu_extension_active( 'ubermenu-sticky' ) ){

		$notices = get_option( UBERMENU_EXT_STICKY_UPDATE_NOTICES_KEY , array() );
		$update_error = $desc = '';
		if( isset( $notices['errors'] ) ){
			foreach ( $notices['errors'] as $e ){
				if( is_string( $e ) ) $update_error.= "<div class='ubermenu-license-error'>UberMenu Sticky update check error: $e</div>";
			}
			$desc.= $update_error;
		}
		$license_data = get_option( 'ubermenu-sticky_license_data' );
		//uberp( $license_data );
		if( $license_data ){
			$license_status = $license_data->license;
			switch( $license_status ){
				case 'invalid':
					$desc.= '<div class="ubermenu-license-error">'.$license_data->error;
					if( $license_data->error == 'expired' ){
						$desc.= ' '.$license_data->expires;
					}
					$desc.= '</div>';
				case 'valid':
					//$desc = __( 'License is valid' , 'ubermenu' );
			}
		}

		$fields[$section][] = array(
			'name'	=> 'extensions_sticky_license',
			'label' => __( 'Sticky Extension License' , 'ubermenu' ),
			'desc'	=> $desc,
			'type'	=> 'text',
			'group'	=> 'extensions',
		);
	}


	//UBERMENU CONDITIONALS EXTENSION
	if( ubermenu_extension_active( 'ubermenu-conditionals' ) ){

		$notices = get_option( UBERMENU_EXT_CONDITIONALS_UPDATE_NOTICES_KEY , array() );
		$update_error = $desc = '';
		if( isset( $notices['errors'] ) ){
			foreach ( $notices['errors'] as $e ){
				if( is_string( $e ) ) $update_error.= "<div class='ubermenu-license-error'>UberMenu Conditionals update check error: $e</div>";
			}
			$desc.= $update_error;
		}
		$license_data = get_option( 'ubermenu-conditionals_license_data' );
		//uberp( $license_data );
		if( $license_data ){
			$license_status = $license_data->license;
			switch( $license_status ){
				case 'invalid':
					$desc.= '<div class="ubermenu-license-error">'.$license_data->error;
					if( $license_data->error == 'expired' ){
						$desc.= ' '.$license_data->expires;
					}
					$desc.= '</div>';
				case 'valid':
					//$desc = __( 'License is valid' , 'ubermenu' );
			}
		}

		$fields[$section][] = array(
			'name'	=> 'extensions_conditionals_license',
			'label' => __( 'Conditionals Extension License' , 'ubermenu' ),
			'desc'	=> $desc,
			'type'	=> 'text',
			'group'	=> 'extensions',
		);

	}


	
	//UBERMENU FLAT SKINS PACK EXTENSION
	if( ubermenu_extension_active( 'ubermenu-flat-skin-pack' ) ){
		$fields[$section][] = array(
			'name'	=> 'extensions_flat_skins_pack_license',
			'label' => __( 'Flat Skins Pack License' , 'ubermenu' ),
			'desc'	=> __( '', 'ubermenu' ),
			'type'	=> 'text',
			'group'	=> 'extensions',
		);
	}


	//Check for each extension in turn
	//Also provide hook for new extensions


//uberp( $fields[$section] , 3 );
	return $fields;

}





//PUC FILTERS
//Add the license key to query arguments.

//ICONS
function ubermenu_ext_icons_filter_update_checks( $queryArgs ) {

    $settings = ubermenu_updates_get_account_settings();
    //unset?

	foreach( $settings as $key => $val ){
		$queryArgs[$key] = urlencode( $val );
	}

	$queryArgs['site_url'] = get_site_url( null , '' , 'http' );
	$queryArgs['ubermenu_version'] = UM_ICONS_VERSION;
	$queryArgs['auto_updates'] = UBERMENU_AUTO_UPDATES;

	$queryArgs['core_license'] = $queryArgs['purchase_code'];
	$queryArgs['purchase_code'] = $queryArgs['extensions_icons_license'];
// uberp( $queryArgs );
// die();    
    return $queryArgs;
}

function ubermenu_ext_icons_filter_update_results( $pluginInfo, $result ){

	$notices = array();
// uberp( $pluginInfo );
// uberp( $result );
// die();
	if( isset( $pluginInfo->error ) ){
		//$notices = get_option( UBERMENU_UPDATE_NOTICES_KEY , array() );
		if( !isset( $notices['errors'] ) ) $notices['errors'] = array();
		$notices['errors'][] = $pluginInfo->error;
	}

	update_option( UBERMENU_EXT_ICONS_UPDATE_NOTICES_KEY , $notices );

	return $pluginInfo;

}

//$file = basename( dirname( UBERMENU_FILE ) ) . '/' . basename( UBERMENU_FILE );
add_action( "after_plugin_row_ubermenu-icons/ubermenu.icons.php" , 'ubermenu_ext_icons_plugin_display_notice' , 10 , 2 );

function ubermenu_ext_icons_plugin_display_notice( $file , $plguin_data ){
	$notices = get_option( UBERMENU_EXT_ICONS_UPDATE_NOTICES_KEY , array() );
	if( isset( $notices['errors'] ) ){
		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange">';
		foreach ( $notices['errors'] as $e ){
			if( is_string( $e ) ) echo "<div class='update-message'>UberMenu Icons update check error: $e</div>";
			else uberp( $e,4);
		}
		echo '</td></tr>';
	}
}




//STICKY
function ubermenu_ext_sticky_filter_update_checks( $queryArgs ) {

    $settings = ubermenu_updates_get_account_settings();
    //unset?

	foreach( $settings as $key => $val ){
		$queryArgs[$key] = urlencode( $val );
	}

	$queryArgs['site_url'] = get_site_url( null , '' , 'http' );
	$queryArgs['ubermenu_version'] = UM_STICKY_VERSION;
	$queryArgs['auto_updates'] = UBERMENU_AUTO_UPDATES;

	$queryArgs['core_license'] = $queryArgs['purchase_code'];
	$queryArgs['purchase_code'] = $queryArgs['extensions_sticky_license'];
// uberp( $queryArgs );
// die();    
    return $queryArgs;
}

function ubermenu_ext_sticky_filter_update_results( $pluginInfo, $result ){

	$notices = array();
// uberp( $pluginInfo );
// uberp( $result );
// die();
	if( isset( $pluginInfo->error ) ){
		//$notices = get_option( UBERMENU_UPDATE_NOTICES_KEY , array() );
		if( !isset( $notices['errors'] ) ) $notices['errors'] = array();
		$notices['errors'][] = $pluginInfo->error;
	}

	update_option( UBERMENU_EXT_STICKY_UPDATE_NOTICES_KEY , $notices );

	return $pluginInfo;

}

add_action( "after_plugin_row_ubermenu-sticky/ubermenu-sticky.php" , 'ubermenu_ext_sticky_plugin_display_notice' , 10 , 2 );
function ubermenu_ext_sticky_plugin_display_notice( $file , $plguin_data ){
	$notices = get_option( UBERMENU_EXT_STICKY_UPDATE_NOTICES_KEY , array() );
	if( isset( $notices['errors'] ) ){
		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange">';
		foreach ( $notices['errors'] as $e ){
			if( is_string( $e ) ) echo "<div class='update-message'>UberMenu Sticky update check error: $e</div>";
			else uberp( $e,4);
		}
		echo '</td></tr>';
	}
}







//CONDITIONALS
function ubermenu_ext_conditionals_filter_update_checks( $queryArgs ) {

    $settings = ubermenu_updates_get_account_settings();
    //unset?

	foreach( $settings as $key => $val ){
		$queryArgs[$key] = urlencode( $val );
	}

	$queryArgs['site_url'] = get_site_url( null , '' , 'http' );
	$queryArgs['ubermenu_version'] = defined( 'UBERMENU_CONDITIONALS_VERSION' ) ? UBERMENU_CONDITIONALS_VERSION : '3.0';
	$queryArgs['auto_updates'] = UBERMENU_AUTO_UPDATES;

	$queryArgs['core_license'] = $queryArgs['purchase_code'];
	$queryArgs['purchase_code'] = $queryArgs['extensions_conditionals_license'];
// uberp( $queryArgs );
// die();    
    return $queryArgs;
}

function ubermenu_ext_conditionals_filter_update_results( $pluginInfo, $result ){

	$notices = array();

	if( isset( $pluginInfo->error ) ){
		//$notices = get_option( UBERMENU_UPDATE_NOTICES_KEY , array() );
		if( !isset( $notices['errors'] ) ) $notices['errors'] = array();
		$notices['errors'][] = $pluginInfo->error;
	}

	update_option( UBERMENU_EXT_CONDITIONALS_UPDATE_NOTICES_KEY , $notices );

	return $pluginInfo;

}

add_action( "after_plugin_row_ubermenu-conditionals/ubermenu-conditionals.php" , 'ubermenu_ext_conditionals_plugin_display_notice' , 10 , 2 );
function ubermenu_ext_conditionals_plugin_display_notice( $file , $plguin_data ){
	$notices = get_option( UBERMENU_EXT_CONDITIONALS_UPDATE_NOTICES_KEY , array() );
	if( isset( $notices['errors'] ) ){
		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange">';
		foreach ( $notices['errors'] as $e ){
			if( is_string( $e ) ) echo "<div class='update-message'>UberMenu Conditionals update check error: $e</div>";
			else uberp( $e,4);
		}
		echo '</td></tr>';
	}
}





function ubermenu_edd_activate_licenses( $old_value , $value , $option ) {

	$extensions = ubermenu_get_extensions();
	foreach( $extensions as $extension_id => $data ){

		$status_key = $extension_id.'_license_status';
		$data_key = $extension_id.'_license_data';

		$license = trim( ubermenu_op( $data['license_field'] , 'updates' ) );
		
		//Only process license if it's a sevenspark.com license
		if( ubermenu_license_source( $license ) != 'sevenspark' ){
			continue;
		}

		if( $license == '' ){
			update_option( $status_key , '' );
			update_option( $data_key , '' );
			continue;
		}

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $data['edd_name'] ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, UBERMENU_EDD_UPDATES_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			continue;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $data_key , $license_data );


	}

}
add_action( 'update_option_'.UBERMENU_PREFIX.'updates' , 'ubermenu_edd_activate_licenses' , 10 , 3 );



function ubermenu_register_extension( $extension_slug , $data ){
	$um = _UBERMENU();
	$um->register_extension( $extension_slug , $data );
}
function ubermenu_get_extension_data( $extension_slug ){
	$um = _UBERMENU();
	return $um->get_extension_data( $extension_slug );
}
function ubermenu_get_extensions(){
	$um = _UBERMENU();
	return $um->get_extensions();
}
// 'ubermenu-icons',
// array(
// 	'license_field'	=> 'extensions_icons_license',
// 	'edd_name'		=> 'UberMenu Icons Extension',
// 	'version'		=> UM_ICONS_VERSION,
// 	'plugin_file'	=> UM_ICONS_PLUGIN_FILE,
// ));

