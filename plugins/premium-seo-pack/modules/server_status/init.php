<?php
/*
* Define class pspServerStatus
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('pspServerStatus') != true) {
    class pspServerStatus
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/server_status/';
			$this->module = $this->the_plugin->cfg['modules']['server_status'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/server_status/ajax.php' );
			new pspServerStatusAjax( $this->the_plugin );
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspServerStatus Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{ 
    		add_submenu_page(
    			$this->the_plugin->alias,
    			$this->the_plugin->alias . " " . __('Check System status', $this->the_plugin->localizationName),
	            __('System Status', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_server_status",
	            array($this, 'display_index_page')
	        );

			return $this;
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			global $wpdb;
			
			// Google Analytics
			if ( $this->the_plugin->verify_module_status( 'Google_Analytics' ) ) { //module is active
    			$analytics_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_google_analytics' );
    			$analytics_mandatoryFields = array(
    				'client_id'			=> false,
    				'client_secret'		=> false,
    				'redirect_uri'		=> false
    			);
        
    			// get the module init file
    			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Google_Analytics/init.php' );
    			// Initialize the pspGoogleAnalytics class
    			$pspGoogleAnalytics = new pspGoogleAnalytics();
			} // end Google Analytics

			
			// Google SERP
			if ( $this->the_plugin->verify_module_status( 'serp' ) ) { //module is active
    			$serp_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_serp' );
    			$serp_mandatoryFields = array(
    				'developer_key'			=> false,
    				'custom_search_id'		=> false,
    				'google_country'		=> false
    			);
    			
    			// get the module init file
    			// require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/serp/init.php' );
    			// Initialize the pspSERP class
    			// $pspSERP = new pspSERP($this->cfg, ( isset($module) ? $module : array()) );
			} // end Google SERP

			
			// Google Pagespeed
			if ( $this->the_plugin->verify_module_status( 'google_pagespeed' ) ) { //module is active
    			$pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_pagespeed' );
    			$pagespeed_mandatoryFields = array(
    				'developer_key'			=> false,
    				'google_language'		=> false
    			);
    			
    			// get the module init file
    			// require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/google_pagespeed/ajax.php' );
    			// Initialize the pspPageSpeedInsightsAjax class
    			// $pspPagespeed = new pspPageSpeedInsightsAjax($this->the_plugin);
			} // end Google Pagespeed
			
			
			// Facebook
			if ( $this->the_plugin->verify_module_status( 'facebook_planner' ) ) { //module is active
    			$facebook_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_facebook_planner' );
    			$facebook_mandatoryFields = array(
    				'app_id'			=> false,
    				'app_secret'		=> false,
    				'language'			=> false,
    				'redirect_uri'		=> false
    			);
    			
    			// get the module init file
    			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/init.php' );
    			// Initialize the pspFacebook_Planner class
    			$pspFacebook_Planner = new pspFacebook_Planner();
			} // end Facebook
			

            // Tiny Compress
            if ( $this->the_plugin->verify_module_status( 'tiny_compress' ) ) { //module is active
                $tinycompress_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_tiny_compress' );
                $tinycompress_mandatoryFields = array(
                    'tiny_key'         => false,
                    'image_sizes'      => false,
                );
                
                // get the module init file
                require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/tiny_compress/init.php' );
                // Initialize the pspTinyCompress class
                $pspTinyCompress = new pspTinyCompress();
            } // end Tiny Compress

			$plugin_data = get_plugin_data( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'plugin.php' );  
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		
		<div class="<?php echo $this->the_plugin->alias; ?>">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content">
			
				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('general|server_status')->show_menu();
				?>
				
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
						
					<?php 
					echo psp()->print_section_header(
						$this->module['server_status']['menu']['title'],
						$this->module['server_status']['description'],
						$this->module['server_status']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">
			
						<!-- Main loading box -->
						<div id="psp-main-loading">
							<div id="psp-loading-overlay"></div>
							<div id="psp-loading-box">
								<div class="psp-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
								<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
							</div>
						</div>

						<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
							
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="clearfix">
									
	                        		<div class="psp-panel">
	                        			
	                        			<div class="psp-panel-content psp-server-status">
	                        				
											<table class="psp-table" cellspacing="0">
												
												<thead>
													<tr>
														<th colspan="2"><?php _e( 'Modules', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
										         	<tr>
										         		<td><?php _e( 'Active Modules',$this->the_plugin->localizationName); ?>:</td>
										         		<td><div class="psp-loading-ajax-details" data-action="active_modules"></div></td>
										         	</tr>
												</tbody>
												
												
												<?php
													$opStatus_stat = $this->the_plugin->plugin_integrity_get_last_status( 'check_database' );
													
													$check_last_msg = '';
													if ( '' != trim($opStatus_stat['html']) ) {
														$check_last_msg = ( $opStatus_stat['status'] == true ? '<div class="psp-message psp-success">' : '<div class="psp-message psp-error">' ) . $opStatus_stat['html'] . '</div>';
													}
												?>
												<thead>
													<tr>
														<th colspan="2"><?php _e( 'Plugin Integrity', $this->the_plugin->localizationName ); ?></th>
													</tr>
												</thead>
										
												<tbody>
										         	<tr>
										         		<td><?php _e( 'Database', $this->the_plugin->localizationName ); ?>:</td>
										         		<td>
										         			<?php /*<div class="psp-loading-ajax-details" data-action="check_integrity_database"></div>*/ ?>
										         			<div class="psp-check-integrity-container">
										         				<a href="#check_integrity_database" class="psp-form-button psp-form-button-info" data-action="check_integrity_database">Check</a>
										         				<div class="psp-response"><?php echo $check_last_msg; ?></div>
										         			</div>
										         		</td>
										         	</tr>
												</tbody>
												
												
												<?php
												// Google Analytics module
												if ( $this->the_plugin->verify_module_status( 'Google_Analytics' ) ) { //module is inactive
												?>
					
												<thead>
													<tr>
														<th colspan="2"><a id="sect-google_analytics" name="sect-google_analytics"></a><?php _e( 'Module Google Analytics:', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
													<tr>
										                <td width="190"><?php _e( 'Your client id',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php
															if ( isset($analytics_settings['client_id']) && !empty($analytics_settings['client_id']) ) {
																$analytics_mandatoryFields['client_id'] = true;
																echo $analytics_settings['client_id'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php } ?>
														</td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Your client secret',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($analytics_settings['client_secret']) && !empty($analytics_settings['client_secret']) ) {
																$analytics_mandatoryFields['client_secret'] = true;
																echo $analytics_settings['client_secret'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php } ?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Redirect URI',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($analytics_settings['redirect_uri']) && !empty($analytics_settings['redirect_uri']) ) {
																$analytics_mandatoryFields['redirect_uri'] = true;
																echo $analytics_settings['redirect_uri'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php } ?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Profile ID',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($analytics_settings['profile_id']) && !empty($analytics_settings['profile_id']) ) {
																echo $analytics_settings['profile_id'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#Google_Analytics"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php } ?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Authorize',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<div class="psp-begin-test-container noheight">
															<?php
															$mandatoryValid = true;
															foreach ($analytics_mandatoryFields as $k=>$v) {
																if ( !$v ) {
																	$mandatoryValid = false;
																	break;
																}
															}
															if ( $mandatoryValid ) {
																if ( $pspGoogleAnalytics->makeoAuthLogin() ) {
															?>
																
																<a href="#google-analytics/authorize" class="psp-form-button psp-form-button-info pspStressTest inline psp-google-authorize-app" data-saveform="no">Re-Authorize app</a>&nbsp;(<?php _e( 'app is authorized',$this->the_plugin->localizationName); ?>)
															
														<?php
															} else {
														?>
															
																<a href="#google-analytics/authorize" class="psp-form-button-small psp-form-button-info pspStressTest inline psp-google-authorize-app" data-saveform="no">Authorize app</a>
																<span style="margin-left: 10px;">(<?php _e( 'app is not authorized yet',$this->the_plugin->localizationName); ?>)</span>
															<?php
																}
															} else {
															?>
																<div class="psp-message psp-error">
																	<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can authorize the app!',$this->the_plugin->localizationName); ?>
																</div>
															<?php
															}
															?>
															</div>
										                </td>
										            </tr>
																
										            <tr>
										            	<td style="vertical-align: middle;">Verify:</td>
										                <td>
															<div class="psp-verify-products-test">
																<div class="psp-test-timeline">
																	<div class="psp-one_step stepid-step1 nbsteps4">
																		<div class="psp-step-status psp-loading-inprogress"></div>
																		<span class="psp-step-name">Step 1</span>
																	</div>
																	<div class="psp-one_step stepid-step2 nbsteps4">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 2</span>
																	</div>
																	<div class="psp-one_step stepid-step3 nbsteps4">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 3</span>
																	</div>
																	<div class="psp-one_step stepid-step4 nbsteps4">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 4</span>
																	</div>
																	<div style="clear:both;"></div>
																</div>
																<table class="psp-table psp-logs" cellspacing="0">
																	<tr class="logbox-step1">
																		<td width="50">Step 1:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Set mandatory fields: client id, client secret, redirect uri', $this->the_plugin->localizationName); ?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step2">
																		<td width="50">Step 2:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Authorize app on Google APIs Console:', $this->the_plugin->localizationName); ?>
																				<a target="_blank" href="https://code.google.com/apis/console/">https://code.google.com/apis/console/</a>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step3">
																		<td width="50">Step 3:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Get profile ID', $this->the_plugin->localizationName); ?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step4">
																		<td width="50">Step 4:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Make a test request from Google Analytics', $this->the_plugin->localizationName); ?>
																				<?php
																				$today = date( 'Y-m-d' );
																				$from_date 	= date( 'Y-m-d', strtotime( "-1 week", strtotime( $today ) ) );
																				$to_date 	= date( 'Y-m-d', strtotime( $today ) );
																				echo " (from $from_date to $to_date)";
																				?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																</table>
																<div class="psp-begin-test-container">
																	<a href="#google-analytics/verify" class="psp-form-button psp-form-button-info pspStressTest verify" data-module="google_analytics">Verify</a>
																</div>
															</div>
														</td>
										            </tr>
												</tbody>
												<?php
												} // end Google Analytics module!
												?>
	
	
												<?php
												// Google SERP module
												if ( $this->the_plugin->verify_module_status( 'serp' ) ) { //module is inactive
												?>
												<thead>
													<tr>
														<th colspan="2"><a id="sect-google_serp" name="sect-google_serp"></a><?php _e( 'Module Google SERP:', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
													<tr>
										                <td width="190"><?php _e( 'Google Developer Key',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php
															if ( isset($serp_settings['developer_key']) && !empty($serp_settings['developer_key']) ) {
																$serp_mandatoryFields['developer_key'] = true;
																echo $serp_settings['developer_key'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
														</td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Custom Search Engine ID',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($serp_settings['custom_search_id']) && !empty($serp_settings['custom_search_id']) ) {
																$serp_mandatoryFields['custom_search_id'] = true;
																echo $serp_settings['custom_search_id'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Google location',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($serp_settings['google_country']) && !empty($serp_settings['google_country']) ) {
																$serp_mandatoryFields['google_country'] = true;
																echo 'google.'.$serp_settings['google_country'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#serp"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Status',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<div class="psp-begin-test-container noheight">
															<?php
															$mandatoryValid = true;
															foreach ($serp_mandatoryFields as $k=>$v) {
																if ( !$v ) {
																	$mandatoryValid = false;
																	break;
																}
															}
															if ( $mandatoryValid ) {
															?>
																<div class="psp-message psp-success">
																	<?php _e( 'all mandatory module settings are set!',$this->the_plugin->localizationName); ?>
																</div>
															<?php
															} else {
															?>
																<div class="psp-message psp-error">
																	<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a serp request!',$this->the_plugin->localizationName); ?>
																</div>
															<?php
															}
															?>
															</div>
										                </td>
										            </tr>
																
										            <tr>
										            	<td style="vertical-align: middle;">Verify:</td>
										                <td>
															<div class="psp-verify-products-test">
																<div class="psp-test-timeline">
																	<div class="psp-one_step stepid-step1 nbsteps2">
																		<div class="psp-step-status psp-loading-inprogress"></div>
																		<span class="psp-step-name">Step 1</span>
																	</div>
																	<div class="psp-one_step stepid-step2 nbsteps2">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 2</span>
																	</div>
																	<div style="clear:both;"></div>
																</div>
																<table class="psp-table psp-logs" cellspacing="0">
																	<tr class="logbox-step1">
																		<td width="50">Step 1:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Set mandatory fields: google developer key, custom search engine id, google location', $this->the_plugin->localizationName); ?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step2">
																		<td width="50">Step 2:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Make a test request from Google SERP', $this->the_plugin->localizationName); ?>
																				<?php
																				$serp_keyword 	= 'test';
																				$serp_link		= 'www.test.com';
																				echo " (keyword: $serp_keyword , url: $serp_link)";
																				?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																</table>
																<div class="psp-begin-test-container">
																	<a href="#google-serp/verify" class="psp-form-button psp-form-button-info pspStressTest verify" data-module="serp">Verify</a>
																</div>
															</div>
														</td>
										            </tr>
												</tbody>
												<?php
												} // end Google SERP module!
												?>
	
	
												<?php
												// Google Pagespeed module
												if ( $this->the_plugin->verify_module_status( 'google_pagespeed' ) ) { //module is inactive
												?>
												<thead>
													<tr>
														<th colspan="2"><a id="sect-google_pagespeed" name="sect-google_pagespeed"></a><?php _e( 'Module Google Pagespeed:', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
													<tr>
										                <td width="190"><?php _e( 'Google Developer Key',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php
															if ( isset($pagespeed_settings['developer_key']) && !empty($pagespeed_settings['developer_key']) ) {
																$pagespeed_mandatoryFields['developer_key'] = true;
																echo $pagespeed_settings['developer_key'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#google_pagespeed"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
														</td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Google language',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($pagespeed_settings['google_language']) && !empty($pagespeed_settings['google_language']) ) {
																$pagespeed_mandatoryFields['google_language'] = true;
																echo $pagespeed_settings['google_language'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#google_pagespeed"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Status',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<div class="psp-begin-test-container noheight">
															<?php
															$mandatoryValid = true;
															foreach ($pagespeed_mandatoryFields as $k=>$v) {
																if ( !$v ) {
																	$mandatoryValid = false;
																	break;
																}
															}
															if ( $mandatoryValid ) {
															?>
																<div class="psp-message psp-success">
																	<?php _e( 'all mandatory module settings are set!', $this->the_plugin->localizationName); ?>
																</div>
															<?php
															} else {
															?>
																<div class="psp-message psp-error">
																	<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a serp request!',$this->the_plugin->localizationName); ?>
																</div>
															<?php
															}
															?>
															</div>
										                </td>
										            </tr>
																
										            <tr>
										            	<td style="vertical-align: middle;">Verify:</td>
										                <td>
															<div class="psp-verify-products-test">
																<div class="psp-test-timeline">
																	<div class="psp-one_step stepid-step1 nbsteps2">
																		<div class="psp-step-status psp-loading-inprogress"></div>
																		<span class="psp-step-name">Step 1</span>
																	</div>
																	<div class="psp-one_step stepid-step2 nbsteps2">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 2</span>
																	</div>
																	<div style="clear:both;"></div>
																</div>
																<table class="psp-table psp-logs" cellspacing="0">
																	<tr class="logbox-step1">
																		<td width="50">Step 1:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Set mandatory fields: google developer key, google language', $this->the_plugin->localizationName); ?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step2">
																		<td width="50">Step 2:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Make a test request from Google Pagespeed', $this->the_plugin->localizationName); ?>
																				<?php
																				$serp_link		= 'www.test.com';
																				echo " (url: $serp_link)";
																				?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																</table>
																<div class="psp-begin-test-container">
																	<a href="#google-pagespeed/verify" class="psp-form-button psp-form-button-info pspStressTest verify" data-module="pagespeed">Verify</a>
																</div>
															</div>
														</td>
										            </tr>
												</tbody>
												<?php
												} // end Google Pagespeed module!
												?>
												
												
												<?php
												// Facebook Planner module
												if ( $this->the_plugin->verify_module_status( 'facebook_planner' ) ) { //module is inactive
												?>
												<thead>
													<tr>
														<th colspan="2"><a id="sect-facebook_planner" name="sect-facebook_planner"></a><?php _e( 'Module Facebook:', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
													<tr>
										                <td width="190"><?php _e( 'Facebook App ID',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php
															if ( isset($facebook_settings['app_id']) && !empty($facebook_settings['app_id']) ) {
																$facebook_mandatoryFields['app_id'] = true;
																echo $facebook_settings['app_id'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
														</td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Facebook App Secret',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($facebook_settings['app_secret']) && !empty($facebook_settings['app_secret']) ) {
																$facebook_mandatoryFields['app_secret'] = true;
																echo $facebook_settings['app_secret'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Redirect URI',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($facebook_settings['redirect_uri']) && !empty($facebook_settings['redirect_uri']) ) {
																$facebook_mandatoryFields['redirect_uri'] = true;
																echo $facebook_settings['redirect_uri'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Facebook Language',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<?php 
															if ( isset($facebook_settings['language']) && !empty($facebook_settings['language']) ) {
																$facebook_mandatoryFields['language'] = true;
																echo $facebook_settings['language'];
															} else {
															?>
															<div class="psp-begin-test-container">
																<a href="<?php echo admin_url("admin.php?page=psp#facebook_planner"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
																}
															?>
										                </td>
										            </tr>
													<tr>
										                <td width="190"><?php _e( 'Authorize',$this->the_plugin->localizationName); ?>:</td>
										                <td>
															<div class="psp-begin-test-container noheight">
															<?php
															$mandatoryValid = true;
															foreach ($facebook_mandatoryFields as $k=>$v) {
																if ( !$v ) {
																	$mandatoryValid = false;
																	break;
																}
															}
															if ( $mandatoryValid ) {
																if ( 'fbv4' == $this->the_plugin->facebook_sdk_version ) {
																	$authFb = $pspFacebook_Planner->makeoAuthLogin_fbv4(array(
																		'psp_redirect_url'		=> 'server_status',
																	));
																}
																//else {
																//	$authFb = $pspFacebook_Planner->makeoAuthLogin();
																//}
																if ( $authFb ) {
																	$facebook_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_facebook_planner' );
															?>
																<?php
																	$btnFb = $this->fb_auth_url(array(
																		'text'				=> __('Re-Authorize app', $this->the_plugin->localizationName),
																		'fb_details'		=> $facebook_settings,
																		'psp_redirect_url'	=> 'server_status',
																	));
																	echo $btnFb['html'];
																?>
															&nbsp;(
															<?php
																$__fbauthor = __( 'app is authorized ',$this->the_plugin->localizationName);
																if ( isset($facebook_settings['auth_foruser_name']) ) {
 																	if ( isset($facebook_settings['auth_foruser_link']) ) {
 																		$__fbauthor = __( 'app is authorized for: ',$this->the_plugin->localizationName) . '<a target="_blank" href="' . $facebook_settings['auth_foruser_link'] . '">' . $facebook_settings['auth_foruser_name'] . '</a>';
 																	}
																	else {
																		$__fbauthor = __( 'app is authorized for: ',$this->the_plugin->localizationName) . $facebook_settings['auth_foruser_name'];
																	}
																}
																echo $__fbauthor;
															?>
															)
															<?php
																} else {
															?>
																<?php
																	$btnFb = $this->fb_auth_url(array(
																		'text'				=> __('Authorize app', $this->the_plugin->localizationName),
																		'fb_details' 		=> $facebook_settings,
																		'psp_redirect_url'	=> 'server_status',
																	));
																	echo $btnFb['html'];
																?>
																<span style="margin-left: 10px;">(<?php _e( 'app is not authorized yet',$this->the_plugin->localizationName); ?>)</span>
															<?php
																}
															} else {
															?>
																<div class="psp-message psp-error">
																	<?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can authorize the app!',$this->the_plugin->localizationName); ?>
																</div>
															<?php
															}
															?>
															</div>
										                </td>
										            </tr>
																
										            <tr>
										            	<td style="vertical-align: middle;">Verify:</td>
										                <td>
															<div class="psp-verify-products-test">
																<div class="psp-test-timeline">
																	<div class="psp-one_step stepid-step1 nbsteps2">
																		<div class="psp-step-status psp-loading-inprogress"></div>
																		<span class="psp-step-name">Step 1</span>
																	</div>
																	<div class="psp-one_step stepid-step2 nbsteps2">
																		<div class="psp-step-status"></div>
																		<span class="psp-step-name">Step 2</span>
																	</div>
																	<div style="clear:both;"></div>
																</div>
																<table class="psp-table psp-logs" cellspacing="0">
																	<tr class="logbox-step1">
																		<td width="50">Step 1:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Set mandatory fields: app id, app secret, redirect uri, language', $this->the_plugin->localizationName); ?>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																	<tr class="logbox-step2">
																		<td width="50">Step 2:</td>
																		<td>
																			<div class="psp-log-title">
																				<?php _e( 'Authorize app on Facebook Developers:', $this->the_plugin->localizationName); ?>
																				<a target="_blank" href="http://developers.facebook.com/">http://developers.facebook.com/</a>
																				<a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
																			</div>
																			
																			<textarea class="psp-log-details"></textarea>
																		</td>
																	</tr>
																</table>
																<div class="psp-begin-test-container">
																	<a href="#facebook-planner/verify" class="psp-form-button psp-form-button-info pspStressTest verify" data-module="facebook_planner">Verify</a>
																</div>
															</div>
														</td>
										            </tr>
												</tbody>
												<?php
												} // end Facebook Planner module!
												?>
												
												
												<?php
												// Tiny Compress module
												if ( $this->the_plugin->verify_module_status( 'tiny_compress' ) ) { //module is inactive
												?>
	                                            <thead>
	                                                <tr>
	                                                    <th colspan="2">                                            <a id="sect-tiny_compress" name="sect-tiny_compress"></a><?php _e( 'Module Tiny Compress:', $this->the_plugin->localizationName); ?></th>
	                                                </tr>
	                                            </thead>
	                                    
	                                            <tbody>
	                                                <tr>
	                                                    <td width="190"><?php _e( 'Tiny Compress API Key',$this->the_plugin->localizationName); ?>:</td>
	                                                    <td>
															<?php
															if ( isset($tinycompress_settings['tiny_key']) && !empty($tinycompress_settings['tiny_key']) ) {
															    $tinycompress_mandatoryFields['tiny_key'] = true;
															    echo $tinycompress_settings['tiny_key'];
															} else {
															?>
															<div class="psp-begin-test-container">
															    <a href="<?php echo admin_url("admin.php?page=psp#tiny_compress"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
															    }
															?>
	                                                    </td>
	                                                </tr>
	                                                <tr>
	                                                    <td width="190"><?php _e( 'Selected Image Sizes',$this->the_plugin->localizationName); ?>:</td>
	                                                    <td>
															<?php 
															if ( isset($tinycompress_settings['image_sizes']) && !empty($tinycompress_settings['image_sizes']) ) {
															    $tinycompress_mandatoryFields['image_sizes'] = true;
															    echo implode(', ', (array) $tinycompress_settings['image_sizes']);
															} else {
															?>
															<div class="psp-begin-test-container">
															    <a href="<?php echo admin_url("admin.php?page=psp#tiny_compress"); ?>" class="psp-form-button-small psp-form-button-info pspStressTest">Update module settings</a>
															</div>
															<?php
															    }
															?>
	                                                    </td>
	                                                </tr>
	                                                <tr>
	                                                    <td width="190"><?php _e( 'Status',$this->the_plugin->localizationName); ?>:</td>
	                                                    <td>
															<div class="psp-begin-test-container noheight">
															<?php
															$mandatoryValid = true;
															foreach ($tinycompress_mandatoryFields as $k=>$v) {
															    if ( !$v ) {
															        $mandatoryValid = false;
															        break;
															    }
															}
															if ( $mandatoryValid ) {
															?>
															    <div class="psp-message psp-success">
															        <?php _e( 'all mandatory module settings are set!', $this->the_plugin->localizationName); ?>
															    </div>
															<?php
															} else {
															?>
															    <div class="psp-message psp-error">
															        <?php _e( 'some mandatory module settings are missing or not valid, so first fill them and then you can make a tiny compress request!',$this->the_plugin->localizationName); ?>
															    </div>
															<?php
															}
															?>
															</div>
	                                                    </td>
	                                                </tr>
	
	                                                <tr>
	                                                    <td><?php _e('Monthly limit',$this->the_plugin->localizationName); ?>:</td>
	                                                    <td>
	                                                        <?php
	                                                            $compress_limits = $pspTinyCompress->get_compress_limits();
	                                                        ?>
	                                                        <div class="psp-message psp-<?php echo $compress_limits['status'] == 'valid' ? 'success' : 'error'; ?>">
	                                                            <p><?php echo $compress_limits['msg']; ?></p>
	                                                        </div>
	                                                    </td>
	                                                </tr>
	
	                                                <tr>
	                                                    <td style="vertical-align: middle;">Verify connection:</td>
	                                                    <td>
	                                                        <div class="psp-verify-products-test">
	                                                            <div class="psp-test-timeline">
	                                                                <div class="psp-one_step stepid-step1 nbsteps2">
	                                                                    <div class="psp-step-status psp-loading-inprogress"></div>
	                                                                    <span class="psp-step-name">Step 1</span>
	                                                                </div>
	                                                                <div class="psp-one_step stepid-step2 nbsteps2">
	                                                                    <div class="psp-step-status"></div>
	                                                                    <span class="psp-step-name">Step 2</span>
	                                                                </div>
	                                                                <div style="clear:both;"></div>
	                                                            </div>
	                                                            <table class="psp-table psp-logs" cellspacing="0">
	                                                                <tr class="logbox-step1">
	                                                                    <td width="50">Step 1:</td>
	                                                                    <td>
	                                                                        <div class="psp-log-title">
	                                                                            <?php _e( 'Set mandatory fields: tiny api key, image sizes', $this->the_plugin->localizationName); ?>
	                                                                            <a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
	                                                                        </div>
	                                                                        
	                                                                        <textarea class="psp-log-details"></textarea>
	                                                                    </td>
	                                                                </tr>
	                                                                <tr class="logbox-step2">
	                                                                    <td width="50">Step 2:</td>
	                                                                    <td>
	                                                                        <div class="psp-log-title">
	                                                                            <?php _e( 'Connection status to TinyPNG.com API', $this->the_plugin->localizationName); //Make a test request from Tiny API ?>
	                                                                            <a href="#" class="psp-form-button psp-form-button-info"><?php _e( 'View details +', $this->the_plugin->localizationName); ?></a>
	                                                                        </div>
	                                                                        
	                                                                        <textarea class="psp-log-details"></textarea>
	                                                                    </td>
	                                                                </tr>
	                                                            </table>
	                                                            <div class="psp-begin-test-container">
	                                                                <a href="#tiny-compress/verify" class="psp-form-button psp-form-button-info pspStressTest verify" data-module="tinycompress">Verify</a>
	                                                            </div>
	                                                        </div>
	                                                    </td>
	                                                </tr>
	                                            </tbody>
												<?php
												} // end Tiny Compress module!
												?>
												
												<thead>
													<tr>
														<th colspan="2"><?php _e( 'Environment', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
													<tr>
										                <td width="190"><?php _e( 'Home URL',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php echo home_url(); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'psp Version',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php echo $plugin_data['Version'];?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'WP Version',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( is_multisite() ) echo 'WPMU'; else echo 'WP'; ?> <?php bloginfo('version'); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'Web Server Info',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] );  ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'PHP Version',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'MySQL Version',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( function_exists( 'mysql_get_server_info' ) ) echo esc_html( (is_resource($wpdb->dbh)) ? mysql_get_server_info( $wpdb->dbh ) : $wpdb->db_version() ); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'WP Memory Limit',$this->the_plugin->localizationName); ?>:</td>
										                <td><div class="psp-loading-ajax-details" data-action="check_memory_limit"></div></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'WP Debug Mode',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo __( 'Yes', $this->the_plugin->localizationName); else echo __( 'No', $this->the_plugin->localizationName); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e( 'WP Max Upload Size',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php echo size_format( wp_max_upload_size() ); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e('PHP Post Max Size',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( function_exists( 'ini_get' ) ) echo size_format( $this->let_to_num( ini_get('post_max_size') ) ); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e('PHP Time Limit',$this->the_plugin->localizationName); ?>:</td>
										                <td><?php if ( function_exists( 'ini_get' ) ) echo ini_get('max_execution_time'); ?></td>
										            </tr>
										            <tr>
										                <td><?php _e('WP Remote GET',$this->the_plugin->localizationName); ?>:</td>
										                <td><div class="psp-loading-ajax-details" data-action="remote_get"></div></td>
										            </tr>
										            <tr>
										                <td><?php _e('SOAP Client',$this->the_plugin->localizationName); ?>:</td>
										                <td><div class="psp-loading-ajax-details" data-action="check_soap"></div></td>
										            </tr>
												</tbody>
										
												<thead>
													<tr>
														<th colspan="2"><?php _e( 'Plugins', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
										         	<tr>
										         		<td><?php _e( 'Installed Plugins',$this->the_plugin->localizationName); ?>:</td>
										         		<td><div class="psp-loading-ajax-details" data-action="active_plugins"></div></td>
										         	</tr>
												</tbody>
										
												<thead>
													<tr>
														<th colspan="2"><?php _e( 'Settings', $this->the_plugin->localizationName); ?></th>
													</tr>
												</thead>
										
												<tbody>
										
										            <tr>
										                <td><?php _e( 'Force SSL',$this->the_plugin->localizationName); ?>:</td>
														<td><?php echo get_option( 'woocommerce_force_ssl_checkout' ) === 'yes' ? __( 'Yes', $this->the_plugin->localizationName) : __( 'No', $this->the_plugin->localizationName); ?></td>
										            </tr>
												</tbody>
											</table>
										</div>
				            		</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>

<?php
		}

		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requesto to 404 table
		*/
		public function ajax_request()
		{
			global $wpdb;
			$request = array(
				'id' 			=> isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0
			);
			
			$asin = get_post_meta($request['id'], '_amzASIN', true);
			
			$sync = new wwcAmazonSyncronize( $this->the_plugin );
			$sync->updateTheProduct( $asin, $request['id'] );
		}
		
		public function let_to_num($size) {
			if ( function_exists('wc_let_to_num') ) {
				return wc_let_to_num( $size );
			}

			$l = substr($size, -1);
			$ret = substr($size, 0, -1);
			switch( strtoupper( $l ) ) {
				case 'P' :
					$ret *= 1024;
				case 'T' :
					$ret *= 1024;
				case 'G' :
					$ret *= 1024;
				case 'M' :
					$ret *= 1024;
				case 'K' :
					$ret *= 1024;
			}
			return $ret;
		}

		public function fb_auth_url( $pms=array() ) {
			$pms = array_merge(array(
				'facebook'			=> null,
				'fb_details'		=> array(),
				'psp_redirect_url'	=> '',
				'text'				=> __('Authorize app', $this->the_plugin->localizationName),
			), $pms);
			extract($pms);

			$ret = array(
				'html'				=> '',
				'url'				=> '',
			);

			if ( 'fbv4' == $this->the_plugin->facebook_sdk_version ) {
				$ret = array_merge( $ret, $this->the_plugin->facebook_get_authorization_url( $pms ) );
			}
			//else {
			//	$ret['url'] = '#facebook-planner/authorize';
			//	$ret['html'] = '<a href="' . $link_href . '" class="psp-form-button psp-form-button-info pspStressTest inline psp-facebook-authorize-app" data-saveform="no">' . $text . '</a>';
			//}

			return $ret;
		}
	}
}
// Initialize the pspServerStatus class
//$pspServerStatus = new pspServerStatus();
$pspServerStatus = pspServerStatus::getInstance();