<?php

/**
 * Shortcode definition
 */

// Toggle
$rounded_avatar   = ( ($rounded_image   == 1) ? "team-avatar-rounded" : "" );

$class = 'eacs-team-item ' . " " . $rounded_avatar . " " . $class;

?>

<div <?php echo cs_atts( array( 'id' => $id, 'class' => $class, 'style' => $style ) ); ?>>

	<div class="eacs-team-image">
		<figure>
			<img src="<?php echo $image;?>" style="margin: <?php echo $image_margin; ?>; width: <?php echo $image_width; ?>; border: <?php echo $image_border_width ?>px solid <?php echo $image_border_color?>;" alt="<?php echo $alt_tag;?>">
		</figure>
	</div>

	<div class="eacs-team-content" style="background-color: <?php echo $slide_bg_color;?>">
		<h3 class="eacs-team-member-name" style="color: <?php echo $team_member_text_color;?>"><?php echo $team_member_name; ?></h3>
		<h4 class="eacs-team-member-position" style="color: <?php echo $team_member_position_text_color;?>"><?php echo $team_member_position; ?></h4>
		<p class="eacs-team-text"><?php echo $content; ?></p>
	</div>
</div>