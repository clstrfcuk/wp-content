<?php
/**
 * Facebook Post Planner
 * http://www.aa-team.com
 * ======================
 *
 * @package			psp_fbPlannerUtils
 * @author			AA-Team
 */

// Plugin facebook SDK load
global $psp;
require_once ( $psp->cfg['paths']['scripts_dir_path'] . '/facebook/facebook.php' );

class psp_fbPlannerUtils
{
    // Hold an instance of the class
    private static $instance;
	
	// Hold an utils of the class
    private static $utils;
	
    private $fb;
    
    public $the_plugin = null;
    
    private $fb_details = null;
    	
 
    // The singleton method getInstance
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new psp_fbPlannerUtils;
        }
        return self::$instance;
    }
	
	// The constructor, call on class instance
	public function __construct(){
		
        global $psp;

        $this->the_plugin = $psp;
	
		$this->fb_details = $this->the_plugin->getAllSettings('array', 'facebook_planner');
		
		// create utils
		self::$utils = array(
			'token'		=> get_option('psp_fb_planner_token'),
			'appId'		=> $this->fb_details['app_id'],
			'secret'	=> $this->fb_details['app_secret'],
			'inputs_available' => $this->fb_details['inputs_available']
		); 

		// try to login on fb with static facebook key
		if ( ! $this->fb_login() ) {
			//die('Invalid FB login!');
			die('User is not loggedin or app authorized yet.');
		}
		//$this->getFbUserData();
	}

/*
	public function fb_login() {
		// Create our Application instance (replace this with your appId and secret).
		$this->fb = new psp_Facebook(array(
			'appId'  => self::$utils['appId'],
			'secret' => self::$utils['secret'],
		));
		
		// set saved access token
		$this->fb->setAccessToken(self::$utils['token']);
		
		// Get User ID
		$user = $this->fb->getUser();
		if(trim($user) == ""){
			return false;
		}
		
		return true;
	}
*/
	public function fb_login() {
		$pms = array(
			'fb_details'		=> $this->fb_details,
			//'psp_redirect_url'	=> $psp_redirect_url,
		);
		$is_loggedin = $this->the_plugin->facebook_is_loggedin($pms);
		return $is_loggedin;
	}

/*
	public function getFbUserData() {
		if($this->fb_login()){
			return $this->fb->api('/me');
		}else{
			return array();
		}
	}
*/
	public function getFbUserData() {
		$pms = array(
			'fb_details'		=> $this->fb_details,
			//'psp_redirect_url'	=> $psp_redirect_url,
		);
		$retUserData = $this->the_plugin->facebook_get_user_profile($pms);
		//var_dump('<pre>', $retUserData, $retUserData['result']['name'], '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		return $retUserData;
	}

