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

class The_Grid_Youtube {

	private $api_key;
	private $transient_sec;
	private $error;
	
	private $order;
	private $source_type;
	private $playlist_id;
	private $channel_id;
	private $video_ids = array();
	private $videos_details = array();
	
	private $count;
	private $media = array();
	private $last_media = array();
	

	/**
	* Initialize the class and set its properties.
	* @since 1.0.0
	*/
	public function __construct() {
		
		$this->api_key = get_option('the_grid_youtube_api_key', '');
		$this->transient_sec = apply_filters('tg_transient_youtube', 3600);
		
	}
	
	/**
	* Get instagram data
	* @since 1.0.0
	*/
	public function get_data($order, $source, $channel, $playlist, $videos, $count){
		
		// store Youtube data
		$this->order       = $order;
		$this->source_type = $source;
		$this->channel_id  = $channel;
		$this->playlist_id = $playlist;
		$this->video_ids   = preg_replace('/\s+/', '', $videos);
		$this->count = ($count <= 0) ? 10 : $count;
		$this->count = ($this->count > 50) ? 50 : $this->count;
		
		// get last media from ajax
		$this->last_media = (isset($_POST['grid_social']) && !empty($_POST['grid_social'])) ? $_POST['grid_social']['pageToken'] : array();
		
		// retrieve Instagram data
		if ($this->last_media != 'none') {
			$this->get_media();
		}

		return array(
			'content' => $this->media,
			'ajax_data' => $this->last_media,
			'error' => $this->error
		);
		
	}
	
	/**
	* Retrieve media data
	* @since 1.0.0
	*/
	public function get_media() {
		
		switch ($this->source_type) {
			case 'channel_info':
				$this->get_channel_info();		
				break;
			case 'channel':
				$this->get_channel();
				$this->get_video_ids();			
				break;
			case 'playlist':
				$this->get_playlist();
				$this->get_video_ids();
				break;
		}
		
		if (!empty($this->video_ids)) {
			$this->get_video();
			$this->media = $this->build_media_array($this->videos_details, '', '');
		}
		
	}
	
	/**
	* Retrieve media data
	* @since 1.0.0
	*/
	public function get_video_ids() {
		
		$this->video_ids = array();
		
		if (isset($this->media->items)) {
			// loop through each video details
			foreach($this->media->items as $item) {
				// get video id (depends if playlist or not)
				if (isset($item->id->videoId)) {
					array_push($this->video_ids, $item->id->videoId);
				} else if (isset($item->snippet->resourceId->videoId)) {
					array_push($this->video_ids, $item->snippet->resourceId->videoId);
				}
			}
			$this->last_media['videos'] = $this->video_ids;
			// prepare video id for videos youtube call
			$this->video_ids = implode(',', $this->video_ids);
		}

	}
	
	/**
	* Get Youtube Channel Items
	* @since    1.0.0
	*/
	public function get_channel_info() {
		
		$call = $this->_makeCall('channels', 'id', $this->channel_id, 'id,contentDetails,snippet,brandingSettings,statistics', '');
		$this->last_media = array();
		$this->last_media['pageToken'] = 'none';
		$this->error = (isset($call->error->errors[0]->reason)) ? $call->error->errors[0]->reason : '';
		
	}

	/**
	* Get Youtube Channel Items
	* @since    1.0.0
	*/
	public function get_channel() {
		
		$call = $this->_makeCall('search', 'channelId', $this->channel_id, 'snippet&type=video', $this->last_media);
		$this->last_media = array();
		$this->last_media['pageToken'] = (isset($call->nextPageToken)) ? $call->nextPageToken : 'none';
		$this->error = (isset($call->error->errors[0]->reason)) ? $call->error->errors[0]->reason : '';
		
	}
	
	/**
	* Get Youtube Playlist Items
	* @since    1.0.0
	*/
	public function get_playlist() {
		
		$call = $this->_makeCall('playlistItems', 'playlistId', $this->playlist_id, 'snippet,contentDetails', $this->last_media);
		$this->last_media = array();
		$this->last_media['pageToken'] = (isset($call->nextPageToken)) ? $call->nextPageToken : 'none';
		$this->error = (isset($call->error->errors[0]->reason)) ? $call->error->errors[0]->reason : '';
		
	}
	
