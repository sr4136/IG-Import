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
	wp_enqueue_script('wp-api');
}
add_action('admin_menu', 'igti_register_options_page');


function igti_register_options_page_markup() {
	wp_enqueue_script('wp-api');

?>
	<section id="igti_form">
		<h3>Choose JSON:</h3>
		<form enctype="multipart/form-data">
			<input id="upload" type=file accept="text/json" name="files[]" size=30>
		</form>
	</section>

	<section id="igti_upload">
		<h3>Uploaded input:</h3>
		<textarea class="form-control" rows=5 style="width:95%;" id="igti_input"></textarea>
	</section>

	<script>
		let $ = jQuery;

		function handleFileSelect(evt) {
			let files = evt.target.files; // FileList object

			// use the 1st file from the list
			let f = files[0];

			let reader = new FileReader();

			// Closure to capture the file information.
			reader.onload = (function() {
				return function(e) {
					const the_result = e.target.result;
					$('#igti_input').val(the_result);
					//console.log(the_result);

					const the_json = JSON.parse(the_result);
					//console.log(the_json);
					do_json_stuff(the_json);
				};
			})(f);

			// Read in the image file as a data URL.
			reader.readAsText(f);
		}

		function do_json_stuff(the_json) {

			// Make sure the API is ready.
			wp.api.loadPromise.done(function() {

				let the_post = the_json[0];
				let key = 0;
				console.log(the_post);

				//the_json.forEach(function(the_post, key) {

				// Set the WP post's caption. 
				// Sometimes the caption is in the root of the IG post...
				// ...while others it is attached to a media item.
				let the_caption = null;
				if (the_post.title) {
					the_caption = the_post.title;
					console.table(key, "ROOT Caption: ", the_caption);
				} else {
					the_post.media.forEach(function(a_media) {
						if (a_media.title) {
							the_caption = a_media.title;
							console.table(key, "MEDIA Caption: ", the_caption);
						}
					}); // the_post.forEach
				}

				// Handle media
				the_post.media.forEach(function(a_media) {


					let media_post = new wp.api.models.Media();

					media_post
						.file( a_media.uri )
						.create({
							title: 'My awesome image',
							alt_text: 'an image of something awesome',
							caption: 'This is the caption text',
							description: 'More explanatory information'
						});

					// media_post.save().done(function(media) {
					// 	console.log('🖼 Media Created');
					// 	console.log(media);
					// });

				});





				//}); // the_json.forEach

				/*
				// Use the first item in the post's timestamp as the one for all.
				const taken_at = the_post.media[0].creation_timestamp;
				console.table("Date Raw: ", taken_at);

				const the_date = new Date((taken_at * 1000));
				console.table("Date Converted: ", the_date);

				const the_caption = (the_post.media[0].title) ? the_post.media[0].title : null;
				console.table("Caption: ", the_caption);

				
				// Loop through the media of each post.
				let wp_post_content = ``;

				the_post.media.forEach(function(the_media) {
					const the_media_uri = the_media.uri;
					console.table("Media URI: ", the_media_uri);

					const the_media_type = the_media_uri.split('.').pop();;
					console.table("Media Type: ", the_media_type);


					wp_post_content += `
						<!-- wp:quote -->
						<blockquote class="wp-block-quote">
							<!-- wp:paragraph -->
							<p>${the_caption}</p>
							<!-- /wp:paragraph -->
						</blockquote>
						<!-- /wp:quote -->
					`;
				} );

				



				
				// Publish post.
				let post = new wp.api.models.Post({
					title: 'IG - ' + taken_at + ' - ' + the_caption,
					status: 'publish',
					content: wp_post_content,
					date: the_date
				});

				
				post.save().done(function(post) {
					console.log('🟢 Post Created: "' + post.title.raw + '"');
				});
				*/


			}) // wp.api.loadPromise.done
		} // do_json_stuff

		document.getElementById('upload').addEventListener('change', handleFileSelect, false);
	</script>


<?php

}
