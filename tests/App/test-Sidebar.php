<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\tests\App;

class Sidebar extends \WP_UnitTestCase {

	/**
	 * @test
	 */
	public function bbpress_sidebar() {
		global $wp_registered_sidebars;
		$this->assertArrayHasKey( 'bbpress-sidebar-widget-area', $wp_registered_sidebars );
	}
}
