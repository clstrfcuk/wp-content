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

class The_Grid_Skin_Post {

	private $old_post;
	private $old_posts;
	
	/**
	* __construct
	* @since: 1.0.0
	*/
	function __construct(){}
		
	/**
	* Create Virtual post on the fly
	* @since: 1.0.0
	*/
	public function virtual_post() {
		
		global $post, $posts;
		
		$this->old_post  = $post;
		$this->old_posts = $posts;
		
		$content = 'Actique exilium principis is in nullos Constantio et absolutum quorum movebantur ita intendebantur ubi gladii sub coopertos facile uncosque in poenales coopertos ubi eculei quemquam sceleste ex alii Constantio parabat principis Paulus exilium deiectos movebantur intend.';
	
		$post = array(
			'ID'             => 0,
			'post_title'     => 'The post title',
			'post_name'      => sanitize_title('The post title'),
			'post_content'   => $content,
			'post_excerpt'   => $content,
			'post_parent'    => 0,
			'menu_order'     => 0,
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'comment_count'  => 2,
			'post_password'  => '',
			'to_ping'        => '',
			'pinged'         => '',
			'guid'           => get_bloginfo('wpurl' . '/' . 'the_grid_skin_post'),
			'post_date'      => current_time( 'mysql' ),
			'post_date_gmt'  => current_time( 'mysql', 1 ),
			'post_author'    => is_user_logged_in() ? get_current_user_id() : 0,
			'is_virtual'     => TRUE,
			'filter'         => 'raw'
		);
		$post = new \WP_Post( (object) $post );
		
		$posts   = NULL;
		$posts[] = $post;
		
		return $posts;
	
	}

	/**
	* Reset Virtual post
	* @since: 1.0.0
	*/
	public function reset_virtual_post() {
		
		global $post, $post;
		$post  = $this->old_post;
		$posts = $this->old_posts;
		
	}
}
