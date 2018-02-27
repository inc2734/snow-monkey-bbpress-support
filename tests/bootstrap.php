<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Snow_Monkey_Bbpress_Support
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	register_theme_directory( dirname( dirname( __FILE__ ) ) . '/.themes/' );
	switch_theme( 'snow-monkey' );
	search_theme_directories();

	require dirname( dirname( __FILE__ ) ) . '/snow-monkey-bbpress-support.php';

	require dirname( dirname( __FILE__ ) ) . '/.plugins/bbpress/bbpress.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
