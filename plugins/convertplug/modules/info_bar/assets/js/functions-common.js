(function(e){function t(){var e=document.createElement("p");var t=false;if(e.addEventListener)e.addEventListener("DOMAttrModified",function(){t=true},false);else if(e.attachEvent)e.attachEvent("onDOMAttrModified",function(){t=true});else return false;e.setAttribute("id","target");return t}function n(t,n){if(t){var r=this.data("attr-old-value");if(n.attributeName.indexOf("style")>=0){if(!r["style"])r["style"]={};var i=n.attributeName.split(".");n.attributeName=i[0];n.oldValue=r["style"][i[1]];n.newValue=i[1]+":"+this.prop("style")[e.camelCase(i[1])];r["style"][i[1]]=n.newValue}else{n.oldValue=r[n.attributeName];n.newValue=this.attr(n.attributeName);r[n.attributeName]=n.newValue}this.data("attr-old-value",r)}}var r=window.MutationObserver||window.WebKitMutationObserver;e.fn.attrchange=function(i){var s={trackValues:false,callback:e.noop};if(typeof i==="function"){s.callback=i}else{e.extend(s,i)}if(s.trackValues){e(this).each(function(t,n){var r={};for(var i,t=0,s=n.attributes,o=s.length;t<o;t++){i=s.item(t);r[i.nodeName]=i.value}e(this).data("attr-old-value",r)})}if(r){var o={subtree:false,attributes:true,attributeOldValue:s.trackValues};var u=new r(function(t){t.forEach(function(t){var n=t.target;if(s.trackValues){t.newValue=e(n).attr(t.attributeName)}s.callback.call(n,t)})});return this.each(function(){u.observe(this,o)})}else if(t()){return this.on("DOMAttrModified",function(e){if(e.originalEvent)e=e.originalEvent;e.attributeName=e.attrName;e.oldValue=e.prevValue;s.callback.call(this,e)})}else if("onpropertychange"in document.body){return this.on("propertychange",function(t){t.attributeName=window.event.propertyName;n.call(e(this),s.trackValues,t);s.callback.call(this,t)})}return this}})(jQuery)

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

jQuery(window).load(function(){
	parent.customizerLoaded();
	jQuery("head").append('<style id="cp_input_css"></style>');
	jQuery(".cp-info-bar-container").appendTo("body");
	cp_color_for_list_tag();
});

var cp_empty_classes = {
		".cp-info-bar-msg" 				: ".cp-msg-container",
		".cp-info-bar-desc"             : ".cp-info-bar-desc-container",
		".cp-content-editor"  			: ".cp-content-container"
	};

