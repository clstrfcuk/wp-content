<?php
/**
 * Social_Stats Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'Link_Builder' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 23,
				'title' => __('Link Builder', 'psp')
				,'icon' => '<span class="' . ( $psp->alias ) . '-icon-Link_Builder"><span class="path1"></span><span class="path2"></span></span>'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_Link_Builder")
			),
			'description' => __("You can create a list of keywords and URLs, and they will be automatically created", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/link-builder/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Link_Builder',
					'admin-ajax.php'
				),
				'frontend' => true
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