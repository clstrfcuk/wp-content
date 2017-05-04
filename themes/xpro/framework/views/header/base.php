<?php

// =============================================================================
// VIEWS/HEADER/BASE.PHP
// -----------------------------------------------------------------------------
// Declares the DOCTYPE for the site, includes the <head>, opens the <body>
// element as well as the .x-site <div>.
// =============================================================================

$atts = array(
  'id'    => 'x-site',
  'class' => 'x-site site'
);

$atts = x_atts( apply_filters( 'x_site_atts', $atts ) );

?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

<head>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php do_action( 'x_before_site_begin' ); ?>

  <div <?php echo $atts; ?>>

  <?php do_action( 'x_after_site_begin' ); ?>