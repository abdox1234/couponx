<?php
header('X-Accel-Buffering: no');
header('Content-Type: text/event-stream');
header('Content-Encoding: none');
header('Cache-Control: no-cache');

ob_implicit_flush(true);
// Clear, and turn off output buffering
while (ob_get_level() > 0) {
	// Get the curent level
	$level = ob_get_level();
	// End the buffering
	ob_end_clean();
	// If the current level has not changed, abort
	if (ob_get_level() == $level) break;
}
ob_start();

global $mts_presets;

if ( !isset( $_REQUEST['nonce'] ) || !wp_verify_nonce( $_REQUEST['nonce'], "mts_demo_importer" ) ) {
	die( 0 );
}

if ( isset( $_REQUEST['mts_remove_demos'] ) ) {

	include dirname( __FILE__ ) .'/init.php'; //load importer
	$installer = new Radium_Theme_Demo_Data_Importer( 'mts_remove_demos', array() );

	ob_end_clean();
	die();

} else if ( array_key_exists( $_REQUEST['demo_import_id'], $mts_presets ) ) {

	$import_parts = array();
	$import_parts['options'] = isset( $_REQUEST['demo_import_options'] ) && '1' === $_REQUEST['demo_import_options'];
	$import_parts['content'] = isset( $_REQUEST['demo_import_content'] ) && '1' === $_REQUEST['demo_import_content'];
	$import_parts['menus']   = isset( $_REQUEST['demo_import_content'] ) && '1' === $_REQUEST['demo_import_content'];
	$import_parts['widgets'] = isset( $_REQUEST['demo_import_widgets'] ) && '1' === $_REQUEST['demo_import_widgets'];

	include dirname( __FILE__ ) .'/init.php'; //load importer
	$installer = new Radium_Theme_Demo_Data_Importer( $_REQUEST['demo_import_id'], $import_parts );

	ob_end_clean();
	die();
}
