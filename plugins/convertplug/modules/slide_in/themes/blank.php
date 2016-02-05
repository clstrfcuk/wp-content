<?php
if( !function_exists( "slide_in_theme_blank" ) ) {
	function slide_in_theme_blank( $atts, $content = null ){
		$style_id = $settings_encoded = $load_on_refresh = '';
		extract(shortcode_atts(array(
			'style_id'			=> '',
			'settings_encoded'		=> '',
	    ), $atts));
		
		$settings = base64_decode( $settings_encoded );
		$style_settings = unserialize( $settings );

		foreach($style_settings as $key => $setting){
			$style_settings[$key] = apply_filters('smile_render_setting',$setting);
		}		
		
		$style = $slidein_id = $option = $new_style = $slidein_size = $overlay_effect = $cp_slidein_width = $cp_slidein_height = $slidein_bg_color = '';
		$slidein_overlay_bg_color = $slidein_title = $slidein_title_color = $slidein_desc_color = $close_slidein = $close_txt = $slidein_content = $loading_delay = '';
		$developer_mode = $conversion_cookie = $closed_cookie =  $display_on_first_load = $show_for_logged_in = $autoload_on_duration = $autoload_on_scroll = '';
		$load_on_duration = $load_after_scroll = $mailer = $custom_class = $live = $global = $border = $box_shadow = $shadow_type = $pages_to_exclude = '';
		$cats_to_exclude = $exclusive_pages = $exclusive_cats = $slidein_exit_intent = $close_slidein = $close_txt = $close_img =$content_padding = '';
		$slidein_title_bg_color = $slidein_image = $slidein_short_desc1 = $slidein_confidential = $tip_color = $form_type = $custom_html_form = $close_text_color = '';
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $schedule = $msg_wrong_email = '';
		$opt_bg = $slidein_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = $image_vertical_position = $image_displayon_mobile = '' ;
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = $exit_animation = '';

		$a = shortcode_atts( array(

			/** = Global Common - Options
			 *-----------------------------------------------------------*/
			'style'						=> '',
			'slidein_id'					=> '',
			'slidein_position'		=> '',
			'option'					=> '',
			'new_style'					=> '',
			'slidein_size'				=> '',
			'cp_slidein_width'			=> '',
			'cp_slidein_height'			=> 'auto',
			'slidein_bg_color'			=> 'rgb(255, 255, 128)',
			'slidein_bg_gradient' 		=> '',
			'slidein_bg_gradient_lighten' => '',
			'developer_mode'			=> '',
			'conversion_cookie'			=> '',
			'closed_cookie'				=> '',
			'show_for_logged_in'		=> '',
			'autoload_on_scroll'		=> '',
			'autoload_on_duration'		=> '',
			'load_on_duration'			=> 1200,
			'load_after_scroll'			=> 70,
			'live'						=> '',
			'global'					=> '',
			'pages_to_exclude' 			=> '',
			'cats_to_exclude' 			=> '',
			'exclusive_pages' 			=> '',
			'exclusive_cats' 			=> '',
			'content_padding'			=> '',
			'schedule'					=> '',
			'cp_google_fonts'			=> '',
			'opt_bg'					=> '',
			'inactivity'				=> '',
			'hide_on_device'			=> '',
			'hide_on_os'				=> '',
			'hide_on_browser'			=> '',
			'all_users'					=> '',			
			'enable_custom_class' 		=> '',
			'custom_class'				=> '',
			'display_on_first_load' 	=> '',
			'slidein_exit_intent'			=> '',
			'slidein_title1'				=> '',
			'slidein_title_color'			=> 'rgb(76, 17, 48)',
			'slidein_desc_color'			=> 'rgb(76, 17, 48)',
			'slidein_content'				=> '',
			'slidein_confidential'		=> '',
			'slidein_short_desc1'			=> '',
			'border'					=> '',
			'box_shadow'				=> '',
			'shadow_type'				=> '',
			'slidein_title_bg_color'		=> '',
			'button_title'				=> '',
			'button_bg_color'			=> '',
			'button_txt_color'			=> '',
			'border_size'				=> '',
			'slidein_bg_image'			=> '',
			'cp_close_image_width'      => '',

			// close slidein
			'close_text_color' 			=> '',			
			'close_position'            => '',
			'close_bg_color' 			=> '',
			'close_img'					=> '',
			'close_slidein'				=> '',
			'close_txt'					=> 'Close',
			
			//	Affiliate
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',

			//	Overlay animation 
			'overlay_effect'			=> 'slidein-overlay-zoomin',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'exit_animation'            => '',

			// tooltip 
			'close_slidein_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',
			'custom_css'				=> '',

			'display'					=> '',

			/** = Style dependent - Options
			 *-----------------------------------------------------------*/	

			'style_class' 				=> 'cp-blank',
			'tip_color'					=> '',
			'slidein_sec_title'           => '',
			'slidein_sec_title_color'     => '',

			// Form             
            'form_type'					=> '',
            'custom_html_form'			=> '',
            'mailer'					=> '',
            'namefield'					=> '',
            'input_bg_color'			=> '',
            'placeholder_color'			=> '',
            'placeholder_text'			=> '',
            'input_border_color'		=> '',
            'placeholder_font'          => '',
            'name_text'					=> '',
            'button_border_color'		=> '',
            'btn_disp_next_line'        => '',

            //	button - submit
			'btn_border_radius'			=> '',
			'button_txt_hover_color' 	=> '',
			'button_bg_gradient_color'	=> '',
			'button_bg_hover_color'		=> '',
			'btn_shadow_color'			=> '',
			'btn_shadow'				=> '',
			'btn_style'					=> '',

            // submission
            "mailer"				=> '',
            "inactivity_link"			=> '',
            "success_message"			=> '',
            "msg_wrong_email"			=> '',

            // Redirection
            'on_success'				=> '',
            'redirect_url'				=> '',
            'redirect_data'				=> '',			

			// slidein image  		
			'slidein_image'				=> '',	
			'image_displayon_mobile'	=> '',
			// 'image_resp_width'			=> '',
			'image_position'			=> '',			
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',	

			//Slide In button
			'toggle_btn'					=>'',
			'side_btn_gradient'				=>'',
			'button_animation'				=>'',
			'toggle_button_font'			=>'',
			'slide_button_title'			=>'',
			'slidein_btn_position'			=>'',
			'side_btn_style'				=>'',
			'side_button_bg_color'			=>'',
			'side_button_txt_hover_color'	=>'',
			'side_button_bg_hover_color' 	=>'',
			'side_button_bg_gradient_color'	=>'',
			'side_btn_border_radius'		=>'',
			'side_btn_shadow'				=>'',
			'slide_button_text_color'       =>'',

			//	Triggers
			'enable_after_post'				=> '',

		), $style_settings );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_slidein_global_before', array( $a ) );

		
?>
		<!-- BEFORE CONTENTS -->
        <div class="cp-row">
        	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
				<?php 
                /*$content = html_entity_decode( $a['slidein_title1'] );
               	$content = htmlspecialchars_decode( $content );
               	$content = htmlspecialchars($content);
               	$content = html_entity_decode( $content );
               	echo do_shortcode( stripslashes( $content ) );*/

               	echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_title1'] ) ) ); 

                ?>
			</div>
		</div>
		<!-- AFTER CONTENTS -->
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_slidein_global_after', array( $a ) );

	   	return ob_get_clean();
	}
}
