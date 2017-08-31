<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		pspAjaxListTable
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('pspAjaxListTable') != true) {
	class pspAjaxListTable {

		/*
         * Some required plugin information
         */
        const VERSION = '1.0';

		/*
         * Singleton pattern
         */
		static protected $_instance;

		/*
         * Store some helpers
         */
		public $the_plugin = null;

		/*
         * Store some default options
         */
		public $default_options = array(
			'id' 					=> '', /* string, uniq list ID. Use for SESSION filtering / sorting actions */
			'debug_query' 			=> false, /* default is false */
			'show_header' 			=> true, /* boolean, true or flase */
			'list_post_types' 		=> 'all', /* array('post', 'pages' ... etc) or 'all' */
			'items_per_page' 		=> 15, /* number. How many items per page */
			'post_statuses' 		=> 'all',
			'search_box' 			=> true, /* boolean, true or flase */
			'show_statuses_filter' 	=> true, /* boolean, true or flase */
			'show_pagination' 		=> true, /* boolean, true or flase */
			'show_category_filter' 	=> true, /* boolean, true or flase */
			'columns' 				=> array(),
			'custom_table' 			=> '',
			'requestFrom'			=> 'init', /* values: init | ajax */
			
			'custom_table_force_action' 	=> false,
			'deleted_field' 				=> false,
			'force_publish_field' 			=> false,
			'show_header_buttons' 			=> false,
			'params'						=> null,
		);
		private $items;
		private $items_nr;
		private $args;

		public $opt = array();


        /*
         * Required __construct() function that initalizes the AA-Team Framework
         */
        public function __construct( $parent )
        {
        	$this->the_plugin = $parent;
			add_action('wp_ajax_pspAjaxList', array( $this, 'request' ));
			add_action('wp_ajax_pspAjaxList_actions', array( $this, 'ajax_request' ), 10, 2);
        }

		/**
	     * Singleton pattern
	     *
	     * @return class Singleton instance
	     */
	    static public function getInstance( $parent )
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self($parent);
	        }

	        return self::$_instance;
	    }

		/**
	     * Setup
	     *
	     * @return class
	     */
		public function setup( $options=array() )
		{
			global $psp;
			$this->opt = array_merge( $this->default_options, $options );

			$this->opt["custom_table"] = trim($this->opt["custom_table"]);
			if ( $this->opt["custom_table"] != "") {
				$this->opt = array_merge( $this->opt, array(
					'orderby'		=> 'id',
					'order'			=> 'DESC',
				));
			}

			//unset($_SESSION['pspListTable']); // debug

			// check if set, if not, reset
			if ( isset($options['requestFrom']) && $options['requestFrom'] == 'ajax' ) ;
			else {

				$keepvar = isset($_SESSION['pspListTable']['keepvar']) ? $_SESSION['pspListTable']['keepvar'] : '';
				$sess = isset($_SESSION['pspListTable'][$this->opt['id']]['params']) ? $_SESSION['pspListTable'][$this->opt['id']]['params'] : array();

				$options['params']['posts_per_page'] = isset($sess['posts_per_page']) ? $sess['posts_per_page'] : $this->opt['items_per_page'];
				if ( isset($keepvar) && isset($keepvar['paged']) ) {
					$options['params']['paged'] = isset($sess['paged']) ? $sess['paged'] : 1;
					unset( $keepvar['paged'] );
					$_SESSION['pspListTable']['keepvar'] = $keepvar;
				}

			}
			$_SESSION['pspListTable'][$this->opt['id']] = $options;

			return $this;
		}

		/**
	     * Singleton pattern
	     *
	     * @return class Singleton instance
	     */
		public function request()
		{
			$request = array(
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
				'ajax_id' 		=> isset($_REQUEST['ajax_id']) ? $_REQUEST['ajax_id'] : '',
				'params' 		=> isset($_REQUEST['params']) ? $_REQUEST['params'] : '',
			);
			   
			if( $request['sub_action'] == 'post_per_page' ){
				$new_post_per_page = $request['params']['post_per_page'];

				if( $new_post_per_page == 'all' ){
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = '-1';
				}
				elseif( (int)$new_post_per_page == 0 ){
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = $this->opt['items_per_page'];
				}
				else{
					$_SESSION['pspListTable'][$request['ajax_id']]['params']['posts_per_page'] = $new_post_per_page;
				}

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}

			if( $request['sub_action'] == 'paged' ){
				$new_paged = $request['params']['paged'];
				if( $new_paged < 1 ){
					$new_paged = 1;
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = $new_paged;
			}

			if( $request['sub_action'] == 'post_type' ){
				$new_post_type = $request['params']['post_type'];
				if( $new_post_type == "" ){
					$new_post_type = "";
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['post_type'] = $new_post_type;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}

			if( $request['sub_action'] == 'post_status' ){
				$new_post_status = $request['params']['post_status'];
				if( $new_post_status == "all" ){
					$new_post_status = "";
				}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']['post_status'] = $new_post_status;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}

			if( $request['sub_action'] == 'general_field' ){
				$filter_name = isset($request['params']['filter_name']) ? $request['params']['filter_name'] : '';
				$filter_val = isset($request['params']['filter_val']) ? $request['params']['filter_val'] : '';
				//if( $filter_val == "all" ){
				//	$filter_val = "";
				//}

				$_SESSION['pspListTable'][$request['ajax_id']]['params']["$filter_name"] = $filter_val;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}
			
			if( $request['sub_action'] == 'search' ){
				$search_text = $request['params']['search_text'];
				
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['search_text'] = $search_text;

				// reset the paged as well
				$_SESSION['pspListTable'][$request['ajax_id']]['params']['paged'] = 1;
			}
			

			// create return html
			ob_start();

			$_SESSION['pspListTable'][$request['ajax_id']]['requestFrom'] = 'ajax';

			$this->setup( $_SESSION['pspListTable'][$request['ajax_id']] );
			$this->print_html();
			$html = ob_get_contents();
			ob_clean();

			die( json_encode(array(
				'status' 	=> 'valid',
				'html'		=> $html
				//,'sess'		=> $_SESSION['pspListTable'][$request['ajax_id']]['params']
			)) );
		}

		/**
	     * Helper function
	     *
	     * @return object
	     */
		public function get_items()
		{
			global $wpdb;

			$ses = isset($_SESSION['pspListTable'][$this->opt['id']]['params']) ? $_SESSION['pspListTable'][$this->opt['id']]['params'] : array();

			$this->args = array(
				'posts_per_page'  	=> ( isset($ses['posts_per_page']) ? $ses['posts_per_page'] : $this->opt['items_per_page'] ),
				'paged'				=> ( isset($ses['paged']) ? $ses['paged'] : 1 ),
				'category'        	=> ( isset($ses['category']) ? $ses['category'] : '' ),
				'orderby'         	=> 'post_date',
				'order'          	=> 'DESC',
				'post_type'       	=> ( isset($ses['post_type']) && trim($ses['post_type']) != "all" ? $ses['post_type'] : array_keys($this->get_list_postTypes()) ),
				'post_status'     	=> ( isset($ses['post_status']) ? $ses['post_status'] : '' ),
				'suppress_filters' 	=> true
			);

			// MEDIA -  smushit
			if ( in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) {
				$this->args = array_merge($this->args, array(
					'post_type'			=> 'attachment',
					'post_status'		=> 'inherit',
					'post_mime_type'	=> array('image/jpeg', 'image/jpg', 'image/png')
				));
				$this->args = array_merge(
					$this->args,
					$this->post_media_getQuery( isset($ses['post_status']) ? $ses['post_status'] : ''
				));
			}

			// if custom table, make request in the custom table not in wp_posts
			$this->opt["custom_table"] = trim($this->opt["custom_table"]);
			if ( $this->opt["custom_table"] != "") {
				$pages = array();

				//---------------
				// Query Start
			    // select all pages and post from DB
			    $myQuery = "SELECT SQL_CALC_FOUND_ROWS a.* FROM " . $wpdb->prefix . ( $this->opt["custom_table"] ) . " as a WHERE 2=2 ";

				// search fields
				$search_where = $this->search_posts_where();
				//$search_where = str_replace('AND ', '', $search_where);
				$myQuery .= $search_where;
				
				// dropdown filter fields
				$filter_where = '';
				$filter_fields = isset($this->opt["filter_fields"]) && !empty($this->opt["filter_fields"])
					? $this->opt["filter_fields"] : array();
				foreach ($filter_fields as $field => $vals) {
					$this->filter_fields["$field"] = array();
					$field_val = isset($ses["$field"]) ? (string) trim($ses["$field"]) : '';
					//if ( $field_val != '' ) {
					if ( isset($ses["$field"]) && ('--all--' != $ses["$field"]) ) {
						//if ( ($this->opt["custom_table"] == 'psp_link_redirect') && ('--is-error--' == $field_val) ) {
						//	$filter_where .= " AND $field NOT IN ('', 'is_ok') ";
						//}
						//else {
							$filter_where .= " AND $field = '" . esc_sql($field_val) . "' ";
						//}
					}
				}
				$myQuery .= $filter_where;
				
				$myQuery .= ' AND 1=1 ';

				// limit query
			    $__limitClause = $this->args['posts_per_page']>0 ? " 1=1 limit " . (($this->args['paged'] - 1) * $this->args['posts_per_page']) . ", " . $this->args['posts_per_page'] : '1=1 ';
				$result_query = str_replace("1=1 ", $__limitClause, $myQuery);

				// order by
				$orderby = isset($this->opt["orderby"]) ? $this->opt["orderby"] : '';
				$order = isset($this->opt["order"]) ? $this->opt["order"] : 'ASC';
				if( !empty($orderby) ) {
					if ( $this->args['posts_per_page']>0 ) {
						$result_query = str_replace('1=1 limit', "1=1 ORDER BY a.$orderby $order limit", $result_query);
					}
					else {
						$result_query = str_replace('1=1', "1=1 ORDER BY a.$orderby $order", $result_query);	
					}
				}

				//publish field
			    if (isset($this->opt["force_publish_field"]) && $this->opt["force_publish_field"]) {
			    	$myQuery = str_replace("1=1 ", " 1=1 and a.publish='Y' ", $myQuery);
			    	$result_query = str_replace("1=1 ", " 1=1 and a.publish='Y' ", $result_query);
			    }

			    //deleted field
			    if (isset($this->opt["deleted_field"]) && $this->opt["deleted_field"]) {
			    	$myQuery = str_replace("1=1 ", " 1=1 and a.deleted=0 ", $myQuery);
			    	$result_query = str_replace("1=1 ", " 1=1 and a.deleted=0 ", $result_query);
			    }

			    $myQuery .= ";"; $result_query .= ";";
				
				// dropdown filter fields
				//		when option <display> = links
				foreach ($filter_fields as $field => $vals) {
					$display = isset($vals['display']) && ('links' == $vals['display']) ? 'links' : 'default';
					$field_val = isset($ses["$field"]) ? (string) trim($ses["$field"]) : '';

					if ( 'links' == $display ) {
						$sql_ff = $myQuery;

						$sql_ff = str_replace(" AND $field = '" . esc_sql($field_val) . "' ", "", $sql_ff);
						//if ( ($this->opt["custom_table"] == 'psp_link_redirect') && ('--is-error--' == $field_val) ) {
						//	$sql_ff = str_replace(" AND $field NOT IN ('', 'is_ok') ", "", $sql_ff);
						//}

						$sql_ff = str_replace('SQL_CALC_FOUND_ROWS', '', $sql_ff);
	                	$sql_ff = str_replace("a.*", "a.$field, count(a.id) as __nb", $sql_ff);
						$sql_ff = str_replace(";", " GROUP BY a.$field ORDER BY a.$field ASC", $sql_ff);
						$this->filter_fields["$field"]['count'] = $wpdb->get_results( $sql_ff, OBJECT_K );
					}
				}
				//var_dump('<pre>', $this->filter_fields, '</pre>'); die('debug...'); 
					
			    // Query End
			    //---------------

				if ( $this->opt["custom_table"] == 'psp_serp_reporter' ) {

			    	if ( isset($_SESSION['psp_serp']['search_engine'])
			    		&& !empty($_SESSION['psp_serp']['search_engine'])
			    		&& $_SESSION['psp_serp']['search_engine'] != '--all--'
			    	) {
			    		$myQuery = str_replace("1=1 ", " 1=1 and a.search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $myQuery);

			    		$result_query = str_replace("1=1 ", " 1=1 and a.search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $result_query);
			    	}
				}

			    $query = $wpdb->get_results( $result_query, ARRAY_A);

			    foreach ($query as $key => $myrow) {
			    	$pages[$myrow['id']] = $myrow;
			    	$pages[$myrow['id']]['__tr_css'] = '';

					if ( $this->opt["custom_table"] == 'psp_serp_reporter' ) {
						$pages[$myrow['id']]['engine_location'] = substr(
							$myrow['search_engine'],
							strpos($myrow['search_engine'], '.')
						);
					}
			    	else if( $this->opt["custom_table"] == 'psp_post_planner_cron' ) {
						$pages[$myrow['id']]['post_to_group'] = $myrow['post_to-page_group'];
			    	}
			    	else if( $this->opt["custom_table"] == 'psp_link_redirect' ) {
			    		if ( 'regexp' == $myrow['redirect_rule'] ) {
			    			$pages[$myrow['id']]['__tr_css'] = 'psp-tr-verify-inactive';
			    		}
			    	}
			    } // end foreach

			    //var_dump('<pre>',$pages,'</pre>');

				$this->items = $pages;

				//$this->items_nr = $wpdb->get_var( str_replace("a.*", "count(a.id) as nbRow", $myQuery) );
				$this->items_nr = $wpdb->get_var( "SELECT FOUND_ROWS();" );

				$dbg_query = $result_query;
			}
			else {

				// remove empty array
				$this->args = array_filter($this->args);

				//hook retrieve posts where clause
				add_filter( 'posts_where' , array( $this, 'search_posts_where' ) );

				$args = array_merge($this->args, array(
					'suppress_filters' 	=> false,
					//'no_found_rows'		=> true,
				));

				//$this->items = get_posts( $args );

				// get all post count
				//$nb_args = $args;
				//$nb_args['posts_per_page'] = '-1';
 				//$nb_args['fields'] = 'ids';
				//$this->items_nr = (int) count( get_posts( $nb_args ) );

				$wpquery = new WP_Query( $args );
				$this->items = $wpquery->posts;
				$this->items_nr = (int) $wpquery->found_posts;

				if ( $this->opt['debug_query'] == true ) {
					//$query = new WP_Query( $args );
					$dbg_query = $wpquery->request;
				}
			}

			if ( $this->opt['debug_query'] == true ) {
				$dbg_query = preg_replace('/[\n\r\t]*/imu', '', $dbg_query);
				echo '<script>';
				echo 	'console.log("query rows// ' . $this->items_nr . '");';
				echo 	'console.log("query// ' . $dbg_query . '");';
				echo '</script>';
			}

			return $this;
		}

		public function search_posts_where( $where='' ) {

			if( is_admin() ) {
				$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

				//search text
				$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';
				$search_text = trim( $search_text );
				$esc_search_text = esc_sql($search_text);
				$esc_search_text = $this->the_plugin->escape_mysql_regexp( $esc_search_text );

				if ( isset( $search_text ) && $search_text!='' ) {
					if ( $search_text!='' && $this->the_plugin->utf8->strlen($search_text)<200 ) {
					//if ( $search_text!='' && strlen($search_text)<200 ) {
						if ( $this->opt["custom_table"] != '' ) {
							$search_fields = $this->opt["search_box"]['fields'];
							$__where = array();
							foreach( $search_fields as $v) {
								$__where[] = "a.$v regexp '" . $esc_search_text . "'";
							}
							$__where = implode(' OR ', $__where);
							if (count($search_fields) > 1 ) {
								$where .= " AND ( $__where ) ";
							}
							else {
								$where .= " AND $__where ";
							}
						}
						else {
							$where .= " AND ( post_title regexp '" . $esc_search_text . "' OR post_content regexp '" . $esc_search_text . "' ) ";
						}
					}
				}
			}
			return $where;
		}

		private function getAvailablePostStatus()
		{
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			//post type
			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';
			$post_type = trim( $post_type );
			$qClause = '';
			if ( $post_type!='' && $post_type!='all' )
				$qClause .= " AND post_type = '" . ( esc_sql($post_type) ) . "' ";
			else
				$qClause .= " AND post_type IN ( " . implode( ',', array_map( array($this->the_plugin, 'prepareForInList'), array_keys($this->get_list_postTypes()) ) ) . " ) ";

			//search text
			$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';
			$search_text = trim( $search_text );
			if ( $search_text!='' && $this->the_plugin->utf8->strlen($search_text)<200 )
				$qClause .= " AND ( post_title regexp '" . ( esc_sql($search_text) ) . "' OR post_content regexp '" . ( esc_sql($search_text) ) . "' ) ";
			
			$sql = "SELECT count(id) as nbRow, post_status, post_type FROM " . ( $this->the_plugin->db->prefix ) . "posts WHERE 1 = 1 ".$qClause." group by post_status";
			$sql = preg_replace('~[\r\n]+~', "", $sql);
			//$sql = $wpdb->prepare( $sql );

			return $this->the_plugin->db->get_results( $sql, ARRAY_A );
		}

		private function get_list_postTypes()
		{
			// overwrite wrong post-type value
			if( !isset($this->opt['list_post_types']) ) $this->opt['list_post_types'] = 'all';

			// custom array case
			if( is_array($this->opt['list_post_types']) && count($this->opt['list_post_types']) > 0 ) return $this->opt['list_post_types'];

			// all case
			//$_builtin = get_post_types(array('show_ui' => TRUE, 'show_in_nav_menus' => TRUE, '_builtin' => TRUE), 'objects');
            $_builtin = get_post_types(array('show_ui' => TRUE, '_builtin' => TRUE), 'objects');
			if ( !is_array($_builtin) || count($_builtin)<0 )
				$_builtin = array();

			//$_notBuiltin = get_post_types(array('show_ui' => TRUE, 'show_in_nav_menus' => TRUE, '_builtin' => FALSE), 'objects');
            $_notBuiltin = get_post_types(array('show_ui' => TRUE, '_builtin' => FALSE), 'objects');
			if ( !is_array($_notBuiltin) || count($_notBuiltin)<0 )
				$_notBuiltin = array();
				
			$exclude = array();
			$ret = array_merge($_builtin, $_notBuiltin);
			if (!empty($exclude)) foreach ( $exclude as $exc) if ( isset($ret["$exc"]) ) unset($ret["$exc"]);
  
			return $ret;
		}
		
		public function post_statuses_filter()
		{
			$html = array();

			$availablePostStatus = $this->getAvailablePostStatus();
			
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			$curr_post_status = isset($ses['post_status']) && trim($ses['post_status']) != "" ? $ses['post_status'] : 'all';

			if( $this->opt['post_statuses'] == 'all' ){
				$postStatuses = array(
				    'all'   	=> __('All', $this->the_plugin->localizationName),
				    'publish'   => __('Published', $this->the_plugin->localizationName),
				    'draft'   	=> __('Draft', $this->the_plugin->localizationName),
				    'future'    => __('Scheduled', $this->the_plugin->localizationName),
				    'private'   => __('Private', $this->the_plugin->localizationName),
				    'pending'   => __('Pending Review', $this->the_plugin->localizationName)
				);
			}
			else{
				die('invalid value of <i>post_statuses</i>. Only implemented value is: <i>all</i>!');
			}


			$html[] = 		'<ul class="subsubsub psp-post_status-list">';

			$cc = 0;
			// add into _postStatus array only if have equivalent into query results
			$_postStatus = array();
			$totals = 0;
			foreach ($availablePostStatus as $key => $value){

				if( !in_array($value["post_status"], array("auto-draft", "inherit")) ) {
					if( in_array($value['post_status'], array_keys($postStatuses))){
						 
						$_postStatus[$value['post_status']] = $value['nbRow'];
						$totals = $totals + $value['nbRow'];
					}
				}
			}

			foreach ($postStatuses as $key => $value){
				$cc++;

				if( $key == 'all' || in_array($key, array_keys($_postStatus)) ){
					$html[] = 		'<li class="ocs_post_status">';
					$html[] = 			'<a href="#post_status=' . ( $key ) . '" class="' . ( $curr_post_status == $key ? 'current' : '' ) . '" data-post_status="' . ( $key ) . '">';
					$html[] = 				$value . ' <span class="count">(' . ( ( $key == 'all' ? $totals : $_postStatus[$key] ) ) . ')</span>';
					$html[] = 			'</a>' . ( count($_postStatus) > ($cc) ? ' |' : '');
					$html[] = 		'</li>';
				}
			}

			$html[] = 		'</ul>';

			return implode("\n", $html);
		}


		/**
		 * Media files
		 *
		 */
		private function post_media_getQuery( $key='' ) {
			
				$nb_args = array();
				switch ($key) {

					case 'smushed':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> array('reduced', 'nosave'),
									'type'    	=> 'CHAR',
									'compare' 	=> 'IN'
								)
							)
						));
						break;

					case 'not_processed':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> '',
									'compare' 	=> 'NOT EXISTS'
								)
							)
						));
						break;

					case 'with_errors':
						$nb_args = array_merge($nb_args, array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key'     	=> 'psp_smushit_status',
									'value'   	=> 'invalid',
									'type'    	=> 'CHAR',
									'compare' 	=> '='
								)
							)
						));
						break;

					default:
						break;
				}
				return $nb_args;
		}
		
		private function post_media_statusDetails()
		{
			$ret = array();

			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			//post type
			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';
			$post_type = trim( $post_type );


			$args = array_merge($this->args, array(
				'post_type'			=> 'attachment',
				'post_status'		=> 'inherit',
				'post_mime_type'	=> array('image/jpeg', 'image/jpg', 'image/png')
			));
			
			// remove empty array
			$args = array_filter( $args );

			//hook retrieve posts where clause
			add_filter( 'posts_where' , array( &$this, 'search_posts_where' ) );

			$args = array_merge($args, array(
				'suppress_filters' => false
			));

			// get all post count
			$nb_args = $args;
			$nb_args['posts_per_page'] = '-1';
			$nb_args['fields'] = 'ids';
			
			$postStatuses = $this->post_media_status();

			foreach ($postStatuses as $key => $value){

				if ( $key == 'all' ) continue 1;
				
				$nb_args = array_merge( $nb_args, $this->post_media_getQuery( $key ) );

				$ret["$key"] = array(
					'post_status'	=> $key,
					'nbRow'			=> (int) count( get_posts( $nb_args ) )
				);
			}
			return $ret;
		}
		
		private function post_media_status() {

			$postStatuses = array(
				'all'   			=> __('All', $this->the_plugin->localizationName),
				'smushed'   		=> __('Compressed', $this->the_plugin->localizationName),
				'not_processed'   	=> __('Not processed', $this->the_plugin->localizationName),
				'with_errors'   	=> __('With errors', $this->the_plugin->localizationName)
			);
			return $postStatuses;
		}
		
		public function post_media_filter( $return='output' )
		{
			$html = array();

			$availablePostStatus = $this->post_media_statusDetails();
			
			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];

			$curr_post_status = isset($ses['post_status']) && trim($ses['post_status']) != "" ? $ses['post_status'] : 'all';

			if( $this->opt['post_statuses'] == 'all' ){
				$postStatuses = $this->post_media_status();
			}
			else{
				die('invalid value of <i>post_statuses</i>. Only implemented value is: <i>all</i>!');
			}


			$html[] = 		'<ul class="subsubsub psp-post_status-list">';

			$cc = 0;
			// add into _postStatus array only if have equivalent into query results
			$_postStatus = array();
			$totals = 0;
			foreach ($availablePostStatus as $key => $value){

				if( in_array($value['post_status'], array_keys($postStatuses))){
					$_postStatus[$value['post_status']] = $value['nbRow'];
					$totals = $totals + $value['nbRow'];
				}
			}

			foreach ($postStatuses as $key => $value){
				$cc++;

				if ( $return == 'array' && $key == 'all' ) unset($postStatuses[$key]);
					
				if( $key == 'all' || in_array($key, array_keys($_postStatus)) ){
					
					$html[] = 		'<li class="ocs_post_status">';
					$html[] = 			'<a href="#post_status=' . ( $key ) . '" class="' . ( $curr_post_status == $key ? 'current' : '' ) . '" data-post_status="' . ( $key ) . '">';
					$html[] = 				$value . ' <span class="count">(' . ( ( $key == 'all' ? $totals : $_postStatus[$key] ) ) . ')</span>';
					$html[] = 			'</a>' . ( count($_postStatus) > ($cc) ? ' |' : '');
					$html[] = 		'</li>';
				} else {
					if ( $return == 'array' ) unset($postStatuses[$key]);
				}
			}

			$html[] = 		'</ul>';
			
			if ( $return == 'array' ) return $postStatuses;

			return implode("\n", $html);
		}

		private function get_pagination()
		{
			$html = array();

			$ses = $_SESSION['pspListTable'][$this->opt['id']]['params'];
			$posts_per_page = ( isset($ses['posts_per_page']) ? $ses['posts_per_page'] : $this->opt['items_per_page'] );
			$paged = ( isset($ses['paged']) ? $ses['paged'] : 1 );
			$total_pages = ceil( $this->items_nr / $posts_per_page );

			if( $this->opt['show_pagination'] ){
				$html[] = 	'<div class="psp-list-table-right-col" id="admin-display-pagination">';


				$html[] = 		'<div class="psp-box-show-per-pages">';
				$html[] = 			'<select name="psp-post-per-page" id="psp-post-per-page" class="psp-post-per-page">';


				$html[] = 				'<option val="1" ' . ( $posts_per_page == 1 ? 'selected' : '' ). '>1</option>';
				foreach( range(5, 50, 5) as $nr => $val ){
					$html[] = 			'<option val="' . ( $val ) . '" ' . ( $posts_per_page == $val ? 'selected' : '' ). '>' . ( $val ) . '</option>';
				}
				foreach( range(100, 500, 100) as $nr => $val ){
					$html[] = 			'<option val="' . ( $val ) . '" ' . ( $posts_per_page == $val ? 'selected' : '' ). '>' . ( $val ) . '</option>';
				}

				$html[] = 				'<option value="all" ' . ($posts_per_page == -1 ? 'selected' : '') . '>';
				$html[] =				__('Show All', $this->the_plugin->localizationName);
				$html[] = 				'</option>';
				$html[] =			'</select>';
				$html[] = 			'<label for="psp-post-per-page">' . __('per page', $this->the_plugin->localizationName) . '</label>';
				$html[] = 		'</div>';

				$html[] = 		'<div class="psp-list-table-pagination tablenav">';

				$html[] = 			'<div class="tablenav-pages">';
				$html[] = 				'<span class="displaying-num">' . ( $this->items_nr ) . ' ' . __('items', $this->the_plugin->localizationName) . '</span>';
				if( $total_pages > 1 ){

				$html[] = 				'<span class="pagination-links"><a class="first-page ' . ( $paged == 1 ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the first page', $this->the_plugin->localizationName) . '" href="#paged=1">«</a>';
					$html[] = 				'<a class="prev-page ' . ( $paged == 1 ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the previous page', $this->the_plugin->localizationName) . '" href="#paged=' . ( $paged > 2 ? ($paged - 1) : 1 ) . '">‹</a>';
					$html[] = 				'<span class="paging-input"><input class="current-page" title="' . __('Current page', $this->the_plugin->localizationName) . '" type="text" name="paged" value="' . ( $paged ) . '" size="2" style="width: 45px;"> ' . __('of', $this->the_plugin->localizationName) . ' <span class="total-pages">' . ( ceil( $this->items_nr / $this->args['posts_per_page'] ) ) . '</span></span>';
					$html[] = 				'<a class="next-page ' . ( ( $paged == ($total_pages)) ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the next page', $this->the_plugin->localizationName) . '" href="#paged=' . ( $paged < $total_pages ? $paged + 1 : $total_pages ) . '">›</a>';
					$html[] = 				'<a class="last-page ' . ( $paged ==  ($total_pages - 1) ? 'disabled' : '' ) . ' psp-jump-page" title="' . __('Go to the last page', $this->the_plugin->localizationName	) . '" href="#paged=' . ( $total_pages ) . '">»</a></span>';
				}
				$html[] = 			'</div>';
				$html[] = 		'</div>';

				$html[] = 	'</div>';
			}

			return implode("\n", $html);
		}

		public function print_header()
		{
			$nb_cols = 0;
			$html = array();
			$ses = isset($_SESSION['pspListTable'][$this->opt['id']]['params']) ? $_SESSION['pspListTable'][$this->opt['id']]['params'] : array();

			$post_type = isset($ses['post_type']) && trim($ses['post_type']) != "" ? $ses['post_type'] : '';

			// start psp-list-table-header
			$html[] = '<div id="psp-list-table-header">';

			// start psp-list-table-header-row-first
			$html[] = 	'<div class="psp-list-table-header-row-first">';

			if( trim($this->opt["custom_table"]) == ""){
				$html[] = '<div class="psp-list-table-left-col">';

 				// if NOT smushit
                if ( !in_array(
                	$_SESSION['pspListTable'][$this->opt['id']]['id'],
                	array('pspSmushit', 'pspTinyCompress')
                )) {

					$html[] = 		'<select name="psp-filter-post_type" class="psp-filter-post_type">';
					$html[] = 			'<option value="all" >';
					$html[] =			__('Show All', $this->the_plugin->localizationName);
					$html[] = 			'</option>';

					if ( in_array($_SESSION['pspListTable'][$this->opt['id']]['id'], array('pspSmushit', 'pspTinyCompress')) ) { // smushit
						$filterArr = $this->post_media_filter('array');
					} else {
						$filterArr = $this->get_list_postTypes();
					}
					
					foreach ( $filterArr as $name => $postType ){

						$html[] = 		'<option ' . ( $name == $post_type ? 'selected' : '' ) . ' value="' . ( $this->the_plugin->escape($name) ) . '">';
						$html[] = 			( is_object($postType) ? ucfirst($this->the_plugin->escape($name)) : ucfirst($name) );
						$html[] = 		'</option>';
					}
					$html[] = 		'</select>';

				} // end if NOT smushit!


				if( $this->opt['show_statuses_filter'] ){

					// if is smushit
                    if ( in_array(
                    	$_SESSION['pspListTable'][$this->opt['id']]['id'],
                    	array('pspSmushit', 'pspTinyCompress')
                    )) {

						$html[] = $this->post_media_filter();
					}
					// end if is smushit!
					// if NOT smushit
					else {
						$html[] = $this->post_statuses_filter();
					}
					// end if NOT smushit
				}
				$html[] = 		'</div>';
				$nb_cols++;

				if( $this->opt['search_box'] ){

					$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';

					$html[] = 	'<div class="psp-list-table-right-col" id="searchbox-admin">';
					$html[] = 		'<div class="psp-list-table-search-box psp-search-standard-design">';
					$html[] = 			'<input type="text" name="psp-search-text" id="psp-search-text" placeholder="' . __('Search', $this->the_plugin->localizationName) . '" class="'.($search_text!='' ? 'search-highlight' : '').'" >';
					$html[] = 			'<button class="psp-search-btn" name="psp-search-btn"><span class="psp-checks-search3"></span></button>';
					//$html[] = 			'<input type="button" name="psp-search-btn" id="psp-search-btn" class="button">';
					$html[] = 		'</div>';
					$html[] = 	'</div>';
					$nb_cols++;
				}

				if( $this->opt['show_category_filter'] && 3==4 ){
					$html[] = '<div class="psp-list-table-left-col" >';
					$html[] = 	'<select name="psp-filter-post_type" class="psp-filter-post_type">';
					$html[] = 		'<option value="all" >';
					$html[] =		__('Show All', $this->the_plugin->localizationName);
					$html[] = 		'</option>';
					$html[] =	'</select>';
					$html[] = '</div>';
					$nb_cols++;
				}
			}else{
				if (1) {
					// dropdown filter fields
					$filter_fields = isset($this->opt["filter_fields"]) && !empty($this->opt["filter_fields"])
						? $this->opt["filter_fields"] : array();

					$html[] = '<div class="psp-list-table-left-col '. $this->opt["custom_table"] .'">';
					foreach ($filter_fields as $field => $vals) {

						$field_val = isset($ses["$field"]) ? (string) trim($ses["$field"]) : '--all--';
						$include_all = isset($vals['include_all']) ? $vals['include_all'] : false;

						// drowdown options list
						$options = isset($vals['options']) ? $vals['options'] : array();
						if ( isset($vals['options_from_db']) && $vals['options_from_db'] ) {
							$_options = $this->get_filter_from_db( $field );
							$options = array_merge($options, $_options);
						}

						if ( $include_all ) { // && count($options) > 1
							// fixed: I've replace array_merge with array_replace, to maintain keys
							$options = array_replace(array(), array(
								'--all--' 		=> __('Show All', $this->the_plugin->localizationName),
							), $options);
						}

						$display = isset($vals['display']) && ('links' == $vals['display']) ? 'links' : 'default';
						if ( 'links' == $display ) {

							$_options = array();

							$html[] = 	'<ul class="subsubsub psp-filter-general_field" data-filter_field="'.$field.'">';

							$totals = 0;
							foreach ($options as $opt_key => $opt_text) {
								$_options["$opt_key"] = array('text' => $opt_text, 'nb' => 0);

								if ( '--all--' == $opt_key ) continue 1;

								if ( isset($this->filter_fields["$field"], $this->filter_fields["$field"]["count"],
									$this->filter_fields["$field"]["count"]["$opt_key"]) ) {
									$_options["$opt_key"]['nb'] = (int) $this->filter_fields["$field"]["count"]["$opt_key"]->__nb;
								}
								$totals += $_options["$opt_key"]['nb'];
							}
							$_options["--all--"]['nb'] = (int) $totals;

							$cc = 0;
							foreach ($_options as $opt_key => $opt_vals) {
								$cc++;
								
								if ( ('all' == $opt_key) && !$include_all ) continue 1;

								$html[] = 	'<li class="ocs_post_status">';
								// || ( 'all' == $opt_key && empty($field_val) )
								$html[] = 		'<a href="#'.$field.'=' . ( $opt_key ) . '" class="' . ( ( (string) $opt_key === (string) $field_val ) ? 'current' : '' ) . '" data-filter_val="' . ( $opt_key ) . '">';
								$html[] = 			$this->the_plugin->escape($opt_vals['text']) . ' <span class="count">(' . ( $opt_vals['nb'] ) . ')</span>';
								$html[] = 		'</a>' . ( count($_options) > ($cc) ? ' |' : '');
								$html[] = 	'</li>';
							}

							$html[] = 	'</ul>';

						}
						else {

							// dropdown html
							$html[] = 		'<select name="psp-filter-'.$field.'" class="psp-filter-general_field" data-filter_field="'.$field.'">';
							if ( isset($vals['title']) ) {
								$html[] =		'<option value="" disabled="disabled">';
								$html[] =			$vals['title'];
								$html[] = 		'</option>';
							}
							//if ( $include_all && count($options) > 1 ) {
							//	$html[] = 		'<option value="all" >';
							//	$html[] =			__('Show All', $this->the_plugin->localizationName);
							//	$html[] = 		'</option>';
							//}
				            foreach ( $options as $opt_key => $opt_text ){
								$html[] = 		'<option ' . ( (string) $opt_key === (string) $field_val ? 'selected' : '' ) . ' value="' . ( $this->the_plugin->escape($opt_key) ) . '">';
								$html[] = 			$this->the_plugin->escape($opt_text);
								$html[] = 		'</option>';
				            }
							$html[] = 		'</select>';

						}
					} // end foreach
					$html[] = '</div>';
					$nb_cols++;

					//$html[] = '<div class="psp-list-table-left-col">'
					//    . '<span>Number of rows: ' . $this->items_nr . '</span>'
					//. '</div>';
					
					// search box
					$search_box = isset($this->opt['search_box']) ? $this->opt['search_box'] : false;
					$search_box = is_array($search_box) && isset($search_box['fields']) ? $search_box : false;

					if( !empty($search_box) ){
						$search_text = isset($ses['search_text']) ? $ses['search_text'] : '';

						$search_title = isset($search_box['title'])
							? $search_box['title'] : __('Search', $this->the_plugin->localizationName);
							
						$search_fields = isset($search_box['fields']) ? implode(',', $search_box['fields']) : '';

						$html[] = 	'<div class="psp-list-table-right-col '. $this->opt["custom_table"] .'">';
						$html[] = 		'<div class="psp-list-table-search-box psp-search-standard-design">';
						$html[] = 			'<input type="text" name="psp-search-text" id="psp-search-text" placeholder="Search" value="'.($search_text).'" class="'.($search_text!='' ? 'search-highlight' : '').'" />';
						$html[] = 			'<button class="psp-search-btn" name="psp-search-btn"><span class="psp-checks-search3"></span></button>';
						// $html[] = 			'<input type="button" name="psp-search-btn" id="psp-search-btn" class="psp-form-button-small psp-form-button-primary" />';
						$html[] = 		'</div>';
						$html[] = 	'</div>';
						$nb_cols++;
					} // end search box
				}
			}

			$html[] = 	'</div>'; // end psp-list-table-header-row-first

			// start psp-list-table-header-row-second
			$html[] = 	'<div class="psp-list-table-header-row-second">';

			// buttons
			if ( $this->opt["show_header_buttons"] ) {
				if( isset($this->opt['mass_actions']) && ($this->opt['mass_actions'] === false) ){
					$html[] = '<div class="psp-list-table-left-col '. $this->opt["custom_table"] .'" style="padding-top: 5px;">';
					$html[] = 	'<input type="button" value="' . __('Add Keyword', $this->the_plugin->localizationName) . '" class="psp-form-button-small psp-form-button-success" id="psp-submit-to-reporter">';
					$html[] = 	'<input type="button" value="' . __('Delete', $this->the_plugin->localizationName) . '" class="psp-form-button-small psp-form-button-info" id="psp-submit-to-reporter">';
					$html[] = 	'<input type="button" value="' . __('Add Competitor', $this->the_plugin->localizationName) . '" class="psp-form-button-small psp-form-button-warning" id="psp-submit-to-reporter">';
					$html[] = '</div>';
				}elseif( isset($this->opt['mass_actions']) && is_array($this->opt['mass_actions']) && ! empty($this->opt['mass_actions']) ){
					$html[] = '<div class="psp-list-table-left-col '. $this->opt["custom_table"] .'" style="padding-top: 5px;">&nbsp;';

					foreach ($this->opt['mass_actions'] as $key => $value){
						$html[] = 	'<input type="button" value="' . ( $value['value'] ) . '" id="psp-' . ( $value['action'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['color'] ) . '">';
					}
					$html[] = '</div>';
				}else{
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
					$html[] = 	'<input type="button" value="' . __('Auto detect focus keyword for All', $this->the_plugin->localizationName) . '" id="psp-all-auto-detect-kw" class="psp-form-button-small psp-form-button-info">';
					$html[] = 	'<input type="button" value="' . __('Optimize All', $this->the_plugin->localizationName) . '" id="psp-all-optimize" class="psp-form-button-small psp-form-button-info">';
					$html[] = '</div>';
				}

				$nb_cols++;
			}
			else{
				$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;</div>';
				$nb_cols++;
			}

			// show top pagination
			if ( !($nb_cols%2) ) {
				//$html[] = '<div style="padding-top: 5px;" class="psp-list-table-left-col">&nbsp;</div>';
			}
			$html[] = $this->get_pagination();

			$html[] = 	'</div>';
			// end psp-list-table-header-row-second

			$html[] = '</div>';
			// end psp-list-table-header

            echo implode("\n", $html);

			return $this;
		}

		public function print_main_table( $items )
		{
			$html = array();

			// start psp-list-table-posts
			$html[] = '<div id="psp-list-table-posts">';
			$html[] = 	'<table class="psp-table">';
			$html[] = 		'<thead>';
			$html[] = 			'<tr>';

			foreach ($this->opt['columns'] as $key => $value){
				if( $value['th'] == 'checkbox' ){
					$html[] = '<th class="checkbox-column" width="20"><input type="checkbox" id="psp-item-check-all" checked></th>';
				}
				else{
					$html[] = '<th' . ( isset($value['width']) && (int)$value['width'] > 0 ? ' width="' . ( $value['width'] ) . '"' : '' ) . '' . ( isset($value['align']) && $value['align'] != "" ? ' align="' . ( $value['align'] ) . '"' : '' ) . '>' . ( $value['th'] ) . '</th>';
				}
			}

			$html[] = 			'</tr>';
			$html[] = 		'</thead>';

			$html[] = 		'<tbody>';

			if( $this->opt['id'] == 'pspPageOptimization' ){
				//use to generate meta keywords, and description for your requested item
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/seo-check-class/seo.class.php' );
				$seo = pspSeoCheck::getInstance();
			}
			
			$show_notice = false;
			if ( isset($this->opt['notices']['default']) ) {
				if ( isset($this->opt['notices']['default_clause'])
					&& $this->opt['notices']['default_clause']=='empty'
					&& count($this->items) <= 0 ) {
					$show_notice = true;
					$html[] = '<tr><td colspan=15 style="height: 37px; text-align: left;">' . $this->opt['notices']['default'] . '</td></tr>';
				}
			}

			foreach ($this->items as $post) { // main foreach
				if( isset($post->ID) ){
					$item_data = array(
						'score' 	=> get_post_meta( $post->ID, 'psp_score', true )
					);
				}
				//continue 1; //DEBUG

				$html[] = '<tr';
				$html[] = 	' data-itemid="' . ( isset($post->ID) ? $post->ID : $post['id'] ) . '"';
				if ( is_array($post) && isset($post['__tr_css']) && ! empty($post['__tr_css']) ) {
					$html[] = ' class="' . ( $post['__tr_css'] ) . '"';
				}
				$html[] = '>';
				foreach ($this->opt['columns'] as $key => $value) { // columns foreach

					$html[] = '<td style="'
					. ( isset($value['align']) && $value['align'] != "" ? 'text-align:' . ( $value['align'] ) . ';' : '' ) . ''
					. ( isset($value['css']) && count($value['css']) > 0 ? $this->print_css_as_style($value['css']) : '' ) . ''
					. '" class="' . ( isset($value['class']) ? $value['class'] : '' ) . '">';

					if( $value['td'] == 'checkbox' ){
						$html[] = '<input type="checkbox" class="psp-item-checkbox" name="psp-item-checkbox-' . ( isset($post->ID) ? $post->ID : $post['id'] ) . '" checked>';
					}
					elseif( $value['td'] == '%score%' ){
						$score = isset($item_data['score']) && ! empty($item_data['score'])
							? (float) $item_data['score'] : 0;
						$html[] = $this->the_plugin->build_score_html_container( $score );
					}
					elseif( $value['td'] == '%focus_keyword%' ){
						$focus_kw = ''; //get_post_meta( $post->ID, 'psp_kw', true );
						$psp_meta = $this->the_plugin->get_psp_meta( $post->ID );
						$fieldsParams = array(
							'mfocus_keyword'			=> isset($psp_meta['mfocus_keyword']) ? $psp_meta['mfocus_keyword'] : ''
						);

						$html[] = '<div class="psp-focus-kw-box">';
						$html[] = 	'<div class="psp-fields-params" style="display: none;">' . htmlentities(json_encode( $fieldsParams )). '</div>';
						$html[] = 	'<input type="text" class="psp-text-field-kw" id="psp-focus-kw-' . ( $post->ID ) . '" value="' . ( $focus_kw ) . '" placeholder="type something and hit enter or tab" />';
						//$html[] = '<input type="button" class="psp-auto-detect-kw-btn psp-form-button-small psp-form-button-info" value="' . __('Auto detect', $this->the_plugin->localizationName) . '" />';
						$html[] = '</div>';
					}
					elseif( $value['td'] == '%seo_report%' ){
						$html[] = '<a class="psp-button green psp-seo-report-btn psp-form-button-small psp-form-button-success" href="#" data-itemid="' . ( $post->ID ) . '">
                                    	' . __('SEO Report', $this->the_plugin->localizationName) . '
                                    </a>';
					}
					elseif( $value['td'] == '%auto_detect%' ){
						$html[] = '<input type="button" class="psp-auto-detect-kw-btn psp-form-button-small psp-form-button-info" value="' . __('Auto detect', $this->the_plugin->localizationName) . '" />';
					}
					elseif( strtolower($value['td']) == '%id%' ){
						$html[] = is_object($post) ? (isset($post->ID) ? $post->ID : $post->id) : (isset($post['ID']) ? $post['ID'] : $post['id']);
					}
					elseif( $value['td'] == '%title%' ){
						$html[] = '<input type="hidden" id="psp-item-title-' . ( $post->ID ) . '" value="' . ( str_replace('"', "'", $post->post_title) ) . '" />';
						$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">';
						
						if ( $post->post_status == 'inherit' && $post->post_type == 'attachment' ) { // media image file

							$html[] = 	( $post->post_title . ( isset($post->post_mime_type) && preg_match('/^image\//i', $post->post_mime_type) > 0 ? ' <span class="item-state">- ' . strtoupper(str_replace('image/', '', $post->post_mime_type)) : '</span>') );
							$html[] = '</a>';
							$html[] = '
							<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post->ID ) . '">
								<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">Edit</a>
								 | <a href="' . ( wp_get_attachment_url( $post->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
							</span>';
						} else {
						
							$html[] = 	( $post->post_title . ( $post->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($post->post_status) : '</span>') );
							$html[] = '</a>';
						}
					}
					elseif( $value['td'] == '%title_and_actions%' ){
						$html[] = '<input type="hidden" id="psp-item-title-' . ( $post->ID ) . '" value="' . ( str_replace('"', "'", $post->post_title) ) . '" />';
						$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">';
						$html[] = 	( $post->post_title . ( $post->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($post->post_status) : '</span>') );
						$html[] = '</a>';
						
						$__row_actions = $this->the_plugin->edit_post_inline_data( $post->ID, $seo );
						$html[] = '
						<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post->ID ) . '">
							<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $post->ID)) . '">Edit</a>
							 | <a href="#" class="editinline" title="' . __('Edit this item inline', $this->the_plugin->localizationName) . '">' . __('Quick Edit', $this->the_plugin->localizationName) . '</a>
							 | <a href="' . ( get_permalink( $post->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
						</span>';
						$html[] = '
						<div id="psp-inline-row-data-' . ( $post->ID ) . '" class="hide" style="display: none;">
							'.$__row_actions.'
						</div>
						';
					}
					elseif( $value['td'] == '%custom_title%' ){
						$html[] = '<i>' . ( $post['title'] ) . '</i>';
					}
					elseif( $value['td'] == '%buttons_group%' ){

						if( isset($value['option']) && is_array($value['option']) && count($value['option']) > 0 ){
							foreach ($value['option'] as $opk => $opv ) {

								$_value = $opv['value'];
								$_color = isset($opv['color']) ? $opv['color'] : 'gray';
								$_icon = isset($opv['icon']) ? $opv['icon'] : '';

								if ( isset($opv['value_change']) ) {
									if ( isset($post['publish']) && ($post['publish'] != 'Y') ) {
										$_value = $opv['value_change'];
										$_icon = isset($opv['icon_change']) ? $opv['icon_change'] : '';
									}
								}

								if ( ! empty($_icon) ) {
									$html[] = '<a href="#" class="psp-form-button-group psp-' . ( $opv['action'] ) . '" title="' . ( $_value ) . '">' . ( $_icon ) . '</a>';
								}
								else {
									$html[] = '<input type="button" value="' . ( $_value ) . '" class="psp-form-button-small psp-form-button-' . ( $_color ) . ' psp-' . ( $opv['action'] ) . '">';
								}
							} //end foreach
						}
					}
					elseif( $value['td'] == '%button%' ){
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $value['option']['value'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '">';
					}
					elseif( $value['td'] == '%button_publish%' ){
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $post['publish']=='Y' ? $value['option']['value'] : $value['option']['value_change'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '">';
					}
					elseif( $value['td'] == '%button_html5data%' ){
						$__html5data = array();
						foreach ($value['html5_data'] as $ttk=>$ttv) {
							$__html5data[] = "data-" . $ttk . "=\"" . $ttv . "\"";
						}
						$__html5data = ' ' . implode(' ', $__html5data) . ' ';
						$value['option']['color'] = isset($value['option']['color']) ? $value['option']['color'] : 'gray';
						$html[] = 	'<input type="button" value="' . ( $value['option']['value'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['option']['color'] ) . ' psp-' . ( $value['option']['action'] ) . '"
						' . $__html5data . '
						>';
					}
					elseif( $value['td'] == '%date%' ){
						$html[] = '<i>' . ( $post->post_date ) . '</i>';
					}
					else if( $value['td'] == '%created%' ){
						$html[] = '<i>' . ( $post['created'] ) . '</i>';
					}
					elseif( $value['td'] == '%hits%' ){
						$html[] = '<i class="psp-hits">' . ( $post['hits'] ) . '</i>';
					}
					elseif( $value['td'] == '%url%' ){
						$html[] = '<i>' . ( $post['url'] ) . '</i>';
					}
					elseif( $value['td'] == '%bad_url%' ){
						$html[] = '<i>' . ( $post['url'] ) . '</i>';
					}
					elseif( $value['td'] == '%phrase%' ){
						$html[] = '<i>' . ( $post['phrase'] ) . '</i>';
					}
					elseif( $value['td'] == '%referrers%' ){
						$html[] = (trim($post['referrers']) != "" ? '<a href="#referrers" class="psp-button gray psp-btn-referrers-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
					}
					elseif( $value['td'] == '%user_agents%' ){
						$html[] = (trim($post['user_agents']) != "" ? '<a href="#user_agents" class="psp-button gray psp-btn-user_agents-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
					}
					elseif( $value['td'] == '%last_date%' ){
						$html[] = '<i>' . ( $post['data'] ) . '</i>';
					}

					//:: pspSmushit | pspTinyCompress
                    if ( in_array($this->opt['id'], array('pspSmushit', 'pspTinyCompress')) ) {
						
						$id = intval( $post->ID );

						if( $value['td'] == '%thumbnail%' ){

							$attachment_img_thumb = wp_get_attachment_image( $id, 'thumbnail' );
							$patterns = array(
								'/<img(.*?)width="(.*?)"(.*?)>/',
								'/<img(.*?)height="(.*?)"(.*?)>/'
							);
							$replacements = array(
								'<img\1width="60"\3>',
								'<img\1height="60"\3>'
							);
							$html[] = preg_replace( $patterns, $replacements, $attachment_img_thumb );
						}
						else if( $value['td'] == '%smushit_status%' ){
							
							//$html[] = '<div class="psp-message">';
							//$html[] = 	'<span class="psp-smushit-loading"></span>';
							
							// retrieve the existing value(s) for this meta field. This returns an array
							$meta_new = wp_get_attachment_metadata( $id );

							if ( isset($meta_new['psp_smushit']) && !empty($meta_new['psp_smushit']) ) {
			
								$msg = (array) $this->the_plugin->smushit_show_sizes_msg_details( $meta_new ); $__msg = array();
								if ( isset($meta_new['psp_smushit_errors']) && ( (int) $meta_new['psp_smushit_errors'] ) > 0 ) {
									$status = 'invalid';
									$msg_cssClass = 'psp-error';
									$__msg = array( __('errors occured on compress!', $this->the_plugin->localizationName) );
								} else {
									$status = 'valid';
									$msg_cssClass = 'psp-success';
								}
								$msg = implode('<br />', array_merge($__msg, $msg));
								
								$html[] = '<div id="' . ('psp-smushit-resp-'.$id) . '" class="psp-message ' . $msg_cssClass . '">' . $msg . '</div><br />';
							} else {
								
								$html[] = '<div id="' . ('psp-smushit-resp-'.$id) . '" class="psp-message psp-info">' . __( 'not processed!', $this->the_plugin->localizationName ) . '</div><br />';
							}
							//$html[] = '</div>';
				
						}
					}

					//:: pspPageSpeed
					if( $this->opt['id'] == 'pspPageSpeed' ){
						if( $value['td'] == '%mobile_score%' ){
							$mobile = get_post_meta( $post->ID, 'psp_mobile_pagespeed', true ); 
							
							if( isset($mobile['score']) ){
								$score = isset($mobile['score']) && ! empty($mobile['score'])
									? (int) $mobile['score'] : 0;
								$html[] = $this->the_plugin->build_score_html_container( $score, array(
									'show_score' 	=> true,
									'css_style'		=> 'style="margin-right:4px"',
								));
							}else{
								$html[] = '<i>Never Checked</i>';
							}
						}
						if( $value['td'] == '%desktop_score%' ){
							$desktop = get_post_meta( $post->ID, 'psp_desktop_pagespeed', true ); 
							
							if( isset($desktop['score']) ){
								$score = isset($desktop['score']) && ! empty($desktop['score'])
									? (int) $desktop['score'] : 0;
								$html[] = $this->the_plugin->build_score_html_container( $score, array(
									'show_score' 	=> true,
									'css_style'		=> 'style="margin-right:4px"',
								));
							}else{
								$html[] = '<i>Never Checked</i>';
							}
						}
					}

					//:: pspLinkBuilder
					if( $this->opt['id'] == 'pspLinkBuilder' ){

						if( $value['td'] == '%builder_phrase%' ){
							//$html[] = '<input type="text" value="' . ( $post['phrase'] ) . '" readonly />';
							$html[] = '<ul class="psp-link-builder-phrase">';
							if ( ! empty($post['phrase']) ) {
								$html[] = '<li>' . ( $post['phrase'] ) . '</li>';
							}
							if ( ! empty($post['title']) ) {
								$html[] = '<li>' . ( $post['title'] ) . '</li>';	
							}
							$html[] = '</ul>';
						}
						else if( $value['td'] == '%builder_url%' ){
							//$html[] = '<input type="text" value="' . ( $post['url'] ) . '" readonly />';
							$html[] = '<i>' . ( $post['url'] ) . '</i>';
						}
						else if( $value['td'] == '%builder_rel%' ){
							$html[] = '<i>' . ( $post['rel'] ) . '</i>';
						}
						else if( $value['td'] == '%builder_target%' ){
							$html[] = '<i>' . ( $post['target'] ) . '</i>';
						}
						else if( $value['td'] == '%url_attributes%' ){
							$html[] = (1==1 ? '<a href="#url_attributes" class="psp-button gray psp-btn-url-attributes-lightbox" data-itemid="' . ( $post['id'] ) . '">' . ( __('Show All', $this->the_plugin->localizationName) ) . '</a>' : '-');
						}
						else if( $value['td'] == '%max_rpl%' ){
							//$html[] = '<input type="text" value="' . ( $post['url'] ) . '" readonly />';
							$max_rpl = $post['max_replacements'];
							if ( -1 == $max_rpl ) {
								$max_rpl = 'all';
							}
							$html[] = '<i>' . ( $max_rpl ) . '</i>';
						}
					}

					//:: pspLinkRedirect
					if( $this->opt['id'] == 'pspLinkRedirect' ){
						if( $value['td'] == '%linkred_url%' ){
							//$html[] = '<input type="text" value="' . ( $post['url'] ) . '" readonly />';
							if ( isset($post['redirect_rule']) && ('regexp' == $post['redirect_rule']) ) {
								$html[] = '<i>' . ( $post['url'] ) . '</i>';
							}
							else {
								$html[] = '<a href="' . $post['url'] . '" target="_blank">' . ( $post['url'] ) . '</a>';
							}
						}
						else if( $value['td'] == '%linkred_url_redirect%' ){
							//$html[] = '<input type="text" value="' . ( $post['url_redirect'] ) . '" readonly />';
							if ( isset($post['redirect_rule']) && ('regexp' == $post['redirect_rule']) ) {
								$html[] = '<i>' . ( $post['url_redirect'] ) . '</i>';
							}
							else {
								$html[] = '<a href="' . $post['url_redirect'] . '" target="_blank">' . ( $post['url_redirect'] ) . '</a>';
							}
						}
						else if( $value['td'] == '%redirect_type_and_rule%' ){
							$redirect_rules = array(
								'custom_url' => __('Custom URL', 'psp'),
								'regexp' => __('Regexp', 'psp'),
							);
							$redirect_rule = isset($redirect_rules["{$post['redirect_rule']}"])
								? $redirect_rules["{$post['redirect_rule']}"] : 'unknown';

							$redirect_type = $this->the_plugin->get_redirect_type(array(
								'settings'		=> array(),
								'row'			=> $post,
							));
							$html[] = '<div>';
							$html[] = 	'<div><i>' . $redirect_rule . '</i></div>';
							$html[] = 	'<div><i>' . $redirect_type['title'] . '</i></div>';
							$html[] = '</div>';
						}
						else if( $value['td'] == '%redirect_type%' ){
							$redirect_type = $this->the_plugin->get_redirect_type(array(
								'settings'		=> array(),
								'row'			=> $post,
							));
							$html[] = '<i>' . $redirect_type['title'] . '</i>';
						}
						else if( $value['td'] == '%redirect_rule%' ){
							$redirect_rules = array(
								'custom_url' => __('Custom URL', 'psp'),
								'regexp' => __('Regexp', 'psp'),
							);
							$redirect_rule = isset($redirect_rules["{$post['redirect_rule']}"])
								? $redirect_rules["{$post['redirect_rule']}"] : 'unknown';
							$html[] = '<i>' . $redirect_rule . '</i>';
						}
						else if( $value['td'] == '%last_check_status%' ){
							$target_details = isset($post['target_status_details'])
								? $post['target_status_details'] : array();
							$target_details = maybe_unserialize( $target_details );

							$target_code = isset($post['target_status_code'])
								? (string) $post['target_status_code'] : '';

							$last_status = __('Never Checked', 'psp');
							$last_css_class = 'psp-message psp-info';
							if ( isset($post['redirect_rule']) && ('regexp' == $post['redirect_rule']) ) {
								$last_status = __('**', 'psp');
								$last_css_class = '';
							}

							if ( 'valid' == $target_code ) {
								$last_status = __('Valid', 'psp');
								$last_css_class = 'psp-message psp-success';
							}
							else if ( 'invalid' == $target_code ) {
								$last_status = __('Invalid', 'psp');
								$last_css_class = 'psp-message psp-error';
							}

							$last_status_details = $last_status;
							if ( isset($target_details['resp_msg']) ) {
								$last_status_details = $target_details['resp_msg'];
							}

							$last_status_check_at = '';
							if ( isset($target_details['last_check_at']) ) {
								$last_status_check_at = $target_details['last_check_at'];
							}

							$html[] = '<div class="psp-last-check-status">';
							$html[] = 	'<div><span title="' . $last_status_details . '" class="' . $last_css_class . '">' . $last_status . '</span></div>';
							$html[] = 	'<div><i>' . $last_status_check_at . '</i></div>';
							$html[] = '</div>';
						}
					}

					//:: pspSocialStats
					if( $this->opt['id'] == 'pspSocialStats' ){
						$page_permalink = get_permalink( $post->ID );

						$socialServices = $this->the_plugin->social_get_allowed_providers();
						$socialData = $this->the_plugin->social_get_stats(array(
							'from'					=> 'listing',
							'cache_life_time'		=> 1800, // in seconds
							'website_url'			=> $page_permalink,
							'postid'				=> $post->ID,
						));

						$dashboard_module_url = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/dashboard/';

						$ssKey =  $value['td'];
						$ssKey = str_replace('%ss_', '', $ssKey);
						$ssKey = str_replace('%', '', $ssKey);
						if ( isset($socialServices["$ssKey"]) ) {

							$ssVal = $socialServices["$ssKey"];

							$socialHtmlBox = $this->the_plugin->social_get_htmlbox(array(
								'from'			=> 'listing',
								'img_src'		=> $dashboard_module_url . 'assets/stats/',
								'ssKey'			=> $ssKey,
								'ssVal'			=> $ssVal,
								'socialData'	=> $socialData,
								'postid'		=> $post->ID,
								'only_counts'	=> array('facebook'),
							));
							$html[] = $socialHtmlBox;
						}
					}

					//:: pspWebDirectories
					if( $this->opt['id'] == 'pspWebDirectories' ){
						if( $value['td'] == '%directory_name%' ){
							$html[] = '<a href="' . ( $post['submit_url'] ) . '" target="_blank">' . ( $post['directory_name'] ) . '</a>';
						}
						elseif( $value['td'] == '%pagerank%' || $value['td'] == '%alexa%' ){
							$html[] = '<code>' . ( $post[$key] ) . '</code>';
						}
						elseif( $value['td'] == '%submit_btn%' ){
							$html[] = '<a href="' . ( $post['submit_url'] ) . '" target="_blank" class="psp-form-button psp-form-button-info psp-btn-submit-website" data-itemid="' . ( $post['id'] ) . '">' . ( __('Submit website', $this->the_plugin->localizationName) ) . '</a>';
						}

						elseif( $value['td'] == '%submit_status%' ){
							// never submited / $post['status'] = 2;
							$html_status = '<div class="psp-message" style="padding: 5px;">' . ( __('Never submited', $this->the_plugin->localizationName) ) . '</div>';
							if( $post['status'] == 2 ){
								$html_status = '<div class="psp-message psp-warning" style="padding: 5px;background-image: none;">' . ( __('Submit in progress', $this->the_plugin->localizationName) ) . '</div>';
							}
							elseif( $post['status'] == 3 ){
								$html_status = '<div class="psp-message psp-error" style="padding: 5px;background-image: none;">' . ( __('Error on submit', $this->the_plugin->localizationName) ) . '</div>';
							}
							elseif( $post['status'] == 1 ){
								$html_status = '<div class="psp-message psp-success" style="padding: 5px;background-image: none;">' . ( __('Submit successfully', $this->the_plugin->localizationName) ) . '</div>';
							}
							
							$html[] = $html_status;
						}
					}

					//:: pspPageHTMLValidation
					if( $this->opt['id'] == 'pspPageHTMLValidation' ){

						// get html verify data
						$html_verify_details = get_post_meta( $post->ID, 'psp_w3c_validation', true );
						
						if( $value['td'] == '%nr_of_errors%' ){
							$nr_of_errors = isset($html_verify_details['nr_of_errors']) ? $html_verify_details['nr_of_errors'] : $value['def'];

							$html[] = '<i class="' . ( $key ) . '">' . $nr_of_errors . '</i>';
						}
						elseif( $value['td'] == '%nr_of_warning%' ){
							$nr_of_warning = isset($html_verify_details['nr_of_warning']) ? $html_verify_details['nr_of_warning'] : $value['def'];

							$html[] = '<i class="' . ( $key ) . '">' . $nr_of_warning . '</i>';
						}
						elseif( $value['td'] == '%status%' ){
							$current_status_css = isset($html_verify_details['status'])
								&& $html_verify_details['status'] == 'invalid' ? 'color: red;' : 'color: green;';

							$current_status = isset($html_verify_details['status'])
								? $html_verify_details['status'] : $value['def'];
							$current_status = isset($html_verify_details['msg']) && ! empty($html_verify_details['msg'])
								? $html_verify_details['msg'] : $current_status;

							// title="' . $current_status . '"
							$html[] = '<strong class="' . ( $key ) . '" style="' . $current_status_css . '">' . $current_status . '</strong>';
						}
						elseif( $value['td'] == '%last_check_at%' ){
							$last_check_at = isset($html_verify_details['last_check_at']) ? $html_verify_details['last_check_at'] : $value['def'];

							$html[] = '<i class="' . ( $key ) . '">' . $last_check_at . '</i>';
						}
						elseif( $value['td'] == '%view_full_report%' ){
							$html[] = '<a target="_blank" href="' . ( 'http://validator.w3.org/check?uri=' . get_permalink( $post->ID ) ) . '" class="psp-button gray">' . ( __('View report', $this->the_plugin->localizationName) ) . '</a>';
						}
					}

					//:: pspSERPKeywords
					if( $this->opt['id'] == 'pspSERPKeywords' ){
						$rank_data = $post;

						if( $value['td'] == '%serp_focus_keyword%' ){
							//$html[] = '<input type="text" value="' . ( $post['focus_keyword'] ) . '" />';
					 
							$html[] = '<a href="#" class="psp-serp-keyword-details" data-keywordID="' . ( $post['id'] ) . '">';
							$html[] =  	$post['focus_keyword'];
							$html[] = '</a>';	      
						}

						else if( $value['td'] == '%serp_url%' ){
							//$html[] = '<input type="text" value="' . ( $post['url'] ) . '" />';
							$html[] = '<i>' . ( $post['url'] ) . '</i>';
						}
						elseif( $value['td'] == '%serp_location%' ){
							$html[] = '<i>' . ( $post['engine_location'] ) . '</i>';
						}

						else if( $value['td'] == '%serp_google%' ){

							if( isset($rank_data) && is_array($rank_data) && count($rank_data) > 0 ){
							/*	// get best rank
								$best_pos = (int) $post['position_best'];
								
								// get worst
								$worst_pos = (int) $post['position_worst'];
								*/
								// current rank
								$current_pos = (int) $rank_data['position'];
								
							/*	// previous rank
								$previous_pos = (int) $rank_data['position_prev'];*/

								/*ai pt stationar clasa de icon :psp-checks-arrows-h, pt arrow up - psp-checks-arrow-up si pt down psp-checks-arrow-down  */
								$html[] = '<span class="the_pos">' . ( $current_pos ) . '<i class="psp-checks-arrow-up"></i></span>';

							/*	//direction icon!
								$icon = 'same';
								if( $current_pos > $previous_pos ){
									$icon = 'down';
								}
								if( $current_pos < $previous_pos ){
									$icon = 'up';
								}
								
								$__notInTop100 = __('Not in top', $this->the_plugin->localizationName);
								$__icon_not100 = '<i class="serp-icon notintop100" title="' . $__notInTop100 . '"></i>';

								$__icon = '<i class="serp-icon ' . $icon . '"></i>';
								$__iconExtra = '';
								if ($icon=='up') {
									$__iconExtra .= '('.($previous_pos==999 ? '~' : '').'&#43;' . ( $previous_pos==999 ? (int) (100 - $current_pos) : (int) ($previous_pos - $current_pos) ) . ')';
								}
								else if($icon=='down') {
									$__iconExtra .= '('.($current_pos==999 ? '~' : '').'&minus;' . ($current_pos==999 ? (int) (100 - $previous_pos) : (int) ($current_pos - $previous_pos) ) . ')';
								}
								$__icon .= $__iconExtra;

								$html[] = '<div class="serp-rank-container">';
								$html[] = 	'<table class="serp-tbody-rank">';
								$html[] = 		'<tbody>';
								$html[] = 			'<tr>';
								$html[] = 					'<td width="83">';
								if( $current_pos==999 ){
									$html[] = 					'<div class="psp-rank-container-block-extra">' . ( $__icon_not100 ) . '</div>';
								}else{
								
									$html[] = 					'<div class="psp-rank-container-block">';
									$html[] = 						'<span class="the_pos">' . ( '#' . $current_pos ) . '</span>';
									
									
									$cur_pos_dir = $previous_pos - $current_pos; 
									$cur_pos_dir_symbol = '';
									if( $cur_pos_dir > 0 ){
										$cur_pos_dir_symbol = '+'; 
									}elseif( $cur_pos_dir < 0 ){
										$cur_pos_dir_symbol = '-'; 
									}
									$html[] = 						'<span class="the_status ' . ( $icon ) . '">' . ( $cur_pos_dir_symbol ) . ( abs($cur_pos_dir) ) . '</span>';
									$html[] = 					'</div>';
								}
								$html[] = 					'</td>';
								$html[] = 					'<td width="52"><div class="psp-rank-container-block-extra">' . ( $best_pos==999 ? $__icon_not100 : '#'.$best_pos ) . '</div></td>';
								$html[] = 					'<td><div class="psp-rank-container-block-extra">' . ( $worst_pos==999 ? $__icon_not100 : '#'.$worst_pos ) . '</div></td>';
								$html[] = 			'</tr>';
								$html[] = 		'</tbody>';
								$html[] = 	'</table>';
								$html[] = '</div>';*/
							}
						}

						else if( $value['td'] == '%serp_start_date%' ){
							$html[] = '<i>' . ( $post['created'] ) . '</i>';
						}

						else if( $value['td'] == '%serp_visits%' ){
							$html[] = '<i>' . ( $post['visits'] ) . '</i>';
						}
					}

					//:: pspFacebookPlanner
					if( $this->opt['id'] == 'pspFacebookPlanner' ){
						
						if( $value['td'] == '%post_id%' ){
							$html[] = $post['id_post'];
						}
						elseif( $value['td'] == '%post_name%' ){
							$__postInfo = get_post( $post['id_post'], OBJECT );
							$html[] = '<input type="hidden" id="psp-item-title-' . ( $post['id'] ) . '" value="' . ( str_replace('"', "'", $__postInfo->post_title) ) . '" />';
							$html[] = '<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $__postInfo->ID)) . '" class="psp-post-name">';
							$html[] = 	( $__postInfo->post_title . ( $__postInfo->post_status != 'publish' ? ' <span class="item-state">- ' . ucfirst($__postInfo->post_status) . '</span>' : '' ) );
							$html[] = '</a>';
							
							$html[] = '
							<span class="psp-inline-row-actions show" id="psp-inline-row-actions-' . ( $post['id'] ) . '">
								<a href="' . ( sprintf( admin_url('post.php?post=%s&action=edit'), $__postInfo->ID)) . '">Edit</a>
								 | <a href="' . ( get_permalink( $__postInfo->ID ) ) . '" target="_blank">' . __('View', $this->the_plugin->localizationName) . '</a>
							</span>';
						}
						else if( $value['td'] == '%status%' ){

							$__statusVals = array(
								0 	=> __( "New", $this->the_plugin->localizationName ),
								1	=> __( "Finished", $this->the_plugin->localizationName ),
								2	=> __( "Running", $this->the_plugin->localizationName ),
								3	=> __( "Error", $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['status'] ];
						}
						else if( $value['td'] == '%attempts%' ){
							$html[] = $post['attempts'];
						}
						else if( $value['td'] == '%response%' ){
							$html[] = $post['response'];
						}
						else if( $value['td'] == '%post_to%' ){
							
							$pg = get_option('psp_fb_planner_user_pages');
							if(trim($pg) != ""){
								$pg = @json_decode($pg);
							}

							$post_to = '';
							$serialize = $post['post_to'];
							$arr = unserialize($serialize);

							if( trim($arr['profile']) == 'on' ) {
								$post_to = '- Profile';
							}

							if( trim($arr['page_group']) != '' ) {
								$page_group = explode('##', $arr['page_group']);
								$post_to .= trim($post_to) != '' ? '<br />' : '';

								if($page_group[0] == 'page') {
									foreach($pg->pages as $k => $v) {
										if($v->id == $page_group[1]) {
											$post_to_title = $v->name;
										}
									}
								}else if($page_group[0] == 'group') {
									foreach($pg->groups as $k => $v) {
										if($v->id == $page_group[1]) {
											$post_to_title = $v->name;
										}
									}
								}

								$post_to .= "- ".(ucfirst($page_group[0])).": " . $post_to_title;
							}

							$html[] = $post_to;
						}
						else if( $value['td'] == '%email_at_post%' ){

							$__statusVals = array(
								'on' 	=> __( 'ON', $this->the_plugin->localizationName ), 
								'off'	=> __( 'OFF', $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['email_at_post'] ];
						}
						else if( $value['td'] == '%repeat_status%' ){

							$__statusVals = array(
								'on' 	=> __( 'ON', $this->the_plugin->localizationName ), 
								'off'	=> __( 'OFF', $this->the_plugin->localizationName )
							);
							$html[] = $__statusVals[ $post['repeat_status'] ];
						}
						else if( $value['td'] == '%repeat_interval%' ){
							$html[] = $post['repeat_interval'];
						}
						else if( $value['td'] == '%run_date%' ){
							$html[] = $post['run_date'];
						}
						else if( $value['td'] == '%started_at%' ){
							$html[] = $post['started_at'];
						}
						else if( $value['td'] == '%ended_at%' ){
							$html[] = $post['ended_at'];
						}
						else if( $value['td'] == '%post_privacy%' ){
							
							$__statusVals = array(
		        				"EVERYONE" => __('Everyone', $this->the_plugin->localizationName),
		        				"ALL_FRIENDS" => __('All Friends', $this->the_plugin->localizationName),
		        				"NETWORKS_FRIENDS" => __('Networks Friends', $this->the_plugin->localizationName),
		        				"FRIENDS_OF_FRIENDS" => __('Friends of Friends', $this->the_plugin->localizationName),
		        				"CUSTOM" => __('Private (only me)', $this->the_plugin->localizationName)
							);
							//$html[] = $__statusVals[ $post['post_privacy'] ];
							$html[] = $post['post_privacy'];
						}
					}

					$html[] = '</td>';
				} // end columns foreach

				$html[] = '</tr>';
				if( $this->opt['id'] == 'pspSERPKeywords' ){
					$html[] = '<tr style="display: none">';
					$html[] = 	'<td colspan="' . ( count($this->opt['columns']) ) . '" rel="">';
					$html[] = 		'<span class="psp-serp-loading">loading ...</span>';
					$html[] = 	'</td>';
					$html[] = '</tr>';
				}
			} // end main foreach

			$html[] = 		'</tbody>';
			$html[] = 	'</table>';

			$html[] = '</div>'; // end psp-list-table-posts

			// start footer
			$html[] = '<div id="psp-list-table-footer">';

			// buttons
			if( trim($this->opt["custom_table"]) == ""){

				if( isset($this->opt['mass_actions']) && ($this->opt['mass_actions'] === false) ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;</div>';
				}elseif( isset($this->opt['mass_actions']) && is_array($this->opt['mass_actions']) && ! empty($this->opt['mass_actions']) ){
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';

					foreach ($this->opt['mass_actions'] as $key => $value){
						$html[] = 	'<input type="button" value="' . ( $value['value'] ) . '" id="psp-' . ( $value['action'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['color'] ) . '">';
					}
					$html[] = '</div>';
				}else{
					$html[] = '<div class="psp-list-table-left-col" style="padding-top: 5px;">&nbsp;';
					$html[] = 	'<input type="button" value="' . __('Auto detect focus keyword for All', $this->the_plugin->localizationName) . '" id="psp-all-auto-detect-kw" class="psp-form-button-small psp-form-button-info">';
					$html[] = 	'<input type="button" value="' . __('Optimize All', $this->the_plugin->localizationName) . '" id="psp-all-optimize" class="psp-form-button-small psp-form-button-info">';
					$html[] = '</div>';
				}
				
				if( $this->opt['id'] == 'pspPageOptimization' ){
					$html[] = '<div id="psp-inline-editpost-boxtpl" style="display: none;">';
					$html[] = $this->the_plugin->edit_post_inline_boxtpl();
					$html[] = '</div>';
				}
			}
			else{
				$html[] = '<div class="psp-list-table-left-col '. $this->opt["custom_table"] .'" style="margin-bottom: 6px;">&nbsp;';
				if( isset($this->opt['mass_actions']) && is_array($this->opt['mass_actions']) && ! empty($this->opt['mass_actions']) ){
					foreach ($this->opt['mass_actions'] as $key => $value){
						$html[] = 	'<input type="button" value="' . ( $value['value'] ) . '" id="psp-' . ( $value['action'] ) . '" class="psp-form-button-small psp-form-button-' . ( $value['color'] ) . '">';
					}
				}
				$html[] = '</div>';
			}

			$html[] = $this->get_pagination();

			$html[] = '</div>'; // end footer

            echo implode("\n", $html);

			return $this;
		}
		
		public function print_html()
		{
			$html = array();

			$items = $this->get_items();

			$html[] = '<input type="hidden" class="psp-ajax-list-table-id" value="' . ( $this->opt['id'] ) . '" />';

			// header
			if( $this->opt['show_header'] === true ) $this->print_header();

			// main table
			$this->print_main_table( $items );

			echo implode("\n", $html);

			return $this;
		}

		private function print_css_as_style( $css=array() )
		{
			$style_css = array();
			if( isset($css) && count($css) > 0 ){
				foreach ($css as $key => $value) {
					$style_css[] = $key . ": " . $value;
				}
			}

			return ( count($style_css) > 0 ? implode(";", $style_css) : '' );
		}


		/**
		 * Update february 2016
		 */
		private function get_filter_from_db( $field='' ) {
			if (empty($field)) return array();
			
			global $wpdb;
			
			$table = $wpdb->prefix  . $this->opt["custom_table"];
			$sql = "SELECT a.$field as __field FROM " . $table . " as a WHERE 1=1 GROUP BY a.$field ORDER BY a.$field ASC;";
		    $res = $wpdb->get_results( $sql, ARRAY_A);
		    
			$rows = array();
		    foreach ($res as $key => $vals){
		    	$id = $vals['__field'];
				$rows["$id"] = ucfirst( $id );
			}
			return $rows;
		}
	
		public function ajax_request( $retType='die', $pms=array() ) {
            $request = array(
                'action'             => isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
                'ajax_id'            => isset($_REQUEST['ajax_id']) ? $_REQUEST['ajax_id'] : '',
            );
            extract($request);
			//var_dump('<pre>', $request, '</pre>'); die('debug...');

            $ret = array(
                'status'        => 'invalid',
                'html'          => '',
            );
			
			if ( in_array($action, array('publish', 'delete', 'bulk_delete')) ) {
				// maintain box html
				$_SESSION['pspListTable'][$request['ajax_id']]['requestFrom'] = 'ajax';
				$this->setup( $_SESSION['pspListTable'][$request['ajax_id']] );
			}

			$opStatus = array();
            if ( 'publish' == $action ) {
            	$opStatus = $this->__action_publish();
            }
			else if ( 'delete' == $action ) {
            	$opStatus = $this->__action_delete();
            }
			else if ( 'bulk_delete' == $action ) {
            	$opStatus = $this->__action_bulk_delete();
            }
			else if ( 'edit_inline' == $action ) {
            	$opStatus = $this->__action_edit_inline();
            }
			$ret = array_merge($ret, $opStatus);
			
			if ( in_array($action, array('publish', 'delete', 'bulk_delete')) ) {
				// create box return html
				ob_start();
				
				$_SESSION['pspListTable'][$request['ajax_id']]['requestFrom'] = 'ajax';
	
				$this->setup( $_SESSION['pspListTable'][$request['ajax_id']] );
				$this->print_html();
				$html = ob_get_contents();
				ob_clean();
				
				$ret['html'] = $html;
				//$ret = array_map('utf8_encode', $ret);
			}

			if ( $retType == 'return' ) { return $ret; }
			else { die( json_encode( $ret ) ); }
		}

		public function __action_publish()
		{
			global $wpdb;

            $ret = array(
                'status'        => 'invalid',
                'msg'          => '',
            );
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
			);
			
			$status = 'invalid'; $status_msg = '';
			if( $request['itemid'] > 0 ) {
				$table = $wpdb->prefix  . $this->opt["custom_table"];

				$row = $wpdb->get_row( "SELECT * FROM " . $table . " WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				
				$row_id = (int)$row['id'];

				if ($row_id>0) {
				
					// publish/unpublish
					if ( 1 ) {
						$wpdb->update( 
							$table, 
							array( 
								'publish'		=> 'Y' == $row['publish'] ? 'N' : 'Y'
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
					}

					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('paged' => true, 'posts_per_page' => true);
					
					$status = 'valid';
					$status_msg = 'row published successfully.';
				}
				else {
					$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
				}
			}
			else {
				$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
			}
			
			$ret = array_merge($ret, array(
				'status' 	=> $status,
				'msg'		=> $status_msg
			));
			return $ret;
		}
		
		public function __action_delete()
		{
			global $wpdb;
			
            $ret = array(
                'status'        => 'invalid',
                'msg'          => '',
            );
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			$status = 'invalid'; $status_msg = '';
			if( $request['itemid'] > 0 ) {
				$table = $wpdb->prefix  . $this->opt["custom_table"];

				$wpdb->delete( 
					$table, 
					array( 'id' => $request['itemid'] )
				);
				
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page' => true);
				
				$status = 'valid';
				$status_msg = 'row deleted successfully.';
			}
			else {
				$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
			}

			$ret = array_merge($ret, array(
				'status' 	=> $status,
				'msg'		=> $status_msg
			));
			return $ret;
		}
		
		public function __action_bulk_delete() {
			global $wpdb;
			
            $ret = array(
                'status'        => 'invalid',
                'msg'          => '',
            );
			
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
			
			$status = 'invalid'; $status_msg = '';
			if (!empty($request['id'])) {

				$table = $wpdb->prefix  . $this->opt["custom_table"];

				// delete record
				$query = "DELETE FROM " . $table . " where 1=1 and id in (" . ($request['id']) . ");";
				/*
				$query = "UPDATE " . ($table) . " set
						deleted = '1'
						where id in (" . ($request['id']) . ");";
				*/
				$__stat = $wpdb->query($query);
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('posts_per_page' => true);
					
					$status = 'valid';
					$status_msg = 'bulk rows deleted successfully.';
				}
				else {
					$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
				}
			}
			else {
				$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
			}
			
			$ret = array_merge($ret, array(
				'status' 	=> $status,
				'msg'		=> $status_msg
			));
			return $ret;
		}

		public function __action_edit_inline()
		{
			global $wpdb;

            $ret = array(
                'status'        => 'invalid',
                'msg'          => '',
            );
			
			$request = array(
				'table'			=> isset($_REQUEST['table']) ? trim((string)$_REQUEST['table']) : '',
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
				'field_name'	=> isset($_REQUEST['field_name']) ? trim((string)$_REQUEST['field_name']) : '',
				'field_value'	=> isset($_REQUEST['field_value']) ? trim((string)$_REQUEST['field_value']) : '',
			);
			extract($request);
			
			$status = 'invalid'; $status_msg = '';
			if( $request['itemid'] > 0 ) {
				$table = $wpdb->prefix  . $table;

				if ( 1 ) {
				
					// update field
					if ( 1 ) {
						$wpdb->update(
							$table, 
							array( 
								$field_name		=> $field_value
							), 
							array( 'id' => $itemid ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
					}

					//keep page number & items number per page
					//$_SESSION['pspListTable']['keepvar'] = array('paged' => true, 'posts_per_page' => true);
					
					$status = 'valid';
					$status_msg = 'row field updated successfully.';
				}
				else {
					$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
				}
			}
			else {
				$status_msg = 'error: ' . __FILE__ . ":" . __LINE__;
			}
			
			$ret = array_merge($ret, array(
				'status' 	=> $status,
				'msg'		=> $status_msg
			));
			return $ret;
		}

		public function list_table_rows( $retType='die', $pms=array() ) {
			$request = array(
				'action'             => isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
				'ajax_id'            => isset($_REQUEST['ajax_id']) ? $_REQUEST['ajax_id'] : '',
			);
			extract($request);
			//var_dump('<pre>', $request, '</pre>'); die('debug...');

			$ret = array(
				'status'        => 'invalid',
				'html'          => '',
			);

			// create box return html
			ob_start();

			$_SESSION['pspListTable'][$request['ajax_id']]['requestFrom'] = 'ajax';

			$this->setup( $_SESSION['pspListTable'][$request['ajax_id']] );
			$this->print_html();
			$html = ob_get_contents();
			ob_clean();

			$ret['html'] = $html;
			//$ret = array_map('utf8_encode', $ret);

			if ( $retType == 'return' ) { return $ret; }
			else { die( json_encode( $ret ) ); }
		}
	}
}