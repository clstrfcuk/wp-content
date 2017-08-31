/*
    Document   :  aaFreamwork
    Created on :  August, 2013
    Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspFreamwork = (function ($) {
    "use strict";

	var t 			= null,
		ajaxBox 	= null,
		loading 	= null,
		in_loading_section = null,

		maincontainer 	= null,
		mainloading 	= null,
		lightbox 		= null,

		section		= 'dashboard', // menu main section
		subsection	= '', // menu sub-section
		subistab   	= '', // menu section tab (not sub-section)
		subsectgo	= '', // menu sub-section as for css class
		topMenu		= null,
		debug_level = 0;

	var upload_popup_parent = null;


	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
 
			t 			= $("div.psp" ),
			ajaxBox 	= t.find('#psp-ajax-response');
			topMenu 	= t.find('nav.psp-nav');

			// menu already setted
			if ( topMenu.find('>ul').length ) {
				var currentnav = topMenu.find('>ul').data('currentnav') || '';

				currentnav = $.trim(currentnav);
				if ( '' != currentnav ) {
					menuSetSection( currentnav );
				}
			}
 
	        // plugin depedencies if default!
	        if ( $("li#psp-nav-depedencies").length > 0 ) {
	        	section = 'depedencies';
	        }

			maincontainer = $("div.psp-content");
			mainloading = $("#psp-main-loading");
			lightbox = $("#psp-lightbox-overlay");
			
			triggers();
			fixLayoutHeight();
		});
	})();



    
	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+ d.toUTCString();
	    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var decodedCookie = decodeURIComponent(document.cookie);
	    var ca = decodedCookie.split(';');
	    for(var i = 0; i <ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}

	$(document).on('click', '.psp-section-collapse_menu', function(e){
		e.preventDefault();

		var psp_sidebar = $('.psp-sidebar');
		
		psp_sidebar.toggleClass('psp-sidebar-collapsed');

		var that = $(this),
			sidebar_collapsed = $('.psp-sidebar-collapsed');

		if( psp_sidebar.hasClass('psp-sidebar-collapsed') ) {

			if( getCookie('psp_sidebar_collapsed_active') != 'true' ) {
				setCookie('psp_sidebar_collapsed_active', 'true', 30);
				/*tooltip menu collapsed*/
				$('a[data-toggle=tipsy]').attr('rel', 'tipsy_menu');
				$('a[rel=tipsy_menu]').tipsy({fade: false, gravity: 'w', className: 'tipsy_menu'});
			}

			psp_sidebar.find('.logo').hide();
			psp_sidebar.find('#psp-collapsed-logo').show();
			that.find('i').addClass('psp-checks-arrow-right').removeClass('psp-checks-arrow-left');
		}else{
			if( getCookie('psp_sidebar_collapsed_active') == 'true' ) {
				setCookie('psp_sidebar_collapsed_active', 'true', -30);
				$('a[data-toggle=tipsy]').removeAttr('rel');
			}
			psp_sidebar.find('#psp-collapsed-logo').hide();
			psp_sidebar.find('.logo').show();
			that.find('i').addClass('psp-checks-arrow-left').removeClass('psp-checks-arrow-right');
		}

		psp_sidebar.find('li span.psp-sidebar-menu-item-title').toggle();
	});

	//:: LOADERS AJAX
	function ajax_loading( label ) 
	{
		// append loading
		loading = $('<div class="psp-loader-holder"><div class="psp-loader"></div> ' + ( label ) + '</div>');
		ajaxBox.html(loading);
	}

	function take_over_ajax_loader( label, target )
	{
		loading = $('<div class="psp-loader-holder-take-over"><div class="psp-loader"></div> ' + ( label ) + '</div>');
		
		if( typeof target != 'undefined' ) {
			target.append(loading);
		}else{
			t.append(loading);
	   }
	}

	function take_over_ajax_loader_close()
	{
		$('.psp-sidebar').css('display','table-cell');
		t.find(".psp-loader-holder-take-over").remove();
	}


	//:: PLUGIN MENU
	function menuSetSection( ss ) {
		if ( typeof ss !== 'undefined' ) {
			if ( $.trim(ss) != '' ) {
				section = $.trim(ss);
			}
		}

		var __tmp = section.indexOf('#');
		if ( __tmp == -1 ) {
		    subsection = '';
		} else { // found subsection block!
			subsection = section.substr( __tmp+1 );
			section = section.slice( 0, __tmp );
		}

		if ( subsection != '' ) {
			subistab = '';

			var __re = /tab:([0-9a-zA-Z_-]*)/gi; //new RegExp("tab:([0-9a-zA-Z_-]*)", "gi");
			if ( __re.test(subsection) ) {
				var __match = subsection.match(__re); //__re.exec(subsection); //null;
				subistab = typeof (__match[0]) != 'undefined' ? __match[0].replace('tab:', '') : '';
			}
		}

		subsectgo = '';
		if ( subsection != '' ) {
			subsectgo = '--' + subsection;
			if ( subistab != '' ) {
				subsectgo = '--' + subistab;
				switch (section) {
				}
			}
		}

		var ret = {
			'section'		: section,
			'subsection'	: subsection,
			'subistab'		: subistab,
			'subsectgo'		: subsectgo
		}
		//console.log( 'menuSetSection//', ret  );
		//console.log( 'global//menuSetSection//', section, subsection, subistab, subsectgo );
		return ret;
	}

	function menuTriggers()
	{
		if( getCookie('psp_sidebar_collapsed_active') == 'true' ) {
			$('.psp-section-collapse_menu').click();
		}

		// responsive???
		$('.psp-responsive-menu').toggle(function() {
			$('nav.psp-nav').show();
		}, function() {
			$('nav.psp-nav').hide();
		});

		// click on menu links
		topMenu.on("click", "a", function(e){
			var that = $(this),
				href = that.attr("href");

			var current_open = topMenu.find("li.active");

			if( !that.parent('li').eq(0).hasClass('active') ) {
				$('.psp-sidebar').hasClass('psp-sidebar-collapsed') ? that.parent("li").eq(0).find(".psp-sub-menu").show() : that.parent("li").eq(0).find(".psp-sub-menu").slideDown(250);
				that.parent("li").eq(0).addClass("active");
			}

			// is top menu item?
			if ( href == "javascript: void(0)" ) {
				// close previous open menu
				$('.psp-sidebar').hasClass('psp-sidebar-collapsed') ? current_open.find(".psp-sub-menu").hide() : current_open.find(".psp-sub-menu").slideUp(350);
				current_open.removeClass("active");
			}
		});

		/*topMenu.find('li').hover(function(){
			if( !$(this).hasClass('psp-section-collapse_menu') ) {
				$(this).addClass('hover_active');
				$(this).find('a').click();
			}
		}, function(){
			if( !$(this).hasClass('psp-section-collapse_menu') ) {
				if( !$(this).hasClass('hover_active') ) {
					$(this).find('a').click();
				}
			}
		});

		topMenu.on('mouseout', 'li ul', function() {
			$(this).parent('li').eq(0).removeClass('hover_active');//find('a').click();
		});*/
	}

	function menuActiveSection( callback ) {
		var callback = callback || false;

		if( !$('.psp-sidebar').hasClass('psp-sidebar-collapsed') ) {
			// find new current menu to become open
			var new_open = topMenu.find( '.psp-section-' + section + subsectgo );
			var in_submenu = new_open.parent('.psp-sub-menu');
			var in_subsubmenu = new_open.parent('.psp-sub-sub-menu');

			// check if is into a sub submenu
			if ( in_subsubmenu.size() > 0 ) {
				in_submenu = in_subsubmenu.parent('li').parent('.psp-sub-menu');
			}
			//console.log( 'menuActiveSection//', [new_open, in_submenu, in_subsubmenu] );

			// close previous open menu
			var current_open = topMenu.find("> li.active");
			if ( current_open != in_submenu.parent('li') ) {
				current_open.find(".psp-sub-menu").slideUp(250);
				current_open.removeClass("active").find('.active').removeClass("active");
			}

			// open current menu
			in_submenu.find('.active').removeClass('active');
			new_open.addClass('active');

			// check if is into a submenu
			if ( in_submenu.size() > 0 ) {
				if ( ! in_submenu.parent('li').hasClass('active') ) {
					in_submenu.slideDown(100);
				}
				in_submenu.parent('li').addClass('active');
			}

			// is dashboard?
			if ( section == 'dashboard' ) {
				topMenu.find(".psp-sub-menu").slideUp(250);
				topMenu.find('.active').removeClass('active');
				topMenu.find('li#psp-nav-' + section).addClass('active');
			}
		}

		// callback - subsection!
		if ( $.isArray(callback) && $.isFunction( callback[0] ) ) {
			if ( callback.length == 1 ) {
				callback[0]();
			}
			else if ( callback.length == 2 ) {
				callback[0]( callback[1] );
			}
		}
	}

	function makeRequest( callback )
	{
		// fix for duble loading of js function
		if( in_loading_section == section ){
			$('.psp-sidebar').css('display','table-cell');

			//if( !$('.psp-sidebar').hasClass('psp-sidebar-collapsed') ) {
				menuActiveSection( callback );
			//}
			return false;
		}
		in_loading_section = section;

		// do not expect the request if we are not into our ajax request pages
		if( ajaxBox.size() == 0 ) return false;

		ajax_loading( "Loading section: " + section );
		var data = {
			'action' 		: 'pspLoadSection',
			'section' 		: section,
			'subsection'	: subsection
		};
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'ok'){
				$("h1.psp-section-headline").html(response.headline);
				ajaxBox.html(response.html);
				
				makeTabs();
				if( !$('.psp-sidebar').hasClass('psp-sidebar-collapsed') ) {
					menuActiveSection( callback );
				}

				if( typeof pspDashboard != "undefined" ){
					pspDashboard.init();
				}

				multiselect_left2right();
				init_custom_checkbox();
				$('.psp-sidebar').css('display','table-cell');
			}
		}, 'json');
	}

	function importSEOData($btn)
	{
		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');
		// replace the save button value with loading message
		$btn.val('import settings ...').removeClass('blue').addClass('gray');
		if(theForm.length > 0) {
			
			// serialiaze the form and send to saving data
			var data_nb = {
				'action' 	: 'pspimportSEOData',
				'options' 	: theForm.serialize(),
				'from'		: theForm.find('#from').val(),
				'subaction' : 'nbres'
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data_nb, function(response) {

				var nbrows = response.nbposts; //parseInt( response.nbrows );

				if(response.status == 'valid' && nbrows > 0 ) {
					
					statusBoxHtml.removeClass('psp-error').addClass('psp-success').html(response.html).fadeIn();
					
					//importSEOData_loop($btn, 0, nbrows);
					importSEOData_loop($btn, 0);
				} else {

					statusBoxHtml.delay(15000).fadeOut();
				}
			}, 'json');
		}
	}
	
	//importSEOData_loop($btn, step, nbrows)
	function importSEOData_loop($btn, last_id) {

		//var step_increase = 10; // DEBUG

		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');

		//if ( nbrows <= step ) {
		if ( last_id == -1 ) {
		
			statusBoxHtml.delay(30000).fadeOut();

			// replace the save button value with default message
			$btn.val( value ).removeClass('gray').addClass('blue');

			//return false; // DEBUG!

			setTimeout(function(){
				window.location.reload();
			}, 30000);
			return true;
		}

		// serialiaze the form and send to saving data
		var data = {
			'action' 		: 'pspimportSEOData',
			'options' 		: theForm.serialize(),
			'from'			: theForm.find('#from').val(),
			//'step'			: step,
			//'rowsperstep'	: step_increase
			'last_id'		: last_id
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {

			var __oldResHtml = statusBoxHtml.html(),
				__newResHtml = __oldResHtml + '<br />' + response.html;
			if(response.status == 'valid'){
				statusBoxHtml.removeClass('psp-error').addClass('psp-success').html(__newResHtml).fadeIn();
			}else{
				statusBoxHtml.removeClass('psp-success').addClass('psp-error').html(__newResHtml).fadeIn();
			}

			last_id = response.last_id;

			//importSEOData_loop($btn, step + step_increase, nbrows);
			setTimeout(function() {
				importSEOData_loop($btn, last_id);
			}, 1000);
		}, 'json');
	}
	
	function installDefaultOptions($btn)
	{
		var theForm 		= $btn.parents('form').eq(0),
			value 			= $btn.val(),
			statusBoxHtml 	= theForm.find('div.psp-message');
		// replace the save button value with loading message
		$btn.val('installing default settings ...').removeClass('blue').addClass('gray');
		if(theForm.length > 0) {
			// serialiaze the form and send to saving data
			var data = {
				'action' 	: 'pspInstallDefaultOptions',
				'options' 	: theForm.serialize()
			};
			
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if(response.status == 'ok'){
					statusBoxHtml.addClass('psp-success').html(response.html).fadeIn().delay(3000).fadeOut();
					setTimeout(function(){
						var currentLoc 	= window.location.href,
							newLoc		= currentLoc.indexOf('#') > 0 ? currentLoc.replace(/#.*$/, '#modules_manager') : currentLoc + '#modules_manager';
						
						window.location.replace( newLoc );
						window.location.reload();
					}, 1000);
				}else{
					statusBoxHtml.addClass('psp-error').html(response.html).fadeIn().delay(13000).fadeOut();
				}
				// replace the save button value with default message
				$btn.val( value ).removeClass('gray').addClass('blue');
			}, 'json');
		}
	}
	
	function saveOptions($btn)
	{
		var theForm 				= $btn.parents('form').eq(0),
			  theForm_id			= theForm.attr('id'),
			  value 					= $btn.val(),
			  statusBoxHtml 	= theForm.find('div#psp-status-box');
		// replace the save button value with loading message
		$btn.val('saving setings ...').removeClass('green').addClass('gray');

		multiselect_left2right(true);
		
		var options       = theForm.serializeArray();
		//console.log( $.param( options ) ); return false;

		// Because serializeArray() ignores unset checkboxes and radio buttons, also empty selects
		var el            = { inputs: null, selects: null };
        el.inputs         = theForm.find('input[type=checkbox]:not(:checked)');
        el.selects        = theForm.find('select:not(:selected)');
        el.selects_m      = theForm.find('select[multiple]:not(:selected)');
        //for (var kk = 0, arr = ['inputs', 'selects'], len = arr.length; kk < len; kk++) {
        //    var vv = arr[kk], $vv = el[vv];
        
        for (var kk in el) {
        	if ( 'psp_title_meta_format' == theForm_id ) {
        		continue;
        	}
            if ( $.inArray(kk, ['selects_m']) > -1 ) {
                options = options.concat(el[kk].map(
                    function() {
                        return {"name": this.name, "value": this.value}
                    }).get()
                );
            }
        }
        //console.log( $.param( options ) ); return false;

		if(theForm.length > 0) {
			// serialiaze the form and send to saving data
			var data = {
				'action' 		: 'pspSaveOptions',
				'options' 		: $.param( options ), //theForm.serialize(),
				'opt_nosave'	: ['last_status', 'profile_last_status']
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function(response) {
				if(response.status == 'ok'){
					
					statusBoxHtml.addClass('psp-success').html(response.html).fadeIn().delay(3000).fadeOut();
					if(section == 'synchronization'){
						updateCron();
					}
					
					// special cases! - local seo
					if(section == 'local_seo'){ // refresh to view the saved slug!
						window.location.reload();
					}
				}
				// replace the save button value with default message
				$btn.val( value ).removeClass('gray').addClass('green');
			}, 'json');
		}
	}
	
	function moduleChangeStatus($btn)
	{
		var module		= $btn.attr('rel'),
			  value 			= $btn.text(),
			  the_status 	= $btn.hasClass('psp_activate') ? 'true' : 'false';

		// replace the save button value with loading message
		$btn.text('saving setings ...');

		var data = {
			'action' 		: 'pspModuleChangeStatus',
			'module' 		: $btn.attr('rel'),
			'the_status' 	: the_status
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'ok'){

				// title & meta format activation => save its options
				if ( 'title_meta_format' == module && 'true' == the_status ) {
					var currentLoc 	= window.location.href,
						newLoc		= currentLoc.indexOf('#') > 0
							? currentLoc.replace(/#.*$/, '#title_meta_format#tab:__tab1') : currentLoc + '#title_meta_format#tab:__tab1';
					
					window.location.replace( newLoc );

					function _check_loaded() {
						// _max_step & _interval => verify for maximum _interval * _max_step seconds ( mili seconds / 1000 )
						var _check_el 		= null,
							  _timer				= null,
							  _max_step		= 50,
							  _current_step = 0,
							  _interval			= 600; // in miliseconds

						function _check() {
							_timer = setTimeout(function() {
								_check_el = $('body .psp-saveOptions');
								_current_step++;

								if ( ! _check_el.length && _current_step < _max_step ) {
									_check();
								}
								else {
									clearTimeout( _timer );
									_timer = null;
									_check_el.trigger('click');
								}
							}, _interval);
						};
						_check();
					};
					_check_loaded();
				}
				else {
					window.location.reload();
				}
			}
		}, 'json');
	}
	
	function updateCron()
	{
		var data = {
			'action' 		: 'pspSyncUpdate'
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {}, 'json');
	}
	
	function fixLayoutHeight()
	{
		var win 			= $(window),
			pspWrapper 	= $(".psp-content"),
			minusHeight 	= 70,
			winHeight		= win.height();
			
		// show the freamwork wrapper and fix the height
		pspWrapper.css('height', parseInt(winHeight - minusHeight)).show();
		$("div#psp-ajax-response").css('min-height', parseInt(winHeight - minusHeight - 240)).show();

		$("#wpbody-content").css('padding-bottom', '40px');
		$("#wpfooter").css('border', 'none');
	}
	
	function activatePlugin( $that )
	{
		var requestData = {
			'ipc'	: $('#productKey').val(),
			'email'	: $('#yourEmail').val()
		};
		if(requestData.ipc == ""){
			swal('Please type your Item Purchase Code!');
			return false;
		}
		$that.replaceWith('Validating your IPC <em>( ' + ( requestData.ipc) + ' )</em>  and activating  Please be patient! (this action can take about <strong>10 seconds</strong>)');
		var data = {
			'action' 	: 'pspTryActivate',
			'ipc'		: requestData.ipc,
			'email'		: requestData.email
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			if(response.status == 'OK') {
				var currentLoc 	= window.location.href,
					newLoc		= currentLoc.indexOf('#') > 0 ? currentLoc.replace(/#.*$/, '#modules_manager') : currentLoc + '#modules_manager';
				
				window.location.replace( newLoc );
				window.location.reload();
			}
			else{
				swal(response.msg);
				return false;
			}
		}, 'json');
	}

	function ajax_list()
	{

		var make_request = function( action, params, callback ){
			take_over_ajax_loader('Loading...');

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, {
				'action' 		: 'pspAjaxList',
				'ajax_id'		: $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
				'sub_action'	: action,
				'params'		: params
			}, function(response) {

				if( response.status == 'valid' )
				{
					$("#psp-table-ajax-response").html( response.html );
					init_custom_checkbox();

					take_over_ajax_loader_close();

					//special cases
					var ajax_id = $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val();
					if ( 'pspSERPKeywords' == ajax_id ) {
						pspSERP.wait_time();
					}
					else if ( 'pspPageOptimization' == ajax_id ) {
						pspOnPageOptimization.multi_keywords.load({});
					}
				}
			}, 'json');
		}

		$(".psp-table-ajax-list").on('change', 'select[name=psp-post-per-page]', function(e){
			e.preventDefault();

			make_request( 'post_per_page', {
				'post_per_page' : $(this).val()
			} );
		})

		.on('change', 'select[name=psp-filter-post_type]', function(e){
			e.preventDefault();

			make_request( 'post_type', {
				'post_type' : $(this).val()
			} );
		})

		.on('click', 'a.psp-jump-page', function(e){
			e.preventDefault();

			make_request( 'paged', {
				'paged' : $(this).attr('href').replace('#paged=', '')
			} );
		})
		
		.on('click', '.psp-post_status-list a', function(e){
			e.preventDefault();

			make_request( 'post_status', {
				'post_status' : $(this).attr('href').replace('#post_status=', '')
			} );
		})

		.on('change', 'select.psp-filter-general_field', function(e){
			e.preventDefault();
			
			var $this       = $(this),
				filter_name = $this.data('filter_field'),
				filter_val  = $this.val();

			make_request( 'general_field', {
				'filter_name'    : filter_name,
				'filter_val'     : filter_val
			} );
		})
		
		.on('click', 'ul.psp-filter-general_field a', function(e){
			e.preventDefault();
 
			var $this       = $(this),
				$parent_ul  = $this.parents('ul').first(),
				filter_name = $parent_ul.data('filter_field'),
				filter_val  = $this.data('filter_val');

			make_request( 'general_field', {
				'filter_name'    : filter_name,
				'filter_val'     : filter_val
			} );
		})        
		
		.on('click', 'button[name=psp-search-btn]', function(e){
			e.preventDefault();

			make_request( 'search', {
				'search_text' : $(this).parent().find('#psp-search-text').val()
			} );
		});
	}
	
	function facebookAuthorizeApp()
	{
		$('body').on('click', ".psp-facebook-authorize-app", function(e){
			e.preventDefault();

			var $this = $(this),
				saveform = $this.data('saveform') || 'yes';
  
			var ajaxPms = {
				'action' 		: 'pspFacebookAuthorizeApp',
				'saveform'		: saveform
			};

			if ( typeof saveform != 'undefined' && saveform == 'yes' ) {
			var form = $this.parents('form').eq(0),
				client_id = form.find("#app_id").val(),
				client_secret = form.find("#app_secret").val();

			// Check if user has client ID and client secret key
			if( client_id == '' || client_secret == '' ){
				swal('Please add your Client ID / Secret for authorize your app.');
				return false;
			}

			ajaxPms.params = form.serialize()		
			}
  
			$.post(ajaxurl, ajaxPms, function(response) {
				if( response.status == 'valid' )
				{
					var newwindow = window.open( response.auth_url ,'Facebook Authorize App','height=400,width=550' );
				}
			}, 'json');

		});
	}
	
	function makeTabs()
	{
		// tabs
		$('ul.psp-tabs-header').each(function() {
			var child_tab = '', child_tab_s = '';

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');
			var $content_sub;

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $(this), __currentTab = $(this).find('li.tabsCurrent').attr('title');
			$active = $( $links.filter('[title="'+__currentTab+'"]')[0] || $links[0] );
			$active.addClass('active');

			// subtabs per tab!
			var __child_tab = makeTabs_subtabs( $active );
			child_tab = __child_tab.child_tab;
			if ( child_tab != '' ) child_tab_s = '.'+child_tab;
 
			$content = $( '.'+($active.attr('title')) );
			if ( child_tab != '' ) {
				$content_sub = $( '.'+($active.attr('title')) + child_tab_s );
			}

			// Hide the remaining content
			$links.not($active).each(function () {
				$( '.'+($(this).attr('title')) ).hide();
			});
			if ( child_tab != '' )
				$( '.'+($active.attr('title')) ).not( 'ul.subtabsHeader,'+child_tab_s ).hide();

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.removeClass('active');
				
				// subtabs per tab!
				var __child_tab = makeTabs_subtabs( $active );
				child_tab = __child_tab.child_tab;
				if ( child_tab != '' ) child_tab_s = '.'+child_tab;

				$content.hide();
				if ( child_tab != '' ) $content_sub.hide();

				// Update the variables with the new link and content
				__currentTab = $(this).attr('title');
				__tabsWrapper.find('li.tabsCurrent').attr('title', __currentTab);
				$active = $(this);
				
				// subtabs per tab!
				var __child_tab = makeTabs_subtabs( $active );
				child_tab = __child_tab.child_tab;
				if ( child_tab != '' ) child_tab_s = '.'+child_tab;

				$content = $( '.'+($(this).attr('title')) );
				if ( child_tab != '' )
					$content_sub = $( '.'+($(this).attr('title')) + child_tab_s );

				// Make the tab active.
				$active.addClass('active');
				if ( child_tab != '' ) $content_sub.show();
				else $content.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});
		
		// subtabs
		$('ul.subtabsHeader').each(function() {
			var parent_tab = $(this).data('parent'), parent_tab_s = '.'+parent_tab;

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active_sub, $content_sub, $links_sub = $(this).find('a');
 
			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $(this), __currentTab = $(this).find('li.tabsCurrent').attr('title');
			$active_sub = $( $links_sub.filter('[title="'+__currentTab+'"]')[0] || $links_sub[0] );
			$active_sub.addClass('active');
			$content_sub = $(parent_tab_s + '.'+($active_sub.attr('title')));
			
			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active_sub.removeClass('active');
				$content_sub.hide();

				// Update the variables with the new link and content
				__currentTab = $(this).attr('title');
				__tabsWrapper.find('li.tabsCurrent').attr('title', __currentTab);
				$active_sub = $(this);
				$content_sub = $( parent_tab_s + '.'+($(this).attr('title')) );

				// Make the tab active.
				$active_sub.addClass('active');
				$content_sub.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});
	}
	
	function makeTabs_subtabs( active_tab ) {
 
		var ret = { 'child_tab': "" };

		var $subtabsWrapper = $('ul.subtabsHeader').filter(function(i) {
			return ( $(this).data('parent') == active_tab.attr('title') );
		});

		$('ul.subtabsHeader').hide();
		if ( $subtabsWrapper.length > 0 ) {

			$subtabsWrapper.show();

			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $links = $subtabsWrapper.find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $subtabsWrapper, __currentTab = $subtabsWrapper.find('li.tabsCurrent').attr('title');
			$active = $( $links.filter('[title="'+__currentTab+'"]')[0] || $links[0] );
			$active.addClass('active');

			ret.child_tab = $active.attr('title');
		}
		return ret;
	}

	function send_to_editor()
	{
		if( window.send_to_editor != undefined ) {
			// store old send to editor function
			window.restore_send_to_editor = window.send_to_editor;	
		}

		window.send_to_editor = function(html){
			var thumb_id = $('img', html).attr('class').split('wp-image-');
			thumb_id = parseInt(thumb_id[1]);
			
			$.post(ajaxurl, {
				'action' : 'pspWPMediaUploadImage',
				'att_id' : thumb_id
			}, function(response) {
				if (response.status == 'valid') {
					
					var upload_box = upload_popup_parent.parents('.psp-upload-image-wp-box').eq(0);
					
					upload_box.find('input').val( thumb_id );
					
					var the_preview_box = upload_box.find('.upload_image_preview'),
						the_img = the_preview_box.find('img');
						
					the_img.attr('src', response.thumb );
					the_img.show();
					the_preview_box.show();
					upload_box.find('.psp-prev-buttons').show();
					upload_box.find(".upload_image_button_wp").hide();
				
				}
			}, 'json');
			
			tb_remove();
			
			if( window.restore_send_to_editor != undefined ) {
				// store old send to editor function
				window.restore_send_to_editor = window.send_to_editor;	
			}
		}
	}
	
	function removeWpUploadImage( $this )
	{
		var upload_box = $this.parents(".psp-upload-image-wp-box").eq(0);
		upload_box.find('input').val('');
		var the_preview_box = upload_box.find('.upload_image_preview'),
			the_img = the_preview_box.find('img');
			
		the_img.attr('src', '');
		the_img.hide();
		the_preview_box.hide();
		upload_box.find('.psp-prev-buttons').hide();
		upload_box.find(".upload_image_button_wp").fadeIn('fast');
	}
	
	function removeHelp()
	{
		$("#psp-help-container").remove();	
	}
	
	function showHelp( that )
	{
		removeHelp();
		var help_type = that.data('helptype');
        var html = $('<div class="psp-panel-widget" id="psp-help-container" />');
        html.append("<a href='#close' class='psp-button red' id='psp-close-help'>Close HELP</a>")
		if( help_type == 'remote' ){
			var url = that.data('url');
			var content_wrapper = $("#psp-content");
			
			html.append( '<iframe src="' + ( url ) + '" style="width:100%; height: 100%;border: 1px solid #d7d7d7;" frameborder="0"></iframe>' )
			
			content_wrapper.append(html);
		}
	}
	
	function multiselect_left2right( autselect ) {
		var $allListBtn = $('.multisel_l2r_btn');
		var autselect = autselect || false;
 
		if ( $allListBtn.length > 0 ) {
			$allListBtn.each(function(i, el) {
 
				var $this = $(el), $multisel_available = $this.prevAll('.psp-multiselect-available').find('select.multisel_l2r_available'), $multisel_selected = $this.prevAll('.psp-multiselect-selected').find('select.multisel_l2r_selected');
 
				if ( autselect ) {
					$multisel_selected.find('option').each(function() {
						$(this).prop('selected', true);
					});
					$multisel_available.find('option').each(function() {
						$(this).prop('selected', false);
					});
				} else {

				$this.on('click', '.moveright', function(e) {
					e.preventDefault();
					$multisel_available.find('option:selected').appendTo($multisel_selected);
				});
				$this.on('click', '.moverightall', function(e) {
					e.preventDefault();
					$multisel_available.find('option').appendTo($multisel_selected);
				});
				$this.on('click', '.moveleft', function(e) {
					e.preventDefault();
					$multisel_selected.find('option:selected').appendTo($multisel_available);
				});
				$this.on('click', '.moveleftall', function(e) {
					e.preventDefault();
					$multisel_selected.find('option').appendTo($multisel_available);
				});
				
				}
			});
		}
	}
	
	function hashChange()
	{
		if ( location.href.indexOf("psp#") != -1 ) {
			// Alerts every time the hash changes!
			if(location.hash != "") {

				menuSetSection( location.hash.replace("#", '') );

				if ( subistab != '' ) {
					makeRequest([
						function (s) {
							$('.psp-tabs-header').find('a[title="'+s+'"]').click();
						},
						subistab
					]);
				}
				else if ( subsection != '' ) {
					makeRequest([
						function (s) { scrollToElement( s ) },
							'#'+subsection
					]);
				}
				else {
					makeRequest();
				}
            }
			return false;
		}
		if ( location.href.indexOf("=psp") != -1 ) {
			makeRequest();
			return false;
		}
	}
	
	function init_custom_checkbox()
	{
		$('.psp-main input[type="checkbox"]').each(function() {
			var $this = $(this);
			
			if( !$this.prev().hasClass('psp-custom-checkbox') ) {
				$this.wrap('<div class="psp-custom-checkbox"></div>');
			}
		});
		
		$('.psp-custom-checkbox').each(function() {
			var $this = $(this);
			if( !$this.find('input[type="checkbox"]').hasClass('input-hidden') ) {
				$this.prepend('<i class="checkbox ' + ( $this.find('input[type="checkbox"]').is(':checked') ? 'checked' : '' ) + '"></i>');
				$this.find('input[type="checkbox"]').addClass('input-hidden').hide();
			}
		});
	}
	
	function check_checkbox(elm) 
	{
		var $this = elm;
		
		if( !$this.hasClass('checked') ) {
			$this.addClass('checked');
			$this.parent().find('input').prop('checked', true);
			$this.parent().find('input').attr('checked','checked');
		}else{
			$this.removeClass('checked');
			$this.parent().find('input').prop('checked', false);
			$this.parent().find('input').removeAttr('checked');
		}
	}
	
	function triggers()
	{
		menuTriggers();
		facebookAuthorizeApp();
		init_custom_checkbox();

		multikw_tabs.triggers();
		//multikw_tabs.load( '.psp-multikw' );
		
		$('body').on('click', '.psp-custom-checkbox .checkbox', function(e) {
			e.preventDefault();
			
			var $this = $(this);
			
			if( typeof $this.parent().find('input').attr('id') != 'undefined' && $this.parent().find('input').attr('id').search('check-all') > 0 ) {
				if( $this.hasClass('checked') ) {
					$(this).parents('table').find('.psp-custom-checkbox').each(function() {
						$(this).find('.checkbox').removeClass('checked');
						$(this).find('input').prop('checked', false);
						$(this).parent().find('input').removeAttr('checked');
					});
				}else{
					$(this).parents('table').find('.psp-custom-checkbox').each(function() {
						$(this).find('.checkbox').addClass('checked');
						$(this).find('input').prop('checked', true);
						$(this).parent().find('input').attr('checked','checked');
					});
				}
			}else{
				check_checkbox( $this );
			}
			
		});
		
		$('body').on('click', '.upload_image_button_wp, .change_image_button_wp', function(e) {
			e.preventDefault();
			upload_popup_parent = $(this);
			var win = $(window);
			
			send_to_editor();
		
			tb_show('Select image', 'media-upload.php?type=image&amp;height=' + ( parseInt(win.height() / 1.2) ) + '&amp;width=610&amp;post_id=0&amp;from=aaframework&amp;TB_iframe=true');
		});
		
		$('body').on('click', '.remove_image_button_wp', function(e) {
			e.preventDefault();
			
			removeWpUploadImage( $(this) );
		});
		
		if ( typeof jQuery.fn.tipsy != "undefined" ) { // verify tipsy plugin is defined in jQuery namespace!
			$('a.aa-tooltip').tipsy({
				gravity: 'e'
			});

			$('.psp-tooltip-trigger').tipsy({
				gravity: 'n'
			});
		}

		$(window).resize(function() {
			fixLayoutHeight();
		});
		
		$("body").on('mousemove', '.psp-loader-holder, .psp-loader-holder-take-over', function( event ) {
			
			var pageCoords = "( " + event.pageX + ", " + event.pageY + " )";
			var clientCoords = "( " + event.clientX + ", " + event.clientY + " )";
			var parent = $(this).parent();
			var parentPos = parent.position();

			if( parent.hasClass('psp-step') == true ){
				return true;
			}
			
			event.pageY = event.pageY - 85;
			if( typeof parent != 'undefined' && !parent.hasClass('psp') ) {
				event.pageY = event.pageY - ( parentPos.top + (parent.height() / 2) + 50 );
			}

			$(this).find(".psp-loader").css( 'margin-top', event.pageY + 'px' );
		});
		
		$('body').on('click', '.psp_activate_product', function(e) {
			e.preventDefault();
			activatePlugin($(this));
		});
		$('body').on('click', '.psp-saveOptions', function(e) {
			e.preventDefault();
			saveOptions($(this));
		});
		$('body').on('click', '.psp-installDefaultOptions', function(e) {
			e.preventDefault();
			installDefaultOptions($(this));
		});
		$('body').on('click', '.psp-ImportSEO', function(e) {
			e.preventDefault();
			importSEOData($(this));
		});
		$("body").on('click', '.psp-section-modules_manager a.psp_action_button', function(e) {
			e.preventDefault();
			moduleChangeStatus($(this));
		});

		$('body').on('click', 'ins.iCheck-helper', function(){
			var that = $(this),
				checkboxes = $('#psp-list-table-posts input.psp-item-checkbox');

			if( that.is(':checked') ){
				checkboxes.prop('checked', true);
				checkboxes.addClass('checked');
			}
			else{
				checkboxes.prop('checked', false);
				checkboxes.removeClass('checked');
			}
		});

		// Bind the hashchange event.
		$(window).on('hashchange', function(){
			hashChange();
		});
		hashChange();

		ajax_list();
		
		$("body").on('click', "a.psp-show-docs-shortcut", function(e){
        	e.preventDefault();
        	
        	$("a.psp-show-docs").click();
        });
        
		$("body").on('click', "a.psp-show-docs", function(e){
        	e.preventDefault();
        	 
        	showHelp( $(this) );
        });
        
         $("body").on('click', "a#psp-close-help", function(e){
        	e.preventDefault();
        	
        	removeHelp();
        });
        
        multiselect_left2right();




        /*
		$('body').on('click', 'input#psp-item-check-all', function(){
			var that = $(this),
				checkboxes = $('#psp-list-table-posts input.psp-item-checkbox');

			if( that.is(':checked') ){
				checkboxes.prop('checked', true);
			}
			else{
				checkboxes.prop('checked', false);
			}
		});
		*/

		$("body").on("click", "#psp-list-rows a", function(e){
			e.preventDefault();
			$(this).parent().find('table').toggle("slow");
		});

		// publish / unpublish row
		$('body').on('click', ".psp-do_item_publish", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id  = row.data('itemid');
				
			do_item_action( id, 'publish', row );
		});

		// delete row       
		$('body').on('click', ".psp-do_item_delete", function(e){
			e.preventDefault();
			var that = $(this),
				row = that.parents('tr').eq(0),
				id  = row.data('itemid');

			swal({
				title: "Are you sure?",
				text: 'Delete row with ID# '+id+' ? This action cannot be rollback !',
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#cb3c46",
				confirmButtonText: "Yes, delete it!",
				closeOnConfirm: false
			},
			function(){
				swal("Deleted!", "The selected rows have been deleted.", "success");
				do_item_action( id, 'delete', row );
			});

		});
		
		$('body').on('click', '#psp-do_bulk_delete_rows', function(e){
			e.preventDefault();

			swal({
				title: "Are you sure?",
				text: 'Are you sure you want to delete the selected rows ? This action cannot be rollback !',
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#cb3c46",
				confirmButtonText: "Yes, delete it!",
				closeOnConfirm: false
			},
			function(){
				swal("Deleted!", "The selected rows have been deleted", "success");
				do_bulk_delete_rows();
			});
				
		});
		
		//all checkboxes are checked by default!
		$('.psp-form .psp-table input.psp-item-checkbox').attr('checked', 'checked');
				
		// inline edit
		inline_edit();
    }


	function do_item_action( itemid, sub_action, row )
	{
		var sub_action = sub_action || '';

		lightbox.fadeOut('fast');
		//mainloading.fadeIn('fast');
		take_over_ajax_loader( "Loading..." );
		row_loading(row, 'show');
		
		jQuery.post(ajaxurl, {
			'action'        : 'pspAjaxList_actions',
			'itemid'        : itemid,
			'sub_action'    : sub_action,
			'ajax_id'       : $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
			'debug_level'   : debug_level
		}, function(response) {

			row_loading(row, 'hide');
			if( response.status == 'valid' ){
				//mainloading.fadeOut('fast');
				take_over_ajax_loader_close();
				//window.location.reload();
				$("#psp-table-ajax-response").html( response.html );

				init_custom_checkbox();
				return false;
			}
			//mainloading.fadeOut('fast');
			take_over_ajax_loader_close();
			swal('Problems occured while trying to execute action: '+sub_action+'!' , '' , 'error');
		}, 'json');
	}

	function do_bulk_delete_rows() {
		var ids = [], __ck = $('.psp-form .psp-table input.psp-item-checkbox:checked');
		__ck.each(function (k, v) {
			ids[k] = $(this).attr('name').replace('psp-item-checkbox-', '');
		});
		ids = ids.join(',');
		if (ids.length<=0) {
			swal('You didn\'t select any rows!' , '', 'error');
			return false;
		}
		
		lightbox.fadeOut('fast');
		//mainloading.fadeIn('fast');
		take_over_ajax_loader( "Loading..." );

		jQuery.post(ajaxurl, {
			'action'        : 'pspAjaxList_actions',
			'id'            : ids,
			'sub_action'    : 'bulk_delete',
			'ajax_id'       : $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
			'debug_level'   : debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				//mainloading.fadeOut('fast');
				take_over_ajax_loader_close();
				//window.location.reload();
				$("#psp-table-ajax-response").html( response.html );

				init_custom_checkbox();
				return false;
			}
			//mainloading.fadeOut('fast');
			take_over_ajax_loader_close();
			swal('Problems occured while trying to execute action: '+'bulk_delete_rows'+'!', '', 'error');
		}, 'json');
	}

	// inline edit fields
	var inline_edit = function() {

		function make_request( pms ) {
			var pms         = pms || {},
				replace     = misc.hasOwnProperty( pms, 'replace' ) ? pms.replace : null,
				itemid      = misc.hasOwnProperty( pms, 'itemid' ) ? pms.itemid : 0,
				table       = misc.hasOwnProperty( pms, 'table' ) ? pms.table : '',
				field       = misc.hasOwnProperty( pms, 'field' ) ? pms.field : '',
				new_val     = misc.hasOwnProperty( pms, 'new_val' ) ? pms.new_val : '',
				el_type     = misc.hasOwnProperty( pms, 'el_type' ) ? pms.el_type : '',
				new_text    = misc.hasOwnProperty( pms, 'new_text' ) ? pms.new_text : '';
				
			//console.log( row, itemid, field_name, field_value ); return false;             
			loading( replace, 'show' );

			jQuery.post(ajaxurl, {
				'action'        : 'pspAjaxList_actions',
				'itemid'        : itemid,
				'sub_action'    : 'edit_inline',
				'table'         : table,
				'field_name'    : field,
				'field_value'   : new_val,
				'ajax_id'       : $(".psp-table-ajax-list").find('.psp-ajax-list-table-id').val(),
				'debug_level'   : debug_level

			}, function(response) {

				loading( replace, 'close' );
				var orig     = replace.prev('.psp-edit-inline'),
					just_new = 'input' == el_type ? new_val : new_text;
				orig.html( just_new );

				// success
				if( response.status == 'valid' ){
					replace.hide();
					orig.show();
					return false;
				}

				// error
				replace.hide();
				orig.show();
				//alert('Problems occured while trying to execute action: '+sub_action+'!');

			}, 'json');
		};
		
		function loading( row, status ) {
			if ( 'close' == status ) {
				row.find('i.psp-edit-inline-loading').remove();
			}
			else {
				row.prepend( $('<i class="psp-edit-inline-loading psp-icon-content_spinner" />') );
			}
		};

		$(document).on(
			{
				mouseenter: function(e) {
					$(this).addClass('psp-edit-inline-hover');
				},
				mouseleave: function(e) {
					$(this).removeClass('psp-edit-inline-hover');
				}
			},
			'.psp-edit-inline'
		);

		$(document).on('click', '.psp-edit-inline', function(e) {
			var that    = $(this),
				replace = that.next('.psp-edit-inline-replace');
				
			that.hide();
			replace.show().focus();
			replace.find('input,select').focus();
		});

		function change_and_blur(e) {
			var that = $(this);
			clearTimeout(change_and_blur.timeout);
			change_and_blur.timeout = null;
			change_and_blur.timeout = setTimeout(function(){
				__();
			}, 200);
 
			function __() {
				//var that        = $(this);
				var parent      = that.parent(),
					row         = that.parents('tr').first(),
					itemid      = row.data('itemid'),
					table       = parent.data('table'),
					field       = that.prop('name').replace('psp-edit-inline[', '').replace(']', ''),
					new_val     = that.val(),
					el_type     = e.target.tagName.toLowerCase(),
					new_text    = 'select' == el_type ? that.find('option:selected').text() : '';
	 
				make_request({
					'replace'       : parent,
					'itemid'        : itemid,
					'table'         : table,
					'field'         : field,
					'new_val'       : new_val,
					'el_type'       : el_type,
					'new_text'      : new_text 
				});
			}
		}
		// $(document).on('change', '.psp-edit-inline-replace input, .psp-edit-inline-replace select', change_and_blur);
		$(document).on('blur', '.psp-edit-inline-replace input, .psp-edit-inline-replace select', change_and_blur);
	};
    
	/* Multi Keywords - sub tabs */
	var multikw_tabs = (function() {
		var mkwtabs = {
			main		: null,
			preload		: null,
			box			: null,
			boxmenu		: null,
			boxcontent	: null
		};

		function triggers() {
			$('body').on('click', '.psp-multikw .psp-multikw-tab-menu a', function(e) {
				e.preventDefault();

				var that 			= $(this);

				mkwtabs.main 		= that.parents(".psp-multikw:first");
				if ( mkwtabs.main && mkwtabs.main.length ) {
					mkwtabs.preload 	= mkwtabs.main.find(".psp-multikw-meta-box-preload:first");
					mkwtabs.box		 	= mkwtabs.main.find(".psp-multikw-meta-box-container:first");
				}
				if ( mkwtabs.box && mkwtabs.box.length ) {
					mkwtabs.boxmenu 	= mkwtabs.box.find(".psp-multikw-tab-menu:first");
					mkwtabs.boxcontent 	= mkwtabs.box.find(".psp-multikw-tab-container:first");
				}
				//console.log( 'admin', mkwtabs );

				if ( mkwtabs.box && mkwtabs.box.length ) ;
				else return false;

				var open 			= mkwtabs.boxmenu.find("a.open"),
					href 			= that.attr('href').replace('#', '');

				mkwtabs.box.hide();

				mkwtabs.boxcontent.find("#psp-tab-div-id-" + href ).show();

				// close current opened tab
				var rel_open = open.attr('href').replace('#', '');

				mkwtabs.boxcontent.find("#psp-tab-div-id-" + rel_open ).hide();

				mkwtabs.preload.show();
				mkwtabs.preload.hide();

				mkwtabs.box.fadeIn('fast');

				open.removeClass('open');
				that.addClass('open');
			});
		}

		function load( container ) {
			if ( container && container.length ) {
				mkwtabs.preload 	= container.find(".psp-multikw-meta-box-preload:first");
				mkwtabs.box 		= container.find('.psp-multikw-meta-box-container:first');
				//console.log( 'admin', mkwtabs );

				mkwtabs.preload.hide();
				mkwtabs.box.fadeIn('fast');
			}
		}

		return {
			'triggers'	: triggers,
			'load'		: load
		};
	})();

    function scrollToElement(selector, time, verticalOffset) 
    {
    	time = typeof(time) != 'undefined' ? time : 1000;
    	verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;

    	var element = jQuery(selector);
    	if ( element.length <= 0 ) return false;

    	var offset = element.offset();
    	var offsetTop = parseInt( parseInt(offset.top) + parseInt(verticalOffset) );
    	$('html, body').animate({
    		scrollTop: offsetTop
    	}, time);
    }

    // UTF8 / UTF-8 related!
    function encode_utf8( s )
    {
    	return unescape( encodeURIComponent( s ) );
    }
    function substr_utf8_bytes(str, startInBytes, lengthInBytes) {

    	/* this function scans a multibyte string and returns a substring.
    	 * arguments are start position and length, both defined in bytes.
    	 *
    	 * this is tricky, because javascript only allows character level
    	 * and not byte level access on strings. Also, all strings are stored
    	 * in utf-16 internally - so we need to convert characters to utf-8
    	 * to detect their length in utf-8 encoding.
    	 *
    	 * the startInBytes and lengthInBytes parameters are based on byte
    	 * positions in a utf-8 encoded string.
    	 * in utf-8, for example:
    	 *       "a" is 1 byte,
    	 "ü" is 2 byte,
    	 and  "你" is 3 byte.
    	 *
    	 * NOTE:
    	 * according to ECMAScript 262 all strings are stored as a sequence
    	 * of 16-bit characters. so we need a encode_utf8() function to safely
    	 * detect the length our character would have in a utf8 representation.
    	 *
    	 * http://www.ecma-international.org/publications/files/ecma-st/ECMA-262.pdf
    	 * see "4.3.16 String Value":
    	 * > Although each value usually represents a single 16-bit unit of
    	 * > UTF-16 text, the language does not place any restrictions or
    	 * > requirements on the values except that they be 16-bit unsigned
    	 * > integers.
    	 */

    	var resultStr = '';
    	var startInChars = 0;

    	// scan string forward to find index of first character
    	// (convert start position in byte to start position in characters)

    	var ch;
    	for (var bytePos = 0; bytePos < startInBytes; startInChars++) {

    		// get numeric code of character (is >128 for multibyte character)
    		// and increase "bytePos" for each byte of the character sequence

    		ch = str.charCodeAt(startInChars);
    		bytePos += (ch < 128) ? 1 : encode_utf8(str[startInChars]).length;
    	}

    	// now that we have the position of the starting character,
    	// we can built the resulting substring

    	// as we don't know the end position in chars yet, we start with a mix of
    	// chars and bytes. we decrease "end" by the byte count of each selected
    	// character to end up in the right position
    	var end = startInChars + lengthInBytes - 1;

    	for (var n = startInChars; startInChars <= end; n++) {
    		// get numeric code of character (is >128 for multibyte character)
    		// and decrease "end" for each byte of the character sequence
    		ch = str.charCodeAt(n);
    		end -= (ch < 128) ? 1 : encode_utf8(str[n]).length;

    		resultStr += str[n];
    	}

    	return resultStr;
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

    // external usage
	return {
		'scrollToElement'			: scrollToElement,
		'substr_utf8_bytes' 		: substr_utf8_bytes,
		'makeTabs'					: makeTabs,
		'to_ajax_loader'        	: take_over_ajax_loader,
		'to_ajax_loader_close'  	: take_over_ajax_loader_close,
		'init_custom_checkbox'		: init_custom_checkbox,
		'multiselect_left2right'	: multiselect_left2right,
		'multikw_tabs_load'			: multikw_tabs.load,
		'row_loading'				: row_loading
    }
    
})(jQuery);


function psp_humanFileSize(bytes, si) {
    var thresh = si ? 1000 : 1024;
    if(Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }
    var units = si
        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    } while(Math.abs(bytes) >= thresh && u < units.length - 1);
    return bytes.toFixed(1)+' '+units[u];
}