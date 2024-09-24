<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_Customizer_Framework\Framework;
use Framework\Helper;

if ( ! is_customize_preview() ) {
	return;
}

Framework::section(
	'snow-monkey-bbpress-support-design-archive',
	array(
		'title'           => __( 'bbPress archive page settings', 'snow-monkey-bbpress-support' ),
		'priority'        => 130,
		'active_callback' => function () {
			if ( ! class_exists( '\bbPress' ) ) {
				return false;
			}

			return Helper::is_bbpress_archive();
		},
	)
);
