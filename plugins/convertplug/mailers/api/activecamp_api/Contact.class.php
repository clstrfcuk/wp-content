<?php

class AC_Contact extends ActiveCampaign {

	public $version;
	public $url_base;
	public $url;
	public $api_key;

	function __construct($version, $url_base, $url, $api_key) {
		$this->version = $version;
		$this->url_base = $url_base;
		$this->url = $url;
		$this->api_key = $api_key;
	}

	

	function sync($params, $post_data) {
		$request_url = "{$this->url}&api_action=contact_sync&api_output={$this->output}";
		if ($params) $request_url .= "&{$params}";
		$response = $this->curl($request_url, $post_data);
		return $response;
	}

}

?>