<!-- Add from Media Library -->
<a href="#" class="envira-media-library button" title="<?php _e( 'Click Here to Insert from Other Image Sources', 'envira-gallery' ); ?>" style="vertical-align: baseline;">
	<?php _e( 'Select Files from Other Sources', 'envira-gallery' ); ?>
</a>

<!-- Progress Bar -->
<div class="envira-progress-bar">
	<div class="envira-progress-bar-inner"></div>
	<div class="envira-progress-bar-status">
		<span class="uploading">
			<?php _e( 'Uploading Image', 'envira-gallery' ); ?>
			<span class="current">1</span>
			<?php _e( 'of', 'envira-gallery' ); ?>
			<span class="total">3</span>
		</span>

		<span class="done"><?php _e( 'All images uploaded.', 'envira-gallery' ); ?></span>
		<span class="uploading_zip"><?php _e( 'Zip file uploaded.', 'envira-gallery' ); ?></span>
		<span class="opening_zip"><span class="spinner"></span> <?php _e( 'Adding images from Zip file.', 'envira-gallery' ); ?></span>
		<span class="done_zip"><?php _e( 'Zip import complete.', 'envira-gallery' ); ?></span>
	</div>
</div>

<div class="envira-progress-adding-images">
	<div class="envira-progress-status">
		<span class="spinner"></span><span class="adding_images"><?php _e( 'Adding items to gallery.', 'envira-gallery' ); ?></span>
	</div>
</div>