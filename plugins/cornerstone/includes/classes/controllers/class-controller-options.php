<?php

class Cornerstone_Controller_Options extends Cornerstone_Plugin_Component {

  public function config() {

    return array(
      'i18n' => $this->plugin->i18n( 'options' ),
    );

  }

}
