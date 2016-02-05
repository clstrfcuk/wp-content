// jQuery.fn.center = function () {
// 	var $this = this.find('.cp-modal-content');
// 	this.css("top", ( ( window.outerHeight - $this.height() ) / 2 ) + jQuery(window).scrollTop() + "px");
// 	return this;
// }

jQuery(document).ready(function(){

	//	Add CSS file of this style
	var css_file = '/flat_discount/flat_discount.min.css';
	jQuery('head').append('<link rel="stylesheet" href="' + cp.demo_dir + css_file + '" type="text/css" />');

	jQuery("html").css('overflow','hidden');
	// setTimeout(function(){
	// 	var cp_modal = jQuery(".cp-modal");
	// 	cp_modal.center();
	// },500);	

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		// data - this is an object that stores all your input information in a format - input:value

		// Common variables 
		var style 				= data.style,
			cp_submit 			= jQuery(".cp-submit"),
			cp_form_button      = jQuery(".form-button"),
			cp_modal_body		= jQuery(".cp-modal-body"),
			cp_modal			= jQuery(".cp-modal"),
			cp_modal_content	= jQuery(".cp-modal-content"),
			modal_overlay		= jQuery(".cp-overlay"),
			cp_modal_body_inner	= jQuery(".cp-modal-body-inner"),
			cp_md_overlay       = jQuery(".cp-modal-body-overlay"),
			form_with_name 		= jQuery(".cp-form-with-name"),
			form_without_name 	= jQuery(".cp-form-without-name"),
			cp_email_form       = jQuery(".cp-email-form"),
			cp_email_input      = jQuery(".cp-email.cp-input");


		// style dependent variables  	
		var modal_size					= data.modal_size,
			cp_modal_width				= data.cp_modal_width,
			cp_modal_height				= 'auto',
			modal_title 				= data.modal_title1,
			bg_color					= data.modal_bg_color,
			overlay_bg_color			= data.modal_overlay_bg_color,
			modal_title_color			= data.modal_title_color,
			tip_color					= data.tip_color,
			border_str 					= data.border,
			btn_disp_next_line  		= data.btn_disp_next_line,
			box_shadow_str 				= data.box_shadow,
			modal_content				= data.modal_content,
			close_txt					= data.close_txt,
			content_padding				= data.content_padding,
			modal_bg_image				= data.modal_bg_image,
			opt_bg						= data.opt_bg,
			modal_bg_image_size			= data.modal_bg_image_size,
			namefield 					= data.namefield,
			affiliate_title 			= data.affiliate_title,
			cp_google_fonts 			= data.cp_google_fonts,
			cp_name_form        		= jQuery(".cp-name-form"),
			modal_image 	    		= data.modal_image,
			cp_img_container			= jQuery(".cp-image-container"),
			cp_submit_container         = jQuery('.cp-submit-container'),
			image_vertical_position 	= data.image_vertical_position,
			image_horizontal_position 	= data.image_horizontal_position,
			image_size 					= data.image_size,
			image_resp_width 		  	= data.image_resp_width,
			modal_image_size			= data.modal_image_size;
			cp_modal_body_inner			= jQuery(".cp-modal-body-inner");	
			
 
		/**
 		 *	Add Selected Google Fonts
 		 *--------------------------------------------------------*/
		cp_get_gfonts(cp_google_fonts);	

		// add custom css 
		cp_add_custom_css(data);	

		// apply animations to modal
		cp_apply_animations(data);

		// affilate settings 
		cp_affilate_settings(data);
		cp_affilate_reinitialize(data);

		cp_tooltip_settings(data); // close button and tooltip related settings 
		cp_tooltip_reinitialize(data); // reinitialize tooltip on modal resize

		var border = generateBorderCss(border_str);		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		

		if( box_shadow.indexOf("inset") > -1 ) {
				style = border; 
				cp_modal_body_inner.attr('style', style);
				cp_md_overlay.attr('style', box_shadow);
				cp_modal_content.css('box-shadow', 'none');
			
		} else {
				cp_md_overlay.css('box-shadow', 'none');
				cp_modal_content.attr('style', box_shadow);	
				cp_modal_body_inner.attr('style', border);						
		}
		
		if( typeof content_padding !== "undefined" && content_padding !== "" ){
			if( content_padding == "1" || content_padding == 1){
				cp_modal_body.addClass('no-padding');
			} else {
				cp_modal_body.removeClass('no-padding');
			}
		}
		
		// set modal width
		cp_modal_width_settings(data);		
				
		// setup all editors
		cp_editor_setup(data);	

		cp_form_style(data);	

		modal_overlay.css('background',overlay_bg_color);
		
		if( !cp_modal.hasClass("cp-modal-exceed") ){
			cp_modal.attr('class', 'cp-modal '+modal_size);
		} else {
			cp_modal.attr('class', 'cp-modal cp-modal-exceed '+modal_size);
		}

		if( modal_title == "" ){
			jQuery(".cp-row.cp-blank-title").css('display','none');
		} else {
			jQuery(".cp-row.cp-blank-title").css('display','block');
		}
		
		// modal background image
		cp_bg_image(data);

		//input form field	
		if( namefield == 1 ){
			form_without_name.css({"display":"none"});
			form_with_name.css({"display":"block"});
			cp_submit.removeClass("cp_simple_submit").addClass('cp_name_submit');
			cp_email_input.removeClass('cp-text-center');
			cp_email_form.removeClass('col-md-8 col-lg-8 col-sm-8 col-md-12 col-lg-12').addClass('col-sm-12 col-xs-12');
			cp_form_button.removeClass("col-md-6 col-lg-6 col-md-4 col-sm-4 col-lg-4").addClass('col-md-12 col-sm-12 col-lg-12 col-xs-12');
		} else {
			form_without_name.css({"display":"block"});
			form_with_name.css({"display":"none"});
			cp_submit.removeClass("cp_name_submit").addClass('cp_simple_submit');
			if( btn_disp_next_line == 1 ) {
				cp_form_button.removeClass("col-md-4 col-lg-4 col-sm-4").addClass('col-md-12 col-lg-12 col-sm-12 col-xs-12 cp_simple_submit');
				cp_email_form.removeClass('col-md-8 col-lg-8 col-sm-8').addClass('col-md-12 col-lg-12 col-xs-12 col-sm-12');
				cp_email_input.addClass('cp-text-center');
			} else {
				cp_email_form.removeClass('col-md-12 col-lg-12 col-sm-12').addClass('col-md-8 col-lg-8 col-sm-8');
				cp_form_button.removeClass("col-md-12 col-lg-12 col-sm-12").addClass('col-md-4 col-sm-4 col-lg-4 cp_simple_submit');
				cp_email_input.removeClass('cp-text-center');
			}
		}

		jQuery(window).resize(function(e) {	
			cp_affilate_reinitialize(data);
			cp_tooltip_reinitialize(data);
		});
		
		///cp_modal.center();

		// add cp-empty class to empty containers
		jQuery.each( cp_empty_classes, function( key, value) {
			if( jQuery(value).length !== 0 ) {
				cp_add_empty_class(key,value);
			}	
		});

		// blinking cursor  
		cp_blinking_cursor('.cp-title',modal_title_color);		

	});
});