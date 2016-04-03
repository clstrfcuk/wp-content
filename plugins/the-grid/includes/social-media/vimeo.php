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

class The_Grid_Vimeo {

	private $api_key;
	private $transient_sec;
	private $error;

	private $sort;
	private $order;
	private $source_type;
	private $source_id;
	
	private $count;
	private $media = array();
	private $last_media = array();
	private $offset = null;
	
	private $loaded;
	private $to_load;

	/**
	* Initialize the class and set its properties.
	* @since 1.0.0
	*/
	public function __construct() {
		
		$this->api_key = get_option('the_grid_vimeo_api_key', '');
		$this->transient_sec = apply_filters('tg_transient_vimeo', 3600);
		
	}
	
	/**
	* Get instagram data
	* @since 1.0.0
	*/
	public function get_data($sort, $order, $source, $user, $album, $group, $channel, $count){
		
		// store Vimeo data
		$this->sort  = $sort;
		$this->order = $order;
		$this->source_type = $source;
		
		// get right source content from Vimeo
		switch ($this->source_type) {
			case 'users':
				$this->source_id = $user;
				break;
			case 'albums':
				$this->source_id = $album;		
				break;
			case 'groups':
				$this->source_id = $group;
				break;
			case 'channels':
				$this->source_id = $channel;		
				break;
		}
		
		// set the number of video to retrieve
		$this->count = ($count <= 0) ? 10 : $count;
		$this->count = ($this->count > 50) ? 50 : $this->count;

		// get last media from ajax
		$this->last_media['page']   = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? (int) $_POST['grid_social']['page']   : 1;
		$this->last_media['count']  = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? (int) $_POST['grid_social']['count']  : 0;
		$this->last_media['onload'] = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? (int) $_POST['grid_social']['onload'] : $this->count;
		$this->last_media['total']  = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? (int) $_POST['grid_social']['total']  : 9999;

		// retrieve Instagram data
		$media = $this->get_media();
		
		// build response array
		return array(
			'content' => $this->media,
			'ajax_data' => $this->last_media,
			'error' => $this->error
		);
		
	}
	
	/**
	* Get instagram user data
	* @since 1.0.0
	*/
	public function get_user($user){
		
		$url = 'https://api.vimeo.com/users/'.$user.'/?access_token='.$this->api_key;
		$response = $this->get_response($url);

		if (isset($response) && !empty($response)){
			return $response;
		} else {
			return '';
		}
		
	}
	
	/**
	* Retrieve media data
	* @since 1.0.0
	*/
	public function get_media() {
		
		// retrieve current Vimeo page data
		$this->get_page();
		// set page offset if ajax nb !=  onload nb
		$this->offset = $this->to_load - $this->loaded + $this->last_media['onload'] - $this->count;
		
		// if the number of result is not enough then loop until enough
		// auto offset because Vimeo doesn't have offset for video endpoint
		while ($this->to_load > $this->loaded && count($this->media) <= $this->last_media['total']) {
			$this->get_page();
		}
		
		// get only necessary element from vimeo data array
		$this->media = array_slice($this->media, $this->offset, $this->count);
		// store last number of video we append
		$this->last_media['count'] = $this->last_media['count'] + count($this->media);
		// get error message if error occurs
		$this->error = (isset($call->error)) ? $call->error : '';
				
	}
	
	/**
	* Retrieve Vimeo page data
	* @since 1.0.0
	*/
	public function get_page() {
		
		// make Vimeo API call
		$call  = $this->_makeCall($this->source_type, $this->source_id, $this->last_media['page']);
		// transform Vimeo data to our data array
		$media = $this->build_media_array($call, '', '');
		// merge current result to previous Vimeo page(s) result
		$this->media = array_merge($this->media, $media);
		
		// check if we need to retrieve next page
		$this->loaded  = $this->last_media['page'] * $this->last_media['onload'];
		$this->to_load = $this->last_media['count'] + $this->count;
		$this->last_media['page']  = ($this->to_load >= $this->loaded && isset($call->page)) ? $call->page+1 : $this->last_media['page'];
		$this->last_media['total'] = (isset($call->total)) ? $call->total : -1;

	}
	