jQuery(document).ready(function(){

	//call to cp_add_empty_class if html in empty
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

	if( jQuery("#title_editor").length !== 0 ) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'title_editor' );

		//	Initially set show CKEditor of 'cp-title'
		//	Ref: http://docs.ckeditor.com/#!/api/CKEDITOR.focusManager
		var focusManager = new CKEDITOR.focusManager( CKEDITOR.instances.title_editor );
		focusManager.focus();

		CKEDITOR.instances.title_editor.config.toolbar = 'Small';
		CKEDITOR.instances.title_editor.on( 'change', function() {
			// Remove Blinking cursor 
			jQuery(".cp-info-bar-container").find(".blinking-cursor").remove();
			
			var data = CKEDITOR.instances.title_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_infobar_title');
			cp_color_for_list_tag();
		} );
	}

	if(jQuery("#description_editor").length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline( 'description_editor' );
		CKEDITOR.instances.description_editor.config.toolbar = 'Small';
		CKEDITOR.instances.description_editor.on( 'change', function() {
			var data = CKEDITOR.instances.description_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_infobar_description');
			cp_color_for_list_tag();
		} );
	}

	if(jQuery("#button_editor").length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline( 'button_editor' );
		CKEDITOR.instances.button_editor.config.toolbar = 'Small';
		CKEDITOR.instances.button_editor.on( 'change', function() {
			var data = CKEDITOR.instances.button_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_button_title');
			cp_color_for_list_tag();
		} );
	}


	if(jQuery("#ib_content_editor").length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline( 'ib_content_editor' );
		CKEDITOR.instances.ib_content_editor.config.toolbar = 'Small';
		CKEDITOR.instances.ib_content_editor.on( 'change', function() {
			var data = CKEDITOR.instances.ib_content_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_info_bar_content');
			cp_color_for_list_tag();
		} );
	}

	//close infobar on click of toggle button
	jQuery("body").on("click", ".cp-ifb-toggle-btn", show_ifb ); 

	//close modal on click of button
	//jQuery("body").on("click", ".ib-close", function(e){
	jQuery("body").on("click", ".ib-close", close_ifb );

	jQuery("body").on("click", ".cp-ifb-toggle-btn", function(e){ parent.setFocusElement('toggle_button_title'); e.stopPropagation(); });

	//another submit button
	if(jQuery("#sec_button_editor").length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline( 'sec_button_editor' );
		CKEDITOR.instances.sec_button_editor.config.toolbar = 'Small';
		CKEDITOR.instances.sec_button_editor.on( 'change', function() {
			var data = CKEDITOR.instances.sec_button_editor.getData();
			parent.updateHTML(htmlEntities(data),'smile_ifb_button_title');
			cp_color_for_list_tag();
		} );
	}


	jQuery("body").on("click", ".cp-image", function(e){ parent.setFocusElement('infobar_image'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-email", function(e){ parent.setFocusElement('placeholder_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp_name", function(e){ parent.setFocusElement('name_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-info-bar", function(e){ parent.setFocusElement('bg_color'); });
	jQuery("body").on("click", "#button_editor", function(e){ parent.setFocusElement('button_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-name-field input", function(e){ parent.setFocusElement('name_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-email-field input", function(e){ parent.setFocusElement('placeholder_text'); e.stopPropagation(); });
	jQuery("body").on("click", ".ib-close", function(e){ parent.setFocusElement('close_img'); e.stopPropagation(); });
	jQuery("body").on("click", "#sec_button_editor", function(e){ parent.setFocusElement('ifb_button_bg_color'); e.stopPropagation(); });

	// remove blinking cursor
	jQuery("body").on("click select", ".cp-highlight,.cp-name,.cp-email", function(e){
		jQuery(".cp-info-bar-container").find(".blinking-cursor").remove();
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


	jQuery('#title_editor').attrchange({
        callback: function (e) {
			var cHeight = jQuery('.cp-info-bar').outerHeight();
			if( jQuery("body").hasClass("managePageDown") ){
				jQuery("body").stop().animate({'marginTop':cHeight+'px'},1000);
			}
		}
	});

	jQuery('#description_editor').attrchange({
        callback: function (e) {
			var cHeight = jQuery('.cp-info-bar').outerHeight();
			if( jQuery("body").hasClass("managePageDown") ){
				jQuery("body").stop().animate({'marginTop':cHeight+'px'},1000);
			}
		}
	});

	jQuery('#ib_content_editor').attrchange({
        callback: function (e) {
			var cHeight = jQuery('.cp-info-bar').outerHeight();
			if( jQuery("body").hasClass("managePageDown") ){
				jQuery("body").stop().animate({'marginTop':cHeight+'px'},1000);
			}
		}
	});
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

//for editor setup
function cp_editor_setup(data) {
	var style 						= data.style;
	var cp_submit 					= jQuery("#button_editor"),
		cp_title 					= jQuery("#title_editor"),
		description 				= data.infobar_description,
		ib_content_editor 			= jQuery("#ib_content_editor"),
		cp_info_bar_desc			= jQuery(".cp-info-bar-desc"),
		content						= data.info_bar_content,
	 	title						= data.infobar_title,
		button_title				= data.button_title,
		cp_second_submit_btn		= jQuery(".cp-second-submit-btn"),
		ifb_button_title			= data.ifb_button_title,
		style_id 					= data.style_id,
		varient_style_id 			= data.variant_style_id,
		cp_info_bar 				= jQuery(".cp-info-bar");
      
	if(varient_style_id !== '' && typeof varient_style_id !== 'undefined'){
		style_id = varient_style_id;
	}
		
	//add style id as class to container
	cp_info_bar.addClass(style_id);

	//title
	title = htmlEntities(title);
	cp_title.html(title);
	if(jQuery("#title_editor").length !== 0) {
		CKEDITOR.instances.title_editor.setData(title);
	}

	//description
	description = htmlEntities(description);
	cp_info_bar_desc.html(description);
	if(jQuery("#description_editor").length !== 0) {
		CKEDITOR.instances.description_editor.setData(description);
	}

	//button
	button_title = htmlEntities(button_title);
	cp_submit.html(button_title);
	if(jQuery("#button_editor").length !== 0) {
		CKEDITOR.instances.button_editor.setData(button_title);
	}

	//content
	content = htmlEntities(content);
	ib_content_editor.html(content);
	if(jQuery("#ib_content_editor").length !== 0) {
		CKEDITOR.instances.ib_content_editor.setData(content);
	}

	//second button	
	ifb_button_title = htmlEntities(ifb_button_title);
	cp_second_submit_btn.html(ifb_button_title);
	if(jQuery("#sec_button_editor").length !== 0) {
		CKEDITOR.instances.sec_button_editor.setData(ifb_button_title);
	}

}

//for close image setup
function cp_info_bar_close_img_settings(data){

	var cp_close			= jQuery(".ib-close"),
		ib_container        = jQuery(".cp-ib-container"),
		ib_body_container   = jQuery(".cp-info-bar-body"),
		close_info_bar		= data.close_info_bar,
		close_txt			= data.close_txt,
		close_text_color	= data.close_text_color,
		close_img			= data.close_img,
		close_img_size		= data.close_img_size,
		close_img_width		= data.close_img_width,
		close_img_position  = data.close_info_bar_pos;

	cp_close.show();
	cp_close.removeAttr("class");
	if( close_img_position == 0 ) { 
		cp_close.appendTo(jQuery(".cp-info-bar-body"));
		ib_body_container.addClass('ib-close-inline').removeClass('ib-close-outside');
	} else {
		cp_close.appendTo(jQuery(".cp-info-bar-container"));
		ib_body_container.addClass('ib-close-outside').removeClass('ib-close-inline');
	}	
	if( close_info_bar == "close_img" ){
		cp_close.addClass("ib-close ib-img-close");
		cp_close.stop().animate({'width':close_img_width+'px'},100);
		if(close_img.indexOf('http') !== 0){
			var img_data = {action:'cp_get_image',img_id:close_img,size:close_img_size};
			jQuery.ajax({
				url: smile_ajax.url,
				data: img_data,
				type: "POST",
				success: function(img){
					cp_close.html('<img src="'+img+'"/>');
					jQuery(document).trigger("cp_ajax_loaded",[data]);
				}
			});
		} else {
			close_img_full_src = close_img.split('|');
			close_img_src = close_img_full_src[0];
			cp_close.html('<img class="ib-img-default" src="'+close_img_src+'"/>');
		}
	} else if( close_info_bar == "close_txt" ) {
		cp_close.removeAttr("style");
		cp_close.addClass("ib-close ib-text-close");
		cp_close.html('<span style="color:'+close_text_color+'">'+close_txt+'</span>');
	} else {
		cp_close.addClass("ib-close");
		cp_close.hide();
	}
}

//for infobar  animation
function cp_info_bar_animation_setup(data){

	var cp_animate		= jQuery(".cp-info-bar"),
		exit_animation	= data.exit_animation,
		entry_animation	= data.entry_animation,
		toggle_btn 		= data.toggle_btn;

	var entry_anim = ( typeof cp_animate.attr("data-entry-animation") !== "undefined" ) ? cp_animate.attr("data-entry-animation") : '';
	var exit_anim = ( typeof cp_animate.attr("data-exit-animation") !== "undefined" ) ? cp_animate.attr("data-exit-animation") : '';

	cp_animate.removeClass('smile-animated');

	if( !cp_animate.hasClass(exit_animation) && exit_animation !== exit_anim ){
		cp_animate.attr('data-exit-animation', exit_animation );
		setTimeout(function(){
			if( exit_animation !== "none" ) {
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(entry_anim);
				// cp_animate.removeClass (function (index, css) {
				//     return (css.match (/\bsmile-\S+/g) || []).join(' ');
				// });
				cp_animate.addClass('smile-animated '+exit_animation);
				cp_animate.attr('data-entry-animation', entry_animation );
			}
			setTimeout( function(){
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(exit_animation);
				cp_animate.removeClass(entry_anim);
				// cp_animate.removeClass (function (index, css) {
				//     return (css.match (/\bsmile-\S+/g) || []).join(' ');
				// });
				cp_animate.addClass('smile-animated '+entry_anim);
			}, 1000 );
		},500);
	}

	if( !cp_animate.hasClass(entry_animation) && entry_animation !== entry_anim ){
		cp_animate.attr('data-entry-animation', entry_animation );
		setTimeout(function(){
			if( entry_animation !== "none" ) {
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(entry_anim);
				cp_animate.removeClass (function (index, css) {
				    return (css.match (/\bsmile-\S+/g) || []).join(' ');
				});
				cp_animate.addClass('smile-animated '+entry_animation);
			}
		},500);
	}
}

//infobar position -top/bottom/fixed
function cp_info_bar_position_setup(data){

	var style 						= data.style;
	var cp_info_bar					= jQuery(".cp-info-bar"),
		cp_info_bar_wrapper 		= jQuery(".cp-info-bar-wrapper"),
		description 				= data.infobar_description,
		cp_info_bar_body			= jQuery(".cp-info-bar-body"),
		cp_info_bar_desc_container	= jQuery(".cp-info-bar-desc-container"),
		cp_info_bar_desc			= jQuery(".cp-info-bar-desc");

	var position					= data.infobar_position,
		height						= data.infobar_height,
		fix_position				= data.fix_position,
		page_down					= data.page_down,
		animate_push_page           = data.animate_push_page;

	//for top/bottom position
	if( !cp_info_bar.hasClass( position ) ){
		cp_info_bar.removeClass( 'cp-pos-top' );
		cp_info_bar.removeClass( 'cp-pos-bottom' );
		cp_info_bar.addClass( position );
	}

	//for fix position
	fix_position = parseInt( fix_position );
	page_down = parseInt( page_down );

	cp_info_bar.addClass('ib-fixed');

	ib_height = cp_info_bar.outerHeight();

	if( position == "cp-pos-top" ) {
		cp_info_bar.css('top','0');
		if( page_down ) {
			jQuery("body").addClass("managePageDown");
			if( animate_push_page == 1 ) {
				jQuery("body").stop().animate({'marginTop':ib_height+'px'},1000);
			} else {
				jQuery("body").css( 'margin-top', ib_height+'px' );
			}
		} else {
			jQuery("body").removeClass("managePageDown");
			if( animate_push_page == 1 ) {
				jQuery("body").stop().animate({'marginTop':'0px'},1000);
			} else {
				jQuery("body").css( 'margin-top' , '0px' );
			}
			setTimeout( function(){ jQuery("body").removeAttr('style'); },1500 );
		}
	} else {
		cp_info_bar.css('top','auto');
		jQuery("body").removeAttr('style');
	}
}

//for form setup
function cp_ifb_form_setup(data){

	var cp_info_bar_body 			= jQuery('.cp-info-bar-body');
		cp_info_bar_body_overlay 	= jQuery('.cp-info-bar-body-overlay');
		cp_form 					= jQuery(".cp-form"),
		cp_info_bar					= jQuery(".cp-info-bar"),
		cp_name 					= jQuery(".cp-name"),
		cp_email 					= jQuery(".cp-email"),
		cp_submit 					= jQuery("#button_editor"),
		cp_name_input				= jQuery(".cp-name-field input"),
		cp_name_wrap				= jQuery(".cp-name-field");
		cp_mail_input				= jQuery(".cp-email-field input"),
		cp_input					= jQuery(".cp-info-bar input"),
		cp_info_bar_wrapper 		= jQuery(".cp-info-bar-wrapper"),
		cp_form_container			= jQuery(".cp-form-container"),
		cp_ifb_toggle_btn 			= jQuery(".cp-ifb-toggle-btn");

	var height						= data.infobar_height,
		width                       = data.infobar_width,
		button_animation			= data.button_animation,

		//	Background
		opt_bg 						= data.opt_bg,
		opt_bg 						= opt_bg.split("|"),
		bg_repeat 					= opt_bg[0],
		bg_pos 						= opt_bg[1],
		bg_size 					= opt_bg[2];
		bg_image 					= data.infobar_bg_image,
		bg_image_size 				= data.infobar_bg_image_size,
		bg_color					= data.bg_color,
		bg_gradient 				= data.bg_gradient,

		//	Submit
		btn_bg_color 				= data.button_bg_color,
		btn_border 					= data.btn_border,
		btn_border_radius 			= data.btn_border_radius,
		btn_border_color 			= data.btn_border_color,
		btn_style 					= data.btn_style,
		btn_shadow 					= data.btn_shadow,
		button_txt_hover_color 		= data.button_txt_hover_color,

		button_title				= data.button_title,
		button_bg_color				= data.button_bg_color,
		button_border_color			= data.button_border_color,
		placeholder_text			= data.placeholder_text,
		name_text					= data.name_text,
		namefield					= data.namefield,
		placeholder_color			= data.placeholder_color,
		input_bg_color				= data.input_bg_color,
		input_border_color			= data.input_border_color,
		new_line_optin				= data.new_line_optin,

		//	Toggle Button
		toggle_button_font 			= data.toggle_button_font,

		//	Border
		enable_shadow				= data.enable_shadow,

		//	Border
		border_str 					= data.border,
		enable_border				= data.enable_border,
		border_darken 				= data.border_darken,
		
		btn_darken					= data.btn_darken,
		btn_gradian 				= data.btn_gradiant,

		//	Custom CSS
		custom_css 					= data.custom_css,

		//	Google Fonts
		cp_google_fonts 			= data.cp_google_fonts,
		placeholder_font 			= data.placeholder_font,

		//	Custom Form
		html_form_container 		= jQuery(".custom-html-form"),
		form_container 				= jQuery(".ib-form-container"),
		custom_html_form 			= data.custom_html_form,
		mailer 						= data.mailer;

	//	Add the custom form HTML code
	html_form_container.html(custom_html_form);

	if( mailer == "custom-form" ) {
		form_container.hide();
		html_form_container.show();
	} else {
		form_container.show();
		html_form_container.hide();
	}

	/**
	 *	Add Selected Google Fonts
	 *--------------------------------------------------------*/
	cp_infobar_get_gfonts(cp_google_fonts);	

	//	Append all CSS
	jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
	var ib_style = '';
		cp_form_container.css({'min-height':height+'px'});
		jQuery('.cp-ib-container').css({'width':width+'px'});
		cp_submit.attr('class','cp-button cp-submit cke_editable cke_editable_inline cke_contents_ltr cke_show_borders smile-animated '+button_animation);
		cp_submit.css({"background":button_bg_color,"border":"1px solid "+button_border_color});

	//for input field setup
	namefield = parseInt( namefield );

	if( typeof namefield !== "undefined" && namefield == 1 )
		cp_name_wrap.show();
	else
		cp_name_wrap.hide();

	cp_name_input.attr("placeholder", name_text );
	cp_mail_input.attr("placeholder", placeholder_text );

	cp_input.css({"color":placeholder_color,"background":input_bg_color,"border-color":input_border_color});
	if(placeholder_font=='undefined'){
		placeholder_font='inherit';
	}
	var input_css = '.cp-info-bar input, .cp-info-bar input::-webkit-input-placeholder {\
					   color: '+placeholder_color+';font-family:'+placeholder_font+'\
					}\
					.cp-info-bar input:-moz-placeholder { /* Firefox 18- */\
					   color: '+placeholder_color+';font-family:'+placeholder_font+'\
					}\
					.cp-info-bar input::-moz-placeholder {  /* Firefox 19+ */\
					   color: '+placeholder_color+';font-family:'+placeholder_font+'\
					}\
					.cp-info-bar input:-ms-input-placeholder {  \
					   color: '+placeholder_color+';font-family:'+placeholder_font+'\
					}';
	jQuery("#cp_input_css").html(input_css);


	/**
	 * 	Submit - (Flat, Outline, 3D, Gradient)
	 * 	
	 */
	//	Remove all classes
	var classList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
	jQuery.each(classList, function(i, v){
       cp_submit.removeClass(v);
    });
	cp_submit.addClass( btn_style );

	var c_normal 	= btn_bg_color;
	if(typeof btn_bg_color != 'undefined'){
		var c_hover  	= darkerColor( btn_bg_color, .05 );
		var light 		= lighterColor( btn_bg_color, .3 );
		jQuery('#smile_btn_darken', window.parent.document ).val( c_hover );
	}

	cp_submit.css('background', c_normal);
	//	Apply box shadow to submit button - If its set & equals to - 1
	var shadow = radius = '';
	if( typeof btn_shadow != 'undefined' && btn_shadow == 1 ) {
		shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
	}
	//	Add - border-radius
	if( btn_border_radius != '' ) {
		radius += 'border-radius: ' + btn_border_radius + 'px;';
	}

	//store button lighten color option
	jQuery('#smile_btn_gradiant', window.parent.document ).val( light ); 
	
	jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
	switch( btn_style ) {
		case 'cp-btn-flat': 	ib_style	+= '.cp-info-bar .' + btn_style + '.cp-submit{ background: '+c_normal+'!important;' + shadow + radius + '; } '
											+ '.cp-info-bar .' + btn_style + '.cp-submit:hover { background: '+c_hover+'!important; } ';
			break;
		case 'cp-btn-3d': 		ib_style 	+= '.cp-info-bar .' + btn_style + '.cp-submit {background: '+c_normal+'!important; '+radius+' position: relative ; box-shadow: 0 6px ' + c_hover + ';} '
											+ '.cp-info-bar .' + btn_style + '.cp-submit:hover {background: '+c_normal+'!important;top: 2px; box-shadow: 0 4px ' + c_hover + ';} '
											+ '.cp-info-bar .' + btn_style + '.cp-submit:active {background: '+c_normal+'!important;top: 6px; box-shadow: 0 0px ' + c_hover + ';} ';
			break;
		case 'cp-btn-outline': 	ib_style 	+= '.cp-info-bar .' + btn_style + '.cp-submit { background: transparent!important;border: 2px solid ' + c_normal + ';color: inherit ;' + shadow + radius + '}'
											+ '.cp-info-bar .' + btn_style + '.cp-submit:hover { background: ' + c_hover + '!important;border: 2px solid ' + c_hover + ';color: ' + button_txt_hover_color + ' ;' + '}'
											+ '.cp-info-bar .' + btn_style + '.cp-submit:hover span { color: inherit !important ; } ';
			break;
		case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
									ib_style  += '.cp-info-bar .' + btn_style + '.cp-submit {'
											+ '     border: none ;'
											+ 		shadow + radius
											+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
											+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
											+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
											+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
											+ '}'
											+ '.cp-info-bar .' + btn_style + '.cp-submit:hover {'
											+ '     background: ' + c_normal + ' !important;'
											+ '}';

			break;
	}		

	/**
	 * 	Shadow & Border
	 * 	
	 */

	/* Shadow */
	if( typeof enable_shadow != 'undefined' && enable_shadow == '1' ) {
		cp_info_bar.addClass('cp-info-bar-shadow');
	} else {
		cp_info_bar.removeClass('cp-info-bar-shadow');
	}

	/* Border */
	if( typeof enable_border != 'undefined' && enable_border == '1' ) {

		cp_info_bar.addClass('cp-info-bar-border');

		//	If border color is not set then add bg color darken color as a border color
		if( border_darken === '' ){
			
			// Generate the BORDER COLOR
			var border_darken =  darkerColor( bg_color, .05 );

			//	store it!
			jQuery('#smile_border_darken', window.parent.document ).val( border_darken );

		}

		ib_style += '.cp-pos-top.cp-info-bar-border {'
				+ '     border-bottom: 2px solid ' + border_darken
				+ '}'
				+ '.cp-pos-bottom.cp-info-bar-border {'
				+ '     border-top: 2px solid ' + border_darken
				+ '}';
	}

	/**
	 * 	Custom CSS
	 * 	
	 */
	ib_style += custom_css;

	/**
	 * 	Background - (Background Color / Gradient)
	 * 	
	 */
	var light = lighterColor( bg_color, .3 );

	if( typeof bg_gradient != 'undefined' && bg_gradient == '1' ) {
		
		//	store it!
		jQuery('#smile_bg_gradient_lighten', window.parent.document ).val( light );
		
		ib_style +=  '.cp-info-bar-body-overlay {'
					+ '     background: -webkit-linear-gradient(' + light + ', ' + bg_color + ');'
					+ '     background: -o-linear-gradient(' + light + ', ' + bg_color + ');'
					+ '     background: -moz-linear-gradient(' + light + ', ' + bg_color + ');'
					+ '     background: linear-gradient(' + light + ', ' + bg_color + ');'
					+ '}';
	} else {
		ib_style +=  '.cp-info-bar-body-overlay {'
					+ '     background: ' + bg_color
					+ '}';
	}

	/**
	 * 	Toggle Button
	 */
	var font = 'sans-serif';
	if( toggle_button_font ) {
		font = toggle_button_font + ',' + font;
	}
	ib_style +=  '.cp-ifb-toggle-btn {'
				+ '     font-family: ' + font + ';'
				+ '}';

	/**
	 * 	Background - (Image)
	 * 	
	 */
	cp_info_bar_body_overlay
	if( bg_image !== "" ) {
		var img_data = {action:'cp_get_image',img_id:bg_image,size:bg_image_size};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: "POST",
			success: function(img){
				cp_info_bar_body.css({
					"background-image"    : 'url('+img+')',
					"background-repeat"   : bg_repeat,
					"background-position" : bg_pos,
					"background-size"     : bg_size,
					"background-color" 	  : "transparent"
				});
			}
		});
	} else {
		//	Add transparent color for info bar BODY
		if( bg_color ) {
			cp_info_bar_body.css('background', bg_color );
		} else {
			cp_info_bar_body.css('background', 'transparent' );
		}
	}

	//	Append ALL CSS
	jQuery('#cp-temporary-inline-css').html('<style>' + ib_style + '</style>');
}

//modal image
function cp_infobar_infobar_image(data){
	var infobar_image 	    		= data.infobar_image,
		image_vertical_position 	= data.image_vertical_position,
		image_horizontal_position 	= data.image_horizontal_position,
		image_size 					= data.image_size,
		infobar_image_size			= data.infobar_image_size,
		image_displayon_mobile 		= data.image_displayon_mobile,
		cp_img_container			= jQuery(".cp-image-container");
		var cp_hide_img  			= '';
	var modal_img_default = infobar_image;

	if( image_displayon_mobile == 1 ){	
		//cp_img_container.find(".cp-image").addClass('cp_ifb_hide_img');
		cp_hide_img = 'cp_ifb_hide_img';
	} else {
		//cp_img_container.find(".cp-image").removeClass('cp_ifb_hide_img');
	}

	if( modal_img_default.indexOf('http') === -1 )
	 {
		if( infobar_image !== "" ) {
			var img_data = {action:'cp_get_image',img_id:infobar_image,size:infobar_image_size};
			jQuery.ajax({
				url: smile_ajax.url,
				data: img_data,
				type: "POST",
				success: function(img){
					cp_img_container.html('<img src="'+img+'" class="cp-image '+cp_hide_img+' cp-highlight" />');
					cp_img_container.find('img').css({'width': image_size+'px' ,'max-width': image_size+'px' ,'left':image_horizontal_position+'px' ,'top':image_vertical_position+'px'});
				}
			});
		} else {
			cp_img_container.html('');
			cp_img_container.find('img').removeAttr('style');
		}
	} else {
		infobar_image_full_src = infobar_image.split('|');
		infobar_image_src = infobar_image_full_src[0];
		cp_img_container.html('<img src="'+infobar_image_src+'" class="cp-image  '+cp_hide_img+' cp-highlight" />');
		cp_img_container.find('img').css({'width': image_size+'px','max-width': image_size+'px','left':image_horizontal_position+'px' ,'top':image_vertical_position+'px'});
	}

	if( image_displayon_mobile == 1 ){	
		cp_img_container.find(".cp-image").addClass('cp_ifb_hide_img');
	} else {
		cp_img_container.find(".cp-image").removeClass('cp_ifb_hide_img');
	}
}


/**
 * Adds blinking cursor
 * @param container  ( html container class for cursor)
 */
function cp_blinking_cursor(container) {
	setTimeout(function() {
		if( jQuery(container).find('.blinking-cursor').length == 0 ) { 

			var bgcolor = jQuery(container).css('color');	
			var font_size = parseInt( jQuery(container).css('font-size') );
			var fontArray = Array();
			if( jQuery(container+' span.cp_font').length ) {
				jQuery(container + " span.cp_font").each(function(){
					fontArray.push( parseInt( jQuery(this).data('font-size') ) );
				});	

				var maxFontSize = Math.max.apply(Math,fontArray);
				font_size = maxFontSize;
			}	

			jQuery(container).append('<i style="background-color:  '+bgcolor+';font-size: '+font_size+'px !important;" class="blinking-cursor">|</i>');
		}
	}, 500);
}

function cp_color_for_list_tag(){
	
	jQuery(".cp-info-bar").each(function(t) {
		var moadal_style    = 'cp-info-bar'; 
				
		jQuery(this).find("li").each(function() {               
            var parent_li   = jQuery(this).parents("div").attr('class').split(' ')[0];
                 
            var  cnt         = jQuery(this).index()+1,  
                font_size   = jQuery(this).find(".cp_font").css("font-size"),                           
                color       = jQuery(this).find("span").css("color"),
                list_type   = jQuery(this).parent(),
                list_type   = list_type[0].nodeName.toLowerCase(),
                style_type  = '',
                style_css   = '';

            //apply style type to list 
            if( list_type == 'ul' ){
                style_type = jQuery(this).closest('ul').css('list-style-type');
                if( style_type == 'none' ){
                    jQuery(this).closest('ul').css( 'list-style-type', 'disc' );  
                }
            } else {
                style_type = jQuery(this).closest('ol').css('list-style-type');
                if( style_type == 'none' ){
                    jQuery(this).closest('ol').css( 'list-style-type', 'decimal' );  
                }
            }

            //apply color to list
            jQuery(this).find("span").each(function(){
                 var spancolor = jQuery(this).css("color");
                 if( spancolor.length > 0 ){
                        color = spancolor;
                 }
            });

            var font_style = ''; 
            jQuery(".cp-li-color-css-"+cnt).remove();
            jQuery(".cp-li-font-css-"+cnt).remove();
            if( font_size ){
               font_style = 'font-size:'+font_size;
               jQuery('head').append('<style class="cp-li-font-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ '+font_style+'}</style>');
            }   
            if( color ){
              jQuery('head').append('<style class="cp-li-color-css'+cnt+'">.'+moadal_style+' .'+parent_li+' li:nth-child('+cnt+'){ color: '+color+';}</style>');
            }     

          });
	});

}

function cp_toggle_button(data , e){	
	var toggle_btn 						= data.toggle_btn,
	    close_info_bar 					= data.close_info_bar,
	    toggle_button_title 			= data.toggle_button_title,
	    toggle_button_text_color 		= data.toggle_button_text_color, 
	    toggle_btn_gradient 			= data.toggle_btn_gradient, 
	    toggle_button_bg_color 			= data.toggle_button_bg_color,
	    toggle_button_bg_hover_color 	= data.toggle_button_bg_hover_color, 
	    toggle_button_bg_gradient_color = data.toggle_button_bg_gradient_color,
	    button_animation 				= 'smile-slideInDown',
	    cp_ifb_toggle_btn 				= jQuery(".cp-ifb-toggle-btn"), 
	    cp_info_bar_body 				= jQuery(".cp-info-bar-body"),
	    cp_info_bar_container			= jQuery(".cp-info-bar-container");

	    //	Disable toggle if
	    //	Close Link is == 'do_not_close'
		if( close_info_bar === 'do_not_close' ) {
			toggle_btn = 0;
		}

	    if(toggle_btn == 1){	    	
	      	cp_info_bar_container.addClass('cp-ifb-with-toggle');
	    }else{
	    	cp_info_bar_container.removeClass('cp-ifb-with-toggle');
	    	cp_info_bar_container.removeClass('cp-ifb-click');
	    }
	    cp_ifb_toggle_btn.html(toggle_button_title);

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
	  	var ifclick = cp_info_bar_container.hasClass('cp-ifb-click');

	  	if(is_cookie && !ifclick){
	  		 if(toggle_btn == 1){
	  		 	//close_ifb(e);
	  		 	if(jQuery(".cp-info-bar-container").hasClass('cp-ifb-with-toggle')){
					var btn_animation 					= 'smile-slideInDown',
					    exit_animation 				 	= cp_info_bar_container.data("exit-animation"),
					    entry_animation 			    = cp_info_bar_container.data("entry-animation");

					    if(cp_info_bar_container.hasClass('cp-pos-bottom')){
							btn_animation = 'smile-slideInUp';
						}
						cp_info_bar_body.removeClass('cp-flex');		    
					    cp_info_bar_container.removeClass(entry_animation);

					var  cp_info_bar_class 				= cp_info_bar_container.attr('class');					     
					     cp_info_bar_container.attr('class', cp_info_bar_class + ' ' + exit_animation);

					    setTimeout(function() {	
					    	cp_ifb_toggle_btn.removeClass('cp-ifb-hide'),
					    	cp_ifb_toggle_btn.addClass('cp-ifb-show smile-animated '+btn_animation +'');
					   		cp_info_bar_container.removeClass('smile-animated');
					   		cp_info_bar_container.addClass('cp-ifb-hide');
					   	}, 500);

					   }else{
							e.preventDefault();
					   }
	  		 }else{
				  		var  btn_animation 	= 'smile-slideInDown';
					    exit_animation 		= cp_info_bar_container.data("exit-animation"),
					    entry_animation 	= cp_info_bar_container.data("entry-animation");

						cp_info_bar_container.removeClass('cp-ifb-hide');
						cp_info_bar_container.removeClass('smile-animated');
						cp_info_bar_container.removeClass(entry_animation);
						cp_info_bar_container.removeClass(exit_animation);
						if(cp_info_bar_container.hasClass('cp-pos-bottom')){
							btn_animation = 'smile-slideInUp';
						}

					var  cp_info_bar_class 				= cp_info_bar_container.attr('class');
					    
					    cp_ifb_toggle_btn.removeClass('cp-ifb-show smile-animated '+ btn_animation +'');

					    cp_info_bar_container.attr('class',cp_info_bar_class);
					   	cp_info_bar_container.attr('class', cp_info_bar_class + ' smile-animated ' + entry_animation);

					    setTimeout(function() {
					    	cp_ifb_toggle_btn.addClass('cp-ifb-hide');
					   		cp_info_bar_body.addClass('cp-flex');	
					   	}, 10);
				}
	  	}
	   

	    // button animation
	    var button_class = cp_ifb_toggle_btn.attr('class');
	    cp_ifb_toggle_btn.attr('class',button_class);

	    //	button style
		var slideclassList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
		jQuery.each(slideclassList, function(i, v){
	       cp_ifb_toggle_btn.removeClass(v);
	    });

		toggle_btn_style = '';
		if( toggle_btn_gradient == 1){
			toggle_btn_style = 'cp-btn-gradient';			
		}else{	
			toggle_btn_style = 'cp-btn-flat';	
		}
		cp_ifb_toggle_btn.addClass( toggle_btn_style );

		var c_normal 	= toggle_button_bg_color;
		var c_hover  	= darkerColor( toggle_button_bg_color, .05 );
		var light 		= lighterColor( toggle_button_bg_color, .3 );

		cp_ifb_toggle_btn.css('background', c_normal);
		cp_ifb_toggle_btn.css('color', toggle_button_text_color);

		jQuery('#cp-toggle-btn-inline-css').remove();
		jQuery('head').append('<div id="cp-toggle-btn-inline-css"></div>');

		switch( toggle_btn_style ) {
		case 'cp-btn-flat': 		jQuery('#cp-toggle-btn-inline-css').html('<style>'
										+ '.cp-ifb-toggle-btn.' + toggle_btn_style + '{ background: '+c_normal+'!important;' + '; } '
										+ '.cp-ifb-toggle-btn.' + toggle_btn_style + ':hover { background: '+c_hover+'!important; } '
										+ '</style>');
			break;		
		
		case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
									jQuery('#cp-toggle-btn-inline-css').html('<style>'
										+ '.cp-ifb-toggle-btn.' + toggle_btn_style + ' {'
										+ '     border: none ;'									
										+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
										+ '}'
										+ '.cp-ifb-toggle-btn.' + toggle_btn_style + ':hover {'
										+ '     background: ' + c_normal + ' !important;'
										+ '}'
										+ '</style>');
			break;
	}

	//	Set either 10% darken color for 'HOVER'
	//	Or 0.10% darken color for 'GRADIENT'
	jQuery('#smile_toggle_button_bg_hover_color', window.parent.document).val( c_hover );
	jQuery('#smile_toggle_button_bg_gradient_color', window.parent.document).val( light );

}

function show_ifb(e){
		var cp_ifb_toggle_btn 				= jQuery(".cp-ifb-toggle-btn"), 
		    cp_info_bar_body 				= jQuery(".cp-info-bar-body"),
		    cp_info_bar_container			= jQuery(".cp-info-bar-container"),
		    btn_animation 					= 'smile-slideInDown';
		    exit_animation 				 	= cp_info_bar_container.data("exit-animation"),
		    entry_animation 				= cp_info_bar_container.data("entry-animation");

			cp_info_bar_container.removeClass('cp-ifb-hide');
			cp_info_bar_container.removeClass('smile-animated');
			cp_info_bar_container.removeClass(entry_animation);
			cp_info_bar_container.removeClass(exit_animation);
			if(cp_info_bar_container.hasClass('cp-pos-bottom')){
				btn_animation = 'smile-slideInUp';
			}

			cp_info_bar_container.addClass('cp-ifb-click');

		var  cp_info_bar_class 				= cp_info_bar_container.attr('class');
		    
		    cp_ifb_toggle_btn.removeClass('cp-ifb-show smile-animated '+ btn_animation +'');

		    cp_info_bar_container.attr('class',cp_info_bar_class);
		   	cp_info_bar_container.attr('class', cp_info_bar_class + ' smile-animated ' + entry_animation);

		    setTimeout(function() {
		    	cp_ifb_toggle_btn.addClass('cp-ifb-hide');
		   		cp_info_bar_body.addClass('cp-flex');	
		   	}, 10);
}


function close_ifb(e){
		if(jQuery(".cp-info-bar-container").hasClass('cp-ifb-with-toggle')){
		var cp_ifb_toggle_btn 				= jQuery(".cp-ifb-toggle-btn"), 
		    cp_info_bar_body 				= jQuery(".cp-info-bar-body"),
		    cp_info_bar_container			= jQuery(".cp-info-bar-container"),
		    btn_animation 					= 'smile-slideInDown',
		    exit_animation 				 	= cp_info_bar_container.data("exit-animation"),
		    entry_animation 			    = cp_info_bar_container.data("entry-animation");

		    if(cp_info_bar_container.hasClass('cp-pos-bottom')){
				btn_animation = 'smile-slideInUp';
			}

			cp_info_bar_container.removeClass('cp-ifb-click');

		    cp_info_bar_container.removeClass(entry_animation);

		var  cp_info_bar_class 				= cp_info_bar_container.attr('class');
		     
		     cp_info_bar_container.attr('class', cp_info_bar_class + ' ' + exit_animation);

		    setTimeout(function() {	
		    	cp_ifb_toggle_btn.removeClass('cp-ifb-hide');
		    	cp_ifb_toggle_btn.addClass('cp-ifb-show smile-animated '+btn_animation +'');
		   		cp_info_bar_container.removeClass('smile-animated');
		   		cp_info_bar_container.addClass('cp-ifb-hide');
		   		cp_info_bar_body.removeClass('cp-flex');

		   	}, 500);

		   }else{
				e.preventDefault();
		   }
}


//for another submit btn_style

function cp_next_submit_btn(data){

	var ifb_btn_style   			 = data.ifb_btn_style,
		ifb_button_title			 = data.ifb_button_title,
		ifb_button_bg_color			 = data.ifb_button_bg_color,
		ifb_button_txt_hover_color	 = data.ifb_button_txt_hover_color,
		ifb_button_bg_hover_color	 = data.ifb_button_bg_hover_color,
		ifb_button_bg_gradient_color = data.ifb_button_bg_gradient_color,
		ifb_btn_border_radius		 = data.ifb_btn_border_radius,
		ifb_btn_darken 				 = data.ifb_btn_darken,
		ifb_btn_gradiant 			 = data.ifb_btn_gradiant,
		ifb_btn_shadow				 = data.ifb_btn_shadow ,
		button_animation			 = data.button_animation,
		cp_second_submit_btn 		 = jQuery(".cp-second-submit-btn");


		cp_second_submit_btn.removeAttr('class');
		cp_second_submit_btn.attr('class','cp-button cp-second-submit-btn cke_editable cke_editable_inline cke_contents_ltr cke_show_borders smile-animated '+button_animation);
		

		/**
		 * 	Submit - (Flat, Outline, 3D, Gradient)
		 * 	
		 */
		//	Remove all classes
		var newclassList = ['cp-btn-flat', 'cp-btn-3d', 'cp-btn-outline', 'cp-btn-gradient'];
		jQuery.each(newclassList, function(i, v){
	       cp_second_submit_btn.removeClass(v);
	    });
		cp_second_submit_btn.addClass( ifb_btn_style );

		var c_normal 	= ifb_button_bg_color;
		if(typeof ifb_button_bg_color != 'undefined'){
			var c_hover  	= darkerColor( ifb_button_bg_color, .05 );
			var light 		= lighterColor( ifb_button_bg_color, .3 );
			jQuery('#smile_ifb_btn_darken', window.parent.document ).val( c_hover );
			//store button lighten color option
			jQuery('#smile_ifb_btn_gradiant', window.parent.document ).val( light ); 
			//console.log(c_hover);
		}

		cp_second_submit_btn.css('background', c_normal);
		//	Apply box shadow to submit button - If its set & equals to - 1
		var shadow = radius = '';
		if( typeof ifb_btn_shadow != 'undefined' && ifb_btn_shadow == 1 ) {
			shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
		}
		//	Add - border-radius
		if( ifb_btn_border_radius != '' ) {
			radius += 'border-radius: ' + ifb_btn_border_radius + 'px;';
		}

		
		var ib_style = '';

		jQuery('head').append('<div id="cp-btn-inline-css"></div>');
		switch( ifb_btn_style ) {
			case 'cp-btn-flat': 	ib_style	+= '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn{ background: '+c_normal+'!important;' + shadow + radius + '; } '
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:hover { background: '+c_hover+'!important; } ';
				break;
			case 'cp-btn-3d': 		ib_style 	+= '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn {background: '+c_normal+'!important; '+radius+' position: relative ; box-shadow: 0 6px ' + c_hover + ';} '
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:hover {background: '+c_normal+'!important;top: 2px; box-shadow: 0 4px ' + c_hover + ';} '
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:active {background: '+c_normal+'!important;top: 6px; box-shadow: 0 0px ' + c_hover + ';} ';
				break;
			case 'cp-btn-outline': 	ib_style 	+= '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn { background: transparent!important;border: 2px solid ' + c_normal + ';color: inherit ;' + shadow + radius + '}'
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:hover { background: ' + c_hover + '!important;border: 2px solid ' + c_hover + ';color: ' + ifb_button_txt_hover_color + ' ;' + '}'
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:hover span { color: inherit !important ; } ';
				break;
			case 'cp-btn-gradient': 	//	Apply box shadow to submit button - If its set & equals to - 1
										ib_style  += '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn {'
												+ '     border: none ;'
												+ 		shadow + radius
												+ '     background: -webkit-linear-gradient(' + light + ', ' + c_normal + ') !important;'
												+ '     background: -o-linear-gradient(' + light + ', ' + c_normal + ') !important;'
												+ '     background: -moz-linear-gradient(' + light + ', ' + c_normal + ') !important;'
												+ '     background: linear-gradient(' + light + ', ' + c_normal + ') !important;'
												+ '}'
												+ '.cp-info-bar .' + ifb_btn_style + '.cp-second-submit-btn:hover {'
												+ '     background: ' + c_normal + ' !important;'
												+ '}';

				break;
		}	

		//	Append ALL CSS
		jQuery('#cp-btn-inline-css').html('<style>' + ib_style + '</style>');	

}


