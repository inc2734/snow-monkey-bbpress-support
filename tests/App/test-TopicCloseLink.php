<?php
/**
 * @package snow-monkey-bbpress-support
 */

namespace Snow_Monkey\Plugin\bbPressSupport\tests\App;

class TopicCloseRedirect extends \RuntimeException {}

class TopicCloseLink extends \WP_UnitTestCase {

	/**
	 * Topic author ID.
	 *
	 * @var integer
	 */
	protected $author_id;

	/**
	 * Topic ID.
	 *
	 * @var integer
	 */
	protected $topic_id;

	/**
	 * Create a participant and their topic.
	 */
	public function set_up() {
		parent::set_up();
		set_current_screen( 'front' );
		$this->author_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		bbp_set_user_role( $this->author_id, bbp_get_participant_role() );
		wp_set_current_user( $this->author_id );
		$forum_id       = bbp_insert_forum(
			array(
				'post_title'  => 'Forum',
				'post_author' => self::factory()->user->create(),
			)
		);
		$this->topic_id = bbp_insert_topic(
			array(
				'post_title'  => 'Topic',
				'post_author' => $this->author_id,
				'post_parent' => $forum_id,
			),
			array( 'forum_id' => $forum_id )
		);
	}

	/**
	 * Authors can close and reopen their topics without moderation privileges.
	 */
	public function test_author_can_close_and_reopen_topic() {
		$this->assertFalse( current_user_can( 'moderate', $this->topic_id ) );
		$this->assertSame( bbp_get_topic_permalink( $this->topic_id ), $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_closed( $this->topic_id ) );
		$this->assertSame( bbp_get_topic_permalink( $this->topic_id ), $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
		$this->assertFalse( current_user_can( 'moderate', $this->topic_id ) );
	}

	/**
	 * Other users cannot use the author-only action.
	 */
	public function test_other_user_cannot_toggle_topic() {
		$user_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		bbp_set_user_role( $user_id, bbp_get_moderator_role() );
		wp_set_current_user( $user_id );
		$this->assertNull( $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
	}

	/**
	 * Logged-out visitors cannot toggle topics.
	 */
	public function test_anonymous_user_cannot_toggle_topic() {
		wp_set_current_user( 0 );
		$this->assertNull( $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
	}

	/**
	 * Blocked authors cannot toggle their topics.
	 */
	public function test_blocked_author_cannot_toggle_topic() {
		bbp_set_user_role( $this->author_id, bbp_get_blocked_role() );
		$this->assertNull( $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
	}

	/**
	 * A valid nonce for the requested topic is required.
	 */
	public function test_invalid_nonces_cannot_toggle_topic() {
		foreach ( array( '', 'invalid', array( 'invalid' ), wp_create_nonce( 'close-topic_999999' ) ) as $nonce ) {
			$this->assertNull( $this->request_toggle( $this->topic_id, $nonce ) );
			$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
		}
	}

	/**
	 * Authors cannot restore topics hidden by moderation.
	 */
	public function test_non_public_topics_cannot_be_toggled() {
		$forum_id = bbp_get_topic_forum_id( $this->topic_id );
		foreach ( array( bbp_get_spam_status_id(), bbp_get_pending_status_id(), bbp_get_trash_status_id() ) as $status ) {
			$topic_id = bbp_insert_topic(
				array(
					'post_author' => $this->author_id,
					'post_parent' => $forum_id,
					'post_status' => $status,
				),
				array( 'forum_id' => $forum_id )
			);
			$this->assertSame( $status, get_post_status( $topic_id ) );
			$this->assertNull( $this->request_toggle( $topic_id ) );
			$this->assertSame( $status, get_post_status( $topic_id ) );
		}
	}

	/**
	 * Authors cannot change topics after losing access to the parent forum.
	 */
	public function test_inaccessible_forum_topic_cannot_be_toggled() {
		bbp_hide_forum( bbp_get_topic_forum_id( $this->topic_id ) );
		$this->assertNull( $this->request_toggle( $this->topic_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
	}

	/**
	 * Non-topic posts and invalid IDs cannot be changed.
	 */
	public function test_invalid_targets_cannot_be_toggled() {
		$post_id = self::factory()->post->create( array( 'post_author' => $this->author_id ) );
		foreach ( array( $post_id, 999999, 0, -$this->topic_id, 'invalid', array( $this->topic_id ) ) as $topic_id ) {
			$this->assertNull( $this->request_toggle( $topic_id, 'invalid' ) );
		}
		$this->assertSame( 'publish', get_post_status( $post_id ) );
		$this->assertTrue( bbp_is_topic_open( $this->topic_id ) );
	}

	/**
	 * Links use the plugin action and disappear for ineligible users or statuses.
	 */
	public function test_close_link_matches_handler_permissions() {
		$GLOBALS['post'] = get_post( $this->topic_id );
		$link            = new \Snow_Monkey\Plugin\bbPressSupport\App\TopicCloseLink();
		ob_start();
		$link->_display_to_after_reply_form_submit_button();
		$html = ob_get_clean();
		$this->assertStringContainsString( 'action=snow_monkey_bbpress_support_toggle_topic_close', $html );
		$this->assertStringContainsString( '_wpnonce=', $html );
		wp_update_post( array( 'ID' => $this->topic_id, 'post_status' => bbp_get_spam_status_id() ) );
		$GLOBALS['post'] = get_post( $this->topic_id );
		ob_start();
		$link->_display_to_after_reply_form_submit_button();
		$this->assertSame( '', ob_get_clean() );
	}

	/**
	 * Submit a real bbPress GET request without terminating the test on redirect.
	 *
	 * @param mixed $topic_id Requested topic ID.
	 * @param mixed $nonce    Nonce, or null to generate a valid one.
	 * @return string|null Redirect location on success.
	 */
	protected function request_toggle( $topic_id, $nonce = null ) {
		$previous_get     = $_GET;
		$previous_request = $_REQUEST;
		$previous_method  = $_SERVER['REQUEST_METHOD'];
		$_GET             = array(
			'action'   => 'snow_monkey_bbpress_support_toggle_topic_close',
			'topic_id' => $topic_id,
			'_wpnonce' => null === $nonce ? wp_create_nonce( 'close-topic_' . $topic_id ) : $nonce,
		);
		$_REQUEST                 = $_GET;
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$redirect                 = static function ( $location ) {
			throw new TopicCloseRedirect( $location );
		};
		add_filter( 'wp_redirect', $redirect );

		try {
			bbp_get_request();
		} catch ( TopicCloseRedirect $exception ) {
			return $exception->getMessage();
		} finally {
			remove_filter( 'wp_redirect', $redirect );
			$_GET                      = $previous_get;
			$_REQUEST                  = $previous_request;
			$_SERVER['REQUEST_METHOD'] = $previous_method;
		}

		return null;
	}
}
