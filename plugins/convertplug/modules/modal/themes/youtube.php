<?php
if( !function_exists( "modal_theme_YouTube" ) ) {

	add_filter('cp_youtube_css', 'cp_youtube_css_init');
	function cp_youtube_css_init( $a ) {
		$output  = '<style type="text/css">';
		$output .= '</style>';
		echo $output;
	}

	/**
	 * Return YouTube video embed link
	 *
	 * @since 0.1.6
	 */
	function cp_get_youtube_video_url( $video_id, $video_start, $player_controls, $player_actions) {
		$video_url = 'https://www.youtube.com/embed/' . $video_id . '?wmode=opaque&player=html5&rel=0&autoplay=1&fs=0';

		if( $video_start ){
			$video_url .= '&start=' . $video_start;
		} else {
			$video_url .= '&start=0';
		}

		if( $player_controls == '1' || $player_controls == 1 ){
			$video_url .= '&controls=1';
		} else {
			$video_url .= '&controls=0';
		}

		if( $player_actions == '1' || $player_actions == 1 ){
			$video_url .= '&showinfo=1';
		} else {
			$video_url .= '&showinfo=0';
		}

		return $video_url;
	}

	function modal_theme_YouTube( $atts, $content = null ){
		$style_id = $settings_encoded = $load_on_refresh = '';
		extract( shortcode_atts(array(
			'style_id'			=> '',
			'settings_encoded'	=> '',
	    ), $atts) );
		
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
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $mailer = $schedule = $msg_wrong_email = '';
		$opt_bg = $modal_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = $image_vertical_position = $image_displayon_mobile = '' ;
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = $close_modal_on = '';

		$a = shortcode_atts( array(

			/** = Global Common - Options
			 *-----------------------------------------------------------*/
			'border' 					=> '',
			'box_shadow'				=> '',
			'display'					=> '',
			'image_displayon_mobile'	=> '',
			'image_resp_width'			=> '',
			'image_position'			=> '',
			'style'						=> '',
			'modal_id'					=> '',
			'option'					=> '',
			'new_style'					=> '',
			'modal_size'				=> '',
			'cp_modal_width'			=> '',
			'modal_bg_color'			=> '',
			'cta_bg_color'				=> '',
			'cp_modal_height'			=> 'auto',
			'modal_overlay_bg_color'	=> 'rgba(68, 68, 68, 0.7)',
			'tip_color'					=> '',
			'close_modal'				=> '',
			'close_txt'					=> 'Close',
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
			'close_txt'					=> '',
			'close_img'					=> '',
			'content_padding'			=> '',
			'schedule'					=> '',
			'msg_wrong_email'			=> '',
			'cp_google_fonts'			=> '',
			'opt_bg'					=> '',
			'inactivity'				=> '',
			'hide_on_device'			=> '',
			'hide_on_os'				=> '',
			'hide_on_browser'			=> '',
			'all_users'					=> '',
			'close_text_color' 			=> '',
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'input_border_color' 		=> '',
			'namefield'					=> '',
			'name_text'					=> '',
			'close_position'            => '',
			'close_bg_color' 			=> '',
			'close_modal_on'            => '',
			'enable_custom_class' 		=> '',
			'custom_class'				=> '',
			'display_on_first_load' 	=> '',
			'modal_exit_intent'			=> '',
			'modal_bg_image'			=> '',
			'cp_close_image_width'      => '',

			//	Affiliate
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',

			//	Overlay Settings
			'overlay_effect'			=> 'cp-overlay-zoomin',
			'exit_animation'			=> '',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'close_modal_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',

			/** = Form - Options
			 *-----------------------------------------------------------*/
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

			'success_message'			=> '',
			//	Redirection
			'on_success'				=> '',
			'redirect_url'				=> '',
			'redirect_data'				=> '',

			//	Submit Button
			'button_bg_hover_color'		=> '',
			'button_title'				=> '',
			'button_bg_color'			=> '',
			'btn_shadow_color'			=> '',
			'button_bg_gradient_color'	=> '',
			'button_txt_hover_color'	=> '',
			'btn_style'					=> '',
			'btn_shadow'				=> '',
			'btn_border_radius'			=> '',
			'btn_shadow'				=> '',

			/** = Style - Options
			 *-----------------------------------------------------------*/
			//	Required
			'style_class' 				=> 'cp-youtube',

			// Optional
			'cp_modal_height'			=> '',
			'video_id'					=> '',
			'video_start'				=> '',
			'player_controls'			=> '',
			'player_actions'			=> '',
			'player_autoplay'			=> '',
			'youtube_submit'			=> '',

			//	CTA Type
			'cta_type'					=> '',
			'cta_delay'					=> '',
			'enable_after_post'			=> '',
			'items_in_cart'				=> '',
			'custom_css'				=> '',

		), $style_settings );
	
		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_youtube_css', array( $a ) );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );

		/** = Style - individual options
		 *-----------------------------------------------------------*/
		//	Filters & Actions
		$modal_size_style = $iframe_wrap = '';
		
		$v_height = $a['cp_modal_width'];
		$v_height *= 1;
		$valueHeight = ( ( $v_height / 16 ) * 9 );

		// Youtube Video
		$video_url = cp_get_youtube_video_url( $a['video_id'], $a['video_start'], $a['player_controls'], $a['player_actions'] );
		if( $a['modal_size'] == "cp-modal-custom-size" ){
			$modal_size_style .= 'max-width:'.$a['cp_modal_width'].'px; width: 100%; height:'.$valueHeight.'px;';
			$windowcss='';
		} else {
			$customcss='';
		}
		//	CTA Type
		$cta_type_class = '';
		switch( $a['cta_type'] ) {
			case 'none': 		$cta_type_class = 'cp-youtube-cta-none';
				break;

			case 'button': 		$cta_type_class = 'cp-youtube-cta-button';

				break;

			case 'form': 		$cta_type_class = 'cp-youtube-cta-form';
				break;

		}

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_modal_global_before', array( $a ) );
?>
		<!-- BEFORE CONTENTS -->
		<div class="cp-row">
		<?php if( $a['modal_size'] == "cp-modal-window-size" ){ ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-no-margin-padding" style="float: none; height: 100vh; margin: 0px auto; padding: 0px;">
				<iframe class="cp-youtube-frame" style="margin: 0;" width="100%" height="100%" src="<?php echo $video_url; ?>" data-autoplay="<?php echo esc_attr( $a['player_autoplay'] ); ?>" frameborder="0" allowfullscreen=""></iframe>
			</div>
  		<?php } else { ?>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float: none;margin: 0 auto; padding: 0px;<?php echo esc_attr( $modal_size_style ); ?>">
				<iframe class="cp-youtube-frame" style="margin:0;<?php echo esc_attr( $modal_size_style ); ?>" src="<?php echo $video_url; ?>" data-autoplay="<?php echo esc_attr( $a['player_autoplay'] ); ?>" frameborder="0" allowfullscreen></iframe>
			</div>
		<?php } ?>
		</div><!-- .row-youtube-iframe -->


		<div class="cp-row cp-form-container <?php echo esc_attr( $cta_type_class ); ?>" data-cta-delay="<?php echo esc_attr( $a['cta_delay'] ); ?>" style="<?php echo 'background: ' . $a['cta_bg_color']; ?>;" >

				<!-- CTA - Form -->
				<?php if( $cta_type_class == 'cp-youtube-cta-form' ) {

					//	Check form type
					switch ( $a['mailer'] ) {
						case 'custom-form': 	echo do_shortcode( stripcslashes( $a['custom_html_form'] ) );
							break;
						
						default:   ?>
								<div class="form-main <?php echo esc_attr( $formfiled );?> ">
									<?php

									//	form class (default)
									$form__class = 'cp-form-with-name';
									$email_input = '';
									$submit_input = '';
									//	Has name? 		Show - name, email, submit
									if( $a['namefield'] ) {

										$form__class = 'cp-form-with-name';
										$submit = $email = $name = 'col-md-4 col-lg-4 col-sm-4 col-xs-12 no-margin';
										$submit_input = $email_input = $name_input = 'no-margin';

									} else {
										$form__class = 'cp-form-without-name';

										//	Email, Submit - (submit on next line)
										if( $a['btn_disp_next_line'] == 1 ) {
											$email 	= 'col-md-12 col-lg-12 col-sm-12 col-xs-12';
											$submit = 'col-md-12 col-lg-12 col-sm-12 col-xs-12';
										} else {
											
											// 	Email, Submit
											$submit	= 'col-md-4 col-lg-4 col-sm-4 col-xs-12 no-margin';
											$email	= 'col-md-8 col-lg-8 col-sm-8 col-xs-12 no-margin';
											$submit_input = $email_input = 'no-margin';
										}
									}

									?>
									<form id="smile-optin-form" class="cp-form <?php echo $form__class ; ?>">
						                <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?>

			                			<?php if( $a['namefield'] ) { ?>
											<div class="<?php echo $name; ?> cp-form-name">
						                		<input class="<?php echo $name_input; ?> cp-input cp-name" type="text" name="name" placeholder="<?php echo $a['name_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
						            		</div>
										<?php } ?>

				    	        		<div class="<?php echo $email; ?> cp-form-email form-content">
			            	    			<input class="<?php echo $email_input; ?> cp-email " type="email" name="email" placeholder="<?php echo $a['placeholder_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
			            				</div>
										<div class="<?php echo $submit; ?> cp-form-submit">
											<div class="<?php echo $submit_input; ?> cp-submit btn-modal btn btn-subscribe cp_responsive <?php echo esc_attr( $a['btn_style'] ); ?>">
										 			<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['youtube_submit'] ) ) ); ?>
											</div>
										</div>
			            			</form><!-- #smile-optin-form -->
								</div><!-- .form-main -->

				            <?php
						break;
					}

			    } ?>

				<!-- CTA - Button -->
				<?php if( $cta_type_class == 'cp-youtube-cta-button' ) { ?>
		            <div class="default-form">
		            	<div class="col-md-12 col-xs-12 col-lg-12 cp-submit-container">
		            		<button class="cp-submit cp_responsive btn-modal btn <?php echo esc_attr( $a['btn_style'] ); ?>">
		            	 		<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['youtube_submit'] ) ) ); ?>
		            		</button>
		            	</div>
		            </div>
				<?php } ?>

		</div><!-- .cp-form-container -->

		<!-- AFTER CONTENTS -->
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_after', array( $a ) );

	   return ob_get_clean();
	}
}