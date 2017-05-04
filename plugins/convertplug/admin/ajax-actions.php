<?php

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

require_once( "import-ajax.php" );

$ajax_events = array(
	"cp_trash_list"                  => false,
	"update_style_settings"  		 => false, 					
	"update_variant_test_settings"   => false,
	"smile_duplicate_style"          => false,
	"smile_delete_style"			 => false,	
	"cp_reset_analytics_action"      => false,
	"smile_update_modules"           => false,
	"smile_update_global"            => false,
	"smile_update_status"            => false,
	"smile_update_impressions"       => true,
	"smile_add_list"                 => false,
	"cp_add_subscriber"              => true,
	"get_campaign_analytics_data"    => false,
	"get_campaign_daywise_data"      => false,
	"get_style_analytics_data"       => false,
	"isCampaignExists"               => false,
	"smile_update_settings"          => false,
	"smile_update_debug"             => false,
	"cp_is_list_assigned"            => false, 
	"cp_get_posts_by_query"          => false,
	"cp_get_active_campaigns"        => false,
	"cp_import_presets"              => false,
	"cp_import_presets_step2"        => false 
);

foreach ( $ajax_events as $event_slug => $is_nopriv ) {
	
	add_action( 'wp_ajax_' .$event_slug , $event_slug );
	
	if( $is_nopriv ) {
		add_action( 'wp_ajax_nopriv_' .$event_slug , $event_slug );
	}
}

// Style export post actions
add_action( 'admin_post_cp_export_list', 'handle_cp_export_list_action' );
add_action( 'admin_post_cp_export_modal', 'cp_export_modal_action' );
add_action( 'admin_post_cp_export_infobar', 'cp_export_infobar_action' );
add_action( 'admin_post_cp_export_slidein', 'cp_export_slidein_action' );

/**
* Function to accept ajax call for deleting contact list
* @since 1.0
*/
if( !function_exists( "cp_trash_list" ) ){
	function cp_trash_list(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		check_admin_referer( "cp-delete-list", "security_nonce" );

		$lists = get_option('smile_lists');
		$list_id = esc_attr( $_POST['list_id'] );
		$mailer = esc_attr( $_POST['mailer'] );
		$list = $lists[$list_id];
		$listName = str_replace(" ","_",strtolower( trim( $list['list-name'] ) ) );

		if( $mailer !== "convert_plug" ){
			$contacts_option = "cp_".$mailer."_".$listName;
		} else {
			$contacts_option = "cp_connects_".$listName;
		}

		unset( $lists[$list_id] );

		// delete option which contains campaign contacts
		$deleted = delete_option( $contacts_option );
		$status = update_option( 'smile_lists', $lists);
		if( $status ){
			print_r(json_encode(array(
                'status' => 'success'
				)));
		} else {
			print_r(json_encode(array(
                'status' => 'error'
				)));
		}
		die();
	}
}

/**
* Function to accept ajax call for updating style settings
* @since 1.0
*/
if( !function_exists( "update_style_settings" ) ){
	function update_style_settings(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data = $_POST['style_settings'];
		$pairs = explode( "&", $data );
		$settings = array();

		foreach( $pairs as $pair ) {
			$pair = explode( "=", $pair );
			if( isset($settings[$pair[0]]) ) {
				$settings[$pair[0]] = $settings[$pair[0]].",".$pair[1];
			} else {
				$settings[$pair[0]] = $pair[1];
			}
		}

		$themeName = ucwords( str_replace( "_", " ", $settings['style'] ) );

		if( isset( $settings['style_preset'] ) ) {
			$themeName = ucwords(str_replace( "_", " ", $settings['style_preset'] ));
		}

		$option 						= $settings['option'];
		$prev_styles 					= get_option($option);
		$new_style 						= array();
		$style_id 						= isset($settings['style_id']) && $settings['style_id'] !== "" ? $settings['style_id'] : $themeName;
		$style_name 					= isset($settings['new_style']) && $settings['new_style'] !== "" ? $settings['new_style'] : $themeName;
		$style_settings 				= serialize($settings);
		$key 							= ( is_array( $prev_styles ) && !empty( $prev_styles ) ) ? search_style($prev_styles, $style_id) : null;
		$impressions 					= isset( $prev_styles[$key]['impressions'] ) ? $prev_styles[$key]['impressions'] : 0;
		$conversion 					= isset( $prev_styles[$key]['conversion'] ) ? $prev_styles[$key]['conversion'] : 0;
		$new_style['style_name'] 		= stripslashes($style_name);
		$new_style['style_id'] 			= $style_id;
		$new_style['style_settings'] 	= $style_settings;

		if( is_array($prev_styles) && !empty($prev_styles) ) {
			if( $key !== "" && is_numeric($key) ) {
				$prev_styles[$key] = $new_style;
			} else {
				array_push($prev_styles,$new_style);
			}
		} else {
			$prev_styles = array();
			array_push( $prev_styles, $new_style );
		}
		echo $style_name;
		update_option( $option, $prev_styles );
		die();
	}
}

/**
* Function to accept ajax call for updating variant test settings
* @since 1.0
*/
if( !function_exists( "update_variant_test_settings" ) ){
	function update_variant_test_settings() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data = $_POST['style_settings'];
		$pairs = explode( "&", $data );
		$settings = array();
		foreach( $pairs as $pair ){
			$pair = explode( "=", $pair );
			if( isset($settings[$pair[0]]) ) {
				$settings[$pair[0]] = $settings[$pair[0]].",".$pair[1];
			} else {
				$settings[$pair[0]] = $pair[1];
			}
		}

		$themeName = ucwords(str_replace( "_", " ", $settings['style'] ));

		$option 						= $settings['option'];
		$style 							= $settings['style'];
		$variant_style 					= $settings['style_id'];
		$prev_styles 					= get_option($option);
		$variant_arrays					= isset( $prev_styles[$variant_style] ) ? $prev_styles[$variant_style] : array();
		$new_style 						= array();
		$rand 							= substr(md5(uniqid()),rand(0,26),5);
		$dynamic_style_name 			= 'cp_id_'.$rand;
		$style_id 						= isset($settings['variant-style']) && $settings['variant-style'] !== "" ? $settings['variant-style'] : $themeName;
		$style_name 					= isset($settings['new_style']) && $settings['new_style'] !== "" ? $settings['new_style'] : $themeName;
		$style_settings 				= serialize($settings);
		$key 							= !empty( $variant_arrays ) ? search_style($variant_arrays, $style_id) : null;
		$impressions 					= isset( $variant_arrays[$key]['impressions'] ) ? $variant_arrays[$key]['impressions'] : 0;
		$conversion 					= isset( $variant_arrays[$key]['conversion'] ) ? $variant_arrays[$key]['conversion'] : 0;
		$new_style['style_name'] 		= stripslashes($style_name);
		$new_style['style_id'] 			= $style_id;
		$new_style['style_settings'] 	= $style_settings;

		if( is_array( $variant_arrays ) && !empty( $variant_arrays ) ){
			$ar_key = false;
			foreach( $variant_arrays as $key => $array ) {
				if( $style_id == $array['style_id'] ){
					unset( $prev_styles[$variant_style][$key] );
				}
			}

			$new_variant_test = array();
			$new_variant_test = $new_style;
			array_push($prev_styles[$variant_style],$new_variant_test);
		} else {
			$new_variant_test = $prev_styles[$variant_style] = array();
			$new_variant_test = $new_style;
			array_push($prev_styles[$variant_style],$new_variant_test);
		}

		update_option($option,$prev_styles);
		echo $style_id;
		die();
	}
}


/**
* Function to accept ajax call for duplicating styles
* @since 1.0
*/
if( !function_exists( "smile_duplicate_style" ) ){
function smile_duplicate_style(){

	if ( ! current_user_can( 'access_cp' ) ) {
		die(-1);
	}

	$style_id 		= isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';
	$option 		= isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';
	$module 		= isset( $_POST['module'] ) ? esc_attr( $_POST['module'] ) : '';
	$variant_id 	= isset( $_POST['variant_id'] ) ? esc_attr( $_POST['variant_id'] ) : '';
	$dataOption 	= 'smile_'.$module.'_styles';
	$styleScreen 	= ( isset( $_POST['stylescreen']) && $_POST['stylescreen'] !== '' ) ? esc_attr( $_POST['stylescreen'] ) : '';
	$prev_styles 	= get_option( $dataOption );
	$key = null;

	if( $prev_styles && $variant_id !== "" ) {
		$key = search_style( $prev_styles, $variant_id );
	} else {
		$key = search_style( $prev_styles, $style_id );
	}

	$rand = substr( md5( uniqid() ),rand( 0, 26 ), 5 );
	$dynamic_style_id = 'cp_id_'.$rand;
	$modal_arrays = array();

	$smile_variant_tests = array();
	$smile_variant_tests = get_option( $option );
	$modal_arrays = $smile_variant_tests;

	// if On variant screen
	if( $styleScreen == 'multivariant' ) {
		$new_variant_tests = array();
		if( isset( $smile_variant_tests[ $variant_id ] ) ) {
			$new_variant_tests = $smile_variant_tests[ $variant_id ];
		} else {
			$new_variant_tests = $smile_variant_tests[ $variant_id ] = array();
		}
		$modal_arrays = $smile_variant_tests;

		// Duplicating variant
		if( !empty( $new_variant_tests ) ){

			$match = false;
			foreach( $new_variant_tests as $vkey => $array ){

				// while duplicating variant on variant screen
				if( $array['style_id'] == $style_id ){
					$dynamic_style_id = 'cp_id_'.$rand;
					$new_style_id = $dynamic_style_id;
					$new_style = $new_variant_tests[$vkey];
					$style_name = urldecode($new_style['style_name']);
					$newStyleName = smile_duplicate_styleName( $new_variant_tests, trim($style_name) );
					$new_style['style_name'] = $newStyleName;
					$new_style['style_id'] = $new_style_id;
					$settings = unserialize($new_style['style_settings']);
					$settings['live'] = 0;
					$settings['style_id'] = $new_style_id;
					$settings['variant-style'] = $new_style_id;
					$new_style['style_settings'] = serialize($settings);
					array_push( $new_variant_tests,$new_style);
					$modal_arrays[$variant_id] = $new_variant_tests;
					$match = true;
					break;
				}
			}
			if( !$match ){

				// duplicating main style on variant screen
				$new_style = $prev_styles[$key];
				$style_settings = unserialize($new_style['style_settings']);
				$style_settings['live'] = 0;
				$rand = substr(md5(uniqid()),rand(0,26),5);
				$dynamic_style_id = 'cp_id_'.$rand;
				$new_style_id = $dynamic_style_id;
				$style_name = urldecode($new_style['style_name']);
				$newStyleName = smile_duplicate_styleName($new_variant_tests,$style_name);
				$new_style['style_name'] = $newStyleName;
				$new_style['style_id'] = $new_style_id;
				$style_settings['variant-style'] = $new_style_id;
				$new_style['style_settings'] = serialize($style_settings);
				array_push($new_variant_tests,$new_style);
				$modal_arrays[$variant_id] = $new_variant_tests;
			}
		} else {

			// if duplicating main style on variant screen if modal has no variants
			$smile_variant_tests[ $variant_id ] = array();
			$new_style = $prev_styles[$key];
			$style_settings = unserialize($new_style['style_settings']);
			$style_settings['live'] = 0;
			$rand = substr(md5(uniqid()),rand(0,26),5);
			$dynamic_style_id = 'cp_id_'.$rand;
			$new_style_id = $dynamic_style_id;
			$style_name = urldecode($new_style['style_name']);
			$newStyleName = smile_duplicate_styleName($prev_styles,$style_name);
			$new_style['style_name'] = $newStyleName;
			$new_style['style_id'] = $new_style_id;
			$style_settings['variant-style'] = $new_style_id;
			$new_style['style_settings'] = serialize($style_settings);
			array_push($smile_variant_tests[ $variant_id ],$new_style);
			$modal_arrays = $smile_variant_tests;
		}
	} else {

		// if on modal list screen
		$new_style = $prev_styles[$key];
		$style_settings = unserialize($new_style['style_settings']);
		$style_settings['live'] = 0;
		$rand = substr(md5(uniqid()),rand(0,26),5);
		$dynamic_style_id = 'cp_id_'.$rand;
		$new_style_id = $dynamic_style_id;
		$style_name = urldecode($new_style['style_name']);
		$newStyleName = smile_duplicate_styleName($prev_styles,$style_name);
		$new_style['style_name'] = $newStyleName;
		$new_style['style_id'] = $new_style_id;
		$style_settings['style_id'] = $new_style_id;
		$new_style['style_settings'] = serialize($style_settings);
		array_push($prev_styles,$new_style);
		$modal_arrays = $prev_styles;
	}

	update_option( $option, $modal_arrays );

	print_r( json_encode( array(
		'message' => 'copied'
	) ) );
	die();
  }
}


