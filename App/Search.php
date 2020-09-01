<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Search {

	public function __construct() {
		add_action( 'bbp_get_search_terms', [ $this, '_bbp_get_search_terms' ] );
	}

	public function _bbp_get_search_terms( $search_terms ) {
		return str_replace( '　', ' ', $search_terms );
	}
}
