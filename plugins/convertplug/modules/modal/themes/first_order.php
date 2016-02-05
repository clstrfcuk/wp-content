<?php
if( !function_exists( "modal_theme_first_order" ) ) {
	function modal_theme_first_order( $atts, $content = null ){
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
		$redirect_url = $success_message = $on_success = $button_title = $button_txt_color = $button_bg_color = $mailer = $schedule = $msg_wrong_email = '';
		$opt_bg = $modal_bg_image = $inactivity = $hide_on_device = $hide_on_os = $hide_on_browser = '';
		$input_bg_color = $placeholder_color =$placeholder_text = $input_border_color = $button_border_color = $image_position = $image_size = $image_horizontal_position = $image_vertical_position = $image_displayon_mobile = '' ;
		$affiliate_setting = $affiliate_username = $affiliate_title = $image_resp_width = $disable_overlay_effect = $hide_animation_width = $close_modal_on = $modal_title_bg_color = '';

		$a = shortcode_atts( array(
			/** = Global Common - Options
			 *-----------------------------------------------------------*/			
			'style'						=> '',
			'modal_id'					=> '',
			'display'					=> '',
			'option'					=> '',
			'new_style'					=> '',
			'modal_size'				=> '',
			'overlay_effect'			=> 'cp-overlay-zoomin',
			'exit_animation'			=> '',
			'cp_modal_width'			=> '',
			'cp_modal_height'			=> 'auto',
			'modal_bg_color'			=> 'rgb(255, 255, 128)',
			'modal_overlay_bg_color'	=> 'rgba(68, 68, 68, 0.7)',
			'modal_title1'				=> '',
			'modal_title_color'			=> 'rgb(76, 17, 48)',
			'modal_desc_color'			=> 'rgb(76, 17, 48)',
			'tip_color'					=> '',
			'modal_content'				=> '',
			'modal_confidential'		=> '',
			'modal_short_desc1'			=> '',
			'close_modal'				=> '',
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
			'modal_exit_intent'			=> '',
			'close_txt'					=> '',
			'close_img'					=> '',
			'content_padding'			=> '',
			'modal_title_bg_color'		=> '',
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
			'modal_bg_image'			=> '',
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
			'namefiled'					=> '',
			'name_text'					=> '',
			'close_position'            => '',
			'button_border_color'		=> '',
			'close_bg_color' 			=> '',
			'close_modal_on'            => '',
			'image_size' 				=> '',
			'affiliate_setting' 		=> '',
			'affiliate_username'		=> '',
			'affiliate_title' 			=> '',
			'image_resp_width'			=> '',
			'disable_overlay_effect' 	=> '',
			'hide_animation_width'		=> '',
			'enable_custom_class' 		=> '',
			'redirect_data'				=> '',
			'close_modal_tooltip'		=> '',
			'tooltip_title'				=> '',
			'tooltip_background'		=> '',
			'tooltip_title_color'		=> '',			
			'namefield'					=> '',
			//	Required
			'style_class' 				=> 'cp-first-order',
			'cp_close_image_width'      => '',
			// modal image  		
			'modal_image'				=> '',	
			'image_displayon_mobile'	=> '',
			// 'image_resp_width'			=> '',
			'image_position'			=> '',			
			'image_horizontal_position' => '',
			'image_vertical_position'   => '',
			'image_size' 				=> '',

			//	button - submit
			'btn_border_radius'			=> '',
			'button_txt_hover_color' 	=> '',
			'button_bg_gradient_color'	=> '',
			'button_bg_hover_color'		=> '',
			'btn_shadow_color'			=> '',
			'btn_shadow'				=> '',
			'btn_style'					=> '',
			'button_conversion'			=> true,
			'enable_after_post'			=> '',
			'items_in_cart'				=> '',
			'custom_css'				=> '',

		), $style_settings );	
		
		//	Merge arrays - 'shortcode atts' & 'style options'
		$a = array_merge($a, $atts );


		/** = Style - individual options
		 *-----------------------------------------------------------*/
		$imgclass 			= ( $a['image_position'] == 0 ) ? 'cp-right-contain' : '';
		$formfiled 			= ( $a['namefield'] ) ? 'cp-formwith-name' : 'cp-form-simple';
		$on_success_action 	= ( $a['on_success'] == "redirect" ) ? $a['redirect_url'] : $a['success_message'] ;
		$input_style		= cp_add_css( 'background-color', $a['input_bg_color'] );
		$input_style	   .= cp_add_css( 'color', $a['placeholder_color'] );
		$input_style	   .= cp_add_css( 'border-color', $a['input_border_color'] );
		$imageStyle		 	= cp_add_css( 'left', $a['image_horizontal_position'], 'px');
		$imageStyle		   .= cp_add_css( 'top', $a['image_vertical_position'], 'px');
		$imageStyle		   .= cp_add_css( 'max-width', $a['image_size'], 'px');

		//	Functions
		$afilate_class 	 		= cp_get_affiliate_class_init( $a['affiliate_setting'], $a['modal_size'] );
		$afilate_link 			= cp_get_affiliate_link_init( $a['affiliate_setting'], $a['affiliate_username'] );

		//	Filters & Actions
		$modal_image 			= apply_filters( 'cp_get_modal_image_url', $a['modal_image'] );

		/** = Before filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_before', array( $a ) );

		//if link is empty
		$link = $a['button_title'];
		$cp_link = '';
		if (strpos($link,'href') == false) {
		   $cp_link = 'cp-link';
		}
		
 ?>
          	<div class="cp-row">
            	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-text-container <?php echo esc_attr($imgclass); ?>" >
               		
               		<div class="">
		              <div class="cp-image-container"> 
		              	<img style="<?php echo esc_attr($imageStyle); ?>" src="<?php echo esc_attr( $modal_image ); ?>" class="cp-image"> 
		              </div>
		            </div>
		            <div class="cp-title-container <?php if( trim( $a['modal_title1'] ) == '' ) { echo 'cp-empty'; } ?>">
               			<h1 class="cp-title cp_responsive" ><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_title1'] ) ) ); ?></h1>
               		</div>              
              		<div class="cp-short-desc-container cp-clear  <?php if( trim( $a['modal_content'] ) == '' ) { echo 'cp-empty'; } ?>">              		
              			<div class="cp-short-description cp_responsive cp-clear " ><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_content'] ) ) ); ?></div>
	             	</div>
	             	<div class="cp-form-container ">
	              		<div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 form-button">
                        
                        	<?php if( $a['button_conversion'] ) { ?>
                            	<form id="smile-optin-form" class="cp-form">
                                    <?php apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) ); ?>
								</form>
							<?php }?>

							<button class="cp-submit btn-modal btn btn-subscribe cp_responsive <?php echo esc_attr( $cp_link );?> <?php echo esc_attr( $a['btn_style'] ); ?>" type="submit">
	                			    <?php echo do_shortcode( html_entity_decode( stripcslashes( $a['button_title'] ) ) ); ?>
	                		</button>	                	
	                	</div>
	                </div>
		            <div class="cp-info-container cp_responsive <?php if( trim( $a['modal_confidential'] ) == '' ) { echo 'cp-empty'; } ?>" >
		                <?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_confidential'] ) ) ); ?>
		           </div>
            </div> 
        </div>
       <!-- AFTER CONTENTS -->		 
<?php
		/** = After filter
		 *-----------------------------------------------------------*/
		apply_filters_ref_array('cp_modal_global_after', array( $a ) );

	   return ob_get_clean();
	}
}