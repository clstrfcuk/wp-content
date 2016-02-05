<?php
if( !function_exists( "modal_theme_counter" ) ) {
	function modal_theme_counter( $atts, $content = null ){
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

		$style = $modal_id = $option = $new_style = $modal_size = $overlay_effect = $cp_modal_width = $cp_modal_height = $modal_bg_color = '';
		$modal_overlay_bg_color = $modal_title = $modal_title_color = $modal_desc_color = $close_modal = $close_txt = $modal_content = $loading_delay = '';
		$developer_mode = $conversion_cookie = $closed_cookie =  $display_on_first_load = $show_for_logged_in = $autoload_on_duration = $autoload_on_scroll = '';
		$load_on_duration = $load_after_scroll = $mailer = $custom_class = $live = $global = $border = $box_shadow = $shadow_type = $pages_to_exclude = '';
		$cats_to_exclude = $exclusive_pages = $exclusive_cats = $modal_exit_intent = $close_modal = $close_txt = $close_img =$content_padding = '';
		$modal_title_bg_color = $modal_image = $modal_short_desc1 = $modal_confidential = $tip_color = $form_type = $custom_html_form = $close_text_color = '';
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $schedule = $msg_wrong_email = '';
		$opt_bg = $modal_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = $image_vertical_position = $image_displayon_mobile = '' ;
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = $close_modal_on = '';

		$a = shortcode_atts( array(

			/** = Global Common - Options
			 *-----------------------------------------------------------*/
			'style'						=> '',
			'modal_id'					=> '',
			'option'					=> '',
			'new_style'					=> '',
			'modal_size'				=> '',
			'cp_modal_width'			=> '',
			'cp_modal_height'			=> 'auto',
			'modal_bg_color'			=> 'rgb(255, 255, 128)',
			'modal_overlay_bg_color'	=> 'rgba(68, 68, 68, 0.7)',
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
			'modal_exit_intent'			=> '',
			'modal_title1'				=> '',
			'modal_title_color'			=> 'rgb(76, 17, 48)',
			'modal_desc_color'			=> 'rgb(76, 17, 48)',
			'modal_content'				=> '',
			'modal_confidential'		=> '',
			'modal_short_desc1'			=> '',
			'border'					=> '',
			'box_shadow'				=> '',
			'shadow_type'				=> '',
			'modal_title_bg_color'		=> '',
			'button_title'				=> '',
			'button_bg_color'			=> '',
			'button_txt_color'			=> '',
			'border_size'				=> '',
			'modal_bg_image'			=> '',
			'cp_close_image_width'      => '',

			// close modal
			'close_text_color' 			=> '',
			'close_position'            => '',
			'close_bg_color' 			=> '',
			'close_modal_on'            => '',
			'close_img'					=> '',
			'close_modal'				=> '',
			'close_txt'					=> 'Close',

			//	Affiliate
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',

			//	Overlay animation
			'overlay_effect'			=> 'cp-overlay-zoomin',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'exit_animation'			=> '',

			// tooltip
			'close_modal_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',

			/** = Style dependent - Options
			 *-----------------------------------------------------------*/

			'style_class' 				=> 'cp-counter',
			'tip_color'					=> '',
			'modal_sec_title'           => '',
			'modal_sec_title_color'     => '',

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

			// modal image
			'modal_image'				=> '',
			'image_displayon_mobile'	=> '',
			// 'image_resp_width'			=> '',
			'image_position'			=> '',
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',

			//counter
			'datepicker_advance_option' => '',
			'disable_datepicker' 		=> '',
			'date_time_picker'			=> '',
			'counter_font'				=> '',
			'counter_bg_color'			=> '',
			'counter_digit_text_color'	=> '',
			'counter_digit_text_size'	=> '',
			'counter_timer_text_color'	=> '',
			'counter_timer_text_size'	=> '',
			'counter_digit_border_color' => '',
			'counter_option'			=> '',
			'custom_css'				=> '',

		), $style_settings );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );

				
		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_modal_global_before', array( $a ) );

?>
		<!-- BEFORE CONTENTS -->
        <div class="cp-row ">
        	<div class="cp_title cp_responsive">
				<?php
                $content = html_entity_decode( $a['modal_title1'] );
               	$content = htmlspecialchars_decode( $content );
               	$content = htmlspecialchars($content);
               	$content = html_entity_decode( $content );
               	echo do_shortcode( stripslashes( $content ) );
                ?>
			</div>
			<?php if( $a['disable_datepicker']){ ?>
			 <div id="cp_defaultCountdown" data-timeformat ='<?php echo esc_attr( $a['counter_option'] ); ?>' data-date="<?php echo esc_attr( $a['date_time_picker'] );?>"></div>
			<?php } ?>
		</div>
		<!-- AFTER CONTENTS -->
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_after', array( $a ) );

	   	return ob_get_clean();
	}
}
