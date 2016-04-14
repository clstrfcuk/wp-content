
jQuery(window).load(function(){
	parent.customizerLoaded();
});


//	Generate and return the YouTube URL
function generateURL( video_id, video_start, player_actions, player_controls, player_autoplay ) {
	var video_url = 'https://www.youtube.com/embed/'+video_id+'?rel=0&fs=0';
	if( player_controls == '1' || player_controls == 1 ){
		video_url += '&controls=1';
	} else {
		video_url += '&controls=0';
	}
	if( player_actions == '1' || player_actions == 1 ){
		video_url += '&showinfo=1';
	} else {
		video_url += '&showinfo=0';
	}
	if( video_start ){
		video_url += '&start='+video_start;
	} else {
		video_url += '&start=0';
	}
	return video_url;
}

//	Added padding for submit button
//	Only for customizer preview
//	Removed submit button padding on front end if CTA type is button.
jQuery('head').append('<style>.cp-modal .cp-youtube .cp-youtube-cta-button .cp-submit { padding: 10px 20px; }</style>');

jQuery(document).ready(function(){

	//	Add CSS file of this style
	var css_file = '/youtube/youtube.min.css';
	jQuery('head').append('<link rel="stylesheet" href="' + cp.demo_dir + css_file + '" type="text/css" />');

	//	Initially store options in data attribute & generate the URL
	var iframe 			= jQuery(".cp-content-container").find('iframe'),
		video_id 		= jQuery('#smile_video_id', window.parent.document).val() || '',
		video_start 	= jQuery('.video_start', window.parent.document).val() || '',
		player_actions 	= jQuery('#smile_player_actions', window.parent.document).val() || '',
		player_controls = jQuery('#smile_player_controls', window.parent.document).val() || '',
		player_autoplay = jQuery('#smile_player_autoplay', window.parent.document).val() || '';

		iframe.attr('data-video_id', video_id );
		iframe.attr('data-video_start', video_start );
		iframe.attr('data-player_actions', player_actions );
		iframe.attr('data-player_controls', player_controls );
		iframe.attr('data-player_autoplay', player_autoplay );
		var url = generateURL( video_id, video_start, player_actions, player_controls, player_autoplay );
		iframe.attr('data-url', url );
		iframe.attr('src', url );

	jQuery("html").css('overflow','hidden');
	
	jQuery("body").on("click", ".cp-form-container", function(e) { parent.setFocusElement('cta_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-overlay", function(e) { parent.setFocusElement('modal_overlay_bg_color'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-overlay-close", function(e){ parent.setFocusElement('close_modal'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-affilate-link", function(e){ parent.setFocusElement('affiliate_title'); e.stopPropagation(); });
	jQuery("body").on("click", ".cp-affilate", function(e){ parent.setFocusElement('affiliate_title'); e.stopPropagation(); });

	// remove blinking cursor
	jQuery("body").on("click", ".cp-title-container", function(e){
		jQuery(this).find(".blinking-cursor").remove();
	}); 

	// Preventing links and form navigation in an iframe
	jQuery('a, button').click(function(e){
		e.preventDefault();
	});
	jQuery(this).on('submit','form',function(e){
		e.preventDefault();
	});

	//	CKEditor
	if( jQuery("#youtube_submit_btn").length ) {
		// Turn off automatic editor creation first.
		CKEDITOR.disableAutoInline = true;
		CKEDITOR.inline( 'youtube_submit_btn' );	
		CKEDITOR.instances.youtube_submit_btn.on( 'change', function() {
			var data = CKEDITOR.instances.youtube_submit_btn.getData();
			parent.updateHTML(htmlEntities(data),'smile_youtube_submit');
		} );

		//	In any case if CKEditor is not initialized then use below code
		CKEDITOR.instances.youtube_submit_btn.on( 'instanceReady', function( ev ) {
			var editor = ev.editor;
	     	editor.setReadOnly( false );
		} );
	}

	//	Highlight Background options
	jQuery("body").on("click", ".cp-form-container", function(e){ parent.setFocusElement('modal_bg_color'); e.stopPropagation(); });

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){
		// data - this is an object that stores all your input information in a format - input:value

		jQuery('#youtube_submit_btn').attr('contenteditable', true );

		var style 					= data.style;
		var cp_modal_body			= jQuery(".cp-modal-body"),
			cp_content_container	= jQuery(".cp-content-container"),
			cp_modal				= jQuery(".cp-modal"),
			cp_modal_content		= jQuery(".cp-modal-content"),
			modal_overlay			= jQuery(".cp-overlay"),
			overlay_close			= jQuery(".cp-overlay-close"),
			form_container			= jQuery(".default-form"),
			cp_md_overlay       	= jQuery(".cp-modal-body-overlay"),
			html_form_container		= jQuery(".custom-html-form"),
			cp_text_container 		= jQuery(".cp-text-container"),
			cp_affilate_link 		= jQuery(".cp-affilate-link") ,
			cp_afl_link 			= jQuery(".cp_afl_link"),
			cp_fs_overlay			= jQuery(".cp_fs_overlay"),
			cp_hide_link			= jQuery(".cp-hide-link"),
			cp_affilate_bottom		= jQuery(".cp-affilate-bottom"),
			cp_youtube_iframe 		= jQuery(".cp-content-container > iframe"),
			cp_affilate 			= jQuery(".cp-affilate"),
			cp_youtube_submit		= jQuery("#youtube_submit_btn"),

			//	Form
			form_without_name 		= jQuery(".cp-form-without-name"),
			form_with_name 			= jQuery(".cp-form-with-name"),
			cp_submit_wrap 			= jQuery('.cp-submit-container'),
			cp_name        			= jQuery(".cp-name-form"),
			cp_email_with_name      = jQuery(".cp-email.cp-input"),
			cp_email_without_name   = jQuery(".cp-email-form"),
			cp_submit 				= jQuery(".cp-submit"),
			cp_img_container		= jQuery(".cp-image-container"),
			cp_form_container   	= jQuery(".cp-form-container"),
			cp_md_overlay       	= jQuery(".cp-modal-body-overlay");

		var modal_size					= data.modal_size,
			cp_modal_width				= data.cp_modal_width,
			overlay_bg_color		  	= data.modal_overlay_bg_color,
			border_str					= data.border,
			box_shadow_str 				= data.box_shadow,
			close_text_color 		  	= data.close_text_color ,
			close_img_size				= data.close_img_size,
			close_bg_color 				= data.close_bg_color,
			close_position 				= data.close_position,
			video_id 					= data.video_id,
			player_controls				= data.player_controls,
			cta_bg_color				= data.cta_bg_color,
			video_start 				= data.video_start,
			cp_google_fonts				= data.cp_google_fonts,
			player_actions				= data.player_actions,
			player_autoplay 			= data.player_autoplay,
			bg_color					= data.modal_bg_color,

			cta_switch 					= data.cta_switch,

			// Form
			btn_disp_next_line  		= data.btn_disp_next_line,
			namefield 					= data.namefield,
			cta_type					= data.cta_type,
			youtube_submit 				= data.youtube_submit;

		//	Add background Color
		cp_md_overlay.css( "background-color", bg_color );

		/**
 		 *	Hide parent modal custom width option for this style only.
 		 *--------------------------------------------------------*/
 		var v = jQuery('#smile_modal_size', window.parent.document).val();
 		if( typeof v != 'undefined' && v != null ) {
 			var f_width = jQuery('input[name="cp_modal_width"]', window.parent.document).closest('.smile-element-container').hide();
 			if( v == 'cp-modal-window-size' ) {
 				f_width.hide();
 			} else {
 				f_width.show();
 			}
 		}

 		// CKEditor submit button
		youtube_submit = htmlEntities(youtube_submit);
		cp_youtube_submit.html(youtube_submit);
		if( jQuery("#youtube_submit_btn").length ) {
			CKEDITOR.instances.youtube_submit_btn.setData(youtube_submit);
		}


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

		// animations
		cp_tooltip_settings(data); // close button and tooltip related settings 
		cp_tooltip_reinitialize(data); // reinitialize tooltip on modal resize

		cp_form_style(data);

		jQuery(window).resize(function(e) {						
			cp_affilate_reinitialize(data);
			cp_tooltip_reinitialize(data);
		});

		//	iFrame border
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

		// 	Check & Udpate the YouTube url.
		// 	Set NEW URL if there is difference in between OLD & NEW url
		var new_url = generateURL( video_id, video_start, player_actions, player_controls, player_autoplay );
		var old_url = cp_youtube_iframe.attr('data-url');
		if( new_url !== old_url ) {
			cp_youtube_iframe.attr('src', new_url);
			cp_youtube_iframe.attr('data-url', new_url);
		}

		var v_height = cp_modal_width;
		v_height *= 1;
		var valueHeight = Math.round((v_height/16)*9);
		// parent.setYoutubeVideoHeight(valueHeight);

		cp_md_overlay.css( "background-color", cta_bg_color );	

		switch (modal_size) {
			case 'cp-modal-custom-size':						
						cp_modal.removeClass('cp-modal-window-size');
						cp_content_container.css({ 'float':'none', 'max-width':cp_modal_width+'px', 'width':'100%', 'height':valueHeight+'px', 'margin': '0 auto', 'padding': 0 });
						cp_modal_content.css({'max-width':cp_modal_width+'px','width':'100%'});
						cp_modal.css({'max-width':cp_modal_width+'px','width':'100%'});
				break;
				
			case 'cp-modal-window-size':
						cp_content_container.css({'float':'none', 'max-width':'', 'width':'100vw','height':'100vh', 'margin': '0 auto', 'padding': 0 });
						cp_modal_content.css({'max-width':'100vw','width':'100vw'});
						cp_modal.css({'max-width':'100vw','width':'100vw'});
				break;
			
			default:
						cp_modal.removeClass('cp-modal-custom-size');
						jQuery(".cp_cs_overlay").css({"display":"none"});
						jQuery(".cp_fs_overlay").css({"display":"block"});

						cp_modal.removeAttr('style');
						cp_modal_content.removeAttr('style');
						cp_content_container.removeAttr('style');
						var ww = jQuery(window).width();			
						var wh = jQuery(window).height();
						cp_content_container.css({'float':'none', 'max-width':ww+'px','width':'100%','height':wh+'px','padding':'0','margin':'0 auto'});
						cp_modal_content.css({'max-width':ww+'px','width':'100%'});
						cp_modal.css({'max-width':ww+'px','width':'100%'});
				break;
		}

		modal_overlay.css('background', overlay_bg_color);
		
		// Form bg color
		cp_form_container.css('background', cta_bg_color); // cp_modal_content.css('background', bg_color);

		if( !cp_modal.hasClass("cp-modal-exceed") ){
			cp_modal.attr('class', 'cp-modal '+modal_size);
		} else {
			cp_modal.attr('class', 'cp-modal cp-modal-exceed '+modal_size);
		}

		if( cta_switch == '0' ) {
			jQuery('.cp-form-container').hide();
		} else {
			jQuery('.cp-form-container').show();
		}

	});
});