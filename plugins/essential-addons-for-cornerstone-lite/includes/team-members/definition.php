<?php

/**
 * Element Definition: "Team Members"
 */

class EACS_Team_Members {

	public function ui() {
		return array(
			'name'        => 'eacs-team-members',
      		'title'	=> __( 'EA Team Members', 'essential-addons-cs' ),
      		'icon_group' => 'essential-addons-cs',
      		'icon_id' => 'eacs-team-members'
    );
	}

	public function flags() {
		// dynamic_child allows child elements to render individually, but may cause
		// styling or behavioral issues in the page builder depending on how your
		// shortcodes work. If you have trouble with element presentation, try
		// removing this flag.
		return array(
			'dynamic_child' => false
		);
	}

}