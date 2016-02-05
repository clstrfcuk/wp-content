<?php
if(function_exists("smile_framework_add_options")){

	$cp_settings = get_option('convert_plug_settings');
	$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '3000';
	$style = isset( $_GET['style'] ) ? $_GET['style'] : '';
	smile_framework_add_options('Smile_Info_Bars',"newsletter",
		array(
			"style_name" 		=> "Newsletter",
			"demo_url"			=> plugins_url("../../assets/demos/newsletter/newsletter.html",__FILE__),
			"img_url"			=> plugins_url("../../assets/demos/newsletter/newsletter.png",__FILE__),
			"customizer_js"		=> plugins_url("../../assets/demos/newsletter/customizer.js",__FILE__),
			"category"          => "All,Optins",
			"tags"              => "form",
			"options"			=> array()
		)
	);
}