/**
* Function to accept ajax call for changing modal status
* @since 1.0
*/
if( !function_exists( "smile_update_status" ) ){
	function smile_update_status(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		check_admin_referer( "cp-change-style-status", "security_nonce" );

		$status = esc_attr( $_POST['status'] );
		$style_id = esc_attr( $_POST['style_id'] );
		$option = esc_attr( $_POST['option'] );
		$variant_option = isset( $_POST['variant'] ) ? esc_attr( $_POST['variant'] ) : '';
		$cp_start = isset( $_POST['cp_start'] ) ? $_POST['cp_start'] : '';
		$cp_end = isset( $_POST['cp_end'] ) ? $_POST['cp_end'] : '';
		$prev_styles = get_option($option);

		$key = search_style($prev_styles, $style_id);
		$modal_arrays = array();

		$smile_variant_tests = get_option( $variant_option );

		$key = search_style($prev_styles, $style_id);
		if( $key !== NULL ) {

			$new_style = $prev_styles[$key];
			$settings = unserialize($new_style['style_settings']);
			$settings['live'] = $status;
			if( $status == 2 ) {
				$settings['schedule'] = array(
					'start' => $cp_start,
					'end'	=> $cp_end
				);
			}
			$new_style['style_settings'] = serialize($settings);
			$prev_styles[$key] = $new_style;
			$modal_arrays = $prev_styles;
		} else {
			foreach($smile_variant_tests as $key1 => $arrays ){
				foreach($arrays as $key2 => $array ){
					if( $array['style_id'] == $style_id ){
						$modal_arrays = $array;
						$settings = unserialize($smile_variant_tests[$key1][$key2]['style_settings']);
						$settings['live'] = $status;
						$smile_variant_tests[$key1][$key2]['style_settings'] = serialize($settings);
						break;
					}
				}
			}
			$modal_arrays = $smile_variant_tests;
		}

		update_option($option,$modal_arrays);

		print_r(json_encode(array(
			'message' => 'status changed'
		)));
		die();
	}
}

/**
* Function to record impressions for style
* @since 1.0
*/
if( !function_exists( "smile_update_impressions" ) ){
	function smile_update_impressions(){

		// verify nonce
		if( !wp_verify_nonce( esc_attr( $_POST['security'] ), "cp-impress-nonce" ) ) {
			wp_send_json_error();
		}

		global $cp_analytics_end_time;
		$user_role = '';
		$condition = true;
		$cp_settings = get_option('convert_plug_settings');

		if( is_array($cp_settings) ) {
			$banneduser = explode(",",$cp_settings['cp-user-role']);
		}

		if( is_user_logged_in() ) {
			$current_user = new WP_User(wp_get_current_user());
			$user_roles = $current_user->roles;
			$user_role = $user_roles[0];
		}

		if( !empty($cp_settings) ) {
			$condition = !is_user_logged_in() || (is_user_logged_in() && (!in_array($user_role, $banneduser)));
		} else {
			$condition = !is_user_logged_in() || (is_user_logged_in() && ( $user_role != 'administrator' ));
		}

		if( $condition ) {

			$styles = array_map( 'sanitize_text_field', wp_unslash( $_POST['styles'] ) );

			foreach( $styles as $style ) {

				$style_id = $style;
				$impression = esc_attr( $_POST['impression'] );

				/// Save analytics data
				$existing_data = get_option('smile_style_analytics');
				$date = $cp_analytics_end_time;

				if( !$existing_data ) {

					$analyticsData = array (
						$style_id => array(
						 	$date => array(
						 		'impressions' => 1,
						 		'conversions' => 0
						 	)
						)
					);

				} else {

					if( isset($existing_data[$style_id]) ) {
						$isDateExist = cp_search_key_in_array( $existing_data[$style_id], $date );

						if( $isDateExist ) {

							foreach ( $existing_data[$style_id] as $key => $value ) {
								if( $key === $date ) {
									$oldImpressions = $value['impressions'];
									$oldConversions = $value['conversions'];
									$existing_data[$style_id][$date] = array(
									'impressions' => $oldImpressions + 1,
									'conversions' => $oldConversions
									);
								}
							}
						} else {
							$existing_data[$style_id][$date] = array(
								'impressions' => 1,
								'conversions' => 0,
							);
						}
					} else {

						$existing_data[$style_id] = array(
							$date => array(
								'impressions' => 1,
								'conversions' => 0,
							 )
						);
					}
					$analyticsData = $existing_data;
				}

				update_option( 'smile_style_analytics', $analyticsData );
			}

			echo 'impression added';
		}

		die();
	}
}

/**
* Function to accept ajax call for deleting existing styles
* @since 1.0
*/
if( !function_exists( "smile_delete_style" ) ){
function smile_delete_style(){

	if ( ! current_user_can( 'access_cp' ) ) {
		die(-1);
	}

	check_admin_referer( "cp-delete-style", "security_nonce" );

	$style_id = esc_attr( $_POST['style_id'] );
	$option = isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';
	$variant_option = isset( $_POST['variant_option'] ) ? esc_attr( $_POST['variant_option'] ) : '';
	$prev_styles = get_option($option);
	$key = search_style( $prev_styles, $style_id );
	$hasVariants = false;
	$result = true;

	$modal_arrays = array();

	$smile_variant_tests = get_option( $variant_option );
	if( $smile_variant_tests && is_array($smile_variant_tests) ) {
		$hasVariants = array_key_exists( $style_id, $smile_variant_tests );
	}

	if( $hasVariants && $key !== NULL ) {

			$delMethod = esc_attr( $_POST['deleteMethod'] );
			if( $delMethod == 'soft' ) {
				$prev_styles[$key]['multivariant'] = true;
				$settings = unserialize($prev_styles[$key]['style_settings']);
				$settings['live'] = '0';
				$prev_styles[$key]['style_settings'] = serialize($settings);
			} else {
				unset( $prev_styles[$key] );
				unset( $smile_variant_tests[$style_id] );
			}
			update_option( $option, $prev_styles );
			update_option( $variant_option, $smile_variant_tests );

			// reset analytics data for style
			cp_reset_analytics($style_id);

	} else {

		if( $key !== NULL ) {
			unset($prev_styles[$key]);
			$modal_arrays = $prev_styles;
			$result = update_option( $option,$modal_arrays );

			// reset analytics data for style
			cp_reset_analytics($style_id);

		} else {
			foreach($smile_variant_tests as $key1 => $arrays ){
				foreach($arrays as $key2 => $array ){
					if( $array['style_id'] == $style_id ){
						$modal_arrays = $array;
						unset( $smile_variant_tests[$key1][$key2] );
						$modal_arrays = $smile_variant_tests;
						$result = update_option( $variant_option, $modal_arrays );

						// reset analytics data for style
						cp_reset_analytics($style_id);
						break;
					}
				}
			}
		}
	}

	if( $result ){
		print_r(json_encode(array(
               'message' => 'Deleted'
			)));
		die();
	} else {
		echo __('Unable to delete the style. Please Try again.','smile');
	}
	die();
 }
}

/**
* Function to accept ajax call for updating module list
* @since 1.0
*/
if( !function_exists( "smile_update_modules" ) ){
	function smile_update_modules(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$module_list = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		unset( $module_list['action'] );
		$new_module_list = array();
		foreach( $module_list as $module => $file ) {
			$new_module_list[] = $module;
		}

		$result = update_option( 'convert_plug_modules', $new_module_list );
		if( $result ) {
			print_r(json_encode(array(
				'message' => __( 'Modules Updated!', 'smile' )
			)));
		} else {
			print_r(json_encode(array(
				'message' => __( 'No settings were updated. Try again!', 'smile' )
			)));
		}
		die();
	}
}

/**
* Function to accept ajax call for updating globally displayed modal settings
* @since 1.0
*/
if( !function_exists( "smile_update_global" ) ){
	function smile_update_global(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$result = update_option( 'smile_global_modal',$data );
		if( $result ) {
			echo 'Updated';
		} else {
			echo __('Something went wrong! Please try again.','smile');
		}
		die();
	}
}

/**
* Function to accept ajax call for adding the new contact list
* @since 1.0
*/
if( !function_exists( "smile_add_list" ) ){
	function smile_add_list(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		check_admin_referer( 'cp-create-list-nonce' );

		$data = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$cp_addon_list = Smile_Framework::$addon_list;
		$data['list-name'] = cp_clean_string($data['list-name']);
		$old_value = get_option('smile_lists');
		$arr = array();
		$provider_list = array();

		// This if is a case where multiple lists needs to be saved for any campaign.
		if( isset( $cp_addon_list[$data['list-provider']]['mailer_type'] ) ) {
			if( $cp_addon_list[$data['list-provider']]['mailer_type'] == 'multiple' ) {
				if( isset( $data['list'] ) ) {
					$decoded_json = json_decode( stripslashes( $data['list'] ), true );
					if( count( $decoded_json ) > 0 ) {
						foreach ( $decoded_json as $d ) {
							$tmp = json_decode( $d, true );
							foreach( $tmp as $key=>$t ) {
								$arr[$key] = $t;
								$provider_list[] = $key;
							}
						}
					}
				}
				$data['list'] = implode( ",", $provider_list );
				$data['provider_list'] = $arr;
			}
		}

		unset($data['action']);
		$list_data = $data;
		$old_value[] = $list_data;
		$status = update_option( 'smile_lists', $old_value );
		if( $status ){
			print_r(json_encode(array(
	            'message' => 'added'
			)));
			die();
		} else {
			print_r(json_encode(array(
	            'message' => 'error'
			)));
			die();
		}
	}
}

/**
* Function to remove special characters from string
* @since 1.0
*/
if( !function_exists( "cp_clean_string" ) ){
	function cp_clean_string($string) {

	   $string = trim($string);

	   // remove single and double quotes from string
	   $string  = str_replace( array("'", '"'), "", $string );
	   $string  = str_replace(array('\\', '/'),"",$string);  // remove slashes

	   return $string;
	}
}

/**
* Function to check if campaign with same name already exists
* @since 1.0
*/
if( !function_exists( "isCampaignExists" ) ){
	function isCampaignExists(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$campaignName = sanitize_text_field( $_POST['campaign'] );
		$isExists = false;
		$lists = get_option('smile_lists');

		if( is_array($lists) ) {
			foreach ( $lists as $key => $list ) {
				if( strtolower( trim($list['list-name']) )  == strtolower( trim($campaignName) ) ) {
						$isExists = true;
				}
			}
		}

		if( $isExists ) {
			print_r(json_encode(array(
	            'status'  => 'error',
	            'message' => __( "Campaign with same name already exists", "smile" )
			)));
			die();
		} else {
			print_r(json_encode(array(
	            'status' => "success",
			)));
			die();
		}
	}
}

/**
* Function to generate option name to be used to store contacts in list
* @since 2.0.3.1
*/
function cp_generate_option( $list_id )  {

	$smile_lists = get_option('smile_lists');
	$data_option = '';

	if( is_array($smile_lists) ) {
		$list_details = $smile_lists[$list_id];

		$listName = ( $list_details != '' ) ? str_replace( " ","_",strtolower( trim( $list_details['list-name'] ) ) ) : '';

		$mailer = ( $list_details != '' ) ? $list_details['list-provider'] : '';

		if( $mailer == 'Convert Plug' ) {
	        $mailer_id = 'cp';
	        $data_option = "cp_connects_".$listName;
	    } else {
	        $mailer_id = strtolower($mailer);
	        $data_option = "cp_".$mailer_id."_".$listName;
	    }
	}

    return $data_option;
}

