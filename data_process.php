<?php
function igi_get_data() {
	$dir_path = plugin_dir_path(__FILE__) . 'ig_data/';
	$dir_url = plugin_dir_url(__FILE__) . 'ig_data/';
	$json_filename = 'posts_1.json';
	$full_path_to_JSON = $dir_path . $json_filename;

	echo ('<p><strong>Note</strong>: this expects a folder (`ig_data`) at this plugin root, with the data files from the IG export nested directly inside: `posts_1.json` and `media/*`.</p>');

	if (!file_exists($full_path_to_JSON)) {
		echo ('<p class="igi-status-note error"><strong>Error</strong>: JSON file not found at "' . $full_path_to_JSON . '"</p>');
	} else {
		echo ('<p class="igi-status-note status success"><strong>Success</strong>: JSON file found at "' . $full_path_to_JSON . '"</p>');

		$json_contents = igi_get_local_file_contents($full_path_to_JSON);

		if ($json_contents) {

			$final_posts = array(
				'post_count_initial'	=> 0,
				'post_count_valid'     => 0,
				'posts' 				=> [],
			);

			$json_posts = json_decode($json_contents, TRUE);

			$final_posts['post_count_initial'] = count($json_posts);

			foreach ($json_posts as $jp) {

				$post_structure = array(
					'title'					=> '',
					'caption'				=> '',
					'date'					=> null,
					'date_display'			=> null,
					'date_forWP'			=> null,
					'media'					=> [],
					'media_count_initial'	=> 0,
					'media_count_valid'		=> 0,
					'media_count_diff'		=> false,
					'valid_images_count'	=> 0,
					'valid_videos_count'	=> 0,
					'has_mixed_media'		=> false,
				);

				if (isset($jp['media'])) {

					/* MEDIA ITEMS */
					$media = $jp['media'];
					$post_structure['media_count_initial'] = count($media);

					// Set Valid media list.
					$valid_extensions = ['jpg', 'webp', 'mp4'];
					foreach ($media as $key => $m) {
						$media_path = $dir_url . $m['uri'];
						if (in_array(igi_get_file_extension($media_path), $valid_extensions)) {
							$post_structure['media'][] = $media_path;
						}
					}
					$post_structure['media_count_valid'] = count($post_structure['media']);

					if ($post_structure['media_count_valid'] > 0) {

						/* DATE */
						// The date of the post. Sometimes will be set at the root. Otherwise will come from the media loop below.
						if (isset($jp['creation_timestamp'])) {
							if ($jp['creation_timestamp']) {
								$post_structure['date'] = $jp['creation_timestamp'];
							}
						}

						/* CAPTION */
						// The text of the post. Sometimes will be set at the root. Otherwise will come from the media loop below.
						if (isset($jp['title'])) {
							if ($jp['title']) {
								$post_structure['caption'] = $jp['title'];
							}
						}

						/* DATE & CAPTION from MEDIA */
						// Set title if not exists already.
						if (empty($post_structure['caption'])) {
							foreach ($media as $m) {
								if (isset($m['title'])) {
									$post_structure['caption'] = $m['title'];
								}
							}
						}
						// Set date if not exists already.
						if (empty($post_structure['date'])) {
							foreach ($media as $m) {
								if (isset($m['creation_timestamp'])) {
									$post_structure['date'] = $m['creation_timestamp'];
								}
							}
						}

						foreach ($post_structure['media'] as $m) {
							if (igi_get_file_extension($m) == 'jpg' || igi_get_file_extension($m) == 'webp') {
								$post_structure['valid_images_count']++;
							}
							if (igi_get_file_extension($m) == 'mp4 ') {
								$post_structure['valid_videos_count']++;
							}
						}

						/* FINAL DATA TRANSFORMATIONS */
						// Dates.
						$post_structure['date_display'] = date('F j Y h:i:s A', $post_structure['date']);
						$post_structure['date_forWP'] = date('Y-m-d H:i:s', $post_structure['date']);

						// Title & Caption.
						$post_structure['title'] = 'IG Import - ' . $post_structure['date_display'];
						$post_structure['caption'] = cleanString( $post_structure['caption'] );
						if( empty( $post_structure['caption'] ) ){
							$post_structure['caption'] = "(untitled)";
						}

						// Media Difference.
						if ($post_structure['media_count_initial'] !== $post_structure['media_count_valid']) {
							$post_structure['media_count_diff'] = true;
						}

						// Has Mixed Media.
						if ($post_structure['valid_images_count'] > 0 && $post_structure['valid_videos_count'] > 0) {
							$post_structure['has_mixed_media'] = true;
						}

						$final_posts['posts'][] = $post_structure;
					} // media_count_valid > 0
				} // if post media.
			} // foreach post.

			$final_posts['post_count_valid'] = count($final_posts['posts']);

			return $final_posts;
		} // if JSON contents.
	} // if file exists.
} // function igi_get_data()
