/*
Document   :  Social Stats
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspLinkRedirect = (function ($) {
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

			jQuery('.psp-last-check-status span').tipsy({live: true, gravity: 'w'});
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

		if_rule_regexp( $('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response .psp-redirect-rule-sel').val() );
	}
	
	function showUpdateLink()
	{
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

		if_rule_regexp( $('#psp-lightbox-overlay').find('#psp-lightbox-seo-report-response2 .psp-redirect-rule-sel').val() );
	}
	
	function addToBuilder( $form, force_save )
	{
		var force_save = force_save || false;

		//lightbox.fadeOut('fast');
		pspFreamwork.to_ajax_loader( "Loading..." );
		
		var url = $form.find('#new_url'), 
			url_val = url.val(),
			url_redirect = $form.find('#new_url_redirect'),
			url_redirect_val = url_redirect.val(),
			redirect_rule = $form.find('#redirect_rule').val();

		if ( 'regexp' != redirect_rule ) {
			if ( ! url_val.match("^https?://") ) {
				url.val("http://" + url_val);
			}
			if ( ! url_redirect_val.match("^https?://") ) {
				url_redirect.val("http://" + url_redirect_val);
			}
		}

		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspAddToRedirect" });
    	//data_save.push({ name: "sub_action", value: sub_action });
    	data_save.push({ name: "ajax_id", value: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val() });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: 0 });

    	if ( force_save ) {
    		data_save.push({ name: "force_save", value: "yes" });
    	}

		jQuery.post(ajaxurl, data_save, function(response) {

			if ( response.status == 'invalid' ) {
				pspFreamwork.to_ajax_loader_close();

				if ( misc.hasOwnProperty(response, 'can_force_save') ) {
					if ( 'yes' == response.can_force_save ) {
						if ( confirm( response.msg + ' Are you sure you want to add it?') ) {
							addToBuilder( $form, true );
							return false;
						}
						else {
							return false;
						}
					}
				}
				swal( response.msg );
				return false;
			}

			lightbox.fadeOut('fast');

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
			'action' 		: 'pspGetUpdateDataRedirect',
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
			url = data.url,
			url_redirect = data.url_redirect,
			redirect_type = data.redirect_type,
			redirect_rule = data.redirect_rule;

		$form.find('input#upd-itemid').val( itemid ); //hidden field to indentify used row for update!
		$form.find('input#new_url2').val( url );
		$form.find('input#new_url_redirect2').val( url_redirect );
		$form.find('select#redirect_type2').val( redirect_type );
		$form.find('select#redirect_rule2').val( redirect_rule );
	}
	
	function updateToBuilder( itemid, sub_action, force_save )
	{
		var sub_action = sub_action || '';
		var force_save = force_save || false;

		var $form 	= $('.psp-update-link-form');
		//var row 	= find_current_row( $form, itemid );

		var data_save = $form.serializeArray();
    	data_save.push({ name: "action", value: "pspUpdateToRedirect" });
    	data_save.push({ name: "sub_action", value: sub_action });
    	data_save.push({ name: "ajax_id", value: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val() });
    	data_save.push({ name: "debug_level", value: debug_level });
    	data_save.push({ name: "itemid", value: itemid });

    	data_save.push({ name: "new_url2", value: $form.find('input#new_url2').val() });

    	if ( force_save ) {
    		data_save.push({ name: "force_save", value: "yes" });
    	}
			
		//lightbox.fadeOut('fast');
		pspFreamwork.to_ajax_loader( "Loading..." );
		//pspFreamwork.row_loading(current_row, 'show');
		
		jQuery.post(ajaxurl, data_save, function(response) {

			if ( response.status == 'invalid' ) {
				pspFreamwork.to_ajax_loader_close();

				if ( misc.hasOwnProperty(response, 'can_force_save') ) {
					if ( 'yes' == response.can_force_save ) {
						if ( confirm( response.msg + ' Are you sure you want to update it?') ) {
							updateToBuilder( itemid, sub_action, true );
							return false;
						}
						else {
							return false;
						}
					}
				}
				swal( response.msg );
				return false;
			}

			lightbox.fadeOut('fast');
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
			'action' 		: 'pspGetUpdateDataRedirect',
			'sub_action'	: 'get_status_code',
			'ajax_id'		: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
			'itemid'		: itemid,
			'debug_level'	: debug_level
		}, function(response) {

			pspFreamwork.row_loading(row, 'hide');
			//if( response.status == 'valid' ){
			//}
			
			$("#psp-table-ajax-response").html( response.html );

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
		
		// add row
		$('body').on('click', ".psp-add-link-form input#psp-submit-to-builder", function(e){
			e.preventDefault();
			
			var $form = $('.psp-add-link-form'),
			url = $form.find('#new_url').val(),
			url_redirect = $form.find('#new_url_redirect').val();

			//maybe some validation!
			url = $.trim(url);
			url_redirect = $.trim(url_redirect);
			if (url=='' || url_redirect=='') {
				swal('You didn\'t complete the necessary fields!', '', 'error');
				return false;
			}
			if (url == url_redirect) {
				swal('URL & URL Redirect fields are identical!', '', 'error');
				return false;
			}

			addToBuilder( $form );
		});
		

		
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
			url_redirect = $form.find('input#new_url_redirect2').val();
	
			//maybe some validation!
			if ($.trim(url_redirect)=='') {
				swal('You didn\'t complete the necessary fields!', '', 'error');
				return false;
			}
			updateToBuilder( itemid );
		});



		// verify row
		$('body').on('click', ".psp-do_item_verify", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id	= row.data('itemid');
				
			doVerify( id, row );
		});

		// redirect rule
		$('body').on('click', ".psp-redirect-rule-sel", function(e){
			e.preventDefault();
			var that = $(this),
				$form = that.parents('form').eq(0);
				
			if_rule_regexp( that.val() );
		});
	}

	function find_current_row( $form, itemid ) {
		var $table = $form.parents('.psp-content').eq(0).find('#psp-table-ajax-response > table'),
			$rows = $table.find('> tr');

		var row = null;
		$rows.each(function(i) {
			if ( $(this).data('itemid') == itemid ) {
				row = $(this);
			}
		});
		return row;
	}

	function if_rule_regexp( redirect_rule ) {
		var redirect_rule = redirect_rule || '';

		if ( 'regexp' != redirect_rule ) {
			$('.psp-use-regexp-redirects-notice').hide();
			return false;
		}

		$('.psp-use-regexp-redirects-notice').show();
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
