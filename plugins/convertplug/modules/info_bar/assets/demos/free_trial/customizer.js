
jQuery(document).ready(function(){
	
	//	1. Add CSS file of this individual style
	var css_file = '/free_trial/free_trial.min.css';
	jQuery('head').append('<link rel="stylesheet" href="' + info_bar.demo_dir + css_file + '" type="text/css" />');

	// do the stuff to customize the element upon the action "smile_data_received"
	jQuery(this).on('smile_data_received',function(e,data){

			//close image settings
			cp_info_bar_close_img_settings(data);

			//animation setup
			cp_info_bar_animation_setup(data);

			//for infobar position
			cp_info_bar_position_setup(data);

			//form setup
			cp_ifb_form_setup(data);	

			// setup all editors
			cp_editor_setup(data);

			//toggle_functionality
			cp_toggle_button(data);

			//infobar image 
			cp_infobar_infobar_image(data);

			// add cp-empty class to empty containers
			jQuery.each( cp_empty_classes , function( key, value) {
				if( jQuery(value).length !== 0 ) {
					cp_add_empty_class(key,value);
				}	
			});

			// blinking cursor  
			cp_blinking_cursor('.cp-info-bar-msg');
	});
});