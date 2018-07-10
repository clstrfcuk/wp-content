/*
Document   :  Social Stats
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspAlexaRank = (function ($) {
	"use strict";

	// public
	var debug_level = 0;
	var maincontainer = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");
			
			triggers();
		});
	})();

	function triggers()
	{
		// Datepicker (range)
		$( "#psp-filter-by-date-from" ).datepicker({
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: "yy-mm-dd",
			onClose: function( selectedDate ) {
				$( "#psp-filter-by-date-to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		
		$( "#psp-filter-by-date-to" ).datepicker({
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: "yy-mm-dd",
			onClose: function( selectedDate ) {
				$( "#psp-filter-by-date-from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	}

	// external usage
	return {
	}
})(jQuery);
