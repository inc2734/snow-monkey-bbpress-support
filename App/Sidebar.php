<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Sidebar {

	public function __construct() {
		add_action( 'wp_head', [ $this, '_remove_sidebars' ], 11 );
		add_action( 'snow_monkey_sidebar', [ $this, '_snow_monkey_sidebar' ] );
		add_action( 'widgets_init', [ $this, '_widgets_init' ] );
	}

	/**
	 * Remove sidebars in bbPress
	 *
	 * @return [void]
	 */
	public function _remove_sidebars() {
		if ( ! is_bbpress() ) {
			return;
		}

		unregister_sidebar( 'contents-bottom-widget-area' );
		unregister_sidebar( 'title-top-widget-area' );
		unregister_sidebar( 'archive-top-widget-area' );
		unregister_sidebar( 'sidebar-widget-area' );
		unregister_sidebar( 'archive-sidebar-widget-area' );
	}

	/**
	 * Add sidebar for bbPress
	 *
	 * @return [void]
	 */
	public function _snow_monkey_sidebar( $index ) {
		if ( ! is_bbpress() ) {
			return;
		}

		if ( is_active_sidebar( 'bbpress-sidebar-widget-area' ) ) {
			dynamic_sidebar( 'bbpress-sidebar-widget-area' );
		}
	}

	/**
	 * Register bbPress sidebar
	 *
	 * @return [void]
	 */
	public function _widgets_init() {
		register_sidebar( [
			'name'          => __( 'bbPress sidebar', 'snow-monkey-bbpress-support' ),
			'description'   => __( 'This widgets are displayed in the sidebar of bbPress.', 'snow-monkey-bbpress-support' ),
			'id'            => 'bbpress-sidebar-widget-area',
			'before_widget' => '<div id="%1$s" class="c-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="c-widget__title"><span>',
			'after_title'   => '</span></h2>',
		] );
	}
}
