<?php

/**
 * Element Definition: "Post Carousel"
 */

class EACS_Post_Carousel {

	public function ui() {
		return array(
        'name'        => 'eacs-post-carousel',
     		'title' => __( 'EA Post Carousel', 'essential-addons-cs' ),
        'icon_group' => 'essential-addons-cs',
        'icon_id' => 'eacs-post-carousel',
    );
	}

  public function flags() {
    // dynamic_child allows child elements to render individually, but may cause
    // styling or behavioral issues in the page builder depending on how your
    // shortcodes work. If you have trouble with element presentation, try
    // removing this flag.
    return array(
      'dynamic_child' => false
    );
  }

}


// Recent Post with excerpt
// ================================


function eacs_post_carousel( $atts ) {
  extract( shortcode_atts( array(
    'id'           => '',
    'class'        => '',
    'style'        => '',
    'type'         => 'post',
    'count'        => '',
    'category'     => '',
    'offset'       => '',
    'orientation'  => '',
    'show_excerpt' => 'true',
    'excerpt_length' => '',
    'no_sticky'    => '',
    'no_image'     => '',
    'fade'         => '',
    'meta_position' => ''
  ), $atts, 'eacs_post_carousel' ) );

  $allowed_post_types = apply_filters( 'cs_recent_posts_post_types', array( 'post' => 'post' ) );
  $type = ( isset( $allowed_post_types[$type] ) ) ? $allowed_post_types[$type] : 'post';

  $id            = ( $id           != ''     ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class         = ( $class        != ''     ) ? 'eacs-post-grid row ' . esc_attr( $class ) : 'eacs-post-grid row';
  $style         = ( $style        != ''     ) ? 'style="' . $style . '"' : '';
  $count         = ( $count        != ''     ) ? $count : 9999;
  $category      = ( $category     != ''     ) ? $category : '';
  $category_type = ( $type         == 'post' ) ? 'category_name' : 'portfolio-category';
  $offset        = ( $offset       != ''     ) ? $offset : 0;
  $orientation   = ( $orientation  != ''     ) ? ' ' . $orientation : ' horizontal clearfix';
  $show_excerpt  = ( $show_excerpt == 'true' );
  $excerpt_length   = ( $excerpt_length      != ''     ) ? $excerpt_length : 50;
  $no_sticky     = ( $no_sticky    == 'true' );
  $no_image      = ( $no_image     == 'true' ) ? $no_image : '';
  $fade          = ( $fade         == 'true' ) ? $fade : 'false';
  $meta_position = ( ($meta_position   == 'entry-footer') ? "entry-footer" : "entry-header" );
  $entry_header = '';
  $entry_footer = '';

  $js_params = array(
    'fade' => ( $fade == 'true' )
  );

  $data = cs_generate_data_attributes( 'recent_posts', $js_params );

  $output = "<div {$id} class=\"{$class}{$orientation}\" {$style} {$data} data-fade=\"{$fade}\" >";

    $q = new WP_Query( array(
      'orderby'             => 'date',
      'post_type'           => "{$type}",
      'posts_per_page'      => "{$count}",
      'offset'              => "{$offset}",
      "{$category_type}"    => "{$category}",
      'ignore_sticky_posts' => $no_sticky
    ) );

    if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();

      if ( $no_image == 'true' ) {
        $entry_media        = '';
      } else {
        $image              = wp_get_attachment_image_src( get_post_thumbnail_id(), 'entry-cropped' );
        $entry_media        = ( $image[0] != '' ) ? '<div class="eacs-entry-media">'
                        . '<div class="eacs-entry-overlay">'
                          . '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>'
                          . '<a href="' . get_permalink( get_the_ID() ) . '"></a>'
                        . '</div>'
                          .  '<div class="eacs-entry-thumbnail">'
                             . '<img src=' . $image[0] . '>'
                           . '</div>'
                      . '</div>' : '';
      }

        $trimmed_excerpt =  wp_trim_words( cs_get_raw_excerpt(), $excerpt_length, '...' );    
        $excerpt = ( $show_excerpt ) ? '<div class="eacs-grid-post-excerpt"><p>' . preg_replace('/<a.*?more-link.*?<\/a>/', '', $trimmed_excerpt ) . '</p></div>' : '';



      if ( $meta_position == 'entry-header' ) : {
        $entry_header ='<div class="eacs-entry-meta">'
          . '<span class="eacs-posted-by">'
          . '<a href="'. get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ). '">'. get_the_author_meta( 'display_name' ) .'</a>' 
          .'</span>'
          . '<span class="eacs-posted-on"><time datetime="'. get_the_date() .'">'. get_the_date() .'</time></span>'
        . '</div>';
      } else: {
        $entry_footer = '<div class="eacs-entry-footer">'
          . '<div class="eacs-author-avatar">'
            . '<a href="' .  get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ). '">'
            . get_avatar( get_the_author_meta( 'ID' ), 96 )
            .'</a>'
          . '</div>'
          . '<div class="eacs-entry-meta">'
            . '<div class="eacs-posted-by">'
            .'<a href="'. get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ). '">'. get_the_author_meta( 'display_name' ) .'</a>'
            . '</div>'
            . '<div class="eacs-posted-on"><time datetime="'. get_the_date() .'">'. get_the_date() .'</time></div>'
          . '</div>'
        . '</div>';
      } endif;

      $output .= '<article id="post-' . get_the_ID() . '" class="eacs-grid-post eacs-post-grid-column">'
                  .'<div class="eacs-grid-post-holder">'
                   . '<div class="eacs-grid-post-holder-inner">'

                             .$entry_media

                        . '<div class="eacs-entry-wrapper">'
                         . '<header class="eacs-entry-header">'

                            . '<h2 class="eacs-entry-title"><a class="eacs-grid-post-link" href="' . get_permalink( get_the_ID() ) . '" title="'. get_the_title() .'">'. get_the_title() .'</a></h2>'

                             .$entry_header

                          . '</header>'

                          . '<div class="eacs-entry-content">'

                            . $excerpt 

                          . '</div>'
                        . '</div>'

                            .$entry_footer
                   . '</div>'
                  . '</div>'
               . '</article>';

    endwhile; endif; wp_reset_postdata();

  $output .= '</div>';

  return $output;
}

add_shortcode( 'eacs_post_carousel', 'eacs_post_carousel' );
