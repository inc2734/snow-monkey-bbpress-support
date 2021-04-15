<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Assets {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
	}

	/**
	 * Enqueue assets.
	 */
	public function _wp_enqueue_scripts() {
		wp_dequeue_style( 'bbp-default' );
		wp_enqueue_style(
			'snow-monkey-bbpress-support',
			SNOW_MONKEY_BBPRESS_SUPPORT_URL . '/assets/css/bbpress.min.css',
			[ get_template() ],
			filemtime( SNOW_MONKEY_BBPRESS_SUPPORT_PATH . '/assets/css/bbpress.min.css' )
		);

		wp_enqueue_script(
			'snow-monkey-bbpress-support',
			SNOW_MONKEY_BBPRESS_SUPPORT_URL . '/assets/js/app.js',
			['jquery'],
			filemtime( SNOW_MONKEY_BBPRESS_SUPPORT_PATH . '/assets/js/app.js' ),
			true
		);
	}
}
