<?php

/**
 * Shortcode handler : Post Carousel
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
$post_carousel_id = "eacs-post-carousel-".$randnum;
$post_carousel_class      = "eacs-post-carousel" . " " . $add_border . " " . $nav_position . " " . $class ;

// Toggle
$show_excerpt = ( ($show_excerpt   == 1) ? "true" : "false" );
$hide_featured_image = ( ($hide_featured_image   == 1) ? "true" : "false" );
$auto_play   = ( ($auto_play   == 1) ? "true" : "false" );
$loop        = ( ($loop == 1) ? "true" : "false" );
$pause_hover = ( ($pause_hover == 1) ? "true" : "false" );
$draggable   = ( ($draggable == 1) ? "true" : "false" );


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


?>

<div id="<?= $post_carousel_id ?>">
	<div id="<?= $id ?>" class="<?= $post_carousel_class ?>" style="<?= $style ?>" >
		<?php echo do_shortcode("[eacs_post_carousel type=\"$post_type\" count=\"$max_post_count\" offset=\"$offset\" category=\"$category\" show_excerpt=\"$show_excerpt\" no_image=\"$hide_featured_image\"]") ?>
	</div>
</div>


<script type="text/javascript">

  jQuery(document).ready(function($) {
     $("<?= '#'.$post_carousel_id . ' ' . '.x-recent-posts' ?>").slick({
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

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .x-recent-posts a {
  margin: <?php echo $slide_spacing; ?>;
}

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel.slide-border-enabled .x-recent-posts a {
  border: <?= $post_border_width ?>px solid <?= $post_border_color?>;
}

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-prev::before, <?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-next::before {
  color: <?php echo $slide_nav_color; ?>;
}

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-dots li button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-dots li.slick-active button::before {
  color: <?php echo $slide_nav_bg_color; ?>;
}

<?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-prev, <?php echo '#'.$post_carousel_id; ?> .eacs-post-carousel .slick-next {
  background-color: <?php echo $slide_nav_bg_color; ?>;
}


</style>


