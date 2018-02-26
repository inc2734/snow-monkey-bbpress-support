<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

class Front {

	public function __construct() {
		add_action( 'login_form', [ $this, '_add_gianism_login' ] );
		add_filter( 'snow_monkey_google_adsense', [ $this, '_snow_monkey_google_adsense' ] );
	}

	/**
	 * Add gianism login buttons
	 *
	 * @return [void]
	 */
	public function _add_gianism_login() {
		if ( function_exists( 'gianism_login' ) ) {
			gianism_login( '', '', home_url( '/forums/' ) );
		}
	}

	/**
	 * Remove Snow Monkey advatizement areas
	 *
	 * @param  [boolean] $bool
	 * @return [boolean]
	 */
	public function _snow_monkey_google_adsense( $bool ) {
		if ( ! is_bbpress() ) {
			return $bool;
		}

		return false;
	}
}
