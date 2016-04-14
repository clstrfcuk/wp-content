//	Add class 'cp-no-responsive' to manage the line height of cp-highlight
function cp_set_no_responsive( sel, data ) {
	if ( data.toLowerCase().indexOf("cp_font") >= 0 && data.match("^<span") && data.match("</span>$") ) {
		sel.addClass('cp-no-responsive');
	} else {
		sel.removeClass('cp-no-responsive');
	}
}

var cp_empty_classes = {
		".cp-title" 				: ".cp-title-container",
		".cp-sec-title"             : ".cp-sec-title-container",
		".cp-description"  			: ".cp-desc-container",
		".cp-info-container" 		: ".cp-info-container",
		".cp-short-description"  	: ".cp-short-desc-container",
		".cp-desc-bottom"           : ".cp-desc-timetable",
		".cp-mid-description"       : ".cp-mid-desc-container"	
	};	

jQuery(document).ready(function(){

	jQuery.each( cp_empty_classes, function( key, value) {

		if( jQuery(value).length !== 0 ) {
			jQuery(value).focusout( function() {
				cp_add_empty_class(key,value);
			} );

			jQuery(value).focusin( function() {
				cp_remove_empty_class(value);
			} );
		}
	});	

	jQuery("html").css('overflow','hidden');
	
	if( jQuery("#main_title_editor").length !== 0 ) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'main_title_editor' );	

		//	Initially set show CKEditor of 'cp-title'
		//	Ref: http://docs.ckeditor.com/#!/api/CKEDITOR.focusManager
		var focusManager = new CKEDITOR.focusManager( CKEDITOR.instances.main_title_editor );
		focusManager.focus();

		CKEDITOR.instances.main_title_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CP_slide_in_height();

			// Remove Blinking cursor 
			jQuery(".cp-slidein-body").find(".blinking-cursor").remove();

			//	Check & update responsive font sizes
			check_responsive_font_sizes();
			
			//set color for li tags 
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.main_title_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_slidein_title1');
		} );

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.main_title_editor.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     		editor.setReadOnly( false );
		} );
	}

	if( jQuery("#info_editor").length ) {

		var sel_info_editor = jQuery("#info_editor");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'info_editor' );
		CKEDITOR.instances.info_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.info_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.info_editor.getData();
			cp_set_no_responsive( sel_info_editor, data );
		});

		CKEDITOR.instances.info_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CP_slide_in_height();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.info_editor.getData();
			parent.updateHTML(data,'smile_slidein_confidential');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_info_editor, data );

		} );

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.info_editor.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     		editor.setReadOnly( false );
		} );
	}

	if( jQuery("#sec_title_editor").length !== 0 ) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'sec_title_editor' );	
		CKEDITOR.instances.sec_title_editor.on( 'change', function() {

			//	Check & update responsive font sizes
			check_responsive_font_sizes();

			//	Set class - `cp-modal-exceed`
			CP_slide_in_height();

			//set color for li tags 
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.sec_title_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_slidein_sec_title');
		} );

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.sec_title_editor.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     		editor.setReadOnly( false );
		} );
	}

	
	if( jQuery("#desc_editor").length !== 0 ) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'desc_editor' );	
		CKEDITOR.instances.desc_editor.config.toolbar = 'Small';
		CKEDITOR.instances.desc_editor.on( 'change', function() {

			//	Check & update responsive font sizes
			check_responsive_font_sizes();

			//set color for li tags 
        	cp_color_for_list_tag();

        	//	Set class - `cp-modal-exceed`
			CP_slide_in_height();

			var data = CKEDITOR.instances.desc_editor.getData();
			parent.updateHTML(data,'smile_slidein_short_desc1');
		} );

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.desc_editor.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     		editor.setReadOnly( false );
		} );
	}

	if(jQuery("#short_desc_editor").length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'short_desc_editor' );	
		CKEDITOR.instances.short_desc_editor.config.toolbar = 'Small';
		CKEDITOR.instances.short_desc_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CP_slide_in_height();

			//	Check & update responsive font sizes
			check_responsive_font_sizes();

			//set color for li tags 
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.short_desc_editor.getData();
			parent.updateHTML(data,'smile_slidein_content');
		} );

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.short_desc_editor.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     		editor.setReadOnly( false );
		} );
	}
	
	//open slide on click of button
	jQuery("body").on("click", ".cp-toggle-container", function(e){ 
		
		jQuery(this).toggleClass("cp-slide-hide-btn");

		parent.setFocusElement('slide_button_title'); 

		var	cp_animate_container = jQuery(".cp-animate-container"),
			entryanimation       = cp_animate_container.attr("data-entry-animation"),
			slidein_overlay 	 = jQuery(".slidein-overlay");			

		slidein_overlay.addClass('cp-slidein-click');
		cp_animate_container.attr( 'class', 'cp-animate-container cp-hide-slide smile-animated' );
		
		setTimeout(function() {	
			cp_animate_container.attr('class' , 'cp-animate-container smile-animated '+entryanimation);
		}, 10);

		jQuery('cp-backend-tooltip-hide').remove();
		jQuery('head').append('<style class="cp-backend-tooltip-hide">.customize-support .tip[class*="arrow"]:before{display:block} .tip[class*="arrow"]:before{display:block}.customize-support .tip[class*="arrow"]{display:block} .tip[class*="arrow"]{display:block}</style>');

		e.stopPropagation();
	});	

	
	// close slide in on click of button
	jQuery("body").on("click", ".slidein-overlay-close", function(e){

		if( !jQuery(".slidein-overlay").hasClass('cp-slide-without-toggle') ) {
			var cp_toggle_container    = jQuery(".cp-toggle-container"),
				exitanimation 		 = jQuery(".cp-animate-container").attr('data-exit-animation'),
				cp_animate_container = jQuery(".cp-animate-container"),
				slidein_overlay 	 = jQuery(".slidein-overlay");		

	         cp_animate_container.attr('class' , 'cp-animate-container smile-animated '+exitanimation);
	          
	         slidein_overlay.removeClass('cp-slidein-click'); 

			setTimeout(function() {
				cp_animate_container.addClass("cp-hide-slide");
				cp_toggle_container.removeClass("cp-slide-hide-btn");
				cp_animate_container.removeClass(exitanimation);
			}, 500);

			jQuery('cp-backend-tooltip-hide').remove();
			jQuery('head').append('<style class="cp-backend-tooltip-hide">.customize-support .tip[class*="arrow"]:before{display:none} .tip[class*="arrow"]:before{display:none}.customize-support .tip[class*="arrow"]{display:none} .tip[class*="arrow"]{display:none}</style>');


		} else {
			e.stopPropagation();
		}
		
	});	

	jQuery("body").on("click", ".cp-image", function(e){ parent.setFocusElement('slidein_image'); e.stopPropagation(); });	
	jQuery("body").on("click", ".cp-submit", function(e){ parent.setFocusElement('button_bg_color'); e.stopPropagation(); });	
	jQuery("body").on("click", ".cp-slidein-body", function(e){ parent.setFocusElement('slidein_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-name", function(e){ parent.setFocusElement('name_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".slidein-overlay-close", function(e){ parent.setFocusElement('close_slidein'); e.stopPropagation(); });

	// remove blinking cursor
	jQuery("body").on("click select", ".cp-highlight,.cp-name,.cp-email", function(e){
		jQuery(".cp-slidein-body").find(".blinking-cursor").remove();
	}); 

	jQuery(this).on('submit','form',function(e){
		e.preventDefault();
	});

});

