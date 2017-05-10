<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// 01. Snippet Website
// 02. Snippet Organization
// 03. Snippet Post/Page Info
// 04. Output
// -----------------------------------------------------------------------------
// =============================================================================

// Snippet Website
// =============================================================================

if ( ! function_exists( 'snippet_website' )) {

  function snippet_website() {

    $data = get_option('snippet');

    //
    // Test if website output is enabled
    //

    if ( ! is_array($data) ) {
      return;
    }

    if ( ! array_key_exists( 'output', $data ) ) {
      return;
    }

    if ( ! array_key_exists( 'website', $data['output'] ) ) {
      return;
    }

    $x_json_ld = new Snippet_Json_Ld;
    $json_ld   = $x_json_ld->generate_website($data);

    if ( null === $json_ld) {
      return null;
    }

    $json_ld_string = json_encode($json_ld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    ?><script id="snippet-json-ld-website" type="application/ld+json"><?php echo $json_ld_string; ?></script>

    <?php
  }

}




// Snippet Organization
// =============================================================================

if ( ! function_exists( 'snippet_organization' )) {

  function snippet_organization() {

    $data = get_option('snippet');

    //
    // Test if website output is enabled
    //

    if ( ! is_array($data) ) {
      return;
    }

    if ( ! array_key_exists( 'output', $data ) ) {
      return;
    }

    if ( ! array_key_exists( 'organization', $data['output'] ) ) {
      return;
    }

    $x_json_ld = new Snippet_Json_Ld;
    $json_ld   = $x_json_ld->generate_organization($data);

    if ( null === $json_ld) {
      return null;
    }

    $json_ld_string = json_encode($json_ld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    ?><script id="snippet-json-ld-organization" type="application/ld+json"><?php echo $json_ld_string; ?></script>

    <?php
  }

}


// Snippet Post/Page Info
// =============================================================================

if ( ! function_exists( 'snippet_page_post' )) {

  function snippet_page_post() {

    global $wp_query;

    if ( is_single() || is_page() ) {

      $post_obj = $wp_query->get_queried_object();

      $data = get_option('snippet');

      //
      // Test if post type is enabled
      //

      if ( ! is_array($data) ) {
        return;
      }

      if ( ! array_key_exists( 'output', $data ) ) {
        return;
      }

      if ( ! array_key_exists( $post_obj->post_type, $data['output'] ) ) {
        return;
      }

      $x_json_ld = new Snippet_Json_Ld;
      $json_ld   = $x_json_ld->generate_schema_for_page_post( $post_obj->ID );

      if ( null === $json_ld) {
        return null;
      }

      $json_ld_string = json_encode($json_ld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

      ?><script id="snippet-json-ld-page-post" type="application/ld+json"><?php echo $json_ld_string; ?></script>

      <?php
    }

  }

}



// Output
// =============================================================================

if ( SNIPPET_IS_LOADED === true ) {

  add_action( 'wp_footer', 'snippet_website' );
  add_action( 'wp_footer', 'snippet_organization' );
  add_action( 'wp_footer', 'snippet_page_post' );

}
