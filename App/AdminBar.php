<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class AdminBar {

	public function __construct() {
		add_action( 'admin_bar_menu', [ $this, '_admin_bar_menu' ], 9999 );
		add_action( 'wp_before_admin_bar_render', [ $this, '_wp_before_admin_bar_render' ] );
	}

	/**
	 * Remove default admin bar content
	 *
	 * @param  [object] $wp_admin_bar
	 * @return [object]
	 */
	public function _admin_bar_menu( $wp_admin_bar ) {
		if ( current_user_can( 'moderate' ) ) {
			return;
		}

		$wp_admin_bar->remove_menu( 'my-account' );
		$wp_admin_bar->remove_menu( 'site-name' );
	}

	/**
	 * Add admin bar content
	 *
	 * @return [void]
	 */
	public function _wp_before_admin_bar_render() {
		if ( current_user_can( 'moderate' ) ) {
			return;
		}

		global $wp_admin_bar;

		$wp_admin_bar->add_menu( [
			'id'    => 'snow-monkey-bbpress-support-logout',
			'title' => __( 'Logout', 'snow-monkey-bbpress-support' ),
			'href'  => wp_logout_url( home_url() ),
		] );

		$wp_admin_bar->add_menu( [
			'id'    => 'snow-monkey-bbpress-support-account',
			'title' => __( 'Account', 'snow-monkey-bbpress-support' ),
			'href'  => bbp_get_user_profile_url( bbp_get_current_user_id() ),
		] );
	}
}
