jQuery(document).ready(function(){

	//	Add CSS file of this style
	var css_file = '/optin/optin.min.css';
	jQuery('head').append('<link rel="stylesheet" href="' + slide_in.demo_dir + css_file + '" type="text/css" />');

	//for middle description
	if( jQuery("#mid_desc_editor").length !== 0 ) {	
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'mid_desc_editor' );			
		CKEDITOR.instances.mid_desc_editor.on( 'change', function() {
			var data = CKEDITOR.instances.mid_desc_editor.getData();
			parent.updateHTML(data,'smile_slidein_middle_desc');
		} );
	}

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		// data - this is an object that stores all your input information in a format - input:value

		var style 					= data.style,
			cp_submit 				= jQuery(".cp-submit"),
			cp_form_button      	= jQuery(".form-button"),
			cp_slidein_body			= jQuery(".cp-slidein-body"),
			cp_slidein				= jQuery(".cp-slidein"),
			cp_slidein_content		= jQuery(".cp-slidein-content"),
			slidein_overlay			= jQuery(".cp-overlay"),
			cp_slidein_body_inner	= jQuery(".cp-slidein-body-inner"),
			cp_md_overlay       	= jQuery(".cp-slidein-body-overlay"),
			form_with_name 			= jQuery(".cp-form-with-name"),
			form_without_name 		= jQuery(".cp-form-without-name");
			cp_description 			= jQuery(".cp-description"),
			cp_desc_bottom 			= jQuery(".cp-desc-bottom"),
			cp_name_form			= jQuery(".cp-name-form"),
			cp_email_form			= jQuery(".cp-email-form"),
			cp_optin_form			= jQuery(".cp-optin-form"),
			cp_info_container 		= jQuery(".cp-info-container"),
			cp_mid_desc 			= jQuery(".cp-mid-description"),
			cp_slide_edit_btn 		= jQuery(".cp-slide-edit-btn"),
			cp_animate_container 	= jQuery(".cp-animate-container");

			 	
		// style dependent variables  	
		var cp_slidein_width			= data.cp_slidein_width,
			cp_slidein_height			= 'auto',
			slidein_position			= data.slidein_position,
			slidein_title 				= data.slidein_title1,
			bg_color					= data.slidein_bg_color,
			overlay_bg_color			= data.slidein_overlay_bg_color,
			slidein_title_color			= data.slidein_title_color,
			tip_color					= data.tip_color,
			border_str 					= data.border,
			box_shadow_str 				= data.box_shadow,
			slidein_content				= data.slidein_content,
			close_txt					= data.close_txt,
			content_padding				= data.content_padding,
			slidein_bg_image			= data.slidein_bg_image,
			opt_bg						= data.opt_bg,
			slidein_bg_image_size		= data.slidein_bg_image_size,
			namefield 					= data.namefield,
			affiliate_title 			= data.affiliate_title,
			cp_google_fonts 			= data.cp_google_fonts,
			cp_name_form        		= jQuery(".cp-name-form"),
			slidein_image 	    		= data.slidein_image,
			cp_img_container			= jQuery(".cp-image-container"),
			cp_submit_container         = jQuery('.cp-submit-container'),
			image_vertical_position 	= data.image_vertical_position,
			image_horizontal_position 	= data.image_horizontal_position,
			image_size 					= data.image_size,
			image_resp_width 		  	= data.image_resp_width,
			slidein_image_size			= data.slidein_image_size,
			form_border_color 			= data.form_border_color,
			form_bg_color				= data.form_bg_color,
			btn_disp_next_line 			= data.btn_disp_next_line,
			slidein_confidential 		= data.slidein_confidential,
			slidein_middle_desc 		= data.slidein_middle_desc,
			toggle_btn					= data.toggle_btn;
 
		/**
 		 *	Add Selected Google Fonts
 		 *--------------------------------------------------------*/
		cp_get_gfonts(cp_google_fonts);

		// Add Slide In position class to body
		cp_add_class_for_body(slidein_position);

		// add custom css 
		cp_add_custom_css(data);	

	  	//if( toggle_btn == 0 ) {
			// apply animations to slide in
			cp_apply_animations(data);
		//}

		cp_tooltip_settings(data); // close button and tool tip related settings 
		cp_tooltip_reinitialize(data); // reinitialize tool tip on slide in resize

		var border = generateBorderCss(border_str);	
		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		

		if( box_shadow.indexOf("inset") > -1 ) {
			style = border; 
			cp_slidein_content.attr('style', style);
			cp_md_overlay.attr('style', box_shadow);
			cp_slidein_content.css('box-shadow', 'none');
		} else {
			cp_md_overlay.css('box-shadow', 'none');
			style = border+';'+box_shadow; 
			cp_slidein_content.attr('style', style);						
		}
		
		if( typeof content_padding !== "undefined" && content_padding !== "" ){
			if( content_padding == "1" || content_padding == 1){
				cp_slidein_body.addClass('no-padding');
			} else {
				cp_slidein_body.removeClass('no-padding');
			}
		}
		
		// set Slide In width
		cp_slidein_width_settings(data);		
				
		// setup all editors
		cp_editor_setup(data);	

		cp_form_style(data);	

		slidein_overlay.css('background',overlay_bg_color);

		// if( !cp_slidein.hasClass("cp-slidein-exceed") ){
		// 	cp_slidein.attr('class', 'cp-slidein slidein-'+slidein_position);
		// } else {
		// 	cp_slidein.attr('class', 'cp-slidein slidein-'+slidein_position+' cp-slidein-exceed');
		// }	

		cp_slidein.removeClass('slidein-top-center slidein-bottom-center slidein-center-left slidein-center-right slidein-top-left slidein-bottom-right slidein-bottom-left slidein-top-right').addClass('slidein-'+slidein_position);	
		
		//extra middle editor 
		slidein_middle_desc = htmlEntities(slidein_middle_desc);
		cp_mid_desc.html(slidein_middle_desc);
		if( jQuery("#mid_desc_editor").length !== 0 ) {
			CKEDITOR.instances.mid_desc_editor.setData(slidein_middle_desc);
		}

		if( slidein_title == "" ) {
			jQuery(".cp-row.cp-blank-title").css('display','none');
		} else {
			jQuery(".cp-row.cp-blank-title").css('display','block');
		}	

		// slide in background image
		cp_bg_image(data);

		//Slide In button settings
		slide_button_setting(data);
		
		if( namefield == 1 ) {
			form_without_name.css({"display":"none"});
			form_with_name.css({"display":"block"});
			cp_submit_container.removeClass("cp_simple_submit");
			cp_submit_container.removeClass("col-md-4 col-sm-4 col-lg-4").addClass('col-md-4 col-sm-4 col-lg-4 cp_name_submit');
			cp_email_form.removeClass('cp-email-wth-btn-onnext');

		} else {
			form_without_name.css({"display":"block"});
			form_with_name.css({"display":"none"});
			cp_submit_container.removeClass("cp_name_submit");				
			if( btn_disp_next_line == '1' ){
				cp_email_form.removeClass('col-md-8 col-sm-8 col-lg-8').addClass('col-md-12 col-lg-12 col-sm-12 cp-email-wth-btn-onnext');
				cp_submit_container.removeClass("col-md-4 col-sm-4 col-lg-4").addClass('col-md-12 col-lg-12 col-sm-12 cp_simple_submit');
			} else {
				cp_email_form.removeClass('col-md-12 col-lg-12 col-sm-12 cp-email-wth-btn-onnext').addClass('col-md-8 col-sm-8 col-lg-8');
				cp_submit_container.removeClass("col-md-12 col-lg-12 col-sm-12 ").addClass('col-md-4 col-sm-4 col-lg-4 cp_simple_submit');
			}
		}
		
		jQuery(".cp-name-form").removeClass('cp_big_name');	

		// Form border color
		cp_optin_form.css({'border-top-color':form_border_color , 'background-color':form_bg_color});

		jQuery(window).resize(function(e) {						
			cp_tooltip_reinitialize(data);	
			cp_apply_animations(data);			
			calwidth();		

		});

		jQuery(document).ready(function(e) {
			calwidth();//function for responsive form
		});	

		function calwidth(){
			var width = window.outerWidth;
			var vw = jQuery(window).width();
			var vh = jQuery(window).height();			
			//for responsive name and email field	
			if( vw >= 768 ){
				cp_name_form.addClass('cp_big_name');
			} else {
				cp_name_form.removeClass('cp_big_name');
				if(namefield !== '1'){				
					if(btn_disp_next_line == 1 ){
						cp_email_form.addClass('cp_big_email');
					}else{
						cp_email_form.removeClass('cp_big_email');
					}
				}else{	
					cp_email_form.removeClass('cp_big_email');
				}
			}	
		}
		
		// add cp-empty class to empty containers
		jQuery.each( cp_empty_classes, function( key, value) {
			if( jQuery(value).length !== 0 ) {
				cp_add_empty_class(key,value);
			}	
		});

		// blinking cursor  
		cp_blinking_cursor('#main_title_editor',slidein_title_color);

	});
});