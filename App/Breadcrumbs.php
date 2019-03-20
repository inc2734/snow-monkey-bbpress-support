<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Breadcrumbs {

	public function __construct() {
		add_filter( 'snow_monkey_breadcrumbs', [ $this, '_snow_monkey_breadcrumbs' ] );
	}

	/**
	 * Update breadcrumbs in bbPress
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	public function _snow_monkey_breadcrumbs( $breadcrumbs ) {
		if ( bbp_is_single_topic() ) {
			$breadcrumbs = $this->_single_topic( $breadcrumbs );
		} elseif ( bbp_is_search() ) {
			$breadcrumbs = $this->_search( $breadcrumbs );
		} elseif ( bbp_is_single_user() ) {
			$breadcrumbs = $this->_single_user( $breadcrumbs );
		} elseif ( bbp_is_topic_tag() || ( get_query_var( 'bbp_topic_tag' ) && ! bbp_is_topic_tag_edit() ) ) {
			$breadcrumbs = $this->_topic_tag( $breadcrumbs );
		} elseif ( bbp_is_topic_tag_edit() ) {
			$breadcrumbs = $this->_topic_tag( $breadcrumbs );
		} elseif ( bbp_is_topic_archive() ) {
			$breadcrumbs = $this->_topic_archive( $breadcrumbs );
		} elseif ( bbp_is_single_reply() ) {
			$breadcrumbs = $this->_single_reply( $breadcrumbs );
		}

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for single topic
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _single_topic( $breadcrumbs ) {
		foreach ( $breadcrumbs as $key => $item ) {
			if ( isset( $item['link'] ) && bbp_get_topics_url() === $item['link'] ) {
				$adding_items[] = [
					'title' => bbp_get_forum_archive_title(),
					'link'  => bbp_get_forums_url(),
				];

				$ancestors = array_reverse( (array) get_post_ancestors( get_the_ID() ) );
				foreach ( $ancestors as $post_id ) {
					$adding_items[] = [
						'title' => bbp_get_forum_title( $post_id ),
						'link'  => bbp_get_forum_permalink( $post_id ),
					];
				}

				unset( $breadcrumbs[ $key ] );
				array_splice( $breadcrumbs, $key, 0, $adding_items );
			}
		}

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for search
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _search( $breadcrumbs ) {
		$breadcrumbs[] = [
			'title' => bbp_get_forum_archive_title(),
			'link'  => '',
		];

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for single user
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _single_user( $breadcrumbs ) {
		$breadcrumbs[] = [
			'title' => bbp_get_forum_archive_title(),
			'link'  => bbp_get_forums_url(),
		];

		$breadcrumbs[] = [
			'title' => get_userdata( bbp_get_user_id() )->display_name,
			'link'  => '',
		];

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for topic tag
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _topic_tag( $breadcrumbs ) {
		$breadcrumbs[] = [
			'title' => bbp_get_forum_archive_title(),
			'link'  => bbp_get_forums_url(),
		];

		$breadcrumbs[] = [
			'title' => bbp_get_topic_tag_name(),
			'link'  => '',
		];

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for topic archive
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _topic_archive( $breadcrumbs ) {
		$adding_items = [
			[
				'title' => bbp_get_forum_archive_title(),
				'link'  => bbp_get_forums_url(),
			],
		];

		array_splice( $breadcrumbs, -1, 0, $adding_items );

		return $breadcrumbs;
	}

	/**
	 * Update breadcrumbs for single reply
	 *
	 * @param  [array] $breadcrumbs
	 * @return [array]
	 */
	protected function _single_reply( $breadcrumbs ) {
		$breadcrumbs[] = [
			'title' => bbp_get_forum_archive_title(),
			'link'  => bbp_get_forums_url(),
		];

		$breadcrumbs[] = [
			'title' => bbp_get_topic_title(),
			'link'  => bbp_get_topic_permalink(),
		];

		$breadcrumbs[] = [
			'title' => bbp_get_reply_title(),
			'link'  => '',
		];

		return $breadcrumbs;
	}
}
