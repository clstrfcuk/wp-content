<?php
/*
* Global functions for modal
*/

if( !function_exists( "smile_get_live_styles" )){
	function smile_get_live_styles(){
		$styles = get_option( 'smile_modal_styles' );
		$smile_variant_tests = get_option( 'modal_variant_tests' );
		$live_array = array();
		if( !empty( $styles ) ) {
			foreach( $styles as $key => $style ){
				$settings = unserialize( $style[ 'style_settings' ] );

				$split_tests = isset( $smile_variant_tests[$style['style_id']] ) ? $smile_variant_tests[$style['style_id']] : '';
				if( is_array( $split_tests ) && !empty( $split_tests ) ) {
					$split_array = array();
					$live = isset( $settings[ 'live' ] ) ? (int)$settings[ 'live' ] : false;
					if( $live ){
						array_push( $split_array, $styles[ $key ] );
					}
					foreach( $split_tests as $key => $test ) {
						$settings = unserialize( $test[ 'style_settings' ] );
						$live = isset( $settings[ 'live' ] ) ? (int)$settings[ 'live' ] : false;
						if( $live ){
							array_push( $split_array, $test );
						}
					}
					if( !empty( $split_array ) ) {
						$key 	= array_rand( $split_array, 1 );
						$array 	= $split_array[$key];
						array_push( $live_array, $array );
					}
				} else {
					$live = isset( $settings[ 'live' ] ) ? (int)$settings[ 'live' ] : false;
					if( $live ){
						array_push( $live_array, $styles[ $key ] );
					}
				}
			}
		}

		return $live_array;
	}
}
/*add_action('cp_append_css_code', 'cp_append_css_code_init');

if( !function_exists( "cp_append_css_code_init" )){
	function cp_append_css_code_init( $style ) {
		/* CP - Modal Styling
		echo '<style type="text/css">'.$style.'</style>';
	/*}
}*/

if( !function_exists( "cp_generate_style_css" )){
function cp_generate_style_css( $a ) {

	$style_class 	= ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';
	$StyleID 		= ( isset( $a['uid'] ) ) ? esc_attr( $a['uid'] ) : '';

	/** = Submit Button - CSS
	 *-----------------------------------------------------------*/
	$shadow = $style = '';

	$c_hover  	= ( isset( $a['button_bg_hover_color'] ) ) ? esc_attr( $a['button_bg_hover_color'] ) : '';
	$c_normal 	= ( isset( $a['button_bg_color'] ) ) ? esc_attr( $a['button_bg_color'] ) : '';
	$light 		= ( isset( $a["button_bg_gradient_color"] ) ) ? esc_attr( $a["button_bg_gradient_color"] ) : '';
	$h_color 	= ( isset( $a["button_txt_hover_color"] ) ) ? esc_attr( $a["button_txt_hover_color"] ) : '';

	$class = ( isset( $a['btn_style'] ) ) ? $a['btn_style'] : '' ;

	//	Apply box shadow to submit button - If its set & equals to - 1
	if( isset( $a["btn_shadow"] ) && $a["btn_shadow"] == 1 ) {
		$shadow .= "box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);";
	}

	$radius = '';
	if( isset( $a["btn_border_radius"] ) && $a["btn_border_radius"] != '' ) {
		$radius = 'border-radius:' . esc_attr( $a["btn_border_radius"] ) . 'px;';
	}

	switch( $class ) {

		case 'cp-btn-flat': 	//	Normal
								$style .= "." . $StyleID . " .cp-modal .cp-submit." 	. $class . " { "
										. " 	background: " 		. $c_normal . "!important;"
										. 		$radius . $shadow
										. "}"

								//	Hover
										. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":hover { "
										. " 	background: " 		. $c_hover	. "!important;"
										. "}";
			break;
		case 'cp-btn-3d': 		//	Normal
								$style  .= "." . $StyleID . " .cp-modal .cp-submit." 	. $class . " { "
										 . " 	background: " 		. $c_normal . " !important;"
										 . " 	box-shadow: 0 6px " . $c_hover . ";"
										 . " 	position: relative;"
										 . 		$radius
										 . "}"

								//	Hover
										. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":hover { "
										. " 	box-shadow: 0 4px " . $c_hover . ";"
										. " 	top: 2px;"
										. "}"

								//	Active
										. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":active { "
										. " 	top: 6px;"
										. "		box-shadow: 0 0px " . $c_hover . ";"
										. "}";
			break;
		case 'cp-btn-outline': 	//	Normal
								$style .= "." . $StyleID . " .cp-modal .cp-submit." . $class . " { "
										. " 	background: transparent;"
										. " 	border: 2px solid " . $c_normal . ";"
										. " 	color: inherit;"
										.		$shadow . $radius
										. "}"

								//	Hover
										. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":hover { "
										. " 	background: " 		. $c_hover	. ";"
										. " 	border: 2px solid " . $c_hover . ";"
										. " 	color: ". $h_color
										. "}"

								//	Inner span color inherit to apply hover color
										. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":hover span { color: inherit !important ; } ";
			break;
		case 'cp-btn-gradient': 	//	Normal
									$style  .= "." . $StyleID . " .cp-modal .cp-submit." . $class . " { "
											. 		$shadow . $radius
											. " 	border: none;"
											. "		background: -webkit-linear-gradient(" . $light . ", " . $c_normal . ");"
											. "		background: -o-linear-gradient(" . $light . ", " . $c_normal . ");"
											. "		background: -moz-linear-gradient(" . $light . ", " . $c_normal . ");"
											. "		background: linear-gradient(" . $light . ", " . $c_normal . ");"
											. "}"

									//	Hover
											. "." . $StyleID . " .cp-modal .cp-submit." 	. $class . ":hover { "
											. " 	background: " 		. $c_normal	. ";"
											. "}";
			break;
	}

		//customcss
		$style  .= $a['custom_css'];

		// 	Append CSS code
		//apply_filters( 'cp_append_css_code' , $style );
		echo '<style type="text/css" id="praj">'.$style.'</style>';
	}
}

