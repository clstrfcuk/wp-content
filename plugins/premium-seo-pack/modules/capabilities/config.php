<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'capabilities' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 30,
				'title' => __('Capabilities', 'psp')
				,'icon' => '<i class="' . ( $psp->alias ) . '-icon-capabilities"></i>'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_capabilities")
			),
			'description' => __("Using this module you can choose to assign different kinds of permissions to different kinds of user groups.", $psp->localizationName),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/capabilities/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_capabilities',
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