/**
* Function to accept ajax call for adding contact in a list
* @since 1.0
*/
if( !function_exists( "cp_add_subscriber" ) ){
	function cp_add_subscriber(){

		$style_id 			= isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';

		check_ajax_referer( 'cp-submit-form-'.$style_id );
		
		$param 				= array_map( 'sanitize_text_field', wp_unslash( $_POST['param'] ) );
		$email 				= isset( $_POST['param']['email'] ) ? sanitize_text_field( $_POST['param']['email'] ) : '';
		$list_id 			= isset( $_POST['list_id'] ) ? esc_attr( $_POST['list_id'] ) : '';
		$data_option 		= cp_generate_option( $list_id );
		$only_conversion 	= isset( $_POST['only_conversion'] ) ? true : false;
		$default_action 	= isset( $_POST['action'] ) ? esc_attr( $_POST['action'] ) : '';
		$cp_settings        = get_option( 'convert_plug_settings' );

		if( isset( $_POST['message'] ) ) {
			$on_success = 'message';
		} else if( isset( $_POST['redirect'] ) ){
			$on_success = 'redirect';
		} else {
			$on_success = 'close';
		}

		$msg_wrong_email = ( isset( $_POST['msg_wrong_email']  ) && $_POST['msg_wrong_email'] !== '' ) ? sanitize_text_field( $_POST['msg_wrong_email'] ) : __( 'Please enter correct email address.', 'smile' );
		$msg = ( isset( $_POST['message'] ) && $_POST['message'] !== '' ) ? do_shortcode( html_entity_decode( stripcslashes( $_POST['message'] ) ) ) : __( 'Thank you.', 'smile' );

		if( $on_success == 'message' ) {
			$action		= 	'message';
			$url		= 	'none';
		} else if( $on_success == "redirect" ) {
			$action		= 	'redirect';
			$url		= 	$_POST['redirect'];
		} else {
			$action		= 	'close';
			$url		= 	'#';
		}

		$contact = array();
		$prev_contacts = get_option($data_option);

		//	Check Email in MX records
		$email_status = true;
		if( !$only_conversion ) {

			//	Check MX Record setting globally enabled / disabled?
			if( !empty($email) && ( apply_filters( 'cp_enabled_mx_record', $email ) ) ) {

				if( filter_var( $email, FILTER_VALIDATE_EMAIL) ) {
					$email_status = apply_filters( 'cp_valid_mx_email', $email );
				} else {
					$email_status = false;
				}
			}
		}

		if( $email_status ) {

			$status = 'success';
			$contact = $param;
			$updated = false;
			$index = false;

			if( !empty($email) && $prev_contacts) {
				$index = cp_check_in_array($email, $prev_contacts, 'email');
			}

			if ( $index !== false ) {

				$contact['user_id'] = $prev_contacts[$index]['user_id'];
				$prev_contacts[$index] = $contact;
				$updated = true;
				$status = 'error';
				//	Show message for already subscribed users
				
				$default_msg_status =	isset($cp_settings['cp-default-messages']) ? $cp_settings['cp-default-messages'] : 1;
				$already_subscribed =	isset($cp_settings['cp-already-subscribed']) ? $cp_settings['cp-already-subscribed'] : __( 'Already Subscribed...!', 'smile' );
				$debug_data         = 	get_option( 'convert_plug_debug' );
				$sub_def_action     = 	isset( $debug_data['cp-post-sub-action'] ) ? $debug_data['cp-post-sub-action'] : 'process_success';

				if( $default_msg_status ) {
					$msg = stripslashes($already_subscribed);
				}

				if( $sub_def_action == 'process_success' ) {
					$status = 'success';
					$msg = ( isset( $_POST['message'] ) && $_POST['message'] !== '' ) ? do_shortcode( html_entity_decode( stripcslashes( sanitize_text_field( $_POST['message'] ) ) ) ) : __( 'Thank you.', 'smile' );
				}

			} else {
				$prev_contacts[] = $contact;
			}

			if( !empty( $prev_contacts ) ){
				$prev_contacts =  array_map( "unserialize", array_unique( array_map( "serialize", $prev_contacts ) ) );
			}

			if( !$only_conversion ){
				update_option( $data_option, $prev_contacts );
			}

			if( !$updated ) {
				// update conversions
				smile_update_conversions( $style_id );
			}

		} else {
			if( $only_conversion ) {
				// update conversions
				$status = 'success';
				smile_update_conversions( $style_id );
			} else {
				$msg = $msg_wrong_email;
				$status = 'error';
			}
		}
		
		//send subscriber notification to provided email address
		$sub_optin = isset($cp_settings['cp-sub-notify']) ? $cp_settings['cp-sub-notify'] : 0;
		$sub_email = isset($cp_settings['cp-sub-email']) ? $cp_settings['cp-sub-email'] :get_option( 'admin_email' );

		if( $status == 'success' && $default_action == 'cp_add_subscriber' && !$only_conversion && $sub_optin == '1') {

			$list_name = str_replace( "cp_connects_", "", $data_option );
			$list_name = str_replace( "_", " ", $list_name );

			cp_notify_sub_to_admin( $list_name, $param, $sub_email );
		}
		
		print_r(json_encode(array(
			'action' => $action,
			'email_status' => $email_status,
			'status' => $status,
			'message' => $msg,
			'url' => $url,
		)));

		die();
	}
}

/**
* Custom function to add contact to list databaseFunction to get data for style analytics
* @since 1.0
*/

if( !function_exists( "cp_add_subscriber_contact" ) ) {
	function cp_add_subscriber_contact( $contacts_option = '', $subscriber ) {
		
		$list_id = isset( $_POST['list_parent_index'] ) ? esc_attr( $_POST['list_parent_index'] ) : '';
		$option = cp_generate_option( $list_id );

		$data = get_option($option);
		$index = false;
		$updated = false;

		$email = isset( $subscriber['email'] ) ? strtolower($subscriber['email']) : '';
		if( $data ) {
			$index = cp_check_in_array($email, $data, 'email');
		}

		$subscriber = array_map( 'sanitize_text_field', wp_unslash( $subscriber ) );

		if ( $index !== false ) {
			unset($data[$index]);
			$data[] = $subscriber;
			$updated = true;
		} else {
			$data[] = $subscriber;
		}

		if( !empty( $data ) ){
			$data =  array_map( "unserialize", array_unique( array_map( "serialize", $data ) ) );
		}

		//convert array
		$data1 = array();
		$data = array_filter($data);
		foreach ( $data as $key => $value ) {
			$newdata = array();
			foreach ( $value as $key1 => $value1 ) {
				if( $key1 == 'email' ) {
					$newdata[$key1] = strtolower($value1);
				} else {
					$newdata[$key1] = $value1;
				}
			}
			array_push( $data1, $newdata );
		}

		update_option( $option, $data1 );
		return $updated;
	}
}


/**
* Custom function to search string in array
* @since 1.0
*/
if( !function_exists('cp_check_in_array')){
	function cp_check_in_array( $value, $array, $key ) {
		if( is_array( $array ) ) {
			foreach( $array as $index => $item ){
				if( isset( $item[$key] ) ) {
			    	if(strtolower($item[$key]) == $value) {
			     		return $index;
			    	}
				}
		   	}
		}
		return false;
	}
}

/**
* Custom function to search for key in array
* @since 1.0
*/
if( !function_exists('cp_search_key_in_array')){
	function cp_search_key_in_array($array, $key){
	    foreach( $array as $index => $item ){
			if( $key == $index )
		    	return true;
		}
	    return false;
	}
}


/**
* Custom function to retrive list name by its ID
* @since 1.0
*/
if( !function_exists('cp_get_list_name_by_id')){
	function cp_get_list_name_by_id( $listID, $provider ){
		$listoption = strtolower($provider).'_lists';
		$data = get_option($listoption);
		$listName = $data[$listID];
		return $listName;
	}
}

if( !function_exists('cpGenerateCsv')) {
	function cpGenerateCsv( $data, $delimiter = ',', $enclosure = '"' ) {
		
		$handle = fopen('php://temp', 'r+');
		$contents = '';
		if( is_array( $data ) && !empty( $data ) ) {

		  // get header from keys and set its first character to Upper case
			$headers = array_change_key_case( $data[0], CASE_LOWER );
			fputcsv( $handle, array_map('ucfirst', array_keys( $headers ) ) );

			foreach ($data as $line) {
				fputcsv( $handle, $line, $delimiter, $enclosure );
			}
			
			rewind($handle);
			while ( !feof($handle) ) {
				$contents .= fread( $handle, 8192 );
			}
			fclose($handle);
			return $contents;
	  	} else {
			return __( "No contacts available to export.", "smile" );
	  	}
	}
}

function toLower($value) {
    return strtolower($value);
}

