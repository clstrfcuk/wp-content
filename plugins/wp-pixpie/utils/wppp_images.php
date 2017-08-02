<?php


/*
Images helper
*/
function wppp_get_image_id( $image_url ) {
    global $wpdb;
    $attachment = 
    	$wpdb->get_col(
    		$wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )
		); 
    return $attachment[0]; 
}

/*
Wordpress images sizes 
*/

function wppp_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

function wppp_register_new_sizes() {
	$sizes = wppp_get_image_sizes();
	foreach ( $sizes as $size_name => $size_val ) {
		if ( ! wppp_ends_with( $size_name, SIZE_UNCOMP ) ) {
			$new_size_name = $size_name . SIZE_UNCOMP;
			add_image_size(
				$new_size_name, 
				$size_val['width'], 
				$size_val['height'], 
				$size_val['crop'] 
			); 
		}
	}
}

function wppp_get_formatted_file_size($file_size ) {
	$file_size = $file_size / 1024;
	if ( $file_size > 512 ) {
		$file_size = number_format( (float) ( $file_size / 1024 ), 2, '.', '' ) . ' Mb';
	} else {
		$file_size = number_format( (float) $file_size, 2, '.', '' ) . ' Kb';
	}
	return $file_size;
}
