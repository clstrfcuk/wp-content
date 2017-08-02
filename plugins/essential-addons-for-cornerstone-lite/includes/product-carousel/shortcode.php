<?php

/**
 * Shortcode handler : Product Carousel
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

// Pagination position
switch ( $pagination_position ) {
  case 'nav_top_left':
    $nav_position = 'nav-top-left';
    break;

  case 'nav_top_right':
    $nav_position = 'nav-top-right';
    break;

  default: // NONE
    $nav_position  = 'nav-left-right';
    break;
}


// Toggle
$auto_play   = ( ($auto_play   == 1) ? "true" : "false" );
$loop        = ( ($loop == 1) ? "true" : "false" );
$pause_hover = ( ($pause_hover == 1) ? "true" : "false" );
$draggable   = ( ($draggable == 1) ? "true" : "false" );
$show_rating   = ( ($show_rating == 1) ? "show_rating" : "hide_rating" );


// Pagination type
switch ( $pagination_type ) {
  case 'dots':
    $dots = 'true';
    $nav  = 'false';
    break;

  case 'prev_next':
    $dots = 'false';
    $nav  = 'true';
    break;

  case 'dots_nav':
    $dots = 'true';
    $nav  = 'true';
    break;

  default: // NONE
    $nav  = 'false';
    $dots = 'false';
    break;
}

// Class, ID, Styles
$product_carousel_id = "eacs-product-carousel-".$randnum;
$class      = "eacs-product-carousel" . " " . $add_border  . " " . $preset_style  . " " . $show_rating . " " . $nav_position . " " . $class ;

?>

<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => $style ) ); ?>>
  <div id="<?= $product_carousel_id ?>">
    <?php echo do_shortcode("[recent_products per_page=\"$max_product_count\" category=\"$category\"]") ?>
  </div>
</div>


<script type="text/javascript">

  jQuery(document).ready(function($) {
     $("<?= '#'.$product_carousel_id . ' ' . '.woocommerce .products' ?>").slick({
      autoplay: <?= $auto_play ?>,
      infinite: <?= $loop ?>,
      slidesToShow: <?= $max_visible_items ?>,
      slidesToScroll: <?= $slide_to_scroll ?>,
      arrows: <?= $nav ?>,
      dots: <?= $dots ?>,
      pauseOnHover: <?= $pause_hover ?>,
      draggable: <?= $draggable ?>,
      responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: <?= $max_visible_items_tablet ?>,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: <?= $max_visible_items_mobile ?>,
        slidesToScroll: 1
      }
    }
  ]
    });
  });
</script> 


<style type="text/css">

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product {
  margin: <?php echo $slide_spacing; ?>;
  background-color: <?php echo $product_bg_color; ?>;
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce ul.products li.product h3, .eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce ul.products li.product h3 a, .eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce ul.products li.product .price, .eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce ul.products li.product .price .amount {
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel.slide-border-enabled <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product {
  border: <?= $product_border_width ?>px solid <?= $product_border_color?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .slick-prev::before, <?php echo '#'.$product_carousel_id; ?> .eacs-product-carousel .slick-next::before {
  color: <?php echo $slide_nav_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .slick-dots li button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .slick-dots li.slick-active button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .slick-prev, <?php echo '#'.$product_carousel_id; ?> .eacs-product-carousel .slick-next {
  background-color: <?php echo $slide_nav_bg_color; ?>;
}

.eacs-product-carousel <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product .entry-product, .eacs-product-carousel.eacs-product-reveal <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product .entry-wrap, .eacs-product-carousel.eacs-product-overlay <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product .entry-wrap::before, .eacs-product-carousel.eacs-product-reveal <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product:hover .entry-wrap::before {
  background-color: <?php echo $product_bg_color; ?>;
  color: <?php echo $product_text_color; ?>;
}

.eacs-product-carousel:not(.eacs-product-no-style) <?php echo '#'.$product_carousel_id; ?> .woocommerce li.product .entry-header .button {
    background-color: <?php echo $cart_bg_color; ?>;
    border-color: <?php echo $cart_border_color; ?>;
    color: <?php echo $cart_text_color; ?>;
}


</style>