/**
* Custom function to retrieve analytics data for campaign
* @since 1.0
*/
if( !function_exists('get_campaign_analytics_data')){

	function get_campaign_analytics_data(){

		$smile_lists = get_option('smile_lists');
		$data = array();
		$startDate = sanitize_text_field( $_POST['startDate'] );
		$endDate = sanitize_text_field( $_POST['endDate'] );

		// to unset deactivated / inactive mailer add ons
		if( is_array($smile_lists) ) {
			foreach( $smile_lists as $key => $list ){
			    $provider = $list['list-provider'];
			    if( $provider !== 'Convert Plug' ) {
			      if( !isset( Smile_Framework::$addon_list[$provider] ) && !isset( Smile_Framework::$addon_list[strtolower($provider)] ) ) {
			        unset( $smile_lists[$key] );
			      }
			    }
			}
		}

		if( !is_array($smile_lists) ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		if( is_array($_POST['listid']) )
			 $list_ids = $_POST['listid'];
		else
			$list_ids = explode( ",", $_POST['listid'] );

		if ( in_array("all", $list_ids) ) {
			$list_ids = array_keys($smile_lists);
		}

		$colorIndex = 0;
		$totalCount = 0;

	  	if( !empty( $smile_lists ) ){

		  	foreach( $smile_lists as $key => $list ){

		  		$contactCount = 0;
				$list_name = $list['list-name'];
				$provider = $list['list-provider'];
				$list_id = isset( $list['list'] ) ? $list['list'] : '';

				if(in_array($key, $list_ids)) {

					$cp_list_id = 'cp_list_'.$key;
					$mailer = str_replace(" ","_",strtolower( trim( $provider ) ) );
					if( $mailer !== "convert_plug" ){
						$contacts_option = "cp_".$mailer."_".str_replace(" ","_",strtolower( trim( $list_name ) ) );
				  		$list_contacts = get_option($contacts_option);
					} else {
						$contacts_option = "cp_connects_".str_replace(" ","_",strtolower( trim( $list_name ) ) );
						$list_contacts = get_option($contacts_option);
					}

					if (is_array($list_contacts) || is_object($list_contacts)) {

						foreach( $list_contacts as $contact ) {
							$date = strtotime($contact['date']);

							if( $startDate == '' && $endDate == '' ) {
								$contactCount++;
							} else if( $date <= strtotime($endDate) && $date >= strtotime($startDate) )  {
								$contactCount++;
							}
						}
					}

					if( $contactCount !== 0 ) {

						global $colorPallet;
						if ( $colorIndex >= count($colorPallet)) {
					    	$colorIndex = 0;
					    }

				    	$randomColor = array_rand($colorPallet, count($colorPallet));
						$data[] = array(
							'color' => $colorPallet[$randomColor[$colorIndex]],
        					'highlight' => $colorPallet[$randomColor[$colorIndex]],
							'value' => $contactCount,
							'label' => $list_name
						);
						$colorIndex++;
					}
				}

				$totalCount = $totalCount + $contactCount;
			}
		}

		if( $totalCount == 0 ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		echo json_encode($data);
		die();
	}
}


/**
* Function to get data for campaign analytics
* @since 1.0
*/
if( !function_exists('get_campaign_daywise_data')){

	function get_campaign_daywise_data(){

		global $cp_analytics_start_time,$cp_analytics_end_time,$colorPallet;
		$data = array();
		$dateFormat = "M d";
		$startDate = sanitize_text_field( $_POST['startDate'] );
		$chartType = sanitize_text_field( $_POST['chartType'] );
		$endDate = sanitize_text_field( $_POST['endDate'] );

		$smile_lists = get_option('smile_lists');

		// to unset deactivated / inactive mailer add ons
		if( is_array($smile_lists) ) {
			foreach( $smile_lists as $key => $list ){
			    $provider = $list['list-provider'];
			    if( $provider !== 'Convert Plug' ) {
			      if( !isset( Smile_Framework::$addon_list[$provider] ) && !isset( Smile_Framework::$addon_list[strtolower($provider)] ) ) {
			        unset( $smile_lists[$key] );
			      }
			    }
			}
		}

		if( !is_array($smile_lists) ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		if( is_array($_POST['listid']) )
			 $list_ids = $_POST['listid'];
		else
			$list_ids = explode( ",", $_POST['listid'] );

		if ( in_array("all", $list_ids) ) {
			if($smile_lists) {
				$list_ids = array_keys($smile_lists);
			}
		}

		if($startDate == '' && $endDate == '') {
			$startdate = $cp_analytics_start_time;
			$enddate = $cp_analytics_end_time;
		} else {
			$startdate = $startDate;
			$enddate  = $endDate;
		}

		$datesArray = getDatesFromRange($startdate,$enddate,$dateFormat);

		foreach ( $datesArray as $key => $value ) {
		    	$data['labels'][] = $key;
		}

		$colorIndex = 0;
		/// create dataset array
		foreach( $list_ids as $listid ) {

			$dateValues = array();
		    $list = $smile_lists[$listid];
		    $provider = $list['list-provider'];
		    $listName = $list['list-name'];

		    $id = isset( $list['list'] ) ? $list['list'] : '';
		    $cp_list_id = 'cp_list_'.$listid;
		    $mailer = str_replace(" ","_",strtolower( trim( $provider ) ) );

		    if( $mailer !== "convert_plug" ){
				$contacts_option = "cp_".$mailer."_".str_replace(" ","_",strtolower( trim( $listName ) ) );
		  		$contacts = get_option($contacts_option);
			} else {
				$contacts_option = "cp_connects_".str_replace(" ","_",strtolower( trim( $listName ) ) );
				$contacts = get_option($contacts_option);
			}

	    	if( $contacts ) {
		    	// remove null records from array
		    	$contacts = array_filter($contacts, function($k) {
			    	return $k !== null;
				});

 		    	$counted = array_count_values(array_map(function($value){return $value['date'];}, $contacts));

			    foreach($counted as $key => $value) {
			    	$firstDate = $key;
			    	break;
			    }
			}

		    if($startDate == '' && $endDate == '') {

		    	$startdate = $cp_analytics_start_time;
		    	$enddate = $cp_analytics_end_time;
		    	$datesArray = getDatesFromRange($startdate,$enddate,$dateFormat);

		    	if( $contacts ) {
			    	foreach($counted as $key => $value) {

				    	$date = strtotime($key);
				    	$key = date($dateFormat,strtotime($key));
				    	$sDate = strtotime($startdate);
				    	$eDate = strtotime($enddate);
				    	if( $date <= $eDate && $date >= $sDate ) {
				    		$datesArray[$key] = $value;
				    	}
				    }
			    }

		    } else {

		    	$toDate = ( $endDate == '' ? date($dateFormat) : $endDate );
		    	$fromDate = ( $startDate == '' ? $firstDate : $startDate );

		    	$datesArray = getDatesFromRange($fromDate,$toDate, $dateFormat);

		    	if( $contacts ) {
			    	foreach($counted as $key => $value) {

				    	$date = strtotime($key);
				    	$key = date($dateFormat,strtotime($key));
				    	$sDate = strtotime($startDate);
				    	$eDate = strtotime($endDate);

				    	if( $date <= $eDate && $date >= $sDate )
				    			$datesArray[$key] = $value;
				    }
				}
		    }

		    $listData = $datesArray;
		    foreach ($listData as $key => $value) {
		    	$dateValues[] = $value;
		    }

		    if ( $colorIndex >= count($colorPallet)) {
		    	$colorIndex = 0;
		    }

		    $randomColor = array_rand($colorPallet, count($colorPallet));

		    if($chartType == 'bar' ) {
		    	$data['datasets'][] = array(
		    		'label' => urldecode($listName),
		    		'fillColor' => $colorPallet[$randomColor[$colorIndex]],
		            'strokeColor' => $colorPallet[$randomColor[$colorIndex]],
		            'highlightFill' =>  $colorPallet[$randomColor[$colorIndex]],
		            'highlightStroke' => $colorPallet[$randomColor[$colorIndex]],
		    		'data'  => $dateValues,
		    		'tpl_var_count' =>  array_sum($dateValues)

		    	);
		    } else {
			    $data['datasets'][] = array(
			    		'label' => urldecode($listName),
			    		'fillColor' => 'rgba(229,243,249,0.4)',
			            'strokeColor' => $colorPallet[$randomColor[$colorIndex]],
			            'pointColor' =>  $colorPallet[$randomColor[$colorIndex]],
			            'pointStrokeColor' => $colorPallet[$randomColor[$colorIndex]],
			            'pointHighlightFill' =>  $colorPallet[$randomColor[$colorIndex]],
			            'pointHighlightStroke' =>  'rgba(68,68,68,0.5)',
			    		'data'  => $dateValues,
			    		'tpl_var_count' =>  array_sum($dateValues)
			    );
			}

			$colorIndex++;
		}

		if( !array_key_exists('datasets', $data) ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		echo json_encode($data);
		die();
	}
}

/**
* Function to update style conversions
* @since 1.0
*/
if( !function_exists( 'smile_update_conversions' ) ) {
	function smile_update_conversions( $style_id ) {

		global $cp_analytics_end_time;
		$user_role = '';
		$condition = true;
		$cp_settings = get_option('convert_plug_settings');

		if( is_array($cp_settings) ) {
		 	$banneduser = explode( ",", $cp_settings['cp-user-role'] );
		}

		if( is_user_logged_in() ) {
		 	$current_user = new WP_User(wp_get_current_user());
		 	$user_roles = $current_user->roles;
		 	$user_role = $user_roles[0];
		}

		if(!empty($cp_settings)){
			$condition = !is_user_logged_in() || (is_user_logged_in() && (!in_array($user_role, $banneduser)));
		} else {
		 	$condition = !is_user_logged_in() || (is_user_logged_in() && ( $user_role != 'administrator' ));
		}

		if( $condition ) {

			/// Save analytics data
			$existing_data = get_option('smile_style_analytics');
			$date = $cp_analytics_end_time;

			if( !is_array($existing_data) ) {

				// First conversion
				$analyticsData = array (
					$style_id => array(
					 	$date => array(
					 		'impressions' => 0,
					 		'conversions' => 1
					 	)
					)
				);

			} else {
				if( isset( $existing_data[$style_id] ) ) {
					foreach ( $existing_data[$style_id] as $key => $value ) {
						if( $key === $date ) {
							$oldImpressions = $value['impressions'];
							$oldConversions = $value['conversions'];
							$existing_data[$style_id][$date] = array(
								'impressions' => $oldImpressions,
								'conversions' => $oldConversions + 1
							);
						}
					}
				} else {
					// first conversion for this particular style
					$existing_data[$style_id] = array(
				 		$date => array(
					 		'impressions' => 0,
					 		'conversions' => 1
					 	)
					);
				}
				$analyticsData = $existing_data;
			}

			update_option( 'smile_style_analytics', $analyticsData );
		}
	}
}

/**
* Function to get data for style analytics
* @since 1.0
*/
if( !function_exists('get_style_analytics_data')){
	function get_style_analytics_data(){

		global $cp_analytics_start_time,$cp_analytics_end_time;
		$dateFormat = "M d";
		$colorIndex = 0;
		$module = isset( $_POST['module'] ) ? $_POST['module'] : 'modal';
		$analtics_Data = get_option('smile_style_analytics');
		$smile_styles = get_option('smile_'.$module.'_styles');
		$variant_option = $module.'_variant_tests';
		$variant_tests = get_option( $variant_option );
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$data = array();
		$chartType = $_POST['chartType'];
		$compFactor = $_POST['compFactor'];
		$conversions = array();
		$impressions = array();
		$dateValues = array();

		if( !is_array($smile_styles) ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		if( is_array($_POST['styleid'])  ) {
			$style_ids = $_POST['styleid'];

			if(count($_POST['styleid']) > 1)
				$style = 'multiple';
			else
				$style = 'single';
		} else {
			$style = 'single';
			$style_ids = explode( ",", $_POST['styleid'] );
		}

		if ( in_array("all", $style_ids) ) {
			$style_ids = array();
		   	foreach ( $smile_styles as $style ) {
		   		if(!isset($style['multivariant'])) {
		   			$style_ids[] = $style['style_id'];
		   		}

		   		if(isset($variant_tests[$style['style_id']])) {
		   			foreach ( $variant_tests[$style['style_id']] as $value ) {
				      $style_ids[] = $value['style_id'];
				    }
		   		}
		   	}
			$style = 'multiple';
		} else {
		   	$style_ids[] = $style_ids;

		   	if( $compFactor == 'impVsconv' ) {
			   	$style = 'single';
			} else {
				$style = 'multiple';
			}
		}
		if( $startDate == '' && $endDate == '' ) {
	    	$startDate = $cp_analytics_start_time;
	    	$endDate = $cp_analytics_end_time;
	   	}

    	foreach( $style_ids as $style_id ) {
    		$dateValues = array();
    		$impCount = 0;
    		$convCount = 0;
    		$datesArray = getStyleAnalticsRange($startDate,$endDate,$dateFormat,$style);

	    		$styleName = cp_get_styleNameByID($style_id, $smile_styles, $variant_option);

	    		if( $styleName !== null ) {

	    			if( isset( $analtics_Data[$style_id] ) ) {
						foreach ( $analtics_Data[$style_id] as $key => $value ) {

						    	$date = strtotime($key);
						    	$key = date($dateFormat,strtotime($key));
						    	$sDate = strtotime($startDate);
						    	$eDate = strtotime($endDate);

				    		if( $chartType == 'line' || $chartType == 'bar' ) {

					    		if( $date <= $eDate && $date >= $sDate ) {
					    			switch($compFactor) {
					    				case "imp":
					    					$datesArray[$key] = $value['impressions'];
					    				break;
					    				case "conv":
					    					$datesArray[$key] = $value['conversions'];
					    				break;
					    				case "convRate":
					    					$conversionRate = ($value['conversions'] / $value['impressions']) * 100;
					    					$datesArray[$key] = round($conversionRate, 2);
					    				break;
					    				case "impVsconv":
					    					$datesArray[$key] = array (
					    						'impressions' => $value['impressions'],
					    						'conversions' => $value['conversions']
					    					);
					    				break;
					    			}
							    }

						    } else {
						    	if( $date <= $eDate && $date >= $sDate ) {
					    				$impCount = $impCount + $value['impressions'];
					    				$convCount = $convCount + $value['conversions'];
							    }
						    }
				    	}
			    	}

			    	$styleData = $datesArray;

				    foreach ( $styleData as $key => $value ) {
				    	$dateValues[] = $value;
				    	if($style == 'single') {
				    		$impressions[] = $value['impressions'];
				    		$conversions[] = $value['conversions'];
				    	}
				    }

				    global $colorPallet;

				    if ( $colorIndex >= count($colorPallet)) {
				    	$colorIndex = 0;
				    }

				    $randomColor = array_rand($colorPallet, count($colorPallet));

				    if( $chartType == 'donut' || $chartType == 'polararea' ) {

					    switch( $compFactor ) {
		    				case "imp":
		    					$dataValue = $impCount;
		    				break;
		    				case "conv":
		    					$dataValue = $convCount;
		    				break;
		    				case "convRate":
		    					if( $impCount == 0 || $convCount == 0 ) {
		    						$dataValue = 0;
		    					}
		    					else {
		    						$convRate = ($convCount / $impCount) * 100;
		    						$dataValue = round($convRate, 2);
		    					}
		    				break;
					    }

					   	if( $style == 'single' ) {

					   		if( $impCount !== 0 ) {
						   		$data[] = array(
										'color' => $colorPallet[$randomColor[$colorIndex]],
			        					'highlight' => $colorPallet[$randomColor[$colorIndex]],
										'value' => $impCount,
										'label' => 'Impressions'
								);
					   		}

					   		if( $convCount !== 0 ) {
								$data[] = array(
										'color' => $colorPallet[$randomColor[$colorIndex + 1]],
			        					'highlight' => $colorPallet[$randomColor[$colorIndex +1]],
										'value' => $convCount,
										'label' => 'Conversions'
								);
							}

					   	} else {
					   		if( $dataValue != 0 ) {
							    $data[] = array(
										'color' => $colorPallet[$randomColor[$colorIndex]],
			        					'highlight' => $colorPallet[$randomColor[$colorIndex]],
										'value' => $dataValue,
										'label' => urldecode(stripslashes($styleName))
								);
							}
						}

					} else {

					    if( $style == 'single' ) {

				    			$imp_count = array_sum($impressions);
				    			$conv_count = array_sum($conversions);

						    	$data['datasets'][] = array(
						    		'label' => 'Impressions',
						    		'fillColor' => 'rgba(229,243,249,0.4)',
						            'strokeColor' => $colorPallet[$randomColor[$colorIndex]],
						            'pointColor' =>  $colorPallet[$randomColor[$colorIndex]],
						            'highlightFill' =>  $colorPallet[$randomColor[$colorIndex]],
						            'highlightStroke' => $colorPallet[$randomColor[$colorIndex]],
						    		'data'  => $impressions,
						    		'tpl_var_count' =>  $imp_count
						    	);

						    	$data['datasets'][] = array(
						    		'label' => 'Conversions',
						    		'fillColor' => 'rgba(229,243,249,0.4)',
						            'strokeColor' => $colorPallet[$randomColor[$colorIndex + 1]],
						            'pointColor' =>  $colorPallet[$randomColor[$colorIndex + 1]],
						            'highlightFill' =>  $colorPallet[$randomColor[$colorIndex + 1]],
						            'highlightStroke' => $colorPallet[$randomColor[$colorIndex + 1]],
						    		'data'  => $conversions,
						    		'tpl_var_count' =>  $conv_count
						    	);

					    } else {

					    		if($compFactor == 'convRate') {
									$var_count = cp_calculate_average($dateValues)." %";
					    		} else {
					    			$var_count = array_sum($dateValues);
					    		}

							    if($chartType == 'bar' ) {
								    	$data['datasets'][] = array(
								    		'label' => urldecode(stripslashes($styleName)),
								    		'fillColor' => $colorPallet[$randomColor[$colorIndex]],
								            'strokeColor' => $colorPallet[$randomColor[$colorIndex]],
								            'highlightFill' =>  $colorPallet[$randomColor[$colorIndex]],
								            'highlightStroke' => $colorPallet[$randomColor[$colorIndex]],
								    		'data'  => $dateValues,
								    		'tpl_var_count' =>  $var_count
								    	);
							    } else {
								    $data['datasets'][] = array(
								    		'label' => urldecode(stripslashes($styleName)),
								    		'fillColor' => 'rgba(229,243,249,0.4)',
								            'strokeColor' => $colorPallet[$randomColor[$colorIndex]],
								            'pointColor' =>  $colorPallet[$randomColor[$colorIndex]],
								            'pointStrokeColor' => $colorPallet[$randomColor[$colorIndex]],
								            'pointHighlightFill' =>  $colorPallet[$randomColor[$colorIndex]],
								            'pointHighlightStroke' =>  'rgba(68,68,68,0.5)',
								    		'data'  => $dateValues,
								    		'tpl_var_count' =>  $var_count
								    );
								}
						}
					}
				}
		    $colorIndex++;
		}

		if( empty($data) ) {
			$message = "unavailable";
	    	echo json_encode($message);
	    	die();
		}

		if( $chartType == 'line' || $chartType == 'bar' ) {
		    foreach ($datesArray as $key => $value) {
		    	if($key !== 0 && $key !== null )
		    		$data['labels'][] = $key;
		    }
		}

	    echo json_encode($data);
		die();
	}
}


/**
* Function to return array of dates from particular range
* @since 1.0
*/
if( !function_exists('getDatesFromRange')){
	function getDatesFromRange($start, $end, $dateFormat) {
	    $interval = new DateInterval('P1D');

	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);

	    $period = new DatePeriod(
	         new DateTime($start),
	         $interval,
	         $realEnd
	    );

	    foreach($period as $date) {
	        $array[$date->format($dateFormat)] = 0;
	    }

	    return $array;
	}
}


/**
* Function which returns array of dates for impression and conversions
* @since 1.0
*/
if( !function_exists( 'getStyleAnalticsRange' )){
	function getStyleAnalticsRange( $start, $end, $dateFormat, $type ) {
	    $interval = new DateInterval('P1D');

	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);

	    $period = new DatePeriod(
	         new DateTime($start),
	         $interval,
	         $realEnd
	    );

	    foreach($period as $date) {
	    	if($type != 'single')
	        	$array[$date->format($dateFormat)] = 0;
	        else {
	        	$array[$date->format($dateFormat)] = array(
	        		'impressions' => 0,
	        		'conversions' => 0
	        		);
	        }
	    }
	    return $array;
	}
}


/**
* Function to get style name by its ID
* @since 1.0
*/
if( !function_exists('cp_get_styleNameByID')){

	function cp_get_styleNameByID( $style_id, $smile_styles, $variant ) {
		$styles = $smile_styles;
		if( !empty( $styles ) ) {
			foreach($styles as $style) {
				if($style['style_id'] == $style_id) {
					return $style['style_name'];
				}
			}
		}

		$variantStyles = (!$variant == "" ) ? get_option( $variant ) : false;

		if($variantStyles) {
			foreach ($variantStyles as $key => $value) {
				if(count($value) > 0 ) {
					foreach($value as $variantstyle) {
						if ( $variantstyle['style_id'] == $style_id )  {
							$style_name = $variantstyle['style_name'];
							return urldecode(stripslashes($style_name));
						}
					}
				}
			}
		}
	}
}


/**
* Function for updating creating duplicate style name
* @since 1.0
*/
if( !function_exists( "smile_duplicate_styleName" ) ){
	function smile_duplicate_styleName( $prev_styles,$style_name ) {

		$stylePresent = false;

		foreach($prev_styles as $style) {

			if( $style['style_name'] !== $style_name ) {

				if( strpos($style['style_name'],$style_name."_",0) === 0 ) {

					$postfixNumberPosition = strlen($style_name) + 1;
					$postfixString = substr($style['style_name'],$postfixNumberPosition);
					if ( strpos($postfixString,"_") === false ) {
						$stylePresent = true;
						$incrementalNumber = $postfixString + 1;
						$newStyleName = $style_name."_".$incrementalNumber;
					}
				}
			}
		}

		if(!$stylePresent)
			$newStyleName = $style_name."_1";

		return $newStyleName;
	}
}

/**
* Function to accept ajax call for updating User settings
* @since 1.0
*/
if( !function_exists( "smile_update_settings" ) ){
	function smile_update_settings(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$module_list = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );

	    unset($module_list['action']);
		$new_module_list = array();

		if ( !isset($_POST['cp-access-role']) ) {
			$old_settings = get_option( 'convert_plug_settings' );
			$new_module_list['cp-access-role'] = $old_settings['cp-access-role'];
		}

		foreach( $module_list as $module => $file ){
			$new_module_list[$module] = $file;
		}

		$result = update_option('convert_plug_settings',$new_module_list);
		if($result){
			print_r(json_encode(array(
				'message' => __( 'Settings Updated!', 'smile' )
			)));
		} else {
			print_r(json_encode(array(
				'message' => __( 'No settings were updated. Try again!', 'smile' )
			)));
		}
		die();
	}
}

/**
* Function for ajax callback to save debug options
* @since 1.0
*/
if( !function_exists( "smile_update_debug" ) ) {
	function smile_update_debug(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$opts = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$result = update_option( 'convert_plug_debug', $opts );
		if( $result ){
			print_r(json_encode(array(
				'message' => __( 'Settings Updated!', 'smile' )
			)));
		} else {
			print_r(json_encode(array(
				'message' => __( 'No settings were updated. Try again!', 'smile' )
			)));
		}
		die();
	}
}

/**
* Function to calculate average of array values
* @since 1.0
*/
if( !function_exists( "cp_calculate_average" ) ){
	function cp_calculate_average($arr) {
		$total = 0;
	    $count = count($arr); //total numbers in array
	    foreach ( $arr as $value ) {
	        $total = $total + $value; // total value of array numbers
	    }
	    $average = round( ( $total/$count ), 2 ); // get average value
	    return $average;
	}
}

/**
* Function to check if list is assigned to any modal or info bar
* @since 1.0
*/
if( !function_exists( "cp_is_list_assigned" ) ){
	function cp_is_list_assigned() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$list_id = esc_attr( $_POST['list_id'] ); 
		$is_assigned = false;
		$assigned_to = array();

		$modules = array(
			'modal',
			'info_bar',
			'slide_in'
		);

		foreach( $modules as $module ) {

			$styles = get_option( 'smile_'.$module.'_styles' );
			$variant_tests = get_option( $module.'_variant_tests' );

			if( $styles && is_array($styles) ) {
				foreach ( $styles as $style ) {
					$style_settings = unserialize($style['style_settings']);
					$style_id = $style['style_id'];
					$style_name = urldecode( $style['style_name'] );

					if( isset($style_settings['mailer']) ) {
						$mailer = $style_settings['mailer'];
						$theme = $style_settings['style'];

						if( !isset($style['multivariant']) ) {
							if( $mailer == $list_id ) {
				 				$is_assigned = true;
				 				$link = '?page=smile-'.$module.'-designer&style-view=edit&style='.$style_id.'&theme='.urlencode($theme);
				 				$styleArr = array (
				 					$style_name => $link
				 				);
				 				array_push($assigned_to, $styleArr);
							}
						}

						// check if list is assigned to any variant
						if( $variant_tests && is_array( $variant_tests ) ) {
							if( isset( $variant_tests[ $style_id ] ) ) {
								foreach( $variant_tests[ $style_id ] as $key => $variant_test ){
									$style_settings = unserialize($variant_test['style_settings']);
									$var_style_name = urldecode( $variant_test['style_name'] );
									$variant_style_id = $variant_test['style_id'];
									$mailer = $style_settings['mailer'];
									if( $mailer == $list_id ) {
						 				$is_assigned = true;
						 				$link = '?page=smile-'.$module.'-designer&style-view=variant&variant-test=edit&variant-style='.$variant_style_id.'&style='.stripslashes($var_style_name);
						 				$link .= '&parent-style='.urlencode( stripslashes($style_name) ).'&style_id='.$style_id.'&theme='.urlencode($theme);
						 				$styleArr = array (
						 					$var_style_name => $link
						 				);
						 				array_push($assigned_to, $styleArr);
									}
								}
							}
						}
					}
				}
			}
		}

		$assigned_to = apply_filters( 'is_list_assign_check', $assigned_to, $list_id );

		$is_assigned = ( count( $assigned_to ) > 0 ) ? true : false;

		if( $is_assigned ){
			$styleCount = count($assigned_to);
			print_r(json_encode(array(
				'message' => 'yes',
				'assigned_to' => $assigned_to,
				'style_count' => $styleCount
			)));
		} else {
			print_r(json_encode(array(
				'message' => 'no'
			)));
		}
		die();
	}
}

// Get behavior section settings i.e. Launch,repeat,target control settings
function get_quick_behavior_settings($data,$module) {

	if( $module == 'modal' ) {
		$exit_intent = $data['modal_exit_intent'];
	} else if( $module == 'info_bar' ) {
		$exit_intent = $data['ib_exit_intent'];
	} else if( $module == 'slide_in' )  {
		$exit_intent = $data['slidein_exit_intent'];
	}

	if( isset($data['enable_after_post']) ) {
		$enable_after_content = ( $data['enable_after_post'] ) ? 'Yes' : 'No';
	} else {
		$enable_after_content = '';
	}

	// Define launch control parameters and respective values
	$launch_control = array (
	    'Before User Leaves / Exit Intent'  =>  ( $exit_intent ) ? 'Yes' : 'No',
	    'Load After Seconds' 				=>  ( $data['autoload_on_duration'] ) ? 'Yes, '.$data['load_on_duration'].' Seconds' : 'No',
	    'Load After Scroll' 				=>  ( $data['autoload_on_scroll'] ) ? 'Yes, '.$data['load_after_scroll'].'% Scroll' : 'No',
	    "Launch After Content"              => $enable_after_content,
	    'When User Is Inactive'   			=>  ( $data['inactivity'] ) ? 'Yes' : 'No',
	    'Launch With CSS Class' 			=>  ( $data['custom_class'] !=='') ? 'Yes, with <b>'.$data['custom_class'].'</b>' : 'No',
	);

	// Define repeat control parameters and respective values
    $repeat_control =  array (
         'Enable Cookies'  				=> ( $data['developer_mode'] ) ? 'Yes' : 'No',
         'Do Not Show After Conversion' => ( $data['developer_mode'] ) ? $data['conversion_cookie']. ' Days' : '',
         'Do Not Show After Closing'    => ( $data['developer_mode'] ) ? $data['closed_cookie'].' Days' : '',
    );

    $disabled_pages = $enabled_pages = $disabled_on = $enabled_on = $exclude_post_type = $exclusive_post_type = '';

    // Pages to exclude
  	if( isset( $data['exclude_from'] ) && $data['exclude_from'] !== '' )  {
  		$disabled_pages = explode(",",$data['exclude_from'] );
  		$exclude_pages = '';

  		foreach ( $disabled_pages as $key => $page ) {
  			if( strpos( $page, 'tax-') !== false ) {
  				$tax_id = str_replace( 'tax-', '', $page );
  				$type = cp_get_taxonomy_by_id( $tax_id );
  				$page_title = $type;
  			} else if( strpos( $page, 'post-') !== false ) {
  				$page_title = get_the_title( str_replace( 'post-', '', $page ) );
  			} else if( strpos( $page, 'special-') !== false ) {
  				$page_title = ucfirst( str_replace( 'special-','',$page ) ) . " Page";
  			}

  			$disabled_pages[$key] = substr( $page_title, 0, 15 );
  		}

  		$total_disabled_pages = count($disabled_pages);

  		if( $total_disabled_pages > 5 ) {
  			$disabled_pages = array_slice($disabled_pages, 0, 5, true);
  			$rem_disabled_pages = $total_disabled_pages - 5;
  			$disabled_pages = implode(', ',$disabled_pages);
  			$disabled_pages .= ' and '.$rem_disabled_pages.' more';
  		} else {
  			$disabled_pages = implode(', ',$disabled_pages);
  		}
  	}

  	// Display excluded post types
  	if( isset( $data['exclude_post_type'] ) && $data['exclude_post_type'] !== '' ) {
  		$exclude_post_type = explode(",",$data['exclude_post_type'] );

  		$total_exclude_post_type = count($exclude_post_type);

  		if( $total_exclude_post_type > 5 ) {
  			$exclude_post_type = array_slice($exclude_post_type, 0, 5, true);

  			foreach( $exclude_post_type as $key => $post_type ) {
  				$post_type = str_replace( 'cp-' , '', $post_type );
  				$post_type = ucfirst(str_replace( 'post_' , '', $post_type ));
  				$exclude_post_type[$key] = $post_type;
  			}

  			$rem_total_exclude_post_type = $total_exclude_post_type - 5;
  			$exclude_post_type = implode(', ',$exclude_post_type);
  			$exclude_post_type .= ' and '.$rem_total_exclude_post_type.' more';
  		} else {
  			foreach( $exclude_post_type as $key => $post_type ) {
  				$post_type = str_replace( 'cp-' , '', $post_type );
  				$post_type = ucfirst(str_replace( 'post_' , '', $post_type ));
  				$exclude_post_type[$key] = $post_type;
  			}
  			$exclude_post_type = implode(', ',$exclude_post_type);
  		}
  	}

  	$disabled_on .= '<ul><li></li>';

  	if( $disabled_pages !== '' )  {
  		$disabled_on .= "<li><b>Pages / Posts / Terms</b> - " .$disabled_pages."</li>";
  	}

  	if( $exclude_post_type !== '' )  {
		$disabled_on .= "<li><b>Post Types / Taxonomies</b> - ".$exclude_post_type."</li>";
	}

	$disabled_on .= '</ul>';

	// Display exclusive pages
  	if( isset( $data['exclusive_on'] ) && $data['exclusive_on'] !== '' )  {
  		$enabled_pages = explode(",",$data['exclusive_on'] );
  		$exclude_pages = '';
  		foreach ($enabled_pages as $key => $page) {
  			if( strpos( $page, 'tax-') !== false ) {
  				$tax_id = str_replace( 'tax-', '', $page );
  				$type = cp_get_taxonomy_by_id( $tax_id );
  				$page_title = $type;
  			} else if( strpos( $page, 'post-') !== false ) {
  				$page_title = get_the_title( str_replace( 'post-', '', $page ) );
  			} else if( strpos( $page, 'special-') !== false ) {
  				$page_title = ucfirst( str_replace( 'special-','',$page ) ) . " Page";
  			}

  			$enabled_pages[$key] = substr( $page_title, 0, 15 );
  		}

  		$total_enabled_pages = count($enabled_pages);

  		if( $total_enabled_pages > 5 ) {
  			$enabled_pages = array_slice($enabled_pages, 0, 5, true);
  			$rem_enabled_pages = $total_enabled_pages - 5;
  			$enabled_pages = implode(', ',$enabled_pages);
  			$enabled_pages .= ' and '.$rem_enabled_pages.' more';
  		} else {
  			$enabled_pages = implode(', ',$enabled_pages);
  		}
  	}

  	// Display exclusive post types
  	if( isset( $data['exclusive_post_type'] ) && $data['exclusive_post_type'] !== '' ) {
  		$exclusive_post_type = explode(",",$data['exclusive_post_type'] );
  		$total_exclusive_post_type = count($exclusive_post_type);

  		if( $total_exclusive_post_type > 5 ) {
  			$exclusive_post_type = array_slice($exclusive_post_type, 0, 5, true);

  			foreach( $exclusive_post_type as $key => $post_type ) {
  				$post_type = str_replace( 'cp-' , '', $post_type );
  				$post_type = ucfirst(str_replace( 'post_' , '', $post_type ));
  				$exclusive_post_type[$key] = $post_type;
  			}

  			$rem_total_exclusive_post_type = $total_exclusive_post_type - 5;
  			$exclusive_post_type = implode(', ',$exclusive_post_type);
  			$exclusive_post_type .= ' and '.$rem_total_exclusive_post_type.' more';
  		} else {
  			foreach( $exclusive_post_type as $key => $post_type ) {
  				$post_type = str_replace( 'cp-' , '', $post_type );
  				$post_type = ucfirst(str_replace( 'post_' , '', $post_type ));
  				$exclusive_post_type[$key] = $post_type;
  			}
  			$exclusive_post_type = implode(', ',$exclusive_post_type);
  		}
  	}

  	$enabled_on .= '<ul><li></li>';

	if( $enabled_pages !== '' )  {
		$enabled_on .= "<li><b>Pages / Posts / Terms</b> - " .$enabled_pages."</li>";
  	}

  	if( $exclusive_post_type !== '' )  {
		$enabled_on .= "<li><b>Post Types / Taxonomies</b> - ".$exclusive_post_type."</li>";
	}

	$enabled_on .= '</ul>';

  	// Define target pages parameters and respective values
    $target_pages = array(
    	'Enable On Complete Site' 	 => ( $data['global'] ) ? '<b>Yes</b>' : '<b>No</b>',
        'Exceptionally, Disable On'  => ( $data['global'] && strip_tags($disabled_on) !== '' ) ? $disabled_on : '',
        'Enable Only On'   			 => ( !$data['global'] && strip_tags($enabled_on) !== '' ) ? $enabled_on : ''
    );

  	// Define target visitors parameters and respective values
    $target_visitors = array(
        'Logged In Users'  => ( $data['show_for_logged_in'] ) ? 'Yes' : 'No',
        'First Time Users' => ( $data['display_on_first_load'] ) ? 'Yes' : 'No',
    );

    $behavior_settings =  '<div class=\'cp-row first-row\'><div class=\'col-md-6 cp-behavior-col-first\'><ul>';
    $behavior_settings .= '<li><i class=\'connects-icon-location-2\'></i><b>Launch Control</b></li>';

    foreach( $launch_control as $key => $value ) {
    	if( $value !== '' )
        	$behavior_settings .= '<li>'.$key.' - <b>'. $value .'</b></li>';
    }
    $behavior_settings .= '</ul></div>';

    $behavior_settings .= '<div class=\'col-md-6 cp-behavior-col-second\'><ul><li><i class=\'connects-icon-repeat\'></i><b>Repeat Control</b></li>';

  	foreach( $repeat_control as $key => $value ) {
    	if( $value !== '' )
        	$behavior_settings .= '<li>'.$key.' - <b>'. $value .'</b></li>';
    }

    $behavior_settings .= '</div></div><div class=\'cp-row second-row\'><div class=\'col-md-6 cp-behavior-col-first\'><ul><li><i class=\'connects-icon-paper\'></i><b>Target Pages</b></li>';

   	foreach( $target_pages as $key => $value ) {
    	if( $value !== '' )
        	$behavior_settings .= '<li>'.$key.' - '. $value .'</li>';
    }

    $behavior_settings .= '</ul></div>';

    $behavior_settings .= '<div class=\'col-md-6 cp-behavior-col-second\'><ul><li><i class=\'connects-icon-head\'></i><b>Target Visitors</b></li>';

    foreach( $target_visitors as $key => $value ) {
    	if( $value !== '' )
       		$behavior_settings .= '<li>'.$key.' - <b>'. $value .'</b></li>';
    }

    $behavior_settings .= '</ul></div></div>';

    return $behavior_settings;
}


// Function to add behavior settings icon after delete button
function cp_before_delete_action_init( $settings, $module ) {
	ob_start();
	// Retrieve behavior related settings
    $behavior_settings = get_quick_behavior_settings( $settings , $module );
    if( !isset($settings['variant-style']) ) {
	    $style_id = $settings['style_id'];
    } else {
    	$style_id = $settings['variant-style'];
	}

    $analyticsData = get_option( 'smile_style_analytics' );

	?>
	<a class="action-list cp-behavior-settings" data-position="left" style="margin-left: 25px;" data-settings="<?php echo $behavior_settings; ?>">
       <span class="action-tooltip">Behavior Quick View</span><i class="connects-icon-paper"></i>
    </a>
    <?php if( isset( $analyticsData[$style_id] ) ) { ?>
	    <a class="action-list cp-reset-analytics" data-style="<?php echo esc_attr( $style_id ); ?>" data-position="left" style="margin-left: 25px;cursor:pointer;">
	       <span class="action-tooltip">Reset Analytics</span><i class="connects-icon-reload"></i>
	    </a>
    <?php } ?>

    <?php
    return ob_get_clean();
}

add_filter( 'cp_before_delete_action', 'cp_before_delete_action_init', 10 , 2 );


// Get taxonomy name by ID
function cp_get_taxonomy_by_id($tax_id) {

	$args = array(
	   'public'   => true,
	   '_builtin' => false
	);

	$output = 'objects'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );

	if( is_array($taxonomies) ) {
	    foreach ( $taxonomies as $taxonomy ) {

	        $terms = get_terms( $taxonomy->name, array(
				'orderby'    => 'count',
				'hide_empty' => 0,
			 ) );

			if( !empty( $terms ) ){
				foreach( $terms as $term ) {
					if( $tax_id == $term->term_id ) {
						return $term->name;
					}
				}
			}
		}
	}

	$args = array(
	   'public'   => true,
	   '_builtin' => true
	);

	$taxonomies = get_taxonomies( $args, $output, $operator );

	if( is_array($taxonomies) ) {
	    foreach ( $taxonomies as $taxonomy ) {

	        $terms = get_terms( $taxonomy->name, array(
				'orderby'    => 'count',
				'hide_empty' => 0,
			 ) );

			if( !empty( $terms ) ){
				foreach( $terms as $term ) {
					if( $tax_id == $term->term_id ) {
						return $term->name;
					}
				}
			}
		}
	}

	return false;
}


