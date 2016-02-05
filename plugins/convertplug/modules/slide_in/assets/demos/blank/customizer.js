
jQuery(document).ready(function(){

	//	Add CSS file of this style
	var css_file = '/blank/blank.css';
	jQuery('head').append('<link rel="stylesheet" href="' + slide_in.demo_dir + css_file + '" type="text/css" />');

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		
		// data - this is an object that stores all your input information in a format - input:value

		// html container variables 

		var cp_slidein_height		= 'auto',
			cp_submit 				= jQuery(".cp-submit"),
			cp_form_button      	= jQuery(".form-button"),
			cp_slidein_body			= jQuery(".cp-slidein-body"),
			cp_slidein				= jQuery(".cp-slidein"),
			cp_slidein_content		= jQuery(".cp-slidein-content"),
			slidein_overlay			= jQuery(".slidein-overlay"),
			cp_slidein_body_inner	= jQuery(".cp-slidein-body-inner"),
			cp_si_overlay       	= jQuery(".cp-slidein-body-overlay"),
			cp_submit_container 	= jQuery('.cp-submit-container'),
			cp_slide_edit_btn 		= jQuery(".cp-slide-edit-btn"),
			cp_animate_container 	= jQuery(".cp-animate-container");
	
		// data variables  	
		var style 						= data.style,
			cp_slidein_width			= data.cp_slidein_width,
			slidein_title 				= data.slidein_title1,
			bg_color					= data.slidein_bg_color,
			slidein_position			= data.slidein_position,
			slidein_title_color			= data.slidein_title_color,
			tip_color					= data.tip_color,
			border_str 					= data.border,
			btn_disp_next_line  		= data.btn_disp_next_line,
			box_shadow_str 				= data.box_shadow,
			slidein_content				= data.slidein_content,
			close_txt					= data.close_txt,
			content_padding				= data.content_padding,
			slidein_bg_image			= data.slidein_bg_image,
			opt_bg						= data.opt_bg,
			affiliate_title 			= data.affiliate_title,
			cp_google_fonts 			= data.cp_google_fonts,
			toggle_btn					= data.toggle_btn;
			
 		/**
 		 *	Add Selected Google Fonts
 		 *--------------------------------------------------------*/
		cp_get_gfonts(cp_google_fonts);

		slidein_content = htmlEntities(slidein_content);

		if( slidein_content !== "" && typeof slidein_content !== "undefined" && jQuery( "#short_desc_editor" ).length !== 0 ){
			CKEDITOR.instances.short_desc_editor.setData(slidein_content);
		}	

		// Add Slide In position class to body
		cp_add_class_for_body(slidein_position);

		// add custom css 
		cp_add_custom_css(data);	

		// apply animations to slide in
		cp_apply_animations(data);

		cp_tooltip_settings(data); // close button and tooltip related settings 
		cp_tooltip_reinitialize(data); // reinitialize tooltip on slidein resize

		var width = window.outerWidth;
		var vw = jQuery(window).width();

		var border = generateBorderCss(border_str);		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		

		if( box_shadow.indexOf("inset") > -1 ) {
			style = border;
			cp_slidein_content.attr('style', style);
			cp_si_overlay.attr('style', box_shadow);
			cp_slidein_content.css('box-shadow', 'none');			
		} else {
			cp_si_overlay.css('box-shadow', 'none');
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
		
		// set slide in width
		cp_slidein_width_settings(data);	

		// setup all editors
		cp_editor_setup(data);	

		//slide in button setup
		slide_button_setting(data);	
		
		if( !cp_slidein.hasClass("cp-slidein-exceed") ){
			cp_slidein.attr('class', 'cp-slidein slidein-'+slidein_position);
		} else {
			cp_slidein.attr('class', 'cp-slidein slidein-'+slidein_position+' cp-slidein-exceed');
		}
		
		// Slide In background image
		cp_bg_image(data);

		jQuery(window).resize(function(e) {						
			cp_tooltip_reinitialize(data);	
			cp_apply_animations(data);
		});

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