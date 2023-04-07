<?php
/*
Plugin Name:  IG Test Import
Plugin URI:   https://github.com/sr4136/IG-Test-Import/
Description:  Imports an IG archive to posts.
Version:      0.0.1
Author:       SteveRudolfi 
Author URI:   https://github.com/sr4136/
License:      NA
License URI:  NA
Text Domain:  igti
*/

/*
 * Additional files.
 */
require(plugin_dir_path(__FILE__) . 'helpers.php');
require(plugin_dir_path(__FILE__) . 'data_process.php');
require(plugin_dir_path(__FILE__) . 'data_output_test.php');
require(plugin_dir_path(__FILE__) . 'data_run_import.php');

/*
 * Register options page.
 */
function igi_register_options_page() {
	add_options_page('Insta Import', 'Insta Import', 'manage_options', 'igti', 'igi_register_options_page_markup');
}
add_action('admin_menu', 'igi_register_options_page');


/*
 * Options page markup.
 */
function igi_register_options_page_markup() {
	// URI to this options page.
	$igti_uri = $_SERVER['REQUEST_URI'];
?>
	<section class="igi-header">
		<h2>IG Import:</h2>

		<form action="<?php echo $igti_uri ?>" method="post">
			<input type="submit" value="Test Data" name="submit_data" class="igsubmit test">
			<input type="submit" value="Run Import" name="submit_data" class="igsubmit import">
		</form>
	</section>

	<section class="igi-status">
		<?php igi_handleButtons(); ?>
	</section>

<?php
} // function igi_register_options_page_markup()


/*
 * Enqueue CSS just for the options page.
 */
function igi_admin_enqueue($hook) {
	if ('settings_page_igti' != $hook)
		return;
	wp_enqueue_style('igti-style', plugins_url('/style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'igi_admin_enqueue');
