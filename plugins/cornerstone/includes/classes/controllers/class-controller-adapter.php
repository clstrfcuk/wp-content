<?php

class Cornerstone_Controller_Adapter extends Cornerstone_Plugin_Component {

  public function __call( $name, $arguments ) {

    $params = $arguments[0];

    $component_name = 'Model_' . cs_to_component_name( $name );
    $model = $this->plugin->loadComponent( $component_name );

    if ( ! $model ) {
      throw new Exception( "Requested model '$component_name' does not exist." );
    }

    return $model->query( $params );

  }

}
