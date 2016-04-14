
jQuery(document).ready(function(){
	

	// set focus events
	jQuery("body").on("click", ".cp-title,.cp-description", function(e){ parent.setFocusElement('content_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-info-container", function(e){ parent.setFocusElement('form_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-content-section", function(e){ parent.setFocusElement('content_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-form-section", function(e){ parent.setFocusElement('form_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-form-separator", function(e){ parent.setFocusElement('form_separator'); e.stopPropagation(); });

	//	Add CSS file of this style
	var css_file = '/jugaad/jugaad.min.css';
	jQuery('head').append('<link rel="stylesheet" href="' + cp.demo_dir + css_file + '" type="text/css" />');

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){

		
		// data - this is an object that stores all your input information in a format - input:value

		// html container variables 
		var style 					= data.style,
			cp_submit 				= jQuery(".cp-submit"),
			cp_form_button      	= jQuery(".form-button"),
			cp_modal_body			= jQuery(".cp-modal-body"),
			cp_modal				= jQuery(".cp-modal"),
			cp_modal_content		= jQuery(".cp-modal-content"),
			modal_overlay			= jQuery(".cp-overlay"),
			cp_modal_body_inner		= jQuery(".cp-modal-body-inner"),
			cp_md_overlay       	= jQuery(".cp-modal-body-overlay"),
			form_with_name 			= jQuery(".cp-form-with-name"),
			form_without_name 		= jQuery(".cp-form-without-name"),
			cp_email_form       	= jQuery(".cp-email-form"),
			cp_email_input      	= jQuery(".cp-email.cp-input"),
			form_section        	= jQuery(".cp-form-section"),
			content_section     	= jQuery(".cp-content-section"),
			cp_short_title          = jQuery(".cp-short-title"),
			cp_modal_note           = jQuery(".cp-modal-note"),
			cp_modal_note_2         = jQuery(".cp-modal-note-2");

		// data variables  	
		var modal_size					= data.modal_size,
			cp_modal_width				= data.cp_modal_width,
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
			form_position               = data.form_position,
			img_position                = data.img_position,
			modal_image 	    		= data.modal_image,
			cp_img_container			= jQuery(".cp-image-container"),
			cp_submit_container         = jQuery('.cp-submit-container'),
			image_vertical_position 	= data.image_vertical_position,
			image_horizontal_position 	= data.image_horizontal_position,
			image_size 					= data.image_size,
			placeholder_font      		= data.placeholder_font,
			modal_image_size			= data.modal_image_size,
			form_bg_color               = data.form_bg_color,
			form_bg_image               = data.form_bg_image,
			content_bg_color            = data.content_bg_color,
			content_bg_image            = data.content_bg_image,
			form_separator              = data.form_separator,
			form_sep_part_of            = data.form_sep_part_of,
			form_sep_fill_color         = data.form_sep_fill_color,
			modal_layout                = data.modal_layout,
			modal_col_width             = data.modal_col_width,
			modal_short_title           = data.modal_short_title,
			modal_note_1                = data.modal_note_1,
			modal_note_2                = data.modal_note_2; 

 
		/**
 		 *	Add Selected Google Fonts
 		 *--------------------------------------------------------*/
		cp_get_gfonts(cp_google_fonts);	

		// add custom css 
		cp_add_custom_css(data);	

		// apply animations to modal
		cp_apply_animations(data);

		// affiliate settings 
		cp_affilate_settings(data);
		cp_affilate_reinitialize(data);

		cp_tooltip_settings(data); // close button and tool tip related settings 
		cp_tooltip_reinitialize(data); // reinitialize tool tip on modal resize

		var width = window.outerWidth;
		var vw = jQuery(window).width();

		//for responsive name field	
		if( width >= 1366 && vw >= 768 ){
			cp_name_form.addClass('cp_big_name');
		} else {
			cp_name_form.removeClass('cp_big_name');
		}

		var border = generateBorderCss(border_str);		
		var box_shadow = generateBoxShadow(box_shadow_str);
		var style = '';		

		if( box_shadow.indexOf("inset") > -1 ) {
			style = border; 
			cp_modal_content.attr('style', style);
			cp_md_overlay.attr('style', box_shadow);
			cp_modal_content.css('box-shadow', 'none');			
		} else {
			cp_md_overlay.css('box-shadow', 'none');
			style = border+';'+box_shadow; 
			cp_modal_content.attr('style', style);						
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
		
		var modal_img_default = modal_image;
		if( modal_img_default.indexOf('http') === -1 )
		 {					
			if( modal_image !== "" ) {
				var img_data = {action:'cp_get_image',img_id:modal_image,size:modal_image_size};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: "POST",
					success: function(img){
						cp_img_container.html('<img src="'+img+'" class="cp-image cp-highlight" />');
						cp_img_container.find('img').css({'top': image_vertical_position+'px','left': image_horizontal_position+'px' ,'max-width': image_size+'px'});
						cp_set_width_svg();
					}
				});
			} else {
				cp_img_container.html('');
				cp_img_container.find('img').removeAttr('style');
			}
		} else {
			modal_image_full_src = modal_image.split('|');
			modal_image_src = modal_image_full_src[0];
			cp_img_container.html('<img src="'+modal_image_src+'" class="cp-image cp-highlight" />');
			cp_img_container.find('img').css({'top': image_vertical_position+'px','left': image_horizontal_position+'px','max-width': image_size+'px'});
		}

		if( modal_title == "" ) {
			jQuery(".cp-row.cp-blank-title").css('display','none');
		} else {
			jQuery(".cp-row.cp-blank-title").css('display','block');
		}
	
		// Modal image 
		cp_image_processing(data);
		cp_image_settings(data);

		// form background 
		cp_form_background(data);

		// content background 
		cp_content_background(data);

		jQuery(".cp-submit-container").attr('class','cp-submit-container col-xs-12');
		jQuery(".cp-name-form").attr('class','cp-name-form');
		jQuery(".cp-email-form").attr('class','cp-email-form');	

		// input form field	
		if( namefield == 1 ) {
			form_without_name.css({"display":"none"});
			form_with_name.css({"display":"block"});
			cp_submit.removeClass("cp_simple_submit");
			cp_submit_container.removeClass("col-md-5 col-lg-5").addClass('col-md-12 col-lg-12 cp_name_submit');
			cp_email_form.removeClass('col-md-12 col-lg-12 col-md-7 col-sm-7 col-lg-7');
			cp_email_input.removeClass('cp-text-center');
			jQuery('.cp-form-with-name .cp-name-form,.cp-form-with-name .cp-email-form').addClass('col-md-12 col-sm-12 col-lg-12').removeClass('col-md-6 col-sm-6 col-lg-6');
		} else {
			cp_submit_container.addClass('col-md-5 col-lg-5 col-sm-5').removeClass('col-md-12 col-lg-12');
			if( btn_disp_next_line == 1 ) {
				cp_submit_container.removeClass("col-md-5 col-sm-5 col-lg-5").addClass('col-md-12 col-lg-12 cp_simple_submit');
				cp_email_form.removeClass('col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-lg-12 col-xs-12');
				cp_email_input.addClass('cp-text-center');
			} else {
				cp_email_form.removeClass('col-md-12 col-lg-12').addClass('col-md-7 col-sm-7 col-lg-7');
				cp_submit_container.removeClass("col-md-12 col-lg-12").addClass('col-md-5 col-sm-5 cp_simple_submit');
				cp_email_input.removeClass('cp-text-center');
			}

			form_without_name.css({"display":"block"});
			form_with_name.css({"display":"none"});		
		}

		var addFormClasses = '',
			addContentClasses = '';

		var rmClasses = 'col-md-6 col-sm-6 col-lg-6 col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7';

		if( modal_layout == 'form_left' || modal_layout == 'form_right' || modal_layout == 'form_left_img_top' || modal_layout == 'form_right_img_top' ) { 
			if ( modal_col_width == 0 ) {
				addFormClasses = addContentClasses = 'col-md-6 col-sm-6 col-lg-6';
			} else {
				addFormClasses  = 'col-md-5 col-sm-5 col-lg-5';
				addContentClasses = 'col-md-7 col-sm-7 col-lg-7';
			}
		}

		content_section.removeClass( rmClasses + 'form-sep-padding cp-columns-equalized').addClass(addContentClasses);				
		jQuery(".cp-form-section").removeClass(rmClasses +' form-sep-padding').addClass(addFormClasses);
		jQuery(".cp-jugaad-text-container,.cp-image-container").removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-sm-12');
		jQuery(".cp-jugaad-text-container,.cp-info-container").addClass('cp-text-center');

		form_section.insertBefore(".cp-content-section");
		jQuery(".cp-image-container").insertBefore(".cp-jugaad-text-container");
		jQuery(".cp-image-container").removeClass('cp-hidden-element');
		jQuery(".cp-image-container").show();
		var form_sep_pos = 'vertical';
		var form_sep_direction = 'upward';
		jQuery(".cp-modal-body > .cp-row").attr('class', "cp-row cp-table "+ modal_layout);
		jQuery(".cp-modal-body > .cp-row").css('height','auto');

		if ( modal_col_width == 0 ) {
			jQuery(".cp-modal-body > .cp-row").addClass('form-one-by-two');	
		} else {
			jQuery(".cp-modal-body > .cp-row").addClass('form-one-third');
		}
		
		switch(modal_layout) {
			case "form_left": 
				content_section.css('height','auto');
				form_section.addClass("form-pos-left").removeClass('form-pos-right form-pos-bottom');
				jQuery(".cp-image-container").addClass('cp-hidden-element');
				jQuery(".cp-jugaad-text-container").addClass('txt-pos-bottom').removeClass('txt-pos-left txt-pos-right');
				if( form_sep_part_of == 1 ) {
					form_sep_direction = 'downward';
				}					
			break;
			case "form_right":
				form_section.addClass("form-pos-right").removeClass('form-pos-left form-pos-bottom');
				content_section.insertBefore(".cp-form-section").css('height','auto');
				jQuery(".cp-image-container").addClass('cp-hidden-element');
				jQuery(".cp-jugaad-text-container").addClass('txt-pos-bottom').removeClass('txt-pos-left txt-pos-right');
				if( form_sep_part_of == 0 ) {
					form_sep_direction = 'downward';
				}
			break;
			case "form_left_img_top":
					form_section.addClass("form-pos-left cp-column-equalized-center").removeClass('form-pos-right form-pos-bottom');
					jQuery(".cp-jugaad-text-container").addClass('txt-pos-bottom').removeClass('txt-pos-left txt-pos-right cp-column-equalized-center');	
					content_section.addClass('cp-column-equalized-center');
					jQuery(".cp-image-container").insertBefore(".cp-jugaad-text-container").removeClass('cp-column-equalized-center');	
					if( form_sep_part_of == 1 ) {
						form_sep_direction = 'downward';
					}			
			break;				
			case "form_right_img_top":
					form_section.addClass("form-pos-right cp-column-equalized-center").removeClass('form-pos-left form-pos-bottom');
					content_section.insertBefore(".cp-form-section").css('height','auto').addClass('cp-column-equalized-center');
					jQuery(".cp-image-container").insertBefore(".cp-jugaad-text-container").removeClass('cp-column-equalized-center');
					jQuery(".cp-jugaad-text-container").removeClass('cp-column-equalized-center');
					if( form_sep_part_of == 0 ) {
						form_sep_direction = 'downward';
					}
			break;
			case "img_left_form_bottom":
				jQuery(".cp-modal-body > .cp-row").addClass("cp-block").removeClass('cp-table');
				form_section.addClass("form-pos-bottom").removeClass('form-pos-right form-pos-left');
				jQuery(".cp-form-section,.cp-content-section").removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-sm-12 col-lg-12');
				jQuery(".cp-jugaad-text-container").removeClass('col-md-12 col-sm-12 col-sm-5 col-md-5 col-lg-5 cp-text-center').addClass('col-md-7 col-sm-7 col-lg-7 cp-column-equalized-center');
				jQuery(".cp-image-container").removeClass('col-md-12 col-sm-12 cp-text-center').addClass('col-md-5 col-sm-5 col-lg-5 cp-column-equalized-center');
				content_section.insertBefore(".cp-form-section").addClass('cp-columns-equalized');
				jQuery(".cp-image-container").insertBefore(".cp-jugaad-text-container");
				jQuery(".cp-info-container").removeClass('cp-text-center');
				form_sep_pos = 'horizontal';
				if( form_sep_part_of == 0 ) {
					form_sep_direction = 'downward';
				}

				jQuery('.cp-form-with-name .cp-name-form,.cp-form-with-name .cp-email-form').addClass('col-md-6 col-sm-6 col-lg-6').removeClass('col-md-12 col-sm-12 col-lg-12');
				if( namefield == '1' ) {
					jQuery('.cp-name-form,.cp-email-form,.cp-submit-container').addClass('col-md-5 col-sm-5 col-lg-5').removeClass('col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7');
				}

			break;
			case "img_right_form_bottom":
				jQuery(".cp-modal-body > .cp-row").addClass("cp-block").removeClass('cp-table').css( 'height', 'auto' );
				form_section.addClass("form-pos-bottom").removeClass('form-pos-right form-pos-left');
				jQuery(".cp-form-section,.cp-content-section").removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-sm-12 col-lg-12');
				jQuery(".cp-jugaad-text-container").removeClass('col-md-12 col-sm-12 col-sm-5 col-md-5 col-lg-5 cp-text-center').addClass('col-md-7 col-sm-7 col-lg-7 cp-column-equalized-center');
				jQuery(".cp-image-container").removeClass('col-md-12 col-sm-12 cp-text-center').addClass('col-md-5 col-sm-5 col-lg-5 cp-column-equalized-center');
				content_section.insertBefore(".cp-form-section").addClass('cp-columns-equalized');
				jQuery(".cp-jugaad-text-container").insertBefore(".cp-image-container");
				jQuery(".cp-info-container").removeClass('cp-text-center');
				form_sep_pos = 'horizontal';
				if( form_sep_part_of == 0 ) {
					form_sep_direction = 'downward';
				}
				jQuery('.cp-form-with-name .cp-name-form,.cp-form-with-name .cp-email-form').addClass('col-md-6 col-sm-6 col-lg-6').removeClass('col-md-12 col-sm-12 col-lg-12');
				if( namefield == '1' ) {
					jQuery('.cp-name-form,.cp-email-form,.cp-submit-container').addClass('col-md-5 col-sm-5 col-lg-5').removeClass('col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7');
				}	
			break;
			case "form_bottom_img_top":
				jQuery(".cp-modal-body > .cp-row").addClass("cp-block").removeClass('cp-table');
				form_section.addClass("form-pos-bottom").removeClass('form-pos-right form-pos-left');
				jQuery(".cp-form-section,.cp-content-section").removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-sm-12 col-lg-12');
			    jQuery(".cp-jugaad-text-container").removeClass('col-md-7 col-sm-7 col-sm-5 col-md-5 col-lg-5 cp-column-equalized-center').addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
				jQuery(".cp-image-container").removeClass('col-md-5 col-sm-5 cp-column-equalized-center').addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
				content_section.insertBefore(".cp-form-section").css('height','auto');
				jQuery(".cp-image-container").insertBefore(".cp-jugaad-text-container");
				form_sep_pos = 'horizontal';
				if( form_sep_part_of == 0 ) {
					form_sep_direction = 'downward';
				}
				if( namefield == '1' ) {
					jQuery('.cp-name-form,.cp-email-form,.cp-submit-container').addClass('col-md-5 col-sm-5 col-lg-5').removeClass('col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7');
				}
			break;
			case "form_bottom":
				jQuery(".cp-image-container").addClass('cp-hidden-element');
				jQuery(".cp-modal-body > .cp-row").addClass("cp-block").removeClass('cp-table');
				form_section.addClass("form-pos-bottom").removeClass('form-pos-right form-pos-left');
				jQuery(".cp-form-section,.cp-content-section").removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7').addClass('col-md-12 col-sm-12 col-lg-12');
				jQuery(".cp-jugaad-text-container").removeClass('col-md-7 col-sm-7 col-sm-5 col-md-5 col-lg-5 cp-column-equalized-center').addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
				jQuery(".cp-image-container").removeClass('col-md-5 col-sm-5 cp-column-equalized-center').addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
				content_section.insertBefore(".cp-form-section").css('height','auto');
				form_sep_pos = 'horizontal';
				if( form_sep_part_of == 0 ) {
					form_sep_direction = 'downward';
				}
				if( namefield == '1' ) {
					jQuery('.cp-name-form,.cp-email-form,.cp-submit-container').addClass('col-md-5 col-sm-5 col-lg-5').removeClass('col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7');
				}
			break;
		}

		var svg = '';
		if(form_sep_part_of == 0)  {
			var svgFillColor =  content_bg_color;
			var form_part_of = 'part-of-content';
		} else {
			var svgFillColor = form_bg_color;
			var form_part_of = 'part-of-form';
		}

		jQuery(".cp-form-separator").remove();
		
		var viewbox  = cp_get_viewbox_svg(form_separator);

		var shape = form_separator;	

		if( form_separator !== 'none' ) {
	
			var svg = cp_get_svg(shape,svgFillColor,viewbox,form_sep_part_of);
			var cp_modal_height = jQuery('.cp-modal-body').height();
			var form_sep_width = cp_modal_height +'px';

			if( form_sep_pos == 'horizontal' ) {
				if( form_sep_part_of == 0 ) {
					jQuery(".cp-modal-body .cp-row .cp-content-section").append('<div class="cp-form-separator '+form_separator+' '+modal_layout+' '+form_sep_direction+' part-of-content cp-fs-'+form_sep_pos+' cp-fs-'+form_sep_pos+'-content" >'+svg+'<div>');
				} else {
					jQuery(".cp-modal-body .cp-row .cp-form-section").append('<div class="cp-form-separator '+form_separator+' '+modal_layout+' '+form_sep_direction+' part-of-form cp-fs-'+form_sep_pos+' cp-fs-'+form_sep_pos+'-form" >'+svg+'<div>');
				}
			} else {
				if( form_sep_part_of == 0 ) {
					jQuery(".cp-modal-body .cp-row").append('<div class="cp-form-separator '+form_separator+' '+modal_layout+' '+form_sep_direction+' part-of-content cp-fs-'+form_sep_pos+' cp-fs-'+form_sep_pos+'-content" >'+svg+'<div>');
				} else {
					jQuery(".cp-modal-body .cp-row").append('<div class="cp-form-separator '+form_separator+' '+modal_layout+' '+form_sep_direction+' part-of-form cp-fs-'+form_sep_pos+' cp-fs-'+form_sep_pos+'-form" >'+svg+'<div>');
				}				
			}
		}

		form_sep_position();
		cp_form_sep_top();
		cp_set_width_svg();

		jQuery(window).resize(function(e) {						
			cp_affilate_reinitialize(data);
			cp_tooltip_reinitialize(data);	
			cp_image_processing(data);
			cp_image_settings(data);
		});	

		//Short title
		modal_short_title = htmlEntities(modal_short_title);
		cp_short_title.html(modal_short_title);
		if( jQuery("#short_title_editor").length ) {
			CKEDITOR.instances.short_title_editor.setData(modal_short_title);
		}

		// Modal Note
		modal_note_1 = htmlEntities(modal_note_1);
		cp_modal_note.html(modal_note_1);
		if( jQuery("#modal_note_1").length ) {
			CKEDITOR.instances.modal_note_1.setData(modal_note_1);
		}

		// Modal Note
		modal_note_2 = htmlEntities(modal_note_2);
		cp_modal_note_2.html(modal_note_2);
		if( jQuery("#modal_note_2").length ) {
			CKEDITOR.instances.modal_note_2.setData(modal_note_2);
		}

		// add cp-empty class to empty containers
		jQuery.each( cp_empty_classes, function( key, value) {
			if( jQuery(value).length !== 0 ) {
				cp_add_empty_class(key,value);
			}	
		});

		CPModelHeight();

	});
});


function cp_form_background(data) {

	var form_bg_image_size		= data.form_bg_image_size,
		form_bg_image           = data.form_bg_image,
		form_opt_bg             = data.form_opt_bg,
		form_opt_bg 			= form_opt_bg.split("|"),
		form_bg_repeat 			= form_opt_bg[0],
		form_bg_pos 			= form_opt_bg[1],
		form_bg_size 			= form_opt_bg[2],
		form_bg_color           = data.form_bg_color,
		form_section        	= jQuery(".cp-form-section"),
		form_section_overlay 	= jQuery(".cp-form-section-overlay");

	form_section_overlay.css("background-color",form_bg_color);	
	if( form_bg_image !== '' ) {
		var img_data = {action:'cp_get_image',img_id:form_bg_image,size:form_bg_image_size};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: "POST",
			success: function(img){
				form_section.css({
					"background-image"    : 'url('+img+')',
					"background-repeat"   : form_bg_repeat,
					"background-position" : form_bg_pos,
					"background-size"     : form_bg_size
				});					
			}
		});
	} else {
		form_section.css({
			"background-image"    : 'none'
		});	
	}
}

