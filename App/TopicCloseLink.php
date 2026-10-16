<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class TopicCloseLink {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bbp_get_request_snow_monkey_bbpress_support_toggle_topic_close', array( $this, '_toggle_topic_close' ) );
		add_action( 'bbp_template_before_replies_loop', array( $this, '_display_to_before_replies_loop' ) );
		add_action( 'bbp_theme_after_reply_form_submit_button', array( $this, '_display_to_after_reply_form_submit_button' ) );
	}

	/**
	 * Check whether the current user can open or close their own topic.
	 *
	 * @param \WP_Post|null $topic Topic object.
	 * @return boolean
	 */
	protected function _can_toggle_topic( $topic ) {
		return ! empty( $topic->ID )
			&& is_user_logged_in()
			&& (int) get_current_user_id() === (int) $topic->post_author
			&& current_user_can( 'participate', $topic->ID )
			// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Registered by bbPress.
			&& current_user_can( 'edit_topic', $topic->ID )
			// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Registered by bbPress.
			&& current_user_can( 'read_topic', $topic->ID )
			// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Registered by bbPress.
			&& current_user_can( 'read_forum', bbp_get_topic_forum_id( $topic->ID ) )
			&& in_array( $topic->post_status, array( bbp_get_public_status_id(), bbp_get_closed_status_id() ), true );
	}

	/**
	 * Handle opening or closing the current user's own topic.
	 */
	public function _toggle_topic_close() {
		if ( empty( $_GET['topic_id'] ) || ! is_numeric( $_GET['topic_id'] ) ) {
			return;
		}

		$topic_id = (int) $_GET['topic_id'];
		$topic    = $topic_id > 0 ? bbp_get_topic( $topic_id ) : null;
		if ( ! $this->_can_toggle_topic( $topic ) ) {
			return;
		}

		if (
			empty( $_GET['_wpnonce'] )
			|| ! is_string( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'close-topic_' . $topic_id )
		) {
			return;
		}

		$post_data = array( 'ID' => $topic_id );
		$action    = 'bbp_toggle_topic_close';
		$retval    = bbp_toggle_topic(
			array(
				'id'     => $topic_id,
				'action' => $action,
				'data'   => $post_data,
			)
		);

		do_action( 'bbp_toggle_topic_handler', $retval['status'], $post_data, $action );

		if ( empty( $retval['status'] ) || is_wp_error( $retval['status'] ) ) {
			bbp_add_error( 'bbp_toggle_topic', $retval['message'] );
			return;
		}

		bbp_redirect( $retval['redirect_to'] );
	}

	/**
	 * Return close link html.
	 *
	 * @return string
	 */
	protected function _close_link() {
		$topic_id = bbp_get_topic_id();
		$topic    = bbp_get_topic( $topic_id );

		if ( ! $this->_can_toggle_topic( $topic ) ) {
			return;
		}

		if (
			! apply_filters(
				'snow_monkey_bbpress_support_display_topic_close_link',
				'__return_true',
				$topic->ID
			)
		) {
			return;
		}

		$args = bbp_parse_args(
			array(),
			array(
				'close_text' => __( 'Close this topic', 'snow-monkey-bbpress-support' ),
				'open_text'  => __( 'Open this topic', 'snow-monkey-bbpress-support' ),
			),
			'get_topic_close_link'
		);

		$display = bbp_is_topic_open( $topic->ID ) ? $args['close_text'] : $args['open_text'];
		$uri     = add_query_arg(
			array(
				'action'   => 'snow_monkey_bbpress_support_toggle_topic_close',
				'topic_id' => $topic->ID,
			)
		);
		$uri     = wp_nonce_url( $uri, 'close-topic_' . $topic->ID );

		return sprintf(
			'<a href="%1$s" class="button">%2$s</a>',
			esc_url( $uri ),
			esc_html( $display )
		);
	}

	/**
	 * Add topic close link to before_replies_loop.
	 */
	public function _display_to_before_replies_loop() {
		$close_link = $this->_close_link();
		if ( ! $close_link ) {
			return;
		}
		?>
		<div class="snow-monkey-bbpress-support-my-topic-close-link">
			<?php echo wp_kses_post( $close_link ); ?>
		</div>
		<?php
	}

	/**
	 * Add topic close link to reply_form_submit_button.
	 */
	public function _display_to_after_reply_form_submit_button() {
		$close_link = $this->_close_link();
		if ( ! $close_link ) {
			return;
		}

		echo wp_kses_post( $close_link );
	}
}
