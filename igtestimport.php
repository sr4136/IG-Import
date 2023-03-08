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
				const the_first = the_json[0];
				console.table("Data:", the_first);

				const taken_at = the_first.taken_at;
				console.table("Date Raw: ", taken_at);

				const the_date = new Date((taken_at * 1000));
				console.table("Date Converted: ", the_date);

				const the_caption = the_first.caption.text;
				console.table("Caption: ", the_caption);

				const the_images = the_first.carousel_media;
				let the_images_urls = [];
				the_images.forEach(function(img) {
					the_images_urls.push(img.image_versions2.candidates[0].url);
				});
				//
				console.table("Image URLS: ", the_images_urls);

				// ❗❗❗❗ Waiting on the HTML IG export so I can have the imagessss.

				let post_content = `
					<!-- wp:gallery {"linkTo":"none"} -->
					<figure class="wp-block-gallery has-nested-images columns-default is-cropped">
				`;

				post_content += `
						<!-- wp:image {"id":35,"sizeSlug":"large","linkDestination":"none"} -->
							<figure class="wp-block-image size-large"><img src="http://igtest.local/wp-content/uploads/2023/03/one.jpg" alt="" class="wp-image-35"/></figure>
						<!-- /wp:image -->
						<!-- wp:image {"id":36,"sizeSlug":"large","linkDestination":"none"} -->
							<figure class="wp-block-image size-large"><img src="http://igtest.local/wp-content/uploads/2023/03/two.jpg" alt="" class="wp-image-36"/></figure>
						<!-- /wp:image -->
				`;

				if (the_caption) {
					post_content += `<figcaption class="blocks-gallery-caption wp-element-caption">${the_caption}</figcaption>`;
				}

				post_content += `
					</figure>
					<!-- /wp:gallery -->
				`;


				let post = new wp.api.models.Post({
					title: 'IG Import - ' + taken_at,
					status: 'publish',
					content: post_content,
					date: the_date
				});
				post.save().done(function(post) {
					console.log('🟢 Post Created: "' + post.title.raw + '"');
				});

			}) // wp.api.loadPromise.done
		} // do_json_stuff

		document.getElementById('upload').addEventListener('change', handleFileSelect, false);
	</script>


<?php

}
