<?php
function handle_local_attachment($local_media_url) {

	// Temporarily storing the media in WP.
	$tmp = download_url($local_media_url);

	$file_info = array(
		'name' => basename($local_media_url),
		'tmp_name' => $tmp
	);

	// Check for download errors. If there are error unlink the temp file name.
	if (is_wp_error($tmp)) {
		@unlink($file_info['tmp_name']);
		return $tmp;
	}

	// $post_id set to '0' to not attach it to any particular post.
	$post_id = 0;

	$id = media_handle_sideload($file_info, $post_id);

	// Check for upload errors. If there are error unlink the temp file name.
	if (is_wp_error($id)) {
		@unlink($file_info['tmp_name']);
		return $id;
	}

	return $id;
}


function igi_run_import() {

	$data = igi_get_data();

	if ($data && $data['post_count_valid'] > 0) :
		foreach ($data['posts'] as $key => $post) :
			ob_start();
			?>

			<!-- wp:quote -->
			<blockquote class="wp-block-quote"><!-- wp:paragraph -->
				<p><?php echo $post['caption']; ?></p>
				<!-- /wp:paragraph -->
			</blockquote>
			<!-- /wp:quote -->

			<!-- wp:group {"className":"has-#-items"} -->
			<div class="wp-block-group has-<?php echo $post['media_count_valid']; ?>-items">

				<?php
				foreach ($post['media'] as $m) {
					$new_media_id = handle_local_attachment($m);
					$new_media_url = wp_get_attachment_url($new_media_id);
					if (igi_get_file_extension($m) == 'jpg' || igi_get_file_extension($m) == 'webp') {
				?>
						<!-- wp:image {"id":<?php echo $new_media_id; ?>} -->
						<figure class="wp-block-image"><img src="<?php echo $new_media_url; ?>" class="wp-image-<?php echo $new_media_id; ?>" /></figure>
						<!-- /wp:image -->
					<?php
					} // images 

					if (igi_get_file_extension($m) == 'mp4') {
					?>
						<!-- wp:video {"id":<?php echo $new_media_id; ?>} -->
						<figure class="wp-block-video"><video controls src="<?php echo $new_media_url; ?>"></video></figure>
						<!-- /wp:video -->
				<?php
					} // video
				} // foreach media
				?>

			</div>
			<!-- /wp:group -->

			<?php
			$post_content = ob_get_clean();

			$post_atts = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'post_date' => $post['date_forWP'],
				'post_title' => $post['title'],
				'post_content' => $post_content,
			);

			$new_post_id = wp_insert_post($post_atts);

		endforeach; // each $data['posts']

		echo ('<p class="igi-status-note status success"><strong>Success</strong>: Import comepleted successfully.</p>');

	else : // if $data
		echo ('<p class="igi-status-note error"><strong>Error</strong>: Import unable to run.</p>');
	endif; // if $data

} // igi_run_import()