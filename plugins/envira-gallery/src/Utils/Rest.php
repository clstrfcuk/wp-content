<?php

namespace Envira\Utils;

class Rest{

    public $namespace = 'envira';
    public $version = 'v1';

    public function __construct(){

    }

    public function reguster_routes(){


        do_action( 'envira_register_routes' );

    }

    public function get_gallery(){

    }

    public function get_gallery_config(){

    }

    public function update_gallery(){

    }

    public function get_gallery_images(){

    }
    public function get_image(){

    }
    public function update_image(){

    }

    public function validate_request(){

    }

}