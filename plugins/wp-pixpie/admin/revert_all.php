<div class="wrap">

	<h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; Revert All Images</small></h1>

	<hr/>



	<?php
	$action = $_GET["action"];
	if (isset($action) && ('revert' == $action)){

		wppp_log_info('Revert All started',0,'','','revert-all' );

		$compressed = wppp_get_all_images_ids();
		$comp_count = count( $compressed );
		wppp_log_trace('Revert All - ' . $comp_count . ' images to revert',0,'','','revert-all'
			);				

		foreach ( $compressed as $image_id ) {
			wppp_revert_image( $image_id );
		}
		wppp_log_info ('Revert All finished',0,'','','revert-all');

		?>
		<p>Done! All images reverted.</p>
		<?php

	} else {

		$compressed = wppp_get_all_images();
		$comp_count = count( $compressed );

		?>
			<p>You have <?php echo $comp_count; ?> compressed images</p>

			<p>You can <b>roll back</b> your images to uncompressed original versions.</p>

			<p>All compressed images will be deleted.</p>

			<?php

				if ( wppp_is_plugin_activated() ) {

				?>

					<a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_REVERT_ALL ); ?>&action=revert" class="button-primary">Revert all images
					</a>


				<?php

				} else {
			
			?>

			<hr/>

			<b>Plese set up the plugin settings first.</b>

			<?php

				} 
			
			?>

		<?php

	}
	?>

</div>
