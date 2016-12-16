<?php
/*
* Define class psp404Monitor
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('psp404Monitor') != true) {
    class psp404Monitor
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
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/monitor_404/';
			$this->module = $this->the_plugin->cfg['modules']['monitor_404'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			if ( !$this->the_plugin->verify_module_status( 'monitor_404' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true )
					add_action("template_redirect", array( &$this, 'store_new_404_log' ));
			}
			
			// ajax  helper
			if ( $this->the_plugin->is_admin === true ) {
				add_action('wp_ajax_pspGet404MonitorRequest', array( &$this, 'ajax_request' ));
				add_action('wp_ajax_psp404MonitorToRedirect', array( &$this, 'add404MonitorToRedirect' ));
			
				//delete bulk rows!
				add_action('wp_ajax_psp_do_bulk_delete_404_rows', array( &$this, 'delete_404_rows' ));
			}
			
			// init module!
			$this->init();
        }
        
		private function init() {
			//$this->createTable();
		}
		
		/**
	    * Singleton pattern
	    *
	    * @return psp404Monitor Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('monitor_404') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Monitor Page Not Found errors', 'psp'),
		            __('Monitor 404 errors', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_mass404Monitor",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		/**
		 * backend methods: build the admin interface
		 *
		 */
		private function createTable() {
			global $wpdb;
			
			// check if table exist, if not create table
			$table_name = $wpdb->prefix . "psp_monitor_404";
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

		            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
						`id` INT(10) NOT NULL AUTO_INCREMENT,
						`hits` INT(10) NULL DEFAULT '1',
						`url` VARCHAR(200) NULL DEFAULT NULL,
						`referrers` TEXT NULL DEFAULT NULL,
						`user_agents` TEXT NULL DEFAULT NULL,
						`data` TIMESTAMP NOT NULL DEFAULT current_timestamp
						PRIMARY KEY (`id`),
						UNIQUE INDEX `uniq_urls` (`url`)
					);";
		            //`deleted` SMALLINT(1) NULL DEFAULT '0',

		            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		            dbDelta($sql);
			}
		}


	    /**
	    * Store new 404 error log
	    */
		public function store_new_404_log()
		{
			if(is_404()) {
				global $wpdb, $_path, $psp; // this is how you get access to the database

				// collect data for insert into DB
				# Request URI
				$visitor_request_uri = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ? 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] :  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                # Referer
                $visitor_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                # user agent
                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
				
				//doing_wp_cron
				if( preg_match('/doing_wp_cron/i', $visitor_request_uri) == false ){
					// escape mysql injections
					// $visitor_request_uri = mysql_real_escape_string($visitor_request_uri);
					// $visitor_referer = mysql_real_escape_string($visitor_referer);
					// $user_agent = mysql_real_escape_string($user_agent);
					
					// mysql & mysqli comptabile: replaced mysql_real_escape_string with $wpdb->_real_escape
                    // $visitor_request_uri = $wpdb->_real_escape($visitor_request_uri);
                    // $visitor_referer = $wpdb->_real_escape($visitor_referer);
                    // $user_agent = $wpdb->_real_escape($user_agent);
					
					$table_name = $wpdb->prefix . "psp_monitor_404";
	
					// create insert or update
					/*$query = "INSERT IGNORE INTO " . ($table_name) . "
					(
						url,
						referrers,
						user_agents
					)
					VALUES (
						'$visitor_request_uri',
						'$visitor_referer',
						'$user_agent'
					)";*/
                    $query = $wpdb->prepare(
                        "INSERT IGNORE INTO " . ($table_name) . " (url, referrers, user_agents) VALUES (%s, %s, %s)",
                        $visitor_request_uri,
                        $visitor_referer,
                        $user_agent
                    );
					if ($wpdb->query($query) == 0) {
						// record already exist, update hits
						$query_update = "UPDATE " . ($table_name) . " set
							hits=hits+1,
							referrers=CONCAT(referrers, '\n$visitor_referer'),
							user_agents=CONCAT(user_agents, '\n$user_agent')
							where url='$visitor_request_uri'";
						$wpdb->query($query_update);
					}
				}
			}
		}
		
		/**
		 * delete Bulk 404 rows!
		 */
		public function delete_404_rows() {
			global $wpdb; // this is how you get access to the database
			
			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 0
			);
			if ($request['id']!=0) {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (int) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}
				
			$table_name = $wpdb->prefix . "psp_monitor_404";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				/*$query_update = "UPDATE " . ($table_name) . " set
						deleted=1
						where id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_update);*/
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => ''
					)) );
				}
			}
			
			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => ''
			)) );
		}
		
		public function add404MonitorToRedirect() {
			global $wpdb;
			
			$request = array(
				'itemid' 		=> isset($_REQUEST['itemid']) && !empty($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : 0,
				'subaction' 	=> isset($_REQUEST['subaction']) ? trim($_REQUEST['subaction']) : '',
				'url_redirect'	=> isset($_REQUEST['new_url_redirect2']) ? trim($_REQUEST['new_url_redirect2']) : ''
			);
			
			$request['id'] = $request['itemid'];

			if ($request['id']!=0) {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (int) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}
			
			$check = $wpdb->get_results("SELECT b.id, a.url FROM " . ( $wpdb->prefix ) . "psp_link_redirect AS a INNER JOIN " . ( $wpdb->prefix ) . "psp_monitor_404 AS b ON a.url=b.url WHERE b.id IN (" . $request['id'] . ")");
			 
			if( count($check) > 0 ) {
				foreach( $check as $url ) {
					if( ($key = array_search($url->id, $__rq2)) !== false) {
						unset($__rq2[$key]);
					}
					$msg['err'][] = __('Redirect for', 'psp') .' "'.($url->url).'" ' . __('already exists. (not added)', 'psp');
				}
				$request['id'] = implode(',', $__rq2);
			}
			
			if( $request['id'] != '' ) {
				$sql = "
					INSERT INTO " . ( $wpdb->prefix ) . "psp_link_redirect (url, url_redirect)
					 SELECT url, %s FROM " . ( $wpdb->prefix ) . "psp_monitor_404 AS a
					 WHERE 1=1 AND a.id IN (" . $request['id'] . ");
				";
				$sql = $wpdb->prepare( $sql, $request['url_redirect'] );
				 
				$__stat = $wpdb->query( $sql );
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);
						
					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => isset($msg['err']) && count($msg['err']) > 0 ? implode("\n\n", $msg['err']) : __('Redirect added.', 'psp'),
						'nbrows' => $__stat
					)) );
				}
			}
			
			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => isset($msg['err']) && count($msg['err']) > 0 ? implode("\n\n", $msg['err']) : ''
			)) );
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
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		
		<div class="<?php echo $this->the_plugin->alias; ?> psp-404">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content"> 
				
				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('monitoring|monitor_404')->show_menu();
				?>
				
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
					
					<?php 
					echo psp()->print_section_header(
						$this->module['monitor_404']['menu']['title'],
						$this->module['monitor_404']['description'],
						$this->module['monitor_404']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">
			
						<div id="psp-lightbox-overlay">
							<div id="psp-lightbox-container">
								<h1 class="psp-lightbox-headline">
									<span class="psp-details-text"><?php _e('Details:', 'psp');?></span>
									<a href="#" class="psp-close-btn psp-form-button-small psp-form-button-danger" title="<?php _e('Close Lightbox', 'psp'); ?>">
										<i class="psp-icon-close"></i>
									</a>
								</h1>
			
								<div class="psp-seo-status-container">
									<div id="psp-lightbox-seo-report-response"></div>
									<div id="psp-lightbox-seo-report-response2">
										<form class="psp-update-link-form">
											<input type="hidden" id="upd-itemid" name="upd-itemid" value="" />
											<table width="100%">
												<tr>
													<td width="120"><label><?php _e('URL:', 'psp');?></label></td>
													<td><span id="old_url_list"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('URL Redirect:', 'psp');?></label></td>
													<td><input type="text" id="new_url_redirect2" name="new_url_redirect2" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button green" value="<?php _e('Add to Link Redirect', 'psp'); ?>" id="psp-submit-to-builder2">
													</td>
												</tr>
											</table>
											
										</form>
									</div>
									<div style="clear:both"></div>
								</div>
							</div>
						</div>
			
						<!-- Main loading box -->
						<div id="psp-main-loading">
							<div id="psp-loading-overlay"></div>
							<div id="psp-loading-box">
								<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
								<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
							</div>
						</div>

						<div class="panel-heading psp-panel-heading">
							<h2><?php _e('Monitor Page Not Found Errors', 'psp');?></h2>
						</div>
						
                		<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
				
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="clearfix">
									<div class="psp-panel">
										
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspMonitor404',
													'custom_table'		=> "psp_monitor_404",
													'custom_table_force_action' => true,
													//'deleted_field'		=> true,
													'show_header' 		=> true,
													'items_per_page' 	=> '10',
													'post_statuses' 	=> 'all',
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', 'psp'),
															'td'	=> '%id%',
															'width' => '40'
														),

														'hits'		=> array(
															'th'	=> __('Hits', 'psp'),
															'td'	=> '%hits%',
															'width' => '40'
														),

														'bad_url'		=> array(
															'th'	=> __('Bad URL', 'psp'),
															'td'	=> '%bad_url%',
															'align' => 'left'
														),

														'referrers'		=> array(
															'th'	=> __('Referrers', 'psp'),
															'td'	=> '%referrers%',
															'align' => 'center',
															'width' => '109'
														),

														'user_agents'	=> array(
															'th'	=> __('User Agents', 'psp'),
															'td'	=> '%user_agents%',
															'align' => 'center',
															'width' => '112'
														),

														'last_date'		=> array(
															'th'	=> __('Last Log Date', 'psp'),
															'td'	=> '%last_date%',
															'width' => '177'
														)
													),
													'mass_actions' 	=> array(
														'add_new_link' => array(
															'value' => __('Add to Link Redirect', 'psp'),
															'action' => 'do_add_new_link',
															'color' => 'info'
														),
														'delete_404_rows' => array(
															'value' => __('Delete selected rows', 'psp'),
															'action' => 'do_bulk_delete_404_rows',
															'color' => 'info'
														)
													)
												))
												->print_html();
								            ?>
								            </div>
							            </form>
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
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			global $wpdb;
			$request = array(
				'id' 			=> isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0,
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? strtolower($_REQUEST['sub_action']) : ''
			);

			$res = $wpdb->get_var( "SELECT " . ( $request['sub_action'] ) . " from " . $wpdb->prefix . "psp_monitor_404 WHERE 1=1 and id=" . ( $request['id'] ) . ";" );
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> implode( '<br />', explode( PHP_EOL, $res ) )
				//'data'	=> $wpdb->get_var( "SELECT " . ( $request['sub_action'] ) . " from " . $wpdb->prefix . "psp_monitor_404 WHERE 1=1 and deleted=0 and id=" . ( $request['id'] ) . ";" )
			)) );
		}
    }
}

// Initialize the psp404Monitor class
//$psp404Monitor = new psp404Monitor($this->cfg, ( isset($module) ? $module : array()) );
$psp404Monitor = psp404Monitor::getInstance( $this->cfg, ( isset($module) ? $module : array()) );