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
		'cronjobs' => array(
			'version' => '1.0',
			'menu' => array(
			    'show_in_menu' => false,
				'order' => 3,
				'title' => __('Cronjobs', 'psp'),
				'icon' => '<i class="' . ( $psp->alias ) . '-checks-cron"></i>'
			),
            'in_dashboard' => array(
                'icon'  => 'images/32.png',
                'url'   => admin_url("admin.php?page=psp#cronjobs")
            ),
            'help' => array(
                'type' => 'remote',
                'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/cronjobs/'
            ),
			'description' => "Using this module you can view this plugin associated cronjobs.",
			'module_init' => 'cronjobs.core.php',
			'load_in' => array(
				'backend' => array('@all'),
				'frontend' => true
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy'
			),
			'css' => array(
				'admin',
				'tipsy'
			)
		)
	)
 );