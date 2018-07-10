<?php
/*
* Define class pspAlexaRank
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspAlexaRank') != true) {
	class pspAlexaRank
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
		
		public $ga = null;
		public $ga_params = array();

		static protected $_instance;

		private $table_name = '';
		private $domain = '';

		private $tries = 0;


		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct( $is_cron=false )
		{
			global $psp;

			$this->table_name = psp()->db->prefix . 'psp_alexa_rank';

			//$this->create_table();
			// check if it's first time we load this module?
			$is_checked = get_option('psp_alexa_rank_checked', false);
			if ( ! $is_checked ) {
				$psp->plugin_integrity_check( 'check_database', true );
				update_option('psp_alexa_rank_checked', true);
			}

			$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Alexa_Rank/';
			$this->module = $this->the_plugin->cfg['modules']['Alexa_Rank'];
			

			if ( $this->the_plugin->is_admin === true && !$is_cron ) {
				if ( $this->the_plugin->capabilities_user_has_module('Alexa_Rank') ) {

					add_action('admin_menu', array( $this, 'adminMenu' ));
				}
			}

			$this->domain = $this->get_domain( home_url() );
			//$this->domain = $this->get_domain( 'http://facebook.com' );

			add_action( 'psp_alexa_rank_update', array( $this, 'alexa_rank_update' ) );
		}

		public function cronjob( $pms, $return='die' ) {
			$ret = array('status' => 'failed');

			//$current_cron_status = $pms['status']; //'new'; //

			do_action( 'psp_alexa_rank_update' );

            $ret = array_merge($ret, array(
                'status'            => 'done',
            ));
            return $ret;
		}

		function get_domain( $url ) 
		{
			$urlobj = parse_url($url);
			$domain = $urlobj['host'];
			if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
				return $regs['domain'];
			}
      		return false;
		}

		/**
		 * backend methods: build the admin interface
		 *
		 */
		/*
		private function create_table() 
		{
			global $wpdb;
			$table_name = $this->table_name;
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

				$sql = "
					CREATE TABLE IF NOT EXISTS " . $table_name . " (
						`id` INT(10) NOT NULL AUTO_INCREMENT,
						`domain` VARCHAR(50) NOT NULL DEFAULT '0',
						`global_rank` INT(10) NOT NULL DEFAULT '0',
						`rank_delta` VARCHAR(150) NOT NULL DEFAULT '0',
						`country_rank` INT(10) NOT NULL DEFAULT '0',
						`country_code` VARCHAR(4) NOT NULL DEFAULT '0',
						`country_name` VARCHAR(50) NOT NULL DEFAULT '0',
						`update_date` DATE NOT NULL,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `update_date` (`update_date`)
					);
					";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				dbDelta($sql);
			}
		}
		*/


		/**
		* Singleton pattern
		*
		* @return pspAlexaRank Singleton instance
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
			if ( $this->the_plugin->capabilities_user_has_module('Alexa_Rank') ) {
				add_submenu_page(
					$this->the_plugin->alias,
					$this->the_plugin->alias . " " . __('Alexa Rank', 'psp'),
					__('Alexa Rank', 'psp'),
					'read',
					$this->the_plugin->alias . "_Alexa_Rank",
					array($this, 'display_index_page')
				);
			}

			return $this;
		}
		
		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('Alexa_Rank') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}

		public function alexa_rank_update()
		{
			$result = (array) psp()->db->get_row( "SELECT * FROM " . ( $this->table_name ) . " WHERE 1=1 AND update_date='" . ( date("Y-m-d") ) . "' limit 1", ARRAY_A );
			
			// no record for today, try to add one now
			if( count($result) == 0 ){
				$this->get_rank_now();
			}
		}

		public function get_rank_now()
		{
			$xmlstring = wp_remote_retrieve_body( wp_remote_get( 'http://data.alexa.com/data?cli=10&dat=snbamz&url=' . $this->domain ) );
			$xml = simplexml_load_string( $xmlstring, "SimpleXMLElement", LIBXML_NOCDATA );
			$json = json_encode( $xml );
			$array = json_decode( $json, TRUE );

			$rank = array(
				'domain' 			=> trim( $array['SD'][0]['@attributes']['HOST']),
				'global_rank' 		=> trim( $array['SD'][1]['POPULARITY']['@attributes']['TEXT']),
				'rank_delta' 		=> trim( $array['SD'][1]['RANK']['@attributes']['DELTA']),
				'country_rank' 		=> trim( $array['SD'][1]['COUNTRY']['@attributes']['RANK']),
				'country_code' 		=> trim( $array['SD'][1]['COUNTRY']['@attributes']['CODE']),
				'country_name' 		=> trim( $array['SD'][1]['COUNTRY']['@attributes']['NAME']),
				'update_date'		=> date("Y-m-d")
			);

			psp()->db->insert( 
				$this->table_name, 
				$rank, 
				array( 
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s'
				) 
			);
		}

		public function current_site_data()
		{
			$result = (array) psp()->db->get_row( "SELECT * FROM " . ( $this->table_name ) . " WHERE 1=1 order by update_date DESC limit 1", ARRAY_A );

			if( count($result) == 0 ){
				if( $this->tries <= 3 ){
					$this->get_rank_now();

					return $this->current_site_data();
				}
				$this->tries++;
			}

			return $result;
		}

		public function site_data_to_msg( $site_data )
		{
			$messages = array();

			$messages['global'] = 'A rough estimate of this site\'s popularity. The rank is calculated using a combination of average daily visitors to
this site and pageviews on this site over the past 3 months. The site with the highest combination
of visitors and pageviews is ranked #1.';

			$messages['country_rank'] = 'Traffic Rank in Country.A rough estimate of this site\'s popularity in a specific country.
The rank by country is calculated using a combination of average daily visitors
to this site and pageviews on this site from users from that country over the
past month. The site with the highest combination of visitors and pageviews
is ranked #1 in that country.';
		
			$site_data['rank_delta'] = (int)$site_data['rank_delta'];
			if( $site_data['rank_delta'] == 0 ){
				$messages['rank_delta'] = '';
			}elseif( $site_data['rank_delta'] > 0 ){
				$messages['rank_delta'] = '<span class="psp-tooltip-trigger psp-declined" title="The rank declined ' . ( $site_data['rank_delta'] ) . ' positions versus the previous 3 months."><i class="psp-checks-arrow-down"></i>' . ( $site_data['rank_delta'] ) . '</span>';
			}elseif( $site_data['rank_delta'] < 0 ){
				$messages['rank_delta'] = '<span class="psp-tooltip-trigger psp-improved" title="The rank improved ' . ( $site_data['rank_delta'] ) . ' positions versus the previous 3 months."><i class="psp-checks-arrow-up"></i>' . ( $site_data['rank_delta'] ) . '</span>';
			}

			$messages['popular'] = 'Alexa Traffic Ranks show how popular a site is relative to other sites.';

			return $messages;
		}

		public function get_ranks_graph()
		{
			$from_date = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : date('Y-m-d', strtotime("-1 month"));
			$to_date = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : date('Y-m-d');

			$sql = "SELECT global_rank, country_rank, DATE_FORMAT( update_date,'%Y-%m-%d') as the_date FROM " . ( $this->table_name ) . " WHERE update_date >= '" . ( $from_date ) . "' AND update_date <= '" . ( $to_date ) . "' limit 999";
			return (array)psp()->db->get_results( $sql, ARRAY_A );
		}

		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" />

		<div class="<?php echo $this->the_plugin->alias; ?>" data-url="<?php echo $this->module_folder;?>">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content">

				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('monitoring|Alexa_Rank')->show_menu();
				?>

				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main psp-AlexaRank">
					
					<?php 
						echo psp()->print_section_header(
							$this->module['Alexa_Rank']['menu']['title'],
							$this->module['Alexa_Rank']['description'],
							$this->module['Alexa_Rank']['help']['url']
						);
					?>

					<!-- Main loading box -->
					<div id="psp-main-loading">
						<div id="psp-loading-overlay"></div>
						<div id="psp-loading-box">
							<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
							<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
						</div>
					</div>

				<?php 
					$site_data = $this->current_site_data();

					$messages = $this->site_data_to_msg( $site_data );
				?>
				<div class="container" id="psp-AlexaRank-wrapper">

					<div class="row">
						<div class="col-md-4 col-sm-12 col-xs-12">
							<div class="panel panel-default">
								<div class="panel-body psp-topchannels-graph">
									
									<!-- Container -->
									<div class="psp-container clearfix psp-rank-summary">
										<p class="psp-rank-site">
											<span>How popular is:</span>
											<span><?php echo $this->domain;?></span>
											<span class="psp-tooltip-trigger psp-question-mark" title="<?php echo $messages['popular'];?>">?</span></label>
										</p>
										<div class="psp-row-rank">
											<label>Global Rank <span class="psp-tooltip-trigger psp-question-mark" title="<?php echo $messages['global'];?>">?</span></label>
											<div>
												<i class="psp-icon-off_page_optimization"></i>
												<span class="psp-rank-pos"><?php echo $site_data['global_rank'];?></span>
												<?php echo $messages['rank_delta'];?>
											</div>
										</div>
										<?php 
										if( trim($site_data['country_name']) != "" ) {
										?>
										<div class="psp-row-rank">
											<label>Rank in <?php echo $site_data['country_name'];?> <span class="psp-tooltip-trigger psp-question-mark" title="<?php echo $messages['country_rank'];?>">?</span></label>
											<div>
												<span class="flag-icon flag-icon-<?php echo strtolower( $site_data['country_code'] );?>"></span>
												<span class="psp-rank-pos"><?php echo $site_data['country_rank'];?></span>
											</div>
										</div>
										<?php }?>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-8 col-sm-12 col-xs-12">
							<div id="<?php echo $this->the_plugin->alias; ?>-AlexaRank-wrapper" class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">

								<div class="panel-heading psp-panel-heading">
									<form action="<?php echo admin_url('admin.php');?>">
										<h2><?php _e('Rank History', 'psp');?></h2>
										<input type="hidden" name="page" value="psp_Alexa_Rank" />
										<div class="psp-top-filters">
											<div id="psp-filter-by-date">
												<label for="psp-filter-by-date-from"><?php _e('From:', 'psp');?></label>
												<input type="text" id="psp-filter-by-date-from" name="from_date" value="<?php echo date('Y-m-d', strtotime("-1 week"));?>" />
												<label for="psp-filter-by-date-to"><?php _e('to', 'psp');?></label>
												<input type="text" id="psp-filter-by-date-to" name="to_date" value="<?php echo date('Y-m-d');?>" />
												<input type="submit" class="psp-form-button-small psp-form-button-info" id="psp-filter-graph-data" value="<?php _e('Apply Filters', 'psp');?>">
											</div>
										</div>
									</form>
								</div>
								
								<div class="panel-body psp-pageviews-graph <?php echo $this->the_plugin->alias; ?>-panel-body">
									
									<!-- Container -->
									<div class="psp-container clearfix">
					
										<!-- Main Content Wrapper -->
										<div id="psp-content-wrap" class="clearfix">
											
											<div class="psp-grid_4">
												<div class="psp-panel">
													<div class="psp-audience-container">
														<div class="psp-alexa-rank-graph" >
															<canvas id="psp-alexa-rank-graph"></canvas>
															<?php 
																$ranks = $this->get_ranks_graph();

																$dates = array();
																if( count($ranks) > 0 ){
																	foreach ($ranks as $key => $value) {
																		$dates[] = $value['the_date'];
																	}
																}

																$global_ranks = array();
																if( count($ranks) > 0 ){
																	foreach ($ranks as $key => $value) {
																		$global_ranks[] = $value['global_rank'];
																	}
																}

																$country_ranks = array();
																if( count($ranks) > 0 ){
																	foreach ($ranks as $key => $value) {
																		$country_ranks[] = $value['country_rank'];
																	}
																}
															?>

															<script type="text/javascript">

														        var config = {
														            type: 'line',
														            data: {
														                labels: <?php echo json_encode($dates);?>,
														                datasets: [{
														                    label: "Global Rank",
														                    backgroundColor: '#8e44ad',
														                    borderColor: '#8e44ad',
														                    data: <?php echo json_encode($global_ranks);?>,
														                    fill: false,
														                }, {
														                    label: "Rank in <?php echo $site_data['country_name'];?>",
														                    fill: false,
														                    backgroundColor: '#e67e22',
														                    borderColor: '#e67e22',
														                    data: <?php echo json_encode($country_ranks);?>,
														                }]
														            },
														            options: {
														               maintainAspectRatio: false,
														                tooltips: {
														                    mode: 'index',
														                    intersect: false,
														                },
														                legend: {
													                        position: 'right',
													                    },
														                hover: {
														                    mode: 'nearest',
														                    intersect: true
														                },
														                scales: {
														                    xAxes: [{
														                        display: true
														                    }],
														                    yAxes: [{
														                        display: true
														                    }]
														                }
														            }
														        };
														        window.onload = function() {
														            var ctx = document.getElementById("psp-alexa-rank-graph").getContext("2d");
														            window.myLine = new Chart(ctx, config);
														        };
															</script>
														</div>
													</div>
												</div>
											</div>
										</div>
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
	}
}

// Initialize the pspAlexaRank class
//$pspAlexaRank = new pspAlexaRank();
$pspAlexaRank = pspAlexaRank::getInstance();