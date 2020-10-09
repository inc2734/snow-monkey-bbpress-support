<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Helper {

	/**
	 * bbPress is single or not.
	 *
	 * @return boolean
	 */
	public static function is_bbpress_single() {
		return bbp_is_topic_tag()
			|| bbp_is_topic_tag_edit()
			|| bbp_is_single_topic()
			|| bbp_is_single_reply()
			|| bbp_is_topic_edit()
			|| bbp_is_topic_merge()
			|| bbp_is_topic_split()
			|| bbp_is_reply_edit()
			|| bbp_is_reply_move()
			|| bbp_is_single_view()
			|| bbp_is_single_user_edit()
			|| bbp_is_single_user()
			|| bbp_is_user_home()
			|| bbp_is_user_home_edit()
			|| bbp_is_topics_created()
			|| bbp_is_replies_created()
			|| bbp_is_favorites()
			|| bbp_is_subscriptions();
	}

	/**
	 * bbPress is archive or not.
	 *
	 * @return boolean
	 */
	public static function is_bbpress_archive() {
		return bbp_is_forum_archive()
			|| bbp_is_topic_archive()
			|| bbp_is_single_forum()
			|| bbp_is_search()
			|| bbp_is_search_results();
	}

	/**
	 * Return true when the user can access dashboard.
	 *
	 * @return boolean
	 */
	public static function is_prevent_admin_access() {
		$is_prevent_admin_access = ! current_user_can( 'edit_posts' );
		return apply_filters(
			'snow_monkey_bbpress_support_prevent_admin_access',
			$is_prevent_admin_access
		);
	}
}
