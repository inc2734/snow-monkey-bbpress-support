<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Author {

	public function __construct() {
		add_action( 'bbp_get_author_ip', [ $this, '_bbp_get_author_ip' ] );
	}

	public function _bbp_get_author_ip() {
		return false;
	}
}
