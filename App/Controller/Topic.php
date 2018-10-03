<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App\Controller;

class Topic {

	public function __construct() {
		add_action( 'bbp_template_before_replies_loop', [ $this, '_display_topic_close_link' ] );
	}

	/**
	 * Add topic close link
	 *
	 * @return [void]
	 */
	public function _display_topic_close_link() {
		$topic_id     = bbp_get_topic_id();
		$topic        = bbp_get_topic( $topic_id );
		$current_user = wp_get_current_user();

		if ( empty( $topic->ID ) || ! current_user_can( 'participate', $topic->ID ) || (int) $current_user->ID !== (int) $topic->post_author ) {
			return;
		}

		$r = bbp_parse_args(
			[],
			[
				'close_text' => __( 'Close this topic', 'snow-monkey-bbpress-support' ),
				'open_text'  => __( 'Open this topic', 'snow-monkey-bbpress-support' ),
			],
			'get_topic_close_link'
		);

		$display = bbp_is_topic_open( $topic->ID ) ? $r['close_text'] : $r['open_text'];
		$uri = add_query_arg(
			[
				'action'   => 'bbp_toggle_topic_close',
				'topic_id' => $topic->ID,
			]
		);
		$uri = wp_nonce_url( $uri, 'close-topic_' . $topic->ID );
		?>
		<div class="snow-monkey-bbpress-support-my-topic-close-link">
			<a href="<?php echo esc_url( $uri ); ?>" class="button"><?php echo esc_html( $display ); ?></a>
		</div>
		<?php
	}
}
