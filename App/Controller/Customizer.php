<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

use Framework\Helper;

class Customizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'snow_monkey_post_load_customizer', [ $this, '_load_customizer' ] );
	}

	/**
	 * Loads customizer.
	 */
	public function _load_customizer() {
		Helper::include_files( SNOW_MONKEY_BBPRESS_SUPPORT_PATH . '/customizer' );
	}
}
