<?php

/**
 * Shortcode definition
 */

$link_target   = ( ($link_target == 1) ? "_blank" : "_self" );

$class = 'eacs-logo-carousel-item ' . $class;
?>

<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => $style ) ); ?>>
	<a href="<?php echo $logo_url;?>" target="<?php echo $link_target;?>"><img src="<?php echo $image;?>" style="padding: <?php echo $image_padding; ?>;" alt="<?php echo $alt_tag;?>"></a>
</div>