/**
 * Function to reset analytics data for style
 * @since 1.1.1
*/
if( !function_exists( "cp_reset_analytics" ) ){
	function cp_reset_analytics($style_id) {
		$analyticsData = get_option( 'smile_style_analytics' );
		if( isset( $analyticsData[$style_id]) ) {
			unset($analyticsData[$style_id]);
		}

		$result = update_option( "smile_style_analytics", $analyticsData );
		return $result;
	}
}

if( !function_exists( "cp_reset_analytics_action" ) ){
	function cp_reset_analytics_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		check_admin_referer( "cp-reset-analytics", "security_nonce" );

		$style_id = esc_attr( $_POST['style_id'] );
		$result = cp_reset_analytics($style_id);
		echo 'reset';
		die();
	}
}


if ( ! function_exists( 'cp_get_posts_by_query' ) ) {
	function cp_get_posts_by_query() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$searchString = isset( $_POST['q'] ) ? $_POST['q'] : '';
		$data = array();
		$result = array();

		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
	    $post_types = get_post_types( $args, $output, $operator );

	    $post_types["Posts"] = "post";
	    $post_types["Pages"] = "page";

	    foreach ( $post_types as $key => $post_type ) {

	    	$data = array();

	    	$query = new WP_Query( array( 's' => $searchString, 'post_type' => $post_type ) );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$title = get_the_title();
					$ID = get_the_id();
					$data[] = array('id' => "post-".$ID, 'text' => $title );
				}
			}

			if( is_array($data) && !empty($data) ) {
				$result[] = array (
						"text" => $key,
						"children" => $data
				);
			}
	    }


		$data = array();

		wp_reset_postdata();

		$args = array(
		   'public'   => true
		);

		$output = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
	    $taxonomies = get_taxonomies( $args, $output, $operator );

	    foreach ( $taxonomies as $taxonomy ) {
	        $terms = get_terms( $taxonomy->name, array(
				'orderby'    => 'count',
				'hide_empty' => 0,
				'name__like' => $searchString
			 ) );

	        $data = array();

	        $label = ucwords( $taxonomy->label );

			 if( !empty( $terms ) ) {

				foreach( $terms as $term ) {

					$data[] = array( 'id' => "tax-".$term->term_id , 'text' => $term->name );

				}
			}

			if( is_array($data) && !empty($data) ) {
				$result[] = array (
					"text" => $label,
					"children" => $data
				);
			}
		}

		$data = array();

		// Special Pages
		$spacial_pages = array(
			'blog' 			=> 'Blog / Posts Page',
			'front_page' 	=> 'Front Page',
			'archive' 		=> 'Archive Page',
			'author' 		=> 'Author Page',
			'search' 		=> 'Search Page',
			'404' 			=> '404 Page',
		);

		foreach ( $spacial_pages as $page => $title ) {
			$data[] = array( 'id' => "special-".$page , 'text' => $title );
		}

		if( is_array($data) && !empty($data) ) {
			$result[] = array (
				"text" => 'Special Pages',
				"children" => $data
			);
		}

		// return the result in json
		echo json_encode( $result );
		die();
	}
}

