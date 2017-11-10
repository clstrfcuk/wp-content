<?php

/**
 * Shortcode definition
 */

// Toggle
$show_avatar   = ( ($show_avatar   == 1) ? "" : "testimonial-avatar-disabled" );
$rounded_avatar   = ( ($rounded_image   == 1) ? "testimonial-avatar-rounded" : "" );

$class = 'eacs-testimonial-item ' . $show_avatar . " " . $rounded_avatar . " " . $class;

?>

<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => "background-color: $slide_bg_color;" . $style ) ); ?>>

	<div class="eacs-testimonial-image">
		<span class="eacs-testimonial-quote" style="color: <?php echo $testimonial_quotation_mark_color ?>"></span>
		<figure>
			<img src="<?php echo $image;?>" style="margin: <?php echo $image_margin; ?>; width: <?php echo $image_width; ?>; border: <?php echo $image_border_width ?>px solid <?php echo $image_border_color?>;" alt="<?php echo $alt_tag;?>">
		</figure>
	</div>

	<div class="eacs-testimonial-content">
		<span class="eacs-testimonial-quote" style="color: <?php echo $testimonial_quotation_mark_color ?>"></span>
		<p class="eacs-testimonial-text"><?php echo $content; ?></p>
		<p class="eacs-testimonial-user" style="color: <?php echo $testimonial_user_text_color;?>"><?php echo $testimonial_user_name; ?></p>
		<p class="eacs-testimonial-user-company" style="color: <?php echo $testimonial_user_company_text_color;?>"><?php echo $testimonial_user_company; ?></p>
	</div>
</div>
