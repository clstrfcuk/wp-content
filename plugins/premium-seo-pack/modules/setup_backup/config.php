<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'setup_backup' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 31,
				'title' => __('Setup / Backup', 'psp')
				,'icon' => '<i class="' . ( $psp->alias ) . '-icon-setup_backup"></i>'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/menu_icon.png',
				'url'	=> admin_url("admin.php?page=psp#setup_backup")
			),
			'description' => __("Using this module you can install a default configuration for the plugin, as well as backing up the settings!", 'psp'),
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/setup-backup-2/'
			),
			'load_in' => array(
				'backend' => array(
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy'
			),
			'css' => array(
				'admin'
			)
		)
	)
 );