jQuery(window).load(function(){
	parent.customizerLoaded();
});


// removes &nbsp; and <br> tags from html string 
function cp_get_clean_string(string) {
	var cleanString = string.replace(/[<]br[^>]*[>]/gi, '').replace(/[&]nbsp[;]/gi, '').replace(/[\u200B]/g, ''); 
	cleanString = jQuery.trim(cleanString);
	return cleanString;
}
		
// Add cp-empty class
function cp_add_empty_class(element,container) {

	var cleanString 	=  cp_get_clean_string( jQuery(element).html() );
	
	// Slide In title 
	if( cleanString.length == 0 ) {
		jQuery(container).addClass('cp-empty');
		jQuery(element).html(cleanString);
	} else {
		jQuery(container).removeClass('cp-empty');
	}
}

// removes cp-empty class from container
function cp_remove_empty_class(element) {
	if( jQuery(element).length !== 0 ) {
		jQuery(element).removeClass('cp-empty');
	}
}

function cp_image_settings(data) {
	var image_position 			= data.image_position,
		cp_text_container 		= jQuery(".cp-text-container"),
		cp_img_container		= jQuery(".cp-image-container");

	// image position left/right alignment
	if( image_position == 1 ){		 
		cp_text_container.removeClass('cp-right-contain');
	} else {			
		cp_text_container.addClass('cp-right-contain');
	}
}

