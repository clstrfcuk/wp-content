<?php

// =============================================================================
// FUNCTIONS/ADMIN/CONTROLLER.PHP
// -----------------------------------------------------------------------------
// This file handles all the admin page logic, and loads the view. Included
// from the context of "X_Email_Integration"
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Run Conditionally
//   02. Generate Tabs
//   03. Save Options
//   04. Prepare Additional Data For View
//   05. Tab Setup: Metaboxes
//   06. Load View
// =============================================================================


// Run Conditionally
// =============================================================================

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( __( 'You do not have sufficient permissions to access this page.', '__x__') );
}



// Generate Tabs
// =============================================================================

$tabs = $this->config['admin_tabs'];

foreach ( $tabs as $name => $tab ) {
  $tabs[$name]['url'] = add_query_arg( array( 'tab' => $name ), $this->get_transport( 'plugin_admin_url' ) );
}

$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->config['default_tab'];

$this->set_transport( 'tabs', $tabs );
$this->set_transport( 'current_tab', $current_tab );
$this->set_transport( 'selected_tab_view', $tabs[$current_tab]['view'] );



// Save Options
// =============================================================================

if ( isset( $_POST[ $this->slug . '_form_submitted' ] ) && strtolower( $_POST[ $this->slug . '_form_submitted' ] ) == 'submitted' ) {

  $this->options->validate_form();

  $this->options->save();

}



// Prepare Additional Data For View
// =============================================================================

$about_items  = array_key_exists('about_items', $this->config) ? $this->config['about_items'] : array();
$this->set_transport( 'about_items', $about_items );

$schema_list  = require($this->path . '/functions/includes/schema-metaboxes-list.php');
$this->set_transport( 'schema_list', $schema_list );

$country_list = require($this->path . '/functions/includes/country-list.php');
$this->set_transport( 'country_list', $country_list );

$currency_list = require($this->path . '/functions/includes/currency-list.php');
$this->set_transport( 'currency_list', $currency_list );

$language_list = require($this->path . '/functions/includes/language-list.php');
$this->set_transport( 'language_list', $language_list );

$contact_option_list = require($this->path . '/functions/includes/contact-option-list.php');
$this->set_transport( 'contact_option_list', $contact_option_list );

$contact_type_list = require($this->path . '/functions/includes/contact-type-list.php');
$this->set_transport( 'contact_type_list', $contact_type_list );

$social_list = require($this->path . '/functions/includes/social-list.php');
$this->set_transport( 'social_list', $social_list );

$org_tree = require($this->path . '/functions/includes/schema-organization-tree.php');
$this->set_transport( 'org_tree', $org_tree );

$output_list = require($this->path . '/functions/includes/output-list.php');
$this->set_transport( 'output_list', $output_list );



// Tab Setup: Metaboxes
// =============================================================================

$meta_boxes = array();

if ( array_key_exists($current_tab,  $this->config['metaboxes']) ) {

  foreach ( $this->config['metaboxes'][$current_tab] as $key => $item ) {
    $meta_boxes[$key]['title']   = $item['title'];
    $meta_boxes[$key]['content'] = $this->view->make( $item['view'] );
    $meta_boxes[$key]['hide']    = array_key_exists( 'hide', $item) ? $item['hide'] : false;
  }

}



// Load View
// =============================================================================

$this->set_transport( 'meta_boxes', $meta_boxes );

$this->view->show( 'admin/options-page' );
