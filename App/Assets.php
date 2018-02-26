<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
	}

	/**
	 * Enqueue front css
	 *
	 * @return [void]
	 */
	public function _wp_enqueue_scripts() {
		wp_enqueue_style(
			'snow-monkey-bbpress-support',
			plugins_url( '/../assets/css/style.min.css', __FILE__ )
		);
	}
}
