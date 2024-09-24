<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

use Framework\Helper;

class Front {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'login_form', array( $this, '_add_gianism_login' ) );
		add_filter( 'snow_monkey_google_adsense', array( $this, '_snow_monkey_google_adsense' ) );
		add_filter( 'snow_monkey_layout', array( $this, '_snow_monkey_layout' ) );
	}

	/**
	 * Add gianism login buttons.
	 *
	 * @return void
	 */
	public function _add_gianism_login() {
		if ( function_exists( 'gianism_login' ) ) {
			gianism_login( '', '', home_url( '/forums/' ) );
		}
	}

	/**
	 * Remove Snow Monkey advatizement areas.
	 *
	 * @param boolean $is_output Displayed google adsense or not.
	 * @return boolean
	 */
	public function _snow_monkey_google_adsense( $is_output ) {
		if ( ! is_bbpress() ) {
			return $is_output;
		}

		return false;
	}

	/**
	 * Set layout.
	 *
	 * @param string $layout The layout slug.
	 * @return string
	 */
	public function _snow_monkey_layout( $layout ) {
		if ( Helper::is_bbpress_mypage() || Helper::is_bbpress_single() || Helper::is_bbpress_archive() ) {
			$old_layout = false;
			$new_layout = false;

			if ( Helper::is_bbpress_mypage() || Helper::is_bbpress_archive() ) {
				$old_layout = get_theme_mod( 'snow-monkey-bbpress-support-archive-page-layout' );
				if ( is_customize_preview() ) {
					$new_layout = get_theme_mod( 'bbpress-archive-page-layout' );
				} else {
					$mods       = get_theme_mods();
					$new_layout = isset( $mods['bbpress-archive-page-layout'] ) ? $mods['bbpress-archive-page-layout'] : false;
				}
			} elseif ( Helper::is_bbpress_single() ) {
				$old_layout = get_theme_mod( 'snow-monkey-bbpress-support-single-layout' );

				if ( is_customize_preview() ) {
					$new_layout = get_theme_mod( 'bbpress-single-layout' );
				} else {
					$mods       = get_theme_mods();
					$new_layout = isset( $mods['bbpress-single-layout'] ) ? $mods['bbpress-single-layout'] : false;
				}
			}

			if ( $old_layout && false === $new_layout ) {
				return $old_layout;
			}
			if ( $new_layout ) {
				return $new_layout;
			}
		}

		return $layout;
	}
}
