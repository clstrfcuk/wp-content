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
			'pagespeed' => array(
				'title' 	=> 'Google Page Speed Insights',
				'icon' 		=> '{plugin_folder_uri}assets/16_pagespeed.png',
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

					'developer_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'AIzaSyCt1tsWk-2xsgivuZZUrGbYBSdL-ik5xs8',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('API Key:', 'psp'),
						'desc' 		=> __('API Key - manually create one in Google Console - the default value is a working key that has a limit of 25000 requests per day', 'psp')
					),
					
					'google_language' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'en',
						'size' 		=> 'large',
						'force_width'=> '170',
						'title' 	=> __('Supported Languages', 'psp'),
						'desc' 		=> __('All possible Google response supported languages.', 'psp'),
						'options' 	=> array(
							'ar' => 'Arabic',
							'bg' => 'Bulgarian',
							'ca' => 'Catalan',
							'zh_TW' => 'Traditional Chinese (Taiwan)',
							'zh_CN' => 'Simplified Chinese',
							'hr' => 'Croatian',
							'cs' => 'Czech',
							'da' => 'Danish',
							'nl' => 'Dutch',
							'en' => 'English',
							'en_GB' => 'English UK',
							'fil' => 'Filipino',
							'fi' => 'Finnish',
							'fr' => 'French',
							'de' => 'German',
							'el' => 'Greek',
							'iw' => 'Hebrew',
							'hi' => 'Hindi',
							'hu' => 'Hungarian',
							'id' => 'Indonesian',
							'it' => 'Italian',
							'ja' => 'Japanese',
							'ko' => 'Korean',
							'lv' => 'Latvian',
							'lt' => 'Lithuanian',
							'no' => 'Norwegian',
							'pl' => 'Polish',
							'pt_BR' => 'Portuguese (Brazilian)',
							'pt_PT' => 'Portuguese (Portugal)',
							'ro' => 'Romanian',
							'ru' => 'Russian',
							'sr' => 'Serbian',
							'sk' => 'Slovakian',
							'sl' => 'Slovenian',
							'es' => 'Spanish',
							'sv' => 'Swedish',
							'th' => 'Thai',
							'tr' => 'Turkish',
							'uk' => 'Ukrainian',
							'vi' => 'Vietnamese',
						)
					),
					
					'report_type' 	=> array(
						'type' 		=> 'select',
						'std' 		=> 'en',
						'size' 		=> 'large',
						'force_width'=> '170',
						'title' 	=> __('Report Type', 'psp'),
						'desc' 		=> __('The strategy to use when analyzing the page. Valid values are desktop, mobile or both.', 'psp'),
						'options' 	=> array(
							'both' => 'Both',
							'desktop' => 'Desktop',
							'mobile' => 'Mobile'
						)
					),

						'last_status' 	=> array(
						'type' 		=> 'textarea-array',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Request Last Status:', 'psp'),
						'desc' 		=> __('Last Status retrieved from Google, for the Request operation', 'psp')
					),

					array(
						'type' 		=> 'html',
						
						'html' 		=> '<div class="panel-body psp-panel-body"><div class="psp-callout psp-callout-primary">' . __('
							<ol>
								<li>Create a Project in the Google APIs Console: <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a></li>
								<li>Enable the PageSpeed Insights API from the <a href="https://console.developers.google.com/apis/library" target="_blank" > Library </a></li>
								<li>After Enabling the API go to -> <a href="https://console.developers.google.com/apis/credentials" target="_blank"> Credentials </a> -> Create Credentials Button -> API key</li>
							</ol>', 'psp') . '</div></div>',
					)
					
					
					
				
				)
			)
		)
	)
);