/** 
 * Get list of active campaigns
 *
 * @since 2.2.0
*/
if(!function_exists('cp_get_active_campaigns')) {
	function cp_get_active_campaigns() {

		$source = ( isset( $_POST['source'] ) && $_POST['source'] == 'cp-addon' ) ? true : false;

		if ( $source ) {
			$smile_lists = get_option('smile_lists');
			$req_data = array();
		    // to unset deactivated / inactive mailer addons
		    if( is_array($smile_lists) ) {
		    	foreach( $smile_lists as $key => $list ) {
		    		$provider = $list['list-provider'];
		    		if( $provider !== 'Convert Plug' ) {
			    		if( !isset( Smile_Framework::$addon_list[$provider] ) && !isset( Smile_Framework::$addon_list[strtolower($provider)] ) ) {
			    			unset( $smile_lists[$key] );
			    		} else {
			    			$data = array(
							"list-provider" => $list['list-provider'],
			    			"list-name"     => $list['list-name']
			    			);
			    			$req_data[$key] = $data;
			    		}

			    	} else {
			    		$data = array(
							"list-provider" => $list['list-provider'],
			    			"list-name"     => $list['list-name']
			    			);
			    		$req_data[$key] = $data;
			    	}
		    	}
		    }

		    print_r(json_encode($req_data));
		}
		die();
	}
}


