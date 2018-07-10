<?php

namespace Envira\Utils;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}
class Tracking{

    public $data = array();

    public function __construct(){

    }

    public function can_track(){

        return get_option( 'envira_can_track', false );

    }

    public function setup_data(){

        $data = array();
        $active_plugins = wp_get_active_and_valid_plugins();

        $data['active_plugins']   = '';
        $data['inactive_plugins'] = '';
        $data['galleries']        = wp_count_posts( 'envira' )->publish;
        $data['envira_version']   = '';
        $data['wp_version']       = '';
        $data['pro_install_date'] = '';
        $data['lite_upgrade']     = '';

        //Set the data;
        $this->data = $data;

    }

    public function optin(){

        $notice = get_option( 'envira_tracking_notice', true );

    }

    public function check_in( $override = false ){

        $home_url = '';


    }
}