<?php

	// use to clear an option for customize page
	if( !function_exists('infinite_clear_option') ){
		function infinite_clear_option(){
			$options = array('general', 'typography', 'color', 'plugin');

			foreach( $options as $option ){
				unset($GLOBALS[INFINITE_SHORT_NAME . '_' . $option]);
			}
			
		}
	}