function cp_content_background(data) {

	var content_bg_image_size	= data.content_bg_image_size,
		content_bg_image        = data.content_bg_image,
		content_opt_bg          = data.content_opt_bg,
		content_bg_color        = data.content_bg_color,
		content_opt_bg 			= content_opt_bg.split("|"),
		content_bg_repeat 		= content_opt_bg[0],
		content_bg_pos 			= content_opt_bg[1],
		content_bg_size 		= content_opt_bg[2],
		content_section        	= jQuery(".cp-content-section"),
		content_section_overlay = jQuery(".cp-content-section-overlay");

	content_section_overlay.css("background-color",content_bg_color);	
	if( content_bg_image !== '' ) {
		var img_data = {action:'cp_get_image',img_id:content_bg_image,size:content_bg_image_size};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: "POST",
			success: function(img){
				content_section.css({
					"background-image"    : 'url('+img+')',
					"background-repeat"   : content_bg_repeat,
					"background-position" : content_bg_pos,
					"background-size"     : content_bg_size
				});					
			}
		});
	} else {
		content_section.css({
			"background-image"    : 'none'
		});	
	}
}

jQuery(document).on('ckeditorChange', function() {
	setTimeout(function() {
		cp_form_sep_top();
		cp_set_width_svg();
	}, 100);
});
