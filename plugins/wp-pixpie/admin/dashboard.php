<div class="wrap">

	<h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; Dashboard</small></h1>

	<hr/>

	<?php

			$all_sizes = wppp_get_image_sizes();

			$compressed = wppp_get_all_images();
			$comp_count = count($compressed);

			$total_compressed_images = 0;
			$total_raw_size = 0;
			$total_compressed_size = 0;

			// calculate total for all compressed images
			if ( $comp_count > 0 ) {

				foreach ( $compressed as $comp_image ) {
					$total_compressed_images = $total_compressed_images + 1;
					$total_raw_size = $total_raw_size + $comp_image->size_before;
					$total_compressed_size = $total_compressed_size + $comp_image->size_after;
				}

				if ( $total_compressed_images > 0 ) {

					$total_raw_size_display = wppp_get_display_file_size($total_raw_size);
                    $total_compressed_size_display = wppp_get_display_file_size($total_compressed_size);

					if ( $total_raw_size > 0 ) {					
						$size_saved = 100 - ( $total_compressed_size * 100 / $total_raw_size );
						$size_saved = number_format( (float) $size_saved, 2, '.', '' );
					}

				}

				?>

					<table class="wp-pixpie-plugin-dashboard-stats">

						<tr>
					  <td>Count of compressed images: </td>
					  <td><?php echo $total_compressed_images; ?></td>
						</tr>

					<?php
					if ($total_compressed_images > 0){
						?>

						<tr>
					  <td>Total uncompressed size: </td>
					  <td><?php echo $total_raw_size_display; ?></td>
						</tr>

						<tr>
					  <td>Total compressed size: </td>
					  <td><?php echo $total_compressed_size_display; ?></td>
						</tr>

						<tr>
					  <td>Size reduced by: </td>
					  <td><?php echo $size_saved; ?>%</td>
						</tr>

					<?php
					}
					?>

					</table>


				<?php

			} else {


				?>

				<i>No compressed images</i>

				<?php

			}

			?>

			<hr/>

			<div>
				<span>
					<a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_SETTINGS ); ?>">Settings</a>
				</span>
				<span>|</span>
				<span>
					<a href="<?php echo ( get_admin_url() . 'admin.php?page=' . WPPP_PLUGIN_PAGE_ID_REVERT_ALL ); ?>">Revert All Images</a>
				</span>				
			</div>

			<?php

	?>


</div>


