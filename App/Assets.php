<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Assets {

	public function __construct() {
		add_filter( 'bbp_default_styles', [ $this, '_bbp_default_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
	}

	public function _bbp_default_styles( $styles ) {
		$styles['bbp-default'] = array_merge( $styles['bbp-default'], [
			'file'         => '../../../snow-monkey-bbpress-support/assets/css/bbpress.min.css',
			'dependencies' => [ get_template() ],
		] );

		return $styles;
	}

	public function _wp_enqueue_scripts() {
		wp_enqueue_script(
			'snow-monkey-bbpress-support',
			plugin_dir_url( __FILE__ ) . '../assets/js/app.min.js',
			['jquery'],
			false,
			true
		);
	}
}
