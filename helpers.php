<?php

function igi_get_local_file_contents($file_path) {
	ob_start();
		include $file_path;
		$contents = ob_get_clean();
	return $contents;
}


function igi_get_file_extension($name) {
	$n = strrpos($name, '.');
	return ($n === false) ? '' : substr($name, $n + 1);
}


function igi_handleButtons(){
	if (!empty($_POST)) {
		// echo( '<p>Full _POST:</p>' );
		// echo( '<textarea>' . json_encode( $_POST ) . '</textarea>' );

		if (isset($_POST['submit_data'])) {
			// Run test.
			if ($_POST['submit_data'] == "Test Data") {
				igi_output_test_data();
			}
			// Run import.
			if ($_POST['submit_data'] == "Run Import") {
				igi_run_import();
			}
		}
	}
}