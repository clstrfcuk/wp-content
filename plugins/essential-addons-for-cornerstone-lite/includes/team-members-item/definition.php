<?php

/**
 * Element Definition: Team Member Item
 */

class EACS_Team_Item {

	public function ui() {
		return array(
      'title' => __( 'Team Member Item', 'essential-addons-cs' )
    );
	}

	public function flags() {
		return array(
      'child' => true
    );
	}



}