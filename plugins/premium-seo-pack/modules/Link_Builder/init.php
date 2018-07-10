<?php
/*
* Define class pspLinkBuilder
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLinkBuilder') != true) {
    class pspLinkBuilder
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
		
		private $settings = array();

		static protected $_instance;
		
		//search phrase pattern
		//eliminated cases: (a,h,script,embed) tags and also any tag attributes!
		static protected $pattern = '/{phrase}(?!((?i:[^<]*<\s*\/?(?:a|h\d{1}|script|embed)>)|[^<]*>))/';
		
		static protected $strtolower;

		
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Link_Builder/';
			$this->module = $this->the_plugin->cfg['modules']['Link_Builder'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'Link_Builder' );
			
			$this->setStringFunc(); //string function per encoding!
	
			if ( $this->the_plugin->is_admin === true ) {
	            add_action('admin_menu', array( $this, 'adminMenu' ));

				// ajax handler
				add_action('wp_ajax_pspGetUpdateDataBuilder', array( $this, 'ajax_request' ));
				add_action('wp_ajax_pspAddToBuilder', array( $this, 'addToBuilder' ));
				add_action('wp_ajax_pspUpdateToBuilder', array( $this, 'updateToBuilder' ));
				add_action('wp_ajax_pspGetHitsByPhrase', array( $this, 'getHitsByPhrase' ));
				//add_action('wp_ajax_pspRemoveFromBuilder', array( $this, 'removeFromBuilder' ));
				
				//delete bulk rows!
				//add_action('wp_ajax_pspLinkBuilder_do_bulk_delete_rows', array( $this, 'delete_rows' ));
			}
			
			//if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') )
			if ( !$this->the_plugin->verify_module_status( 'Link_Builder' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin !== true ) {
					add_filter('the_content', array( $this, 'do_link'), 999);

					if (isset($this->settings['is_comment']) && $this->settings['is_comment']=='yes') {
						add_filter('comment_text', array( $this, 'do_link'), 999);
					}
				}
			}

			// init module!
			//$this->init();
        }
        
		private function init() {
			//$this->createTable();
		}

		/**
	    * Singleton pattern
	    *
	    * @return pspLinkBuilder Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Link Builder', $this->the_plugin->localizationName),
		            __('Link Builder', $this->the_plugin->localizationName),
		            'read',
		           	$this->the_plugin->alias . "_Link_Builder",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('Link_Builder') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		
		/**
		 * FRONTEND
		 *
		 */
		public function do_link($content) {
	    	global $post;

	    	if ( ! $this->is_allowed_post($post) ) {
				return $content;
	    	}

			// get phrases to be replaced!
			$phrases = $this->getPhrasesLinks();

			if ( ! is_array($phrases) || empty($phrases) ) {
				return $content;
			}

			// use in this way for work with the shortcodes too
			$theContent = $this->the_plugin->do_shortcode( $content );

			// set pattern
			self::$pattern .= 'um'; //default utf-8
			$case_sensitive = isset($this->settings['case_sensitive']) && $this->settings['case_sensitive'] == 'yes'
				? true : false;
			if ( ! $case_sensitive ) { //case insensitive!
				self::$pattern .= 'i';
			}

			// replace phrases with link aliases!
			if (1) {
				//$__phrases = '('.implode(')|(', array_keys($phrases)).')';
				foreach ($phrases as $phrase => $linkInfo) {
					$link_template = $this->get_link_template( $linkInfo );

					$max_replacements = (int) $linkInfo['max_replacements'];
					if ( empty($max_replacements) || $max_replacements < -1 || $max_replacements > 30 ) {
						//default in anything went wrong!
						$max_replacements = -1;
					}

					$pattern = $this->set_pattern( self::$pattern, $phrase );
					$theContent = preg_replace($pattern, $link_template, $theContent, $max_replacements, $nbFound);
				}
			}
			return $theContent;
		}
		
		private function set_pattern($pattern, $phrase) {
			return str_replace('{phrase}', $phrase, $pattern);
		}
		
		private function getPhrasesLinks() {
			global $wpdb;
			
			$result_query = "SELECT a.url, a.phrase, a.title, a.rel, a.target, a.attr_title, a.max_replacements FROM " . $wpdb->prefix . "psp_link_builder as a WHERE 1=1 and a.publish='Y' order by a.id asc;";
			$res = $wpdb->get_results( $result_query, ARRAY_A );

			$ret = array();
			if (is_array($res) && count($res)>0) {
				foreach ($res as $k=>$v) {
					$ret["{$v['phrase']}"] = $v;
				}
			}
			return $ret;
		}

		private function get_link_template( $linkInfo=array() ) {
			$def = '<a href="{url}" title="{attr_title}" rel="{rel}" target="{target}">{title}</a>';
			$link_template = isset($this->settings['template_format'])
				? $this->settings['template_format'] : $def;

			$linkInfo = array_replace_recursive(array(
				'phrase'	=> '', // phrase
				'url'		=> '', // url
				'title'		=> '', // new replacement text
				'rel'		=> '', // url rel attribute
				'target'	=> '', // url target attribute
				'attr_title'=> '', // url title attribute
			), $linkInfo);
			if ( '' == trim($linkInfo['title']) ) {
				$linkInfo['title'] = $linkInfo['phrase'];
			}

			$ret = $link_template;
			foreach ($linkInfo as $key => $val) {
				$keyy = '{'.$key.'}';
				
				if ( in_array($key, array('rel', 'target')) ) {
					if ( empty($val) || 'no' == $val ) {
						$ret = str_replace($keyy, '', $ret);
						$ret = str_replace("$key=\"\"", '', $ret);
						continue 1;
					}
				}
				else if ( 'attr_title' == $key ) {
					if ( empty($val) ) {
						$ret = str_replace($keyy, '', $ret);
						$ret = str_replace('title=""', '', $ret);
						continue 1;
					}
				}

				$ret = str_replace($keyy, $val, $ret);
			} // end foreach
			return $ret;
		}
		
		
		/**
		 * backend methods: build the admin interface
		 *
		 */
		private function createTable() {
			global $wpdb;
			
			// check if table exist, if not create table
			$table_name = $wpdb->prefix . "psp_link_builder";
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

				$sql = "
					CREATE TABLE IF NOT EXISTS " . $table_name . " (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `hits` int(10) DEFAULT '0',
					  `url` varchar(200) DEFAULT NULL,
					  `rel` enum('no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag') DEFAULT 'no',
					  `title` varchar(100) DEFAULT NULL,
					  `target` enum('no','_blank','_parent','_self','_top') DEFAULT 'no',
					  `phrase` varchar(100) DEFAULT NULL,
					  `post_id` int(10) DEFAULT '0',
					  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `publish` char(1) DEFAULT 'Y',
					  `max_replacements` smallint(2) DEFAULT '1',
					  PRIMARY KEY (`id`),
					  UNIQUE INDEX `unique` (`phrase`,`url`),
					  KEY `publish` (`publish`),
					  KEY `url` (`url`)
					);
					";
				//KEY `deleted` (`deleted`,`publish`),
				//`deleted` smallint(1) DEFAULT '0',

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				dbDelta($sql);
			}
		}
		
		//addToBuilder: add new row into link builder table 
		public function addToBuilder()
		{
			global $wpdb;

			$request = array(
				//'itemid' 		=> isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : $itemid
				'force_save'	=> isset($_REQUEST['force_save']) ? trim($_REQUEST['force_save']) : 'no',
				'return'		=> isset($_REQUEST['return']) ? trim($_REQUEST['return']) : '',

				'url' 		=> isset($_REQUEST['new_url']) ? trim($_REQUEST['new_url']) : '',
				'phrase' 	=> isset($_REQUEST['new_text']) ? trim($_REQUEST['new_text']) : '',
				'rel' 		=> isset($_REQUEST['new_rel']) ? trim($_REQUEST['new_rel']) : '',
				'title' 	=> isset($_REQUEST['new_title']) ? trim($_REQUEST['new_title']) : '',
				'target' 	=> isset($_REQUEST['new_target']) ? trim($_REQUEST['new_target']) : '',
				'hits' 		=> isset($_REQUEST['new_hits']) ? trim($_REQUEST['new_hits']) : '0',
				'attr_title' 	=> isset($_REQUEST['new_attr_title']) ? trim($_REQUEST['new_attr_title']) : '',
				'max_replacements' 		=> isset($_REQUEST['new_max_replacements']) ? trim($_REQUEST['new_max_replacements']) : '1',
			);

			$ret = array(
				'status' 	=> 'invalid',
				'html'		=> '',
				'msg'		=> '',
			);

			$msg = ''; $is_valid = true;
			if ( $is_valid && ($request['url']=='' || $request['phrase']=='') ) {
				$is_valid = false;
				$msg = __('You didn\'t complete the necessary fields!', 'psp');
			}
			if ( ! $is_valid ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> $msg,
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			if (1) {
				$wpdb->insert(
					$wpdb->prefix . "psp_link_builder", 
					array( 
						'url' 		=> $request['url'],
						'phrase' 	=> $request['phrase'],
						'rel'		=> $request['rel'],
						'title' 	=> $request['title'],
						'target' 	=> $request['target'],
						'attr_title' 	=> $request['attr_title'],
						'hits'		=> $request['hits'],
						'max_replacements'	=> $request['max_replacements']
					), 
					array( 
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%d',
						'%d'
					)
				);
				$insert_id = $wpdb->insert_id;
				if ($insert_id<=0) {
					$ret = array_replace_recursive($ret, array(
						'msg' 	=> 'error at inserting into db.',
					));

					if ( $request['return'] == 'array' ) {
						return $ret;
					}
					die(json_encode($ret));
				}
			}

			//keep page number & items number per page
			$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
					
			// return for ajax
			$list_table = $this->ajax_list_table_rows();

			$ret = array_replace_recursive($ret, array(
				'status' => 'valid',
				'html'	 => $list_table['html'],
			));

			if ( $request['return'] == 'array' ) {
				return $ret;
			}
			die(json_encode($ret));
		}
		
		//updateToBuilder: update row from link builder table
		public function updateToBuilder()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
				'sub_action' => isset($_REQUEST['sub_action']) ? trim($_REQUEST['sub_action']) : '',
				'force_save'	=> isset($_REQUEST['force_save']) ? trim($_REQUEST['force_save']) : 'no',
				'return'		=> isset($_REQUEST['return']) ? trim($_REQUEST['return']) : '',

				'rel' 		=> isset($_REQUEST['new_rel2']) ? trim($_REQUEST['new_rel2']) : '',
				'title' 	=> isset($_REQUEST['new_title2']) ? trim($_REQUEST['new_title2']) : '',
				'target' 	=> isset($_REQUEST['new_target2']) ? trim($_REQUEST['new_target2']) : '',
				'attr_title' 	=> isset($_REQUEST['new_attr_title2']) ? trim($_REQUEST['new_attr_title2']) : '',
				'max_replacements' 	=> isset($_REQUEST['new_max_replacements2']) ? trim($_REQUEST['new_max_replacements2']) : '1'
			);

			$ret = array(
				'status' 	=> 'invalid',
				'html'		=> '',
				'msg'		=> '',
			);

			if ( $request['itemid'] ) {
				$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_link_builder WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				$row_id = (int)$row['id'];
			}

			if ( ! $request['itemid'] || ! $row_id ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> 'itemid is empty.',
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			$msg = ''; $is_valid = true;
			//if ( $is_valid && ($request['url']=='' || $request['phrase']=='') ) {
			//	$is_valid = false;
			//	$msg = __('You didn\'t complete the necessary fields!', 'psp');
			//}
			if ( ! $is_valid ) {
				$ret = array_replace_recursive($ret, array(
					'msg' 	=> $msg,
				));

				if ( $request['return'] == 'array' ) {
					return $ret;
				}
				die(json_encode($ret));
			}

			// do the operation
			{
				{
					// publish/unpublish
					if ( $request['sub_action']=='publish' ) {
						$wpdb->update( 
							$wpdb->prefix . "psp_link_builder", 
							array( 
								'publish'		=> $row['publish']=='Y' ? 'N' : 'Y'
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
					}
					// update row info!
					else {
						$wpdb->update( 
							$wpdb->prefix . "psp_link_builder", 
							array( 
								'rel'		=> $request['rel'],
								'title' 	=> $request['title'],
								'target' 	=> $request['target'],
								'attr_title' 	=> $request['attr_title'],
								'max_replacements' 	=> $request['max_replacements']
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s',
								'%s',
								'%s',
								'%s',
								'%d'
							), 
							array( '%d' )
						);
					}
					
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);

					$list_table = $this->ajax_list_table_rows();

					$ret = array_replace_recursive($ret, array(
						'status' => 'valid',
						'html'	 => $list_table['html'],
					));

					if ( $request['return'] == 'array' ) {
						return $ret;
					}
					die(json_encode($ret));
				}
			}

			$ret = array_replace_recursive($ret, array(
				'msg' 	=> 'itemid is empty.',
			));

			if ( $request['return'] == 'array' ) {
				return $ret;
			}
			die(json_encode($ret));
		}
		
		/*
		public function removeFromBuilder()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ) {
				$wpdb->delete( 
					$wpdb->prefix . "psp_link_builder", 
					array( 'id' => $request['itemid'] ) 
				);
				
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
				
				die(json_encode(array(
					'status' => 'valid'
				)));
			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		public function delete_rows() {
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
				
			$table_name = $wpdb->prefix . "psp_link_builder";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				//$query_update = "UPDATE " . ($table_name) . " set
				//		deleted=1
				//		where id in (" . ($request['id']) . ");";
				//$__stat = $wpdb->query($query_update);
				
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
		*/
		
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
		
		<div class="<?php echo $this->the_plugin->alias; ?> psp-mod-link-builder">
			
			<div class="<?php echo $this->the_plugin->alias; ?>-content">
			
				<?php
				// show the top menu
				pspAdminMenu::getInstance()->make_active('off_page_optimization|Link_Builder')->show_menu();
				?>
				
				<!-- Content -->
				<section class="<?php echo $this->the_plugin->alias; ?>-main">
						
					<?php 
					echo psp()->print_section_header(
						$this->module['Link_Builder']['menu']['title'],
						$this->module['Link_Builder']['description'],
						$this->module['Link_Builder']['help']['url']
					);
					?>
					
					<div class="panel panel-default <?php echo $this->the_plugin->alias; ?>-panel">
						
						<div id="psp-lightbox-overlay">
							<div id="psp-lightbox-container">
								<h1 class="psp-lightbox-headline"> 
									<span id="link-title-details"><?php _e('Details:', $this->the_plugin->localizationName);?></span>
									<span id="link-title-add"><?php _e('Add new link:', $this->the_plugin->localizationName);?></span>
									<span id="link-title-upd"><?php _e('Update link:', $this->the_plugin->localizationName);?></span>
									<a href="#" class="psp-close-page-detail psp-close-btn">
										<i class="psp-checks-cross2"></i>
									</a>
								</h1>
			
								<div class="psp-seo-status-container">
									<div id="psp-lightbox-seo-report-response-details">
											<table width="100%">
												<tr>
													<td><label><?php _e('Phrase:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_text"></span></td>
												</tr>
												<tr>
													<td width="180"><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_url"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('Replacement Text:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_title"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Rel:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_rel"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Target:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_target"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Title:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_attr_title"></span></td>
												</tr>
												<tr>
													<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
													<td><span id="details_max_replacements"></span></td>
												</tr>
											</table>
									</div>
								
									<div id="psp-lightbox-seo-report-response">
										<form class="psp-add-link-form">
											<input type="hidden" id="new_hits" name="new_hits" value="0" />
											<table width="100%">
												<tr>
													<td width="180"><label><?php _e('Phrase:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_text" name="new_text" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_url" name="new_url" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button blue" value="Verify founds" id="psp-builder-verify-hits"><span style="margin-left:10px;" id="psp-builder-text-hits"><span style="font-weight:bold;"></span><?php _e(' posts|pages in which the text was found!', $this->the_plugin->localizationName); ?></span>
													</td>
												</tr>
												<tr>
													<td><label><?php _e('Replacement Text:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_title" name="new_title" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Rel:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="rel" name="new_rel">
															<?php 
																$arr_rel = array( 'no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag' );
																foreach ($arr_rel as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Target:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="target" name="new_target">
															<?php 
																$arr_target = array( 'no','_blank','_parent','_self','_top' );
																foreach ($arr_target as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td width="80"><label><?php _e('Attr Title:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_attr_title" name="new_attr_title" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="max_replacements" name="new_max_replacements">
															<?php 
																$arr_target = range(1, 30, 1);
																echo '<option value="-1">' . 'All' . '</option>';
																foreach ($arr_target as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button green" value="<?php _e('Add this new link', $this->the_plugin->localizationName); ?>" id="psp-submit-to-builder">
													</td>
												</tr>
											</table>
											
										</form>
									</div>
									
									<div id="psp-lightbox-seo-report-response2">
										<form class="psp-update-link-form">
											<input type="hidden" id="upd-itemid" name="upd-itemid" value="" />
											<table width="100%">
												<tr>
													<td width="180"><label><?php _e('Phrase:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_text2" name="new_text2" value="" class="psp-add-link-field" readonly disabled="disabled" /></td>
												</tr>
												<tr>
													<td><label><?php _e('URL:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_url2" name="new_url2" value="" class="psp-add-link-field" readonly disabled="disabled" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Replacement Text:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_title2" name="new_title2" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Rel:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="rel2" name="new_rel2">
															<?php 
																$arr_rel = array( 'no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag' );
																foreach ($arr_rel as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td><label><?php _e('Attr Target:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="target2" name="new_target2">
															<?php 
																$arr_target = array( 'no','_blank','_parent','_self','_top' );
																foreach ($arr_target as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td width="80"><label><?php _e('Attr Title:', $this->the_plugin->localizationName);?></label></td>
													<td><input type="text" id="new_attr_title2" name="new_attr_title2" value="" class="psp-add-link-field" /></td>
												</tr>
												<tr>
													<td><label><?php _e('Max replacements:', $this->the_plugin->localizationName);?></label></td>
													<td>
														<select id="max_replacements2" name="new_max_replacements2">
															<?php 
																$arr_target = range(1, 30, 1);
																echo '<option value="-1">' . 'All' . '</option>';
																foreach ($arr_target as $key => $value) {
																	echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
																}												
															?>
														</select>
													</td>
												</tr>
												<tr>
													<td></td>
													<td>
														<input type="button" class="psp-button green" value="<?php _e('Update link info', $this->the_plugin->localizationName); ?>" id="psp-submit-to-builder2">
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
								<div class="psp-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
								<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
							</div>
						</div>
			
			
						<div class="panel-heading psp-panel-heading">
							<h2><?php _e('SEO link builder (internal/external)', $this->the_plugin->localizationName);?></h2>
						</div>
	
						<div class="panel-body <?php echo $this->the_plugin->alias; ?>-panel-body">
							
							<!-- Container -->
							<div class="psp-container clearfix">
			
								<!-- Main Content Wrapper -->
								<div id="psp-content-wrap" class="clearfix">
									
	                        		<div class="psp-panel">
	                        		
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspLinkBuilder',
													'custom_table'		=> "psp_link_builder",
													//'deleted_field'		=> true,
													//'force_publish_field' 	=> false,
													'show_header' 		=> true,
													'show_header_buttons' => true,
													'items_per_page' 	=> '10',
													//'post_statuses' 	=> 'all',
													'filter_fields'		=> array(
														'publish'  => array(
															'title' 			=> __('Published', $this->the_plugin->localizationName),
															'options_from_db' 	=> false,
															'include_all'		=> true,
															'options'			=> array(
																'Y'			=> __('Published', $this->the_plugin->localizationName),
																'N'			=> __('Unpublished', $this->the_plugin->localizationName),
															),
															'display'			=> 'links',
														),
													),
													'search_box'		=> array(
														'title' 	=> __('Search', $this->the_plugin->localizationName),
														'fields'	=> array('phrase', 'url', 'title'),
													),
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', $this->the_plugin->localizationName),
															'td'	=> '%id%',
															'width' => '20'
														),

														'url'		=> array(
															'th'	=> __('URL', $this->the_plugin->localizationName),
															'td'	=> '%builder_url%',
															'align' => 'left',
															//'width' => '35%',
															'class'	=> 'psp-url-orig',
														),

														'phrase'		=> array(
															'th'	=> __('Phrase <br/><span class="psp-link-builder-phrase-title">Replacement Text</span>', $this->the_plugin->localizationName),
															'td'	=> '%builder_phrase%',
															'align' => 'left',
															//'width' => '35%',
															'class'	=> 'psp-phrase',
														),

														'hits'		=> array(
															'th'	=> __('Posts', $this->the_plugin->localizationName),
															'td'	=> '%hits%',
															'width' => '15'
														),

														'max_replacements'		=> array(
															'th'	=> __('Rpl', $this->the_plugin->localizationName),
															'td'	=> '%max_rpl%',
															'width' => '15'
														),

														/*'title'	=> array(
															'th'	=> __('Title', $this->the_plugin->localizationName),
															'td'	=> '%custom_title%',
															'align' => 'center',
															'width' => '80'
														),

														'rel'	=> array(
															'th'	=> __('Rel', $this->the_plugin->localizationName),
															'td'	=> '%builder_rel%',
															'align' => 'center',
															'width' => '30'
														),

														'target'	=> array(
															'th'	=> __('Target', $this->the_plugin->localizationName),
															'td'	=> '%builder_target%',
															'align' => 'center',
															'width' => '30'
														),*/
														
														'url_attributes'	=> array(
															'th'	=> __('Link Attributes', $this->the_plugin->localizationName),
															'td'	=> '%url_attributes%',
															'align' => 'center',
															'width' => '100'
														),

														'created'		=> array(
															'th'	=> __('Creation Date', $this->the_plugin->localizationName),
															'td'	=> '%created%',
															'width' => '115'
														),

														'publish_btn' => array(
															'th'	=> __('Operations', 'psp'),
															'td'	=> '%buttons_group%',
															'option' => array(
																array(
																	'value' => __('Unpublish', 'psp'),
																	'value_change' => __('Publish', 'psp'),
																	'action' => 'do_item_publish',
																	'color'	=> 'warning',
																	'icon' => '<i class="fa fa-eye-slash"></i>',
																	'icon_change' => '<i class="fa fa-eye"></i>'
																),
																array(
																	'value' => __('Update', 'psp'),
																	'action' => 'do_item_update',
																	'color'	=> 'info',
																	'icon' => '<i class="fa fa-edit"></i>',
																),
																array(
																	'value' => __('Delete', 'psp'),
																	'action' => 'do_item_delete',
																	'color'	=> 'danger',
																	'icon' => '<i class="fa fa-times"></i>',
																),
																array(
																	'value' => __('Verify Found Posts', 'psp'),
																	'action' => 'do_item_verify',
																	'color'	=> 'info',
																	'icon' => '<i class="fa fa-refresh"></i>',
																)
															),
															'width' => '130',
														),
														/*
														'publish_btn' => array(
															'th'	=> __('Status', $this->the_plugin->localizationName),
															'td'	=> '%button_publish%',
															'option' => array(
																'value' => __('Unpublish', $this->the_plugin->localizationName),
																'value_change' => __('Publish', $this->the_plugin->localizationName),
																'action' => 'do_item_publish',
																'color'	=> 'warning',
															),
															'width' => '40',
															//'td'	=> '%button_html5data%',
															//'html5_data' => array(
															//	'publish'	=> __('Publish', $this->the_plugin->localizationName),
															//	'unpublish'	=> __('Unpublish', $this->the_plugin->localizationName),
															//)
														),
														'update_btn' => array(
															'th'	=> __('Update', $this->the_plugin->localizationName),
															'td'	=> '%button%',
															'option' => array(
																'value' => __('Update', $this->the_plugin->localizationName),
																'action' => 'do_item_update',
																'color'	=> 'info',
															),
															'width' => '30'
														),
														'delete_btn' => array(
															'th'	=> __('Delete', $this->the_plugin->localizationName),
															'td'	=> '%button%',
															'option' => array(
																'value' => __('Delete', $this->the_plugin->localizationName),
																'action' => 'do_item_delete',
																'color'	=> 'danger',
															),
															'width' => '30'
														)
														*/
													),
													'mass_actions' 	=> array(
														'add_new_link' => array(
															'value' => __('Add new link', $this->the_plugin->localizationName),
															'action' => 'do_add_new_link',
															'color' => 'info'
														),
														'delete_all_rows' => array(
															'value' => __('Delete selected rows', $this->the_plugin->localizationName),
															'action' => 'do_bulk_delete_rows',
															'color' => 'danger'
														)
													)
												))
												->print_html();
								            ?>
								            </div>
								            <div>
								            	<ul>
								            		<li><?php _e('<strong>search</strong> = search in phrase, replacement text, url.', 'psp'); ?></li>
								            		<li><?php _e('<strong>Posts</strong> = number of posts/pages where we\'ve found the phrase. Click on "Verify Found Posts" <i class="fa fa-refresh"></i> to refresh the value.', 'psp'); ?></li>
								            		<li><?php _e('<strong>Rpl</strong> = max replacements', 'psp'); ?></li>
								            	</ul>
								            </div>
							            </form>
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
		
		public function getHitsByPhrase( $phrase, $retType='die' ) {
			global $wpdb;

			$allow_future_linking = isset($this->settings['allow_future_linking'])
				&& 'yes' == $this->settings['allow_future_linking']
				? 'yes' : 'no';

			$postStatus = 'publish,private'; //publish,pending,draft,auto-draft,future,private,inherit,trash
			$postStatus2 = array_map( array($this, 'prepareForInList'), explode(',', $postStatus));
			$postStatus2 = implode(',', $postStatus2);

			$postTypes = $this->get_postTypes( 'both' );
			$postTypes2 = array_map( array($this, 'prepareForInList'), $postTypes);
			$postTypes2 = implode(',', $postTypes2);

			$excluded_postid = $this->global_excluded_items();
			$excluded_postid2 = array_map( array($this, 'prepareForInList'), $excluded_postid);
			$excluded_postid2 = implode(',', $excluded_postid2);

			$request = array(
				'phrase' 	=> isset($_REQUEST['phrase']) ? trim($_REQUEST['phrase']) : $phrase
			);
			$request['phrase'] = call_user_func( self::$strtolower, $request['phrase'] );
			
			$sql[] = "
				SELECT count(a.ID) as nb
				FROM " . $wpdb->prefix . "posts as a
				WHERE 1=1
			";
			$sql[] = "and a.post_type in (". $postTypes2 .")";
			$sql[] = "and a.post_status in (". $postStatus2 .")";
			if ( ! empty($excluded_postid) ) {
				$sql[] = "and a.ID NOT IN (". $excluded_postid2 .")";
			}
			// [[:<:]], [[:>:]] = markers for word boundaries, they match the beginning and end of words
			$sql[] = "and lower(a.post_content) REGEXP '[[:<:]]". (strtolower($request['phrase'])) ."[[:>:]]'";
			$sql[] = ";";
			$sql = trim( implode(' ', $sql) );
			//var_dump('<pre>', $sql , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
			$res = $wpdb->get_var( $sql );

			$ret = array(
				'status' => 'valid',
				'data'	=> $res,
				'allow_future_linking' => $allow_future_linking,
				'sql'	=> '' //$wpdb->last_query
			);

			if ( $retType == 'return' ) { return $ret; }
			else { die( json_encode( $ret ) ); }
		}
		
		public function getHitsById( $itemid, $retType='die' ) {
			global $wpdb;

			//$pms = array_replace_recursive(array(
			//	'hits'		=> 0,
			//), $pms);
			//extract( $pms );

			$ret = array(
				'status' => 'invalid',
				'data'	=> '',
			);

			if ( ! $itemid ) {
				if ( $retType == 'return' ) { return $ret; }
				else { die( json_encode( $ret ) ); }
			}

			$sql_id = "SELECT phrase from " . $wpdb->prefix . "psp_link_builder WHERE 1=1 and id=" . ( $itemid ) . ";";
			$phrase = $wpdb->get_var( $sql_id );
			if ( !is_null($phrase) ) {

				$phrase = call_user_func( self::$strtolower, $phrase );

				$res = $this->getHitsByPhrase( $phrase, 'return' );
				$ret = array_replace_recursive($ret, $res);

				$hits = !is_null($ret['data']) ? (int) $ret['data'] : 0;

				// update hits
				$wpdb->update( 
					$wpdb->prefix . "psp_link_builder", 
					array( 
						'hits' 	=> $hits,
					), 
					array( 'id' => $itemid ), 
					array( 
						'%d'
					), 
					array( '%d' )
				);
			}
			
			if ( $retType == 'return' ) { return $ret; }
			else { die( json_encode( $ret ) ); }
		}

		private function get_postTypes( $builtin=true ) {
	        $current = isset($this->settings['post_types']) ? (array) $this->settings['post_types'] : array();
	        $current = array_filter($current);
	        $current = array_unique($current);

			$post_types = $current;
	        if ( empty($current) ) {
				$pms = array(
					'public'   => true,
				);
				if ( $builtin === true || $builtin === false  ) {
					$pms = array_merge($pms, array(
						'_builtin' => $builtin, // exclude post, page, attachment
					));
				}
				//$post_types = get_post_types($pms, 'objects');
				$post_types = get_post_types($pms, 'names');
				unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
			}
			return $post_types;
		}

	    private function global_excluded_items() {
	        $excluded = isset($this->settings['exclude_posts_ids']) ? explode(',', trim($this->settings['exclude_posts_ids'])) : array();
	        $excluded = array_filter( array_map( array($this, 'prepareForDbClean'), $excluded ) );
	        return $excluded;
	    }

	    private function is_allowed_post( $post ) {
	    	if ( empty($post) ) {
	    		return false;
	    	}
	    	if ( is_object($post) ) {
	    		$post_id = isset($post->ID) ? (int) $post->ID : 0;
	    		$post_type = isset($post->post_type) ? $post->post_type : '';
	    	}
	    	if ( is_array($post) ) {
	    		$post_id = isset($post['ID']) ? (int) $post['ID'] : 0;
	    		$post_type = isset($post['post_type']) ? $post['post_type'] : '';
	    	}
			//var_dump('<pre>', $post_id, $post_type , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

	    	if ( empty($post_id) || empty($post_type) ) {
	    		return false;
	    	}

			$postStatus = 'publish,private'; //publish,pending,draft,auto-draft,future,private,inherit,trash
			$postStatus2 = array_map( array($this, 'prepareForInList'), explode(',', $postStatus));
			$postStatus2 = implode(',', $postStatus2);

			$postTypes = $this->get_postTypes( 'both' );
			if ( ! in_array($post_type, $postTypes) ) {
				return false;
			}

			$excluded_postid = $this->global_excluded_items();
			if ( in_array($post_id, $excluded_postid) ) {
				return false;
			}
			return true;
	    }

        private function setStringFunc() {
	    	self::$strtolower = (function_exists('mb_strtolower')) ? 'mb_strtolower' : 'strtolower';
        }
		
		private function prepareForInList($v) {
			return "'".$v."'";
		}

		private function prepareForDbClean($v) {
			return trim($v);
		}


		/**
		 * AJAX
		 *
		 */
		public function ajax_request()
		{
			global $wpdb;

			$request = array(
				'action' 		=> isset($_REQUEST['sub_action']) ? trim($_REQUEST['sub_action']) : '',
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
			);
			extract( $request );

			$ret = array(
				'status'		=> 'invalid',
				'data'			=> '',
			);

			if ( $action == 'get_details') {
				$sql = "SELECT * from " . $wpdb->prefix . "psp_link_builder WHERE 1=1 and id=" . ( $request['itemid'] ) . ";";
				$ret = array_replace_recursive($ret, array(
					'status'		=> 'valid',
					'data'			=> $wpdb->get_row( $sql ),
				));
			}
			else if ( $action == 'verify_posts') {
				$res = $this->getHitsById( $itemid, 'return' );
				$ret = array_replace_recursive($ret, array(
					'status'		=> 'valid',
					'data'			=> $res['data'],
				));
			}
			die(json_encode($ret));
		}

		private function ajax_list_table_rows() {
			return pspAjaxListTable::getInstance( $this->the_plugin )->list_table_rows( 'return', array() );
		}
    }
}

// Initialize the pspLinkBuilder class
$pspLinkBuilder = pspLinkBuilder::getInstance();