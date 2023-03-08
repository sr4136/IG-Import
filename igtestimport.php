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
		<textarea class="form-control" rows=5 cols=120 id="igti_input"></textarea>
	</section>

	<script>
		let $ = jQuery;

		function handleFileSelect(evt) {
			let files = evt.target.files; // FileList object

			// use the 1st file from the list
			let f = files[0];

			let reader = new FileReader();

			// Closure to capture the file information.
			reader.onload = (function(the_file) {
				return function(e) {
					const the_result = e.target.result;
					$('#igti_input').val(the_result);
					console.log(the_result);

					const the_json = JSON.parse(the_result);
					console.log(the_json);
					console.log(the_json[0]);
					do_json_stuff();
				};
			})(f);

			// Read in the image file as a data URL.
			reader.readAsText(the_file);
		}

		function do_json_stuff() {
			wp.api.loadPromise.done(function() {
				//... use the client here
			})
		}

		document.getElementById('upload').addEventListener('change', handleFileSelect, false);
	</script>


<?php

}
