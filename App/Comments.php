<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Comments {

	public function __construct() {
		add_filter( 'comments_open', [ $this, '_comments_open' ], 10, 2 );
		add_filter( 'pings_open', [ $this, '_comments_open' ], 10, 2 );
	}

	public function _comments_open( $open, $post_id ) {
		if ( ! is_bbpress() ) {
			return $open;
		}

		return false;
	}
}
