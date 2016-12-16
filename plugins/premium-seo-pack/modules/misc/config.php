<?php
/**
 * Social_Stats Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
 echo json_encode(
	array(
		'misc' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 13,
				'title' => __('SEO Slug Optimizer', 'psp')
				,'icon' => '<i class="' . ( $psp->alias ) . '-icon-miscellaneous"></i>'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#misc")
			),
			'description' => __('Usefull SEO Settings', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				// subsections on the same module!
				/*'url' => array(
					'slug-optimizer' 	=> 'http://docs.aa-team.com/premium-seo-pack/documentation/seo-slug-optimizer/',
					'insert-code'		=> 'http://docs.aa-team.com/premium-seo-pack/documentation/seo-insert-code/' 
				)*/
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/seo-slug-optimizer/'
			),
	        'load_in' => array(
				'backend' => array(
					'admin-ajax.php',
					'edit.php',
					'post.php',
					'post-new.php',
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