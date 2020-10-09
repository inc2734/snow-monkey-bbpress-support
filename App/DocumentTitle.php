<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class DocumentTitle {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'document_title_parts', [ $this, '_document_title_parts' ] );
	}

	/**
	 * Update document title in bbPress.
	 *
	 * @param string $title Document title.
	 * @return string
	 */
	public function _document_title_parts( $title ) {
		if ( ! is_bbpress() ) {
			return $title;
		}

		if ( ! empty( $title['title'] ) ) {
			return $title;
		}

		if ( bbp_is_topic_edit() ) {
			$title['title'] = bbp_get_topic_title();
		} elseif ( bbp_is_topic_tag() || ( get_query_var( 'bbp_topic_tag' ) && ! bbp_is_topic_tag_edit() ) ) {
			$title['title'] = bbp_get_topic_tag_name();
		} elseif ( bbp_is_topic_tag_edit() ) {
			$title['title'] = bbp_get_topic_tag_name();
		} elseif ( bbp_is_search() ) {
			$title['title'] = bbp_get_forum_archive_title();
		} elseif ( bbp_is_single_user() ) {
			$title['title'] = get_userdata( bbp_get_user_id() )->display_name;
		} elseif ( bbp_is_single_reply() || bbp_is_reply_edit() ) {
			$title['title'] = bbp_get_reply_title();
		}

		return $title;
	}
}
