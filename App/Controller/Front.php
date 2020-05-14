<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

use Snow_Monkey\Plugin\bbPressSupport\App\Helper;

class Front {

	public function __construct() {
		add_action( 'login_form', [ $this, '_add_gianism_login' ] );
		add_filter( 'snow_monkey_google_adsense', [ $this, '_snow_monkey_google_adsense' ] );
		add_filter( 'snow_monkey_layout', [ $this, '_snow_monkey_layout' ] );
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

	/**
	 * Set layout
	 *
	 * @param string $layout
	 * @param string
	 */
	public function _snow_monkey_layout( $layout ) {
		if ( Helper::is_bbpress_single() ) {
			$bbpress_single_layout = get_theme_mod( 'snow-monkey-bbpress-support-single-layout' );
			if ( $bbpress_single_layout ) {
				return $bbpress_single_layout;
			}
		} elseif ( Helper::is_bbpress_archive() ) {
			$bbpress_archive_page_layout = get_theme_mod( 'snow-monkey-bbpress-support-archive-page-layout' );
			if ( $bbpress_archive_page_layout ) {
				return $bbpress_archive_page_layout;
			}
		}

		return $layout;
	}
}
