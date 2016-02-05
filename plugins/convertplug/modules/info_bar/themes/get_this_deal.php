<?php
if( !function_exists( "info_bar_theme_get_this_deal" ) ) {
	function info_bar_theme_get_this_deal( $atts, $content = null ){
		$style_id = $settings_encoded = $load_on_refresh = '';
		extract(shortcode_atts(array(
			'style_id'			=> '',
			'settings_encoded'		=> '',
	    ), $atts));

		$settings = base64_decode( $settings_encoded );
		$style_settings = unserialize( $settings );

		foreach($style_settings as $key => $setting){
			$style_settings[$key] = apply_filters('smile_render_setting',$setting);;
		}

		$infobar_position = $bg_color = $infobar_height = $infobar_title = $button_title = $display_close = $page_down = $fix_position = '';
		$close_info_bar = $close_txt = $info_bar_content = $loading_delay = '';
		$developer_mode = $conversion_cookie = $closed_cookie =  $display_on_first_load = $show_for_logged_in = $autoload_on_duration = $autoload_on_scroll = '';
		$load_on_duration = $load_after_scroll = $mailer = $custom_class = $live = $global = $border = $box_shadow = $shadow_type = $pages_to_exclude = '';
		$cats_to_exclude = $exclusive_pages = $exclusive_cats = $ib_exit_intent = $close_info_bar = $close_txt = $close_img =$content_padding = '';
		$info_bar_title_bg_color = $info_bar_image = $info_bar_short_desc1 = $info_bar_confidential = $tip_color = $form_type = $custom_html_form = $close_text_color = '';
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $schedule = $msg_wrong_email = '';
		$opt_bg = $info_bar_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = '';
		$image_vertical_position = $image_displayon_mobile = $enable_custom_class = '' ;
		$entry_animation = $exit_animation = $button_animation = '';
		$button_bg_color = $button_border_color = $placeholder_text = $name_text = $namefield = $placeholder_color = $input_bg_color = $input_border_color = '';
		$close_info_bar = $close_txt = $close_text_color = $close_img = $close_img_width = $new_line_optin = $message_color = '';

		$a =  shortcode_atts( array(

			/* Design */
			'style_id'					=> '',
			'infobar_position'			=> '',
			'bg_color'					=> '',
			'infobar_height'			=> '',
			'infobar_width'             => '',
			'infobar_title'				=> '',
			'button_title'				=> '',
			'display_close'				=> '',
			'display'					=> '',
			'page_down'					=> '',
			'animate_push_page'         => '',
			'fix_position'				=> '',
			'button_border_color'		=> '',

			'name_text'					=> '',
			'namefield'					=> '',
			'placeholder_color'			=> '',
			'input_bg_color'			=> '',
			'input_border_color'		=> '',
			'new_line_optin'			=> '',
			'message_color'				=> '',


			/* Close Button */
			'close_info_bar'			=> '',
			'close_txt'					=> '',
			'close_text_color'			=> '',
			'close_img'					=> '',
			'close_img_width'			=> '',
			'close_position'            => '',
			'close_bg_color' 			=> '',
			'close_info_bar_on'         => '',
			'close_info_bar_pos'        => '',

			/* Animation */
			'entry_animation'			=> '',
			'exit_animation'			=> '',
			'button_animation'			=> '',

			'style'						=> '',
			'option'					=> '',
			'new_style'					=> '',
			'close_info_bar'				=> '',
			'close_txt'					=> 'Close',
			'developer_mode'			=> '',
			'conversion_cookie'			=> '',
			'closed_cookie'				=> '',
			'display_on_first_load'	=> '',
			'show_for_logged_in'		=> '',
			'autoload_on_scroll'		=> '',
			'autoload_on_duration'		=> '',
			'load_on_duration'			=> 1200,
			'load_after_scroll'			=> 70,
			'custom_class'				=> '',
			'live'						=> '',
			'global'					=> '',
			'border'					=> '',
			'box_shadow'				=> '',
			'shadow_type'				=> '',
			'pages_to_exclude' 			=> '',
			'cats_to_exclude' 			=> '',
			'exclusive_pages' 			=> '',
			'exclusive_cats' 			=> '',
			'ib_exit_intent'			=> '',
			'close_txt'					=> '',
			'close_img'					=> '',
			'content_padding'			=> '',
			'info_bar_title_bg_color'		=> '',
			'mailer'					=> '',
			'on_success'				=> '',
			'redirect_url'				=> '',
			'success_message'			=> '',
			'button_title'				=> '',
			'button_bg_color'			=> '',
			'button_txt_color'			=> '',
			'form_type'					=> '',
			'custom_html_form'			=> '',
			'border_size'				=> '',
			'schedule'					=> '',
			'msg_wrong_email'			=> '',
			'cp_google_fonts'			=> '',
			'opt_bg'					=> '',
			'info_bar_bg_image'			=> '',
			'inactivity'				=> '',
			'hide_on_device'			=> '',
			'hide_on_os'				=> '',
			'hide_on_browser'			=> '',
			'all_users'					=> '',

			//placeholder and input text box
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'input_border_color' 		=> '',
			'namefiled'					=> '',
			'name_text'					=> '',

			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',

			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'enable_custom_class' 		=> '',
			'redirect_data'				=> '',
			'infobar_description'		=> '',
			'seperator_border_color' 	=> '',

			//info bar image
			'infobar_image'				=> '',
			'image_resp_width'			=> '',
			'image_position'			=> '',
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',
			'image_displayon_mobile'	=> '',

			//	Shadow
			'enable_shadow'				=> '',

			//	Border
			'enable_border'				=> '',
			'border_darken'				=> '',

			// custom css
			'custom_css'				=> '',

			// background
			'bg_color'					=> '',
			'infobar_bg_image'			=> '',
			'infobar_bg_image_size'		=> '',
			'opt_bg'					=> '',
			'bg_gradient'				=> '',
			'bg_color'					=> '',
			'bg_gradient'				=> '',
			'bg_gradient_lighten'		=> '',

			//button
			'button_bg_color'			=> '',
			'btn_border'				=> '',
			'btn_border_radius'			=> '',
			'btn_border_color'			=> '',
			'btn_style'				 	=> '',
			'btn_shadow'				=> '',
			'button_txt_hover_color'	=> '',
			'button_bg_hover_color'		=> '',
			'button_bg_gradient_color'	=> '',
			'btn_darken'				=> '',
			'btn_gradiant'				=> '',

			'style_class' 				=> 'cp-get-this-deal',
			
			// Google Fonts
			'cp_google_fonts'			=> '',
			'placeholder_font'			=> '',
			'enable_after_post'			=> '',

			//toggle button
			'toggle_btn'				=> '',
			'toggle_button_title'		=> '',
			'toggle_button_font'		=> '',
			'toggle_button_text_color'	=> '',
			'toggle_btn_gradient'		=> '',
			'toggle_button_bg_color'	=> '',
			'toggle_button_bg_hover_color'	=> '',
			'toggle_button_bg_gradient_color' => '',
			'button_conversion'			=> true,


		),$style_settings );

		//modal image
		$infobar_image 		= apply_filters( 'cp_get_modal_image_url', $a['infobar_image'] );

		$imageStyle		 	= cp_add_css( 'left', $a['image_horizontal_position'], 'px');
		$imageStyle		   .= cp_add_css( 'top', $a['image_vertical_position'], 'px');
		$imageStyle		   .= cp_add_css( 'max-width', $a['image_size'], 'px');
		$imageStyle		   .= cp_add_css( 'width', $a['image_size'], 'px');

		$img_class = '';
		if($a['image_displayon_mobile']){
			$img_class .= 'cp_ifb_hide_img';
		}

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge( $a, $atts );

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_before', array( $a ) );
		?>
        <div class="cp-msg-container <?php echo ( trim( $a['infobar_title'] ) == "" ? "cp-empty" : '' );  ?> ">
            <span class="cp-info-bar-msg cp_responsive"><?php echo do_shortcode( stripslashes( html_entity_decode( $a['infobar_title'] ) ) ); ?></span>
        </div>
		<div class="cp-button-field ib-form-container">
			<?php if( $a['button_conversion'] ) { ?>
	            <form id="smile-ib-optin-form" class="cp-ib-form">
	               <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?> 
	            </form>
	        <?php }?>
            <button class="cp-button ib-subscribe cp-submit smile-animated <?php echo esc_attr( $a['btn_style'] );?>" data-animation="<?php echo esc_attr( $a['button_animation'] ); ?>" style=""><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?></button>
        </div>
<?php
       	/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_after', array( $a ) );
		return ob_get_clean();
	}
}
