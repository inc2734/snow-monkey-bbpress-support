<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

class Admin {

	public function __construct() {
		add_action( 'admin_menu', [ $this, '_redirect' ] );
	}

	/**
	 * General users do not log in to the management screen and redirect.
	 * But the profile page is permitted.
	 *
	 * @return [void]
	 */
	public function _redirect() {
		if ( is_admin() && ! current_user_can( 'moderate' ) && ! preg_match( '/profile\.php$/', $_SERVER['REQUEST_URI'] ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}
}
