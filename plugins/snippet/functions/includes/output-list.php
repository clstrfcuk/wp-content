<?php

// =============================================================================
// FUNCTIONS/INCLUDES/OUTPUP-LIST.PHP
// -----------------------------------------------------------------------------
// List of output targets
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Array of values
// =============================================================================

// Array of values
// =============================================================================

$output_list_data = array(
  'main' => array (
    'website' => __( 'Website', '__x__' ),
    'organization' => __( 'Organization', '__x__' ),
  ),
);

$output_list_post_types = get_post_types( array('public' => true), 'object');
foreach ( $output_list_post_types as $post_type ) {
  $output_list_data['post_types'][ $post_type->name ] = $post_type->label;
}

return $output_list_data;
