(function( $ ) {
	"use strict";
	    // Sets cookies.
	var createCookie = function(name, value, days){

		// If we have a days value, set it in the expiry of the cookie.
		if ( days ) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			var expires = '; expires=' + date.toGMTString();
		} else {
			var expires = '';
		}

		// Write the cookie.
		document.cookie = name + '=' + value + expires + '; path=/';
	}

	//	Email validation
	function isValidEmailAddress(emailAddress) {
	    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	    return pattern.test(emailAddress);
	};

	// Retrieves cookies.
	var getCookie = function(name){
		var nameEQ = name + '=';
		var ca = document.cookie.split(';');
		for ( var i = 0; i < ca.length; i++ ) {
			var c = ca[i];
			while ( c.charAt(0) == ' ' ) {
				c = c.substring(1, c.length);
			}
			if ( c.indexOf(nameEQ) == 0 ) {
				return c.substring(nameEQ.length, c.length);
			}
		}

		return null;
	}

	function ib_process_cp_form(t) {

		var form 						= jQuery(t).parents(".ib-form-container").find("#smile-ib-optin-form"),
			data 						= form.serialize(),
			info_container  			= jQuery(t).parents(".global_info_bar_container").find('.cp-msg-on-submit'),
			form_container  			= jQuery(t).parents(".global_info_bar_container").find('.cp-form-container'),
			spinner  					= jQuery(t).parents(".global_info_bar_container").find('.cp-form-processing'),
			info_bar 					= jQuery(t).parents(".global_info_bar_container"),
			cp_form_processing_wrap 	= jQuery(t).parents(".global_info_bar_container").find('.cp-form-processing-wrap'),
			cp_animate_container    	= jQuery(t).parents(".global_info_bar_container"),
			cp_tooltip    				= info_bar.find(".cp-tooltip-icon").data('classes'),
			close_div 					= jQuery(".ib-close");


		var cookieTime 					= info_bar.data('conversion-cookie-time');
		var cookieName 					= info_bar.data('info_bar-id');
		var redirectdata 				= jQuery(t).parents(".global_info_bar_container").data("redirect-lead-data");

		//	check it is required or not
		var email = form.find('.cp-email').attr('required') ? true : false;
		var name = form.find('.cp-name').attr('required') ? true : false;
		var status_email = true;
		var status_name = true;

		//to pass data with redirect URL
		var query_string ='';
		var string_email = escape(form.find('.cp-email').val());
		var string_name = escape(form.find('.cp-name').val() || '');
		if(string_name!=''){
			query_string = "username="+string_name+"&email="+string_email ;
		}else{
			query_string = "email="+string_email ;
		}

		// Email - is required?
		if( email ) {
			var ev = form.find('.cp-email').val();

			if( !isValidEmailAddress( ev ) ) {
				status_email = false;
				form.find('.cp-email').addClass('cp-error');
			} else {
				form.find('.cp-email').removeClass('cp-error');
				status_email = true;
			}
		}

		// Name - is required?
		if( name ) {
			var ev = form.find('.cp-name').val() || '';

			if( ev === '' ) {
				status_name = false;
				form.find('.cp-name').addClass('cp-error');
			} else if(/^[a-zA-Z0-9- ]*$/.test(ev) == false) {
			    status_name = false;
				form.find('.cp-name').addClass('cp-error');
			} else {
				form.find('.cp-name').removeClass('cp-error');
				status_name = true;
			}
		}

		if( status_email && status_name ) {
			//form_container.hide();				//	Hide form
			cp_form_processing_wrap.show();

			//info_container.fadeOut(100);		//	Hide error/success message
			info_container.fadeOut(120, function() {
			    jQuery(this).show().css({visibility: "hidden"});
			    close_div.show().css({visibility: "hidden"});
			});

			//spinner.fadeIn(100);				//	Show processing spinner
			spinner.hide().css({visibility: "visible"}).fadeIn(100);

			jQuery.ajax({
				url: smile_ajax.url,
				data: data,
				type: 'POST',
				dataType: 'HTML',
				success: function(result){

					jQuery(document).trigger("cp_conversion_done",[this]);
					if(cookieTime) {
						createCookie(cookieName,true,cookieTime);
					}

					var obj = jQuery.parseJSON( result );
					var cls = '';
					if( typeof obj.status != 'undefined' && obj.status != null ) {
						cls = obj.status;
					}

					//	is valid - Email MX Record
					if( obj.email_status ) {
						form.find('.cp-email').removeClass('cp-error');
					} else {
						form.find('.cp-email').addClass('cp-error');
						form.find('.cp-email').focus();
					}

					//	show message error/success
					if(typeof obj.message != 'undefined' && obj.message != null) {
						info_container.hide().css({visibility: "visible"}).fadeIn(120);
						close_div.hide().css({visibility:  "visible"}).fadeIn(120);
						info_container.html( '<div class="cp-m-'+cls+'">'+obj.message+'</div>' );
						cp_animate_container.addClass('cp-form-submit-'+cls);
					}
					
					
					if(typeof obj.action !== 'undefined' && obj.action != null){
						
						spinner.fadeOut(100, function() {
						    jQuery(this).show().css({visibility: "hidden"});
						});

						info_container.hide().css({visibility: "visible"}).fadeIn(120);
						close_div.hide().css({visibility:  "visible"}).fadeIn(120);


						if( cls === 'success' ) {

							//	Complete the Conversion.
							jQuery(document).trigger("ib_conversion_done",[this]);

							//hide tooltip
							jQuery('head').append('<style class="cp-tooltip-css">.tip.'+cp_tooltip+'{display:none }</style>');

							// 	Redirect if status is [success]
							if(obj.action === 'redirect' ) {
								cp_form_processing_wrap.hide();
								info_bar.hide();
								var url =obj.url;
								var urlstring ='';
								if (url.indexOf("?") > -1) {
								    urlstring = '&';
								}else{
									urlstring = '?';
								}

								var redirect_url = url + urlstring + decodeURI(query_string);
								if(redirectdata == 1){
									window.location = redirect_url;
								}else{
									window.location = obj.url;

								}
							} else{

								cp_form_processing_wrap.show();
								
								// if button contains anchor tag then redirect to that url 
								if( ( jQuery(t).find('a').length > 0 ) ) {
									var redirect_src = jQuery(t).find('a').attr('href');
									var redirect_target = jQuery(t).find('a').attr('target');									
									if(redirect_target == '' || typeof redirect_target == 'undefined'){
										redirect_target ='_self';
									}
									if( redirect_src != '' || redirect_src != '#' ) {
										window.open( redirect_src,redirect_target );
									}
								}

							}
						}
					}
				},
				error: function(data){
					//	Show form & Hide processing spinner
					cp_form_processing_wrap.hide();
					spinner.fadeOut(100, function() {
						jQuery(this).show().css({visibility: "hidden"});
					});
		        }
			});
		}
	}

	jQuery(document).ready(function(){

		jQuery('#smile-ib-optin-form').each(function(index, el) {

			// enter key press
			jQuery(el).find("input").keypress(function(event) {
			    if (event.which == 13) {
			        event.preventDefault();
			        ib_process_cp_form(this);
			    }
			});

		    // submit add subscriber request
		    jQuery('.ib-subscribe').click(function(e){
				e.preventDefault;
				if( !jQuery(this).hasClass('disabled') ){
					ib_process_cp_form(this);
				}
				e.preventDefault();
			});
		});

		// Close error message on click of message
		jQuery(document).on("click", ".cp-form-submit-error", function(e){

			var cp_form_processing_wrap = jQuery(this).find(".cp-form-processing-wrap") ,
				cp_tooltip              =  jQuery(this).find(".cp-tooltip-icon").data('classes'),
				cp_msg_on_submit        = jQuery(this).find(".cp-msg-on-submit"),
				cp_form_processing      = jQuery(this).find(".cp-form-processing");

			cp_form_processing_wrap.hide();
			jQuery(this).removeClass('cp-form-submit-error');
			cp_msg_on_submit.html('');
			cp_msg_on_submit.removeAttr("style");
			// cp_form_processing.reset();
			
			//show tooltip
			jQuery('head').append('<style class="cp-tooltip-css">.tip.'+cp_tooltip+'{display:block }</style>');

		});

	});

})( jQuery );