	/**
	* Youtube API call
	* @since 1.0.0
	*/
	public function _makeCall($type, $id, $page = null) {

		// set and retrieve response
		$page  = (!empty($page)) ? '&page='.$page : '';
		$sort  = (!empty($this->sort)) ? '&sort='.$this->sort : '';
		$order = (!empty($this->order)) ? '&direction='.$this->order : '';
		$url  = 'https://api.vimeo.com/'.$type.'/'.$id.'/videos?access_token='.$this->api_key.'&per_page='.$this->last_media['onload'].$page.$sort.$order;
		$response = $this->get_response($url);

		if (isset($response) && !empty($response)){
			return $response;
		}
		

	}
	
	/**
	* Get url response (transient)
	* @since 1.0.0
	*/
	public function get_response($url) {
		
		$transient_name = 'tg_grid_' . md5($url);
		
		if ($this->transient_sec > 0 && ($transient = get_transient($transient_name)) !== false) {
			$response = $transient;
		} else {
			$response = json_decode(wp_remote_fopen($url));
			if (isset($response) && !empty($response)){
				set_transient($transient_name, $response, $this->transient_sec);
			}
		}
		
		return $response;
		
	}
	
	/**
	* Convert Youtube duration format
	* @since 1.0.0
	*/
	public function covtime($duration){
		
		if ($duration/3600 >= 1) {
    		return gmdate('H:i:s', $duration);
		} else {
			return gmdate('i:s', $duration);
		}
		
	}  
	
	/**
	* Build data array for the grid
	* @since 1.0.0
	*/
	public function build_media_array($response, $type, $type_id) {
		
		$videos = array();

		if (isset($response->data)) {

			foreach ($response->data as $data) {
				
				for ($i = 3; $i >= 0; $i--) {
					if (!empty($data->pictures->sizes[$i]->link)) {
						$index = $i;
						break;
					}
				}

				$videos[] = array(
					'id'         => str_replace('/videos/', '', $data->uri),
					'type'       => $type,
					'type_id'    => $type_id,
					'format'     => 'video',
					'filter'     => '',
					'tags'       => '',
					'user_id'    => '',
					'username'   => (isset($data->user->name)) ? $data->user->name : null,
					'fullname'   => (isset($data->user->name)) ? $data->user->name : null,
					'user_link'  => (isset($data->user->link)) ? $data->user->link : null,
					'avatar'     => (isset($data->user->pictures->sizes[1]->link)) ?  $data->user->pictures->sizes[1]->link : null,
					'date'       => (isset($data->created_time)) ?  $data->created_time : null,
					'image'      => array(
						'alt'    => null,
						'url'    => (isset($data->pictures->sizes[$index]->link)) ? $data->pictures->sizes[$index]->link : null,
						'width'  => (isset($data->pictures->sizes[$index]->width)) ? $data->pictures->sizes[$index]->width : null,
						'height' => (isset($data->pictures->sizes[$index]->height)) ? $data->pictures->sizes[$index]->height : null
					),
					'video'        => array(
						'type'     => 'vimeo',
						'duration' => (isset($data->duration)) ? $this->covtime($data->duration) : null,
						'poster' => (isset($data->pictures->sizes[3]->link)) ? $data->pictures->sizes[3]->link : null,
						'source' => array(
							'mp4'  => null,
							'ovg'  => null,
							'webm' => null
						),
					),
					'audio'      => array(
						'type'   => '',
						'poster' => '',
						'source' => array(),
					),
					'link'       => (isset($data->link)) ? $data->link : null,
					'title'      => (isset($data->name)) ? $data->name : null,
					'excerpt'    => (isset($data->description)) ? $data->description : null,
					'likes'      => (isset($data->metadata->connections->likes->total)) ? $data->metadata->connections->likes->total : null,
					'like_title' =>  __( 'Like on Vimeo', 'tg-text-domain' ),
					'comments'   => (isset($data->metadata->connections->comments->total)) ? $data->metadata->connections->comments->total : null,
					'views'      => (isset($data->stats->plays)) ? $data->stats->plays : null,
					'location'   => ''
				);
	
			}

		}

		return $videos;
		
	}
	
}