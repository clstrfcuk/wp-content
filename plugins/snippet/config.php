<?php
// =============================================================================
// CONFIG.PHP
// -----------------------------------------------------------------------------
// The framework configuration sets up metaboxes, about items, shortcodes, and
// widgets to be used for the core of the plugin.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Configuration
// =============================================================================

// Configuration
// =============================================================================

return array(

  //
  // Tabs.
  //

  'admin_tabs' => array(
    'welcome' => array(
      'title' => __( 'Welcome to Snippet', '__x__' ),
      'view'  => 'admin/welcome'
    ),
    'settings' => array(
      'title' => __( 'Global Settings', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
    'website' => array(
      'title' => __( 'Website', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
    'organization' => array(
      'title' => __( 'Organization', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
    'address' => array(
      'title' => __( 'Address', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
    'contacts' => array(
      'title' => __( 'Contacts', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
    'social' => array(
      'title' => __( 'Social Links', '__x__' ),
      'view'  => 'admin/tab-main'
    ),
  ),

  'default_tab' => 'welcome',



  //
  // Metaboxes.
  //

  'metaboxes' => array(
    'settings' => array(
       array(
        'title' => __( 'Schema Settings', '__x__' ),
        'view'  => 'admin/metabox-settings-schema'
      ),
    ),
    'website' => array(
       array(
        'title' => __( 'Required Fields', '__x__' ),
        'view'  => 'admin/metabox-website-required'
      ),
      array(
       'title' => __( 'Optional Fields', '__x__' ),
       'view'  => 'admin/metabox-website-optional'
     ),
    ),
    'organization' => array(
      array(
        'title' => __( 'Required Fields', '__x__' ),
        'view'  => 'admin/metabox-organization-required'
      ),
      array(
        'title' => __( 'Optional Fields', '__x__' ),
        'view'  => 'admin/metabox-organization-optional'
      ),
    ),
    'address' => array(
      array(
        'title' => __( 'Address', '__x__' ),
        'view'  => 'admin/metabox-address'
      ),
      array(
        'title' => __( 'Geographical Coordinates', '__x__' ),
        'view'  => 'admin/metabox-geo'
      ),
    ),
    'contacts' => array(
      array(
        'title' => __( 'Contacts Listing', '__x__' ),
        'view'  => 'admin/metabox-contacts'
      ),
      array(
        'title' => __( 'Required Fields', '__x__' ),
        'view'  => 'admin/metabox-contacts-required',
        'hide'  => true
      ),
      array(
        'title' => __( 'Optional Fields', '__x__' ),
        'view'  => 'admin/metabox-contacts-optional',
        'hide'  => true
      ),
    ),
    'social' => array(
      array(
        'title' => __( 'Social Links', '__x__' ),
        'view'  => 'admin/metabox-social'
      ),
    ),
  ),



  //
  // About items.
  //

  'about_items' => array(
    'settings' => array(
      'title'   => 'Global Settings',
      'content' => 'Define what Schema type will be the default for each post type or disable Schema completely for certain post types.'
    ),
    'website' => array(
      'title'   => __( 'Website', '__x__' ),
      'content' => __( 'Define basic info for your website here. This is where you’ll set up most of the settings and will effect the appearance of your site on search engines.', '__x__' ),
    ),
    'organization' => array(
      'title'   => __( 'Organization', '__x__' ),
      'content' => __( 'Do you want to have your company information appear in one of those fancy Google side boxes? Fill in the required info here and don’t forget to add a logo!', '__x__' ),
    ),
    'address' => array(
      'title'   => __( 'Address', '__x__' ),
      'content' => __( 'Want a map within the side box? Fill in the address and add the co-ordinates of your business with latitude and longitude.', '__x__' ),
    ),
    'contacts' => array(
      'title'   => __( 'Contacts', '__x__' ),
      'content' => __( 'Make it easy for your customer to contact you by adding multiple contact types.', '__x__' ),
    ),
    'social' => array(
      'title'   => __( 'Social Links', '__x__' ),
      'content' => __( 'Add Social Profile links so your customers can follow you anywhere.', '__x__' ),
    ),
    'support' => array(
      'title'   => __( 'Support', '__x__' ),
      'content' => __( 'For questions, please visit our <a href="//theme.co/x/member/kb/extension-snippet/" target="_blank">Knowledge Base tutorial</a> for this plugin.', '__x__' ),
    )
  ),

);
