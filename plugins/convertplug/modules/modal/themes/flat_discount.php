<?php
if( !function_exists( "modal_theme_flat_discount" ) ) {
	function modal_theme_flat_discount( $atts, $content = null ){
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
		
		if( is_array( $style_settings ) && !empty( $style_settings ) ) {
			$style = $style_settings['style'];
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
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = '';

		$a = shortcode_atts( array(

			/** = Global Common - Options
			 *-----------------------------------------------------------*/
			'style'						=> '',
			'modal_id'					=> '',
			'display'					=> '',
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
			'exit_animation'			=> '',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'exit_animation'			=> '',

			// tooltip 
			'close_modal_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',

			/** = Style dependent - Options
			 *-------------------------------------------------------------*/	

			'style_class' 				=> 'cp-flat-discount',
			'tip_color'					=> '',
			'has_content_border'        => false, 

			// Form 			
			'form_type'					=> '',
			'custom_html_form'			=> '',
			'mailer'					=> '',
			'namefield'					=> '',
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'placeholder_font'          => '',
			'input_border_color' 		=> '',
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
			"mailer" 				=> '',
			"inactivity_link" 			=> '',
			"success_message" 			=> '',
			"msg_wrong_email" 			=> '',

			//	Redirection
			'on_success'				=> '',
			'redirect_url'				=> '',
			'redirect_data'				=> '',
			'enable_after_post'			=> '',
			'items_in_cart'				=> '',
			'custom_css'				=> '',

		), $style_settings );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );

        /** = Style - individual options
		 *-----------------------------------------------------------*/
		//	Variables
		$formfiled 			= ( $a['namefield'] ) ? 'cp-formwith-name' : 'cp-form-simple';
		$on_success_action 	= ($a['on_success'] == "redirect") ? $a['redirect_url'] : $a['success_message'] ;
		$input_style		= cp_add_css( 'background-color', $a['input_bg_color'] );
		$input_style	   .= cp_add_css( 'color', $a['placeholder_color'] );
		$input_style	   .= cp_add_css( 'border-color', $a['input_border_color'] );
		
		//	Functions
		$afilate_class 	 		= cp_get_affiliate_class_init( $a['affiliate_setting'], $a['modal_size'] );
		$afilate_link 			= cp_get_affiliate_link_init( $a['affiliate_setting'], $a['affiliate_username'] );
		$body_inner_style       = generateBorderCss($a['border']);

		//for form text align center
		$form_style='';
		if($a['btn_disp_next_line'] || $a['namefield']){
			
			$form_style = 'cp-center-align-text';
		}

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_before', array( $a ) );

?> 
	<!-- BEFORE CONTENTS -->
	       	
	<div class="cp-row">
		<div class="cp-modal-body-inner" style="<?php echo esc_attr( $body_inner_style ); ?>">
	  		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-text-container">
	       		<div class="cp-title-container <?php if( trim( $a['modal_title1'] ) == '' ) { echo 'cp-empty'; } ?>">
	       			<h1 class="cp-title cp_responsive" style="color: <?php echo esc_attr( $a['modal_title_color'] ); ?>;"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_title1'] ) ) ); ?></h1>
	      		</div>	              	
	      		<div class="cp-desc-container <?php if( trim( $a['modal_short_desc1'] ) == '' ) { echo 'cp-empty'; } ?>">
	        		<div class="cp-description cp_responsive" style="color: <?php echo esc_attr( $a['modal_desc_color'] ); ?>;"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_short_desc1'] ) ) ); ?></div>
	      		</div>
	         	<div class="cp-form-container <?php echo esc_attr( $form_style ); ?>">
	          		<?php if( $a['mailer'] == "custom-form" ) {
				  		echo do_shortcode( stripslashes( $a['custom_html_form'] ) );
			  		} else { ?>
						<div class="col-lg-12 form-main <?php echo esc_attr( $formfiled );?> ">
							<?php if( !$a['namefield'] ) { ?>
								<div class="cp-row">
									<?php if(!$a['btn_disp_next_line']) {  ?>
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 cp-email-form">
									<?php } else { ?>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-email-form">
									<?php } ?> 
							<?php } ?>
							<form id="smile-optin-form" class="cp-form">
				                <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?>
	              
	                			<?php if( $a['namefield'] ) { ?>
									<div class="col-lg-6 col-md-6 col-sm-12 col-lg-12 cp-form-name">
				                		<input class="cp-input cp-name" type="text" name="name" placeholder="<?php echo $a['name_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
				            		</div> 
				            		<div class="col-lg-6 col-md-6 col-sm-12 col-lg-12 cp-form-email cp-with-name form-content">
			                			<input class="cp-email " type="email" name="email" placeholder="<?php echo $a['placeholder_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
			            			</div> 
		            		</form>
	            			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-lg-12 cp-submit-btn" > 
	                		<?php } else { ?>
						                <input class="cp-email <?php if($a['btn_disp_next_line']) { echo 'cp-text-center'; } ?>" type="email" name="email" placeholder="<?php echo $a['placeholder_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
						            	</form>
										</div>  
						            	<?php if(!$a['btn_disp_next_line']) {  ?>
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-lg-12 cp-submit-btn form-button"> 
										<?php } else { ?>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-lg-12 cp-submit-btn form-button"> 
										<?php } ?> 
	            			<?php } ?>
	            				<button class="cp-submit btn-modal btn btn-subscribe <?php echo esc_attr( $a['btn_style'] ); ?>" type="submit">
									    <?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?>
								</button>		                	
		                		</div>
	                		</div>				        
	        	      	</div>
	                	<?php } ?>          		
		                <?php if(! $a['namefield']){ ?> </div><?php } ?>
		                <div class="cp-form-processing-wrap">
		                	<div class="cp-form-processing" style=""></div>
		                </div>
		                <div class="cp-info-container <?php if( trim( $a['modal_confidential'] ) == '' ) { echo 'cp-empty'; } ?>" style="color: <?php echo esc_attr( $a['tip_color'] ); ?>;">
		                	<?php echo do_shortcode( html_entity_decode( $a['modal_confidential'] ) ); ?>
		                </div>
        	</div>		            	
		</div> <!-- .cp-modal-body-inner close -->	          	
	</div> <!-- cp-row close  -->
	<!-- AFTER CONTENTS -->
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_after', array( $a ) );

	   return ob_get_clean();
	}
}	