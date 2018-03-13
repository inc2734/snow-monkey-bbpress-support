<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Assets {

	public function __construct() {
		add_filter( 'bbp_default_styles', function( $styles ) {
			$styles['bbp-default'] = array_merge( $styles['bbp-default'], [
				'file'         => '../../../snow-monkey-bbpress-support/assets/css/bbpress.min.css',
				'dependencies' => [ get_template() ],
			] );

			return $styles;
		} );
	}
}