if( !function_exists( 'generateBorderCss' ) ){
	function generateBorderCss($string){
		$pairs = explode( '|', $string );
		$result = array();
		foreach( $pairs as $pair ){
			$pair = explode( ':', $pair );
			$result[ $pair[0] ] = $pair[1];
		}

		$cssCode1 = '';
		$cssCode1 .= $result['br_tl'] . 'px ' . $result['br_tr'] . 'px ' . $result['br_br'] . 'px ';
		$cssCode1 .= $result['br_bl'] . 'px';
		$result['border_width']=' ';
		$text = '';
		$text .= 'border-radius: ' . $cssCode1 .';';
		$text .= '-moz-border-radius: ' . $cssCode1 .';';
		$text .= '-webkit-border-radius: ' . $cssCode1 .';';
		$text .= 'border-style: ' . $result['style'] . ';';
		$text .= 'border-color: ' . $result['color'] . ';';
		$text .= 'border-width: ' . $result['border_width'] . 'px;';
		$text .= 'border-top-width:'. $result['bw_t'] .'px;';
	    $text .= 'border-left-width:'. $result['bw_l'] .'px;';
	    $text .= 'border-right-width:'. $result['bw_r'] .'px;';
	    $text .= 'border-bottom-width:'. $result['bw_b'] .'px;';

		return $text;
	}
}

if( !function_exists( 'generateBoxShadow' )) {
	function generateBoxShadow($string){
		$pairs = explode( '|', $string );
		$result = array();
		foreach( $pairs as $pair ) {
			$pair = explode( ':', $pair );
			$result[$pair[0]] = $pair[1];
		}

		$res = '';
		if ( isset( $result['type'] ) && $result['type'] !== 'outset' )
			$res .= $result['type'] . ' ';

		$res .= $result['horizontal'] . 'px ';
		$res .= $result['vertical'] . 'px ';
		$res .= $result['blur'] . 'px ';
		$res .= $result['spread'] . 'px ';
		$res .= $result['color'];

		$style = 'box-shadow:'.$res.';';
		$style .= '-webkit-box-shadow:'.$res.';';
		$style .= '-moz-box-shadow:'.$res.';';

		if( $result['type'] == 'none' ) {
			$style = '';
		}

		return $style;
	}
}

/**
 *	Filter 'cp_valid_mx_email' for MX - Email validation
 *
 * @since 0.1.0
 */
add_filter( 'cp_valid_mx_email', 'cp_valid_mx_email_init' );

if( !function_exists( "cp_valid_mx_email_init" ) ){
	function cp_valid_mx_email_init($email) {
		if(cp_is_valid_mx_email($email)) {
			return true;
		} else {
			return false;
		}
	}
}

if( !function_exists( "cp_is_valid_mx_email" ) ){
	function cp_is_valid_mx_email($email,$record = 'MX') {
		list($user,$domain) = explode('@',$email);
		return checkdnsrr($domain,$record);
	}
}

/**
 *	= Enqueue Selected - Google Fonts
 *
 * @param string
 * @return string
 * @since 0.1.0
 *-----------------------------------------------------------*/
 if( !function_exists( "cp_enqueue_google_fonts" ) ){
	function cp_enqueue_google_fonts( $fonts = '' ) {

		$pairs = $GFonts = $ar = '';

		$basicFonts = array(
			"Arial",
			"Arial Black",
			"Comic Sans MS",
			"Courier New",
			"Georgia",
			"Impact",
			"Lucida Sans Unicode",
			"Palatino Linotype",
			"Tahoma",
			"Times New Roman",
			"Trebuchet MS",
			"Verdana"
		);

		if (strpos($fonts, ',') !== FALSE)
			$pairs = explode(',', $fonts);

		//	Extract selected - Google Fonts
		if(!empty($pairs)) {
			foreach ($pairs as $key => $value) {
				if( isset($value) && !empty($value) ) {
					if( !in_array( $value, $basicFonts ) ) {
						$GFonts .= str_replace(' ', '+', $value) .'|';
					}
				}
			}
		} else {
			$GFonts = $fonts;
		}

		//	Check the google fonts is enabled from BackEnd.
		$data         = get_option( 'convert_plug_settings' );
		$is_GF_Enable = isset($data['cp-google-fonts']) ? $data['cp-google-fonts'] : 1;

		//	Register & Enqueue selected - Google Fonts
		if( !empty( $GFonts ) && $is_GF_Enable ) {
			wp_register_style('cp-google-fonts' , 'https://fonts.googleapis.com/css?family='.$GFonts, null, null, null);
			wp_enqueue_style('cp-google-fonts' );
		}
	}
}


