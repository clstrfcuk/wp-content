<?php



/*
Do not do before-create table checks and log 
*/
function wppp_plugin_create_log_table() {

	$installed_ver = get_option( "wppp_db_version" );

	if ( $installed_ver != WPPP_VERSION ) {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . WPPP_LOG_TABLE_NAME;

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			attachment_id mediumint(9),
			file_name VARCHAR(255),
			size_name VARCHAR(255),
			step VARCHAR(255),
			level VARCHAR(50),
			message text NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );	

		$table_name_sql = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if( $table_name_sql != $table_name ) {
			error_log( 'WPPP - wp_pixpie_plugin_create_log_table - cannot create log table' );
//			wppp_send_error_by_email( 'Activation error - cannot create log table ', '' );
		} 	

	} else {
		wppp_log_trace(
			"Plugin Activation - Installing same version, skip db changes",
			0,'','','wp_pixpie_plugin_create_log_table'
			);			

	}
	
}	


function wppp_plugin_create_images_table() {

	$installed_ver = get_option( "wppp_db_version" );

	wppp_log_trace(
		'Plugin Activation - installed version = ' . $installed_ver . 
		', new version = ' . WPPP_VERSION,
		0,'','','wp_pixpie_plugin_create_images_table'
		);

	if ( $installed_ver != WPPP_VERSION ) {

		wppp_log_trace(
			'Plugin Activation - updating database (wp_pixpie_plugin_create_images_table)',
			0,'','','wp_pixpie_plugin_create_images_table'
			);

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . WPPP_IMAGES_TABLE_NAME;

		// check is table exists
		$table_name_sql = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if( $table_name_sql != $table_name ) {
			wppp_log_trace(
				"Plugin Activation - before: table $table_name does not exist",
				0,'','','wp_pixpie_plugin_create_images_table'
				);			
		} else {
			wppp_log_trace(
				"Plugin Activation - before: table $table_name already exists",
				0,'','','wp_pixpie_plugin_create_images_table'
				);			
		}
		$num_rows = $wpdb->get_var("select count(*) from $table_name");
		wppp_log_trace(
			"Plugin Activation - before: table $table_name has $num_rows rows",
			0,'','','wp_pixpie_plugin_create_images_table'
			);			

		/* 
		Do changes
		*/
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			attachment_id int(11) NOT NULL,
			file_name VARCHAR(255),
			size_before int(15),
			size_after int(15),
			PRIMARY KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		wppp_log_trace(
			"Plugin Activation - dbDelta executed (wp_pixpie_plugin_create_images_table)",
			0,'','','wp_pixpie_plugin_create_images_table'
			);			

        // check if table was created
		$table_name_sql = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if( $table_name_sql != $table_name ) {
			wppp_log_trace(
				"Plugin Activation - after: table $table_name does not exist",
				0,'','','wp_pixpie_plugin_create_images_table'
				);			
		} else {
			wppp_log_trace(
				"Plugin Activation - after: table $table_name exists",
				0,'','','wp_pixpie_plugin_create_images_table'
				);			
		}
		$num_rows = $wpdb->get_var("select count(*) from $table_name");
		wppp_log_trace(
			"Plugin Activation - after: table $table_name has $num_rows rows",
			0,'','','wp_pixpie_plugin_create_images_table'
			);		

	} else {
		wppp_log_trace(
			'Plugin Activation - Installing same version, skip db changes',
			0,'','','wp_pixpie_plugin_create_images_table'
			);
	}
}	


function wppp_plugin_create_convert_all_table() {

	$installed_ver = get_option( "wppp_db_version" );

	wppp_log_trace(
		'Plugin Activation - installed version = ' . $installed_ver . 
		', new version = ' . WPPP_VERSION,
		0,'','','wp_pixpie_plugin_create_convert_all_table'
		);

	if ( $installed_ver != WPPP_VERSION ) {

		wppp_log_trace(
			'Plugin Activation - updating database (wp_pixpie_plugin_create_convert_all_table)',
			0,'','','wp_pixpie_plugin_create_convert_all_table'
			);

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . WPPP_CONVERT_ALL_TABLE_NAME;

		// check if table existed
		$table_name_sql = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'");
		if( $table_name_sql != $table_name ) {
			wppp_log_trace(
				"Plugin Activation - before: table $table_name does not exist",
				0,'','','wp_pixpie_plugin_create_convert_all_table'
				);			
		} else {
			wppp_log_trace(
				"Plugin Activation - before: table $table_name already exists",
				0,'','','wp_pixpie_plugin_create_convert_all_table'
				);			
		}
		$num_rows = $wpdb->get_var( "select count(*) from $table_name" );
		wppp_log_trace(
			"Plugin Activation - before: table $table_name has $num_rows rows",
			0,'','','wp_pixpie_plugin_create_convert_all_table'
			);			

		/* 
		Do changes
		*/		
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			attachment_id int(11) NOT NULL,
			PRIMARY KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// check if table was created
		$table_name_sql = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if( $table_name_sql != $table_name ) {
			wppp_log_trace(
				"Plugin Activation - after: table $table_name does not exist",
				0,'','','wp_pixpie_plugin_create_convert_all_table'
				);			
		} else {
			wppp_log_trace(
				"Plugin Activation - after: table $table_name exists",
				0,'','','wp_pixpie_plugin_create_convert_all_table'
				);			
		}
		$num_rows = $wpdb->get_var( "select count(*) from $table_name" );
		wppp_log_trace(
			"Plugin Activation - after: table $table_name has $num_rows rows",
			0,'','','wp_pixpie_plugin_create_convert_all_table'
			);			

	} else {
		wppp_log_trace(
			'Plugin Activation - Installing same version, skip db changes',
			0,'','','wp_pixpie_plugin_create_convert_all_table'
			);
	}
}	

function wppp_update_database (){

	// log table first
	wppp_plugin_create_log_table();

	// all others use logging while creating, etc
	wppp_plugin_create_images_table();
	wppp_plugin_create_convert_all_table();
}


/*
Since plugin activation does not run when plugin is updated
*/
function wppp_update_db_check() {

	wppp_log_trace(
		'Plugin Activation - wp_pixpie_update_db_check started',
		0,'','','wppp_update_db_check'
		);			

	$installed_ver = get_option("wppp_db_version");
	if ( $installed_ver != WPPP_VERSION ){
		wppp_log_trace(
			'Plugin Activation - Installing new version, will do database changes',
			0,'','','wppp_update_db_check'
			);

		// do updates
		wppp_update_database();
	}
}