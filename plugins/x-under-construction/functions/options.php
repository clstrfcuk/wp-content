<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $x_under_construction_options;

$social_medias = array(
  'facebook'    => array('title' => 'Facebook', 'x-icon' => '&#xf082;'),
  'twitter'     => array('title' => 'Twitter', 'x-icon' => '&#xf081;'),
  'google_plus' => array('title' => 'Google Plus', 'x-icon' => '&#xf0d4;'),
  'linkedin'    => array('title' => 'Likedin', 'x-icon' => '&#xf08c;'),
  'xing'        => array('title' => 'XING', 'x-icon' => '&#xf169;'),
  'foursquare'  => array('title' => 'Foursquare', 'x-icon' => '&#xf180;'),
  'youtube'     => array('title' => 'Youtube', 'x-icon' => '&#xf166;'),
  'vimeo'       => array('title' => 'Vimeo', 'x-icon' => '&#xf194;'),
  'instagram'   => array('title' => 'Instagram', 'x-icon' => '&#xf16d;'),
  'pinterest'   => array('title' => 'Pinterest', 'x-icon' => '&#xf0d3;'),
  'dribbble'    => array('title' => 'Dribbble', 'x-icon' => '&#xf17d;'),
  'flickr'      => array('title' => 'Flickr', 'x-icon' => '&#xf16e;'),
  'github'      => array('title' => 'Github', 'x-icon' => '&#xf092;'),
  'behance'     => array('title' => 'Behance', 'x-icon' => '&#xf1b5;'),
  'tumblr'      => array('title' => 'Tumblr', 'x-icon' => '&#xf174;'),
  'whatsapp'    => array('title' => 'Whatsapp', 'x-icon' => '&#xf232;'),
  'soundcloud'  => array('title' => 'SoundCloud', 'x-icon' => '&#xf1be;'),
  'rss'         => array('title' => 'RSS', 'x-icon' => '&#xf143;'),
);

if ( isset( $_POST['x_under_construction_form_submitted'] ) ) {
  if ( sanitize_text_field( $_POST['x_under_construction_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $x_under_construction_options['x_under_construction_enable']           = ( isset( $_POST['x_under_construction_enable'] ) ) ? sanitize_text_field( $_POST['x_under_construction_enable'] ) : '';
    $x_under_construction_options['x_under_construction_use_custom']       = ( isset( $_POST['x_under_construction_use_custom'] ) ) ? sanitize_text_field( $_POST['x_under_construction_use_custom'] ) : '';
    $x_under_construction_options['x_under_construction_custom']           = sanitize_text_field( $_POST['x_under_construction_custom'] );
    $x_under_construction_options['x_under_construction_heading']          = sanitize_text_field( $_POST['x_under_construction_heading'] );
    $x_under_construction_options['x_under_construction_subheading']       = sanitize_text_field( $_POST['x_under_construction_subheading'] );
    $x_under_construction_options['x_under_construction_extra_text']       = strip_tags( $_POST['x_under_construction_extra_text'] );
    $x_under_construction_options['x_under_construction_date']             = sanitize_text_field( $_POST['x_under_construction_date'] );
    $x_under_construction_options['x_under_construction_background_image'] = sanitize_text_field( $_POST['x_under_construction_background_image'] );
    $x_under_construction_options['x_under_construction_logo_image']       = sanitize_text_field( $_POST['x_under_construction_logo_image'] );
    $x_under_construction_options['x_under_construction_background_color'] = sanitize_text_field( $_POST['x_under_construction_background_color'] );
    $x_under_construction_options['x_under_construction_heading_color']    = sanitize_text_field( $_POST['x_under_construction_heading_color'] );
    $x_under_construction_options['x_under_construction_subheading_color'] = sanitize_text_field( $_POST['x_under_construction_subheading_color'] );
    $x_under_construction_options['x_under_construction_date_color']       = sanitize_text_field( $_POST['x_under_construction_date_color'] );
    $x_under_construction_options['x_under_construction_social_color']     = sanitize_text_field( $_POST['x_under_construction_social_color'] );
    $x_under_construction_options['x_under_construction_whitelist']        = sanitize_text_field( $_POST['x_under_construction_whitelist'] );
    $x_under_construction_options['x_under_construction_bypass_password']  = sanitize_text_field( $_POST['x_under_construction_bypass_password'] );

    foreach ( $social_medias as $key => $sc ) {
      $key = "x_under_construction_{$key}";
      $x_under_construction_options[ $key ] = sanitize_text_field( $_POST[ $key ] );
    }

    update_option( 'x_under_construction', $x_under_construction_options );

  }
}



// Get Options
// =============================================================================

$x_under_construction_options = apply_filters( 'x_under_construction_options', get_option( 'x_under_construction' ) );

if ( $x_under_construction_options != '' ) {

  $x_under_construction_enable           = $x_under_construction_options['x_under_construction_enable'];
  $x_under_construction_use_custom       = $x_under_construction_options['x_under_construction_use_custom'];
  $x_under_construction_custom           = $x_under_construction_options['x_under_construction_custom'];
  $x_under_construction_heading          = $x_under_construction_options['x_under_construction_heading'];
  $x_under_construction_subheading       = $x_under_construction_options['x_under_construction_subheading'];
  $x_under_construction_extra_text       = $x_under_construction_options['x_under_construction_extra_text'];
  $x_under_construction_date             = $x_under_construction_options['x_under_construction_date'];
  $x_under_construction_background_image = $x_under_construction_options['x_under_construction_background_image'];
  $x_under_construction_logo_image       = $x_under_construction_options['x_under_construction_logo_image'];
  $x_under_construction_background_color = $x_under_construction_options['x_under_construction_background_color'];
  $x_under_construction_heading_color    = $x_under_construction_options['x_under_construction_heading_color'];
  $x_under_construction_subheading_color = $x_under_construction_options['x_under_construction_subheading_color'];
  $x_under_construction_date_color       = $x_under_construction_options['x_under_construction_date_color'];
  $x_under_construction_social_color     = $x_under_construction_options['x_under_construction_social_color'];
  $x_under_construction_whitelist        = $x_under_construction_options['x_under_construction_whitelist'];
  $x_under_construction_bypass_password  = $x_under_construction_options['x_under_construction_bypass_password'];

  foreach ( $social_medias as $key => $sc ) {
    $key = "x_under_construction_{$key}";
    $$key = $x_under_construction_options[ $key ];
  }

}
