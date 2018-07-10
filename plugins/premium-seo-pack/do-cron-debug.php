<?php
if ( !defined('ABSPATH') ) {
	/**
	 * mod		: serp | facebook | cronjob
	 * act	: (mod: actions associated)
	 * 		cronjob 		: get_cron
	 */
	$req = array(
		'mod'		=> isset($_REQUEST['mod']) ? (string) $_REQUEST['mod'] : '',
		'act'		=> isset($_REQUEST['act']) ? (string) $_REQUEST['act'] : '',
	);
	extract($req);

	//echo __FILE__ . ":" . __LINE__;die . PHP_EOL;

    $absolute_path = __FILE__;
    $path_to_file = explode( 'wp-content', $absolute_path );
    $path_to_wp = $path_to_file[0];

    /** Set up WordPress environment */
    require_once( $path_to_wp.'/wp-load.php' );
    global $psp;

    @ini_set('max_execution_time', 0);
    @set_time_limit(0); // infinte
    //pspSERP_cronReporter_event();
 
    // CRONJOBS...
    if ( 'cronjob' == $mod ) {
	    require_once( $path_to_wp.'/wp-content/plugins/premium-seo-pack/modules/cronjobs/cronjobs.core.php' );
    	$cronjobs = new pspCronjobs($psp);

		if ( 'get_cron' == $act || empty($act) ) {
		    var_dump('<pre>','first time','</pre>'); 
		    $get_config = $cronjobs->get_config();
		    foreach ($get_config as $cron_id => $cron) {
		        if ( !in_array($cron_id, array('unblock_crons')) ) continue 1;
		        //if ( !in_array($cron_id, array('sync_products')) ) continue 1;
		        //if ( !in_array($cron_id, array('sync_products_cycle')) ) continue 1;
		        //if ( !in_array($cron_id, array('assets_download')) ) continue 1;
		
		        //$cronjobs->set_cron($cron_id, array('status' => 'new'));
		        
		        $cronjobs->run($cron_id);
		        $status = $cronjobs->get_cron($cron_id);
		        $status = $status['status'];
		        var_dump('<pre>', $cron_id, $status, '</pre>');
		    }
		
		    var_dump('<pre>','second time','</pre>');  
		    $get_config = $cronjobs->get_config();
		    foreach ($get_config as $cron_id => $cron) {
		        $status = $cronjobs->get_cron($cron_id);
		        $status = $status['status'];
		        var_dump('<pre>', $cron_id, $status, '</pre>');
		    }
		
		    echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
	    }
    }


    // SERP...
    if ( 'serp' == $mod ) {
	    require_once( $path_to_wp.'/wp-content/plugins/premium-seo-pack/modules/serp/init.php' );
	    $serp = new pspSERP(true);
	
	    $cronjob = $serp->serp_cronjob_check_reporter(array());
	    var_dump('<pre>', $cronjob, '</pre>'); die('debug...');
	}

    
    // FACEBOOK...
    if ( 'facebook' == $mod ) {
	    require_once( $path_to_wp.'/wp-content/plugins/premium-seo-pack/modules/facebook_planner/init.php' );
	    $facebook = new pspFacebook_Planner();
	
	    $cronjob = $facebook->facebook_cronjob(array());
	    var_dump('<pre>', $cronjob, '</pre>'); die('debug...');
	}
}
die;   