// tooltip related settings
function cp_tooltip_settings(data) {

	var close_tooltip     		= '', 
		close_tooltip_end 		= '', 
		tip_position      		= '',
		tooltip_class     		= '',
		offset_position   		= '',
		innerclass        		= '',
		tooltip_title       	= data.tooltip_title,
		tooltip_title_color 	= data.tooltip_title_color,
		tooltip_background  	= data.tooltip_background,
		close_slidein_tooltip 	= data.close_slidein_tooltip,
		close_slidein	      	= data.close_slidein,
		close_img	      		= data.close_img,
		close_txt		  		= data.close_txt,
		overlay_close	  		= jQuery(".slidein-overlay-close"),
		cp_slidein		  		= jQuery(".cp-slidein"),
		cp_animate_container 	= jQuery(".cp-animate-container");
		close_text_color  		= data.close_text_color,
		slidein_overlay     	= jQuery(".slidein-overlay"),
		close_img_size			= data.close_img_size,
		cp_close_image_width    = data.cp_close_image_width;

	var close_img_default = close_img;				
	overlay_close.appendTo(cp_animate_container);
	overlay_close.addClass('cp-inside-close').removeClass('cp-adjacent-close');

	if( close_slidein_tooltip == '1' ){
		var psid = slidein_overlay.find(".cp-slidein-content").data('ps-id');
			
			tooltip_class = 'cp-custom-tooltip';		
			tip_position = "left";
			offset_position = 30;	
			
			jQuery('.has-tip:empty').remove();		
	} else {
		jQuery('.has-tip:empty').remove();
	}

	close_tooltip = '<span class="'+tooltip_class+' cp-tooltip-icon has-tip cp-tipcontent-'+psid+'" data-classes="close-tip-content-'+psid+'" data-position="'+tip_position+'"  title="'+tooltip_title+'" data-original-title ="'+tooltip_title+'" data-color="'+tooltip_title_color+'" data-bgcolor="'+tooltip_background+'" data-closeid ="cp-tipcontent-'+psid+'" data-offset="'+offset_position+'" >';
	close_tooltip_end ='</span>';

	if( close_slidein == "close_icon" ){
		jQuery(".slidein-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
		overlay_close.html('<i class="'+close_icon+'"></i>');
		overlay_close.css({"color":close_text_color});
	} else if( close_slidein == "close_txt" ){
		jQuery(".slidein-overlay-close").removeClass('cp-image-close').addClass('cp-text-close');
		overlay_close.html(close_tooltip+'<span class ="close-txt">'+close_txt+'</span>'+close_tooltip_end);
		overlay_close.css({"color":close_text_color});
	} else if(close_img_default.indexOf('http') === -1){
			if( close_slidein == "close_img" && close_img !== "" ) {
				jQuery(".slidein-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
				var img_data = {action:'cp_get_image',img_id:close_img,size:close_img_size};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: "POST",
					success: function(img){						
						overlay_close.html(close_tooltip+'<img src="'+img+'" />'+close_tooltip_end);
						jQuery(document).trigger("cp_ajax_loaded",[data]);
					}
				});
			} else {
				jQuery(".slidein-overlay-close").removeClass('cp-text-close cp-imnage-close');
				overlay_close.html('');
			}
	} else if( close_slidein == "do_not_close") {
		jQuery(".slidein-overlay-close").removeClass('cp-text-close cp-imnage-close');
		overlay_close.html('');
	} else if( close_img_default.indexOf('http') != -1 ) {
		close_img_full_src = close_img.split('|');
		close_img_src = close_img_full_src[0];
		jQuery(".slidein-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
		overlay_close.html(close_tooltip+'<img class="cp-default-close" src="'+close_img_src+'" />'+close_tooltip_end);
	}
		
	overlay_close.css('background',"transparent");

	if( close_slidein == "do_not_close") {
		overlay_close.css('background',"none");
	}

	if( close_slidein != 'close_txt' )
		overlay_close.css( 'width', cp_close_image_width+'px' );
	else 
		overlay_close.css( 'width', 'auto' );
}


// function to reinitialize tooltip
function cp_tooltip_reinitialize(data) {

	var close_slidein_tooltip 	 	= data.close_slidein_tooltip,
		slidein_overlay_close       = jQuery(".slidein-overlay-close"),
		cp_slidein_width			= data.cp_slidein_width,
		slidein_overlay     		= jQuery(".slidein-overlay"),
		innerclass                  = '',
		tooltip_background 			= data.tooltip_background,
		tooltip_title_color         = data.tooltip_title_color,
		psid 						= slidein_overlay.find(".cp-slidein-content").data('ps-id'),
		slidein_overlay_close     	= jQuery(".slidein-overlay-close"),
		slideinht  					= jQuery(".cp-slidein-content").outerHeight();
	
	//tool tip for slide in close
	if( close_slidein_tooltip == '1' ){
        var tooltip_classname = "cp-tipcontent-"+psid;       
        var tclass = "close-tip-content-"+psid;
        var vw = jQuery(window).width(); 

		if( cp_slidein_width > 768 ){
            jQuery(".has-tip").data("position" ,"left");
            tip_position = "left";
        } else {
    		jQuery(".has-tip").data("position" ,"left");
        	tip_position = "left";
        }
		
		if(slideinht >= 500){
           jQuery(".has-tip").data("position" ,"left");
            tip_position = "left";
        }

		if(slidein_overlay_close.hasClass('cp-text-close')){
			jQuery(".has-tip").data("position" ,"left");
			tip_position = "left";
		}

    	jQuery("."+tooltip_classname).frosty({ 
            className: 'tip close-tip-content-'+psid               
        });
    
    	jQuery(".cp-backend-tooltip-css").remove();

    	jQuery('head').append('<style class="cp-backend-tooltip-css">.customize-support .tip.'+tclass+'{color: '+tooltip_title_color+';background-color:'+tooltip_background+';border-color:'+tooltip_background+';border-radius:7px;padding:15px 30px;font-size:13px; }</style>');
        
        if( tip_position == 'left' ){
           jQuery('head').append('<style class="cp-backend-tooltip-css">.customize-support .'+tclass+'[class*="arrow"]:before{border-left-color: '+tooltip_background+';border-width:8px;margin-top:-8px;border-top-color:transparent }</style>');
        } else {
            jQuery('head').append('<style class="cp-backend-tooltip-css">.customize-support .'+tclass+'[class*="arrow"]:before{border-top-color: '+tooltip_background+';border-width:8px;margin-top:0px; border-left-color:transparent}</style>');
        }
	}	
}

// slide in image related settings
function cp_image_processing(data) {

	var vw = jQuery(window).width(),
		vh = jQuery(window).height(),
	 	image_displayon_mobile  = data.image_displayon_mobile,
		image_resp_width 		= "768",
		cp_text_container 		= jQuery(".cp-text-container"),
		cp_img_container		= jQuery(".cp-image-container"),
		image_position 			= data.image_position;

	// hide image on mobile devices
	var image_on_left = '';
	if( image_position == 1 ){
		image_on_left = 'cp-right-contain';
	}

	if( image_displayon_mobile == 1 ) {	
		if( vw <= image_resp_width ) {
            if( image_resp_width >= 768 ){	
                cp_text_container.removeClass('col-lg-7 col-md-7 col-sm-7').addClass('col-lg-12 col-md-12 col-sm-12 cp-bigtext-container');
            } else {
                cp_text_container.removeClass('col-lg-12 col-md-12 col-sm-12 cp-bigtext-container').addClass('col-lg-7 col-md-7 col-sm-7');
            }
        } else {
        	cp_text_container.removeClass('col-lg-12 col-md-12 col-sm-12 cp-bigtext-container').addClass('col-lg-7 col-md-7 col-sm-7');
        }

		if( vw <= image_resp_width ) {		
			cp_img_container.addClass('cp-hide-image');					
		} else {	
			cp_img_container.removeClass('cp-hide-image');
		}					
	} else {
		cp_text_container.removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-7 col-md-7 col-sm-7 '+image_on_left);
		cp_img_container.removeClass('cp-hide-image');
	}
}


// adds custom css 
function cp_add_custom_css(data) {
	var custom_css	= data.custom_css;
	jQuery("#cp-custom-style").remove();
	jQuery("head").append('<style id="cp-custom-style">'+custom_css+'</style>');
}

// animations in customizer  
function cp_apply_animations(data) {
	var disable_overlay_effect 	= data.disable_overlay_effect,
		hide_animation_width 	= data.hide_animation_width,
		overlay_effect			= data.overlay_effect,
		exit_animation			= data.exit_animation,
		cp_animate	   			= jQuery(".cp-animate-container"),
		slidein_overlay 		= jQuery(".slidein-overlay"),
        vw 						= jQuery(window).width(),
        toggle_btn 			= data.toggle_btn,
		toggle_btn_visible  = data.toggle_btn_visible;
     

	if(slidein_overlay.find('.cp-slidein-toggle').length > 0){     	
	  overlay_effect = 'slidein-smile-slideInUp';
	  disable_overlay_effect == 0;
	}
    
	if( disable_overlay_effect == 1 ){
		var vw = jQuery(window).width();
		if( vw <= hide_animation_width ){
			overlay_effect = exit_animation = 'slidein-overlay-none';
		}
	} else {
		cp_animate.removeClass('slidein-overlay-none');
	}

	var entry_anim = ( typeof cp_animate.attr("data-entry-animation") !== "undefined" ) ? cp_animate.attr("data-entry-animation") : '';
	var exit_anim = ( typeof cp_animate.attr("data-exit-animation") !== "undefined" ) ? cp_animate.attr("data-exit-animation") : '';

	cp_animate.attr('data-exit-animation', exit_animation );
	cp_animate.attr("data-entry-animation", overlay_effect );
		
	if( toggle_btn == '1' && toggle_btn_visible == '1' ) { 
		// do not apply animations to info bar 
	} else {
		
		if( !cp_animate.hasClass(exit_animation) && exit_animation !== exit_anim ){
			
			setTimeout(function(){
				if( exit_animation !== "none" ) {	
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated '+exit_animation);
					cp_animate.attr('data-entry-animation', overlay_effect );
				}
				setTimeout( function(){
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(exit_animation);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated '+entry_anim);
				}, 1000 );
			},500);		
		}
		
		if( !cp_animate.hasClass(overlay_effect) && overlay_effect !== entry_anim ){
			setTimeout(function(){
				if( overlay_effect !== "none" ) {
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated '+overlay_effect);
					cp_animate.attr('data-entry-animation', overlay_effect );
				}
			},500);		
		}
	}
}

// CP Darker / Lighter colors - {Start}
var pad = function(num, totalChars) {
    var pad = '0';
    num = num + '';
    while (num.length < totalChars) {
        num = pad + num;
    }
    return num;
};

// Ratio is between 0 and 1
var changeColor = function(color, ratio, darker) {
    // Trim trailing/leading whitespace
    color = color.replace(/^\s*|\s*$/, '');

    // Expand three-digit hex
    color = color.replace(
        /^#?([a-f0-9])([a-f0-9])([a-f0-9])$/i,
        '#$1$1$2$2$3$3'
    );

    // Calculate ratio
    var difference = Math.round(ratio * 256) * (darker ? -1 : 1),
        // Determine if input is RGB(A)
        rgb = color.match(new RegExp('^rgba?\\(\\s*' +
            '(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
            '\\s*,\\s*' +
            '(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
            '\\s*,\\s*' +
            '(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
            '(?:\\s*,\\s*' +
            '(0|1|0?\\.\\d+))?' +
            '\\s*\\)$'
        , 'i')),
        alpha = !!rgb && rgb[4] != null ? rgb[4] : null,

        // Convert hex to decimal
        decimal = !!rgb? [rgb[1], rgb[2], rgb[3]] : color.replace(
            /^#?([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i,
            function() {
                return parseInt(arguments[1], 16) + ',' +
                    parseInt(arguments[2], 16) + ',' +
                    parseInt(arguments[3], 16);
            }
        ).split(/,/),
        returnValue;

    // Return RGB(A)
    return !!rgb ?
        'rgb' + (alpha !== null ? 'a' : '') + '(' +
            Math[darker ? 'max' : 'min'](
                parseInt(decimal[0], 10) + difference, darker ? 0 : 255
            ) + ', ' +
            Math[darker ? 'max' : 'min'](
                parseInt(decimal[1], 10) + difference, darker ? 0 : 255
            ) + ', ' +
            Math[darker ? 'max' : 'min'](
                parseInt(decimal[2], 10) + difference, darker ? 0 : 255
            ) +
            (alpha !== null ? ', ' + alpha : '') +
            ')' :
        // Return hex
        [
            '#',
            pad(Math[darker ? 'max' : 'min'](
                parseInt(decimal[0], 10) + difference, darker ? 0 : 255
            ).toString(16), 2),
            pad(Math[darker ? 'max' : 'min'](
                parseInt(decimal[1], 10) + difference, darker ? 0 : 255
            ).toString(16), 2),
            pad(Math[darker ? 'max' : 'min'](
                parseInt(decimal[2], 10) + difference, darker ? 0 : 255
            ).toString(16), 2)
        ].join('');
};
var lighterColor = function(color, ratio) {
    return changeColor(color, ratio, false);
};
var darkerColor = function(color, ratio) {
    return changeColor(color, ratio, true);
};
// CP Darker / Lighter colors - {End}

// form inputs styling
function cp_form_style(data) {

	var cp_email_data 				= data.cp_email,
		placeholder_text		  	= data.placeholder_text,
		button_border_color 	   	= data.button_border_color,
		name_text 				 	= data.name_text,
		cp_submit 					= jQuery(".cp-submit"),
		cp_name						= jQuery(".cp-name"),
		cp_input					= jQuery(".cp-input"),
		cp_email 					= jQuery(".cp-email"),
		input_border_color 			= data.input_border_color,
		placeholder_color		 	= data.placeholder_color,
		btn_bg_color			  	= data.button_bg_color,
		button_bg_hover_color 		= data.button_bg_hover_color,
		mailer              		= data.mailer,
		form_container				= jQuery(".default-form"),
		html_form_container			= jQuery(".custom-html-form"),
		custom_html_form		  	= data.custom_html_form,
		input_bg_color 				= data.input_bg_color,

		btn_border 					= data.btn_border,
		btn_border_radius 			= data.btn_border_radius,
		btn_border_color 			= data.btn_border_color,
		btn_style 					= data.btn_style,
		btn_shadow 					= data.btn_shadow,
		button_txt_hover_color 		= data.button_txt_hover_color,
		placeholder_font      		= data.placeholder_font,
		btn_disp_next_line 			= data.btn_disp_next_line,
		namefield 					= data.namefield,
		cp_form_container 			= jQuery(".cp-form-container")

	if( mailer == "custom-form" ) {
		form_container.css('display','none');
		html_form_container.html(custom_html_form).show();
	} else {
		form_container.css('display','block');
		html_form_container.css('display','none');
	}

	// email field style
	cp_email.attr('placeholder',cp_email_data);
	cp_email.attr("value", placeholder_text);
	cp_email.attr("placeholder", placeholder_text);

	//	Remove all classes
	var classList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
	jQuery.each(classList, function(i, v){
       cp_submit.removeClass(v);
    });
	cp_submit.addClass( btn_style );

	var c_normal 	= btn_bg_color;
	var c_hover  	= darkerColor( btn_bg_color, .05 );
	var light 		= lighterColor( btn_bg_color, .3 );

	cp_submit.css('background', c_normal);
	//	Apply box shadow to submit button - If its set & equals to - 1
	var shadow = radius = '';
	if( btn_shadow == 1 ) {		
		shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
	}
	//	Add - border-radius
	if( btn_border_radius != '' ) {
		radius += 'border-radius: ' + btn_border_radius + 'px;';
	}

	jQuery("#cp-temporary-inline-css").remove();
	jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
	switch( btn_style ) {
		case 'cp-btn-flat': 		jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-slidein .' + btn_style + '.cp-submit{ background: '+c_normal+'!important;' + shadow + radius + '; } '
										+ '.cp-slidein .' + btn_style + '.cp-submit:hover { background: '+c_hover+'!important; } '
										+ '</style>');
			break;
		case 'cp-btn-3d': 			jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-slidein .' + btn_style + '.cp-submit {background: '+c_normal+'!important; '+radius+' position: relative ; box-shadow: 0 6px ' + c_hover + ';} '
										+ '.cp-slidein .' + btn_style + '.cp-submit:hover {background: '+c_normal+'!important;top: 2px; box-shadow: 0 4px ' + c_hover + ';} '
										+ '.cp-slidein .' + btn_style + '.cp-submit:active {background: '+c_normal+'!important;top: 6px; box-shadow: 0 0px ' + c_hover + ';} '
										+ '</style>');
			break;
		case 'cp-btn-outline': 		jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-slidein .' + btn_style + '.cp-submit { background: transparent!important;border: 2px solid ' + c_normal + ';color: inherit ;' + shadow + radius + '}'
										+ '.cp-slidein .' + btn_style + '.cp-submit:hover { background: ' + c_hover + '!important;border: 2px solid ' + c_hover + ';color: ' + button_txt_hover_color + ' ;' + '}'
										+ '.cp-slidein .' + btn_style + '.cp-submit:hover span { color: inherit !important ; } '
										+ '</style>');
			break;
		case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
									jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-slidein .' + btn_style + '.cp-submit {'
										+ '     border: none ;'
										+ 		shadow + radius
										+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '}'
										+ '.cp-slidein .' + btn_style + '.cp-submit:hover {'
										+ '     background: ' + c_normal + ' !important;'
										+ '}'
										+ '</style>');
			break;
	}

	//	Set either 10% darken color for 'HOVER'
	//	Or 0.10% darken color for 'GRADIENT'
	jQuery('#smile_button_bg_hover_color', window.parent.document).val( c_hover );
	jQuery('#smile_button_bg_gradient_color', window.parent.document).val( light );

	// name field style
	cp_name.attr("value", name_text);
	cp_name.attr("placeholder", name_text);
	jQuery(".cp-name-form").removeClass('cp_big_name');	

	if ( placeholder_font == '' ) {
		placeholder_font = 'inherit';
	}

	cp_input.css({ 
		"background-color":input_bg_color,
		"border-color":input_border_color,
		"color":placeholder_color,
		"font-family": placeholder_font
	});

	//to align text of form at center
	if( ( btn_disp_next_line == 1 ) || ( namefield == 1 ) ) {		
		cp_form_container.addClass('cp-center-align-text');
	} else {
		cp_form_container.removeClass('cp-center-align-text');
	}
	
	apply_boxshaddow(input_border_color);

}

