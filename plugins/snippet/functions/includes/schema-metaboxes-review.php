<?php

// =============================================================================
// FUNCTIONS/INCLUDES/SCHEMA-METABOXES-REVIEW.PHP
// -----------------------------------------------------------------------------
// List of metaboxes (attributes) of Review
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
    'id' => '_snippet_review_type',
    'name' => '@type',
    'label' => __( 'Type', '__x__' ),
    'description' => __( 'Type of schema', '__x__' ),
    'schema_type' => '',
    'type' => 'type',
  ),
  array (
    'id' => '_snippet_review_item_reviewed',
    'name' => 'itemReviewed',
    'label' => __( 'Item Reviewed', '__x__' ),
    'description' => __( 'The item that is being reviewed/rated.', '__x__' ),
    'schema_type' => 'Thing',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_review_author',
    'name' => 'author',
    'label' => __( 'Author', '__x__' ),
    'description' => __( 'The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably.', '__x__' ),
    'schema_type' => 'Organization',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_review_description',
    'name' => 'description',
    'label' => __( 'Description', '__x__' ),
    'description' => __( 'A description of the item.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_review_review_body',
    'name' => 'reviewBody',
    'label' => __( 'Review Body', '__x__' ),
    'description' => __( 'The actual body of the review.', '__x__' ),
    'schema_type' => 'Text',
    'type' => 'text',
  ),
  array (
    'id' => '_snippet_review_review_rating',
    'name' => 'reviewRating',
    'label' => __( 'Review Rating', '__x__' ),
    'description' => __( 'The rating given in this review. Note that reviews can themselves be rated. The reviewRating applies to rating given by the review. The aggregateRating property applies to the review itself, as a creative work.', '__x__' ),
    'schema_type' => 'Rating',
    'type' => 'rating',
  ),
);
