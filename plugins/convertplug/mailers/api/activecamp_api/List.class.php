<?php

class AC_List_ extends ActiveCampaign {

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

	function list_($params, $post_data) {
		if ($post_data) {
			if (isset($post_data["ids"]) && is_array($post_data["ids"])) {
				// make them comma-separated.
				$post_data["ids"] = implode(",", $post_data["ids"]);
			}
		}
		$request_url = "{$this->url}&api_action=list_list&api_output={$this->output}&{$params}";
		$response = $this->curl($request_url, $post_data);
		return $response;
	}

	

}

?>