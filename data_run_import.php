<?php
function igi_run_import() {

	$data = igi_get_data();


	if ($data && $data['post_count_valid'] > 0) :
		foreach ($data['posts'] as $post) :
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
					if (igi_get_file_extension($m) == 'jpg' || igi_get_file_extension($m) == 'webp') {
				?>
						<!-- wp:image -->
						<figure class="wp-block-image"><img src="<?php echo $m; ?>" alt="" /></figure>
						<!-- /wp:image -->
					<?php
					} // images 

					if (igi_get_file_extension($m) == 'mp4') {
					?>
						<!-- wp:video -->
						<figure class="wp-block-video"><video controls src="<?php echo $m; ?>"></video></figure>
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

			wp_insert_post($post_atts);
		endforeach; // each $data['posts']
	endif; // if $data
} // igi_run_import()