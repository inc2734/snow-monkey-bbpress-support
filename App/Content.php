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
		remove_filter( 'bbp_get_reply_content', 'bbp_make_clickable', 40 ); // v2.6.0〜
		remove_filter( 'bbp_get_topic_content', 'bbp_make_clickable', 40 ); // v2.6.0〜

		add_filter( 'bbp_get_reply_content', [ $this, '_wp_oembed_blog_card_sanitize' ], 100 );
		add_filter( 'bbp_get_topic_content', [ $this, '_wp_oembed_blog_card_sanitize' ], 100 );
	}

	/**
	 * @todo
	 *
	 * @param string $content
	 * @return string
	 */
	public function _wp_oembed_blog_card_sanitize( $content ) {
		$content = preg_replace( '|(<div class="wp-oembed-blog-card"[^>]+?><a [^>]+>)</p>|', '$1', $content );
		$content = str_replace( '<p></a></div>', '</a></div>', $content );
		return $content;
	}
}
