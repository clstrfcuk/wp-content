<?php

/**
 * Shortcode handler : Product Grid
 */

?>


<?php  

/*
 * => VARS & INFO
 * ---------------------------------------------------------------------------*/


$randnum = rand(0,5000); 


//
// Dynamic classes
//


// Add border class

$add_border   = ( ($add_border == 1) ? "slide-border-enabled" : "" );

// Preset Styles
switch ( $preset_style ) {
  case 'preset-theme-1':
    $preset_style = 'eacs-product-simple';
    break;

  case 'preset-theme-2':
    $preset_style = 'eacs-product-reveal';
    break;

  case 'preset-theme-3':
    $preset_style = 'eacs-product-overlay';
    break;

  default: // none
    $preset_style  = 'eacs-product-no-style';
    break;
}

// Product Columns
switch ( $product_columns ) {
  case 'single_column':
    $product_columns = 'eacs-col-1';
    break;

  case 'two_columns':
    $product_columns = 'eacs-col-2';
    break;

  case 'three_columns':
    $product_columns = 'eacs-col-3';
    break;

  default: // NONE
    $product_columns  = 'eacs-col-4';
    break;
}

// Toggle
$show_rating   = ( ($show_rating == 1) ? "show_rating" : "hide_rating" );


// Class, ID, Styles
$product_grid_id = "eacs-product-grid-".$randnum;
$class      = "eacs-product-carousel eacs-product-grid" . " " . $add_border  . " " . $preset_style . " " . $product_columns . " " . $show_rating . " " . $class ;

?>
<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => $style ) ); ?>>
  <div id="<?= $product_grid_id ?>">
		<?php echo do_shortcode("[recent_products per_page=\"$max_product_count\" category=\"$category\"]") ?>
    <div class="clearfix"></div>
	</div>

<script type="text/javascript">

(function ($) {
    'use strict';

  $(window).load(function(){
    $('.eacs-product-grid .woocommerce').removeClass('columns-4');
    $('.eacs-product-grid ul.products > li').removeClass('last');
  });
    
}(jQuery));

</script>
</div>


<style type="text/css">

/* Inheriting some styles from product carousel because DRY! :) */

.eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce li.product {
  background-color: <?php echo $product_bg_color; ?>;
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce ul.products li.product h3, .eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce ul.products li.product h3 a, .eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce ul.products li.product .price, .eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce ul.products li.product .price .amount {
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel.slide-border-enabled <?php echo '#'.$product_grid_id; ?> .woocommerce li.product {
  border: <?= $product_border_width ?>px solid <?= $product_border_color?>;
}

.eacs-product-carousel <?php echo '#'.$product_grid_id; ?> .woocommerce li.product .entry-product, .eacs-product-carousel.eacs-product-reveal <?php echo '#'.$product_grid_id; ?> .woocommerce li.product .entry-wrap, .eacs-product-carousel.eacs-product-overlay <?php echo '#'.$product_grid_id; ?> .woocommerce li.product .entry-wrap::before, .eacs-product-carousel.eacs-product-reveal <?php echo '#'.$product_grid_id; ?> .woocommerce li.product:hover .entry-wrap::before {
  background-color: <?php echo $product_bg_color; ?>;
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel:not(.eacs-product-no-style) <?php echo '#'.$product_grid_id; ?> .woocommerce li.product .entry-header .button {
    background-color: <?php echo $cart_bg_color; ?>;
    border-color: <?php echo $cart_border_color; ?>;
    color: <?php echo $cart_text_color; ?>;
}

</style>


