<div class="wrap">

	<h1><?= WPPP_PLUGIN_NAME ?> <small> &mdash; View Log</small></h1>

	<hr/>

	<span>
	<a href="<?php echo admin_url( 'admin-post.php?action=wppp_print_logs' );?>">
		Download logs
	</a>
	</span>

	<hr/>

	<?php
		$all_sizes = wppp_get_image_sizes();

		global $wpdb;

		$table_name = $wpdb->prefix . WPPP_LOG_TABLE_NAME;

		$query = 
			"
			SELECT * 
			FROM $table_name
			ORDER BY id DESC
			LIMIT 500
			";

		$all_log = $wpdb->get_results( 
			$query,
			OBJECT
		);

	?>

	<table class="wppp_log_table">
		<thead>
			<th>ID</th>
			<th>Time</th>
			<th>Attachment ID</th>
			<th>File name</th>
			<th>Size name</th>
			<th>Step</th>
			<th>Level</th>
			<th>Message</th>
		</thead>
		<tbody>
			<?php foreach ( $all_log as $log_record ) { ?>

				<tr class="<?php echo $log_record->level;?>">
					<td class="id"><?php echo $log_record->id;?></td>
					<td class="time"><?php echo $log_record->time;?></td>
					<td class="attachment_id"><?php echo $log_record->attachment_id;?></td>
					<td class="file_name"><?php echo $log_record->file_name;?></td>
					<td class="size_name"><?php echo $log_record->size_name;?></td>
					<td class="step"><?php echo $log_record->step;?></td>
					<td class="level"><?php echo $log_record->level;?></td>
					<td class="message"><?php echo $log_record->message;?></td>
				</tr>

			<?php } // end of foreach ?>
		</tbody>
	</table>


</div>


