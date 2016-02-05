<?php

if( !function_exists( "info_bar_theme_weekly_article" ) ) {
	function info_bar_theme_weekly_article( $atts, $content = null ){
		$style_id = $settings_encoded = $load_on_refresh = '';
		extract(shortcode_atts(array(
			'style_id'			=> '',
			'settings_encoded'		=> '',
	    ), $atts));

		$settings = base64_decode( $settings_encoded );
		$style_settings = unserialize( $settings );

		$data_option = 'smile_info_bar_styles';

		foreach($style_settings as $key => $setting){
			$style_settings[$key] = apply_filters('smile_render_setting',$setting);;
		}

		if( is_array( $style_settings ) && !empty( $style_settings ) ) {
			$style = $style_settings['style'];
		}

		$info_bar_style = 'info_bar_'.$style;

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
		/*$animate_entry =*/ $entry_animation = /*$animate_exit =*/ $exit_animation = /*$animate_button =*/ $button_animation = '';
		$button_bg_color = $button_border_color = $placeholder_text = $name_text = $namefield = $placeholder_color = $input_bg_color = $input_border_color = '';
		$close_info_bar = $close_txt = $close_text_color = $close_img = $close_img_width = $new_line_optin = $message_color = '';

		$a = shortcode_atts( array(
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
			'fix_position'				=> '',			
			'message_color'				=> '',

			/* Animation */
			// 'animate_entry'			=> '',
			'entry_animation'			=> '',
			// 'animate_exit'			=> '',
			'exit_animation'			=> '',
			// 'animate_button'			=> '',
			'button_animation'			=> '',
			'animate_push_page'			=> '',

			'style'						=> '',
			'option'					=> '',
			'new_style'					=> '',
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

			/*input*/
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'input_border_color' 		=> '',
			'namefiled'					=> '',
			'name_text'					=> '',
			'placeholder_text'			=> '',
			'namefield'					=> '',
			'new_line_optin'			=> '',

			/*close button*/
			'close_position'            => '',
			'image_position'			=> '',
			'close_bg_color' 			=> '',
			'close_info_bar_on'         => '',
			'close_info_bar_pos'		=> '',
			'close_info_bar'			=> '',
			'close_txt'					=> '',
			'close_text_color'			=> '',
			'close_img'					=> '',
			'close_img_width'			=> '',

			/*affiliate*/
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',

			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'enable_custom_class' 		=> '',
			'redirect_data'				=> '',
			'style_class' 				=> 'cp-weekly-article',
			"infobar_description"		=> '',
				
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

			//	infobar image
			'infobar_image'				=> '',				
			'image_resp_width'			=> '',
			'image_position'			=> '',			
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',
			'image_displayon_mobile'	=> '',


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
			'button_border_color'		=> '',
			
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

		$el_class = $info_bar_size_style = $close_class = '';

		/**
		 * 	Move Form to Next Line
		 * 
		 */
		$cp_info_bar_body_class = '';
		if( $new_line_optin != '' && $new_line_optin != '0' ) {
			$cp_info_bar_body_class = 'cp-flex-column';
		}

		if( $a['on_success'] == "redirect" ){
			$on_success_action = $a['redirect_url'];
		} elseif( $a['on_success'] == "message" ) {
			$on_success_action = $a['success_message'] ;
		} else {
			$on_success_action = "Close";
		}

		$input_css = "color:".$a['placeholder_color'].";background:".$a['input_bg_color'].";border-color:".$a['input_border_color'].";";
		$button_css = "background:".$a['button_bg_color'].";";

		//modal image
		$infobar_image 		= apply_filters( 'cp_get_modal_image_url', $a['infobar_image'] );

		$imageStyle		 	= cp_add_css( 'left', $a['image_horizontal_position'], 'px');
		$imageStyle		   .= cp_add_css( 'top', $a['image_vertical_position'], 'px');
		$imageStyle		   .= cp_add_css( 'max-width', $a['image_size'], 'px');
		$imageStyle		   .= cp_add_css( 'width', $a['image_size'], 'px');

		$img_class ='';
		if($a['image_displayon_mobile']){
			$img_class .= 'cp_ifb_hide_img';
		}

			//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge( $a, $atts );


		ob_start();
		?>
       
			<div class="cp-image-container"> 
					<img style="<?php echo esc_attr($imageStyle); ?>" src="<?php echo esc_attr( $infobar_image ); ?>" class="cp-image <?php echo esc_attr( $img_class );?>"> 
	        </div>        
	        <div class="cp-msg-container <?php echo ( trim( $a['infobar_title'] ) == "" ? "cp-empty" : '' );  ?>">
	                <span class="cp-info-bar-msg"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['infobar_title'] ) ) ); ?></span>
	        </div>
	        <div class="cp-flex cp-sub-container"> 
	            <?php if( $a['mailer'] == "custom-form" ) { ?>
			        <div class="custom-html-form">
					  	<?php echo do_shortcode( stripslashes( $a['custom_html_form'] ) ); ?>
					</div>
			  	<?php } else { ?>
					<div class="cp-flex ib-form-container">
		            	<form id="smile-ib-optin-form" class="cp-flex cp-ib-form">
			                <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?>
		                    <?php if( (int) $a['namefield'] ) { ?>
		                    <div class="cp-name-field">
		                        <input type="text" name="name" class="cp-name" placeholder="<?php echo esc_attr( $a['name_text'] ); ?>" style="<?php echo esc_attr( $input_css ); ?>" />
		                    </div>
		                    <?php } ?>
		                    <div class="cp-email-field">
		                        <input type="text" name="email" class="cp-email" required="true" placeholder="<?php echo esc_attr( $a['placeholder_text'] ); ?>" style="<?php echo esc_attr( $input_css ); ?>" />
		                    </div>
		                </form>
			            <div class="cp-button-field">
			                <button class="cp-button ib-subscribe cp-submit <?php echo $a['btn_style'];?> smile-animated" data-animation="<?php echo esc_attr( $a['button_animation'] ); ?>" style="<?php echo esc_attr( $button_css ); ?>"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?></button>
			            </div>
					</div>
					<?php } ?>
					<div class="cp-flex cp-info-bar-desc-container <?php echo ( trim( $a['infobar_description'] ) == "" ? "cp-empty" : '' );  ?>">
					    <div class="cp-info-bar-desc"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['infobar_description'] ) ) ); ?></div>
					</div>
			</div>
	       
<?php 

	/** = After filter
	  *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_after', array( $a ) );	

		return ob_get_clean();
	}
}