/*
 Document   :  On Page Optimization
 Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspFacebookPage = (function ($) {
	"use strict";

	var debug_level = 0;
	var IDs = [];
	var loaded_page = 0;
	//var langmsg = {};
	var settings = {};

	// cron table page
	var maincontainer_tasks = null;
	var mainloading_tasks = null;

	// metabox wrappers
	var metabox = {
		main_id		: '',
		main		: null,
		preload		: null,
		box			: null,
		boxmenu		: null,
		boxcontent	: null,
		post_id		: 0,
		lang		: {},
		settings	: {}
	};


	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			// cron table page
			maincontainer_tasks = $(".psp-main");
			mainloading_tasks = maincontainer_tasks.find("#psp-main-loading");

			// metabox
			metabox_init();

			triggers();
		});
	})();

	function triggers()
	{
		// cron table page - delete rows
		maincontainer_tasks.on('click', '#psp-do_bulk_delete_facebook_planner_rows', function(e){
			e.preventDefault();

			if (confirm('Are you sure you want to delete the selected rows?'))
				delete_bulk_rows();
		});
	}

	/**
	 * Meta Box
	 */
	function metabox_init() {
		metabox.main_id		= '#psp_facebook_share-options';
		metabox.main 		= $(metabox.main_id);
		if ( metabox.main.length ) {
			metabox.preload 	= metabox.main.find("#psp-meta-box-preload");
			metabox.box		 	= metabox.main.find(".psp-meta-box-container");
		}
		if ( metabox.box && metabox.box.length ) {
			metabox.boxmenu 	= metabox.box.find(".psp-tab-menu");
			metabox.boxcontent 	= metabox.box.find(".psp-tab-container");
		}
		//console.log( metabox );

		if ( metabox.box && metabox.box.length ) {
			var lang 		= {},
				settings 	= {};

			// language messages
			lang = metabox.main.find('#psp-meta-boxlang-translation').html();
			//lang = JSON.stringify(lang);
			lang = typeof lang != 'undefined'
				? JSON && JSON.parse(lang) || $.parseJSON(lang) : lang;

			// settings
			settings = metabox.main.find('#psp-meta-box-settings').html();
			//settings = JSON.stringify(settings);
			settings = typeof settings != 'undefined'
				? JSON && JSON.parse(settings) || $.parseJSON(settings) : settings;

			metabox.lang = lang;
			metabox.settings = settings;

			metabox.post_id = metabox.box.data('post_id');
			//console.log( metabox );

			metabox_triggers();
			metabox_load();
		}
	}

	function metabox_triggers() {
		metabox.main.on('click', '.psp-tab-menu a', function(e){
			e.preventDefault();

			var that 	= $(this),
				open 	= metabox.boxmenu.find("a.open"),
				href 	= that.attr('href').replace('#', '');

			metabox.box.hide();

			metabox.boxcontent.find("#psp-tab-div-id-" + href ).show();

			// close current opened tab
			var rel_open = open.attr('href').replace('#', '');

			metabox.boxcontent.find("#psp-tab-div-id-" + rel_open ).hide();

			metabox.preload.show();
			metabox.preload.hide();

			metabox.box.fadeIn('fast');

			open.removeClass('open');
			that.addClass('open');
		});

		fb_scheduler.init( {'post_id': metabox.post_id} );
		fb_postnow.init( {'post_id': metabox.post_id} );
		fb_planner_post.init( metabox.settings );
	}

	function metabox_load() {
		var data = {
				action				: 'psp_metabox_fb',
				sub_action			: 'load_box',
				post_id				: metabox.post_id
		};

		//loading( 'show' );

		$.post(ajaxurl, data, function(response) {

			if ( misc.hasOwnProperty(response, 'status') ) {
				metabox.boxcontent.html( response.html );

				// Display DateTimePicker
				jQuery('#psp_wplannerfb_date_hour').datetimepicker({
					timeFormat: 'H',
					separator: ' @ ',
					showMinute: false,
					ampm: false,
					//addSliderAccess: true,
					//sliderAccessArgs: { touchonly: false },
					timeOnlyTitle: metabox.lang.timeOnlyTitle,
					timeText: metabox.lang.timeText,
					hourText: metabox.lang.hourText,
					currentText: metabox.lang.currentText,
					closeText: metabox.lang.closeText
				});

				fb_planner_post.default();
			}

			//loading( 'close' );
			metabox.preload.hide();
			metabox.box.fadeIn('fast');

		}, 'json')
		.fail(function() {})
		.done(function() {})
		.always(function() {});
	}

	/**
	 * Meta Box - related
	 */
	//function setLangMsg( atts ) {
	//	langmsg = $.extend(langmsg, atts);
	//}

	var fb_planner_post = function() {

		var atts = {};

		function init( atts2 ) {
			atts = $.extend(atts, atts2);
			console.log( atts  );
			triggers();
			//defaultValues();
		};

		function triggers() {
			jQuery('body').on('click', '#psp-wplannerfb-auto-complete', function() {
				autocomplete_fields();
			});
		};

		function defaultValues() {
			if ( jQuery('#psp_wplannerfb_permalink_value').val() != '' ) {
				jQuery('#psp_wplannerfb_permalink_value').show();
			} else {
				jQuery('#psp_wplannerfb_permalink_value').hide();
			}
		};

		function autocomplete_fields() {
			var titleValue = jQuery('#titlewrap').find('input#title').val(),
			imageValue = jQuery('#psp_wplannerfb_image').val(),
			featuredImg = jQuery('a#set-post-thumbnail').find('img.attachment-post-thumbnail').attr('src');

			if (typeof tinymce != 'undefined' && tinymce.activeEditor) {
				if(!tinymce.activeEditor.isHidden()) {
					tinymce.activeEditor.save();
				}
			}

			var descValue = jQuery('#content').val();
			if ( typeof descValue != 'undefined' ) {
				descValue = descValue.replace(/(<([^>]+)>)/ig,""); // remove <> codes
				descValue = descValue.replace(/(\[([^\]]+)\])/ig,""); // remove [] shortcodes
				descValue = descValue.replace(/(\s\s+)/ig,""); // remove multiple spaces
				descValue = descValue.substr(0, 10000);
			}

			//if( titleValue != jQuery('#psp_wplannerfb_title').val() ) {
			if( jQuery.trim( jQuery('#psp_wplannerfb_title').val() ) == '' )
				jQuery('#psp_wplannerfb_title').val( titleValue );
			//if( jQuery.trim( jQuery('#psp_wplannerfb_caption').val() ) == '' )
			//	jQuery('#psp_wplannerfb_caption').val( titleValue );				

			//if( descValue != jQuery('#psp_wplannerfb_description').val() ) {
			if( jQuery.trim( jQuery('#psp_wplannerfb_description').val() ) == '' )
				jQuery('#psp_wplannerfb_description').val( descValue );
		};

		return {
			init		: init,
			triggers	: triggers,
			default		: defaultValues,
			autocomplete: autocomplete_fields
		};
	}()
	
	var fb_postnow = function() {

		var atts = {};

		function init( atts2 ) {
			atts = $.extend(atts, atts2);
			console.log( atts  );
			triggers();
		};

		function triggers() {
			jQuery('body').on('click', '#psp_post_planner_postNowFBbtn', function() {
				var postNowBtn = jQuery('#psp_post_planner_postNowFBbtn');
				var wrappLog = jQuery('.psp_post_planner_postNowFBLog');

				wrappLog.html('').hide();

				// Auto-Complete fields with data from above (title, permalink, content) if empty
				if( jQuery('#psp_wplannerfb_title').val() == '' ||
					//jQuery('#psp_wplannerfb_permalink').val() == '' ||
					jQuery('#psp_wplannerfb_description').val() == ''
				) {
					var c = confirm(metabox.lang.mandatory);

					if(c == true) {
						fb_planner_post.autocomplete();
					}else{
						//alert(metabox.lang.publish_cancel);
						wrappLog.html( metabox.lang.publish_cancel ).show();
						return false;
					}
				}


				var postTo = '',
				postMe = jQuery('#psp_wplannerfb_now_post_to_me'),
				postPageGroup = jQuery('#psp_wplannerfb_now_post_to_page'),
				postTOFbNow = jQuery('#psp_postTOFbNow');

				postTOFbNow.show();
				postNowBtn.hide();

				var postToProfile = '';
				var postToPageGroup = '';
				if( postMe.attr('checked') == 'checked' ) {
					postToProfile = 'on';
				}
				if( postPageGroup.attr('checked') == 'checked' ) {
					postToPageGroup = jQuery('#psp_wplannerfb_now_post_to_page_group').val();
				}

				var data = {
					action: 'psp_publish_fb_now',
					postId: atts.post_id,
					postTo: {'profile' : postToProfile, 'page_group' : postToPageGroup},
					privacy: jQuery('#psp_wplannerfb_now_post_privacy').val(),
					psp_wplannerfb_message: jQuery('#psp_wplannerfb_message').val(),
					psp_wplannerfb_title: jQuery('#psp_wplannerfb_title').val(),
					psp_wplannerfb_permalink: jQuery("input[name=psp_wplannerfb_permalink]:checked").val(),
					psp_wplannerfb_permalink_value: jQuery('#psp_wplannerfb_permalink_value').val(),
					psp_wplannerfb_caption: jQuery('#psp_wplannerfb_caption').val(),
					psp_wplannerfb_description: jQuery('#psp_wplannerfb_description').val(),
					psp_wplannerfb_image: jQuery('input[name=psp_wplannerfb_image]').val(),
					psp_wplannerfb_useimage: jQuery('select[name=psp_wplannerfb_useimage]').val()
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					/*
					if(jQuery.trim(response) == 'OK'){
						postTOFbNow.hide();
						alert( metabox.lang.publish_success );
						postNowBtn.show();
					}else{
						alert( metabox.lang.publish_error );
						postNowBtn.show();
					}
					*/
					postTOFbNow.hide();
					wrappLog.html( response.opMsg ).show();
					postNowBtn.show();
				}, 'json')
				.fail(function() { postNowBtn.show(); })
				.done(function() {})
				.always(function() {});
				return false;
			});
		};

		return {
			init		: init,
			triggers	: triggers
		};
	}();
	
	var fb_scheduler = function() {

		var atts = {};

		function init( atts2 ) {
			atts = $.extend(atts, atts2);
			console.log( atts  );
			triggers();
		};

		function triggers() {
			// Check for mandatory empty fields AND Auto-Complete fields with data from post/page (title, permalink, content) if empty
			jQuery('body').on('click', '#psp_wplannerfb_date_hour', function() {
				var wrappLog = jQuery('.psp_wplannerfb_FBLog');

				wrappLog.html('').hide();

				// Auto-Complete fields with data from above (title, permalink, content) if empty
				if( jQuery('#psp_wplannerfb_title').val() == '' ||
					//jQuery('#psp_wplannerfb_permalink').val() == '' ||
					jQuery('#psp_wplannerfb_description').val() == ''
				) {
					//var c = confirm(metabox.lang.mandatory);

					//if(c == true) {
						fb_planner_post.autocomplete();
					//}else{
						//alert(metabox.lang.mandatory2);
						wrappLog.html( metabox.lang.mandatory2 ).show();
					//	return false;
					//}
				}
			});

			// Auto-Check repeat interval input
			jQuery('body').on('keyup', '#psp_wplannerfb_repeating_interval', function() {
				var $t		= jQuery(this),
					val		= $t.val(),
					val2	= parseInt( val );

				if ( misc.isNormalInteger(val, true) || val != val2 || val2 < 1 ) {
					$t.val( (misc.isNormalInteger(val2, true) ? val2 : 0) );
				}
			});
		};

		return {
			init		: init,
			triggers	: triggers
		};
	}();

	/**
	 * cron table page - delete rows
	 */
	// cron table page - delete rows
	function delete_bulk_rows() {
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
			alert('You didn\'t select any rows!');
			return false;
		}
		
		mainloading_tasks.fadeIn('fast');

		jQuery.post(ajaxurl, {
			'action' 		: 'psp_do_bulk_delete_rows',
			'id'			: ids,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				mainloading_tasks.fadeOut('fast');
				//refresh page!
				window.location.reload();
				return false;
			}
			mainloading_tasks.fadeOut('fast');
			alert('Problems occured while trying to delete the selected rows!');
		}, 'json');
	}

	// :: MISC
	var misc = {

		hasOwnProperty: function(obj, prop) {
			var proto = obj.__proto__ || obj.constructor.prototype;
			return (prop in obj) &&
			(!(prop in proto) || proto[prop] !== obj[prop]);
		},

		isNormalInteger: function(str, positive) {
			//return /^\+?(0|[1-9]\d*)$/.test(str);
			return /^(0|[1-9]\d*)$/.test(str);
		}

	};

	// external usage
	return {
		//'setLangMsg'		: setLangMsg,
		//'fb_planner_post'	: fb_planner_post,
		//'fb_scheduler'		: fb_scheduler,
		//'fb_postnow'		: fb_postnow

    }
})(jQuery);