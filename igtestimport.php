<?php
/*
Plugin Name:  IG Test Import
Plugin URI:   NA
Description:  NA
Version:      0.0.1
Author:       NA 
Author URI:   NA
License:      NA
License URI:  NA
Text Domain:  igti
*/

function igti_register_options_page() {
	add_options_page('Insta Import', 'Insta Import', 'manage_options', 'igti', 'igti_register_options_page_markup');
}
add_action('admin_menu', 'igti_register_options_page');

function get_local_file_contents($file_path) {
	ob_start();
	include $file_path;
	$contents = ob_get_clean();
	return $contents;
}

function fileExtension($name) {
	$n = strrpos($name, '.');
	return ($n === false) ? '' : substr($name, $n + 1);
}

function run_data($run_import) {
	$dir_path = plugin_dir_path(__FILE__);
	$dir_url = plugin_dir_url(__FILE__);
	$json_filename = 'posts_1.json';
	$full_path = $dir_path . $json_filename;

	if ($run_import) {
		echo ('<h3>Run import.</h3>');
	} else {
		echo ('<h3>Run test.</h3>');
	}



	if (!file_exists($full_path)) {
		echo ('<p>JSON file not found.</p>');
	} else {
		echo ('<p>JSON file found.</p>');
		$json_contents = get_local_file_contents($full_path);

		if ($json_contents) {
			$posts = json_decode($json_contents, TRUE);
			$posts_count = count($posts);
			$valid_posts_count = 0;

			echo ('<h3>Post entries: ' . $posts_count . '</h3>');

			ob_start();
			foreach ($posts as $key => $post) {
				$post_title = null;
				$post_date = null;
				$post_date_formatted = null;
				$post_date_forWP = null;
				$initial_media_count = null;
				$valid_media_count = null;
				$valid_images_count = 0;
				$valid_video_count = 0;

				if (isset($post['media'])) {
					$media = $post['media'];
					$initial_media_count = count($media);

					// Remove media without a valid extension.
					$valid_extensions = ['jpg', 'webp', 'mp4'];
					foreach ($media as $key => $m) {
						$media_path = $dir_url . $m['uri'];
						if (!in_array(fileExtension($media_path), $valid_extensions)) {
							array_splice($media, $key, 1);
						}
					}
					$valid_media_count = count($media);

					if ($valid_media_count > 0) {
						$valid_posts_count++;
					}

					echo ('<div class="post-set">');
					/* Some posts have timestamp & title at root, while others are nested within a media item. */

					// TIMESTAMP.
					if (isset($post['creation_timestamp'])) {
						if ($post['creation_timestamp']) {
							$post_date = $post['creation_timestamp'];
						}
					} else {
						foreach ($media as $m) {
							if (isset($m['creation_timestamp'])) {
								$post_date = $m['creation_timestamp'];
							}
						}
					}
					$post_date_formatted = date('F j Y h:i:s A', $post_date);
					$post_date_forWP = date('Y-m-d H:i:s', $post_date);


					// TITLE.
					if (isset($post['title'])) {
						if ($post['title']) {
							$post_title = $post['title'];
						}
					} else {
						if (isset($post['media'])) {
							foreach ($media as $m) {
								if (isset($m['title'])) {
									$post_title = $m['title'];
								}
							}
						}
					}


					echo ('<h4>Title: ' . wp_specialchars_decode(nl2br($post_title)) . '</h4>');

					echo ('<h4>Date - Unformatted: ' . $post_date . '</h4>');
					echo ('<h4>Date - Formatted: ' . $post_date_formatted . '</h4>');
					echo ('<h4>Date - For WP: ' . $post_date_forWP . '</h4>');

					echo ('<p>Initial Media count: ' . $initial_media_count . '</p>');
					echo ('<p>Valid Media count: ' . $valid_media_count . '</p>');
					if ($initial_media_count !== $valid_media_count) {
						echo ('<p>Media count discrepancy.</p>');
					}

					echo ('<ul class="media-list"> ');
					foreach ($media as $m) {
						echo ('<li>' . $m['uri'] . '</li>');
					}
					echo ('</ul> ');

					echo ('<div class="media">');
					foreach ($media as $m) {
						$media_path = $dir_url . $m['uri'];
						if (fileExtension($media_path) == 'jpg' || fileExtension($media_path) == 'webp') {
							echo ('<img src="' . $media_path . '" />');
							$valid_images_count++;
						}
						if (fileExtension($media_path) == 'mp4') {
							echo ('<video src="' . $media_path . '" controls></video>');
							$valid_video_count++;
						}
					}
					echo ('</div>');

					echo ('<p>Valid Images count: ' . $valid_images_count . '</p>');
					echo ('<p>Valid Video count: ' . $valid_video_count . '</p>');
					if ($valid_images_count > 0 && $valid_video_count > 0) {
						echo ('<p>Has mixed media.</p>');
					}

					echo ('<p>JSON:</p>');
					echo ('<textarea>' . json_encode($post) . '</textarea>');
					echo ('</div>'); //.post-set




					if ($run_import) {
						if ($valid_media_count > 0 && isset($post_date)) {

							ob_start();
?>
							<!-- wp:quote -->
							<blockquote class="wp-block-quote"><!-- wp:paragraph -->
								<p><?php echo $post_title; ?></p>
								<!-- /wp:paragraph -->
							</blockquote>
							<!-- /wp:quote -->

							<?php
							foreach ($media as $m) {
								$media_path = $dir_url . $m['uri'];
								if (fileExtension($media_path) == 'jpg' || fileExtension($media_path) == 'webp') {
								?>
									<!-- wp:image -->
									<figure class="wp-block-image"><img src="<?php echo $media_path; ?>" alt="" /></figure>
									<!-- /wp:image -->
								<?php
								}
								if (fileExtension($media_path) == 'mp4') {
								?>
									<!-- wp:video -->
									<figure class="wp-block-video"><video controls src="<?php echo $media_path; ?>"></video></figure>
									<!-- /wp:video -->
								<?php
								}
							}

							$post_content = ob_get_clean();

							$post_atts = array(
								'post_type' => 'post',
								'post_status' => 'publish',
								'id' => $post_date,
								'post_date' => $post_date_forWP,
								'post_title' => 'IG Post - ' . $post_date_formatted,
								'post_content' => $post_content
							);

							wp_insert_post($post_atts);
						}
					}
				} // if post media.
			} // foreach post.

			$content = ob_get_clean();

			echo ('<h3>Valid post entries: ' . $valid_posts_count . '</h3>');

			echo $content;
		} // if JSON contents.
	} // if file exists.

} // function run_data()

