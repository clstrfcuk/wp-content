<?php
if( !function_exists( "info_bar_theme_blank" ) ) {
	function info_bar_theme_blank( $atts, $content = null ){
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

		$a =  shortcode_atts( array(

			/* Design */
			'style_id'					=> '',
			'infobar_position'			=> '',
			'bg_color'					=> '',
			'infobar_height'			=> '',
			'infobar_width'             => '',
			'info_bar_content'			=> '',
			'display_close'				=> '',
			'display'					=> '',
			'page_down'					=> '',
			'animate_push_page'         => '',
			'fix_position'				=> '',
			'button_bg_color'			=> '',
			'button_border_color'		=> '',
			'placeholder_text'			=> '',
			'message_color'				=> '',

			/* Close Button */
			'close_info_bar'			=> '',
			'close_txt'					=> '',
			'close_text_color'			=> '',
			'close_img'					=> '',
			'close_img_width'			=> '',
			'close_info_bar_pos'        => '',

			/* Animation */
			'entry_animation'			=> '',
			'exit_animation'			=> '',
			'button_animation'			=> '',

			'infobar_title'				=> '',

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
			'info_bar_title_bg_color'	=> '',
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
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'input_border_color' 		=> '',
			'namefiled'					=> '',
			'name_text'					=> '',
			'close_position'            => '',
			'button_border_color'		=> '',
			'image_position'			=> '',
			'close_bg_color' 			=> '',
			'close_info_bar_on'         => '',
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',
			'image_displayon_mobile'	=> '',
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',
			'image_resp_width'			=> '',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'enable_custom_class' 		=> '',
			'redirect_data'				=> '',
			'style_class' 				=> 'cp-blank-info-bar',

			//	Shadow
			'enable_shadow'				=> '',

			//	Border
			'enable_border'				=> '',
			'border_darken'				=> '',

			//	Custom CSS
			'custom_css'				=> '',

			//	Background
			'infobar_bg_image'			=> '',
			'bg_gradient'				=> '',
			'bg_gradient_lighten'		=> '',

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

		),$style_settings );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge( $a, $atts );

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_before', array( $a ) );
		?>

        <div class="cp-content-container">
                <?php
                $content = html_entity_decode( $a['infobar_title'] );
                $content = htmlspecialchars_decode( $content );
                $content = htmlspecialchars($content);
                $content = html_entity_decode( $content );
                echo do_shortcode( stripslashes( $content ) );
                ?>
        </div>
       <?php

       	/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_after', array( $a ) );

		return ob_get_clean();
	}
}
