/*
Document   :  Modules Manager
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspModulesManager = (function ($) {
	"use strict";
	
	// public
	var debug_level = 0;
	var maincontainer = null;
	var lightbox = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");
			lightbox = $("#psp-lightbox-overlay");

			triggers();
		});
	})();
	
	function get_user_modules() {
		pspFreamwork.to_ajax_loader( "Loading..." );

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, {
			'action' 		: 'pspCapabilities_changeUser',
			'user_role'		: $('select[name=psp-filter-user-roles]').val(),
			'debug_level'		: debug_level
		}, function(response) {

			if( response.status == 'valid' )
			{
				pspFreamwork.to_ajax_loader_close();
				$("#psp-table-ajax-response").html( response.html );
				
				pspFreamwork.init_custom_checkbox();
			}
			pspFreamwork.to_ajax_loader_close();

			$( "td" ).each(function() {
				if( $(this).has("i.checked" ).length ) {
					$(this).css("background", "#e4e4e4");
				}
				else {
					$(this).css("background", "#f6f6f6");
				}
			});

			
			return false;
		}, 'json');
	}
	
	function save_changes() {
		pspFreamwork.to_ajax_loader( "Loading..." );
		
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
		}
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, {
			'action' 		: 'pspCapabilities_saveChanges',
			'user_role'		: $('select[name=psp-filter-user-roles]').val(),
			'modules'		: ids,
			'debug_level'		: debug_level
		}, function(response) {

			if( response.status == 'valid' )
			{
				pspFreamwork.to_ajax_loader_close();
				$("#psp-table-ajax-response").html( response.html );
				pspFreamwork.init_custom_checkbox();
			}
			pspFreamwork.to_ajax_loader_close();
			return false;
		}, 'json');
	}
	
	function triggers()
	{
		maincontainer.on('change', 'select[name=psp-filter-user-roles]', function(e){
			e.preventDefault();

			get_user_modules();

		});
		
		maincontainer.on('click', 'input#psp-save-changes', function(e) {
			e.preventDefault();
			
			save_changes();
		});

		$( "td" ).each(function( index ) {
			var that = $(this);
			if( $( "i" ).hasClass('checked') ) {
				that.css("background", "#e4e4e4");
			}
			else {
				that.css("background", "#f6f6f6");
			}
		});

		maincontainer.on('click', '.psp-custom-checkbox i', function(e) {
			e.preventDefault();

			var that = $(this);
			
			if ( that.hasClass('checked') ) {
				that.parents('td').css("background", "#f6f6f6");
			}
			else {
				that.parents('td').css("background", "#e4e4e4");
			} 

		});


	}

	// external usage
	return {
    	}
})(jQuery);
