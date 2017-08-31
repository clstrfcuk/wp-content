<?php
/*
* Define class pspPageSpeedInsightsAjax
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspDashboard') != true) {
	global $psp;
	require_once( $psp->cfg['paths']['plugin_dir_path'] . 'modules/dashboard/init.php' );
}
if (class_exists('pspPageSpeedInsightsAjax') != true) {
    class pspPageSpeedInsightsAjax extends pspDashboard
    {
    	public $the_plugin = null;
		private $module_folder = null;
		private $file_cache_directory = '/psp-page-speed';
		private $cache_lifetime = 3600; // in seconds - 1 minute
		
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $the_plugin=array() )
        {
        	$this->the_plugin = $the_plugin;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/google_pagespeed/';
			
			// ajax  helper
			add_action('wp_ajax_pspPageSpeedInsightsRequest', array( &$this, 'ajax_request' ));
		}
		
		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			$return = array();
			$actions = isset($_REQUEST['sub_actions']) ? explode(",", $_REQUEST['sub_actions']) : '';
			
			$retType = isset($_REQUEST['return']) ? $_REQUEST['return'] : '';

			// checkPage
			if( in_array( 'checkPage', array_values($actions)) ){
				
				// get page id by URL
				$page_url = get_permalink( (int) $_REQUEST['id'] );
				
				$checkPageRet = $this->check_page( $page_url, (int) $_REQUEST['id'] );
				
				$return['checkPage'] = array(
					'status' => 'valid',
					'desktop_score' => $checkPageRet['desktop_score'],
					'mobile_score' => $checkPageRet['mobile_score'],
					'msg'	=> 'Both returned desktop and mobile scores are 0. Please verify first that you\'ve setted the Google Developer Key.'
				);
			}	

			// viewSpeedRaportById
			if( in_array( 'viewSpeedRaportById', array_values($actions)) ){
				$html = array();
				$page_id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
				
				$upload_dir = wp_upload_dir();
				// get page id by URL
				$page_url = get_permalink( $page_id );

				// update the speed score
				$checkPageRet = $this->check_page( $page_url, $page_id );

				$speed_scores = array();

				$settings = $this->the_plugin->getAllSettings( 'array', 'pagespeed' );

				if ( !isset($settings['report_type']) ) $settings['report_type'] = '';

				if( $settings['report_type'] == 'both' || $settings['report_type'] == 'desktop' ){
					$speed_scores['desktop'] = get_post_meta( $page_id, 'psp_desktop_pagespeed', true );  
				}

				if( $settings['report_type'] == 'both' || $settings['report_type'] == 'mobile' ){
					$speed_scores['mobile'] = get_post_meta( $page_id, 'psp_mobile_pagespeed', true );  
				}

				if(count($speed_scores) > 0 ){
					// write the header 
					$html[] = '<div class="psp-pagespeed-header">';
					
					$cc = 0;
					foreach ($speed_scores as $key_device => $value_device) {

						if ( !isset($value_device['pageStats']) || !isset($value_device['score']) ) {
							continue 1;
						}
						
						// normalize some vars 
						$stats = $value_device['pageStats'];
						
						$score = (int)$value_device['score'];
						$size_class = 'size_';
						if ( $score >= 20 && $score < 40 ){
							$size_class .= '20_40';
						}
						elseif ( $score >= 40 && $score < 60 ){
							$size_class .= '40_60';
						}
						elseif ( $score >= 60 && $score < 80 ){
							$size_class .= '60_80';
						}
						elseif ( $score >= 80 && $score <= 100 ){
							$size_class .= '80_100';
						}
						else{
							$size_class .= '0_20';
						}
						
						$html[] = 	'<div class="psp-tab-item type-' . ( $key_device ) . ' ' . ( $cc == 0 ? 'on' : '' ) . '" data-rel="' . ( $key_device ) . '">';
						$html[] = 		'<table>';
						$html[] = 			'<tr>';
						$html[] = 				'<td>';
						$html[] = 				'<div class="psp-progress">';
						$html[] = 						'<div style="width:' . ( $score ) . '%" class="psp-progress-bar ' . ( $size_class ) . '"></div>';
						$html[] = 						'<div class="psp-progress-score">' . ( $score ) . '/100</div>';
						$html[] = 					'</div>';
						$html[] = 				'</td>';
						$html[] = 				'<td class="psp-tab-title">';
						if ($key_device == "desktop") {
							$html[] = '<i class="psp-checks-display"></i>';
						} else {
							$html[] = '<i class="psp-checks-mobile"></i>';
						}
						$html[] = 				'</td>';
						$html[] = 			'</tr>';
						$html[] = 		'</table>';
						$html[] = 	'</div>';
						
						$cc++;
					} // end foreach
					
					
					$html[] = 	'<a href="#" class="psp-close-page-detail"><i class="psp-checks-cross2"></i></a>';

					$html[] = '</div>';
					
					$html[] = '<div class="psp-pagespeed-page-content">';
					$cc = 0;
					foreach ($speed_scores as $key_device => $value_device) { // foreach content

						if ( !isset($value_device['pageStats']) || !isset($value_device['score']) ) {
							continue 1;
						}

						$img = '';
						if(is_file( $upload_dir['path'] . $this->file_cache_directory . '/' . $page_id . '-' . ( $key_device ) . '.jpg' )){
							$img = $upload_dir['url'] . $this->file_cache_directory . '/' . $page_id . '-' . ( $key_device ) . '.jpg';
						}
						
						// normalize some vars 
						$stats = $value_device['pageStats'];
						$rule_results = $value_device['formattedResults']['ruleResults'];
						
						$html[] = 	'<div id="psp-pagespeed-page-' . ( $key_device ) . '" class="psp-pagespeed-tab" style="' . ( $cc > 0 ? 'display:none' : '' ) . '">';
						$html[] = 		'<div class="left psp-report-rules">';
						$html[] = 			'<div class="psp-grid_4">';
						$html[] = 				'<div class="psp-panel">';
						$html[] = 					'<div class="psp-panel-header">';
						$html[] = 						'<div class="psp-panel-title"><h1>' . ( __('Suggestions Summary', 'psp') ) .'</h1></div>';
						$html[] = 					'</div>';
						$html[] = 				'<div class="psp-panel-content psp-summary-box">';
						$html[] = 					'<div class="psp-sub-panel-content">';
						$html[] = 						'<table class="psp-what-to-do">';

						if ( count($rule_results) > 0 ) { // if rule_results
							foreach ($rule_results as $key_rule => $value_rule) {
								
								//if( $key_rule != "MinifyCss") continue;
								
								$html[] = 					'<tr>';
								$html[] = 						'<td class="psp-icon-status">';
								
								$icon_status = 'is_success';
								if( $value_rule['ruleImpact'] < 3 && $value_rule['ruleImpact'] > 0 ){
									$icon_status = 'is_error';
								}elseif( $value_rule['ruleImpact'] < 20 && $value_rule['ruleImpact'] > 3 ){
									$icon_status = 'is_warning';
								}
								$html[] = 							'<i class="psp-status-icon ' . ( $icon_status ) . '"></i>';
								$html[] = 						'</td>';
								$html[] = 						'<td>';
								$html[] = 							'<a href="#" class="psp-criteria">' . ( $value_rule['localizedRuleName'] ) . '</a>';

								if ( count($value_rule['urlBlocks']) > 0 ) {
									
									$html[] = 						'<div class="psp-desc-complete">';
									$html[] = 							'<ul class="can-do">';

									foreach ($value_rule['urlBlocks'] as $key_blocks => $value_blocks) { 
										$header_format = $value_blocks['header']['format'];
										$hyperlink = '';

					                    if (isset($value_blocks['header']['args'])) {
					                        $cc = 1;
					                        foreach ($value_blocks['header']['args'] as $arg) {
					                            $header_format = str_replace('$' . $cc, $arg['value'], $header_format);
					                            if($arg['type'] == 'HYPERLINK') {
					                                $hyperlink = $arg['value']; 
					                            }
												
					                            $cc++;
					                        }
					                    }

										if (isset($value_blocks['urls'])) {
											$html[] = '<ul>';
											foreach ($value_blocks['urls'] as $key_url => $value_url) {
												$link_format = $value_url['result']['format'];

												if (isset($value_url['result']['args'])) {
													$cc = 1;	
													foreach ($value_url['result']['args'] as $arg) {
							                        	$link_format = str_replace('$' . $cc, $arg['value'], $link_format);
														
														$cc++;
													}
												} 
												$html[] = '<li>' . ( $link_format ) . '</li>';
											}
											$html[] = '</ul>';
										}


										$html[] = '<li>';
										$html[] = 	'<p>' . $header_format . '<p>';
										if( trim($hyperlink) != "" ){
											$html[] = '<a href="' . ( $hyperlink ) . '" class="psp-button gray psp-form-button-small psp-form-button-info" target="_blank">' . ( __('Read Documentation', 'psp') ) . '</a>';
										}
										$html[] = '</li>';
										
									} // end foreach

									$html[] = 							'</ul>';
									$html[] = 						'</div>';
								}


								$html[] = 						'</td>';
								$html[] = 					'</tr>';
							} // end foreach
						} // end if rule_results

						$html[] = 						'</table>';
						$html[] = 					'</div>';
						$html[] = 				'</div>';
						$html[] = 			'</div>';
						$html[] = 		'</div>';


						$html[] = '<div class="psp-grid_4">';
						$html[] = 	'<div class="psp-panel">';
						$html[] = 		'<div class="psp-panel-header">';
						$html[] = 			'<div class="psp-panel-title"><h1>' . ( __('Page Resources Usages', 'psp') ) . '</h1></div>';
						$html[] = 		'</div>';
						$html[] = 		'<div class="psp-panel-content psp-resources-box">';
						$html[] = 			'<div class="psp-sub-panel-content">';
						//$html[] = 				'<div id="psp-' . ( $key_device ) . '-graph" style="height: 200px;width: 100%; "></div>';

						$html[] = 				'<div id="canvas-holder" style="width: 100%;display:inline-block;">';
						$html[] = 					'<canvas id="psp-' . ( $key_device ) . '-graph-bytes" style="height:30px;" />';
						$html[] = 				'</div>';



						$html[] = '
							<script>
								(function($){

									var config = {
										type: "pie", 
										data: {
											datasets: [{
												data: [
													' . ( $stats['htmlResponseBytes'] ) . ',
													' . ( $stats['cssResponseBytes'] ) . ',
													' . ( $stats['javascriptResponseBytes'] ) . ',
													' . ( $stats['otherResponseBytes'] ) . ',
													' . ( $stats['imageResponseBytes'] ) . '
												],
												backgroundColor: [
													"#9966ff",
													"#ff9f40",
													"#4bc0c0", 
													"#ffcd56",
													"#36a2eb"
												],
												label: "Size of Resources Dataset",

											}],
											labels: [
												"HTML",
												"CSS",
												"JavaScript",
												"Others",
												"Image"
											]
										},
										options: {
											responsive: true,
											legend: {
												position: "right",
												labels: {
													padding: 25,
													boxWidth: 65,
													padding: 25,
													fontSize: 16,
												}
											},

											tooltips: {
												enabled: true,
												mode: "single",
												callbacks: {
													label: function(tooltipItems, data) {
														return data.labels[tooltipItems.index] + ": " + psp_humanFileSize(data.datasets[0].data[tooltipItems.index]);
													}
												}
											},
										}, 
									};
									var ctx = document.getElementById("psp-' . ( $key_device ) . '-graph-bytes").getContext("2d");
									window.myPie = new Chart(ctx, config);
								})(jQuery);
							</script>
						';
						$html[] = 			'</div>';
						$html[] = 		'</div>';
						$html[] = 	'</div>';
						$html[] = '</div> <!-- Close here -->';
						
						$html[] = 	'</div>';
						
						$html[] = 	'<div class="right">';
						$html[] = 		'<div class="psp-grid_4">';
						$html[] = 			'<div class="' . ( $key_device ) . '-display">';
						$html[] = 				'<img src="' . ( $img ) . '" width="312">';
						$html[] = 				'<span class="php-the-mask"></span>';
						$html[] = 			'</div>';
						$html[] = 		'</div>';
						$html[] = 		'<div class="psp-grid_4">';
						$html[] = 			'<div class="psp-panel">';
						$html[] = 				'<div class="psp-panel-header psp-score-box">';
						// $html[] = 					'<div class="psp-panel-title">';
						// $html[] = 						__('Page Score:', 'psp');
						
						$score = (int)$value_device['score'];
						$size_class = 'size_';
						$score_text ='';
						if ( $score >= 20 && $score < 40 ){
							$size_class .= '20_40';
							$score_text = __('Poor', 'psp');
						}
						elseif ( $score >= 40 && $score < 60 ){
							$size_class .= '40_60';
							$score_text = __('Needs Work', 'psp');
						}
						elseif ( $score >= 60 && $score < 80 ){
							$size_class .= '60_80';
							$score_text = __('Needs Work', 'psp');
						}
						elseif ( $score >= 80 && $score <= 100 ){
							$size_class .= '80_100';
							$score_text = __('Very Good', 'psp');
						}
						else{
							$size_class .= '0_20';
							$score_text = __('Very Poor', 'psp');
						}

						if( !isset($item_data['score']) || trim($item_data['score']) == "" ){
							$item_data['score'] = 0;
						}

						$html[] = 						'<h1 class="psp-score-text ' . $size_class . '">' . $score_text . '</h1>';
						// $html[] = 					'<div class="psp-progress">';
						// $html[] = 						'<div style="width:' . ( $score ) . '%" class="psp-progress-bar ' . ( $size_class ) . '"></div>';
						$html[] = 						'<div class="psp-progress-score">' . ( $score ) . '/100</div>';
						// $html[] = 					'</div>';
						// $html[] = 				'</div>';
						$html[] = 			'</div>';
						$html[] = 		'</div>';
						$html[] = 	'</div>';
						$html[] = 	'<div class="psp-grid_4">';
						$html[] = 		'<div class="psp-panel">';
						$html[] = 			'<div class="psp-panel-header">';
						$html[] = 				'<div class="psp-panel-title"><h1>' . ( __('Page Statistics', 'psp') ) . '</h1></div>';
						$html[] = 			'</div>';
						$html[] = 		'<div class="psp-panel-content psp-statistics-box">';
						$html[] = 			'<div class="psp-sub-panel-content">';
						$html[] = 				'<table class="psp-table">';
						$html[] = 					'<tr>';
						$html[] = 						'<td>Last Checked</td>';
						$html[] = 						'<td>' . ( date("F j, Y, g:i a", $value_device['_create_at']) ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 					'<tr>';
						$html[] = 						'<td>Number of Hosts</td>';
						$html[] = 						'<td>' . ( isset($stats['numberHosts']) ? $stats['numberHosts'] : 0 ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 					'<tr>';
						$html[] = 						'<td>Total Request Bytes</td>';
						$html[] = 						'<td>' . ( $this->formatBytes( isset($stats['totalRequestBytes']) ? $stats['totalRequestBytes'] : 0 ) ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 					'<tr>';
						$html[] = 						'<td>Total Resources</td>';
						$html[] = 						'<td>' . ( isset($stats['numberResources']) ? $stats['numberResources'] : 0 ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 					'<tr>';
						$html[] = 						'<td>JavaScript Resources</td>';
						$html[] = 						'<td>' . ( isset($stats['numberJsResources']) ? $stats['numberJsResources'] : 0 ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 					'<tr>';
						$html[] = 						'<td>CSS Resources</td>';
						$html[] = 						'<td>' . ( isset($stats['numberCssResources']) ? $stats['numberCssResources'] : 0 ) . '</td>';
						$html[] = 					'</tr>';
						$html[] = 				'</table>';
						$html[] = 			'</div>';
						$html[] = 		'</div>';
		/*				$html[] = 				'<div id="canvas-holder" style="width: 100%;display:inline-block;margin-top:20px;">';
						$html[] = 					'Number of resources: <canvas id="psp-' . ( $key_device ) . '-graph-numbers" style="height:30px;" />';
						$html[] = 				'</div>';*/
						$html[] = 	'</div>';
						$html[] = '</div>';
						
						$html[] = '</div>';
						$html[] = '</div>';
						
						$cc++;
					} // end foreach content
					$html[] = '</div>';
				}

				$return['viewSpeedRaportById'] = array(
					'status'	=> 'valid',
					'desktop_score' => $checkPageRet['desktop_score'],
					'mobile_score' => $checkPageRet['mobile_score'],
					'html' 		=> implode("\n", $html)
				);
			}
			
			if ( $retType == 'array' ) {
				return $return;
			}
			die(json_encode($return));
		}
		
		public function check_page( $url='', $page_id=0 )
		{
			$settings = $this->the_plugin->getAllSettings( 'array', 'pagespeed' );
			$desktop_score = $mobile_score = 0;
			
			if ( !isset($settings['developer_key']) ) $settings['developer_key'] = '';
			if ( !isset($settings['google_language']) ) $settings['google_language'] = '';
			if ( !isset($settings['report_type']) ) $settings['report_type'] = '';
			
			$google_api_url = sprintf( 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url=%s&screenshot=true&snapshots=true&key=%s&locale=%s', urlencode($url), $settings['developer_key'], $settings['google_language'] );
			//var_dump('<pre>',$google_api_url, $url ,'</pre>');

			if( $settings['report_type'] == 'both' || $settings['report_type'] == 'desktop' ){

				// try to get from cache
				$response = get_post_meta( $page_id, 'psp_desktop_pagespeed', true );
				if ( $this->cache_lifetime > 0 && ( time() > (isset($response['_create_at']) ? $response['_create_at'] : 0) + $this->cache_lifetime ) ) {
					// save the desktop results 
					$response = $this->getRemote( $google_api_url . '&strategy=desktop' );
	
					$this->saveTheImage( $page_id, 'desktop', isset($response['screenshot']) ? $response['screenshot'] : array() );
					if( isset($response['screenshot']) ){
						unset($response['screenshot']);
					}
					
					$__respStat = isset($response['id']) && isset($response['responseCode']) && $response['responseCode']=='200' ? 'success' : 'error';
					$last_status = array('last_status' => array('status' => $__respStat, 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $response));
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_pagespeed_last_status', $last_status );
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_pagespeed', array_merge( (array) $settings, $last_status ) );
				}
				
				// save add create at key and save the results to the DB
				$response['_create_at'] = time();
				update_post_meta( $page_id, 'psp_desktop_pagespeed', $response );
				$desktop_score = isset($response['score']) ?  $response['score'] : 0; 
			}
			
			if( $settings['report_type'] == 'both' || $settings['report_type'] == 'mobile' ){
				
				// try to get from cache
				$response = get_post_meta( $page_id, 'psp_mobile_pagespeed', true );  
				if ( $this->cache_lifetime > 0 && ( time() > (isset($response['_create_at']) ? $response['_create_at'] : 0) + $this->cache_lifetime ) ) {
					// save the mobile results 
					$response = $this->getRemote( $google_api_url . '&strategy=mobile' );
	
					$this->saveTheImage( $page_id, 'mobile', isset($response['screenshot']) ? $response['screenshot'] : array() );
					if( isset($response['screenshot']) ){
						unset($response['screenshot']);
					}

					$__respStat = isset($response['id']) && isset($response['responseCode']) && $response['responseCode']=='200' ? 'success' : 'error';
					$last_status = array('last_status' => array('status' => $__respStat, 'step' => 'request', 'data' => date("Y-m-d H:i:s"), 'msg' => $response));
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_pagespeed_last_status', $last_status );
					$this->the_plugin->save_theoption( $this->the_plugin->alias . '_pagespeed', array_merge( (array) $settings, $last_status ) );
				}
				
				// save add create at key and save the results to the DB
				$response['_create_at'] = time();
				update_post_meta( $page_id, 'psp_mobile_pagespeed', $response ); 
				$mobile_score = isset($response['score']) ?  $response['score'] : 0;
			}
			
			return array(
				'status'		=> isset($response['id']) && isset($response['responseCode'])
					&& $response['responseCode']=='200' ? 'valid' : 'invalid',
				'desktop_score' => $desktop_score,
				'mobile_score' 	=> $mobile_score
			);
		}
		
		private function saveTheImage( $post_id=0, $device='desktop', $the_image=array() )
		{
			$upload_dir = wp_upload_dir();
			if(! is_dir( $upload_dir['path'] . '' . $this->file_cache_directory )){
				@mkdir( $upload_dir['path'] . '' . $this->file_cache_directory );
				if(! is_dir( $upload_dir['path'] . '' . $this->file_cache_directory )){
					die("Could not create the file cache directory.");
					return false;
				}
			}
			
			if ( !isset($the_image['data']) ) return false;

			// gogole replace the / with _ and + with - for keep the json response valid, so we need to roll back
			$the_image_data = str_replace("_", '/', $the_image['data']);
			$the_image_data = str_replace("-", '+', $the_image_data);
			
			file_put_contents( sprintf($upload_dir['path'] . '' . $this->file_cache_directory . '/%d-%s.jpg', $post_id, $device), @base64_decode($the_image_data));	
		}

		private function getRemote( $the_url )
		{ 
			$response = wp_remote_get( $the_url, array( 'timeout' => 30 ) );
			// If there's error
            if ( is_wp_error( $response ) ){
            	return array(
					'status' => 'invalid'
				);
            }
        	$body = wp_remote_retrieve_body( $response );
		
	        return json_decode( $body, true );
		}
		
		private function formatBytes($bytes=0)
		{
		    if ($bytes >= 1073741824)
	        {
	            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
	        }
	        elseif ($bytes >= 1048576)
	        {
	            $bytes = number_format($bytes / 1048576, 2) . ' MB';
	        }
	        elseif ($bytes >= 1024)
	        {
	            $bytes = number_format($bytes / 1024, 2) . ' KB';
	        }
	        elseif ($bytes > 1)
	        {
	            $bytes = $bytes . ' bytes';
	        }
	        elseif ($bytes == 1)
	        {
	            $bytes = $bytes . ' byte';
	        }
	        else
	        {
	            $bytes = '0 bytes';
	        }
	
	        return $bytes;
		}
    }
}