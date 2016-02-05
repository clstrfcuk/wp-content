<?php
if( !function_exists( "slide_in_theme_optin" ) ) {
	function slide_in_theme_optin( $atts, $content = null ){
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
		
		$style = $slidein_id = $option = $new_style = $slidein_size = $overlay_effect = $cp_slidein_width = $cp_slidein_height = $slidein_bg_color = '';
		$slidein_overlay_bg_color = $slidein_title = $slidein_title_color = $slidein_desc_color = $close_slidein = $close_txt = $slidein_content = $loading_delay = '';
		$developer_mode = $conversion_cookie = $closed_cookie =  $display_on_first_load = $show_for_logged_in = $autoload_on_duration = $autoload_on_scroll = '';
		$load_on_duration = $load_after_scroll = $mailer = $custom_class = $live = $global = $border = $box_shadow = $shadow_type = $pages_to_exclude = '';
		$cats_to_exclude = $exclusive_pages = $exclusive_cats = $slidein_exit_intent = $close_slidein = $close_txt = $close_img =$content_padding = '';
		$slidein_title_bg_color = $slidein_image = $slidein_short_desc1 = $slidein_confidential = $tip_color = $form_type = $custom_html_form = $close_text_color = '';
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $mailer = $schedule = $msg_wrong_email = '';
		$opt_bg = $slidein_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = $image_vertical_position = $image_displayon_mobile = '' ;
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = '';
		$form_bg_color = '';

		$a = shortcode_atts( array(

			/** = Global Common - Options
			 *-----------------------------------------------------------*/
			'style'						=> '',
			'slidein_id'				=> '',
			'slidein_position'			=> '',
			'option'					=> '',
			'new_style'					=> '',
			'slidein_size'				=> '',
			'overlay_effect'			=> 'cp-overlay-zoomin',
			'exit_animation'			=> '',
			'cp_slidein_width'			=> '',
			'cp_slidein_height'			=> 'auto',
			'slidein_bg_color'			=> '',
			'slidein_bg_gradient' 		=> '',
			'slidein_bg_gradient_lighten' => '',
			'slidein_overlay_bg_color'	=> '',
			'tip_color'					=> '',	
			'cp_close_image_width'      => '',

			/*---close-----*/
			'close_position'            => '',
			'close_slidein'				=> '',
			'close_txt'					=> 'Close',
			'close_txt'					=> '',
			'close_img'					=> '',
			'close_text_color' 			=> '',

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
			'display_on_first_load'		=> '',
			'slidein_exit_intent'			=> '',	
			'content_padding'			=> '',
			'schedule'					=> '',
			'inactivity'				=> '',
			'hide_on_device'			=> '',
			'hide_on_os'				=> '',
			'hide_on_browser'			=> '',
			'all_users'					=> '',	

			'border'					=> '',
			'box_shadow'				=> '',
			'shadow_type'				=> '',			
			'slidein_title1'				=> '',
			'slidein_title_color'			=> 'rgb(76, 17, 48)',
			'slidein_desc_color'			=> 'rgb(76, 17, 48)',
			'slidein_content'				=> '',
			'slidein_confidential'		=> '',
			'slidein_short_desc1'			=> '',
			'custom_class'				=> '',			
			'slidein_title_bg_color'		=> '',
			'button_title'				=> '',
			'button_bg_color'			=> '',
			'button_txt_color'			=> '',
			'border_size'				=> '',
			'msg_wrong_email'			=> '',
			'cp_google_fonts'			=> '',
			'opt_bg'					=> '',
			'slidein_bg_image'			=> '',			
			'input_bg_color' 			=> '',
			'placeholder_color' 		=> '',
			'placeholder_text' 			=> '',
			'input_border_color' 		=> '',
			'namefield'					=> '',
			'name_text'					=> '',
			'button_border_color'		=> '',
			'image_position'			=> '',
			'close_bg_color' 			=> '',
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',
			'image_displayon_mobile'	=> '',	
			'image_resp_width'			=> '',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'enable_custom_class' 		=> '',

			/*----------Form - Options----------*/
			'form_type'					=> '',
			'custom_html_form'			=> '',
			'mailer'				=> '',
			'mailer'					=> '',
			'success_message'			=> '',
			'form_border_color'			=> '',
			'form_bg_color' 			=> '',

			//	button - submit
			'btn_border_radius'			=> '',
			'button_txt_hover_color' 	=> '',
			'button_bg_gradient_color'	=> '',
			'button_bg_hover_color'		=> '',
			'btn_shadow_color'			=> '',
			'btn_shadow'				=> '',
			'btn_style'					=> '',

			//	Redirection
			'on_success'				=> '',
			'redirect_url'				=> '',
			'redirect_data'				=> '',
			
			/*--------------affiliate-------------*/
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',
			
			/*--------------tooltip----------------*/
			'close_slidein_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',
			'btn_disp_next_line'		=> '',

			'display'					=> '',


			/** = Style - Options
			 *-----------------------------------------------------------*/
			//	Required
			'style_class' 				=> 'cp-optin',
			'placeholder_font'			=> '',
			'slidein_middle_desc'			=> '',

			//Slide In button
			'button_animation'				=>'',
			'toggle_button_font'			=>'',
			'side_btn_gradient'				=>'',
			'slide_button_title'			=>'',
			'slidein_btn_position'			=>'',
			'side_btn_style'				=>'',
			'side_button_bg_color'			=>'',
			'side_button_txt_hover_color'	=>'',
			'side_button_bg_hover_color' 	=>'',
			'side_button_bg_gradient_color'	=>'',
			'side_btn_border_radius'		=>'',
			'side_btn_shadow'				=>'',
			'toggle_btn'                    =>'',
			'slide_button_text_color'       =>'',
			'custom_css'					=>'',

			//	Triggers
			'enable_after_post'				=> '',

		), $style_settings );

		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );

		/** = Style - individual options
		 *-----------------------------------------------------------*/
		//	Variables
		//$imgclass 			= ( $a['image_position'] == 0 ) ? 'cp-right-contain' : '';
		$imgclass 			='';
		$formfiled 			= ( $a['namefield'] ) ? 'cp-formwith-name' : 'cp-form-simple';
		$on_success_action 	= ($a['on_success'] == "redirect") ? $a['redirect_url'] : $a['success_message'] ;
		$input_style		= cp_add_css( 'background-color', $a['input_bg_color'] );
		$input_style	   .= cp_add_css( 'color', $a['placeholder_color'] );
		$input_style	   .= cp_add_css( 'border-color', $a['input_border_color'] );
		$imageStyle		 	= cp_add_css( 'left', $a['image_horizontal_position'], 'px');
		$imageStyle		   .= cp_add_css( 'top', $a['image_vertical_position'], 'px');
		$imageStyle		   .= cp_add_css( 'mafx-width', $a['image_size'], '%');

		//	Functions
		$afilate_class 	 		= cp_get_affiliate_class_init( $a['affiliate_setting'], $a['slidein_size'] );
		$afilate_link 			= cp_get_affiliate_link_init( $a['affiliate_setting'], $a['affiliate_username'] );

		//	Filters & Actions
		//$slidein_image 			= apply_filters( 'cp_get_slidein_image_url', $a['slidein_image'] );
		// $afilate_class 	 	= apply_filters( 'cp_get_affiliate_class', $affiliate_setting, $slidein_size );

		//for form text align center
		$form_style='';
		if($a['btn_disp_next_line'] || $a['namefield']){			
			$form_style = 'cp-center-align-text';
		}
		
		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_slidein_global_before', array( $a ) );
		
?>
<!-- BEFORE CONTENTS -->
          	<div class="cp-row">
            	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-text-container <?php echo esc_attr($imgclass); ?>" >
               		
              		<div class="cp-title-container <?php if( trim( $a['slidein_title1'] ) == '' ) { echo 'cp-empty'; } ?>">
               			<h1 class="cp-title cp_responsive"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_title1'] ) ) ); ?></h1>
              		</div>
              		<div class="cp-desc-container <?php if( trim( $a['slidein_short_desc1'] ) == '' ) { echo 'cp-empty'; } ?>">
                		<div class="cp-description cp_responsive" ><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_short_desc1'] ) ) ); ?></div>
              		</div>
                </div><!-- end of text container-->

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-optin-form">
	             	<div class="cp-form-container <?php echo esc_attr( $form_style ); ?>">
	      			<?php if( $a['mailer'] == "custom-form" ) {
			  			echo do_shortcode( stripslashes( stripcslashes( $a['custom_html_form'] ) ) );
		  			} else { ?>
		  				<div class="col-lg-12 form-main <?php echo esc_attr( $formfiled );?> ">
							<?php if( !$a['namefield'] ) { ?>					
									<?php if($a['btn_disp_next_line'])	{?>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-form-email">
									<?php }else{?>
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 cp-form-email">
									<?php } ?>	
									
										<?php }else{?>
											<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 cp-form-name">
										<?php } ?>
										<form id="smile-optin-form" class="cp-form">
							                <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?>

				                			<?php if( $a['namefield'] ) { ?>				                			
												<div class="col-lg-6 col-md-6 col-sm-6 col-lg-12 col-xs-12 cp-form-name">
							                		<input class="cp-input cp-name" type="text" name="name" placeholder="<?php echo $a['name_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
							            		</div>
						    	        		<div class="col-lg-6 col-md-6 col-sm-6 col-lg-12 col-xs-12 cp-form-email form-content">
					            	    			<input class="cp-email " type="email" name="email" placeholder="<?php echo $a['placeholder_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
					            				</div>	
					            				</form><!-- .form-main -->
									        	</div><!-- .col-lg-8 -->
						            		<div class="col-lg-4 col-md-4 col-sm-4 col-lg-12 form-button"> 
					            			<button class="cp-submit btn-slidein btn btn-subscribe <?php echo esc_attr( $a['btn_style'] ); ?>" type="submit">
					            	 			<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?>
					            			</button>	
			            					</div><!-- .form-button -->					            									            			
					            			<?php } else { ?>
					                			<input class="cp-email " type="email" name="email" placeholder="<?php echo $a['placeholder_text']; ?>" required="required" style="<?php echo esc_attr( $input_style ); ?>;margin-right: 15px;" >
								           	</form><!-- .form-main -->
									        </div><!-- .col-lg-8 -->
									        <?php if($a['btn_disp_next_line'])	{?>
												<div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 form-button-nxt-line"> 
											<?php }else{?>
												<div class="col-lg-4 col-md-4 col-sm-4 col-lg-12 form-button"> 
											<?php } ?>	
						            		
					            			<button class="cp-submit btn-slidein btn btn-subscribe <?php echo esc_attr( $a['btn_style'] ); ?>" type="submit">
					            	 			<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?>
					            			</button>	
			            			</div><!-- .form-button -->
								           <?php } ?>
						            		
	    						</div><!-- .form-main -->
	      					</div><!-- .form-container -->	                
		                <?php } ?>	

		                <div class="cp-info-container <?php if( trim( $a['slidein_confidential'] ) == '' ) { echo 'cp-empty'; } ?>" >
		                	<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_confidential'] ) ) ); ?>
		                </div>
	            </div><!-- .optin form -->
	       
          	</div><!--row-->
      
		<!-- AFTER CONTENTS -->
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_slidein_global_after', array( $a ) );

	   return ob_get_clean();
	}
}