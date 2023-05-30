<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Comments {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'comments_open', array( $this, '_comments_open' ), 10 );
		add_filter( 'pings_open', array( $this, '_comments_open' ), 10 );
		add_action( 'init', array( $this, '_remove_wp_make_content_images_responsive' ) );
	}

	/**
	 * Close comment area.
	 *
	 * @param boolean $open Opend or not.
	 * @return boolean
	 */
	public function _comments_open( $open ) {
		if ( ! is_bbpress() ) {
			return $open;
		}

		return false;
	}

	/**
	 * Replace wp_make_content_images_responsive to wp_filter_content_tags for WP5.5.
	 */
	public function _remove_wp_make_content_images_responsive() {
		remove_filter( 'bbp_get_reply_content', 'wp_make_content_images_responsive', 60 );
		remove_filter( 'bbp_get_topic_content', 'wp_make_content_images_responsive', 60 );
		add_filter( 'bbp_get_reply_content', 'wp_filter_content_tags', 60 );
		add_filter( 'bbp_get_topic_content', 'wp_filter_content_tags', 60 );
	}
}
