/*
Document   :  HTML Validation W3C
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
pspHTMLValidation = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var IDs = [];
    var loaded_page = 0;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");
			loading = maincontainer.find("#main-loading");

			triggers();
		});
	})();

	function verifyPage( id, row, callback )
	{
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'pspHtmlValidate',
			'id'			: id,
			'debug_level'	: debug_level
		}, function(response) {

			var nr_of_errors 	= misc.hasOwnProperty(response, 'nr_of_errors') ? response.nr_of_errors : '-',
				nr_of_warning 	= misc.hasOwnProperty(response, 'nr_of_warning') ? response.nr_of_warning : '-',
				last_check_at 	= misc.hasOwnProperty(response, 'last_check_at') ? response.last_check_at : 'Never Checked';

			var current_status 	= misc.hasOwnProperty(response, 'status') ? response.status : '-';
			if ( misc.hasOwnProperty(response, 'msg') ) {
				if ( $.trim(response.msg) != '' ) {
					current_status = response.msg;
				}
			}

			if ( response.status == 'invalid' ) {
				row.find('strong.status').css('color', 'red');
			} else if( response.status == 'valid' ) {
				row.find('strong.status').css('color', 'green');
			}
			row.find('strong.status').text( current_status );

			row.find('i.nr_of_errors').text( nr_of_errors );
			row.find('i.nr_of_warning').text( nr_of_warning );
			row.find('i.last_check_at').text( last_check_at );

			row_loading(row, 'hide');

			if( typeof callback == "function" ){
				callback();
			}
		}, 'json');
	}

	function verifyAllPages()
	{
		// get all pages IDs
		var allPages = $(".psp-table tbody tr");
		if( allPages.size() > 0 ){
			allPages.each(function(key, value) {
				IDs.push( $(value).data('itemid'));
			});
		}

		if( IDs.length > 0 ){
			tailPageVerify(0);
		}
	}

	function tailPageVerify( verify_step )
	{
		var page_id = IDs[verify_step],
			row 	= $("tr[data-itemid='" + page_id + "']");

		row_loading(row, 'show');

		// increse the loaded products marker
		++loaded_page;

		verifyPage( page_id, row, function(){
			// continue insert the rest of page_id
			if( IDs.length > verify_step ) {
				tailPageVerify( ++verify_step );
			}
		} );

	}

	function row_loading( row, status )
	{
		if( status == 'show' ){
			if( row.size() > 0 ){
				if( row.find('.psp-row-loading-marker').size() == 0 ){
					var row_loading_box = $('<div class="psp-row-loading-marker"><div class="psp-row-loading"><div class="psp-meter psp-animate" style="width:30%; margin: 10px 0px 0px 30%;"><span style="width:100%"></span></div></div></div>')
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

	function triggers()
	{
		maincontainer.on('click', 'input.psp-do_item_html_validation', function(e){
			e.preventDefault();

			var that 	= $(this),
				row 	= that.parents("tr").eq(0),
				itemID	= row.data('itemid'),
				title   = row.find('input#psp-item-title-' + itemID);

			row_loading(row, 'show');

			if( $.trim(title.val()) == "" ){

				row_loading(row, 'hide');
				swal('Your post doesn\'t have a title.'); return false;
			}

			verifyPage(itemID, row);
		});

		maincontainer.on('click', '#psp-do_bulk_html_validation', function(){
			var that 	= $(this);

			verifyAllPages();
		});
	}

	// :: MISC
	var misc = {

		hasOwnProperty: function(obj, prop) {
			var proto = obj.__proto__ || obj.constructor.prototype;
			return (prop in obj) &&
			(!(prop in proto) || proto[prop] !== obj[prop]);
		}

	};

	// external usage
	return {
		"verifyPage": verifyPage
    }
})(jQuery);
