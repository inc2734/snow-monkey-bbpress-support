<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

use Snow_Monkey\Plugin\bbPressSupport\App\Helper;

class AdminBar {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, '_admin_bar_menu' ), 9999 );
		add_action( 'admin_bar_menu', array( $this, '_remove_edit' ), 9999 );
		add_action( 'wp_before_admin_bar_render', array( $this, '_wp_before_admin_bar_render' ) );
	}

	/**
	 * Remove default admin bar content.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar instance, passed by reference.
	 */
	public function _admin_bar_menu( $wp_admin_bar ) {
		if ( ! Helper::is_prevent_admin_access() ) {
			return;
		}

		if ( current_user_can( 'moderate' ) ) {
			return;
		}

		$wp_admin_bar->remove_menu( 'my-account' );
		$wp_admin_bar->remove_menu( 'site-name' );
	}

	/**
	 * Remove edit button of adminbar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar instance, passed by reference.
	 */
	public function _remove_edit( $wp_admin_bar ) {
		if ( ! is_bbpress() ) {
			return;
		}

		if ( current_user_can( 'moderate' ) ) {
			return;
		}

		$wp_admin_bar->remove_menu( 'edit' );
	}

	/**
	 * Add admin bar content.
	 *
	 * @return void
	 */
	public function _wp_before_admin_bar_render() {
		if ( ! Helper::is_prevent_admin_access() ) {
			return;
		}

		if ( current_user_can( 'moderate' ) ) {
			return;
		}

		global $wp_admin_bar;

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'snow-monkey-bbpress-support-logout',
				'title' => __( 'Logout', 'snow-monkey-bbpress-support' ),
				'href'  => wp_logout_url( home_url() ),
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'snow-monkey-bbpress-support-account',
				'title' => __( 'Account', 'snow-monkey-bbpress-support' ),
				'href'  => bbp_get_user_profile_url( bbp_get_current_user_id() ),
			)
		);
	}
}