/**
 *	= Enqueue mobile detection js
 *
 * @param string
 * @return string
 * @since 0.1.0
 *-----------------------------------------------------------*/
 if( !function_exists( "cp_enqueue_detect_device" ) ){
	function cp_enqueue_detect_device( $devices ) {
		 if (wp_script_is( 'cp-detect-device', 'enqueued' )) {
	       return;
	     } else {
			wp_enqueue_script('cp-detect-device' );
		}

	}
}

/**
 *	Add Custom CSS for
 *
 * @since 0.1.5
 */
add_filter( 'cp_custom_css','cp_custom_css_filter', 99, 2);

if( !function_exists( "cp_custom_css_filter" ) ) {
	function cp_custom_css_filter($style_id, $css){
		if( $css !== "" ) {
			echo '<style type="text/css" id="custom-css-'.$style_id.'">'.$css.'</style>';
		}
	}
}

/**
 *	Check values are empty or not
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_is_not_empty" ) ) {
	function cp_is_not_empty($vl) {
		if( isset( $vl ) && $vl != '' ) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 *	Check schedule of modal
 *
 * @since 0.1.5
 */

if( !function_exists( "cp_is_modal_scheduled" ) ) {
	function cp_is_modal_scheduled($schedule, $live) {
		$op = '';
		if( is_array( $schedule ) && $live=='2' ) {
			$op = ' data-scheduled="true" data-start="'.$schedule['start'].'" data-end="'.$schedule['end'].'" ';
		} else {
			$op = ' data-scheduled="false" ';
		}
		return $op;
	}
}

/**
 * Generate CSS from dev input
 *
 * @param string 		- $prop
 * @param alphanumeric	- $val
 * @param string		- $suffix
 * @return string 		- Generate & return CSS (e.g. font-size: 16px;)
 * @since 0.1.5
 */
if( !function_exists( "cp_add_css" ) ) {
	function cp_add_css($prop, $val, $suffix = '') {
		$op = '';
		if( $val != '') {
			if( $suffix != '' ) {
				$op = $prop. ':' .esc_attr( $val ) . $suffix. ';';
			} else {
				$op = $prop. ':' .esc_attr( $val ). ';';
			}
		}
		return $op;
	}
}

/**
 *	Get Modal Image URL
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_modal_image_url_init" ) ) {
	function cp_get_modal_image_url_init( $modal_image = '' ) {
		if (strpos($modal_image,'http') !== false) {
			$modal_image = explode( '|', $modal_image );
			$modal_image = $modal_image[0];
		} else {
			$modal_image = apply_filters('cp_get_wp_image_url', $modal_image );
	   	}
	   	return $modal_image;
	}
}
add_filter( 'cp_get_modal_image_url', 'cp_get_modal_image_url_init' );

/**
 *	Get WordPress attachment url
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_wp_image_url_init" ) ) {
	function cp_get_wp_image_url_init( $wp_image = '') {
		if( cp_is_not_empty($wp_image) ){
			$wp_image = explode("|", $wp_image);
			$wp_image = wp_get_attachment_image_src($wp_image[0],$wp_image[1]);
			$wp_image = $wp_image[0];
		}
		return $wp_image;
	}
}
add_filter( 'cp_get_wp_image_url', 'cp_get_wp_image_url_init' );

/**
 *	Set custom class for modal
 *
 * @since 0.1.5
 */
add_filter( 'cp_get_custom_class', 'cp_get_custom_class_init' );

if( !function_exists( "cp_get_custom_class_init" ) ) {
	function cp_get_custom_class_init( $enable_custom_class = 0, $custom_class, $style_id ) {
		//if( $enable_custom_class == 1 ){
			$custom_class = $custom_class;
		/*} else {
			$custom_class = '';
		}*/
		$custom_class  = str_replace( " ", "", trim( $custom_class ) );
		$custom_class  = str_replace( ",", " ", trim( $custom_class ) );
		$custom_class .= ' cp-'.$style_id;
		$custom_class = trim( $custom_class );
		return $custom_class;
	}
}

/**
 * Check modal has redirection
 *
 * @since 0.1.5
 *
 * @param bullion - $on_success
 * @param string - 	$redirect_url
 * @param string -  $redirect_data
 * @return string - Data Attribute
 */

if( !function_exists( "cp_has_redirect_init" ) ) {
	function cp_has_redirect_init($on_success, $redirect_url, $redirect_data) {
		$op = '';
		if($on_success == 'redirect' && $redirect_url != '' && $redirect_data == 1){
			$op = ' data-redirect-lead-data="'.$redirect_data.'" ';
		}
		return $op;
	}
}
add_filter( 'cp_has_redirect', 'cp_has_redirect_init' );

