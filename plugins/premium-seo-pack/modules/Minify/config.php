<?php
/**
 * Minify Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'Minify' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 23,
				'title' => __('Minify', 'psp')
				,'icon' => '<i class="' . ( $psp->alias ) . '-icon-minify"></i>'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#Minify")
			),
			'description' => __("Minify module", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/minify/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Minify',
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