<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

use Snow_Monkey\Plugin\bbPressSupport\App\Helper;

class Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, '_redirect' ) );
	}

	/**
	 * General users do not log in to the management screen and redirect.
	 * But the profile page is permitted.
	 *
	 * @return void
	 */
	public function _redirect() {
		if ( ! Helper::is_prevent_admin_access() ) {
			return;
		}

		if ( is_admin() && ! current_user_can( 'moderate' ) && ! preg_match( '/profile\.php$/', $_SERVER['REQUEST_URI'] ) ) {
			$redirect_to = apply_filters( 'snow_monkey_bbpress_support_unauthorized_user_redirect_to', home_url() );
			wp_redirect( $redirect_to );
			exit;
		}
	}
}
