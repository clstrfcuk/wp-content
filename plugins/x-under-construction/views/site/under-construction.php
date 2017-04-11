<?php

// =============================================================================
// VIEWS/SITE/UNDER-CONSTRUCTION.PHP
// -----------------------------------------------------------------------------
// Plugin site output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Options
//   02. Output
// =============================================================================

// Require Options
// =============================================================================

require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );



// Output
// =============================================================================

?>

<!DOCTYPE html>
<!--[if IE 9]><html class="no-js ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" <?php language_attributes(); ?>><!--<![endif]-->

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php wp_title(''); ?></title>
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php wp_head(); ?>
</head>

<body>

  <div class="x-under-construction-overlay">
    <div class="x-under-construction-wrap-outer">
      <div class="x-under-construction-wrap-inner">
        <div class="x-under-construction">

          <?php if ( ! empty ( $x_under_construction_logo_image ) ) : ?>
            <div class="x-under-construction-logo">
              <img class="" src="<?php echo esc_attr( $x_under_construction_logo_image ) ?>" alt="logo image" />
            </div>
          <?php endif; ?>

          <h1><?php echo stripslashes( $x_under_construction_heading ); ?></h1>
          <h2><?php echo stripslashes( $x_under_construction_subheading ); ?></h2>
          <p><?php echo do_shortcode( stripslashes( nl2br( $x_under_construction_extra_text ) ) ); ?></p>

          <?php if ( $x_under_construction_date != '' ) : ?>

            <div class="x-under-construction-countdown cf">
              <span class="days">0 Days</span>
              <span class="hours">0 Hours</span>
              <span class="minutes">0 Minutes</span>
              <span class="seconds">0 Seconds</span>
            </div>

            <script type="text/javascript">
              jQuery(document).ready(function($) {
                $('.x-under-construction-countdown').countdown('<?php echo $x_under_construction_date; ?>',
                  function(e) {

                    var $this = $(this);

                    $this.find('.days').text(e.strftime('%-D Days'));
                    $this.find('.hours').text(e.strftime('%-H Hours'));
                    $this.find('.minutes').text(e.strftime('%-M Minutes'));
                    $this.find('.seconds').text(e.strftime('%-S Seconds'));

                  }
                );
              });
            </script>

          <?php endif; ?>

            <?php
            foreach ( $social_medias as $key => $sc ) {
              $key = "x_under_construction_{$key}";
              $url = $$key;
              if ( ! empty ( $url ) ) {
                $social = true;
              }
            }
            ?>
            <?php if ( $social ) : ?><div class="x-under-construction-social"><?php endif; ?>
              <?php foreach ( $social_medias as $key => $sc ) :
                $key = "x_under_construction_{$key}";
                $url = $$key;
                if ( ! empty ( $url ) ) :
              ?>
                <a href="<?php echo $url ?>" class="<?php echo $key ?>" title="<?php echo $sc['title'] ?>" target="_blank"><i class="x-icon-<?php echo str_replace( '_', '-', $key); ?>" data-x-icon="<?php echo $sc['x-icon']; ?>" aria-hidden="true"></i></a>
              <?php
                endif;
              endforeach; ?>
            <?php if ( $social ) : ?></div><?php endif; ?>


        </div>
      </div>
    </div>
  </div>

  <?php wp_footer(); ?>

</body>
</html>