/**
 * Check modal overlay settings
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_has_overaly_setting_init" ) ) {
	function cp_has_overaly_setting_init( $overlay_effect, $disable_overlay_effect, $hide_animation_width ) {
		$op = ' data-overlay-animation = "'.$overlay_effect.'" ';
		if($disable_overlay_effect == 1){
			$op .= ' data-disable-animationwidth="'.$hide_animation_width.'" ';
		}
		return $op;
	}
}
add_filter( 'cp_has_overaly_setting', 'cp_has_overaly_setting_init' );



/**
 * Set value Enabled or Disabled. - Default 'enabled'
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_has_enabled_or_disabled_init" ) ) {
	function cp_has_enabled_or_disabled_init( $modal_exit_intent ) {
		$op = ( $modal_exit_intent != '' && $modal_exit_intent != '0' ) ? 'enabled' : 'disabled';
		return $op;
	}
}
add_filter( 'cp_has_enabled_or_disabled', 'cp_has_enabled_or_disabled_init' );


/**
 * Visibility on Browser, Devices & OS
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_modal_visibility_on_devices_browser_os_init" ) ) {
	function cp_modal_visibility_on_devices_browser_os_init( $hide_on_device = '', $hide_on_os = '', $hide_on_browser = '' ) {
		$op = '';
		if( $hide_on_device != '' ){
			$op .= ' data-hide-on-devices="'.$hide_on_device.'" ';
		}
		if( $hide_on_os != '' ){
			$op .= ' data-hide-on-os="'.$hide_on_os.'" ';
		}
		if( $hide_on_browser != '' ){
			$op .= ' data-hide-on-browser="'.$hide_on_browser.'" ';
		}
		return $op;
	}
}
add_filter( 'cp_modal_visibility', 'cp_modal_visibility_on_devices_browser_os_init');

/**
 * Affiliate - Link
 *
 * @since 0.1.5
 */

if( !function_exists( "cp_get_affiliate_link_init" ) ) {
	function cp_get_affiliate_link_init( $affiliate_setting, $affiliate_username ) {
		$op = '';
		if($affiliate_setting == 1){
			if($affiliate_username ==''){
				$affiliate_username = 'BrainstormForce';
				$op = "http://themeforest.net/user/brainstormforce/portfolio?ref=BrainstormForce";
			} else {
				$op = "http://themeforest.net/user/brainstormforce/portfolio?ref=".$affiliate_username."";
			}
			return $op;
		}
	}
}
add_filter( 'cp_get_affiliate_link', 'cp_get_affiliate_link_init');