// setup editors
function cp_editor_setup(data) {

	var slidein_title 				= data.slidein_title1,
	cp_title 						= jQuery(".cp-title"),
	slidein_title_color		 		= data.slidein_title_color,
	slidein_short_desc 				= data.slidein_short_desc1,
	cp_description 					= jQuery(".cp-description"),
	slidein_confidential			= data.slidein_confidential,
	button_title					= data.button_title,
	tip_color				 		= data.tip_color,
	slidein_desc_color		  		= data.slidein_desc_color,
	cp_confidential 				= jQuery(".cp-info-container"),
	cp_submit 						= jQuery(".cp-submit"),
	slidein_content					= data.slidein_content,
	cp_desc_bottom 					= jQuery(".cp-desc-bottom"),
	slide_button_title 				= data.slide_button_title,
	cp_slide_edit_btn				= jQuery(".cp-slide-edit-btn"),
	style_id 						= data.style_id,
	varient_style_id 				= data.variant_style_id,
	cp_slidein_content 				= jQuery(".cp-slidein-popup-container");

	if( varient_style_id !==''  && typeof varient_style_id !== 'undefined' ){
		style_id = varient_style_id;
	}
		
	//add style id as class to container
	cp_slidein_content.addClass( style_id );	

	// slide in title editor
	slidein_title = htmlEntities(slidein_title);
	cp_title.html(slidein_title);
	if(jQuery("#main_title_editor").length !== 0) {
		CKEDITOR.instances.main_title_editor.setData(slidein_title);
	}
	cp_title.css('color',slidein_title_color);

	// secondary title editor	
	if( jQuery("#sec_title_editor").length !== 0 ) {
		sec_title = data.slidein_sec_title;
		slidein_sec_title_color = data.slidein_sec_title_color;
		CKEDITOR.instances.sec_title_editor.setData(sec_title);
		slidein_sec_title = htmlEntities(sec_title);
		jQuery(".cp-sec-title").html(slidein_sec_title);
		jQuery(".cp-sec-title").css('color',slidein_sec_title_color);
	}	
	
	// short description editor
	slidein_short_desc = htmlEntities(slidein_short_desc);
	cp_description.html(slidein_short_desc);
	if(jQuery("#desc_editor").length !== 0) {
		if( slidein_short_desc !== "" && typeof slidein_short_desc !== "undefined" ){
			CKEDITOR.instances.desc_editor.setData(slidein_short_desc);
		}
	}
	cp_description.css('color',slidein_desc_color);

	// confidential info editor
	slidein_confidential = htmlEntities(slidein_confidential);
	cp_confidential.html(slidein_confidential);
	if(jQuery("#info_editor").length !== 0) {
		if( slidein_confidential !== "" && typeof slidein_confidential !== "undefined" && jQuery("#info_editor").length !== 0 ){
			CKEDITOR.instances.info_editor.setData(slidein_confidential);
		}
	}

	jQuery(".cp-info-container").css('color',tip_color);

	//description bottom
	slidein_content = htmlEntities(slidein_content);
	cp_desc_bottom.html(slidein_content);
	if(jQuery("#description_bottom").length !== 0) {
		CKEDITOR.instances.description_bottom.setData(slidein_content);
	} 

	//slide in  button editor
	slide_button_title = htmlEntities(slide_button_title);	
	cp_slide_edit_btn.html(slide_button_title);
}

