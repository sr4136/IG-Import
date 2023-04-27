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
Text Domain:  igi
*/

/*
 * Additional files.
 */
require(plugin_dir_path(__FILE__) . 'helpers.php');
require(plugin_dir_path(__FILE__) . 'data_process.php');
require(plugin_dir_path(__FILE__) . 'data_output_test.php');
require(plugin_dir_path(__FILE__) . 'data_run_import.php');


/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function igi_permalink_igi_permalink_block_init() {
	register_block_type( __DIR__ . '/build/igi-permalink', array(
		'render_callback' => 'igi_permalink_block_render'
	) );
}
add_action('init', 'igi_permalink_igi_permalink_block_init');

/*
 * Dynamic block render.
 */
function igi_permalink_block_render( $attributes, $innerBlocks ) {
	$blockAtts = get_block_wrapper_attributes();
	$permalink = get_the_permalink();
	$markup = "<a {$blockAtts} href='{$permalink}'> {$innerBlocks} </a>";

	return $markup;
}


/*
 * Register options page.
 */
function igi_register_options_page() {
	add_options_page('Insta Import', 'Insta Import', 'manage_options', 'igi', 'igi_register_options_page_markup');
}
add_action('admin_menu', 'igi_register_options_page');


/*
 * Options page markup.
 */
function igi_register_options_page_markup() {
	global $_SERVER;
	// URI to this options page.
	$igi_uri = $_SERVER['REQUEST_URI'];
?>
	<section class="igi-header">
		<h2>IG Import:</h2>

		<form action="<?php echo $igi_uri ?>" method="post">
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
	if ('settings_page_igi' != $hook)
		return;
	wp_enqueue_style( 'igi-options-page-style', plugins_url('/options-page.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'igi_admin_enqueue');

/*
 * Enqueue CSS for frontend.
 */
function igi_style() {
	//wp_enqueue_style('igi-style', plugins_url('/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'igi_style');