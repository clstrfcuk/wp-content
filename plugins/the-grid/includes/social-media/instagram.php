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

class The_Grid_Instagram {

	private $api_key;
	private $transient_sec;
	private $error;

	private $call_nb;
	private $usernames = array();
	private $hashtags  = array();
	
	private $count;
	private $media = array();
	private $last_media = array();
	
	private $tmp_count;
	private $tmp_media = array();
	private $tmp_last_media = array();
	

	/**
	* Initialize the class and set its properties.
	* @since 1.0.0
	*/
	public function __construct() {
		
		$this->api_key = get_option('the_grid_instagram_api_key', '');
		$this->transient_sec = apply_filters('tg_transient_instagram', 3600);
		
	}
	
	/**
	* Get instagram data
	* @since 1.0.0
	*/
	public function get_data($type, $usernames, $hashtags, $count) {
		
		// store Instagram data
		$this->usernames = preg_replace('/\s+/', '', $usernames);
		$this->hashtags  = preg_replace('/\s+/', '', $hashtags);
		$this->count     = ($count <= 0) ? 10 : $count;
		$this->count     = ($this->count > 50) ? 50 : $this->count;
		
		// get last media from ajax
		$last_media = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? $_POST['grid_social'] : array();
		$this->last_media = $last_media;
		$this->tmp_last_media = $last_media;
		
		// prepare Instagram data
		$this->get_users_id();
			
		$this->get_hashtags();
		$this->call_nb = count($this->usernames) + count($this->hashtags);
		
		// retrieve Instagram data
		if ($type == 'media') {
			$this->get_media();
		} else if ($type == 'user_info') {
			$this->get_user_info();
		}

		return array(
			'content' => $this->media,
			'ajax_data' => $this->last_media,
			'error' => $this->error
		);
		
	}
	
	/**
	* Get user ID if necessary
	* @since 1.0.0
	*/
	public function get_users_id() {
		
		$count = 0;
		$this->usernames = array_filter(explode(',', $this->usernames));
		foreach ($this->usernames as $username) {
			$username = $username;
			if (!is_numeric($username)) {
				$url = 'https://api.instagram.com/v1/users/search?q='.$username.'&access_token='.$this->api_key;
				$response = $this->get_response($url);
				if(isset($response->data) && !empty($response->data)) {
					$user_data = $response->data;
					foreach($user_data as $user) {
						if($user->username == $username) {
							$this->usernames[$count] = $user->id;
						}
					}
				}
			}
			$count++;
		}

	}
	
	/**
	* Get hashtags
	* @since 1.0.0
	*/
	public function get_hashtags() {
		
		$this->hashtags = array_filter(explode(',', $this->hashtags));
		$this->hashtags = array_map('trim',$this->hashtags);
		
	}	
	
	/**
	* Retrieve media data
	* @since 1.0.0
	*/
	public function get_media() {

		// retrieve Instagram data
		$this->get_hashtag_media();
		$this->get_user_media();
		
		// sort all data by date
		usort($this->media, function($a, $b) {
    		return str_replace('@', '',$b['date']) - str_replace('@', '',$a['date']);
		});
		// return only the number of element set in grid settings
		$this->media = array_slice($this->media, 0, $this->count);
		
		// get the last media id (max_id)
		$this->get_last_media();
	
	}
	
	/**
	* Get user info
	* @since 1.0.0
	*/
	public function get_user_info() {

		if (!empty($this->usernames)) {
			foreach($this->usernames as $username) {
				$this->_makeCall('users', $username, '');
			}
		} else {
			
			$this->_makeCall('users', 'self', '');
		}
		
	}
	
	/**
	* Retrieve user media
	* @since 1.0.0
	*/
	public function get_user_media() {
		
		if (!empty($this->usernames)) {
			foreach($this->usernames as $username) {
				$this->_makeCall('users', $username, '/media/recent/');
			}
		} else if ($this->call_nb == 0) {
			$this->_makeCall('users', 'self', '/media/recent/');
		}
		
	}
	
	/**
	* Retrieve hashtag media
	* @since 1.0.0
	*/
	public function get_hashtag_media() {
		
		if (!empty($this->hashtags)) {
			foreach($this->hashtags as $hashtag) {
				$this->_makeCall('tags', $hashtag, '/media/recent/');
			}
		}
	
	}
	
