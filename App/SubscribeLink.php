<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class SubscribeLink {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'bbp_get_topic_subscribe_link', array( $this, '_remove_side_border' ) );
		add_filter( 'bbp_get_user_subscribe_link', array( $this, '_remove_side_border' ) );
	}

	/**
	 * Remove side border of subscribe buttons
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public function _remove_side_border( $html ) {
		return str_replace( '&nbsp;|&nbsp;', '', $html );
	}
}
