<?php

// action to add templates
function cp_add_slide_in_template( $args, $preset, $module ) {

	if( $module == 'slide_in' ) {

		$modal_temp_array = array (

			"fashion" =>
				array (
					"optin", // theme slug for template
					"Fashion", // template name
					plugins_url('assets/demos/optin/optin.html',__FILE__), // HTML file for template
					"http://downloads.brainstormforce.com/convertplug/presets/screenshot_fashion.png", // screen shot
					plugins_url('assets/demos/optin/customizer.js',__FILE__), // customizer js for template
					"All,Offers", // categories
					"Shortcode,Canvas,HTML,Custom", // tags
					"fashion" // template unique slug
			),
			"free_checklist" =>
				array (
					"optin_widget", // theme slug for template
					"Free Checklist", // template name
					plugins_url('assets/demos/optin_widget/optin_widget.html',__FILE__), // HTML file for template
					"http://downloads.brainstormforce.com/convertplug/presets/screenshot_checklist.png", // screen shot
					plugins_url('assets/demos/optin_widget/customizer.js',__FILE__), // customizer js for template
					"All,slide in", // categories
					"Shortcode,Canvas,HTML,Custom", // tags
					"free_checklist" // template unique slug
			),
			"free_audit" =>
				array (
					"optin", // theme slug for template
					"Free Audit", // template name
					plugins_url('assets/demos/optin/optin.html',__FILE__), // HTML file for template
					"http://downloads.brainstormforce.com/convertplug/presets/screenshot_audit.png", // screen shot
					plugins_url('assets/demos/optin/customizer.js',__FILE__), // customizer js for template
					"All,slide in", // categories
					"Shortcode,Canvas,HTML,Custom", // tags
					"free_audit" // template unique slug
			),
			"upcoming_event_in_new_york!" =>
				array (
					"optin", // theme slug for template
					"Upcoming Event In New York", // template name
					plugins_url('assets/demos/optin/optin.html',__FILE__), // HTML file for template
					"http://downloads.brainstormforce.com/convertplug/presets/screenshot_events.png", // screen shot
					plugins_url('assets/demos/optin/customizer.js',__FILE__), // customizer js for template
					"All,slide in", // categories
					"Shortcode,Canvas,HTML,Custom", // tags
					"upcoming_event_in_new_york!" // template unique slug
			),

			"apartment_finder" =>
				array (
					"optin_widget", // theme slug for template
					"Apartment Finder", // template name
					plugins_url('assets/demos/optin_widget/optin_widget.html',__FILE__), // HTML file for template
					"http://downloads.brainstormforce.com/convertplug/presets/screenshot_apartement.png", // screen shot
					plugins_url('assets/demos/optin_widget/customizer.js',__FILE__), // customizer js for template
					"All,slide in", // categories
					"Shortcode,Canvas,HTML,Custom", // tags
					"apartment_finder" // template unique slug
			),
		);

		if( $preset  !== '' ) {
			$temp_arr = $modal_temp_array[$preset];
			$modal_temp_array = array();
			$modal_temp_array[$preset] = $temp_arr;
			$args = array_merge( $args, $modal_temp_array );
		} else {
			$args = array_merge( $args, $modal_temp_array );
		}
	}

	return $args;
}