//background image 
function cp_bg_image(data) {

	var bg_repeat 	= "";
	var bg_pos 		= "";
	var bg_size 	= "";

	var opt_bg					= data.opt_bg,
		cp_slidein_content		= jQuery(".cp-slidein-content"),
		cp_slidein_body			= jQuery(".cp-slidein-body"),
		slidein_bg_image		= data.slidein_bg_image,
		slidein_bg_image_size	= data.slidein_bg_image_size,
		cp_md_overlay       	= jQuery(".cp-slidein-body"),
		cp_slidein_body_inner	= jQuery(".cp-slidein-body-inner"),
		cp_si_overlay       	= jQuery(".cp-slidein-body-overlay"),
		bg_color				= data.slidein_bg_color,
		slidein_bg_gradient 	= data.slidein_bg_gradient,
		opt_bg 					= opt_bg.split("|"),
		bg_repeat 				= opt_bg[0],
		bg_pos 					= opt_bg[1],
		bg_size 				= opt_bg[2];

	/**
	 * 	Background - (Background Color / Gradient)
	 * 	
	 */
	var slidelightbg = lighterColor( bg_color, .3 );
	var slide_bg_style ='';
	
	//	Append all CSS
	jQuery('#cp-slidein-bg-css').remove();
	jQuery('head').append('<div id="cp-slidein-bg-css"></div>');
	if( typeof slidein_bg_gradient != 'undefined' && slidein_bg_gradient == '1' ) {

		//	store it!
		jQuery('#smile_slidein_bg_gradient_lighten', window.parent.document ).val( slidelightbg );
		
		slide_bg_style +=  '.cp-slidein-body-overlay {'
					+ '     background: -webkit-linear-gradient(' + slidelightbg + ', ' + bg_color + ');'
					+ '     background: -o-linear-gradient(' + slidelightbg + ', ' + bg_color + ');'
					+ '     background: -moz-linear-gradient(' + slidelightbg + ', ' + bg_color + ');'
					+ '     background: linear-gradient(' + slidelightbg + ', ' + bg_color + ');'
					+ '}';
	} else {
		slide_bg_style +=  '.cp-slidein-body-overlay {'
					+ '     background: ' + bg_color
					+ '}';
	}
		
	//	Append ALL CSS
	
	jQuery('#cp-slidein-bg-css').html('<style>' + slide_bg_style + '</style>');

	if( slidein_bg_image !== "" ) {
		var img_data = {action:'cp_get_image',img_id:slidein_bg_image,size:slidein_bg_image_size};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: "POST",
			success: function(img){
				jQuery(document).trigger("cp_ajax_loaded",[data]);
				
				cp_slidein_content.css({
					"background-color" : "",
					"bacround-image" : ""
				});	
				cp_slidein_body.css({
					"background-image"    : 'url('+img+')',
					"background-repeat"   : bg_repeat,
					"background-position" : bg_pos,
					"background-size"     : bg_size
				});
				
			}
		});
	} else {
		cp_slidein_body.css("background-image",'');
		cp_md_overlay.css("background-color",bg_color);		
	}
}