function igti_register_options_page_markup() {
	// URI to this options page.
	$igti_uri = $_SERVER['REQUEST_URI'];
	?>
	<style>
		textarea {
			width: 100%;
		}

		.post-set {
			border: 1px solid #666;
			margin-bottom: 2em;
			padding: 0 1em 1em 1em;
		}

		.media-list {
			list-style: disc;
			margin-left: 1em;
		}

		.media img,
		.media video {
			width: 200px;
			height: 200px;
			outline: 1px solid #f1f1f1;
			margin-right: 1em;
		}

		.igsubmit {
			border-radius: 6px;
			border-width: 1px;
			padding: 3px 8px;
			cursor: pointer;
		}

		.igsubmit.test {
			background-color: #97FFB6;
		}

		.igsubmit.import {
			background-color: #F98282;
		}

		.igsubmit:hover {
			background-color: #FFF;
		}
	</style>

	<section id="igti_status">
		<h2>IG Import:</h2>
	</section>

	<form action="<?php echo $igti_uri ?>" method="post">
		<input type="submit" value="Test Data" name="submit_data" class="igsubmit test">
		<input type="submit" value="Run Import" name="submit_data" class="igsubmit import">
	</form>

	<?php
	if (!empty($_POST)) {
		// echo( '<p>_POST:</p>' );
		// echo( '<textarea>' . json_encode( $_POST ) . '</textarea>' );

		if (isset($_POST['submit_data'])) {
			// Run test.
			if ($_POST['submit_data'] == "Test Data") {
				run_data(false);
			}
			// Run import.
			if ($_POST['submit_data'] == "Run Import") {
				run_data(true);
			}
		}
	}
	?>

<?php
} // function igti_register_options_page_markup()