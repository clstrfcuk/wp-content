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

	if( jQuery("#main_title_editor").length ) {

		var sel_main_title_editor = jQuery("#main_title_editor");

		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'main_title_editor' );

		//	Initially set show CKEditor of 'cp-title'
		//	Ref: http://docs.ckeditor.com/#!/api/CKEDITOR.focusManager
		var focusManager = new CKEDITOR.focusManager( CKEDITOR.instances.main_title_editor );
		focusManager.focus();

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.main_title_editor.on('instanceReady',function(){
			var data = CKEDITOR.instances.main_title_editor.getData();
			cp_set_no_responsive( sel_main_title_editor, data );
		});

		CKEDITOR.instances.main_title_editor.on( 'change', function() {

			// Remove Blinking cursor
			jQuery(".cp-modal-body").find(".blinking-cursor").remove();

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.main_title_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_modal_title1');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_main_title_editor, data );

		} );
	}

	if( jQuery("#sec_title_editor").length ) {

		var sel_sec_title_editor = jQuery("#sec_title_editor");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'sec_title_editor' );

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.sec_title_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.sec_title_editor.getData();
			cp_set_no_responsive( sel_sec_title_editor, data );
		});

		CKEDITOR.instances.sec_title_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.sec_title_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_modal_sec_title');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_sec_title_editor, data );

		} );
	}


	if( jQuery("#desc_editor").length ) {

		var sel_desc_editor = jQuery("#desc_editor");


		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'desc_editor' );
		CKEDITOR.instances.desc_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.desc_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.desc_editor.getData();
			cp_set_no_responsive( sel_desc_editor, data );
		});

		CKEDITOR.instances.desc_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.desc_editor.getData();
			parent.updateHTML(data,'smile_modal_short_desc1');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_desc_editor, data );

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
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.info_editor.getData();
			parent.updateHTML(data,'smile_modal_confidential');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_info_editor, data );

		} );
	}

	if( jQuery("#short_desc_editor").length ) {

		var sel_short_desc_editor = jQuery("#short_desc_editor");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'short_desc_editor' );
		CKEDITOR.instances.short_desc_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.short_desc_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.short_desc_editor.getData();
			cp_set_no_responsive( sel_short_desc_editor, data );
		});

		CKEDITOR.instances.short_desc_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.short_desc_editor.getData();
			parent.updateHTML(data,'smile_modal_content');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_short_desc_editor, data );

		} );
	}

	if( jQuery("#cp_button_editor").length ) {

		var sel_cp_button_editor = jQuery("#cp_button_editor");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'cp_button_editor' );
		CKEDITOR.instances.cp_button_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.cp_button_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.cp_button_editor.getData();
			cp_set_no_responsive( sel_cp_button_editor, data );
		});

		CKEDITOR.instances.cp_button_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.cp_button_editor.getData();
			parent.updateHTML(data,'smile_button_title');
			var test = jQuery("#cp_button_editor").html();
			jQuery(document).trigger('button_transform' , [test]);

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_cp_button_editor, data );

		} );
	}

	if( jQuery("#afl_editor").length ) {

		var sel_afl_editor = jQuery("#afl_editor");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'afl_editor' );
		CKEDITOR.instances.afl_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.afl_editor.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.afl_editor.getData();
			cp_set_no_responsive( sel_afl_editor, data );
		});

		CKEDITOR.instances.afl_editor.on( 'change', function() {

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.afl_editor.getData();
			parent.updateHTML(data,'smile_affiliate_title');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_afl_editor, data );

		} );
	}

	if( jQuery("#description_bottom").length ) {

		var sel_description_bottom = jQuery("#description_bottom");

		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'description_bottom' );

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.description_bottom.on('instanceReady',function(){
		   	var data = CKEDITOR.instances.description_bottom.getData();
			cp_set_no_responsive( sel_description_bottom, data );
		});

		//CKEDITOR.instances.description_bottom.config.toolbar = 'Small';
		CKEDITOR.instances.description_bottom.on( 'change', function() {


			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
        	cp_color_for_list_tag();

			var data = CKEDITOR.instances.description_bottom.getData();
			parent.updateHTML(data,'smile_modal_content');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive( sel_description_bottom, data );

		} );
	}

	parent.setFocusElement("style_title");
	jQuery("body").on("click", ".cp-image", function(e){ parent.setFocusElement('modal_image'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-submit", function(e){ parent.setFocusElement('btn_style'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-modal-body", function(e){ parent.setFocusElement('modal_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-overlay", function(e) { parent.setFocusElement('modal_overlay_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-email", function(e){ parent.setFocusElement('placeholder_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-name", function(e){ parent.setFocusElement('name_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-overlay-close", function(e){ parent.setFocusElement('close_modal'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-affilate-link", function(e){ parent.setFocusElement('affiliate_username'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-affilate", function(e){ parent.setFocusElement('affiliate_username'); e.stopPropagation(); });

	// remove blinking cursor
	jQuery("body").on("click select", ".cp-highlight,.cp-name,.cp-email", function(e){
		jQuery(".cp-modal-body").find(".blinking-cursor").remove();
	});

	// Preventing links and form navigation in an iframe
	jQuery('a').click(function(e){
		e.preventDefault();
	});
	jQuery('button').click(function(e){
		e.preventDefault();
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

	// modal title
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

function cp_affilate_settings(data){

	var	cp_affilate 			= jQuery(".cp-affilate"),
		cp_affilate_link 		= jQuery(".cp-affilate-link");

	var	affiliate_setting 		= data.affiliate_setting,
		modal_size				= data.modal_size,
		affiliate_title     	= data.affiliate_title,
		affiliate_username      = data.affiliate_username;

	//affiliate link settings
	var crlink = '';
	if( affiliate_setting == '1' ) {
		cp_affilate_link.css({"display":"inline-block"});
		if( affiliate_username != '' ){
			crlink = 'http://themeforest.net/user/brainstormforce/portfolio?ref='+affiliate_username;
		} else {
			crlink = 'http://themeforest.net/user/brainstormforce/portfolio?ref=BrainstormForce';
		}
	} else {
		cp_affilate_link.css({"display":"none"});
	}

	affiliate_title = htmlEntities(affiliate_title);
	cp_affilate_link.find("a").html(affiliate_title);
	if( affiliate_title !== "" && typeof affiliate_title !== "undefined" ){
		CKEDITOR.instances.afl_editor.setData(affiliate_title);
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
		close_modal_tooltip 	= data.close_modal_tooltip,
		modal_size		  		= data.modal_size,
		close_modal	      		= data.close_modal,
		close_img	      		= data.close_img,
		close_txt		  		= data.close_txt,
		overlay_close	  		= jQuery(".cp-overlay-close"),
		close_position    		= data.close_position,
		cp_modal		  		= jQuery(".cp-modal"),
		cp_animate_container 	= jQuery(".cp-animate-container");
		close_text_color  		= data.close_text_color,
		modal_overlay     		= jQuery(".cp-overlay"),
		close_img_size			= data.close_img_size,
		cp_close_image_width    = data.cp_close_image_width;

	var close_img_default = close_img;
	if( close_position == 'adj_modal' ) {
		if( close_modal != 'close_txt' ) {
			overlay_close.appendTo(cp_animate_container);
		} else {
			overlay_close.appendTo(modal_overlay);
		}
		overlay_close.removeClass('cp-inside-close').addClass('cp-adjacent-close');
	} else if( close_position == 'inside_modal' ){
		if( close_modal != 'close_txt' ) {
			overlay_close.appendTo(cp_animate_container);
			overlay_close.addClass('cp-inside-close').removeClass('cp-adjacent-close');
		} else {
			overlay_close.appendTo(modal_overlay);
		}
	} else {
		overlay_close.appendTo(modal_overlay);
		overlay_close.removeClass('cp-inside-close cp-adjacent-close');
	}

	if( close_modal_tooltip == '1' ){
		var psid = modal_overlay.data('ps-id');
			if( close_position == 'adj_modal' ){
				if( modal_size !== 'cp-modal-custom-size' ){
					tip_position = "left";
					offset_position = 45;
				} else {
					tip_position = "left";
					offset_position = 35;
				}
				tooltip_class = '';
			} else {
				tooltip_class = 'cp-custom-tooltip';
				tip_position = "left";
				offset_position = 30;
			}

			if( modal_size !== 'cp-modal-custom-size' ){
				innerclass = 'cp-innertip';
			}
			jQuery('.has-tip:empty').remove();
	} else {
		jQuery('.has-tip:empty').remove();
	}

	close_tooltip = '<span class="'+tooltip_class+' cp-tooltip-icon has-tip cp-tipcontent-'+psid+'" data-classes="close-tip-content-'+psid+'" data-position="'+tip_position+'"  title="'+tooltip_title+'" data-original-title ="'+tooltip_title+'" data-color="'+tooltip_title_color+'" data-bgcolor="'+tooltip_background+'" data-closeid ="cp-tipcontent-'+psid+'" data-offset="'+offset_position+'" >';
	close_tooltip_end ='</span>';

	if( close_modal == "close_icon" ){
		jQuery(".cp-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
		overlay_close.html('<i class="'+close_icon+'"></i>');
		overlay_close.css({"color":close_text_color});
	} else if( close_modal == "close_txt" ){
		jQuery(".cp-overlay-close").removeClass('cp-image-close cp-adjacent-close cp-inside-close').addClass('cp-text-close');
		overlay_close.html(close_tooltip+'<span class ="close-txt">'+close_txt+'</span>'+close_tooltip_end);
		overlay_close.css({"color":close_text_color});
	} else if(close_img_default.indexOf('http') === -1){
			if( close_modal == "close_img" && close_img !== "" ) {
				jQuery(".cp-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
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
				jQuery(".cp-overlay-close").removeClass('cp-text-close cp-imnage-close');
				overlay_close.html('');
			}
	} else if( close_modal == "do_not_close") {
		jQuery(".cp-overlay-close").removeClass('cp-text-close cp-imnage-close');
		overlay_close.html('');
	} else if( close_img_default.indexOf('http') != -1 ) {
		close_img_full_src = close_img.split('|');
		close_img_src = close_img_full_src[0];
		jQuery(".cp-overlay-close").removeClass('cp-text-close').addClass('cp-image-close');
		overlay_close.html(close_tooltip+'<img class="cp-default-close" src="'+close_img_src+'" />'+close_tooltip_end);
	}

	overlay_close.css('background',"transparent");

	if( close_modal == "do_not_close") {
		overlay_close.css('background',"none");
	}

	if( close_modal != 'close_txt' )
		overlay_close.css( 'width', cp_close_image_width+'px' );
	else
		overlay_close.css( 'width', 'auto' );
}


// function to reinitialize tooltip
function cp_tooltip_reinitialize(data) {

	var close_modal_tooltip 	 	= data.close_modal_tooltip,
		modal_size				    = data.modal_size,
		cp_overlay_close            = jQuery(".cp-overlay-close"),
		cp_modal_width			    = data.cp_modal_width,
		modal_overlay     			= jQuery(".cp-overlay"),
		innerclass                  = '',
		tooltip_background 			= data.tooltip_background,
		tooltip_title_color         = data.tooltip_title_color,
		close_position    			= data.close_position,
		psid 						= modal_overlay.data('ps-id'),
		cp_overlay_close     		= jQuery(".cp-overlay-close"),
		modalht  					= jQuery(".cp-modal-content").outerHeight();

	//tool tip for modal close
	if( close_modal_tooltip == '1' ){
        var tooltip_classname = "cp-tipcontent-"+psid;
        var tclass = "close-tip-content-"+psid;
        var vw = jQuery(window).width();

		if( modal_size == 'cp-modal-window-size' ){
			jQuery(".has-tip").data("position" ,"left");
	        tip_position = "left";
		} else {

			if( cp_modal_width > 768 ){
                jQuery(".has-tip").data("position" ,"left");
                tip_position = "left";
            } else {
            	if( close_position == 'out_modal' || close_position == 'adj_modal' ){
            		jQuery(".has-tip").data("position" ,"left");
                	tip_position = "left";
            	}else{
            		jQuery(".has-tip").data("position" ,"top");
                	tip_position = "top";
            	}
            }
		}
			if(modalht >= 500){
               jQuery(".has-tip").data("position" ,"left");
                tip_position = "left";
            }

		if(cp_overlay_close.hasClass('cp-text-close')){
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

// modal image related settings
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

// function to reinitialize affilate
function cp_affilate_reinitialize(data){
	var affiliate_setting = data.affiliate_setting;
	set_affiliate_link(affiliate_setting);
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
		after_exit				= overlay_effect,
		cp_animate	   			= jQuery(".cp-animate-container");

	if( disable_overlay_effect == 1 ){
		var vw = jQuery(window).width();
		if( vw <= hide_animation_width ){
			overlay_effect = exit_animation = 'cp-overlay-none';
		}
	} else {
		cp_animate.removeClass('cp-overlay-none');
	}

	var entry_anim = ( typeof cp_animate.attr("data-entry-animation") !== "undefined" ) ? cp_animate.attr("data-entry-animation") : '';
	var exit_anim = ( typeof cp_animate.attr("data-exit-animation") !== "undefined" ) ? cp_animate.attr("data-exit-animation") : '';

	cp_animate.removeClass('smile-animated');

	if( !cp_animate.hasClass(exit_animation) && exit_animation !== exit_anim ){
		cp_animate.attr('data-exit-animation', exit_animation );
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
		cp_form_container 			= jQuery(".cp-form-container");

	jQuery(".cp-modal-popup-container").addClass( data.style_id )

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

	jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
	switch( btn_style ) {
		case 'cp-btn-flat': 		jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-modal .' + btn_style + '.cp-submit{ background: '+c_normal+'!important;' + shadow + radius + '; } '
										+ '.cp-modal .' + btn_style + '.cp-submit:hover { background: '+c_hover+'!important; } '
										+ '</style>');
			break;
		case 'cp-btn-3d': 			jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-modal .' + btn_style + '.cp-submit {background: '+c_normal+'!important; '+radius+' position: relative ; box-shadow: 0 6px ' + c_hover + ';} '
										+ '.cp-modal .' + btn_style + '.cp-submit:hover {background: '+c_normal+'!important;top: 2px; box-shadow: 0 4px ' + c_hover + ';} '
										+ '.cp-modal .' + btn_style + '.cp-submit:active {background: '+c_normal+'!important;top: 6px; box-shadow: 0 0px ' + c_hover + ';} '
										+ '</style>');
			break;
		case 'cp-btn-outline': 		jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-modal .' + btn_style + '.cp-submit { background: transparent!important;border: 2px solid ' + c_normal + ';color: inherit ;' + shadow + radius + '}'
										+ '.cp-modal .' + btn_style + '.cp-submit:hover { background: ' + c_hover + '!important;border: 2px solid ' + c_hover + ';color: ' + button_txt_hover_color + ' ;' + '}'
										+ '.cp-modal .' + btn_style + '.cp-submit:hover span { color: inherit !important ; } '
										+ '</style>');
			break;
		case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
									jQuery('#cp-temporary-inline-css').html('<style>'
										+ '.cp-modal .' + btn_style + '.cp-submit {'
										+ '     border: none ;'
										+ 		shadow + radius
										+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '}'
										+ '.cp-modal .' + btn_style + '.cp-submit:hover {'
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
	if( ( btn_disp_next_line == 1 ) || ( namefield == 1 ) ){
	 cp_form_container.addClass('cp-center-align-text');
	} else {
		cp_form_container.removeClass('cp-center-align-text');
	}

	apply_boxshaddow(input_border_color);
}

// setup editors
function cp_editor_setup(data) {

	var modal_title 				= data.modal_title1,
	cp_title 						= jQuery(".cp-title"),
	modal_title_color		 		= data.modal_title_color,
	modal_short_desc 				= data.modal_short_desc1,
	cp_description 					= jQuery(".cp-description"),
	modal_confidential				= data.modal_confidential,
	button_title					= data.button_title,
	tip_color				 		= data.tip_color,
	modal_desc_color		  		= data.modal_desc_color,
	cp_confidential 				= jQuery(".cp-info-container"),
	cp_submit 						= jQuery(".cp-submit"),
	modal_content					= data.modal_content,
	cp_desc_bottom 					= jQuery(".cp-desc-bottom"),
	style_id 						= data.style_id,
	varient_style_id 				= data.variant_style_id,
	cp_modal_popup_container 		= jQuery(".cp-modal-popup-container");

	if( varient_style_id !=='' && typeof varient_style_id !== 'undefined' ){
		style_id = varient_style_id;
	}
	//add style id as class to container
	cp_modal_popup_container.addClass(style_id);

	// modal title editor
	modal_title = htmlEntities(modal_title);
	cp_title.html(modal_title);
	if( jQuery("#main_title_editor").length ) {
		CKEDITOR.instances.main_title_editor.setData(modal_title);
	}
	cp_title.css('color',modal_title_color);

	// secondary title editor
	if( jQuery("#sec_title_editor").length ) {
		sec_title = data.modal_sec_title;
		modal_sec_title_color = data.modal_sec_title_color;

		modal_sec_title = htmlEntities(sec_title);
		jQuery(".cp-sec-title").html(modal_sec_title);
		CKEDITOR.instances.sec_title_editor.setData(modal_sec_title);
		jQuery(".cp-sec-title").css('color',modal_sec_title_color);
	}

	// short description editor
	modal_short_desc = htmlEntities(modal_short_desc);
	cp_description.html(modal_short_desc);
	if( jQuery("#desc_editor").length ) {
		if( modal_short_desc !== "" && typeof modal_short_desc !== "undefined" ){
			CKEDITOR.instances.desc_editor.setData(modal_short_desc);
		}
	}
	cp_description.css('color',modal_desc_color);

	// confidential info editor
	modal_confidential = htmlEntities(modal_confidential);
	cp_confidential.html(modal_confidential);
	if( modal_confidential !== "" && typeof modal_confidential !== "undefined" && jQuery("#info_editor").length ){

		CKEDITOR.instances.info_editor.setData(modal_confidential);
	}

	//submit button editor
	button_title = htmlEntities(button_title);
	cp_submit.html(button_title);
	if( button_title !== "" && typeof button_title !== "undefined" && jQuery("#cp_button_editor").length ){

		CKEDITOR.instances.cp_button_editor.setData(button_title);
	}
	jQuery(".cp-info-container").css('color',tip_color);


	//description bottom
	modal_content = htmlEntities(modal_content);
	cp_desc_bottom.html(modal_content);
	if( jQuery("#description_bottom").length ) {
		CKEDITOR.instances.description_bottom.setData(modal_content);
	}


}

//background image
function cp_bg_image(data) {

	var bg_repeat 	= "";
	var bg_pos 		= "";
	var bg_size 	= "";

	var opt_bg					= data.opt_bg,
		cp_modal_content		= jQuery(".cp-modal-content"),
		cp_modal_body			= jQuery(".cp-modal-body"),
		modal_bg_image			= data.modal_bg_image,
		modal_bg_image_size		= data.modal_bg_image_size,
		modal_size				= data.modal_size,
		cp_md_overlay       	= jQuery(".cp-modal-body-overlay"),
		bg_color				= data.modal_bg_color,
		opt_bg 					= opt_bg.split("|"),
		bg_repeat 				= opt_bg[0],
		bg_pos 					= opt_bg[1],
		bg_size 				= opt_bg[2];

	cp_md_overlay.css( "background-color", bg_color );
	if( modal_bg_image !== "" ) {
		var img_data = {action:'cp_get_image',img_id:modal_bg_image,size:modal_bg_image_size};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: "POST",
			success: function(img){
				jQuery(document).trigger("cp_ajax_loaded",[data]);
				if( modal_size == 'cp-modal-custom-size' ){

					cp_modal_content.css({
						"background-color" : "",
						"bacround-image" : ""
					});
					cp_modal_body.css({
						"background-image"    : 'url('+img+')',
						"background-repeat"   : bg_repeat,
						"background-position" : bg_pos,
						"background-size"     : bg_size
					});
				} else {

					cp_modal_body.css({
						"background-color"    : "",
						"background-image"    : "",
						"background-repeat"   : "",
						"background-position" : "",
						"background-size"     : ""
					});
					cp_modal_content.css({
						"background-image"    : 'url('+img+')',
						"background-repeat"   : bg_repeat,
						"background-position" : bg_pos,
						"background-size"     : bg_size
					});
				}
			}
		});
	} else {
		if( modal_size == 'cp-modal-custom-size' ){
			cp_modal_body.css("background-image",'');
			cp_md_overlay.css("background-color",bg_color);
		} else {
			cp_modal_content.css("background-image",'');
			cp_md_overlay.css("background-color",bg_color);
		}
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


// This function set modal width
function cp_modal_width_settings(data) {

	var modal_size      = data.modal_size,
		cp_modal        = jQuery(".cp-modal"),
		cp_modal_width	= data.cp_modal_width,
		cp_modal_body	= jQuery(".cp-modal-body");

	if( modal_size == 'cp-modal-custom-size' ){
		cp_modal.css({'max-width':cp_modal_width+'px','width':'100%'});
		cp_modal_body.css( 'max-width', '' );
		cp_modal.removeClass('cp-modal-window-size');
		jQuery(".cp_fs_overlay").css({"display":"none"});
		jQuery(".cp_cs_overlay").css({"display":"block"});
	} else {

		//	Skip `YouTube` style form window Width
		if( !jQuery('.cp-modal-body').hasClass('cp-youtube') ) {
			cp_modal_body.css('max-width', cp_modal_width+'px' );
			cp_modal.css({'max-width':'auto','width':'auto'});
			cp_modal.removeClass('cp-modal-custom-size');
			jQuery(".cp_cs_overlay").css({"display":"none"});
			jQuery(".cp_fs_overlay").css({"display":"block"});
		}
	}
	cp_modal.addClass(modal_size);
}

/**
 * Adds blinking cursor
 * @param container  ( html container class for cursor)
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