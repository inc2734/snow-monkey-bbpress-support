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
		add_action( 'init', [ $this, '_remove_wp_make_content_images_responsive' ] );
	}

	public function _comments_open( $open, $post_id ) {
		if ( ! is_bbpress() ) {
			return $open;
		}

		return false;
	}

	public function _remove_wp_make_content_images_responsive() {
		remove_filter( 'bbp_get_reply_content', 'wp_make_content_images_responsive', 60 );
		remove_filter( 'bbp_get_topic_content', 'wp_make_content_images_responsive', 60 );
		add_filter( 'bbp_get_reply_content', 'wp_filter_content_tags', 60 );
		add_filter( 'bbp_get_topic_content', 'wp_filter_content_tags', 60 );
	}
}