	/**
	* Instagram API call
	* @since 1.0.0
	*/
	public function _makeCall($type, $id, $content) {

		// set number of item to retrieve and max id if necessary
		$count  = (!empty($this->tmp_count)) ? $this->tmp_count : $this->count;
		$max_pr = ($type == 'tags') ? 'tag_' : '';
		$max_id = (isset($this->tmp_last_media[$id]) && !empty($this->tmp_last_media[$id])) ? '&max_'.$max_pr.'id='.$this->tmp_last_media[$id] : '';

		// set and retrieve response
		$url = 'https://api.instagram.com/v1/'.$type.'/'.$id.$content.'?&access_token='.$this->api_key.$max_id;
		$response = $this->get_response($url);
		$this->error = (isset($response->meta->error_message)) ? $response->meta->error_message : '';

		if (isset($response->data) && !empty($response->data)){
			
			if (!empty($content)){
			
				// build array data for the grid social content
				$data = $this->build_media_array($response, $type, $id);
				// set temporary data for current user/tag
				$this->tmp_media[$id] = (!isset($this->tmp_media[$id])) ? array() : $this->tmp_media[$id];
				$this->tmp_media[$id] = array_merge($this->tmp_media[$id], $data);
				
				// get max id from pagination
				if (isset($response->pagination->next_max_id) || isset($response->pagination->next_max_tag_id)) {
					$this->tmp_last_media[$id] = ($type == 'users') ? $response->pagination->next_max_id : $response->pagination->next_max_tag_id;
				}
				
				$max_nb = ($this->count > 33) ? 33 : $this->count;
				if (count($this->tmp_media[$id]) < $this->count && (count($data) == $max_nb)) {
					// set temporary count to get next set of data (exact number)
					$this->tmp_count = $this->count - count($this->tmp_media[$id]);
					$this->_makeCall($type, $id, $content);
				}
				
				$this->media = array_merge($this->media, $this->tmp_media[$id]);
			
			} else {
				
				$this->media = 	$response->data;
				
			}
		
		}
		
		// reset temporary data for current user/tag
		$this->tmp_count = null;

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
			if(isset($response->data) && !empty($response->data)){
				set_transient($transient_name, $response, $this->transient_sec);
			}
		}
		
		return $response;
		
	}

	/**
	* Store last media media
	* @since 1.0.0
	*/
	public function get_last_media() {
		
		// assign max id
		foreach ($this->media as $media => $data) {
			$id      = $data['id'];
			$type    = $data['type'];
			$type_id = $data['type_id'];
			$count[$type_id] = (!isset($count[$type_id])) ? 1 : $count[$type_id]+1;
			$this->last_media[$type_id] = $id;
		}
		
		// get the right last max id for hashtags for mix content
		if (!empty($this->hashtags)) {
			foreach($this->hashtags as $hashtag) {
				if ($count[$hashtag] < count($this->tmp_media[$hashtag])) {
					// remove image user id because tags doesn't handle user id
					$this->last_media[$hashtag] = strstr($this->tmp_media[$hashtag][$count[$hashtag]]['id'], '_', true);
				} else {
					$this->last_media[$hashtag] = $this->tmp_last_media[$hashtag];
				}
			}
		}
		
	}
	
	/**
	* Build data array for the grid
	* @since 1.0.0
	*/
	public function build_media_array($response, $type, $type_id) {
		
		$images = array();
		
		if (isset($response->data)) {

			foreach ($response->data as $data) {

				$images[] = array(
					'id'         => $data->id,
					'type'       => $type,
					'type_id'    => $type_id,
					'format'     => $data->type,
					'filter'     => $data->filter,
					'tags'       => $data->tags,
					'user_id'    => $data->user->id,
					'username'   => $data->user->username,
					'fullname'   => $data->user->full_name,
					'user_link'  => 'https://www.instagram.com/'.$data->user->username.'/',
					'avatar'     => $data->user->profile_picture,
					'date'       => '@'.$data->created_time, // Unix TimeStamp
					'image'      => array(
						'alt'    => null,
						'url'    => $data->images->standard_resolution->url,
						'width'  => $data->images->standard_resolution->width,
						'height' => $data->images->standard_resolution->height
					),
					'video'      => array(
						'type'   => 'video',
						'poster' => $data->images->standard_resolution->url,
						'source' => array(
							'mp4'  => ($data->type == 'video') ? $data->videos->standard_resolution->url : null,
							'ovg'  => null,
							'webm' => null
						),
					),
					'audio'      => array(
						'type'   => '',
						'poster' => '',
						'source' => array(),
					),
					'link'       => $data->link,
					'title'      => null,
					'excerpt'    => isset($data->caption->text) ? $data->caption->text : null,
					'likes'      => $data->likes->count,
					'like_title' =>  __( 'Like on Instagram', 'tg-text-domain' ),
					'comments'   => $data->comments->count,
					'location'   => (isset($data->location->name)) ? $data->location->name : null
				);

			}
		
		}
		
		return $images;
		
	}
	
}