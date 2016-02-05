<?php
/*
* Global functions for Slide In
*/
if( !function_exists( "smile_get_live_slide_ins" )){
	function smile_get_live_slide_ins(){
		$styles = get_option( 'smile_slide_in_styles' );
		$slide_in_variant_tests = get_option( 'slide_in_variant_tests' );
		$live_array = array();
		if( !empty( $styles ) ) {
			foreach( $styles as $key => $style ){
				$settings = unserialize( $style[ 'style_settings' ] );

				$split_tests = isset( $slide_in_variant_tests[$style['style_id']] ) ? $slide_in_variant_tests[$style['style_id']] : '';
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

if( !function_exists( "slidein_generate_style_css" ) ) {
	function slidein_generate_style_css( $a ) {

		$style_class = ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';

		/** = Submit Button - CSS
		 *-----------------------------------------------------------*/
		$shadow = $style = '';

		$c_hover  	= esc_attr( $a['button_bg_hover_color'] );
		$c_normal 	= esc_attr( $a['button_bg_color'] );
		$light 		= esc_attr( $a["button_bg_gradient_color"] );
		$h_color 	= esc_attr( $a["button_txt_hover_color"] );

		$class = $a['btn_style'];
		//	Apply box shadow to submit button - If its set & equals to - 1
		if( $a["btn_shadow"] == 1 ) {
			$shadow .= "box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);";
		}

		$radius = '';
		if( $a["btn_border_radius"] != '' ) {
			$radius = 'border-radius:' . esc_attr( $a["btn_border_radius"] ) . 'px;';
		}

		//echo $class;

		switch( $class ) {

			case 'cp-btn-flat': 	//	Normal
									$style .= ".cp-slidein .cp-submit." 	. $class . " { "
											. " 	background: " 		. $c_normal . "!important;"
											. 		$radius . $shadow
											. "}"

									//	Hover
											. ".cp-slidein .cp-submit." 	. $class . ":hover { "
											. " 	background: " 		. $c_hover	. "!important;"
											. "}";
				break;
			case 'cp-btn-3d': 		//	Normal
									$style  .= ".cp-slidein .cp-submit." 	. $class . " { "
											 . " 	background: " 		. $c_normal . " !important;"
											 . " 	box-shadow: 0 6px " . $c_hover . ";"
											 . " 	position: relative;"
											 . 		$radius
											 . "}"

									//	Hover
											. ".cp-slidein .cp-submit." 	. $class . ":hover { "
											. " 	box-shadow: 0 4px " . $c_hover . ";"
											. " 	top: 2px;"
											. "}"

									//	Active
											. ".cp-slidein .cp-submit." 	. $class . ":active { "
											. " 	top: 6px;"
											. "		box-shadow: 0 0px " . $c_hover . ";"
											. "}";
				break;
			case 'cp-btn-outline': 	//	Normal
									$style .= ".cp-slidein .cp-submit." . $class . " { "
											. " 	background: transparent;"
											. " 	border: 2px solid " . $c_normal . ";"
											. " 	color: inherit;"
											.		$shadow . $radius
											. "}"

									//	Hover
											. ".cp-slidein .cp-submit." 	. $class . ":hover { "
											. " 	background: " 		. $c_hover	. ";"
											. " 	border: 2px solid " . $c_hover . ";"
											. " 	color: ". $h_color
											. "}"

									//	Inner span color inherit to apply hover color
											. ".cp-slidein .cp-submit." 	. $class . ":hover span { color: inherit !important ; } ";
				break;
			case 'cp-btn-gradient': 	//	Normal
										$style  .= ".cp-slidein .cp-submit." . $class . " { "
												. 		$shadow . $radius
												. " 	border: none;"
												. "		background: -webkit-linear-gradient(" . $light . ", " . $c_normal . ");"
												. "		background: -o-linear-gradient(" . $light . ", " . $c_normal . ");"
												. "		background: -moz-linear-gradient(" . $light . ", " . $c_normal . ");"
												. "		background: linear-gradient(" . $light . ", " . $c_normal . ");"
												. "}"

										//	Hover
												. ".cp-slidein .cp-submit." 	. $class . ":hover { "
												. " 	background: " 		. $c_normal	. ";"
												. "}";
				break;
		}

		$style .= $a['custom_css'];
		/* CP - SlideIn Styling */
		echo '<style type="text/css" id="">'.$style.'</style>';

		//apply_filters( 'custom_css' , $style );
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

		//	Register & Enqueue selected - Google Fonts
		if( !empty( $GFonts ) ) {
			wp_register_style('cp-google-fonts' , 'https://fonts.googleapis.com/css?family='.$GFonts);
			wp_enqueue_style('cp-google-fonts' );
		}
	}
}

/**
 *	Add Custom CSS for
 *
 * @since 0.1.5
 */
add_filter( 'cp_custom_css','cp_custom_css_filter',99,2);
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
 *	Check schedule of slidein
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_is_slidein_scheduled" ) ){
	function cp_is_slidein_scheduled($schedule, $live) {
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
if( !function_exists( "cp_add_css" ) ){
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
 *	Get SlideIn Image URL
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_slidein_image_url_init" ) ){
	function cp_get_slidein_image_url_init( $slidein_image = '' ) {
		if (strpos($slidein_image,'http') !== false) {
			$slidein_image = explode( '|', $slidein_image );
			$slidein_image = $slidein_image[0];
		} else {
			$slidein_image = apply_filters('cp_get_wp_image_url', $slidein_image );
		}
		return $slidein_image;
	}
}
add_filter( 'cp_get_slidein_image_url', 'cp_get_slidein_image_url_init' );

/**
 *	Get WordPress attachment url
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_wp_image_url_init" ) ){
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
 *	Set custom class for slidein
 *
 * @since 0.1.5
 */
add_filter( 'cp_get_custom_class', 'cp_get_custom_class_init' );
if( !function_exists( "cp_get_custom_class_init" ) ) {
	function cp_get_custom_class_init( $enable_custom_class = 0, $custom_class, $style_id ) {
		/*if( $enable_custom_class == 1 ){
			$custom_class = $custom_class;
		} else {
			$custom_class = '';
		}*/

		$custom_class = $custom_class;
		$custom_class  = str_replace( " ", "", trim( $custom_class ) );
		$custom_class  = str_replace( ",", " ", trim( $custom_class ) );
		$custom_class .= ' cp-'.$style_id;
		$custom_class = trim( $custom_class );
		return $custom_class;
	}
}

/**
 * Check slidein has redirection
 *
 * @since 0.1.5
 *
 * @param bullion - $on_success
 * @param string - 	$redirect_url
 * @param string -  $redirect_data
 * @return string - Data Attribute
 */
if( !function_exists( "cp_has_redirect_init" ) ){
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
 * Check slidein overlay settings
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_has_overaly_setting_init" ) ){
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
if( !function_exists( "cp_has_enabled_or_disabled_init" ) ){
	function cp_has_enabled_or_disabled_init( $slidein_exit_intent ) {
		$op = ( $slidein_exit_intent != '' && $slidein_exit_intent != '0' ) ? 'enabled' : 'disabled';
		return $op;
	}
}
add_filter( 'cp_has_enabled_or_disabled', 'cp_has_enabled_or_disabled_init' );


/**
 * Visibility on Browser, Devices & OS
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_slidein_visibility_on_devices_browser_os_init" ) ) {
	function cp_slidein_visibility_on_devices_browser_os_init( $hide_on_device = '', $hide_on_os = '', $hide_on_browser = '' ) {
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
add_filter( 'cp_slidein_visibility', 'cp_slidein_visibility_on_devices_browser_os_init');

/**
 * Affiliate - Link
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_get_affiliate_link_init" ) ){
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
if( !function_exists( "cp_get_affiliate_class_init" ) ){
	function cp_get_affiliate_class_init( $affiliate_setting, $slidein_size ) {
		$op = '';
		if($affiliate_setting == 1 &&  $slidein_size == "cp-slidein-custom-size" ){
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
if( !function_exists( "cp_get_affiliate_setting_init" ) ){
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
if( !function_exists( "cp_hide_image_on_mobile_init" ) ){
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
 * Global Settings - SlideIn
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_slidein_global_settings_init" ) ){
	function cp_slidein_global_settings_init( $closed_cookie, $conversion_cookie, $style_id ) {
		$op  = ' data-closed-cookie-time="'.$closed_cookie.'"';
		$op .= ' data-conversion-cookie-time="'.$conversion_cookie.'" ';
		$op .= ' data-slidein-id="'.$style_id.'" ';
		$op .= ' data-slidein-style="'.$style_id.'" ';
		$op .= ' data-option="smile_slide_in_styles" ';
		return $op;
	}
}
add_filter( 'cp_slidein_global_settings', 'cp_slidein_global_settings_init');

/**
 * SlideIn Before
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_slidein_global_before_init" ) ){
	function cp_slidein_global_before_init( $a ) {

		//	Print CSS of the style
		slidein_generate_style_css( $a );

		$a['image_resp_width'] = '768';

		//	Enqueue detect device
		if( $a['hide_on_device'] ) {

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
			if( $a['slidein_bg_image'] !== '' ){
				$bg_setting .= 'background-repeat: '.$bg_repeat.';';
				$bg_setting .= 'background-position: '.$bg_pos.';';
				$bg_setting .= 'background-size: '.$bg_size.';';
			}
		}

		//	Time Zone
		$timezone = '';
		$timezoneformat = 'none';
		$timezone_settings = get_option('convert_plug_settings');
		$timezone_name = $timezone_settings['cp-timezone'];
		if( $timezone_name != '' && $timezone_name != 'system' ){
		$timezone = get_option('timezone_string');
			if( $timezone == '' ){
				$toffset = get_option('gmt_offset');
				$timezone = "".$toffset."";
				$timezoneformat = 'offset';
			}
		} else {
			$timezone = get_option('timezone_string');
			if( $timezone == '' ){
				$toffset = get_option('gmt_offset');
				$timezone = "".$toffset."";
				$timezoneformat = 'offset';
			}
		}

		//	SlideIn - Padding
		$el_class = '';
		if( isset( $a['content_padding'] ) && !empty( $a['content_padding'] ) ) {
			$el_class .= ' no-padding ';
		}

		//	SlideIn - Background Image & Background Color
		$slidein_bg_image = $customcss  = $windowcss = $inset = $css_style = '';
		$slidein_bg_color = ( isset( $a['slidein_bg_color'] ) ) ? $a['slidein_bg_color'] : '';

		if( isset( $a['slidein_bg_image'] ) && !empty( $a['slidein_bg_image'] ) ) {
			$slidein_bg_image = apply_filters('cp_get_wp_image_url', $a['slidein_bg_image'] );
		}

			//	Variables
		$uid 					= uniqid();

		//$customcss .= cp_add_css('background-color', $slidein_bg_color );

		/**
		 * 	Background - (Background Color / Gradient)
		 *
		 */
		$slide_bg_style = '';
		if( $a['slidein_bg_gradient'] != '' && $a['slidein_bg_gradient'] == '1' ) {

			$slide_bg_style .= '.slidein-overlay.content-'.$uid.' .cp-slidein-body-overlay {
						     background: -webkit-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $a['slidein_bg_color'] . ');
						     background: -o-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $a['slidein_bg_color'] . ');
						     background: -moz-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $a['slidein_bg_color'] . ');
						     background: linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $a['slidein_bg_color'] . ');
						}';
		} else {

			$slide_bg_style .= '.slidein-overlay.content-'.$uid.' .cp-slidein-body-overlay {
							background: ' . $a['slidein_bg_color'] . ';
						}';
		}

		echo '<style class="cp-slidebg-color" type="text/css">'.$slide_bg_style.'</style>';


		$windowcss .= 'background-image:url(' . $slidein_bg_image . ');' .$bg_setting .';';

		if( $slidein_bg_image !== '' ){
			$customcss .= 'background-image:url(' . $slidein_bg_image . ');' .$bg_setting .';';
			$windowcss .= 'background-image:url(' . $slidein_bg_image . ');' .$bg_setting .';';
		}

		//	SlideIn - Box Shadow
		$box_shadow_str = generateBoxShadow($a['box_shadow']);
		if ( strpos( $box_shadow_str,'inset' ) !== false ) {
			$inset 	.= $box_shadow_str.';';
			$inset 	.= "opacity:1";
		} else {
			$css_style 	.= $box_shadow_str;
		}

		$close_html = $slidein_size_style = $close_class = '';

		//	Check 'has_content_border' is set for that style and add border to slidein content (optional)
		//	This option is style dependent - Developer will disable it by adding this variable
		if( !isset( $a['has_content_border'] ) || ( isset( $a['has_content_border'] ) && $a['has_content_border'] ) ) {
			$css_style .= generateBorderCss($a['border']);
		}

		$slidein_size_style .= cp_add_css('height', $a['cp_slidein_height']);
		$slidein_size_style .= cp_add_css('max-width', $a['cp_slidein_width'], 'px');
		//$slidein_size_style .= cp_add_css('width', '100', '%');
		$windowcss = '';

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

		if( isset($a['close_slidein']) && $a['close_slidein'] == "close_txt" ) {
			$close_html = '<span style="color:'.$a['close_text_color'].'">'.$a['close_txt'].'</span>';
		} elseif( isset($a['close_slidein']) && $a['close_slidein'] == "close_img" ){
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

		$cp_settings = get_option('convert_plug_settings');
		$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '3000';
		$inactive_data = '';
		if( $a['inactivity'] ) {
			$inactive_data = 'data-inactive-time="'.$user_inactivity.'"';
		}

		//	Variables
		//$uid 					= uniqid();
		$global_class 			= 'global_slidein_container';
		//	Functions
		$isScheduled 			= cp_is_slidein_scheduled($a['schedule'], $a['live']);
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
		//$afl_setting 	 		= apply_filters( 'cp_get_affiliate_setting', $a['affiliate_setting'] );
		$style_id 				= ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$style_class 			= ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';
		$placeholder_font 		= '';

		//	Filters
		$custom_class 			 = cp_get_custom_class_init( $a['enable_custom_class'], $a['custom_class'], $style_id );

		$slidein_exit_intent 	 = apply_filters( 'cp_has_enabled_or_disabled', $a['slidein_exit_intent'] );
		$load_on_refresh 		 = apply_filters( 'cp_has_enabled_or_disabled', $a['display_on_first_load'] );
		$global_slidein_settings = cp_slidein_global_settings_init( $a['closed_cookie'], $a['conversion_cookie'], $style_id );
		$cp_slidein_visibility 	 = apply_filters( 'cp_slidein_visibility', $a['hide_on_device'] ); 		//	Visibility on Browser, Devices & OS

		$placeholder_color 		 = ( isset( $a['placeholder_color'] ) ) ? $a['placeholder_color'] : '';
		if ( isset( $a['placeholder_font'] ) ) {
			if( $a['placeholder_font'] == '' )
				$placeholder_font = 'inherit';
			else
				$placeholder_font = $a['placeholder_font'];
		}

		$image_position			= ( isset( $a['image_position'] ) ) ? $a['image_position'] : '';

		$exit_animation			= isset( $a['exit_animation'] ) ? $a['exit_animation'] : 'slidein-overlay-none';

		//Slide In button css
			//	Apply box shadow to submit button - If its set & equals to - 1
			$slideshadow = $slideradius = '';
			if( isset($a['side_btn_shadow']) && $a['side_btn_shadow'] != '' ) {
				$slideshadow .= 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
			}
			//	Add - border-radius
			if( isset( $a['side_btn_border_radius'] ) && $a['side_btn_border_radius'] != '' ) {
				$slideradius .= 'border-radius: ' . $a['side_btn_border_radius'] .'px;';
			}
			//side_btn_gradient
			$side_btn_style = $slidelight = $slidebutton_class = '';

			if( isset( $a['side_button_bg_color'] ) && $a['side_button_bg_color'] !== '' ) {
				$slidec_normal 	= $a['side_button_bg_color'];
				$slidec_hover  	= $a['side_button_bg_hover_color'];
				$slidelight 	= $a['side_button_bg_gradient_color'];
				$slidetext_color = $a['slide_button_text_color'];

				$a['side_btn_style'] = '';
				if( $a['side_btn_gradient'] == '1'){
					$a['side_btn_style'] = 'cp-btn-gradient';
				}else{
					$a['side_btn_style'] = 'cp-btn-flat';
				}


				switch( $a['side_btn_style'] ) {
				case 'cp-btn-flat':
						$side_btn_style	    .= '.slidein-overlay.content-'.$uid.' .' . $a['side_btn_style'] . '.cp-slide-edit-btn{ background: '.$slidec_normal.'!important;' .$slideshadow .';'. $slideradius . '; color:'.$slidetext_color.'; } '
												.'.slidein-overlay.content-'.$uid.'  .'.$a['side_btn_style'] . '.cp-slide-edit-btn:hover { background: '.$slidec_hover.'!important; } ';
					break;

				case 'cp-btn-gradient': 	//	Apply box $shadow to submit button - If its set & equals to - 1
						$side_btn_style  .= '.slidein-overlay.content-'.$uid.' .'. $a['side_btn_style'] . '.cp-slide-edit-btn {'
													. '     border: none ;'
													. 		$slideshadow . $slideradius
													. '     background: -webkit-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
													. '     background: -o-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
													. '     background: -moz-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
													. '     background: linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
													. '     color:'.$slidetext_color.'; }'
													. '.slidein-overlay.content-'.$uid.' .' . $side_btn_style . 'cp-slide-edit-btn:hover {'
													. '     background: ' . $slidec_normal . ' !important;'
													. '}';
					break;
			}

		}

		//	Append - Slide In - Toggle CSS
		$font = 'sans-serif';
		if( $a['toggle_button_font'] ) {
			$font = $a['toggle_button_font'] . ',' . $font;
		}
		$side_btn_style .=  '.cp-slide-edit-btn {
								font-family: ' . $font . ';
							}';

		echo '<style class="cp-slidebtn-submit" type="text/css">'.$side_btn_style.'</style>';

		//toggle button setting
		$toggleclass = $slide_toggle_class = '';
		if( $a['toggle_btn'] == 1 && $a['close_slidein'] != 'do_not_close' ){
			$toggleclass = '';
		}else{
			$slide_toggle_class = 'cp-slide-without-toggle';
		}

		// check if inline display is set
		$isInline = ( isset( $a['display'] ) && $a['display'] == "inline" ) ? true : false;
		if( $isInline ){
			$global_class .= ' cp-slidein-inline';
			//$global_class .= ' si-open';
		}

		// check if modal should be triggered after post
		$enable_after_post = (int) ( isset( $a['enable_after_post'] ) ? $a['enable_after_post'] : 0 );
		if( $enable_after_post ) {
			$custom_class .= ' si-after-post';
		}

		$cp_settings = get_option('convert_plug_debug');
		$after_content_scroll = isset( $cp_settings['after_content_scroll'] ) ? $cp_settings['after_content_scroll'] : '50';
		$after_content_data = 'data-after-content-value="'. $after_content_scroll .'"';

		ob_start();
		if( !$isInline ){
	?>
<div data-class-id="content-<?php echo $uid; ?>" <?php echo $after_content_data; ?> class="si-onload overlay-show <?php echo esc_attr( $custom_class ); ?>" data-overlay-class="overlay-zoomin" data-onload-delay="<?php echo esc_attr( $load_on_duration ); ?>" data-onscroll-value="<?php echo esc_attr( $load_after_scroll ); ?>" data-exit-intent="<?php echo esc_attr($slidein_exit_intent); ?>" <?php echo $global_slidein_settings; ?> data-custom-class="<?php echo esc_attr( $custom_class ); ?>" data-load-on-refresh="<?php echo esc_attr($load_on_refresh); ?>" data-dev-mode="<?php echo esc_attr( $dev_mode ); ?>" <?php echo $inactive_data; ?> <?php echo $cp_slidein_visibility; ?>></div>
<?php } ?>
		<div class="cp-slidein-popup-container <?php echo esc_attr( $style_id ); ?> <?php echo $style_class. '-container'; ?>">
			<div class="slidein-overlay <?php echo esc_attr( $slide_toggle_class ); echo ' content-'.$uid;?> <?php echo ' ' . $global_class . ' ' . $close_class ; ?>" data-placeholder-font="<?php echo $placeholder_font; ?>" data-class="content-<?php echo $uid; ?>" <?php echo $global_slidein_settings; ?> data-custom-class="<?php echo esc_attr( $custom_class ); ?>" data-load-on-refresh="<?php echo esc_attr($load_on_refresh); ?>" <?php echo $isScheduled; ?> data-timezone="<?php echo esc_attr($timezone); ?>" data-timezonename="<?php echo esc_attr( $timezone_name );?>" data-timezoneformat="<?php echo esc_attr($timezoneformat);?>" data-placeholder-color="<?php echo $placeholder_color; ?>" data-image-position="<?php echo $image_position ;?>" <?php echo $hide_image; ?>  <?php echo $overaly_setting;?> <?php echo $data_redirect;?>>
				<div class="cp-slidein <?php echo esc_attr( $a['slidein_size'] ) . ' slidein-' . esc_attr( $a['slidein_position'] ); ?>" style="<?php echo esc_attr( $slidein_size_style ); ?>">
					<div class="cp-animate-container <?php echo esc_attr( $toggleclass );?>" <?php echo $overaly_setting;?> data-exit-animation="<?php echo esc_attr( $exit_animation ); ?>">
						<div class="cp-slidein-content" id="slide-in-animate" style="<?php echo esc_attr( $css_style ); ?>;<?php echo esc_attr( $windowcss );?>">
							<div class="cp-slidein-body <?php echo $style_class . ' ' . esc_attr( $el_class ); ?>" style="<?php echo esc_attr( $customcss );?>">
							  <div class="cp-slidein-body-overlay cp_cs_overlay" style="<?php echo esc_attr( $inset ) ?>;"></div>
	<?php
	}
}
add_filter( 'cp_slidein_global_before', 'cp_slidein_global_before_init' );


/**
 * SlideIn After
 *
 * @since 0.1.5
 */
if( !function_exists( "cp_slidein_global_after_init" ) ){
	function cp_slidein_global_after_init( $a ) {

		if( isset( $a['close_slidein'] ) && $a['close_slidein'] !== 'close_txt' )
			$cp_close_image_width = $a['cp_close_image_width']."px";
		else
			$cp_close_image_width = 'auto';

		//	{START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP
		$close_img_class = '';
		if (  isset( $a['close_slidein'] ) && strpos( $a['close_img'], 'http' ) !== false ) {
			$close_img = $a['close_img'];
			if ( strpos($close_img, '|') !== FALSE ) {
					$close_img = explode( '|', $close_img );
					$close_img = $close_img[0];
			}
			$close_img_class = 'cp-default-close';
		} else if( isset( $a['close_slidein'] ) ) {
			$close_img = apply_filters('cp_get_wp_image_url', $a['close_img'] );
		}

		$close_html = $el_class = $slidein_size_style = $close_class = '';
		if( $a['content_padding'] ) {
			$el_class .= 'no-padding ';
		}
		$close_tooltip= $close_tooltip_end ='';
		if( isset( $a['close_slidein'] ) && $a['close_slidein'] == "close_txt" ) {
			$close_class .= 'cp-text-close';
			if( $a['close_slidein_tooltip'] == 1 ) {
				$close_tooltip ='<span class=" cp-close-tooltip cp-tooltip-icon has-tip cp-tipcontent-'.$a['style_id'].'data-classes="close-tip-content-'.$a['style_id'].'" data-position="left"  title="'. $a['tooltip_title'].'"  data-color="'.$a['tooltip_title_color'] .'" data-bgcolor="'.$a['tooltip_background'].'" data-closeid ="cp-tipcontent-'.$a['style_id'].'">';
				$close_tooltip_end ='</span>';
			}
			$close_html = '<span style="color:'.$a['close_text_color'].'">'.$a['close_txt'].'</span>';
		} else if( isset( $a['close_slidein'] ) && $a['close_slidein'] == "close_img" ){
			$close_class .= 'cp-image-close';
			$close_html = '<img class="'.$close_img_class.'" src="'.$close_img.'" />';
		} else {
			$close_class = 'do_not_close';
		}
		//	{END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP

		/*-- tool tip -----*/
		$tooltip_position = 'top';

		$tooltip_class = $tooltip_style = '';
		if( isset( $a['close_slidein_tooltip'] ) && $a['close_slidein_tooltip'] == 1 ) {
			$tooltip_class .= 'cp_closewith_tooltip';
			$tooltip_style .= 'color:'.$a['tooltip_title_color'].';background-color:'.$a['tooltip_background'].';border-top-color: '.$a['tooltip_background'].';';
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
		$result['border_width'] = ' ';
		$formProcessCss = '';
		$formProcessCss .= 'border-radius: ' . $cssCode1 .';';
		$formProcessCss .= '-moz-border-radius: ' . $cssCode1 .';';
		$formProcessCss .= '-webkit-border-radius: ' . $cssCode1 .';';

		// check if inline display is set
		$isInline = ( isset( $a['display'] ) && $a['display'] == "inline" ) ? true : false;
		?>
							</div><!-- .cp-slidein-body -->
							</div><!-- .cp-slidein-content -->
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

							<?php
							$close_overlay_class = 'cp-inside-close';
							if( !$isInline ){
							?>
							<div class="slidein-overlay-close <?php echo esc_attr( $close_class ).' '.esc_attr( $close_overlay_class ); ?>" style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
								<?php if( isset( $a['close_slidein_tooltip'] ) &&  $a['close_slidein_tooltip'] == 1 ) { ?>
								<span class=" cp-tooltip-icon cp-inside-tip has-tip cp-tipcontent-<?php echo $a['style_id']; ?>" data-classes="close-tip-content-<?php echo $a['style_id']; ?>" data-position="<?php echo esc_attr( $tooltip_position );?>"  title="<?php echo esc_attr( $a['tooltip_title'] );?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] );?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] );?>" data-closeid ="cp-tipcontent-<?php echo $a['style_id']; ?>">
								<?php } ?>
								<?php echo $close_html; ?>
								<?php if( isset( $a['close_slidein_tooltip'] ) && $a['close_slidein_tooltip'] == 1 ){ ?></span><?php } ?>
							</div>
							<?php } ?>
							</div><!-- .cp-animate-container -->
					</div><!-- .cp-slidein -->


					<?php if( $a['toggle_btn'] == 1 ) {
						if( $a['side_btn_gradient'] == '1'){
							$slidebutton_class = 'cp-btn-gradient';
						}else{
							$slidebutton_class = 'cp-btn-flat';
						}

						$slide_btn_animation = '';

						if( $a['slidein_position'] == 'center-right' ){
						   $slide_btn_animation = 'smile-slideInUp';
						}else if(  $a['slidein_position'] == 'center-left' ){
							$slide_btn_animation = 'smile-slideInDown';
						}else if(  $a['slidein_position'] == 'top-left' ||  $a['slidein_position'] == 'top-center' ||  $a['slidein_position'] == 'top-right' ){
							$slide_btn_animation = 'smile-slideInDown';
						}else if(  $a['slidein_position'] == 'bottom-left' ||  $a['slidein_position'] == 'bottom-center' ||  $a['slidein_position'] == 'bottom-right' ){
							$slide_btn_animation = 'smile-slideInUp';
						}

						$a['side_btn_style'] = '';
						if( $a['side_btn_gradient'] == '1'){
							$a['side_btn_style'] = 'cp-btn-gradient';
						}else{
							$a['side_btn_style'] = 'cp-btn-flat';
						}

						?>
						<div class="cp-toggle-container <?php echo esc_attr( $slidebutton_class ); ?> slidein-<?php echo esc_attr( $a['slidein_position'] ); ?> cp-slide-hide-btn">
							<div class="<?php echo esc_attr( $a['side_btn_style'] ) ?> cp-slide-edit-btn smile-animated  <?php echo esc_attr( $slide_btn_animation ); ?> ;" ><?php echo  html_entity_decode( $a['slide_button_title'] ) ; ?></div>
						</div>
					<?php  } ?>

		</div><!-- .slidein-overlay -->
	</div><!-- .cp-slidein-popup-container -->
	<?php
	}
}
add_filter( 'cp_slidein_global_after', 'cp_slidein_global_after_init' );