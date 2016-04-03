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

    function validate_it( current_ele, value ) {
        if( !value.trim() ) {
            return true;
        } else if( current_ele.hasClass('cp-email') ) {
            if( !isValidEmailAddress( value ) ) {
                return true;
            }
            else {
                return false;
            }
        } else if( current_ele.hasClass('cp-textfeild') ) {
            if( /^[a-zA-Z0-9- ]*$/.test( value ) == false ) {
                return true;
            } else {
                return false;
            }
        }
        
        return false;
    }

	function slide_in_process_cp_form(t) {

		var form 			= jQuery(t),
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

		// Check for required fields are not empty
		// And create query strings to send to redirect URL after form submission
        var query_string = '';
        var submit_status = true;
        form.find('.cp-input').each( function(index) {
            var $this = jQuery(this);
            
            if( ! $this.hasClass('cp-submit-button')) { // Check condition for Submit Button
                var    input_name = $this.attr('name'),
                    input_value = $this.val();

                var res = input_name.replace(/param/gi, function myFunction(x){return ''; });
                res = res.replace('[','');
                res = res.replace(']','');

                query_string += ( index != 0 ) ? "&" : '';
                query_string += res+"="+input_value ;

                var input_required = $this.attr('required') ? true : false;

                if( input_required ) {    
                    if( validate_it( $this, input_value ) ) {
                        submit_status = false;
                        $this.addClass('cp-input-error');
                    } else {
                        $this.removeClass('cp-input-error');
                    }
                }
            }
        });

		//	All form fields Validation
        var fail = false;
        var fail_log = '';
        form.find( 'select, textarea, input' ).each(function(i, el ){
            if( jQuery( el ).prop( 'required' )){

                if ( ! jQuery( el ).val() ) {
                    fail = true;
                    jQuery( el ).addClass('cp-error');
                    name = jQuery( el ).attr( 'name' );
                    fail_log += name + " is required \n";
                } else {
                	//	Client side email Validation
                	//	If not empty value, Then validate email
                	if( jQuery( el ).hasClass('cp-email') ) {
		    			var email = jQuery( el ).val();

		    			if( isValidEmailAddress( email ) ) {
			    			jQuery( el ).removeClass('cp-error');
			    			fail = false;
			    		} else {
			    			jQuery( el ).addClass('cp-error');
			    			fail = true;
			    			var name = jQuery( el ).attr( 'name' ) || '';
			    			console.log( name + " is required \n" );
			    		}
		    		} else {
                		jQuery( el ).removeClass('cp-error');
		    		}
                }
            }
        });

        //submit if fail never got set to true
        if ( fail ) {
            console.log( fail_log );
        } else {

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
		
		jQuery('.cp-slidein-popup-container').find('#smile-optin-form').each(function(index, el) {

			// enter key press
			jQuery(el).find("input").keypress(function(event) {
			    if ( event.which == 13 ) {
			        event.preventDefault();
			        var check_sucess = jQuery(this).parents(".cp-animate-container").hasClass('cp-form-submit-success');
			        var check_error = jQuery(this).parents(".cp-animate-container").hasClass('cp-form-submit-error');
			       
			        if(!check_sucess){
			        	slide_in_process_cp_form( el );
			    	}		    
			    }
			});

			// submit add subscriber request
		    jQuery(el).find('.btn-subscribe').click(function(e){
				e.preventDefault;
				if( !jQuery(this).hasClass('disabled') ){
					slide_in_process_cp_form( el );
					jQuery(document).trigger("si_conversion_done",[this]);

					//	Redirect after conversion
					var redirect_link 			= jQuery(this).attr('data-redirect-link') || '';
					var redirect_link_target	= jQuery(this).attr('data-redirect-link-target') || '_blank';
					if( redirect_link != 'undefined' && redirect_link != '' ) {
						window.open( redirect_link , redirect_link_target );
					}
				}
				e.preventDefault();
			});

		});

	});
	
})( jQuery );