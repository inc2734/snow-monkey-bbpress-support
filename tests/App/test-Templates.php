<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\tests\App;

class Templates extends \WP_UnitTestCase {

	/**
	 * @test
	 */
	public function bbp_get_bbpress_template() {
		$templates = apply_filters( 'bbp_get_bbpress_template', [] );
		$this->assertContains( 'singular.php', $templates );
	}
}
