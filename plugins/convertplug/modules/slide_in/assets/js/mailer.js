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

	function slide_in_process_cp_form(t) {

		var form 			= jQuery(t).parents(".cp-slidein-body").find("#smile-optin-form"),
			data 			= form.serialize(),
			info_container  = jQuery(t).parents(".cp-animate-container").find('.cp-msg-on-submit'),
			form_container  = jQuery(t).parents(".cp-slidein-body").find('.cp-form-container'),
			spinner  		= jQuery(t).parents(".cp-animate-container").find('.cp-form-processing'),
			slidein 			= jQuery(t).parents(".slidein-overlay"),
			cp_form_processing_wrap = jQuery(t).parents(".cp-animate-container").find('.cp-form-processing-wrap'),
			cp_animate_container    = jQuery(t).parents(".cp-animate-container"),
			cp_tooltip    			=  slidein.find(".cp-tooltip-icon").data('classes');
					
		var cookieTime 		= slidein.data('conversion-cookie-time');
		var cookieName 		= slidein.data('slidein-id');
		var dont_close 		= jQuery(t).parents(".slidein-overlay").hasClass("do_not_close");
		var redirectdata 	= jQuery(t).parents(".slidein-overlay").data("redirect-lead-data");

		//	check it is required or not
		var email = form.find('.cp-email').attr('required') ? true : false;
		var name = form.find('.cp-name').attr('required') ? true : false;
		var status_email = true;
		var status_name = true;

		//to pass data with redirect URL
		var query_string ='';
		var string_email = escape(form.find('.cp-email').val());
		var string_name = escape(form.find('.cp-name').val() || '');
		if( string_name != '' ) {
			query_string = "username="+string_name+"&email="+string_email ;
		} else {
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
			cp_form_processing_wrap.show();

			info_container.fadeOut(120, function() {
			    jQuery(this).show().css({visibility: "hidden"});
			});

			// Show processing spinner
			spinner.hide().css({visibility: "visible"}).fadeIn(100);
			
			jQuery.ajax({
				url: smile_ajax.url,
				data: data,
				type: 'POST',
				dataType: 'HTML',
				success: function(result){

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
					if( typeof obj.message != 'undefined' && obj.message != null ) {
						info_container.hide().css({visibility: "visible"}).fadeIn(120);
						info_container.html( '<div class="cp-m-'+cls+'">'+obj.message+'</div>' );
						cp_animate_container.addClass('cp-form-submit-'+cls);
					}
					
					if(typeof obj.action !== 'undefined' && obj.action != null){
						
						//	Show processing spinner
						spinner.fadeOut(100, function() {
						    jQuery(this).show().css({visibility: "hidden"});
						});

						//	Hide error/success message
						info_container.hide().css({visibility: "visible"}).fadeIn(120);

						if( cls === 'success' ) {
							
							//hide tool tip 	
							jQuery('head').append('<style class="cp-tooltip-css">.tip.'+cp_tooltip+'{display:none }</style>');
							
							// 	Redirect if status is [success]
							if( obj.action === 'redirect' ) {
								cp_form_processing_wrap.hide();
								slidein.hide();
								var url =obj.url;
								var urlstring ='';
								if (url.indexOf("?") > -1) {
								    urlstring = '&';
								} else {
									urlstring = '?';
								}
								
								var redirect_url = url+urlstring+decodeURI(query_string);
								if( redirectdata == 1 ){
									window.location = redirect_url;
								} else {
									window.location = obj.url;
								}
							} else {
								cp_form_processing_wrap.show();
							}

							if(dont_close){
								setTimeout(function(){
						           jQuery(document).trigger('closeSlideIn',[slidein]);
						           
						         },3000);
							}							
						} else {
							//form_container.show();	
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
		
		jQuery('.cp-slidein-body #smile-optin-form').each(function(index, el) {

			// enter key press
			jQuery(el).find("input").keypress(function(event) {
			    if ( event.which == 13 ) {
			        event.preventDefault();
			        var check_sucess = jQuery(this).parents(".cp-animate-container").hasClass('cp-form-submit-success');
			        var check_error = jQuery(this).parents(".cp-animate-container").hasClass('cp-form-submit-error');
			       
			        if(!check_sucess){
			        	slide_in_process_cp_form(this);
			    	}		    
			    }
			});

		    // submit add subscriber request
		    jQuery('.cp-slidein-body').find('button.btn-subscribe').click(function(e){
				e.preventDefault;
				slide_in_process_cp_form(this);
			});
		});

	});
	
})( jQuery );