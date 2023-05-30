<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Post {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'bbp_new_topic_pre_insert', array( $this, '_sanitize' ) );
		add_filter( 'bbp_new_reply_pre_insert', array( $this, '_sanitize' ) );
		add_filter( 'bbp_edit_reply_pre_insert', array( $this, '_sanitize' ) );
		add_filter( 'bbp_edit_topic_pre_insert', array( $this, '_sanitize' ) );
	}

	/**
	 * Sanitize data.
	 *
	 * @param array $data The post data.
	 * @return array
	 */
	public function _sanitize( $data ) {
		// Remove blank lines before and after the content.
		$data['post_content'] = preg_replace( '/[\n(&nbsp;)]*$/', '', $data['post_content'] );
		$data['post_content'] = trim( $data['post_content'] );

		// Code block
		$data['post_content'] = preg_replace(
			'|<code></code>`\n([^`]*?)\n<code></code>`|ms',
			'<pre><code>$1</code></pre>',
			$data['post_content']
		);
		return $data;
	}
}
