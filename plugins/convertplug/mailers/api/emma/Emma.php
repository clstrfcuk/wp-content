<?php

	require_once 'EmmaExceptions.php';
	
	/**
	 * Emma API Wrapper
	 *
	 * @category  Services
	 * @package   Services_Emma
	 * @author    Dennis Monsewicz <dennismonsewicz@gmail.com>
	 * @copyright 2013 Dennis Monsewicz <dennismonsewicz@gmail.com>
	 * @license   http://www.opensource.org/licenses/mit-license.php MIT
	 * @link      https://github.com/myemma/emma-wrapper-php
	 */
	class Emma {
		/**
		* Cache the API base url
		*/
		public $base_url = "https://api.e2ma.net/";
		/**
		* Cache the user account id for API usage
		*/
		protected $_account_id;
		/**
		* Cache the user public key for API usage
		*/
		protected $_pub_key;
		/**
		* Cache the user private key for API usage
		*/
		protected $_priv_key;
		/**
		* Cache optional postdata for HTTP request
		*/
		public $_postData = array();
		/**
		* Cache optional query params for HTTP request
		*/
		public $_params = array();
		
		protected $_debug = false;
		
		/**
		* Connect to the Emma API
		* @param string $account_id		Your Emma Account Id
		* @param string $pub_api_key	Your Emma Public API Key
		* @param string $priv_api_key	Your Emma Public API Key
		* @access public
		*/
		function __construct($account_id, $pub_api_key, $pri_api_key, $debug = false) {
			if(empty($account_id))
				throw new Emma_Missing_Account_Id();
			
			if(empty($pub_api_key) || empty($pri_api_key))
				throw new Emma_Missing_Auth_For_Request();
			
			$this->_account_id = $account_id;
			$this->_pub_key = $pub_api_key;
			$this->_priv_key = $pri_api_key;
			$this->_debug = $debug;
		}
		
		
		/**
		* Adds or updates a single audience member. If you are performing actions on bulk members please use the membersBatchAdd() function
		* @param array $member_data		Array of options
		* @access public
		* @return The member_id of the new or updated member, whether the member was added or an existing member was updated, and the status of the member. The status will be reported as ‘a’ (active), ‘e’ (error), or ‘o’ (optout).
		*/
		function membersAddSingle($member_data = array()) {
			return $this->post("/members/add", $member_data);
		}

		
		/**
		* Add a single member to one or more groups.
		* @param int $id		Member ID
		* @param array $params	Array of options
		* @access public
		* @return An array of ids of the affected groups.
		*/
		function membersGroupsAdd($id, $params = array()) {
			return $this->put("/members/{$id}/groups", $params);
		}
	
		
		/** 
		* API Calls to the Groups related endpoint(s)
		* @see http://api.myemma.com/api/external/groups.html
		*/
		
		/**
		* Get a basic listing of all active member groups for a single account.
		* @param array $params		Array of options
		* @access public
		* @return 	An array of groups.
		*/
		function myGroups($params = array()) {
			return $this->get("/groups", $params);
		}

		

		/**
		* Send a GET HTTP request
		* @param string $path		Optional post data
		* @param array $params		Optional query string parameters
		* @return array of information from API request
		* @access public
		*/
		protected function get($path, $params = array()) {
			$this->_params = array_merge($params, $this->_params);
			$url = $this->_constructUrl($path);
			return $this->_request($url);
		}
		
		/**
		* Send a POST HTTP request
		* @param string $path		Request path
		* @param array $postData	Optional post data
		* @return array of information from API request
		* @access public
		*/
		protected function post($path, $params = array()) {
			$url = $this->_constructUrl($path);
			$this->_postData = array_merge($params, $this->_postData);
			return $this->_request($url, "post");
		}
		
		/**
		* Send a PUT HTTP request
		* @param string $path		Request path
		* @param array $postData	Optional post data
		* @return array of information from API request
		* @access public
		*/
		protected function put($path, $postData = array()) {
			$url = $this->_constructUrl($path);
			$this->_postData = array_merge($postData, $this->_postData);
			return $this->_request($url, "put");
		}
		
		/**
		* Send a DELETE HTTP request
		* @param string $path		Request path
		* @param array $params		Optional query string parameters
		* @return array of information from API request
		* @access public
		*/
		protected function delete($path, $params = array()) {
			$this->_params = array_merge($params, $this->_params);
			$url = $this->_constructUrl($path);
			return $this->_request($url, "delete");
		}

		
		/**
		* Performs the actual HTTP request using cURL
		* @param string $url		Absolute URL to request
		* @param array $verb		Which type of HTTP Request to make
		* @return json encoded array of information from API request
		* @access private
		*/
		protected function _request($url, $verb = null) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->_pub_key}:{$this->_priv_key}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			if(isset($verb)) {
				if($verb == "post") {
					curl_setopt($ch, CURLOPT_POST, true);
				} else {
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($verb));
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->_postData));
			}
			
			$data = curl_exec($ch);
			$info = curl_getinfo($ch);
			
			if($this->_debug) {
				print_r($data . "\n");
				print_r($info);
			}
			
			curl_close($ch);
			
			if($this->_validHttpResponseCode($info['http_code'])) {
				return $data;
			} else {
				throw new Emma_Invalid_Response_Exception(null, 0, $data, $info['http_code']);
			}
		}
		
		/**
		* Performs the actual HTTP request using cURL
		* @param string $path		Relative or absolute URI
		* @param array $params		Optional query string parameters
		* @return string $url
		* @access private
		*/
		protected function _constructUrl($path) {
			$url = $this->base_url . $this->_account_id;
			$url .= $path;
			$url .= (count($this->_params)) ? '?' . http_build_query($this->_params) : '';
			
			return $url;
		}
		
		/**
		* Validate HTTP response code
		* @param integer $code 		HTTP code
		* @return boolean
		* @access private
		*/
		protected function _validHttpResponseCode($code) {
			return (bool)preg_match('/^20[0-9]{1}/', $code);
		}
	}
