<?php

class Cornerstone_Headers extends Cornerstone_Plugin_Component {

  public $header_styles = '';
  public $dependencies = array( 'Coalescence' );

  public function add_styling( $header, $class_prefix, $template_loader ) {

    $coalescence = $this->plugin->component( 'Coalescence' );
    $coalescence->set_template_loader( $template_loader );
    $coalescence->set_config( array( 'class_prefix' => 'hm' ) );
    $coalescence->prepare( array(
      '_type' => 'root',
      'modules' => $header['modules']
    ) );

    $this->header_styles = $coalescence->run();

  }

  public function get_styles() {
    return $this->header_styles;
  }

}
