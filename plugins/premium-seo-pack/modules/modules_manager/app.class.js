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
	
	function triggers()
	{
		maincontainer.on('click', '.psp_read_more', function(e) {
			e.preventDefault();
			$('.psp_module_description').hide();
			$(this).parent().find('.psp_module_description').fadeIn();
		});
		
		maincontainer.on('click', '.psp_close_description', function(e) {
			$(this).parent().hide();
		});
	}

	// external usage
	return {
    	}
})(jQuery);
