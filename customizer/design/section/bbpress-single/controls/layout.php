<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_Customizer_Framework\Framework;
use Framework\Helper;

$wrapper_templates = array_merge(
	[
		'' => __( 'Default', 'snow-monkey-bbpress-support' ),
	],
	Helper::get_wrapper_templates()
);

Framework::control(
	'select',
	'snow-monkey-bbpress-support-single-layout',
	[
		'label'   => __( 'bbPress single page layout', 'snow-monkey-bbpress-support' ),
		'default' => '',
		'choices'  => is_customize_preview() ? $wrapper_templates : [],
	]
);

if ( ! is_customize_preview() ) {
	return;
}

$panel   = Framework::get_panel( 'design' );
$section = Framework::get_section( 'snow-monkey-bbpress-support-design-single' );
$control = Framework::get_control( 'snow-monkey-bbpress-support-single-layout' );
$control->join( $section )->join( $panel );
