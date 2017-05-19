<div class="wrap">

	<h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; Convert All Images</small></h1>

	<hr/>

	<?php

	$action = sanitize_text_field( $_GET["action"] );
    wppp_log_trace('sanitized action: ' . $action,0,'','','convert-all' );
	if ( isset( $action ) && ( 'convert' == $action ) ) {

		$total_todo = sanitize_text_field( $_GET["total_todo"] );
        wppp_log_trace('sanitized total_todo: ' . $total_todo,0,'','','convert-all' );
        if ( isset( $total_todo ) ) {

            // safe
		    $total_todo = intval( $total_todo );
		    if ( $total_todo <= 0 ){
                wppp_log_error('sanitized total_todo <= 0: ' . $total_todo,0,'','','convert-all' );
                die();
            }

			/* 
			kind of initialize section 
			*/
			$total_done = sanitize_text_field( $_GET["total_done"] );
            wppp_log_trace('sanitized total_done: ' . $total_done,0,'','','convert-all' );
			if ( ! isset( $total_done ) || ( ! $total_done ) ) {
				$total_done = 0;

				// Initialize
                wppp_log_info('Convert All started',0,'','','convert-all' );
                wppp_init_convert_all();
			} else {
			    // safe
                $total_done = intval( $total_done );
                if ($total_done < 0){
                    wppp_log_error('sanitized total_done < 0: ' . $total_done,0,'','','convert-all' );
                    die();
                }
            }

            if ($total_done > $total_todo){
                wppp_log_error('sanitized total_done > total_todo',0,'','','convert-all' );
			    die();
            }

			$total_size_raw = sanitize_text_field($_GET["total_size_raw"]);
			if ( ! isset( $total_size_raw ) || ( ! $total_size_raw ) ) {
				$total_size_raw = 0;
			} else {
                // safe
                $total_size_raw = intval($total_size_raw);
                if ($total_size_raw < 0){
                    wppp_log_error('sanitized total_size_raw < 0: ' . $total_size_raw,0,'','','convert-all' );
                    die();
                }
            }
			
			$total_size_comp = sanitize_text_field($_GET["total_size_comp"]);
			if ( ! isset( $total_size_comp ) || ( ! $total_size_comp ) ) {
				$total_size_comp = 0;
			} else {
                // safe
                $total_size_comp = intval($total_size_comp);
                if ($total_size_comp < 0){
                    wppp_log_error('sanitized total_size_comp < 0: ' . $total_size_comp,0,'','','convert-all' );
                    die();
                }
            }

			// Process
			$image_id = wppp_convert_all_pop();

			wppp_log_trace(
				'Convert All - processing next image: ' . $image_id,
				0,'','','convert-all'
				);				

			if ( isset( $image_id ) && ( $image_id != -1 ) ) {

				$meta = wp_get_attachment_metadata( $image_id );

				// if it was already compressed but not in our table
				$pixpie_compressed = $meta['pixpie_compressed'];
				if ( isset( $pixpie_compressed ) && ( $pixpie_compressed == true ) ) {
					$filename = $meta['file'];
					$size_before = $meta['pixpie_raw_sizes'];
					$size_after = $meta['pixpie_compressed_sizes'];
					wppp_log_warning(
						'image not present in converted images, but has pixpie_compressed true, just add to converted images',
						$image_id,'','','convert-all'
						);
					wppp_add_image($image_id,$filename,$size_before,$size_after);
				} else {
					$meta = wppp_generate_compressed_images( $meta );
					wp_update_attachment_metadata( $image_id, $meta );
				}

				// Recalculate
				$total_done = $total_done + 1;

				$filesizes_before = $meta['pixpie_raw_sizes'];
				$total_size_raw = $total_size_raw + $filesizes_before;

				$filesizes_after = $meta['pixpie_compressed_sizes'];
				$total_size_comp = $total_size_comp + $filesizes_after;

				$total_size_raw_display = wppp_get_display_file_size( $total_size_raw );
                $total_size_comp_display = wppp_get_display_file_size( $total_size_comp );

				if ( $total_size_raw > 0 ) {

                    $size_reduced = 100 - ( $total_size_comp * 100 / $total_size_raw );
                    $size_reduced = number_format( (float) $size_reduced, 2, '.', '' );

				}

				?>


					<div class="wp-pixpie-plugin-convert-all__statistics">
						Processed images: <?php echo $total_done ?> / <?php echo $total_todo ?>
					</div>

					<div class="wp-pixpie-plugin-convert-all__statistics">
						Size before: <?php echo $total_size_raw_display ?> / Size after: <?php echo $total_size_comp_display ?>
					</div>

					<?php
						if ( $total_size_raw > 0 ) {
					?>
					<div class="wp-pixpie-plugin-convert-all__statistics">
						Size reduced by: <?php echo $size_reduced ?>%
					</div>
					<?php
						}
					?>

					<?php 
						if ( $total_done < $total_todo ) {
					?>

						<div class="link convert-all-link-wrapper">
							<a id="convert-all-link"
							href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_CONVERT_ALL ); ?>&action=convert&total_todo=<?php echo $total_todo ?>&total_done=<?php echo $total_done ?>&total_size_raw=<?php echo $total_size_raw ?>&total_size_comp=<?php echo $total_size_comp ?>">Next</a>
						</div>

						<div>
							<b>Performing converting, please do not close this window until done</b>
						</div>

					<?php
						} else {
					?>

						<div>
						<b>Done!</b>
						</div>

					<?php
						} 
					?>

				<?php

			} else {
                wppp_log_error('image_id not set or incorrect',0,'','','convert-all' );
                die();
            }

		} else {
            wppp_log_error('total_todo not set or incorrect',0,'','','convert-all' );
			die();
		}

	?>


	<?php

	} else {

	$images_to_convert = wppp_get_all_images_to_convert();
	$unprocessed_count = count( $images_to_convert );

	?>

		<p>You have <b><?php echo $unprocessed_count ?></b> unprocessed image(s)</p>

        <?php if ( 0 < $unprocessed_count ) { ?>

            <p style="color: red; font-weight: bold;">
                The button below will start conversion of all images, that are uploaded in media library. Be careful, it can take a lot of time to process all images.
            </p>

            <?php if ( wppp_is_plugin_activated() ){ ?>

                <a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_CONVERT_ALL ); ?>&action=convert&total_todo=<?php echo $unprocessed_count ?>" class="button-primary">Convert all existing images</a>

            <?php } else { ?>

                <b>Plese set up the plugin settings first.</b>

            <?php } ?>

        <?php } // end if > 0 ?>

	<?php

	}


	?>



</div>


