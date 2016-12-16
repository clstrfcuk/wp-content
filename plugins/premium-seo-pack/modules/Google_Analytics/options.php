<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'google_analytics' => array(
				'title' 	=> __('Google Analytics', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> array(
					'save' => array(
						'value' => __('Save settings', 'psp'),
						'color' => 'success',
						'action'=> 'psp-saveOptions'
					)
				), // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					array(
						'type' 		=> 'html',
						
						'html' 		=> '<div class="panel-heading psp-panel-heading">' . __('<h2>Basic Setup</h2>', 'psp') . '</div>',
					),
					
					array(
						'type' 		=> 'html',
						
						'html' 		=> '<div class="panel-body psp-panel-body"><div class="psp-callout psp-callout-primary">' . __('
							<ol>
								<li>Create a Project in the Google APIs Console: <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a></li>
								<li>Enable the Analytics API from the <a href="https://console.developers.google.com/apis/library" target="_blank" > Library </a></li>
								<li>After Enabling the API go to -> <a href="https://console.developers.google.com/apis/credentials" target="_blank"> Credentials </a> -> Create Credentials Button -> Create OAuth Client ID</li>
								<li>On Application type, choose Web application </li>
								<li>On Authorized redirect URIs make sure you add the link from the Premium Seo Google Settings</li>
							</ol>', 'psp') . '</div></div>',
					),
						
					'client_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '520',
						'title' 	=> __('Your client id:', 'psp'),
						'desc' 		=> __('From the APIs console.', 'psp')
					),
					
					'client_secret' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '520',
						'title' 	=> __('Your client secret:', 'psp'),
						'desc' 		=> __('From the APIs console.', 'psp')
					),
					
					'redirect_uri' 	=> array(
						'type' 		=> 'text',
						'std' 		=> home_url( '/psp_seo_oauth' ),
						'size' 		=> 'normal',
						'readonly'	=> true,
						'title' 	=> __('Redirect URI:', 'psp'),
						'desc' 		=> __('Url to your app, must match one in the APIs console.', 'psp')
					),
					
					'profile_id' 	=> array(
						'type' 		=> 'select',
						'size' 		=> 'large',
						'title' 	=> __('Profile ID:', 'psp'),
						'force_width'=> '300',
						'desc' 		=> __('Select your website profile from list. If list is empty please authorize first the app.', 'psp'),
						'options'	=> apply_filters('psp_google_analytics_get_profiles', '')
					),
					
					'authorize' => array(
						'type' => 'buttons',
						'options' => array(
							'authorize_app' => array(
								'value' => __('Authorize the app', 'psp'),
								'color' => 'info',
								'action'=> 'psp-google-authorize-app',
								'width' => '145px'
							)
						)
					),
					
					'last_status' 	=> array(
						'type' 		=> 'textarea-array',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Authorize Last Status:', 'psp'),
						'desc' 		=> __('Last Status retrieved from Google, for the Authorize operation', 'psp')
					),
					
					'profile_last_status' 	=> array(
						'type' 		=> 'textarea-array',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Get Profile ID Last Status:', 'psp'),
						'desc' 		=> __('Last Status retrieved from Google, for the Get Profile ID operation', 'psp')
					),
					
					array(
						'type' 		=> 'message',
						
						'html' 		=> __('Add <a href="http://www.google.com/analytics/" target="_blank">Google Analytics</a> javascript code on all pages.', 'psp'),
					),
					
					'google_anonymize_ip' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Anonymize Analytics IP: ', 'psp'),
						'desc' 		=> __('If you choose YES, the Google Analytics script which tracks your visitors views will use Google Analytics\' _anonymizeIp function, that anonymizes the last digits of the user\'s IP.', 'psp'),
						'options'	=> array(
							'yes' 	=> __('YES', 'psp'),
							'no' 	=> __('NO', 'psp')
						)
					),
					
					'google_analytics_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '300',
						'title' 	=> __('Google Analytics ID:', 'psp'),
						'desc' 		=> __('Your Google Analytics ID to be used in tracking script', 'psp')
					),
					
					'google_verify' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'small',
						'force_width'=> '500',
						'title' 	=> __('Google Webmaster Tools:', 'psp'),
						'desc' 		=> __('&lt;meta name="google-site-verification" content="<u>content entered in Google Webmaster Tools box</u>" /&gt;', 'psp')
					)

				)
			)
		)
	)
);