/**
 * Affiliate - Class
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_affiliate_class_init" ) ) {
	function cp_get_affiliate_class_init( $affiliate_setting, $modal_size ) {
		$op = '';
		if($affiliate_setting == 1 &&  $modal_size == "cp-modal-custom-size" ){
			$op .= "cp-affilate";
		}
		return $op;
	}
}
add_filter( 'cp_get_affiliate_class', 'cp_get_affiliate_class_init');

/**
 * Affiliate - Setting
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_affiliate_setting_init" ) ) {
	function cp_get_affiliate_setting_init( $affiliate_setting ) {
		$op =  ($affiliate_setting == 1) ? 'data-affiliate_setting='.$affiliate_setting : 'data-affiliate_setting ="0"' ;
		return $op;
	}
}
add_filter( 'cp_get_affiliate_setting', 'cp_get_affiliate_setting_init');


/**
 * Hide Image - On Mobile
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_hide_image_on_mobile_init" ) ) {
	function cp_hide_image_on_mobile_init($image_displayon_mobile, $image_resp_width){
		$hide_image = '';
		if($image_displayon_mobile==1){
			//$hide_image ='cp-hide-image' ;
			$hide_image =' data-hide-img-on-mobile='.$image_resp_width;
		}
		return $hide_image;
	}
}
add_filter( 'cp_hide_image_on_mobile', 'cp_hide_image_on_mobile_init');


/**
 * Global Settings - Modal
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_modal_global_settings_init" ) ) {
	function cp_modal_global_settings_init( $closed_cookie, $conversion_cookie, $style_id ) {
		$op  = ' data-closed-cookie-time="'.$closed_cookie.'"';
		$op .= ' data-conversion-cookie-time="'.$conversion_cookie.'" ';
		$op .= ' data-modal-id="'.$style_id.'" ';
		$op .= ' data-modal-style="'.$style_id.'" ';
		$op .= ' data-option="smile_modal_styles" ';
		return $op;
	}
}
add_filter( 'cp_modal_global_settings', 'cp_modal_global_settings_init');

/**
 * Modal Before
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_modal_global_before_init" ) ) {
function cp_modal_global_before_init( $a ) {

	$uid 					= uniqid();
	$a['uid'] 				= 'content-'.$uid;

	//	Print CSS of the style
	cp_generate_style_css( $a );

	$a['image_resp_width'] = '768';

	//	Enqueue detect device
	if($a['hide_on_device']){
		cp_enqueue_detect_device( $a['hide_on_device'] );
	}


	//	Enqueue Google Fonts
	cp_enqueue_google_fonts( $a['cp_google_fonts'] );

	$bg_repeat = $bg_pos = $bg_size = $bg_setting = "";
	if( strpos( $a['opt_bg'], "|" ) !== false ){
	    $opt_bg      = explode( "|", $a['opt_bg'] );
	    $bg_repeat   = $opt_bg[0];
	    $bg_pos      = $opt_bg[1];
	    $bg_size     = $opt_bg[2];
	    if($a['modal_bg_image']!==''){
	        $bg_setting .= 'background-repeat: '.$bg_repeat.';';
	        $bg_setting .= 'background-position: '.$bg_pos.';';
	        $bg_setting .= 'background-size: '.$bg_size.';';
		}
	}

	//	Time Zone
	$timezone='';
	$timezone_settings = get_option('convert_plug_settings');
	$timezone_name =$timezone_settings['cp-timezone'];
	if( $timezone_name != '' && $timezone_name!='system' ){
	$timezone = get_option('timezone_string');
		if( $timezone == '' ){
			$toffset = get_option('gmt_offset');
			$timezone = "".$toffset."";
		}
	} else {
		$timezone = get_option('timezone_string');
		if($timezone==''){
			$toffset = get_option('gmt_offset');
			$timezone = "".$toffset."";
		}
	}

	//	Modal - Padding
	$el_class = '';
	if( isset( $a['content_padding'] ) && !empty( $a['content_padding'] ) ) {
		$el_class .= ' no-padding ';
	}

	//	Modal - Background Image & Background Color
	$modal_bg_image = $customcss  = $windowcss = $inset = $css_style = '';
	$modal_bg_color = ( isset( $a['modal_bg_color'] ) ) ? $a['modal_bg_color'] : '';

	if( isset( $a['modal_bg_image'] ) && !empty( $a['modal_bg_image'] ) ) {
		$modal_bg_image = apply_filters('cp_get_wp_image_url', $a['modal_bg_image'] );
	}

	$customcss .= cp_add_css('background-color', $modal_bg_color );
	$windowcss .= 'background-image:url(' . $modal_bg_image . ');' .$bg_setting .';';

	if( $modal_bg_image !== '' ){
		$customcss .= 'background-image:url(' . $modal_bg_image . ');' .$bg_setting .';';
		$windowcss .= 'background-image:url(' . $modal_bg_image . ');' .$bg_setting .';';
	}

	//	Modal - Box Shadow
	$box_shadow_str = generateBoxShadow($a['box_shadow']);
	if ( strpos( $box_shadow_str,'inset' ) !== false ) {
		$inset 	.= $box_shadow_str.';';
		$inset 	.= "opacity:1";
	} else {
		$css_style 	.= $box_shadow_str;
	}

	$close_html = $modal_size_style = $close_class = '';

	//	Check 'has_content_border' is set for that style and add border to modal content (optional)
	//	This option is style dependent - Developer will disable it by adding this variable
	if( !isset( $a['has_content_border'] ) || ( isset( $a['has_content_border'] ) && $a['has_content_border'] ) ) {
		$css_style .= generateBorderCss($a['border']);
	}
	if( $a['modal_size'] == "cp-modal-custom-size" ){
		$modal_size_style  = cp_add_css('width', '100', '%');
		$modal_size_style .= cp_add_css('height', $a['cp_modal_height']);
		$modal_size_style .= cp_add_css('max-width', $a['cp_modal_width'], 'px');
		$windowcss = '';
	} else {
		$customcss = 'max-width: '.$a['cp_modal_width'].'px';
		$windowcss .= $box_shadow_str;
	}

	//	{START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP
	$close_img_class = $close_img = '';
	if( isset($a['close_img'] ) && !empty($a['close_img']) ) {
		if (strpos($a['close_img'],'http') !== false) {
		    $close_img = $a['close_img'];
		    if ( strpos($close_img, '|') !== FALSE ) {
				$close_img = explode( '|', $close_img );
				$close_img = $close_img[0];
			}
		    $close_img_class = 'cp-default-close';
		} else {
			$close_img = apply_filters('cp_get_wp_image_url', $a['close_img'] );
		}
	}

	if( $a['close_modal'] == "close_txt") {
		$close_html = '<span style="color:'.$a['close_text_color'].'">'.$a['close_txt'].'</span>';
	} elseif($a['close_modal'] == "close_img"){
		$close_html = '<img class="'.$close_img_class.'" src="'.$close_img.'" />';
	} else {
		$close_class = ' do_not_close ';
	}
	//	{END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP

	$load_after_scroll = '';
	if( $a['autoload_on_scroll'] ) {
		$load_after_scroll = $a['load_after_scroll'];
	}

	$load_on_duration = '';
	if( $a['autoload_on_duration'] ) {
		$load_on_duration = $a['load_on_duration'];
	}

	$dev_mode = 'disabled';
	if( !$a['developer_mode'] ){
		$a['closed_cookie'] = $a['conversion_cookie'] = 0;
		$dev_mode = 'enabled';
	}

	$close_modal_on = '';
	if( $a['close_modal_on'] )
		$close_modal_on = ' close_btn_nd_overlay';

	$cp_settings = get_option('convert_plug_settings');
	$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '60';
	$inactive_data = '';
	if( $a['inactivity'] ) {
		$inactive_data = 'data-inactive-time="'.$user_inactivity.'"';
	}

	//	Variables
	$global_class 			= 'global_modal_container';
	//	Functions
	$isScheduled 			= cp_is_modal_scheduled($a['schedule'], $a['live']);
	//	Filters & Actions
	$data_redirect = '';
	if( isset($a['on_success']) && isset($a['redirect_url']) && isset($a['redirect_data']) ) {
		$data_redirect	 	= cp_has_redirect_init( $a['on_success'], $a['redirect_url'], $a['redirect_data'] );
	}
	$overlay_effect = '';
	if( isset($a['overlay_effect']) ) {
		$overlay_effect = $a['overlay_effect'];
	}

	$hide_image = '';
	if( isset( $a['image_displayon_mobile'] ) && isset( $a['image_resp_width'] ) ) {
		$hide_image 	 	= cp_hide_image_on_mobile_init( $a['image_displayon_mobile'], $a['image_resp_width'] );
	}

	$overaly_setting 		= cp_has_overaly_setting_init( $overlay_effect , $a['disable_overlay_effect'], $a['hide_animation_width'] );
	$afl_setting 	 		= apply_filters( 'cp_get_affiliate_setting', $a['affiliate_setting'] );
	$style_id 				= ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
	$style_class 			= ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';
	$placeholder_font 		= '';

	//	Filters
	$custom_class 			= cp_get_custom_class_init( $a['enable_custom_class'], $a['custom_class'], $style_id );

	$modal_exit_intent 		= apply_filters( 'cp_has_enabled_or_disabled', $a['modal_exit_intent'] );
	$load_on_refresh 		= apply_filters( 'cp_has_enabled_or_disabled', $a['display_on_first_load'] );
	$global_modal_settings 	= cp_modal_global_settings_init( $a['closed_cookie'], $a['conversion_cookie'], $style_id );
	$cp_modal_visibility	= apply_filters( 'cp_modal_visibility', $a['hide_on_device'], $a['hide_on_os'], $a['hide_on_browser'] ); 		//	Visibility on Browser, Devices & OS

	$placeholder_color 		= ( isset( $a['placeholder_color'] ) ) ? $a['placeholder_color'] : '';
	if ( isset( $a['placeholder_font'] ) ) {
		if( $a['placeholder_font'] == '' )
			$placeholder_font = 'inherit';
		else
			$placeholder_font = $a['placeholder_font'];
	}

	$image_position			= ( isset( $a['image_position'] ) ) ? $a['image_position'] : '';
	$exit_animation			= isset( $a['exit_animation'] ) ? $a['exit_animation'] : 'cp-overlay-none';

	//find out offset
	if( !function_exists( "getOffsetByTimeZone" ) ) {
		function getOffsetByTimeZone($localTimeZone) {
			$time = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone($localTimeZone));
			$timezoneOffset = $time->format('P');
			return $timezoneOffset;
		}
	}


	$schedular_tmz_offset = get_option('gmt_offset');
	if( $schedular_tmz_offset == '' ){
		$schedular_tmz_offset = getOffsetByTimeZone(get_option('timezone_string'));
	}

	//  Container Classes
	$cp_modal_content_class = '';
    if( isset( $a['mailer'] ) && ( $a['mailer'] == "custom-form" ) ) {
		$cp_modal_content_class .= ' cp-custom-form-container';

		//  Add - Contact Form 7 Styles
	    $data         			 = get_option( 'convert_plug_debug' );
	    $is_cf7_styles_enable 	 = ( isset( $data['cp-cf7-styles'] ) ) ? $data['cp-cf7-styles'] : 1;
	    $cp_modal_content_class .= ( $is_cf7_styles_enable ) ? ' cp-default-cf7-style1' : '';
    }

	 $schedular_tmz_offset = get_option('gmt_offset');
	 if( $schedular_tmz_offset == '' ){
	 	$schedular_tmz_offset = getOffsetByTimeZone(get_option('timezone_string'));
	}

	//  Container Classes
	$cp_modal_content_class = '';
    if( isset( $a['mailer'] ) && ( $a['mailer'] == "custom-form" ) ) {
		$cp_modal_content_class .= ' cp-custom-form-container';

		//  Add - Contact Form 7 Styles
	    $data         			 = get_option( 'convert_plug_debug' );
	    $is_cf7_styles_enable 	 = ( isset( $data['cp-cf7-styles'] ) ) ? $data['cp-cf7-styles'] : 1;
	    $cp_modal_content_class .= ( $is_cf7_styles_enable ) ? ' cp-default-cf7-style1' : '';
    }

	 // check if modal should be triggered after post
	 $enable_after_post = (int) ( isset( $a['enable_after_post'] ) ? $a['enable_after_post'] : 0 );
	 if( $enable_after_post ) {
		 $custom_class .= ' cp-after-post';
	 }

	 // check if modal should be triggerd if items in the cart
	 $items_in_cart = (int) ( isset( $a['items_in_cart'] ) ? $a['items_in_cart'] : 0 );
	 if( $items_in_cart ) {
		 $custom_class .= ' cp-items-in-cart';
	 }

	// check if inline display is set
	$isInline = ( isset( $a['display'] ) && $a['display'] == "inline" ) ? true : false;
	if( $isInline ){
		$custom_class .= " cp-open";
		$close_class = "do_not_close";
		$a['modal_overlay_bg_color'] = 'rgba( 255,255,255,0 );';
	}

	$cp_settings = get_option('convert_plug_debug');
	$after_content_scroll = isset( $cp_settings['after_content_scroll'] ) ? $cp_settings['after_content_scroll'] : '50';
	$after_content_data = 'data-after-content-value="'. $after_content_scroll .'"';

	ob_start();

?>
<?php if( !$isInline ){ ?>
<div data-class-id="content-<?php echo $uid; ?>" <?php echo $after_content_data; ?> class="cp-onload overlay-show <?php echo esc_attr( $custom_class ); ?>" data-overlay-class="overlay-zoomin" data-onload-delay="<?php echo esc_attr( $load_on_duration ); ?>" data-onscroll-value="<?php echo esc_attr( $load_after_scroll ); ?>" data-exit-intent="<?php echo esc_attr($modal_exit_intent); ?>" <?php echo $global_modal_settings; ?> data-custom-class="<?php echo esc_attr( $custom_class ); ?>" data-load-on-refresh="<?php echo esc_attr($load_on_refresh); ?>" data-dev-mode="<?php echo esc_attr( $dev_mode ); ?>" <?php echo $inactive_data; ?> <?php echo $cp_modal_visibility; ?>></div>
<?php } ?>
	<div class="cp-modal-popup-container <?php echo esc_attr( $style_id ); ?> <?php echo $style_class. '-container'; ?><?php echo ( $isInline ) ? " cp-inline-modal-container" : ""; ?>">
		<div class="<?php echo ( $isInline ) ? "cp-modal-inline" : "cp-overlay "; ?><?php echo esc_attr( $close_modal_on ); ?> <?php echo esc_attr( $overlay_effect ); ?> content-<?php echo $uid . ' ' . $global_class . ' ' . $close_class ; ?>" data-placeholder-font="<?php echo $placeholder_font; ?>" data-class="content-<?php echo $uid; ?>" style=" <?php echo esc_attr( 'background:'.$a['modal_overlay_bg_color'] ); ?>" <?php echo $global_modal_settings; ?> data-custom-class="<?php echo esc_attr( $custom_class ); ?>" data-load-on-refresh="<?php echo esc_attr($load_on_refresh); ?>" <?php echo $isScheduled; ?> data-timezone="<?php echo esc_attr($timezone); ?>" data-timezonename="<?php echo esc_attr( $timezone_name );?>" data-placeholder-color="<?php echo $placeholder_color; ?>" data-image-position="<?php echo $image_position ;?>" <?php echo $hide_image; ?> <?php echo $afl_setting; ?> <?php echo $overaly_setting;?> <?php echo $data_redirect;?> data-tz-offset="<?php echo $schedular_tmz_offset ;?>">
	    	<div class="cp-modal <?php echo esc_attr( $a['modal_size'] ); ?>" style="<?php echo esc_attr( $modal_size_style ); ?>">
	      		<div class="cp-animate-container" <?php echo $overaly_setting;?> data-exit-animation="<?php echo esc_attr( $exit_animation ); ?>">
	      			<div class="cp-modal-content <?php echo $cp_modal_content_class; ?>" style="<?php echo esc_attr( $css_style ); ?>;<?php echo esc_attr( $windowcss );?>">
					<?php if( $a['modal_size'] != "cp-modal-custom-size" ){ ?>
	      				<div class="cp-modal-body-overlay cp_fs_overlay" style="background-color: <?php echo esc_attr( $a['modal_bg_color'] ); ?>;<?php echo esc_attr( $inset ); ?>;"></div>
	      			<?php } ?>

	        		<div class="cp-modal-body <?php echo $style_class . ' ' . esc_attr( $el_class ); ?>" style="<?php echo esc_attr( $customcss );?>">
	          		 <?php if( $a['modal_size'] == "cp-modal-custom-size" ){ ?>
	      					<div class="cp-modal-body-overlay cp_cs_overlay" style="background-color: <?php echo esc_attr( $a['modal_bg_color'] ); ?>;<?php echo esc_attr( $inset ); ?>;"></div>
	      				<?php } ?>
<?php
 }
}
add_filter( 'cp_modal_global_before', 'cp_modal_global_before_init' );


/**
 * Modal After
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_modal_global_after_init" ) ) {
function cp_modal_global_after_init( $a ) {

	$afilate_link 	= cp_get_affiliate_link_init( $a['affiliate_setting'], $a['affiliate_username'] );
	$afilate_class 	= cp_get_affiliate_class_init( $a['affiliate_setting'], $a['modal_size'] );
	if( $a['close_modal'] !== 'close_txt' )
		$cp_close_image_width = $a['cp_close_image_width']."px";
	else
		$cp_close_image_width = 'auto';

	//	{START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP
	$close_img_class = '';
	if ( strpos($a['close_img'],'http') !== false ) {
	    $close_img = $a['close_img'];
	    if ( strpos($close_img, '|') !== FALSE ) {
				$close_img = explode( '|', $close_img );
				$close_img = $close_img[0];
		}
	    $close_img_class = 'cp-default-close';
	} else {
		$close_img = apply_filters('cp_get_wp_image_url', $a['close_img'] );
	}

	$close_html = $el_class = $modal_size_style = $close_class = '';
	if( $a['content_padding'] ) {
		$el_class .= 'no-padding ';
	}
	$close_tooltip= $close_tooltip_end ='';
	if( $a['close_modal'] == "close_txt" ) {
		$close_class .= 'cp-text-close';
		if( $a['close_modal_tooltip'] == 1 ) {
			$close_tooltip ='<span class=" cp-close-tooltip cp-tooltip-icon has-tip cp-tipcontent-'.$a['style_id'].'data-classes="close-tip-content-'.$a['style_id'].'" data-position="left"  title="'. $a['tooltip_title'].'"  data-color="'.$a['tooltip_title_color'] .'" data-bgcolor="'.$a['tooltip_background'].'" data-closeid ="cp-tipcontent-'.$a['style_id'].'">';
			$close_tooltip_end ='</span>';
		}
		$close_html = '<span style="color:'.$a['close_text_color'].'">'.$a['close_txt'].'</span>';
	} elseif( $a['close_modal'] == "close_img" ){
		$close_class .= 'cp-image-close';
		$close_html = '<img class="'.$close_img_class.'" src="'.$close_img.'" />';
	} else {
		$close_class = 'do_not_close';
	}
	//	{END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP

	/*--tooltip-----*/
		$tooltip_position = '';
		if( $a['modal_size'] == "cp-modal-custom-size" ){
			$tooltip_position = 'top';
		}else{
			$tooltip_position = 'left';
		}

		$tooltip_class=$tooltip_style = '';
		if($a['close_modal_tooltip'] == 1){
			$tooltip_class .= 'cp_closewith_tooltip';
			$tooltip_style .= 'color:'.$a['tooltip_title_color'].';background-color:'.$a['tooltip_background'].';border-top-color: '.$a['tooltip_background'].';';
		}
		$affiliate_fullsize='';
		if( $a['modal_size'] !== "cp-modal-custom-size" )
		{
			$affiliate_fullsize ='cp-affiliate-fullsize';
		}

		/// Generate border radius for form processing
		$pairs = explode( '|', $a['border'] );
		$result = array();
		foreach( $pairs as $pair ){
			$pair = explode( ':', $pair );
			$result[ $pair[0] ] = $pair[1];
		}

		$cssCode1 = '';
		$cssCode1 .= $result['br_tl'] . 'px ' . $result['br_tr'] . 'px ' . $result['br_br'] . 'px ';
		$cssCode1 .= $result['br_bl'] . 'px';
		$result['border_width']=' ';
		$formProcessCss = '';
		$formProcessCss .= 'border-radius: ' . $cssCode1 .';';
		$formProcessCss .= '-moz-border-radius: ' . $cssCode1 .';';
		$formProcessCss .= '-webkit-border-radius: ' . $cssCode1 .';';
		$formProcessCss .= 'box-shadow: 0 0 3px 1px '.$a['modal_overlay_bg_color'].' inset;';

		// check if inline display is set
		$isInline = ( isset( $a['display'] ) && $a['display'] == "inline" ) ? true : false;
		if( $isInline ){
			$a['close_modal'] = "do_not_close";
		}
