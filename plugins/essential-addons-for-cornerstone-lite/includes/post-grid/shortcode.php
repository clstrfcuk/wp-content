<?php

/**
 * Shortcode handler : Post Grid
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

// Preset Styles
switch ( $post_grid_column ) {
  case 'eacs-col-1':
    $post_grid_column = 'eacs-col-1';
    break;

  case 'eacs-col-2':
    $post_grid_column = 'eacs-col-2';
    break;

  case 'eacs-col-3':
    $post_grid_column = 'eacs-col-3';
    break;

  default: // none
    $post_grid_column  = 'eacs-col-4';
    break;
}


$hide_post_meta = ( ($hide_post_meta   == 1) ? "hide-post-meta" : "" );

// Class, ID, Styles
$post_grid_id = "eacs-post-grid-".$randnum;
$post_grid_class      = "eacs-post-grid" ." ". $post_alignment . " " . $hide_post_meta . " ". $post_grid_column . " " . $class ;

// Toggle
$show_excerpt = ( ($show_excerpt   == 1) ? "true" : "false" );
$hide_featured_image = ( ($hide_featured_image   == 1) ? "true" : "false" );
$meta_position = ( ($meta_position   == 'entry-footer') ? "entry-footer" : "entry-header" );

?>


<div id="<?= $id ?>" class="<?= $post_grid_class ?>" style="<?= $style ?>" >
  <div id="<?= $post_grid_id ?>">
      <div class="<?php echo $post_grid_class; ?>">
  		<?php echo do_shortcode("[eacs_post_grid type=\"$post_type\" count=\"$max_post_count\" excerpt_length=\"$excerpt_length\"  offset=\"$offset\" category=\"$category\" show_excerpt=\"$show_excerpt\" meta_position=\"$meta_position\" no_sticky=\"true\" no_image=\"$hide_featured_image\"]") ?>
    </div>
	</div>


<script type="text/javascript">

(function ($) {
    'use strict';

  $(window).load(function(){

    $('.eacs-post-grid').masonry({
      itemSelector: '.eacs-grid-post',
      percentPosition: true,
      columnWidth: '.eacs-post-grid-column'
    });

  });
    
}(jQuery));

</script>
</div>


<style type="text/css">


<?php echo '#'.$post_grid_id; ?> .eacs-grid-post-holder {
    background-color: <?php echo $post_background_color; ?>;
}
<?php echo '#'.$post_grid_id; ?> .eacs-entry-overlay {
    background-color: <?php echo $thumbnail_overlay_color; ?>;
}

<?php echo '#'.$post_grid_id; ?> .eacs-grid-post {
    padding: <?php echo $item_spacing; ?>;
}

<?php echo '#'.$post_grid_id; ?> .eacs-entry-title, <?php echo '#'.$post_grid_id; ?> .eacs-entry-title a {
    color: <?php echo $post_title_color; ?>;
    font-size: <?php echo $post_title_font_size; ?>px;
}
<?php echo '#'.$post_grid_id; ?> .eacs-entry-title:hover, <?php echo '#'.$post_grid_id; ?> .eacs-entry-title a:hover {
    color: <?php echo $post_title_hover_color; ?>;
}

<?php echo '#'.$post_grid_id; ?> .eacs-grid-post-excerpt p {
    color: <?php echo $post_excerpt_color; ?>;
}
<?php echo '#'.$post_grid_id; ?> .eacs-entry-meta, <?php echo '#'.$post_grid_id; ?> .eacs-entry-meta a {
    color: <?php echo $post_meta_color; ?>;
}

</style>


