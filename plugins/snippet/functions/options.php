<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Declare any option defaults that will be used. If the plugin hasn't saved yet
// the values here will be used instead of user provided values. This also
// gaurantees the avoidance of notices related to "undefined" variables.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Option Defaults
// =============================================================================

// Option Defaults
// =============================================================================

return array(

  // Global Settings
  // ---------------------------------------------------------------------------

  'output'         => 'disabled',
  'default_schema' => '',
  'schema'         => array(),


  // Website
  // ---------------------------------------------------------------------------

  'website_url'            => get_option('siteurl'),
  'website_name'           => get_option('blogname'),
  'website_alternate_name' => get_option('blogdescription'),
  'website_sitelinks'      => 'no',

  //  Organization
  // ---------------------------------------------------------------------------

  'organization_type'            => '',
  'organization_name'            => '',
  'organization_image'           => '',
  'organization_url'             => get_option('siteurl'),
  'organization_logo'            => '',
  'organization_additional_type' => '',
  'organization_description'     => '',
  'organization_operation_hours' => '',

  // Address && GeoCoordinates
  // ---------------------------------------------------------------------------

  'address_street_address' => '',
  'address_locality'       => '',
  'address_region'         => '',
  'address_postal_code'    => '',
  'address_country'        => '',
  'geo_latitude'           => '',
  'geo_longitude'          => '',

  // Contact
  // ---------------------------------------------------------------------------

  'contacts' => array(),

  // Social
  // ---------------------------------------------------------------------------

  'social_facebook'    => '',
  'social_twitter'     => '',
  'social_google_plus' => '',
  'social_instagram'   => '',
  'social_youtube'     => '',
  'social_linkedin'    => '',
  'social_myspace'     => '',
  'social_pinterest'   => '',
  'social_sondcloud'   => '',
  'social_tumblr'      => '',

);