if ( ! function_exists( 'cp_import_presets' ) ) {

	function cp_import_presets() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$module =  isset( $_POST['module'] ) ? $_POST['module'] : '';
		$preset =  isset( $_POST['preset'] ) ? $_POST['preset'] : '';

		if ( $module !== '' ) {
			$cp_import = new cpImport( $module, $preset );
			//$cp_import->cp_presets_list($module);
		}

	}

}

if ( ! function_exists( 'cp_import_presets_step2' ) ) {
	function cp_import_presets_step2() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$module =  isset( $_POST['module'] ) ? $_POST['module'] : '';
		if ( $module !== '' ) {
			$cp_import = new cpImport( $module );
			$cp_import->cp_import_preset_frontend($module);
		}
	}
}

if ( ! function_exists( 'handle_cp_export_list_action' ) ) {
	function handle_cp_export_list_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$list_id = esc_attr( $_POST['list_id'] );
		check_admin_referer( 'export-list-' . $list_id );

		$path = plugin_dir_path( __FILE__ );

		if( $list_id !== '' ) {

			$smile_lists = get_option('smile_lists');
			$provider = '';
			$list_name = '';
			if( $smile_lists )  {
				if( isset($smile_lists[$list_id]) ) {
					$list = $smile_lists[$list_id];
					$list_name = $list['list-name'];
					$provider = $list['list-provider'];
				}      
			}

			$id = isset( $list['list'] ) ? $list['list'] : '';
			$listName = str_replace(" ","_",strtolower( trim( $list['list-name'] ) ) );
			$mailer = str_replace(" ","_",strtolower( trim( $provider ) ) );

			if( $mailer !== "convert_plug" ) {
				$listOption = "cp_".$mailer."_".$listName;
				$contacts = get_option( $listOption);
			} else {
				$listOption = "cp_connects_".$listName;
				$contacts = get_option($listOption);
			}
			//var_dump($contacts);die();
			if( is_array($contacts) && $contacts ) {

				$export_data = cpGenerateCsv($contacts);
				$content = $export_data;

				$file_name = $path.'cp_export['.$list_name.'].csv';
				$file_url = plugins_url('cp_export['.$list_name.'].csv', __FILE__ );
				$handle = fopen($file_name, "w");
				fwrite($handle, $content);
				fclose($handle);

				header('Pragma: public');   // required
				header('Expires: 0');   // no cache
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
				header('Cache-Control: private',false);
				header('Content-Type: application/application/csv');
				header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: '.filesize($file_name));  // provide file size
				header('Connection: close');
				readfile($file_name);
				unlink( $file_name );
				exit();

			} else {			
				exit();
			}

		} 

	}
}

