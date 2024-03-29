<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Search {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bbp_get_search_terms', array( $this, '_bbp_get_search_terms' ) );
	}

	/**
	 * Replace full-width space to harf width space.
	 *
	 * @param string $search_terms The search terms.
	 * @return string
	 */
	public function _bbp_get_search_terms( $search_terms ) {
		return str_replace( '　', ' ', $search_terms );
	}
}
