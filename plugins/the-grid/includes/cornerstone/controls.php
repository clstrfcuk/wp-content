<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

require_once(TG_PLUGIN_PATH . '/includes/wpml.class.php');
			
$WPML = new The_Grid_WPML();
$WPML_meta_query = $WPML->WPML_meta_query();
$post_args = array(
	'post_type'      => 'the_grid',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'meta_query' => array(
		'relation' => 'AND',
		$WPML_meta_query
	),
	'suppress_filters' => true 
);
			
$grids   = get_posts($post_args);
$grid_nb = count($grids);
$count = 0;
$first_grid = null;
$choices = array();
			
if(!empty($grids)){
	foreach($grids as $grid){
		$grid_post   = (array) get_post_meta($grid->ID, 'the_grid_post_type', true);
		$grid_post   = implode('/', $grid_post);
		$grid_post   = ($grid_post) ? $grid_post : 'post';
		$grid_style  = get_post_meta($grid->ID, 'the_grid_style', true);
		$grid_layout = get_post_meta($grid->ID, 'the_grid_layout', true);
		$grid_name   = get_post_meta($grid->ID, 'the_grid_name', true);
		$choice    = array( 'value' => $grid_name, 'label' => $grid_name.' ('.$grid_post.', '.$grid_style.', '.$grid_layout.')');
		$choices[] = $choice;
		if ($count == 0) {
			$first_grid = $grid_name;
		}
		$count++;
	}
			
}

return array(
	'common' => array( '!id', '!class', '!style' ),
	'name' => array(
		'type'    => 'select',
		'ui' => array(
			'title'   => __( 'Select a grid from the following list:', 'tg-text-domain' ),
		),
		'options' => array(
			'choices' => $choices
		)
	),
);