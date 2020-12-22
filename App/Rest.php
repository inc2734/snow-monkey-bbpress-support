<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Rest {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bbp_register_post_types', [ $this, '_support_rest' ], 11 );
	}

	/**
	 * Add show_in_rest: true to bbPress's post types.
	 */
	public function _support_rest() {
		$post_types = [
			bbp_get_reply_post_type(),
			bbp_get_topic_post_type(),
			bbp_get_forum_post_type(),
		];
		$post_types = array_filter( $post_types );

		foreach ( $post_types as $post_type ) {
			if ( ! $post_type ) {
				continue;
			}

			$object = get_post_type_object( $post_type );
			if ( ! $object ) {
				continue;
			}

			$object->show_in_rest          = true;
			$object->rest_controller_class = 'WP_REST_Posts_Controller';
			$object->labels                = (array) $object->labels;
			register_post_type( $post_type, (array) $object );
		}
	}
}
