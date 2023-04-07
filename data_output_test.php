<?php
function igi_output_test_data() {
	$data = igi_get_data();
?>

	<section class="igi-import-data">
		<ul>
			<li><strong>Processed data</strong>:
				<pre> <?php print_r($data); ?></pre>
			</li>
			<li><strong>Post Count - Initial</strong>: <?php print_r($data['post_count_initial']); ?></li>
			<li><strong>Post Count - Valid</strong>: <?php print_r($data['post_count_valid']); ?></li>
			<li><strong>Posts</strong>:
				<div class="posts-list">
					<?php foreach ($data['posts'] as $post) : ?>
						<ul class="post">
							<li><strong>Title</strong>: <?php echo $post['title']; ?> </li>
							<li><strong>Date - Display</strong>: <?php echo $post['date_display']; ?> </li>
							<li><strong>Date - For WP</strong>: <?php echo $post['date_forWP']; ?> </li>
							<li><strong>Date - Unformatted</strong>: <?php echo $post['date']; ?> </li>
							<li><strong>Caption</strong>: <?php echo $post['caption']; ?> </li>

							<li><strong>Initial Media Count</strong>: <?php echo $post['media_count_initial']; ?> </li>
							<li><strong>Valid Media Count</strong>: <?php echo $post['media_count_valid']; ?> </li>
							<?php if ($post['media_count_diff']) : ?>
								<li class="igi-status-note error"><strong>Media count discrepancy.</strong></li>
							<?php endif; ?>

							<li><strong>Valid Images Count</strong>: <?php echo $post['valid_images_count']; ?> </li>
							<li><strong>Valid Videos Count</strong>: <?php echo $post['valid_videos_count']; ?> </li>
							<?php if ($post['has_mixed_media']) : ?>
								<li class="igi-status-note success"><strong>Has mixed media.</strong></li>
							<?php endif; ?>

							<li class="media-urls"><strong>Media URLs</strong>:
								<ul>
									<?php foreach ($post['media'] as $m) : ?>
										<li><?php echo $m; ?></li>
									<?php endforeach; ?>
								</ul>
							</li>

							<li class="media-list"><strong>Media Thumbs</strong>:
								<ul>
									<?php foreach ($post['media'] as $m) : ?>
										<li>
											<?php
											if (igi_get_file_extension($m) == 'jpg' || igi_get_file_extension($m) == 'webp') {
												echo ('<img src="' . $m . '" />');
											}
											if (igi_get_file_extension($m) == 'mp4') {
												echo ('<video src="' . $m . '" controls></video>');
											}
											?>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>

						</ul> <!-- .post -->
					<?php endforeach; ?>
				</div> <!-- .post-list -->
			</li> <!-- "data item" -->
		</ul> <!-- "data items" -->
	</section> <!-- .igi-import-data -->

<?php
}
