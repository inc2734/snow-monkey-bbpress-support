<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Content {

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, '_after_setup_theme' ] );
	}

	public function _after_setup_theme() {
		remove_filter( 'bbp_get_reply_content', 'bbp_make_clickable', 4 );
		remove_filter( 'bbp_get_topic_content', 'bbp_make_clickable', 4 );
	}
}
