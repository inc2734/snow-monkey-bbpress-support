<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

use Framework\Helper;

class Sidebar {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'snow_monkey_use_post_type_widget_area', [ $this, '_snow_monkey_use_post_type_widget_area' ] );
		add_filter( 'is_active_sidebar', [ $this, '_is_active_sidebar' ], 10, 2 );
		add_action( 'wp_head', [ $this, '_remove_sidebars' ], 11 );
		add_action( 'snow_monkey_sidebar', [ $this, '_snow_monkey_sidebar' ] );
		add_action( 'widgets_init', [ $this, '_widgets_init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, '_enqueue' ] );
	}

	/**
	 * Return false snow_monkey_use_post_type_widget_area hook in bbbPress.
	 *
	 * @param boolean $boolean Use post type widget area.
	 * @return boolean
	 */
	public function _snow_monkey_use_post_type_widget_area( $boolean ) {
		if ( ! is_bbpress() ) {
			return $boolean;
		}

		return false;
	}

	/**
	 * Update is_active_sidebar() in bbPress.
	 *
	 * @param boolean $is_active  Whether or not the sidebar should be considered "active".
	 *                            In other words, whether the sidebar contains any widgets.
	 * @param string  $sidebar_id Index, name, or ID of the dynamic sidebar.
	 * @return boolean
	 */
	public function _is_active_sidebar( $is_active, $sidebar_id ) {
		if ( ! is_bbpress() ) {
			return $is_active;
		}

		if ( ! is_registered_sidebar( $sidebar_id ) ) {
			return false;
		}

		return $is_active;
	}

	/**
	 * Remove sidebars in bbPress.
	 *
	 * @return void
	 */
	public function _remove_sidebars() {
		if ( ! is_bbpress() ) {
			return;
		}

		unregister_sidebar( 'contents-bottom-widget-area' );
		unregister_sidebar( 'title-top-widget-area' );
		unregister_sidebar( 'archive-top-widget-area' );
		unregister_sidebar( 'sidebar-widget-area' );
		unregister_sidebar( 'sidebar-sticky-widget-area' );
		unregister_sidebar( 'archive-sidebar-widget-area' );
	}

	/**
	 * Add sidebar for bbPress.
	 *
	 * @return void
	 */
	public function _snow_monkey_sidebar() {
		if ( ! is_bbpress() ) {
			return;
		}

		if ( is_active_sidebar( 'bbpress-sidebar-widget-area' ) ) {
			?>
			<div class="l-sidebar-widget-area"
				data-is-slim-widget-area="true"
				data-is-content-widget-area="false"
			>
				<?php dynamic_sidebar( 'bbpress-sidebar-widget-area' ); ?>
			</div>
			<?php
		}

		if ( is_active_sidebar( 'bbpress-sidebar-sticky-widget-area' ) ) {
			?>
			<div class="l-sidebar-sticky-widget-area"
				data-is-slim-widget-area="true"
				data-is-content-widget-area="false"
				>

				<?php dynamic_sidebar( 'bbpress-sidebar-sticky-widget-area' ); ?>
			</div>
			<?php
		}
	}

	/**
	 * Register bbPress sidebar.
	 *
	 * @return void
	 */
	public function _widgets_init() {
		register_sidebar(
			[
				'name'          => __( 'bbPress sidebar', 'snow-monkey-bbpress-support' ),
				'description'   => __( 'This widgets are displayed in the sidebar of bbPress.', 'snow-monkey-bbpress-support' ),
				'id'            => 'bbpress-sidebar-widget-area',
				'before_widget' => '<div id="%1$s" class="c-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="c-widget__title"><span>',
				'after_title'   => '</span></h2>',
			]
		);

		register_sidebar(
			[
				'name'          => __( 'Sticky bbPress sidebar', 'snow-monkey-bbpress-support' ),
				'description'   => __( 'This widgets are displayed in the sidebar of bbPress.', 'snow-monkey-bbpress-support' ),
				'id'            => 'bbpress-sidebar-sticky-widget-area',
				'before_widget' => '<div id="%1$s" class="c-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="c-widget__title"><span>',
				'after_title'   => '</span></h2>',
			]
		);
	}

	/**
	 * Enqueue assets.
	 *
	 * @see https://github.com/inc2734/snow-monkey/blob/22d3c9828d5818b8ad580cd0e625db2791adbf36/app/setup/widget-area.php#L403-L411
	 */
	public function _enqueue() {
		if ( ! is_active_sidebar( 'bbpress-sidebar-sticky-widget-area' ) ) {
			return;
		}

		wp_enqueue_script(
			Helper::get_main_script_handle() . '-sidebar-sticky-widget-area',
			get_theme_file_uri( '/assets/js/sidebar-sticky-widget-area.min.js' ),
			[],
			filemtime( get_theme_file_path( '/assets/js/sidebar-sticky-widget-area.min.js' ) ),
			true
		);
	}
}