//decode html char
function escapeHtml(text) {
    var decoded = jQuery('<div/>').html(text).text();
    return decoded;
}

//trigger after ajax sucess 
jQuery(document).on("cp_ajax_loaded", function(e,data){
	// do your stuff here.
	cp_tooltip_reinitialize(data);
});


// This function set slidein width 
function cp_slidein_width_settings(data) {

	var cp_slidein        = jQuery(".cp-slidein"),
		cp_slidein_width	= data.cp_slidein_width,
		cp_slidein_body	= jQuery(".cp-slidein-body");
	
	cp_slidein.css({'max-width':cp_slidein_width+'px'});
	cp_slidein_body.css( 'max-width', '' );
	jQuery(".cp_fs_overlay").css({"display":"none"});
	jQuery(".cp_cs_overlay").css({"display":"block"});
}


/**
 * Adds blinking cursor
 * @param container  ( HTML container class for cursor)
 * @param bgcolor ( background color for cursor ) 
 */
function cp_blinking_cursor(container,bgcolor) {
	setTimeout(function() {	
		if( jQuery(container).find('.blinking-cursor').length == 0 ) { 		
			var font_size = parseInt(jQuery(container).data('font-size')) + 2;
			var fontArray = Array();
			if( jQuery(container+' span.cp_font').length ) {

				jQuery(container + " span.cp_font").each(function(){
					fontArray.push( parseInt( jQuery(this).data('font-size') ) );
				});	

				var maxFontSize = Math.max.apply(Math,fontArray);
				font_size = maxFontSize + 2;
			}	

			jQuery(container).append('<i style="background-color:'+bgcolor+';font-size: '+font_size+'px !important;" class="blinking-cursor">|</i>');
		}
	}, 500);
}


