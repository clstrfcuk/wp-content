( function( $ ) {
	'use strict';
	// Init jQuery Ui Tabs
	$( ".eacs-settings-tabs" ).tabs();

	$( '.eacs-get-pro' ).on( 'click', function() {
		swal({
	  		title: '<h2><span>Go</span> Premium',
	  		type: 'warning',
	  		html:
	    		'Purchase <b><a href="https://essential-addons.com/cornerstone/buy.php" target="_blank" rel="nofollow">premium version</a></b> to unlock these pro elements.',
	  		showCloseButton: true,
	  		showCancelButton: false,
	  		focusConfirm: true,
		});
	} );

	// Adding link id after the url
	$('.eacs-settings-tabs ul li a').click(function (e) {
		$(this).preventDefault();
		var tabUrl = $(this).attr( 'href' );
	   window.location.hash = tabUrl;
	   return false;
	});

	// Saving Data With Ajax Request
	$( 'form#eacs-settings' ).on( 'submit', function(e) {
		e.preventDefault();

		var logoCarousel 		= $( '#logo-carousel' ).attr( 'checked' ) ? 1 : 0;
		var postGrid 			= $( '#post-grid' ).attr( 'checked' ) ? 1 : 0;
		var postCarousel 		= $( '#post-carousel' ).attr( 'checked' ) ? 1 : 0;
		var productCarousel 	= $( '#product-carousel' ).attr( 'checked' ) ? 1 : 0;
		var productGrid 		= $( '#product-grid' ).attr( 'checked' ) ? 1 : 0;
		var teamMembers 		= $( '#team-members' ).attr( 'checked' ) ? 1 : 0;
		var testimonialSlider 	= $( '#testimonial-slider' ).attr( 'checked' ) ? 1 : 0;

		$.ajax( {
			url: settings.ajaxurl,
			type: 'post',
			data: { 
				action: 'save_settings_with_ajax', 
				logoCarousel: logoCarousel,
				postGrid: postGrid,
				postCarousel: postCarousel,
				productCarousel: productCarousel,
				productGrid: productGrid,
				teamMembers: teamMembers,
				testimonialSlider: testimonialSlider,
			},
			success: function( response ) {
				swal(
				  'Settings Saved!',
				  'Click OK to continue',
				  'success'
				);
			},
			error: function() {
				swal(
				  'Oops...',
				  'Something went wrong!',
				  'error'
				);
			}
		} );
		
	} );

} )( jQuery );
