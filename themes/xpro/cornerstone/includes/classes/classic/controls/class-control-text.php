<?php
class Cornerstone_Control_Text extends Cornerstone_Control {

	protected $default_context = 'content';

	public function sanitize( $data ) {
		return Cornerstone_Control::sanitize_html( $data );
	}

}