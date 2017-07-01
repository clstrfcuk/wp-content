/*
 Document   :  On Page Optimization
 Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspOnPageOptimization = (function ($) {
	"use strict";

	var debug_level = 0;
	var maincontainer = null;
	var loading = null;
	var IDs = [];
	var loaded_page = 0;
	var selected_element = [];

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

			// mass optimization page
			maincontainer = $(".psp-main");
			loading = maincontainer.find("#main-loading");

			// metabox
			metabox_init();

			triggers();
		});
	})();

	function triggers()
	{
		//---- :: start Mass Optimization page triggers

		// init google suggest
		/*$('input.psp-text-field-kw').googleSuggest({
			service: 'web'
		});*/

		// :: Multi Keywords - enter
		multi_keywords.load({});

		// SEO Report & Optimize
		maincontainer.on('click', 'a.psp-seo-report-btn', function(e){
			e.preventDefault();

			var that 	= $(this),
				row 	= that.parents("tr").eq(0),
				//field 	= row.find('input.psp-text-field-kw'),
				//focus_kw = field.val(),
				itemID	= that.data('itemid');

			var __mkw = multi_keywords.get_tokens({
				'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+itemID,
				'restype'	: 'list',
			});
			var focus_kw = __mkw;

			row_loading(row, 'show');

			getSeoReport( row, itemID, focus_kw );
		});

		maincontainer.on('click', 'input.psp-do_item_optimize', function(e){
			e.preventDefault();

			optimizePage( $(this) );
		});
		maincontainer.on('click', '#psp-all-optimize', function(e){
			e.preventDefault();
			
			massOptimize( $(this) );
		});
		
		// autodetect - single
		maincontainer.on('click', 'input.psp-auto-detect-kw-btn', function(e){
			e.preventDefault();

			var that 	= $(this),
				row 	= that.parents("tr").eq(0),
				itemID	= row.data('itemid');
				//field 	= row.find('input.psp-text-field-kw'),
				//title   = row.find('input#psp-item-title-' + itemID);

			/* edit post inline */
			row_actions.itemid = itemID;
			row_actions.itemPrev = row_actions.itemCurrent;
			row_actions.itemCurrent = itemID;
			
			row_actions.autoFocus( row, itemID );

			/*row_loading(row, 'show');
			if( $.trim(field.val()) == "" ){
				if( $.trim(title.val()) == "" ){
					row_loading(row, 'hide');
					alert('Your post don\' have any title.'); return false;
				}
				field.val( title.val() );
				row_loading(row, 'hide');
			}
			else{
				row_loading(row, 'hide');
			}*/
		});

		// autodetect - all
		maincontainer.on('click', '#psp-all-auto-detect-kw', function(){
			var that 	= $(this);
			var rowLast = null;

			$('#psp-list-table-posts input.psp-item-checkbox:checked').each(function(){
				var that2 	= $(this),
					row 	= that2.parents("tr").eq(0),
					itemID	= row.data('itemid');
					//field 	= row.find('input.psp-text-field-kw'),
					//title   = row.find('input#psp-item-title-' + itemID);

				/* edit post inline */
				row_actions.itemid = itemID;
				row_actions.itemPrev = row_actions.itemCurrent;
				row_actions.itemCurrent = itemID;
				
				row_actions.autoFocus( row, itemID );
				
				rowLast = row;
				
				/*row_loading(row, 'show');
				if( $.trim(field.val()) == "" ){
					if( $.trim(title.val()) == "" ){
						row_loading(row, 'hide');
						alert('Your post don\' have any title.'); return false;
					}
					field.val( title.val() );
					row_loading(row, 'hide');
				}
				else{
					row_loading(row, 'hide');
				}*/
			});
			
			//special case: close last box
			var $box = rowLast.parent().find('#psp-inline-edit-post-'+row_actions.itemCurrent);
			row_actions.closeBox( $box, rowLast );
		});

		row_actions.init();

		//---- :: end Mass Optimization page triggers



		//---- :: start Products Listing page triggers

		// build score progress bar
		//load_progress_bar( $('body') );

		//---- :: end Products Listing page triggers



		//---- :: start Product Edit page triggers

		// add to wp publish box
		add_to_wp_publish_box();

		// publish / update post - submit form
		(function() {
			var __form_submit = false;

			function _do_submit(e, that) {
				//console.log( that, __form_submit );
				if ( __form_submit ) {
					__form_submit = false;
					return true;
				}

				//NOT WORKING - solution was with "return false" at the function's end
				//e.preventDefault(); e.stopPropagation();

				var __mkw = multi_keywords.get_tokens({
					'el'		: '#psp-field-multifocuskw',
					'restype'	: 'list',
				});
				var focus_kw = __mkw;

				$('#psp-field-multifocuskw').remove();
				that.prepend(
					$('<textarea></textarea>')
					.attr({
						'id' 		: '#psp-field-multifocuskw',
						'name'		: 'psp-field-multifocuskw'
					})
					.css({
						'display'	: 'none'
					})
					.val( focus_kw )
				);

				$('#publish, #submit').removeAttr("disabled"); //#publish : on edit posts ; #submit: on edit categories,tags
				__form_submit = true;
				that.submit();
				return false;
			}

			// triggers
			$('body').on('submit', 'form#edittag', function(e) {
				_do_submit( e, $(this) );
			});
			/*$('body').on('click', 'form#edittag #submit', function(e) {
				_do_submit( e, $(this).closest('form') );
			});*/

			$('body').on('submit', 'form#post', function(e) {
				_do_submit( e, $(this) );
			});
		})();

		//---- :: end Product Edit page triggers
	}

	function add_to_wp_publish_box() {
		var publishInfo = $('.psp-info-column-wrapper');
		//console.log( publishInfo  ); return false;

		if ( $("#misc-publishing-actions").length ) {
			publishInfo.appendTo("#misc-publishing-actions").show();
		}
	}

	
	/**
	 * Meta Box
	 */
	function metabox_init() {
		metabox.main_id		= '#psp_onpage_optimize_meta_box';
		metabox.main 		= $(metabox.main_id);
		if ( metabox.main.length ) {
			metabox.preload 	= metabox.main.find("#psp-meta-box-preload:first");
			metabox.box		 	= metabox.main.find(".psp-meta-box-container:first");
		}
		if ( metabox.box && metabox.box.length ) {
			metabox.boxmenu 	= metabox.box.find(".psp-tab-menu:first");
			metabox.boxcontent 	= metabox.box.find(".psp-tab-container:first");
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

		$("body").on('click', '#psp-tab-div-id-dashboard #psp-edit-focus-keywords', function(e){
			e.preventDefault();
			
			metabox.main.find(".psp-tab-menu a[href='#page_meta']").click();
			//$("#psp-field-focuskw").focus();
			setTimeout(function() {
				$("#psp-field-multifocuskw").parent().find("input.token-input").focus();
			}, 50);
		});
		
		$("body").on('click', '#psp-tab-div-id-dashboard #psp-btn-metabox-autofocus2', function(e){
			e.preventDefault();
			
			metabox.main.find(".psp-tab-menu a[href='#page_meta']").click();
			metaboxAutofocus();
		});
		$("body").on('click', '#psp-tab-div-id-page_meta #psp-btn-metabox-autofocus', function(e){
			e.preventDefault();
			
			metaboxAutofocus();
		});

		// twitter cards
		twitter_cards.init();
	}

	function metabox_load() {
		var data = {
				action				: 'psp_metabox_seosettings',
				sub_action			: 'load_box',
				post_id				: metabox.post_id,
				istax				: metabox.settings.istax,
				taxonomy			: metabox.settings.taxonomy,
				term_id				: metabox.settings.term_id
		};

		//loading( 'show' );

		$.post(ajaxurl, data, function(response) {

			if ( misc.hasOwnProperty(response, 'status') ) {
				metabox.boxcontent.html( response.html );
			}

			// fixit
			var meta_box 		= metabox.box.find(".psp-dashboard-box-content.psp-seo-status-container"),
				meta_box_width 	= metabox.box.width() - 100,
				row				= meta_box.find(".psp-seo-rule-row");
	
			row.width(meta_box_width - 40);
			row.find(".right-col").width( meta_box_width - 180 );
			row.find(".message-box").width(meta_box_width - 45);
			row.find(".right-col .message-box").width( meta_box_width - 180 );

			$(".psp-dashboard-box").each(function(){
				var that = $(this),
					rel = that.attr('rel');
				if( rel != "" ){
					var rel_elm = $("#" + rel);
					if( rel_elm.size() > 0 ){
						var elmHeight = that.height();
						var relHeight = rel_elm.height();

						if( elmHeight > relHeight ){
							rel_elm.height( elmHeight );
						}else if ( relHeight > elmHeight ) {
							that.height( relHeight );
						}
					}
				}
			});

			//loading( 'close' );
			metabox.preload.hide();
			metabox.box.fadeIn('fast');

			snippetPreview();
			setInterval(function(){
				snippetPreview();
			}, 2000);

			charsLeft();

			// build score progress bar
			load_progress_bar( metabox.main );

			// init google suggest
			//$('input#psp-field-focuskw').googleSuggest({
			//	service: 'web'
			//});

			/* Multi Keywords - sub tabs */
			pspFreamwork.multikw_tabs_load( metabox.main.find('.psp-multikw') );

			// :: Multi Keywords - enter
			multi_keywords.load({
				'el'		: '#psp-field-multifocuskw',
				'show_load'	: false
			});

			//metabox.main.find('.psp-tab-menu a').eq(1).trigger('click'); //DEBUG

		}, 'json')
		.fail(function() {})
		.done(function() {})
		.always(function() {});
	}

	function snippetPreview()
	{
		//var focus_kw 	= $("#psp-field-focuskw").val();
		var title 		= $("#psp-field-title").val(),
			title_fb 	= $("#psp-field-facebook-titlu").val(),
			desc 		= $("#psp-field-metadesc").val(),
			link		= $("#sample-permalink").text(),
			prev_box 	= $(".psp-prev-box"),
			post_title	= title;
			
		var $title = $("input[name='post_title']"), $titleTax = $(".form-table").find("input[name='name']");

		if ( $title.length > 0 )
			post_title = $title.val();
		else if ( $titleTax.length >0 )
			post_title = $titleTax.val();
		
		if( $.trim(post_title) == 'Auto Draft' ){
			post_title = '';
		}
		
		/*if ( $.trim( focus_kw ) == '' )
			$("#psp-field-focuskw").val( post_title );
		if ( $.trim( title ) == '' )
			$("#psp-field-title").val( post_title );
		if ( $.trim( title_fb ) == '' )
			$("#psp-field-facebook-titlu").val( post_title );*/

		//prev_box.find(".psp-prev-focuskw").text( $("#psp-field-focuskw").val() );
		prev_box.find(".psp-prev-title").text( $("#psp-field-title").val() );
		prev_box.find(".psp-prev-desc").text( desc );
		prev_box.find(".psp-prev-url").text( link );
		
		$("#psp-field-title").pspLimitChars( $("#psp-field-title-length") );
	}

	function metaboxAutofocus()
	{
		var $box = metabox.box.find('.psp-tab-container'), $boxData = metabox.box.find('#psp-inline-row-data');
		var __mkw = [];

		var postData = {};
		postData.title 			= $boxData.find('.psp-post-title').text();
		postData.gen_desc		= $boxData.find('.psp-post-gen-desc').text();
		postData.gen_kw			= $boxData.find('.psp-post-gen-keywords').text();
		postData.meta_title 	= $boxData.find('.psp-post-meta-title').text();
		postData.meta_desc 		= $boxData.find('.psp-post-meta-description').text();
		postData.meta_kw 		= $boxData.find('.psp-post-meta-keywords').text();
		postData.focus_kw 		= $boxData.find('.psp-post-meta-focus-kw').text();

		//if ( $.trim( $box.find('input[name="psp-field-focuskw"]').val() ) == '' ) {
		//	$box.find('input[name="psp-field-focuskw"]').val( postData.title );
		//}
		// :: Multi Keywords - enter
		__mkw = multi_keywords.get_tokens({
			'el'		: '#psp-field-multifocuskw',
			'restype'	: 'array',
		});
		if ( $.isEmptyObject(__mkw) ) {
			multi_keywords.add_token({
				'el'		: '#psp-field-multifocuskw',
				'token'		: postData.title
			});
		}

		if ( $.trim( $box.find('input[name="psp-field-title"]').val() ) == '' ) {
			$box.find('input[name="psp-field-title"]').val( postData.title );
		}

		if ( $.trim( $box.find('textarea[name="psp-field-metadesc"]').val() ) == '' ) {
			$box.find('textarea[name="psp-field-metadesc"]').val( postData.gen_desc );
		}

		if ( $.trim( $box.find('textarea[name="psp-field-metakewords"]').val() ) == '' ) {
			var __keywords = [];
			//if ( $.trim( $box.find('input[name="psp-field-focuskw"]').val() ) != '' ) {
			//	__keywords.push( $box.find('input[name="psp-field-focuskw"]').val() );
			//}
			// :: Multi Keywords - enter
			__mkw = multi_keywords.get_tokens({
				'el'		: '#psp-field-multifocuskw',
				'restype'	: 'array',
			});
			if ( ! $.isEmptyObject(__mkw) && $.isArray(__mkw) ) {
				for (var ii=0, len=__mkw.length; ii<len; ii++) {
					__keywords.push( __mkw[ii] );
				}
			}

			if ( $.trim( postData.gen_kw ) != '' ) {
				__keywords.push( postData.gen_kw );
			}
			__keywords = __keywords.join(', ');
			$box.find('textarea[name="psp-field-metakewords"]').val( __keywords );
		}
		
		//facebook
		/*if ( $.trim( $box.find('input[name="psp-field-facebook-titlu"]').val() ) == '' )
		$box.find('input[name="psp-field-facebook-titlu"]').val( postData.title );

		if ( $.trim( $box.find('textarea[name="psp-field-facebook-desc"]').val() ) == '' )
		$box.find('textarea[name="psp-field-facebook-desc"]').val( postData.gen_desc );*/
	}
	
	function charsLeft()
	{
		$("#psp-field-metadesc").pspLimitChars( $("#psp-field-metadesc-length") );
		$("#psp-field-metakeywords").pspLimitChars( $("#psp-field-metakeywords-length") );
		$("#psp-field-title").pspLimitChars( $("#psp-field-title-length") );
	}


	/**
	 * Meta Box - Twitter Cards
	 */
	var twitter_cards = {
		init: function() {
			var self = this;

			// load the triggers
			$(document).ready(function(){
				self.triggers();
			});
		},
		
		get_options: function(type) {
			var __type = type || '';
			if ( $.trim(__type)=='' ) return false;
			
			metabox.preload.show();
			metabox.box.hide();

			//var $boxData = metabox.box.find('#psp-inline-row-data');
			var theTrigger = ( __type=='post' ? $('#psp_twc_post_cardtype') : $('#psp_twc_app_isactive') ),
				theTriggerVal = theTrigger.val();
			var theResp = ( __type=='post' ? $('#psp-twittercards-post-response') : $('#psp-twittercards-app-response') );

			if ( $.inArray(theTriggerVal, ['none', 'no', 'default', 'default2']) > -1 ) {
				theResp.html('').hide();
				metabox.preload.hide();
				metabox.box.fadeIn('fast'); //show()
				return false;
			}
			if ( __type=='app' && theTriggerVal=='default' ) {
				theResp.html('').hide();
				return false;
			}

			var   $box_type 		= metabox.main.find('.psp-mb-setts'),
				  $box_taxonomy 	= $box_type.find('.psp-mb-taxonomy'),
				  box_taxonomy		= $box_taxonomy.length ? $.trim( $box_taxonomy.text() ) : '',
				  $box_termid 		= $box_type.find('.psp-mb-termid'),
				  box_termid		= $box_termid.length ? $.trim( $box_termid.text() ) : '';
			$.post(ajaxurl, {
				'action' 			: 'pspTwitterCards',
				'sub_action'		: 'getCardTypeOptions',
				'card_type'			: __type=='post' ? $('#psp_twc_post_cardtype').val() : 'app',
				'page'				: __type=='post' ? 'post' : 'post-app',
				'post_id'			: parseInt( metabox.post_id ), //$boxData.find('.psp-post-postId').text()
				'box_taxonomy'		: box_taxonomy,
				'box_termid'		: box_termid
			}, function(response) {

				metabox.preload.hide();
				metabox.box.fadeIn('fast'); //show()

				var theResp = ( __type=='post' ? $('#psp-twittercards-post-response') : $('#psp-twittercards-app-response') );
				if ( response.status == 'valid' ) {
					theResp.html( response.html ).show();
					return true;
				}
				return false;
			}, 'json');
		},
		
		triggers: function() {
			var self = this;
			
			self.get_options( 'post' );
			self.get_options( 'app' );
	
			$('body').on('change', '#psp-tab-div-id-twitter_cards #psp_twc_post_cardtype', function (e) {
				e.preventDefault();

				self.get_options( 'post' );
			});
			$('body').on('change', '#psp-tab-div-id-twitter_cards #psp_twc_app_isactive', function (e) {
				e.preventDefault();

				self.get_options( 'app' );
			});
		}
	}


	/**
	 * Mass Optimization - optimize, seo report...
	 */
	function tailCheckPages()
	{
		if( selected_element.length > 0 ){
			var curr_element = selected_element[0];
			optimizePage( curr_element.find('.psp-do_item_optimize'), function(){
				selected_element.splice(0, 1);
				
				tailCheckPages();
			});
		}
	}
	
	function massOptimize()
	{
		// reset this array for be sure
		selected_element = [];
		// find all selected items 
		maincontainer.find('.psp-item-checkbox:checked').each(function(){
			var that = $(this),
				row = that.parents('tr').eq(0);
			selected_element.push( row );
		});
		
		tailCheckPages();
	}

	function optimizePage( that, callback )
	{
		var row = that.parents("tr").eq(0),
			id 	= row.data('itemid');
			//kw = row.find('input.psp-text-field-kw').val();

		var __mkw = multi_keywords.get_tokens({
			'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+id,
			'restype'	: 'list',
		});
		var kw = __mkw;

		row_loading(row, 'show');
		
		var itemid = id, $box = row.parent().find('#psp-inline-edit-post-'+itemid);
		if ( $box.length > 0 ) { //item current opened box => action is Quick Save & Optimize in callback!
			row_actions.saveBox( $box, row, [row_actions.optimizeBox, id, kw, row], callback );
			return false;
		} else { //other box => action is Optimize
			row_actions.optimizeBox( id, kw, row, callback );
		}
	}

	function getSeoReport( row, id, kw )
	{
		var lightbox = $("#psp-lightbox-overlay");

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGetSeoReport',
			'id'			: id,
			'kw'			: kw,
			'debug_level'	: debug_level
		}, function(response) {

			if( response.status == 'valid' ){
				do_progress_bar(row, response.score);

				lightbox.find(".psp-lightbox-headline i").eq(0).text( response.post_id );
				lightbox.find("#psp-lightbox-seo-report-response").html( response.html );

				/* Multi Keywords - sub tabs */
				pspFreamwork.multikw_tabs_load( lightbox.find('.psp-multikw') );

				lightbox.fadeIn('fast');
			}

			row_loading(row, 'hide');

		}, 'json');


		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
		});
	}

	function do_progress_bar( row, score, score_show ) {
		var score 		= score || 0,
			score_show 	= typeof score_show !== 'undefined' ? score_show : score;

		var progress_bar = row.find(".psp-progress-bar");
		//progress_bar.attr('class', 'psp-progress-bar');

		//var width = 100; //width = progress_bar.width();
		//width = parseFloat( parseFloat( parseFloat( score / 100 ).toFixed(2) ) * width ).toFixed(1);

		var size_class = 'size_';

		if ( score >= 20 && score < 40 ){
			size_class += '20_40';
		}
		else if ( score >= 40 && score < 60 ){
			size_class += '40_60';
		}
		else if( score >= 60 && score < 80 ){
			size_class += '60_80';
		}
		else if( score >= 80 && score <= 100 ){
			size_class += '80_100';
		}
		else{
			size_class += '0_20';
		}

		progress_bar
		.addClass( size_class )
		.width( score + '%' );

		row.find('.psp-progress').find(".psp-progress-score").text( score_show + "%" );
	}

	function load_progress_bar( wrapper ) {
		//console.log( wrapper.find('.psp-progress') );
		$.each(wrapper.find('.psp-progress'), function(ii) {
			var $this 		= $(this),
				_parent 	= $this.parent(),
				_score		= $this.data('score'),
				_score_show = $this.data('score_show');

			do_progress_bar(_parent, _score, _score_show); //refresh score!
		});
	}

	function row_loading( row, status )
	{
		if( status == 'show' ){
			if( row.size() > 0 ){
				if( row.find('.psp-row-loading-marker').size() == 0 ){
					var row_loading_box = $('<div class="psp-row-loading-marker"><div class="psp-row-loading"><div class="psp-meter psp-animate"><span style="width:100%"></span></div></div></div>')
					row_loading_box.find('div.psp-row-loading').css({
						'width': row.width(),
						'height': row.height()
					});

					row.find('td').eq(0).append(row_loading_box);
				}
				row.find('.psp-row-loading-marker').fadeIn('fast');
			}
		}else{
			row.find('.psp-row-loading-marker').fadeOut('fast');
		}
	}


	/**
	 * Mass Optimization - inline edit | quick edit
	 */
	var row_actions = {
		itemid		: null, //current itemid
		
		//current & previous item box opened for inline edit!
		itemCurrent	: null,
		itemPrev	: null,
		opened		: null,
		
		init: function() {
			var self = this;

			self.triggers();
		},
		
		triggers: function() {
			var self = this;
			
			var tableSelector = '.psp-table-ajax-list table.psp-table';
			
			// show | hide row actions on hover over table tr!
			/*maincontainer.on({
				mouseenter: function () {
					//current item box opened!
					if ( self.opened === true && self.itemCurrent == $(this).data('itemid') )
						return false;
					$(this).find('td span.psp-inline-row-actions').removeClass('hide').addClass('show');
				},
				mouseleave: function () {
					//current item box opened!
					if ( self.opened === true && self.itemCurrent == $(this).data('itemid') )
						return false;
					$(this).find('td span.psp-inline-row-actions').removeClass('show').addClass('hide');
				}
			}, tableSelector+' tr');*/

			maincontainer.on('click', tableSelector+' tr td span.psp-inline-row-actions .editinline',
			function (e) {
				e.preventDefault();
				
				var row = $(this).parents('tr').eq(0),
				itemID	= row.data('itemid');
				
				row_loading(row, 'show');

				self.itemid = itemID;

				self.itemPrev = self.itemCurrent;
				self.itemCurrent = itemID;
				
				if ( $('#psp-inline-edit-post-'+self.itemCurrent).length > 0 ) { //current item box is already opened
					
					// populate box!
					self.boxPopulate( row, self.itemid );

					return false;
				}

				//remove previous item box & row actions view!
				$('#psp-inline-edit-post-'+self.itemPrev).remove();
				//row.parent().find('td span#psp-inline-row-actions-'+self.itemPrev).removeClass('show').addClass('hide');
				
				//build item edit box
				self.buildBox( row, self.itemid );
				
			});
		},
		
		autoFocus: function( row, itemid ) {
			var self = this;
			
			row_loading(row, 'show');
			
			if ( $('#psp-inline-edit-post-'+self.itemCurrent).length > 0 ) { //current item box is already opened
				
				// populate box!
				self.boxPopulate( row, itemid, true );
				
				return false;
			}

			//remove previous item box & row actions view!
			$('#psp-inline-edit-post-'+self.itemPrev).remove();
			//row.parent().find('td span#psp-inline-row-actions-'+self.itemPrev).removeClass('show').addClass('hide');

			//build item edit box
			self.buildBox( row, itemid, true );
		},
		
		buildBox: function( row, itemid, autofocus ) {
			var self = this;
			self.opened = true;

			// create box html!
			self.boxHtml( row, itemid );

			// populate box!
			self.boxPopulate( row, itemid, autofocus );
		},
		
		boxHtml: function( row, itemid ) {
			var self = this;

			var	table = row.parent(), __boxhtml = $('#psp-inline-editpost-boxtpl').html();
			__boxhtml = '<form class="psp-form form-inline-editpost" action="#save_with_ajax">'
				+ __boxhtml + '</form>';
					
			row.after(
				$( '<tr id="psp-inline-edit-post-'+itemid+'" data-itemid="'+itemid+'"></tr>' )
					.append( $('<td colspan=10></td></tr>' ).html( __boxhtml ) )
					.hide()
			);
			
			// retrieve box element!
			var $box = table.find('#psp-inline-edit-post-'+itemid);
			
			// box buttons handlers
			$box.find('input#psp-inline-btn-cancel').bind('click', function (e) {
				self.closeBox( $box, row );
			});
			$box.find('input#psp-inline-btn-save').bind('click', function (e) {
				self.saveBox( $box, row );
			});
		},
		
		boxPopulate: function( row, itemid, autofocus ) {
			var self = this;

			var autofocus = autofocus || false;

			// retrieve box element!
			var	table = row.parent(), $box = table.find('#psp-inline-edit-post-'+itemid),
			$boxData = row.find('#psp-inline-row-data-'+itemid);

			var __mkw = [];

			var postData = {};
			postData.title 			= $boxData.find('.psp-post-title').text();
			postData.gen_desc		= $boxData.find('.psp-post-gen-desc').text();
			postData.gen_kw			= $boxData.find('.psp-post-gen-keywords').text();
			postData.meta_title 	= $boxData.find('.psp-post-meta-title').text();
			postData.meta_desc 		= $boxData.find('.psp-post-meta-description').text();
			postData.meta_kw 		= $boxData.find('.psp-post-meta-keywords').text();
			postData.focus_kw 		= $boxData.find('.psp-post-meta-focus-kw').text();
			postData.canonical		= $boxData.find('.psp-post-meta-canonical').text();
			postData.rindex 		= $boxData.find('.psp-post-meta-robots-index').text();
			postData.rfollow 		= $boxData.find('.psp-post-meta-robots-follow').text();
			postData.spriority 		= $boxData.find('.psp-post-priority-sitemap').text();
			postData.sinclude 		= $boxData.find('.psp-post-include-sitemap').text();

			//row.find('input.psp-text-field-kw').val( postData.focus_kw );
			multi_keywords.replace_tokens({
				'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+itemid,
				'tokens'	: multi_keywords.html2tokens({
					'elm'		: $boxData.find('.psp-post-meta-fields-params')
				})
			});

			$box.find('input[name="psp-editpost-meta-title"]').val( postData.meta_title );
			$box.find('textarea[name="psp-editpost-meta-description"]').val( postData.meta_desc );
			$box.find('textarea[name="psp-editpost-meta-keywords"]').val( postData.meta_kw );
			$box.find('input[name="psp-editpost-meta-canonical"]').val( postData.canonical );
			$box.find('select[name="psp-editpost-meta-robots-index"]').val( postData.rindex );
			$box.find('select[name="psp-editpost-meta-robots-follow"]').val( postData.rfollow );
			$box.find('select[name="psp-editpost-priority-sitemap"]').val( postData.spriority );
			$box.find('select[name="psp-editpost-include-sitemap"]').val( postData.sinclude );

			// placeholders as default
			var postDefault = {};
			postDefault.the_title 				= $boxData.find('.psp-post-default-the_title').length ?
				$boxData.find('.psp-post-default-the_title').text() : '';
			postDefault.the_meta_description	= $boxData.find('.psp-post-default-the_meta_description').length ?
				$boxData.find('.psp-post-default-the_meta_description').text() : '';
			postDefault.the_meta_keywords		= $boxData.find('.psp-post-default-the_meta_keywords').length ?
				$boxData.find('.psp-post-default-the_meta_keywords').text() : '';
			//console.log( postDefault ); 

			$box.find('input[name="psp-editpost-meta-title"]').attr( "placeholder", postDefault.the_title );
			$box.find('input[name="psp-editpost-meta-title"]').attr( "title", postDefault.the_title );
			$box.find('textarea[name="psp-editpost-meta-description"]').attr( "placeholder", postDefault.the_meta_description );
			$box.find('textarea[name="psp-editpost-meta-description"]').attr( "title", postDefault.the_meta_description );
			$box.find('textarea[name="psp-editpost-meta-keywords"]').attr( "placeholder", postDefault.the_meta_keywords );
			$box.find('textarea[name="psp-editpost-meta-keywords"]').attr( "title", postDefault.the_meta_keywords );

			// auto focus
			if ( autofocus ) {
				//if ( $.trim( row.find('input.psp-text-field-kw').val() ) == '' ) {
				//	row.find('input.psp-text-field-kw').val( postData.title );
				//}
				// :: Multi Keywords - enter
				__mkw = multi_keywords.get_tokens({
					'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+itemid,
					'restype'	: 'array',
				});
				if ( $.isEmptyObject(__mkw) ) {
					multi_keywords.add_token({
						'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+itemid,
						'token'		: postData.title
					});
				}

				if ( $.trim( $box.find('input[name="psp-editpost-meta-title"]').val() ) == '' ) {
					$box.find('input[name="psp-editpost-meta-title"]').val( postData.title );
				}

				if ( $.trim( $box.find('textarea[name="psp-editpost-meta-description"]').val() ) == '' ) {
					$box.find('textarea[name="psp-editpost-meta-description"]').val( postData.gen_desc );
				}

				if ( $.trim( $box.find('textarea[name="psp-editpost-meta-keywords"]').val() ) == '' ) {
					var __keywords = [];

					//if ( $.trim( row.find('input.psp-text-field-kw').val() ) != '' ) {
					//	__keywords.push( row.find('input.psp-text-field-kw').val() );
					//}
					// :: Multi Keywords - enter
					__mkw = multi_keywords.get_tokens({
						'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+itemid,
						'restype'	: 'array',
					});
					if ( ! $.isEmptyObject(__mkw) && $.isArray(__mkw) ) {
						for (var ii=0, len=__mkw.length; ii<len; ii++) {
							__keywords.push( __mkw[ii] );
						}
					}

					if ( $.trim( postData.gen_kw ) != '' ) {
						__keywords.push( postData.gen_kw );
					}
					__keywords = __keywords.join(', ');
					$box.find('textarea[name="psp-editpost-meta-keywords"]').val( __keywords );
				}
			}
			
			$box.show();
			
			row_loading(row, 'hide');
		},
		
		closeBox: function( $box, row ) {
			var self = this;
			$box.remove();
			//row.parent().find('td span#psp-inline-row-actions-'+self.itemCurrent).removeClass('show').addClass('hide');
			self.opened = false;
		},
		
		saveBox: function( $box, row, callback, callback2 ) {
			var self = this;
			
			var doOptimize = doOptimize || false;
			
			row_loading(row, 'show');

			var $form = $box.find('.form-inline-editpost'),
				data_save = $form.serializeArray();
				//kw = row.find('input.psp-text-field-kw').val();

			var __mkw = multi_keywords.get_tokens({
				'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+self.itemid,
				'restype'	: 'list',
			});
			var kw = __mkw;

	    	data_save.push({ name: "action", value: "pspQuickEdit" });
	    	data_save.push({ name: "debug_level", value: debug_level });
	    	data_save.push({ name: "id", value: self.itemid });
	    	data_save.push({ name: "kw", value: kw });

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data_save, function(response) {

				if ( response.status == 'valid' ) {
					var id = response.post_id, new_inline = response.edit_inline_new;

					$('#psp-inline-row-data-'+id).html( new_inline ); //refresh post info!

					//refresh focus keyword main table input!
					//row.find('input.psp-text-field-kw').val( response.kw );
					multi_keywords.replace_tokens({
						'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+self.itemid,
						'tokens'	: response.multikw_list
					});

					do_progress_bar(row, response.score); //refresh score!
				}

				row_loading(row, 'hide');

				self.closeBox( $box, row );
				
				if ( $.isArray( callback ) && $.isFunction( callback[0] ) ) {
					callback[0]( callback[1], callback[2], callback[3], callback2 );
				}

			}, 'json');
		},
		
		optimizeBox: function( id, kw, row, callback ) {
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, {
				'action' 		: 'pspOptimizePage',
				'kw'			: kw,
				'id'			: id,
				'debug_level'	: debug_level
			}, function(response) {

				if( response.status == 'valid' ){
					var id = response.post_id, new_inline = response.edit_inline_new;

					$('#psp-inline-row-data-'+id).html( new_inline ); //refresh post info!

					//refresh focus keyword main table input!
					//row.find('input.psp-text-field-kw').val( response.kw );
					multi_keywords.replace_tokens({
						'el'		: 'input.psp-text-field-kw#psp-focus-kw-'+id,
						'tokens'	: response.multikw_list
					});
	
					do_progress_bar(row, response.score);
				}
	
				row_loading(row, 'hide');

				if ( typeof callback == 'function' ) {
					callback();
				}

			}, 'json');
		}
		
	}


	/**
	 * Multi Keywords - enter /implementation
	 */
	var multi_keywords = (function() {
		var _self = this;
		var _field_default_id	= 'input.psp-text-field-kw',
			_delimiter			= "\n";

		function html2tokens( pms ) {
			var pms 		= pms || {},
				elm			= misc.hasOwnProperty(pms, 'elm') ? pms.elm : null,
				restype		= misc.hasOwnProperty(pms, 'restype') ? pms.restype : 'list';

			var fieldsParams = {};
			if (elm.length ) {
				fieldsParams = elm.html();
				//fieldsParams = JSON.stringify(fieldsParams);
				fieldsParams = typeof fieldsParams != 'undefined'
					? JSON && JSON.parse(fieldsParams) || $.parseJSON(fieldsParams) : fieldsParams;
			}

			var __ = '';
			if ( misc.hasOwnProperty(fieldsParams, 'mfocus_keyword') ) {
				__ = $.trim( fieldsParams.mfocus_keyword );
			}

			var ret = '';
			if ( ! $.isEmptyObject(__) && $.isArray(__) ) {
				ret = ( 'list' == restype ? __.join("\n") : __ );
			}
			else if ( typeof __ === 'string' && '' != __ ) {
				ret = ( 'list' == restype ? __ : __.split("\n") );
			}
			else {
				ret = ( 'list' == restype ? '' : [] );
			}
			return ret;
		}

		function get_tokens( pms ) {
			var pms 		= pms || {},
				el			= misc.hasOwnProperty(pms, 'el') ? pms.el : _field_default_id,
				restype		= misc.hasOwnProperty(pms, 'restype') ? pms.restype : 'list';

			var _list 		= '';
			if ( 'list' == restype ) {
				_list = $(el).tokenfield('getTokensList', _delimiter);
			}
			else {
				_list = $(el).tokenfield('getTokens');

				var _tmp = [];
				if ( ! $.isEmptyObject(_list) && $.isArray(_list) ) {
					$.each(_list, function(kk, vv) {
						_tmp.push( vv.value );
					});
				}
				_list = _tmp;
			}
			return _list;
		}

		function add_token( pms ) {
			var pms 		= pms || {},
				el			= misc.hasOwnProperty(pms, 'el') ? pms.el : _field_default_id,
				token		= misc.hasOwnProperty(pms, 'token') ? pms.token : '';

			$(el).tokenfield('createToken', token);
		}

		function replace_tokens( pms ) {
			var pms 		= pms || {},
				el			= misc.hasOwnProperty(pms, 'el') ? pms.el : _field_default_id,
				tokens		= misc.hasOwnProperty(pms, 'tokens') ? pms.tokens : [];

			$(el).tokenfield('setTokens', tokens);
		}

		function load( pms ) {
			var pms 		= pms || {},
				el			= misc.hasOwnProperty(pms, 'el') ? pms.el : _field_default_id,
				show_load 	= misc.hasOwnProperty(pms, 'show_load') ? pms.show_load : true;

			if ( show_load ) {
				pspFreamwork.to_ajax_loader( "Loading..." );
			}

			var convertFields 	= $(el),
				lenBefore		= convertFields.size();

			convertFields.each(function(ii) {
				var that = $(this);

				setTimeout(function() {
					_build_tokenfield({
						'el'		: that,
						'default'	: html2tokens({
							'elm'		: that.prev('.psp-fields-params')
						})
					});
				}, 100);
			});

			if ( show_load ) {
				// check to see if it's finished
				(function() {
					var lenAfter = 0, cc = 0, max = 40;
					function __doit() {
						var __timer = setTimeout(function() {
							cc++;
							lenAfter = $('div.tokenfield.form-control > input.token-input').size();

							if ( ( lenBefore > lenAfter ) && ( cc < max ) ) {
								__doit();
							}
							else {
								setTimeout(function() {
									pspFreamwork.to_ajax_loader_close();
								}, 100);
								clearTimeout(__timer);
								__timer = null;
							}
						}, 250);
					};
					__doit();
				})();
			}
		}

		function _build_tokenfield( pms ) {
			var pms 		= pms || {},
				el			= misc.hasOwnProperty(pms, 'el') ? pms.el : null,
				defaultkw 	= misc.hasOwnProperty(pms, 'default') ? pms.default : '',
				defaultkw2 	= [];

			if ( '' != defaultkw ) {
				defaultkw2 = defaultkw.split("\n");
			}
			//console.log( defaultkw2 );

			el.tokenfield({
				tokens			: defaultkw, //[],
				autocomplete	: { // jQuery UI Autocomplete options
					//delay: 100,
					//source: ['red','blue','green','yellow','violet','brown','purple','black','white'],
					source: function (request, response) {
						var service = { client: 'psy', ds: '' };
						$.ajax({
							url: (window.location.protocol == 'https:' ? 'https' : 'http') + '://www.google.com/complete/search',
							dataType: 'jsonp',
							data: {
								q: request.term,
								nolabels: 't',
								client: service.client,
								ds: service.ds
							},
							success: function (data) {
								response($.map(data[1], function (item) {
									return {
										value: $("<span>")
											.html(item[0])
											.text()
									};
								}));
							}
						});
					}
				},
				delimiter		: ["\n"],
				limit			: 10, // Maximum number of tokens allowed. 0 = unlimited
				minLength		: 3, // Minimum length required for token value
				//showAutocompleteOnFocus: true
			});

			//$.each(defaultkw2, function(i) {
			//	el.tokenfield('createToken', defaultkw2[i]);
			//});
		}

		return {
			'load'				: load,
			'get_tokens'		: get_tokens,
			'add_token'			: add_token,
			'replace_tokens'	: replace_tokens,
			'html2tokens'		: html2tokens
		};
	})();


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
		"optimizePage"		: optimizePage,
		"multi_keywords"	: multi_keywords
    }

})(jQuery);

