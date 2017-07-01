<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function __pspNotifyEngine_localseo( $engine='google', $action='default' ) {
	global $psp;
	
	$req['action'] = $action;
	
	$sitemap_type = 'xml';
	if ( $req['action'] == 'getStatus' ) {
		$notifyStatus = $psp->get_theoption('psp_localseo_engine_notify');
		if ( $notifyStatus === false || !isset($notifyStatus["$engine"]) || !isset($notifyStatus["$engine"]["$sitemap_type"]) )
			return '';
		return $notifyStatus["$engine"]["$sitemap_type"]["msg_html"];
	}

	$html = array();
	
	$html[] = '<div class="psp-form-row psp-notify-engine-ping psp-notify-' . $engine . '">';

	if ( $engine == 'google' ) {
		$html[] = '<div class="">' . sprintf( __('Notify Google: you can check statistics on <a href="%s" target="_blank">Google Webmaster Tools</a>', 'psp'), 'http://www.google.com/webmasters/tools/' ). '</div>';
	} else if ( $engine == 'bing' ) {
		$html[] = '<div class="">' . sprintf( __('Notify Bing: you can check statistics on <a href="%s" target="_blank">Bing Webmaster Tools</a>', 'psp'), 'http://www.bing.com/toolbox/webmaster' ). '</div>';
	}

	$html[] = '<input type="button" class="psp-form-button psp-form-button-info psp-button blue" style="width: 160px;" id="psp-notify-' . $engine . '" value="' . ( __('Notify '.ucfirst($engine), 'psp') ) . '">
	<span style="margin:0px 0px 0px 10px" class="response">' . __pspNotifyEngine_localseo( $engine, 'getStatus' ) . '</span>';

	$html[] = '</div>';

	// view page button
	ob_start();
?>
	<script>
	(function($) {
		var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>',
		engine = '<?php echo $engine; ?>';

		$("body").on("click", "#psp-notify-"+engine, function(){

			$.post(ajaxurl, {
				'action' 		: 'pspAdminAjax',
				'sub_action'	: 'localseo_notify',
				'sitemap_type'	: 'xml',
				'engine'		: engine
			}, function(response) {

				var $box = $('.psp-notify-'+engine), $res = $box.find('.response');
				$res.html( response.msg_html );
				if ( response.status == 'valid' )
					return true;
				return false;
			}, 'json');
		});
   	})(jQuery);
	</script>
<?php
	$__js = ob_get_contents();
	ob_end_clean();
	$html[] = $__js;

	return implode( "\n", $html );
}

function __pspLocalSeo_custom_slug() {
	global $psp;

	$settings = $psp->getAllSettings( 'array', 'local_seo' );
	$slug = isset($settings['slug']) && !empty($settings['slug']) ? $settings['slug'] : 'psplocation';
	return $slug;
}

global $psp;

echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'local_seo' => array(
				'title' 	=> __('Local SEO', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(

                    'help_locations' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Locations & shortcodes</h3>
                            <p>Settings available for locations & shortcodes (schema.org valid)</p>
                        ', 'psp')
                    )

					,'validators_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="panel-body psp-panel-body psp-form-row">
								<label class="psp-form-label" for="site-items">' . __('Validators', 'psp') . '</label>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://www.google.com/webmasters/tools/richsnippets" style="position: relative;bottom: -6px;">Google Rich Snippets Testing Tool</a>
								</div>
								<!--<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://www.ebusiness-unibw.org/tools/goodrelations-validator/" style="position: relative;bottom: -6px;">GoodRelations Validator</a>
								</div>-->
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://linter.structured-data.org/" style="position: relative;bottom: -6px;">Structured Data Linter</a>
								</div>
							</div>'
					)
					,'xmlsitemap_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="panel-body psp-panel-body psp-form-row">
								<label class="psp-form-label" for="site-items">' . __('Locations sitemap', 'psp') . '</label>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-locations.xml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-locations.xml') ) . '</a>
								</div>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-locations.kml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-locations.kml') ) . '</a>
								</div>
							</div>'
					)
					,'notify_google' => array(
						'type' => 'html',
						'html' => __pspNotifyEngine_localseo( 'google' )
					)
					
					,'slug' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'psplocation',
						'size' 		=> 'small',
						'force_width'=> '350',
						'title' 	=> __('Slug: ', 'psp'),
						'desc' 		=> sprintf( __('Custom Slug for your location pages. The slug must be unique and cannot be used anywhere else in your website.
						<br/>You can edit your locations here: <a href="%s" target="_blank">%s</a>.
						<br/>Default slug is <em><u>psplocation</u></em>, but you\'ve change it to <em><u>%s</u></em>.
						<br/>All your locations can be found here: <a href="%s" target="_blank">%s</a>.', 'psp'), admin_url('/edit.php?post_type=psp_locations'), admin_url('/edit.php?post_type=psp_locations'), __pspLocalSeo_custom_slug(), home_url('/' . __pspLocalSeo_custom_slug() . '/'), home_url('/' . __pspLocalSeo_custom_slug() . '/') )
					)
					
					,'address_format' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{street} {city}, {state} {zipcode} {country}',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Address Format: ', 'psp'),
						'desc' 		=> __('You can use the following tags: {street} {city}, {state} {zipcode} {country}. This format is used for locations kml sitemap generation (building address snippet) and for address shortcode. <!--Also {street} is included first by default and {country} is included last by default in this format and you must not include them.-->', 'psp')
					)
					
                    ,'help_google_map_api' => array(
                        'type'      => 'message',
                        'status'    => 'info',
                        'html'      => __('
                            <h3 style="margin: 0px 0px 5px 0px;">Google Map API</h3>
                            <p>Get Google Map API Keys (they are used for your google map shortcodes)</p>
                        ', 'psp')
                    )

					// https://developers.google.com/maps/documentation/javascript/tutorial?hl=en#api_key
					// https://developers.google.com/maps/documentation/static-maps/get-api-key
					,'google_map_api_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Google Map Static API Key:', 'psp'),
						'desc' 		=> __('If you use a shortcode for a Static MAP:
						<br /><a href="https://developers.google.com/maps/documentation/static-maps/get-api-key" target="_blank">How to Get a Google Maps API Console Key (it is a Browser API key)</a>
						<br /><a href="https://developers.google.com/maps/documentation/static-maps/usage-limits" target="_blank">Google Maps API Usage Limits</a>
						<br /><img width="826" height="425" class="aligncenter size-full wp-image-81" alt="wpcron-cpanel" src="{plugin_folder_uri}assets/psp-localseo-static-getkey.png">', 'psp')
					)
					,'google_map_api_key_js' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Google Maps JS API Key:', 'psp'),
						'desc' 		=> __('If you use a shortcode for a Dynamic (Javascript) MAP:
						<br /><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">How to Get a Google Maps API Console Key (it is a Browser API key)</a>
	 					<br /><a href="https://developers.google.com/maps/documentation/javascript/usage" target="_blank">Google Maps API Usage Limits</a>', 'psp')
					)
					,'google_map_api_key_geocode' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Geocoding API Key:', 'psp'),
						'desc' 		=> __('We use this key to calculate the latitude and longitude of an address, when you enter a new psp location:<br /> fields Google Map Latitude, Google Map Longitude, Google Map Preview, from &lt;PSP Location Details meta box / Business Contact and Google Map tab&gt;.
						<br /><a href="https://developers.google.com/maps/documentation/geocoding/get-api-key" target="_blank">How to Get a Google Maps Geocoding API Console Key</a>
						<br /><a href="https://developers.google.com/maps/documentation/geocoding/usage-limits" target="_blank">Google Maps Geocoding API Usage Limits</a>
						<br /><em>NOTICE: Browser API keys (ex.: Google Map Static API Key) cannot have referer restrictions when used with Geocode API. So you must generate another API key for geocode - see the bellow screenshot where we generate 2 keys to be used.</em>
						<br /><img width="747" height="341" class="aligncenter size-full wp-image-81" alt="wpcron-cpanel" src="{plugin_folder_uri}assets/googlemap-key2.png">', 'psp')
					)
					
					//red => color: #e6383f;
					,'gmap_key_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="panel-body psp-panel-body psp-form-row">
								<label class="psp-form-label" for="site-items">' . __('Info', 'psp') . '</label>
						   		<div class="psp-form-item large">
						   			<span style="">Browser API key: create and use it, if your application runs on a client, such as a web browser.</span>
						   			<br /><br />
						   			<a href="https://support.google.com/cloud/answer/6310037" target="_blank">Best practices for securely using API keys</a>
						   			<br /><br />
									<span style="">To prevent your key from being used on unauthorized sites, only allow referrals from domains you administer.</span>
									<br />
									<img width="942" height="748" class="aligncenter size-full wp-image-81" alt="wpcron-cpanel" src="{plugin_folder_uri}assets/googlemap-key.png">
								</div>
							</div>'
					)
				)
			)
			
		)
	)
);