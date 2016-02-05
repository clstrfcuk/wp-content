<?php
if(!defined('AWEBER_API_URI'))
	 define('AWEBER_API_URI',plugin_dir_path(__FILE__).'api/aweber_api/');

require_once('aweber.php');

// Load Mailchimp 
if(!defined('MAILCHIMP_API_URI'))	 
	define('MAILCHIMP_API_URI',plugin_dir_path(__FILE__).'api/mailchimp/');

require_once('mailchimp.php');

// Load Madmini
require_once('madmimi.php');

// Load Campaign Monitor
require_once('campaign_monitor.php');

// Load Active Campaign
require_once('active_campaign.php');

// Load iContact
require_once('iContact.php');

// Load emma
require_once('emma.php');

// load hotspot 
require_once('hubspot.php');