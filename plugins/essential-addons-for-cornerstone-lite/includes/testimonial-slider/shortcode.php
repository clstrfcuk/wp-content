<?php
/**
 * Shortcode: Testimonial Slider
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


// Class, ID, Styles
$testimonial_id = "eacs-testimonial-slider-".$randnum;
$class       = "eacs-testimonial-slider " . $slide_alignment . " " . $add_border . " " . $nav_position . " " . $class ;

// Toggle
$auto_play   = ( ($auto_play   == 1) ? "true" : "false" );
$loop        = ( ($loop == 1) ? "true" : "false" );
$pause_hover = ( ($pause_hover == 1) ? "true" : "false" );
$draggable   = ( ($draggable == 1) ? "true" : "false" );
$variable_width   = ( ($variable_width == 1) ? "true" : "false" );


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



/*
 * => ELEMENT HTML
 * ---------------------------------------------------------------------------*/
?>

<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => $style ) ); ?>>
  <div id="<?= $testimonial_id ?>">
    <?php echo do_shortcode( $content ); ?>
  </div>
</div>


<script type="text/javascript">

  jQuery(document).ready(function($) {
    $("<?= '#'.$testimonial_id ?>").slick({
      autoplay: <?= $auto_play ?>,
      infinite: <?= $loop ?>,
      slidesToShow: <?= $max_visible_items ?>,
      slidesToScroll: <?= $slide_to_scroll ?>,
      arrows: <?= $nav ?>,
      dots: <?= $dots ?>,
      pauseOnHover: <?= $pause_hover ?>,
      draggable: <?= $draggable ?>,
      variableWidth: <?= $variable_width ?>,
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

.eacs-testimonial-slider.slide-border-enabled <?php echo '#'.$testimonial_id; ?> .eacs-testimonial-item {
  border: <?= $logo_border_width ?>px solid <?= $logo_border_color?>;
  margin: <?php echo $slide_spacing; ?>;
}

.eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .eacs-testimonial-item {
  margin: <?php echo $slide_spacing; ?>;
}

.eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-prev::before, .eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-next::before {
  color: <?php echo $slide_nav_color; ?>;
}

.eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-dots li button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

.eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-dots li.slick-active button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

.eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-prev, .eacs-testimonial-slider <?php echo '#'.$testimonial_id; ?> .slick-next {
  background-color: <?php echo $slide_nav_bg_color; ?>;
}

</style>