(function ($) {
	$.fn.googleSuggest = function (opts) {
	    opts = $.extend({
	        service: 'web',
	        secure: false
	    }, opts);

	    var services = {
	        youtube: {
	            client: 'youtube',
	            ds: 'yt'
	        },
	        books: {
	            client: 'books',
	            ds: 'bo'
	        },
	        products: {
	            client: 'products-cc',
	            ds: 'sh'
	        },
	        news: {
	            client: 'news-cc',
	            ds: 'n'
	        },
	        images: {
	            client: 'img',
	            ds: 'i'
	        },
	        web: {
	            client: 'psy',
	            ds: ''
	        },
	        recipes: {
	            client: 'psy',
	            ds: 'r'
	        }
	    }, service = services[opts.service];

	    opts.source = function (request, response) {
	        $.ajax({
	            url: (window.location.protocol == 'https:' ? 'https' : 'http') + '://www.google.com/complete/search',
	            dataType: 'jsonp',
	            data: {
	                q: request.term,
	                nolabels: 't',
	                client: service.client,
	                ds: service.ds
	            },
	            success: function (data) {
	                response($.map(data[1], function (item) {
	                    return {
	                        value: $("<span>")
	                            .html(item[0])
	                            .text()
	                    };
	                }));
	            }
	        });
	    };

	    return this.each(function () {
	        $(this)
	            .autocomplete(opts);
	    });
	}
})(jQuery);

(function($) {
	$.fn.extend( {
		pspLimitChars: function(charsLeftElement, maxLimit) {
			$(this).on("keyup focus", function() {
				countChars($(this), charsLeftElement);
			});
			function countChars(element, charsLeftElement) {
				if ( typeof element == 'undefined' || typeof element.val() == 'undefined' ) return false;

				maxLimit = maxLimit || parseInt( element.attr('maxlength') );
				var currentChars = element.val().length;
				if ( currentChars > maxLimit ) {
					//element.value = element.val( substr(0, maxLimit) );
					element.value = pspFreamwork.substr_utf8_bytes(element.val(), 0, maxLimit);
					currentChars = maxLimit;
				}
				charsLeftElement.html( maxLimit - currentChars );
			}
			countChars($(this), charsLeftElement);
		}
	} );
})(jQuery);