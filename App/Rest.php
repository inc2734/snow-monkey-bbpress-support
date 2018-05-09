<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Rest {

	public function __construct() {
		add_action( 'bbp_register_post_types', [ $this, '_support_rest' ], 11 );
	}

	public function _support_rest() {
		$post_types = [
			bbp_get_reply_post_type(),
			bbp_get_topic_post_type(),
			bbp_get_forum_post_type(),
		];

		foreach ( $post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			$object->show_in_rest = true;
			$object->rest_controller_class = 'WP_REST_Posts_Controller';
			register_post_type( $post_type, (array) $object );
		}
	}
}
