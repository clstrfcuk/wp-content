/*
Document   :  Social Stats
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspLinkBuilder = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var lightbox = null;
    var current_row = null;


	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");
			lightbox = $("#psp-lightbox-overlay");

			triggers();
		});
	})();
	
	function setFlagAdd(val) {
		localStorage.setItem('add_flag', val);
	}
	function getFlagAdd() {
		var myValue = localStorage.getItem( 'add_flag' );
    	if (myValue)
        	return myValue;
        return 0;
	}
	
	function showAddNewLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response-details, #link-title-details')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-title-upd')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-title-add')
			.css({'display': 'table'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
			pspFreamwork.row_loading(current_row, 'hide');
		});
	}
	
	function showUpdateLink()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response-details, #link-title-details')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-title-upd')
			.css({'display': 'table'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-title-add')
			.css({'display': 'none'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
			pspFreamwork.row_loading(current_row, 'hide');
		});
	}
	
	function showDetails()
	{
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response-details, #link-title-details')
			.css({'display': 'table'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2, #link-title-upd')
			.css({'display': 'none'});
		$('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response, #link-title-add')
			.css({'display': 'none'});

		lightbox.fadeIn('fast');
		
		lightbox.find("a.psp-close-btn").click(function(e){
			e.preventDefault();
			lightbox.fadeOut('fast');
			pspFreamwork.row_loading(current_row, 'hide');
		});
	}
	
	function getDetails( itemid )
	{
		pspFreamwork.to_ajax_loader( "Loading..." );
		pspFreamwork.row_loading(current_row, 'show');
			
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGetUpdateDataBuilder',
			'sub_action'	: 'get_details',
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				//pspFreamwork.to_ajax_loader_close();
				
				var r 			= response.data,
					$details 	= $('#psp-lightbox-seo-report-response-details'),

					phrase 		= typeof r.phrase != 'undefined' && r.phrase ? r.phrase : '',
					url 		= typeof r.url != 'undefined' && r.url ? r.url : '',
					title 		= typeof r.title != 'undefined' && r.title ? r.title : '',
					rel 		= typeof r.rel != 'undefined' && r.rel ? r.rel : '',
					target 		= typeof r.target != 'undefined' && r.target ? r.target : '',
					attr_title 	= typeof r.attr_title != 'undefined' && r.attr_title ? r.attr_title : '',
					maxreplace 	= r.max_replacements,
					maxreplace2 = maxreplace == -1 ? 'all' : maxreplace;

				$details.find('#details_text').text( phrase );
				$details.find('#details_url').text( url );
				$details.find('#details_title').text( title );
				$details.find('#details_rel').text( rel );
				$details.find('#details_target').text( target );
				$details.find('#details_attr_title').text( attr_title );
				$details.find('#details_max_replacements').text( maxreplace2 );

				showDetails();
			}
			pspFreamwork.to_ajax_loader_close();
			return false;

		}, 'json');
	}
	
	function addToBuilder( $form )
	{
		lightbox.fadeOut('fast');
		pspFreamwork.to_ajax_loader( "Loading..." );
		
		var url = $form.find('#new_url'),
			url_val = url.val();

		if ( ! url_val.match("^https?://") ) {
			url.val("http://" + url_val);
		}

		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspAddToBuilder" });
    	//data_save.push({ name: "sub_action", value: sub_action });
    	data_save.push({ name: "ajax_id", value: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val() });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: 0 });

		jQuery.post(ajaxurl, data_save, function(response) {
			if( response.status == 'valid' ) {
				//setFlagAdd(1);
				//pspFreamwork.to_ajax_loader_close();
				//window.location.reload();
				$("#psp-table-ajax-response").html( response.html );
			}
			pspFreamwork.to_ajax_loader_close();
			return false;
		}, 'json');
	}
	
	function getUpdateData( itemid ) {
		pspFreamwork.to_ajax_loader( "Loading..." );
		pspFreamwork.row_loading(current_row, 'show');

		jQuery.post(ajaxurl, {
			'action' 		: 'pspGetUpdateDataBuilder',
			'sub_action'	: 'get_details',
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {

			//pspFreamwork.row_loading(row, 'hide');
			if( response.status == 'valid' ){
				//pspFreamwork.to_ajax_loader_close();

				setUpdateForm( response.data );
				showUpdateLink();
			}
			pspFreamwork.to_ajax_loader_close();
			return false;
		}, 'json');
	}

	function setUpdateForm( data ) {
		var $form = $('.psp-update-link-form'),
			itemid = data.id,
			phrase = data.phrase,
			url = data.url,
			title = data.title,
			rel = data.rel,
			target = data.target,
			attr_title = data.attr_title,
			max_replacements = data.max_replacements;

		$form.find('input#upd-itemid').val( itemid ); //hidden field to indentify used row for update!
		$form.find('input#new_text2').val( phrase );
		$form.find('input#new_url2').val( url );
		$form.find('input#new_title2').val( title );
		$form.find('input#new_attr_title2').val( attr_title );
		$form.find('select#rel2').val( rel );
		$form.find('select#target2').val( target );
		$form.find('input#new_attr_title2').val( attr_title );
		$form.find('select#max_replacements2').val( max_replacements );
	}
	
	function updateToBuilder( itemid, sub_action )
	{
		var sub_action = sub_action || '';
		
		var $form = $('.psp-update-link-form');
		
		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspUpdateToBuilder" });
    	data_save.push({ name: "sub_action", value: sub_action });
    	data_save.push({ name: "ajax_id", value: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val() });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: itemid });
			
		lightbox.fadeOut('fast');
		pspFreamwork.to_ajax_loader( "Loading..." );
		//pspFreamwork.row_loading(current_row, 'show');
		
		jQuery.post(ajaxurl, data_save, function(response) {

			pspFreamwork.row_loading(current_row, 'hide');
			if( response.status == 'valid' ){
				//setFlagAdd(1);
				
				if ( sub_action == 'publish' ) ;
				else {
					//pspFreamwork.to_ajax_loader_close();
				}

				//window.location.reload();
				$("#psp-table-ajax-response").html( response.html );
			}
			pspFreamwork.to_ajax_loader_close();
			return false;
		}, 'json');
	}
	


	function doVerify( itemid, row )
	{
		pspFreamwork.to_ajax_loader( "Loading..." );
		pspFreamwork.row_loading(row, 'show');
			
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'pspGetUpdateDataBuilder',
			'sub_action'	: 'verify_posts',
			'ajax_id'		: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {

			pspFreamwork.row_loading(row, 'hide');
			if( response.status == 'valid' ){
				//pspFreamwork.to_ajax_loader_close();
				
				var hits 		= typeof response.data != 'undefined' && response.data ? response.data : '',
					$hits 		= row.find('.psp-hits');

				$hits.text( hits );

				$("#psp-table-ajax-response").html( response.html );
			}
			pspFreamwork.to_ajax_loader_close();
			return false;

		}, 'json');
	}

	function triggers()
	{
		// add form lightbox
		//if (getFlagAdd()==0) ;//showAddNewLink();
		//setFlagAdd(0);

		maincontainer.on("click", '#psp-do_add_new_link', function(e){
			e.preventDefault();
			showAddNewLink();
		});
		
		maincontainer.on('click', 'a.psp-btn-url-attributes-lightbox', function(e){
			e.preventDefault();

			var that 	= $(this),
				row = that.parents('tr').eq(0),
				itemID	= that.data('itemid');

			current_row = row;
			getDetails( itemID );
		});
		
		// add row - but first verify founds!
		$('body').on('click', ".psp-add-link-form input#psp-submit-to-builder", function(e){
			e.preventDefault();
			
			var $form = $('.psp-add-link-form'),
			phrase = $form.find('#new_text').val(),
			url = $form.find('#new_url').val(),
			title = $form.find('#new_title').val(),
			rel = $form.find('#rel').val(),
			target = $form.find('#target').val(),
			attr_title = $form.find('#new_attr_title').val(),
			max_replacements = $form.find('#max_replacements').val();
			
			//maybe some validation!
			if ($.trim(phrase)=='' || $.trim(url)=='') {
				swal('You didn\'t complete the necessary fields!', '', 'error');
				return false;
			}
			
			//verify founds!
			jQuery.post(ajaxurl, {
				'action' 		: 'pspGetHitsByPhrase',
				'phrase'		: $form.find('#new_text').val(),
				'debug_level'	: debug_level
			}, function(response) {
					if( response.status == 'valid' ){
						var $hitsText = $('.psp-add-link-form #psp-builder-text-hits');
						$hitsText.find('span').text( response.data );
						$hitsText.css( {'display': 'inline'} );

						var $new_hits = $form.find('#new_hits');
						$new_hits.val( response.data );
						if ( $new_hits.val()<=0 && ('no' == response.allow_future_linking) ) {
							swal('No possible occurences for the text you\'ve entered!');
							return false;
						}

						// add row
						addToBuilder( $form );
					}
					return false;
			}, 'json');
		});
		$('body').on('click', ".psp-add-link-form input#psp-builder-verify-hits", function(e){
			e.preventDefault();
			
			//verify founds!
			var $form = $('.psp-add-link-form');
			
			jQuery.post(ajaxurl, {
				'action' 		: 'pspGetHitsByPhrase',
				'phrase'		: $form.find('#new_text').val(),
				'debug_level'	: debug_level
			}, function(response) {
					if( response.status == 'valid' ){
						var $hitsText = $('.psp-add-link-form #psp-builder-text-hits');
						$hitsText.find('span').text( response.data );
						$hitsText.css( {'display': 'inline'} );
					}
					return false;
			}, 'json');
		});

		/*
		// delete row
		$('body').on('click', ".psp-do_item_delete", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid'),
				key = row.find('td').eq(3).text(),//.find('input').val(),
				url = row.find('td').eq(4).text();//.find('input').val();

			//row.find('code').eq(0).text()
			if(confirm('Delete row with ID = ' + id + ' from builder? This action can\'t be rollback!' )){
				deleteFromBuilder( id );
			}
		});
		*/
		
		// update row info
		$('body').on('click', ".psp-do_item_update", function(e){
			e.preventDefault();

			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid');

			current_row = row;
			getUpdateData( id );
		});
		$('body').on('click', ".psp-update-link-form input#psp-submit-to-builder2", function(e){
			e.preventDefault();

			var $form = $('.psp-update-link-form'),
			itemid = $form.find('input#upd-itemid').val(),
			title = $form.find('input#new_title2').val();
	
			
			updateToBuilder( itemid );
		});
		
		//all checkboxes are checked by default!
		//$('.psp-form .psp-table input.psp-item-checkbox').attr('checked', 'checked');

		// verify row
		$('body').on('click', ".psp-do_item_verify", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid');
				
			doVerify( id, row );
		});
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
    }
})(jQuery);