	/**
	* Get Youtube videos details
	* @since 1.0.0
	*/
	public function get_video() {
		
		if (!empty($this->video_ids)) {
			$this->videos_details = $this->_makeCall('videos', 'id', $this->video_ids, 'snippet,contentDetails,statistics,status');
			$this->error = (isset($this->videos_details->error->errors[0]->reason)) ? $this->videos_details->error->errors[0]->reason : '';
		}
				
	}
	
	
	/**
	* Youtube API call
	* @since 1.0.0
	*/
	public function _makeCall($type, $id_type, $id, $part, $page = null) {

		// set and retrieve response
		$page  = (!empty($page)) ? '&pageToken='.$this->last_media : '';
		$order = ($type == 'search') ? '&order='.$this->order : '';
		$url = 'https://www.googleapis.com/youtube/v3/'.$type.'?'.$id_type.'='.$id.'&part='.$part.'&maxResults='.$this->count.'&key='.$this->api_key.$page.$order;

		$response = $this->get_response($url);

		if (isset($response) && !empty($response)){
			$this->media = $response;
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
		
    	$start = new DateTime('@0'); // Unix epoch
		$start->add(new DateInterval($duration));
		if (strlen($duration) > 8) {
    		return $start->format('H:i:s');
		} else {
			return $start->format('i:s');
		}
		
	}  
	
	/**
	* Build data array for the grid
	* @since 1.0.0
	*/
	public function build_media_array($response, $type, $type_id) {
		
		$videos = array();
		
		if (isset($response->items)) {

			foreach ($response->items as $data) {

				$videos[] = array(
					'id'         => $data->id,
					'type'       => $type,
					'type_id'    => $type_id,
					'format'     => 'video',
					'filter'     => '',
					'tags'       => '',
					'user_id'    => '',
					'username'   => (isset($data->snippet->channelTitle)) ? $data->snippet->channelTitle : null,
					'fullname'   => (isset($data->snippet->channelTitle)) ? $data->snippet->channelTitle : null,
					'user_link'  => '',
					'avatar'     => '',
					'date'       =>(isset($data->snippet->publishedAt)) ?  $data->snippet->publishedAt : null,
					'image'      => array(
						'alt'    => null,
						'url'    => (isset($data->snippet->thumbnails->high->url)) ? $data->snippet->thumbnails->high->url : null,
						'width'  => (isset($data->snippet->thumbnails->high->width)) ? $data->snippet->thumbnails->high->width : null,
						'height' => (isset($data->snippet->thumbnails->high->height)) ? $data->snippet->thumbnails->high->height : null
					),
					'video'        => array(
						'type'     => 'youtube',
						'duration' => (isset($data->contentDetails->duration)) ? $this->covtime($data->contentDetails->duration) : null,
						'poster' => (isset($data->snippet->thumbnails->high->url)) ? $data->snippet->thumbnails->high->url : null,
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
					'link'       => 'https://www.youtube.com/watch?v='.$data->id,
					'title'      => (isset($data->snippet->title)) ? $data->snippet->title : null,
					'excerpt'    => (isset($data->snippet->description)) ? $data->snippet->description : null,
					'likes'      => (isset($data->statistics->likeCount)) ? $data->statistics->likeCount : null,
					'like_title' =>  __( 'Like on Youtube', 'tg-text-domain' ),
					'comments'   => (isset($data->statistics->commentCount)) ? $data->statistics->commentCount : null,
					'views'      => (isset($data->statistics->viewCount)) ? $data->statistics->viewCount : null,
					'location'   => ''
				);
	
			}
			
			if ($this->source_type == 'videos') {
				$last_media = ($this->last_media) ? $this->last_media : 0;
				$videos = array_slice($videos, $last_media, $this->count);
				$this->last_media = array();
				$this->last_media['pageToken'] = $last_media + $this->count;
			}

		}
		
		return $videos;
		
	}
	
}