<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Pagination {

	public function __construct() {
		add_filter( 'bbp_after_paginate_links_parse_args', [ $this, '_bbp_after_paginate_links_parse_args' ] );
		add_filter( 'bbp_get_topic_pagination_links', [ $this, '_bbp_pagination_links' ] );
		add_filter( 'bbp_get_search_pagination_links', [ $this, '_bbp_pagination_links' ] );
		add_filter( 'bbp_get_forum_pagination_links', [ $this, '_bbp_pagination_links' ] );
	}

	public function _bbp_after_paginate_links_parse_args( $r ) {
		$r['end_size'] = 1;
		return $r;
	}

	public function _bbp_pagination_links( $pagination ) {
		$pagination = preg_replace(
			'/^(\d+)$/',
			'<span class="page-numbers current">$1</span>',
			$pagination
		);
		$pagination = preg_replace(
			'/^<a([^>]+)>(\d+?)<\/a>$/',
			'<a class="page-numbers" $1>$2</a>',
			$pagination
		);
		$pagination = \Inc2734\WP_Basis\App\Model\Pagination::pagination( $pagination );
		$pagination .= "\n";
		return $pagination;
	}
}