//export modal style
if ( ! function_exists( 'cp_export_modal_action' ) ) {
	function cp_export_modal_action(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data_style = isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';

		check_admin_referer( 'export-modal-' . $data_style );
		
		$path = plugin_dir_path( __FILE__ );
		$prev_styles = get_option('smile_modal_styles');
		$variant_tests = get_option('modal_variant_tests');
		
		$data_style_name = '';
		if( isset( $_POST['style_name'] ) ) {
			$data_style_name = strtolower( stripcslashes( $_POST['style_name'] )) ;
			$data_style_name = str_replace(' ', '_', $data_style_name);
		}

		$data_style_name = $data_style_name .'_'. $data_style;

		if( $data_style !== "" ) {
			if(is_array($prev_styles) && !empty($prev_styles)){
				foreach($prev_styles as $key => $style){

					$hasVariants = false;
					$style_name = $style['style_name'];
					$style_id = $style['style_id'];

					if( $data_style == $style_id ) {

					    if( $variant_tests ) {
					        if ( array_key_exists($data_style,$variant_tests) && !empty($variant_tests[$data_style]) ) {
					            $hasVariants = true;
					        }
					    }

						$style_settings = unserialize($style['style_settings']);
						$exp_settings = array();
						foreach( $style_settings as $title => $value ){
						    if( !is_array( $value ) ){
						     	$value = urldecode($value);
								$exp_settings[$title] = htmlentities(stripslashes(utf8_encode($value)), ENT_QUOTES, "UTF-8" );
							} else {
								$val = array();
								foreach( $value as $ex_title => $ex_val ) {
								    $val[$ex_title] = $ex_val;
								}
								$exp_settings[$title] =str_replace('"','&quot;',$val);
							}
						}
						$export = $style;
						$export['style_settings'] = $exp_settings;

						$modal_image = isset( $style_settings['modal_image'] ) ? $style_settings['modal_image'] : '' ;
						$close_image = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '' ;
						$bg_image = isset( $style_settings['modal_bg_image'] ) ? $style_settings['modal_bg_image'] : '';
						$content_bg_image = isset( $style_settings['content_bg_image'] ) ? $style_settings['content_bg_image'] : '';
						$form_bg_image = isset( $style_settings['form_bg_image'] ) ? $style_settings['form_bg_image'] : '';

						if( $hasVariants ) {
							foreach($variant_tests[$data_style] as $variant) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}

			$dir = 'modal_'.$data_style_name;
			if( !is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

			// Get images attached to the style through settings, copy them in export directory and store them in media array
			$media = array();
			if( $modal_image !== "" )
			{
				if ( ( isset( $style_settings['modal_img_src'] ) && $style_settings['modal_img_src'] == 'upload_img'  )
					|| !isset( $style_settings['modal_img_src'] ) )  {

					$modal_image = str_replace( "%7C", "|", $modal_image );
					if ( strpos($modal_image,'http') !== false ) {
						$modal_image = explode( '|', $modal_image );
						$modal_image = $modal_image[0];
						$modal_image = urldecode( $modal_image );
					} else {
						$modal_image = explode("|", $modal_image);
						$modal_image = wp_get_attachment_image_src($modal_image[0],$modal_image[1]);
						$modal_image = $modal_image[0];
					}

					$modal_image_name = basename( $modal_image );
					copy( $modal_image, $dir.'/'.$modal_image_name );

					$media['modal_image'] = $dir.'/'.$modal_image_name;
				}
			}

			if( $close_image !== "" )
			{
				if ( ( isset( $style_settings['close_image_src'] ) && $style_settings['close_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['close_image_src'] ) )  {

					$close_image = str_replace( "%7C", "|", $close_image );
					if ( strpos($close_image,'http') !== false ) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode("|", $close_image);
						$close_image = wp_get_attachment_image_src($close_image[0],$close_image[1]);
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					if ( $close_image_name !== '' ) {
						copy( $close_image, $dir.'/'.$close_image_name );
						$media['close_image'] = $dir.'/'.$close_image_name;
					}
				}
			}

			if( $bg_image !== "" )
			{
				if ( ( isset( $style_settings['modal_bg_image_src'] ) && $style_settings['modal_bg_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['modal_bg_image_src'] ) )  {

					$bg_image = str_replace( "%7C", "|", $bg_image );
					if (strpos($bg_image,'http') !== false) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode("|", $bg_image);
						$bg_image = wp_get_attachment_image_src($bg_image[0],$bg_image[1]);
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir.'/'.$bg_image_name );

					$media['modal_bg_image'] = $dir.'/'.$bg_image_name;
				}

			}

			if( $content_bg_image !== "" )
			{
				$content_bg_image = str_replace( "%7C", "|", $content_bg_image );
				if (strpos($content_bg_image,'http') !== false) {
					$content_bg_image = explode( '|', $content_bg_image );
					$content_bg_image = $content_bg_image[0];
					$content_bg_image = urldecode( $content_bg_image );
				} else {
					$content_bg_image = explode("|", $content_bg_image);
					$content_bg_image = wp_get_attachment_image_src($content_bg_image[0],$content_bg_image[1]);
					$content_bg_image = $content_bg_image[0];
				}

				$content_bg_image_name = basename( $content_bg_image );
				copy( $content_bg_image, $dir.'/'.$content_bg_image_name );

				$media['content_bg_image'] = $dir.'/'.$content_bg_image;
			}

			if( $form_bg_image !== "" )
			{
				$form_bg_image = str_replace( "%7C", "|", $form_bg_image );
				if (strpos($form_bg_image,'http') !== false) {
					$form_bg_image = explode( '|', $form_bg_image );
					$form_bg_image = $form_bg_image[0];
					$form_bg_image = urldecode( $form_bg_image );
				} else {
					$form_bg_image = explode("|", $form_bg_image);
					$form_bg_image = wp_get_attachment_image_src($form_bg_image[0],$form_bg_image[1]);
					$form_bg_image = $form_bg_image[0];
				}

				$form_bg_image_name = basename( $form_bg_image );
				copy( $form_bg_image, $dir.'/'.$form_bg_image_name );

				$media['form_bg_image'] = $dir.'/'.$form_bg_image;
			}

			if( !empty( $media ) ){
				$export['media'] = $media;
			}

			$export['module'] = 'modal';
			$export_data = json_encode( $export );

			$content = $export_data;

			$file_name = $dir.'/modal_'.$data_style_name.'.txt';
			$file_url = plugins_url( $dir.'/modal_'.$data_style_name.'.txt', __FILE__ );
			$handle = fopen( $file_name, "w" );
			fwrite($handle, $content);
			fclose($handle);

			$files = glob( "{$dir}/*" );
			$export_file = $dir.'.zip';

			smile_create_file( $dir , $files , $export_file );
		} 
	}
}

//export infobar
if ( ! function_exists( 'cp_export_infobar_action' ) ) {
	function cp_export_infobar_action(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data_style = isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';
		check_admin_referer( 'export-infobar-' . $data_style );

		$path = plugin_dir_path( __FILE__ );
		$prev_styles = get_option('smile_info_bar_styles');
		$variant_tests = get_option('info_bar_variant_tests');

		$data_style_name = '';
		if( isset( $_POST['style_name'] ) ) {
			$data_style_name = strtolower( stripcslashes( sanitize_text_field( $_POST['style_name'] ) ));
			$data_style_name = str_replace(' ', '_', $data_style_name);
		}

		$data_style_name = $data_style_name .'_'. $data_style;

		if( $data_style !== "" ) {
			if( is_array($prev_styles) && !empty($prev_styles) ){
				foreach( $prev_styles as $key => $style ){

					$hasVariants = false;
					$style_name = $style['style_name'];
					$style_id = $style['style_id'];

					if( $data_style == $style_id ) {

					    if( $variant_tests ) {
					        if ( array_key_exists($data_style,$variant_tests) && !empty($variant_tests[$data_style]) ) {
					            $hasVariants = true;
					        }
					    }

						$style_settings = unserialize($style['style_settings']);
						$exp_settings = array();
						foreach( $style_settings as $title => $value ){
						    if( !is_array( $value ) ){
						     	$value = urldecode($value);
								$exp_settings[$title] = htmlentities(stripslashes(utf8_encode($value)), ENT_QUOTES, "UTF-8" );//esc_attr(str_replace('"','&quot;',$value));
							} else {
								$val = array();
								foreach( $value as $ex_title => $ex_val ) {
								    $val[$ex_title] = $ex_val;
								}
								$exp_settings[$title] =str_replace('"','&quot;',$val);
							}
						}
						$export = $style;
						$export['style_settings'] = $exp_settings;

						$info_bar_image = isset( $style_settings['info_bar_image'] ) ? $style_settings['info_bar_image'] : '' ;
						$close_image = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '' ;
						$bg_image = isset( $style_settings['info_bar_bg_image'] ) ? $style_settings['info_bar_bg_image'] : '';

						if( $hasVariants ) {
							foreach($variant_tests[$data_style] as $variant) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}
			$dir = 'info_bar_'.$data_style_name;
			if( !is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

			// Get images attached to the style through settings, copy them in export directory and store them in media array
			$media = array();
			if( $info_bar_image !== "" )
			{
				$info_bar_image = str_replace( "%7C", "|", $info_bar_image );
				if (strpos($info_bar_image,'http') !== false) {
					$info_bar_image = explode( '|', $info_bar_image );
					$info_bar_image = $info_bar_image[0];
					$info_bar_image = urldecode( $info_bar_image );
				} else {
					$info_bar_image = explode("|", $info_bar_image);
					$info_bar_image = wp_get_attachment_image_src($info_bar_image[0],$info_bar_image[1]);
					$info_bar_image = $info_bar_image[0];
				}

				$info_bar_image_name = basename( $info_bar_image );
				copy( $info_bar_image, $dir.'/'.$info_bar_image_name );

				$media['info_bar_image'] = $dir.'/'.$info_bar_image_name;

			}

			if( $close_image !== "" )
			{
				if ( ( isset( $style_settings['close_ib_image_src'] ) && $style_settings['close_ib_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['close_ib_image_src'] ) )  {

					$close_image = str_replace( "%7C", "|", $close_image );
					if (strpos($close_image,'http') !== false) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode("|", $close_image);
						$close_image = wp_get_attachment_image_src($close_image[0],$close_image[1]);
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					copy( $close_image, $dir.'/'.$close_image_name );

					$media['close_image'] = $dir.'/'.$close_image_name;
				}

			}

			if( $bg_image !== "" )
			{
				if ( ( isset( $style_settings['info_bar_bg_image_src'] ) && $style_settings['info_bar_bg_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['info_bar_bg_image_src'] ) )  {

					$bg_image = str_replace( "%7C", "|", $bg_image );
					if (strpos($bg_image,'http') !== false) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode("|", $bg_image);
						$bg_image = wp_get_attachment_image_src($bg_image[0],$bg_image[1]);
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir.'/'.$bg_image_name );

					$media['info_bar_bg_image'] = $dir.'/'.$bg_image_name;
				}

			}

			if( !empty( $media ) ){
				$export['media'] = $media;
			}

			$export['module'] = 'info_bar';

			$export_data = json_encode( $export );

			$content = $export_data;

			$file_name = $dir.'/info_bar_'.$data_style_name.'.txt';
			$file_url = plugins_url($dir.'/info_bar_'.$data_style_name.'.txt', __FILE__ );
			$handle = fopen($file_name, "w");
			fwrite($handle, $content);
			fclose($handle);

			$files = glob( "{$dir}/*" );
			$export_file = $dir.'.zip';

			smile_create_file( $dir , $files , $export_file );

		}
	}
}

//export slidein settings -

if ( ! function_exists( 'cp_export_slidein_action' ) ) {
	function cp_export_slidein_action(){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}

		$data_style = isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';

		check_admin_referer( 'export-slidein-' . $data_style );
		
		$path = plugin_dir_path( __FILE__ );
		$prev_styles = get_option('smile_slide_in_styles');
		$variant_tests = get_option('slide_in_variant_tests');

		$data_style_name = '';
		if( isset( $_POST['style_name'] ) ) {
			$data_style_name = strtolower( stripcslashes( sanitize_text_field( $_POST['style_name'] ) ));
			$data_style_name = str_replace( ' ', '_', $data_style_name );
		}

		$data_style_name = $data_style_name .'_'. $data_style;

		if( $data_style !== "" ) {
			if( is_array($prev_styles) && !empty($prev_styles) ){
				foreach( $prev_styles as $key => $style ){

					$hasVariants = false;
					$style_name = $style['style_name'];
					$style_id = $style['style_id'];

					if( $data_style == $style_id ) {

					    if( $variant_tests ) {
					        if ( array_key_exists($data_style,$variant_tests) && !empty($variant_tests[$data_style]) ) {
					            $hasVariants = true;
					        }
					    }

						$style_settings = unserialize($style['style_settings']);
						$exp_settings = array();
						foreach( $style_settings as $title => $value ){
						    if( !is_array( $value ) ){
						     	$value = urldecode($value);
								$exp_settings[$title] = htmlentities(stripslashes(utf8_encode($value)), ENT_QUOTES, "UTF-8" );//esc_attr(str_replace('"','&quot;',$value));
							} else {
								$val = array();
								foreach( $value as $ex_title => $ex_val ) {
								    $val[$ex_title] = $ex_val;
								}
								$exp_settings[$title] =str_replace('"','&quot;',$val);
							}
						}
						$export = $style;
						$export['style_settings'] = $exp_settings;

						$slidein_image = isset( $style_settings['slidein_image'] ) ? $style_settings['slidein_image'] : '' ;
						$close_image = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '' ;
						$bg_image = isset( $style_settings['slide_in_bg_image'] ) ? $style_settings['slide_in_bg_image'] : '';

						if( $hasVariants ) {
							foreach($variant_tests[$data_style] as $variant) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}
			$dir = 'slide_in_'.$data_style_name;
			if( !is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

		// Get images attached to the style through settings, copy them in export directory and store them in media array
			$media = array();

			if( $close_image !== "" )
			{
				if ( ( isset( $style_settings['close_si_image_src'] ) && $style_settings['close_si_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['close_si_image_src'] ) )  {

					$close_image = str_replace( "%7C", "|", $close_image );
					if (strpos($close_image,'http') !== false) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode("|", $close_image);
						$close_image = wp_get_attachment_image_src($close_image[0],$close_image[1]);
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					copy( $close_image, $dir.'/'.$close_image_name );

					$media['close_image'] = $dir.'/'.$close_image_name;
				}
			}

			if( $bg_image !== "" )
			{
				if ( ( isset( $style_settings['slide_in_bg_image_src'] ) && $style_settings['slide_in_bg_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['slide_in_bg_image_src'] ) )  {

					$bg_image = str_replace( "%7C", "|", $bg_image );
					if (strpos($bg_image,'http') !== false) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode("|", $bg_image);
						$bg_image = wp_get_attachment_image_src($bg_image[0],$bg_image[1]);
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir.'/'.$bg_image_name );

					$media['slide_in_bg_image'] = $dir.'/'.$bg_image_name;
				}

			}

			if( $slidein_image !== "" )
			{
				if ( ( isset( $style_settings['slidein_image_src'] ) && $style_settings['slidein_image_src'] == 'upload_img'  )
					|| !isset( $style_settings['slidein_image_src'] ) )  {

					$slidein_image = str_replace( "%7C", "|", $slidein_image );
					if (strpos($slidein_image,'http') !== false) {
						$slidein_image = explode( '|', $slidein_image );
						$slidein_image = $slidein_image[0];
						$slidein_image = urldecode( $slidein_image );
					} else {
						$slidein_image = explode("|", $slidein_image);
						$slidein_image = wp_get_attachment_image_src($slidein_image[0],$slidein_image[1]);
						$slidein_image = $slidein_image[0];
					}

					$slidein_image_name = basename( $slidein_image );
					copy( $slidein_image, $dir.'/'.$slidein_image_name );

					$media['slidein_image'] = $dir.'/'.$slidein_image_name;
				}

			}
			if( !empty( $media ) ){
				$export['media'] = $media;
			}

			$export['module'] = 'slide_in';

			$export_data = json_encode( $export );
			$content = $export_data;

			$file_name = $dir.'/slide_in_'.$data_style_name.'.txt';
			$handle = fopen( $file_name, "w" );
			fwrite( $handle, $content );
			fclose( $handle );			

			$files = glob( "{$dir}/*" );
			$export_file = $dir.'.zip';

			smile_create_file( $dir , $files , $export_file );
			
		}
	}  
}

if ( ! function_exists( 'smile_create_file' ) ) {
	function smile_create_file( $dir , $files , $export_file ){

		if ( ! current_user_can( 'access_cp' ) ) {
			die(-1);
		}
		
		$result = smile_create_export_zip( $files, $export_file , true );

		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime( $export_file ) ).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'.basename( $export_file ).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize( $export_file ) );	// provide file size
		header('Connection: close');
		readfile( $export_file );

		// Remove exported directory and its content
		foreach( glob( "{$dir}/*" ) as $file)
	    {
			unlink( $file );
	    }
		unlink( $export_file );
		rmdir( $dir );
		exit();	
	}
}

//create zip file
if ( ! function_exists( 'smile_create_export_zip' ) ) {
	function smile_create_export_zip( $files = array(), $destination = '', $overwrite = false ) {
		//if the zip file already exists and overwrite is false, return false
		if( file_exists($destination) && !$overwrite ) { 
			return false; 
		}

		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if( count($valid_files) ) {
			//create the archive
			$zip = new ZipArchive();
			if ( file_exists( $destination ) ) {
			 	$zipCreate =  $zip->open($destination, ZipArchive::OVERWRITE);
			} else {
				$zipCreate = $zip->open($destination, ZipArchive::CREATE);
			}

			if ( TRUE !== $zipCreate ) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}

			//close the zip -- done!
			$zip->close();

			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}
}