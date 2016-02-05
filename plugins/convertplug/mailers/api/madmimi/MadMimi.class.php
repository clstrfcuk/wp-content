<?php
/*
	Mad Mimi for PHP
	v2.0.3 - Cleaner, faster, and much easier to use and extend. (In my opinion!)

	For release notes, see the README that should have been included.

	_______________________________________

	Copyright (C) 2010 Mad Mimi LLC
	Authored by Nicholas Young <nicholas@madmimi.com> ...and a host of contributors.

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/
if (!class_exists('CP_Spyc')) {
	require("Spyc.class.php");
}
if (!function_exists('curl_init')) {
  die('Mad Mimi for PHP requires the PHP cURL extension.');
}
class MadMimi {
	function __construct($email, $api_key, $debug = false) {
		$this->username = $email;
		$this->api_key = $api_key;
		$this->debug = $debug;
	}
	function default_options() {
		return array('username' => $this->username, 'api_key' => $this->api_key);
	}
	function DoRequest($path, $options, $return_status = false, $method = 'GET') {
		if ($method == 'GET') {
			$request_options = "?";
		} else {
			$request_options = "";
		}
		$request_options .= http_build_query($options);
		$url = "https://api.madmimi.com{$path}";
		if ($method == 'GET') {
			$url .= $request_options;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		// Fix libcurl vs. apache2
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		if ($return_status == true) {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		} else {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		switch($method) {
			case 'GET':
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request_options);
				break;
		}
		if ($this->debug == true) {
			echo "URL: {$url}<br />";
			if ($method == 'POST') {
				echo "Request Options: {$request_options}";
			}
		} else {
			$result = curl_exec($ch);
			if( $result === false){
			  $error = curl_error($ch);
		    echo 'Curl error: ', $error, "\n";
  			die($error);
			}
		}
		curl_close($ch);
		if ($this->debug == false) {
			return $result;
		}
	}
	
	function Lists($return = false) {
		$request = $this->DoRequest('/audience_lists/lists.json', $this->default_options(), $return);
		return $request;
	}
	
	
	function SendMessage($options, $yaml_body = null, $return = false) {
		if (class_exists('CP_Spyc') && $yaml_body != null) {
			$options['body'] = CP_Spyc::YAMLDump($yaml_body);
		}
		$options = $options + $this->default_options();
		if (isset($options['list_name'])) {
			$request = $this->DoRequest('/mailer/to_list', $options, $return, 'POST');
		} else {
			$request = $this->DoRequest('/mailer', $options, $return, 'POST');
		}
		return $request;
	}
	function SendHTML($options, $html, $return = false) {
		if ((!strstr($html, '[[tracking_beacon]]')) && (!strstr($html, '[[peek_image]]'))) {
			die('Please include either the [[tracking_beacon]] or the [[peek_image]] macro in your HTML.');
		}
		$options = $options + $this->default_options();
		$options['raw_html'] = $html;
		if (isset($options['list_name'])) {
			$request = $this->DoRequest('/mailer/to_list', $options, $return, 'POST');
		} else {
			$request = $this->DoRequest('/mailer', $options, $return, 'POST');
		}
		return $request;
	}
	function SendPlainText($options, $message, $return = false) {
		if (!strstr($message, '[[unsubscribe]]')) {
			die('Please include the [[unsubscribe]] macro in your text.');
		}
		$options = $options + $this->default_options();
		$options['raw_plain_text'] = $message;
		if (isset($options['list_name'])) {
			$request = $this->DoRequest('/mailer/to_list', $options, $return, 'POST');
		} else {
			$request = $this->DoRequest('/mailer', $options, $return, 'POST');
		}
		return $request;
	}
	
	function Promotions($page = 1, $return = false) {
		$options = array('page' => $page) + $this->default_options();
		$request = $this->DoRequest('/promotions.xml', $options, $return);
		return $request;
	}
	
	
	function AddMembership($list_name, $email, $additional = array(), $return = false) {
		$options = array('email' => $email) + $additional + $this->default_options();
		$path = '/audience_lists/' . rawurlencode($list_name) . '/add';
		$request = $this->DoRequest($path, $options, $return, 'POST');
		return $request;
	}
	
}