?>
			</div><!-- .cp-modal-body -->

						</div><!-- .cp-modal-content -->
                        <?php if( !isset( $a['button_conversion'] ) ) { ?>
						<div class="cp-form-processing-wrap" style="<?php echo esc_attr($formProcessCss); ?>;">
							<div class="cp-form-after-submit">
		                		<div class ="cp-form-processing" style="">
		                			<div class="smile-absolute-loader" style="visibility: visible;">
								        <div class="smile-loader">
								            <div class="smile-loading-bar"></div>
								            <div class="smile-loading-bar"></div>
								            <div class="smile-loading-bar"></div>
								            <div class="smile-loading-bar"></div>
								        </div>
								    </div>
		                		</div>
		                		<div class ="cp-msg-on-submit"></div>
		                	</div>
		                </div>
                        <?php } ?>

			    		<?php if( $a['close_modal'] == 'close_img' && $a['close_position'] != 'out_modal' ) { ?>

				      		<?php
				      		if( $a['close_position'] == 'adj_modal' )
				      			$close_overlay_class = 'cp-adjacent-close';
				      		else
				      			$close_overlay_class = 'cp-inside-close';
				      		?>
					      	<div class="cp-overlay-close <?php echo esc_attr( $close_class ).' '.esc_attr( $close_overlay_class ); ?>" style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
								<?php if( $a['close_modal_tooltip'] == 1 ) { ?>
				      			<span class=" cp-tooltip-icon cp-inside-tip has-tip cp-tipcontent-<?php echo $a['style_id']; ?>" data-classes="close-tip-content-<?php echo $a['style_id']; ?>" data-position="<?php echo esc_attr( $tooltip_position );?>"  title="<?php echo esc_attr( $a['tooltip_title'] );?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] );?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] );?>" data-closeid ="cp-tipcontent-<?php echo $a['style_id']; ?>">
				      			<?php } ?>
								<?php echo $close_html; ?>
								<?php if($a['close_modal_tooltip'] == 1){ ?></span><?php } ?>
					      	</div>

					    <?php } ?>
					</div><!-- .cp-animate-container -->
			    </div><!-- .cp-modal -->
				<?php if( $a['affiliate_setting'] == 1  ){ ?>
					        <div class ="cp-affilate-link cp-responsive">
					           <a href="<?php echo $afilate_link ?>" target= "_blank"><?php echo do_shortcode( html_entity_decode( $a['affiliate_title'] ) ); ?></a>
					        </div>
		          	<?php } ?><!-- .affiliate link for fullscreen -->

	   			<?php if( ( $a['close_position'] == 'out_modal' && $a['close_modal'] != 'do_not_close') || $a['close_modal'] == 'close_txt' ) { ?>
					    <div class="cp-overlay-close <?php echo esc_attr( $close_class ); ?>"  style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
							 <?php if( $a['close_modal_tooltip'] == 1 ) { ?>
								<span class=" cp-close-tooltip cp-tooltip-icon has-tip cp-tipcontent-<?php echo $a['style_id']; ?>" data-classes="close-tip-content-<?php echo $a['style_id']; ?>" data-position="left"  title="<?php echo esc_attr( $a['tooltip_title'] );?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] );?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] );?>" data-closeid ="cp-tipcontent-<?php echo $a['style_id']; ?>">
							<?php } ?>
							<?php echo $close_html; ?><?php if($a['close_modal_tooltip'] == 1){ ?></span><?php } ?>
						 </div>
				<?php } ?>



	</div><!-- .cp-overlay -->
</div><!-- .cp-modal-popup-container -->
<?php
 }
}
add_filter( 'cp_modal_global_after', 'cp_modal_global_after_init' );
