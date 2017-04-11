<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-EVENT.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of Event
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Array of values
// =============================================================================

// Array of values
// =============================================================================

return array (
  array (
    'id' => '_snippet_event_name',
    'name' => 'name',
    'label' => __( 'Name', '__x__' ),
    'description' => __( 'The name of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_event_description',
    'name' => 'description',
    'label' => __( 'Description', '__x__' ),
    'description' => __( 'A description of the event.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'textarea',
  ),
  array (
    'id' => '_snippet_event_image',
    'name' => 'image',
    'label' => __( 'Image', '__x__' ),
    'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'media',
  ),
  array (
    'id' => '_snippet_event_start_date',
    'name' => 'startDate',
    'label' => __( 'Start Date', '__x__' ),
    'description' => __( 'The start date and time of the item (in ISO 8601 date format).', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'datetime',
  ),
  array (
    'id' => '_snippet_event_end_date',
    'name' => 'endDate',
    'label' => __( 'End Date', '__x__' ),
    'description' => __( 'The end date and time of the item (in ISO 8601 date format).', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'datetime',
  ),
  array (
    'id' => '_snippet_event_location',
    'name' => 'location',
    'label' => __( 'Location', '__x__' ),
    'description' => __( 'The location of for example where the event is happening, an organization is located, or where an action takes place.', '__x__' ),
    'schema_type' => 'PostalAddress',
    'type' => 'place',
  ),
  array (
    'id' => '_snippet_event_offers',
    'name' => 'offers',
    'label' => __( 'Offer', '__x__' ),
    'description' => __( 'An offer to provide event. Select a currency, a price on "0.00" format and a URL for the offer (like to buy the ticket).', '__x__' ),
    'schema_type' => 'Offer',
    'type' => 'offer',
  ),
  array (
    'id' => '_snippet_event_performer',
    'name' => 'performer',
    'label' => __( 'Performer', '__x__' ),
    'description' => __( 'A performer at the event&#x2014;for example, a presenter, musician, musical group or actor. Supersedes performers.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_event_url',
    'name' => 'url',
    'label' => __( 'Url', '__x__' ),
    'description' => __( 'URL of the item.', '__x__' ),
    'schema_type' => 'URL',
    'type' => 'text',
  ),

);
