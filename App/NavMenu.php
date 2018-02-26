<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class NavMenu {

	public function __construct() {
		add_filter( 'nav_menu_css_class', [ $this, '_nav_menu_css_class' ], 10, 2 );
	}

	/**
	 * Remove current class from not forum items
	 *
	 * @param  [array]  $classes
	 * @param  [object] $item
	 * @return [array]
	 */
	public function _nav_menu_css_class( $classes, $item ) {
		if ( ! is_bbpress() ) {
			return $classes;
		}

		if ( false === strpos( $item->url, bbp_get_forums_url() ) ) {
			$classes = $this->_remove_current_class( $classes );
		} else {
			$classes[] = 'current_page_parent';
			$classes   = array_unique( $classes );
		}

		return $classes;
	}

	/**
	 * Remove current class
	 *
	 * @param  [array] $classes
	 * @return [array]
	 */
	protected function _remove_current_class( $classes ) {
		$key = array_search( 'current_page_parent', $classes );
		if ( false === $key ) {
			return $classes;
		}

		unset( $classes[ $key ] );
		return $classes;
	}
}
