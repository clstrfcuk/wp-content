<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-BLOGPOSTING.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of BlogPosting
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
    'id' => '_snippet_article_type',
    'name' => '@type',
    'label' => __( 'Type', '__x__' ),
    'description' => __( 'Type of schema', '__x__' ),
    'schema_type' => '',
    'type' => 'type',
  ),
  array (
    'id' => '_snippet_blogposting_headline',
    'name' => 'headline',
    'label' => __( 'Headline', '__x__' ),
    'description' => __( 'Headline of the article.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
    'default_value' => array( 'post', 'post_title' ),
  ),
  array (
    'id' => '_snippet_blogposting_alternative_headline',
    'name' => 'alternativeHeadline',
    'label' => __( 'Alternative Headline', '__x__' ),
    'description' => __( 'A secondary title of the CreativeWork.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_blogposting_description',
    'name' => 'description',
    'label' => __( 'Description', '__x__' ),
    'description' => __( 'A description (excerpt).', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'textarea',
    // 'default_value' => array( 'post_method', 'get_the_excerpt' ),
  ),
  array (
    'id' => '_snippet_blogposting_image',
    'name' => 'image',
    'label' => __( 'Image', '__x__' ),
    'description' => __( 'An image of the item. This can be a URL or a fully described ImageObject.', '__x__' ),
    'schema_type' => 'ImageObject',
    'type' => 'text',
    'default_value' => array( 'post_method', 'get_the_post_thumbnail_url' ),
  ),
  array (
    'id' => '_snippet_blogpostindate-publisheded',
    'name' => 'datePublished',
    'label' => __( 'Date Published', '__x__' ),
    'description' => __( 'Date of first broadcast/publication.', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'date-published',
    'hide' => true,
  ),
  array (
    'id' => '_snippet_blogposting_date_modified',
    'name' => 'dateModified',
    'label' => __( 'Date Modified', '__x__' ),
    'description' => __( 'The date of the last modify.', '__x__' ),
    'schema_type' => 'Date',
    'type' => 'date-modified',
    'hide' => true,
  ),
  array (
    'id' => '_snippet_blogposting_author',
    'name' => 'author',
    'label' => __( 'Author', '__x__' ),
    'description' => __( 'The author of this content. Default to author of the post if already saved.', '__x__' ),
    'schema_type' => 'Person',
    'type' => 'text',
    'default_value' => array( 'author', 'display_name' ),
  ),
  array (
    'id' => '_snippet_blogposting_publisher',
    'name' => 'publisher',
    'label' => __( 'Publisher', '__x__' ),
    'description' => __( 'The publisher of the creative work.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
    'default_value' => array( 'snippet', 'organization_name')
  ),
);