/*
	public function fb_publish( $pms=array() ) {
		$pms = array_merge(array(
			'wall'			=> '',
			'fields'		=> array(),
		), $pms);
		extract($pms);

		try {
			$ret = $this->fb->api(
				$wall, 
				'post',
				$fields
			);
		} catch (psp_FacebookApiException $e) {
			if (isset($e->faultcode)) { // error occured!
				$msg = $e->faultcode .  ' : ' . (isset($e->faultstring) ? $e->faultstring : $e->getMessage());
			} else {
				$msg = $e->getMessage();
			}
			var_dump('<pre>', $msg ,'</pre>'); die;
			return false;
		}
		return true;
	}
*/
	public function fb_publish( $pms=array() ) {
		$pms = array_merge(array(
			'facebook'			=> null,
			'fb_details'		=> $this->fb_details,
			//'plugin_url'		=> '',
			//'plugin_url_'		=> '',
			'do_authorize'		=> true,
			'wall'			=> '',
			'fields'		=> array(),
		), $pms);
		$retPublish = $this->the_plugin->facebook_publish($pms);
		//if ( ! $retPublish['opStatus'] ) {
		//	echo $retPublish['opMsg'];
		//	exit;
		//}
		//var_dump('<pre>', $retPublish , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		return $retPublish;
	}

	public function publishToWall($id, $whereToPost, $postPrivacy, $postData = NULL) {

		$ret = array(
			'opStatus'		=> 'invalid',
			'opMsg'			=> '',
			'opDetails'		=> array(
				'profile'		=> array(),
				'page_group'	=> array(),
			),
		);
		$html = array();

		// retrive WP post metadata
		if( is_null($postData) ) {
			$postData = $this->getPostByID($id);
		}

		// where to publish post
		$whereToPost = unserialize($whereToPost);
		
		if(trim($whereToPost['profile']) == '' && trim($whereToPost['page_group']) == '') {
			$ret['opMsg'] = '<span style="color: red; font-weight: bold;">' . __( 'You need to select Facebook Profile or Page / Group, where you want to post.', 'psp' ) . '</span>';

			//return false;
			return $ret;
		}

		if(count($postData) > 0) {
			//try {
				$post_link = trim($postData['link']) == 'post_link' ? get_permalink($id) : $postData['link'];
				
				if($postPrivacy == 'CUSTOM') {
					$q_postPrivacy = array('value' => $postPrivacy, 'friends' => 'SELF');
				}else{
					$q_postPrivacy = array('value' => $postPrivacy);
				}

				$finalImg = '';
				if ( $postData['use_picture'] == 'yes' ) {
				
					if ( empty($postData['picture']) ) {

						if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $id ) ) {
							$__featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
							$__featured_image = $__featured_image[0];
							if ( !empty($__featured_image) )
								$postData['picture'] = $__featured_image;
						}
					}

					if ( !empty($postData['picture']) ) {
	
						//$finalImg = '{plugin_url}timthumb.php?src={img}&amp;w={thumb_w}&amp;h={thumb_h}&amp;zc={thumb_zc}';
						$finalImg = '{plugin_url}timthumb.php?src={img}&w={thumb_w}&h={thumb_h}&zc={thumb_zc}';
	
						$img_size = explode('x', $this->fb_details['featured_image_size']);
	
						$finalImg = str_replace('{plugin_url}', $this->the_plugin->cfg['paths']['plugin_dir_url'], $finalImg);
						$finalImg = str_replace('{img}', $postData['picture'], $finalImg);
						$finalImg = str_replace('{thumb_w}', (isset($img_size[0]) ? $img_size[0] : 450), $finalImg);
						$finalImg = str_replace('{thumb_h}', (isset($img_size[1]) ? $img_size[1] : 320), $finalImg);
						$finalImg = str_replace('{thumb_zc}', ($this->fb_details['featured_image_size_crop'] == 'true' ? 1 : 2), $finalImg);
					}
				} // end use picture!

				$arrFbData = array(
					'link'			=> $post_link,
					'name' 			=> stripslashes($postData['name']),
					'description' 	=> substr( stripslashes($postData['description']), 0, 2000 ),
				);
				
				if ( is_array(self::$utils['inputs_available']) && !empty(self::$utils['inputs_available']) ) {
					$arrFbData = array_merge($arrFbData, array(
						'picture'	 	=> in_array('image', self::$utils['inputs_available']) ? $finalImg : '',
						'caption'		=> in_array('caption', self::$utils['inputs_available']) ? stripslashes($postData['caption']) : '',
						'message' 		=> in_array('message', self::$utils['inputs_available']) ? stripslashes($postData['message']) : ''
					));
				}

				// post on profile
				if( trim($whereToPost['profile']) == 'on' ) {
					$arrFbData['privacy'] = $q_postPrivacy;
 					
					$statusUpdate = $this->fb_publish(array(
						'wall'		=> '/me/feed', 
						'fields'	=> $arrFbData,
					));
					$ret['opDetails']['profile'] = array(
						'status'	=> $statusUpdate['opStatus'] ? 'valid' : 'invalid',
						'msg'		=> $statusUpdate['opStatus']
							? __( 'The post was published successfully on your facebook profile!', 'psp' )
							: '<span style="color: red; font-weight: bold;">' . __( 'Error on publishing the post on your facebook profile. Please try again later!', 'psp' ) . '</span>',
					);
				} else {
					$ret['opDetails']['profile'] = array(
						'status'	=> 'valid',
						'msg'		=> '',
					);
				}
				$html[] = $ret['opDetails']['profile']['msg'];

				// post on page / group
				if ( trim($whereToPost['page_group']) != '' ) {
					unset( $arrFbData['privacy'] );

					$page_access_token = null;
					$whereToPost = explode('##', $whereToPost['page_group']);
					$postTo_ident = $whereToPost[0];
					$postTo_id = $whereToPost[1];
					
					if($postTo_ident == 'page') {
						$page_access_token = $whereToPost[2];
						if( !empty($page_access_token) ) {
							$arrFbData['access_token'] = $page_access_token;
						}
					}

					$statusUpdate = $this->fb_publish(array(
						'wall'		=> "/$postTo_id/feed", 
						'fields'	=> $arrFbData,
					));
					$ret['opDetails']['page_group'] = array(
						'status'	=> $statusUpdate['opStatus'] ? 'valid' : 'invalid',
						'msg'		=> $statusUpdate['opStatus']
							? __( 'The post was published successfully on your selected facebook page / group!', 'psp' )
							: '<span style="color: red; font-weight: bold;">' . __( 'Error on publishing the post on your selected facebook page / group. Please try again later!', 'psp' ) . '</span>',
					);
				} else {
					$ret['opDetails']['page_group'] = array(
						'status'	=> 'valid',
						'msg'		=> '',
					);
				}
				$html[] = $ret['opDetails']['page_group']['msg'];

				$ret['opStatus'] =
					( 'valid' == $ret['opDetails']['profile']['status'] ) && ( 'valid' == $ret['opDetails']['page_group']['status'] )
					? 'valid' : 'invalid';
				$x = 1;
				$ret['opMsg'] = 'valid' == $ret['opStatus']
					? __( 'The post was published on facebook OK!', 'psp' )
					//: '<span style="color: red; font-weight: bold;">' . __( 'Error on publishing. Please try again later!', 'psp' ) . '</span>';
					: implode('<br/>', $html);

				//return true;
				return $ret;
			//} catch (psp_FacebookApiException $e) {
			//	var_dump('<pre>', $e ,'</pre>'); die;
			//	return false;
			//}
		}
		//return false;
		return $ret;
	}
	
	public function getPostByID($id){
		if((int)$id > 0){
			return array(
				'name' 			=> get_post_meta($id, 'psp_wplannerfb_title', true),
				'link' 			=> get_post_meta($id, 'psp_wplannerfb_permalink', true),
				'description' 	=> get_post_meta($id, 'psp_wplannerfb_description', true),
				'caption' 		=> get_post_meta($id, 'psp_wplannerfb_caption', true),
				'message' 		=> get_post_meta($id, 'psp_wplannerfb_message', true),
				'picture'	 	=> get_post_meta($id, 'psp_wplannerfb_image', true),
				'use_picture'	=> get_post_meta($id, 'psp_wplannerfb_useimage', true)
			);
		}
		return array();
	}
}