function slide_button_setting(data){

	//	Append all CSS
	jQuery('head').append('<div id="cp-slide-button-inline-css"></div>');

	var slide_in_style					= '',
		side_btn_style 					= data.side_btn_style,
	    slidein_btn_position 			= data.slidein_btn_position,
	    slidein_position 				= data.slidein_position,
	    slide_button_title 				= data.slide_button_title,
	    side_button_bg_color 			= data.side_button_bg_color,
	    side_button_txt_hover_color   	= data.side_button_txt_hover_color,
	    side_button_bg_hover_color	  	= data.side_button_bg_hover_color,
	    side_button_bg_gradient_color 	= data.side_button_bg_gradient_color,
	    side_btn_border_radius 			= data.side_btn_border_radius,
	    side_btn_shadow 				= data.side_btn_shadow,
	    close_slidein 					= data.close_slidein,
	    button_animation				= data.button_animation,
	    hide_button_class				= '',
	    cp_animate_container 			= jQuery(".cp-animate-container"),
	    slidein_overlay 				= jQuery(".slidein-overlay"),
	    cp_slide_edit_btn				= jQuery(".cp-slide-edit-btn"),
	    cp_toggle_container 			=jQuery(".cp-toggle-container"),
	    slide_button_text_color 		= data.slide_button_text_color,
	    side_btn_gradient 				= data.side_btn_gradient,
	    toggle_btn						= data.toggle_btn,
	    toggle_btn_visible  			= data.toggle_btn_visible,

	    //	Toggle Button
		toggle_button_font 				= data.toggle_button_font;


	/**
	 * 	Toggle Button
	 */
	var font = 'sans-serif';
	if( toggle_button_font ) {
		font =  toggle_button_font + ',' + font;
	}

	var font_style ='';
	font_style = '     font-family: ' + font + ';';
				

    var name = 'cp-unsaved-changes';
  	var is_cookie = '';
  	var nameEQ = name + '=';
  	var ca = document.cookie.split(';');
  	for ( var i = 0; i < ca.length; i++ ) {
		var c = ca[i];
		while ( c.charAt(0) == ' ' ) {
			c = c.substring(1, c.length);
		}
		if ( c.indexOf(nameEQ) == 0 ) {
			is_cookie =  c.substring(nameEQ.length, c.length);
		}
  	}

  	//	Disable the toggle button
  	if( close_slidein === 'do_not_close' ) {
		toggle_btn = 0;
  	}

  	if( toggle_btn == 1 ){
		slidein_overlay.removeClass('cp-slide-without-toggle');
	} else {
	    slidein_overlay.addClass('cp-slide-without-toggle');
	    slidein_overlay.removeClass('cp-slidein-click'); 
	}
	
	if(!cp_animate_container.hasClass('cp-hide-slide')){
		hide_button_class ='cp-slide-hide-btn';
	}
	
	if( slidein_position == 'center-right' ){ 
	   button_animation = 'smile-slideInUp'; 
	}
 
	if( slidein_position == 'center-left' ){
		button_animation = 'smile-slideInDown';
	}
 	
	if( slidein_position == 'top-left' || slidein_position == 'top-center' || slidein_position == 'top-right' ){
		button_animation = 'smile-slideInDown';
	}

	if( slidein_position == 'bottom-left' || slidein_position == 'bottom-center' || slidein_position == 'bottom-right' ){
		button_animation = 'smile-slideInUp';
	}

	jQuery('#smile_button_animation', window.parent.document).val( button_animation );
	
	// button animation
	cp_slide_edit_btn.removeAttr('class');
	cp_slide_edit_btn.attr('class','cp-slide-edit-btn smile-animated '+button_animation +' ');
	cp_toggle_container.addClass(hide_button_class);

	if( toggle_btn == 1 && toggle_btn_visible == 1 ) {
		if( !jQuery(".slidein-overlay").hasClass('cp-slide-without-toggle') ) {
		var cp_toggle_container    = jQuery(".cp-toggle-container"),
			exitanimation 		 = jQuery(".cp-animate-container").data("exit-animation"),
			cp_animate_container = jQuery(".cp-animate-container");
	 
		cp_animate_container.attr('class', 'cp-animate-container');                                      
        cp_animate_container.attr('class' , 'cp-animate-container smile-animated '+exitanimation);
        cp_animate_container.addClass("cp-hide-slide");
		cp_toggle_container.removeClass("cp-slide-hide-btn");
		cp_animate_container.removeClass('exitanimation');
		} else {
			e.stopPropagation();
		}
	} else {
		if( !jQuery(".cp-toggle-container").hasClass('cp-slide-hide-btn') ) {
		jQuery(".cp-toggle-container").addClass("cp-slide-hide-btn");
		parent.setFocusElement('slide_button_title'); 
		var	cp_animate_container = jQuery(".cp-animate-container"),
			entryanimation       = cp_animate_container.data("entry-animation");

			cp_animate_container.attr('class', 'cp-animate-container cp-hide-slide smile-animated');
		
			setTimeout(function() {	
			 cp_animate_container.attr('class' , 'cp-animate-container smile-animated '+entryanimation);	
			}, 10);
		}
	}

	if( side_btn_gradient == 1 ) {
    	side_btn_style = 'cp-btn-gradient';
    } else {
    	side_btn_style = 'cp-btn-flat';
    }

    //set button style
	jQuery('#smile_side_btn_style', window.parent.document).val( side_btn_style );

	//	button style
	var slideclassList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
	jQuery.each(slideclassList, function(i, v){
       cp_slide_edit_btn.removeClass(v);
    });
  
    cp_slide_edit_btn.addClass(side_btn_style);	

	// button position
	var positionclassList = ['slidein-top-left','slidein-top-center','slidein-top-right','slidein-bottom-left','slidein-bottom-center','slidein-bottom-right','slidein-center-left','slidein-center-right'];
	jQuery.each(positionclassList, function(i, v){		
       cp_toggle_container.removeClass(v);
    });
	cp_toggle_container.addClass( 'slidein-'+slidein_position );

	var c_normal 	= side_button_bg_color;
	var c_hover  	= darkerColor( side_button_bg_color, .05 );
	var light 		= lighterColor( side_button_bg_color, .3 );

	cp_slide_edit_btn.css('background', c_normal);
	cp_slide_edit_btn.css('color', slide_button_text_color);

	//	Apply box shadow to submit button - If its set & equals to - 1
	var shadow = radius = '';
	if( side_btn_shadow == 1 ) {		
		shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
	}
	//	Add - border-radius
	if( side_btn_border_radius != '' ) {
		radius += 'border-radius: ' + side_btn_border_radius + 'px;';
	}	

	jQuery("#cp-slide-button-inline-css").remove();

	jQuery('head').append('<div id="cp-slide-button-inline-css"></div>');
	
	switch( side_btn_style ) {
		case 'cp-btn-flat': 		jQuery('#cp-slide-button-inline-css').html('<style>'
										+ '.slidein-overlay .cp-slide-edit-btn.' + side_btn_style + '{ background: '+c_normal+'!important;' + shadow + radius + font_style +'; } '
										+ '.cp-slidein .cp-slide-edit-btn.' + side_btn_style + ':hover { background: '+c_hover+'!important; } '
										+ '</style>');
			break;		
		
		case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
									jQuery('#cp-slide-button-inline-css').html('<style>'
										+ '.slidein-overlay .cp-slide-edit-btn.' + side_btn_style + ' {'
										+ '     border: none ;'
										+ 		shadow + radius + font_style
										+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '}'
										+ '.slidein-overlay .cp-slide-edit-btn.' + side_btn_style + ':hover {'
										+ '     background: ' + c_normal + ' !important;'
										+ '}'
										+ '</style>');
			break;
	}

	//	Set either 10% darken color for 'HOVER'
	//	Or 0.10% darken color for 'GRADIENT'
	jQuery('#smile_side_button_bg_hover_color', window.parent.document).val( c_hover );
	jQuery('#smile_side_button_bg_gradient_color', window.parent.document).val( light );

}

// Add class to body for Slide In position 
function cp_add_class_for_body(bodyclass) {
	jQuery("body").removeClass('cp-slidein-top-center cp-slidein-bottom-center cp-slidein-center-left cp-slidein-center-right cp-slidein-top-left cp-slidein-bottom-right cp-slidein-bottom-left cp-slidein-top-right');
	jQuery('body').addClass('cp-slidein-'+bodyclass);
}