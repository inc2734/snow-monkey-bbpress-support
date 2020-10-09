<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Stars {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'bbp_theme_after_reply_content', [ $this, '_bbp_theme_after_reply_content' ] );
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
		add_action( 'wp_ajax_snow_monkey_bbpress_support_star', [ $this, '_update_stars' ] );
		add_action( 'wp_ajax_nopriv_snow_monkey_bbpress_support_star', [ $this, '_update_stars' ] );
		add_action( 'bbp_template_after_user_profile', [ $this, '_bbp_template_after_user_profile' ] );
		add_action( 'bbp_theme_before_reply_author_details', [ $this, '_add_user_stars_to_replies' ] );
		add_action( 'bbp_theme_after_reply_author_details', [ $this, '_stop_add_user_stars_to_replies' ] );
	}

	/**
	 * Display stars to after reply content.
	 */
	public function _bbp_theme_after_reply_content() {
		if ( ! apply_filters( 'snow_monkey_bbpress_support_display_replies_stars', '__return_true', get_the_ID() ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		$author_id    = get_the_author_meta( 'ID' );
		$button_tag   = 0 < $author_id && 0 < $current_user->ID && (int) $current_user->ID !== (int) $author_id ? 'button' : 'span';
		$stars        = $this->_get_post_stars( get_the_ID() );
		$icon         = $this->_get_icon();
		?>
		<div class="u-text-right">
			<<?php echo esc_html( $button_tag ); ?> class="smbbpress-stars" data-reply-id="<?php the_ID(); ?>" data-reply-author="<?php echo esc_attr( $author_id ); ?>">
				<span class="smbbpress-stars__stars">
					<?php echo wp_kses_post( $icon ); ?>
				</span>
				<span class="smbbpress-stars__count">
					<?php echo esc_html( $stars ); ?>
				</span>
			</<?php echo esc_html( $button_tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Add endpoint for stars.
	 */
	public function _wp_enqueue_scripts() {
		wp_localize_script(
			'snow-monkey-bbpress-support',
			'SNOW_MONKEY_BBPRESS_SUPPORT',
			[
				'endpoint' => admin_url( 'admin-ajax.php' ),
				'action'   => 'snow_monkey_bbpress_support_star',
				'secure'   => wp_create_nonce( 'SNOW_MONKEY_BBPRESS_SUPPORT' ),
			]
		);
	}

	/**
	 * Update stars.
	 */
	public function _update_stars() {
		check_ajax_referer( 'SNOW_MONKEY_BBPRESS_SUPPORT', 'secure' );

		$reply_id     = $_POST['replyId'];
		$author_id    = $_POST['authorId'];
		$current_user = wp_get_current_user();

		if ( 0 < $reply_id && 0 < $author_id && 0 < $current_user->ID && (int) $current_user->ID !== (int) $author_id ) {
			$stars     = $this->_get_post_stars( $reply_id );
			$new_stars = $stars + 1;
			update_post_meta( $reply_id, 'smbbpress-support-stars', $new_stars );

			$stars     = $this->_get_user_stars( $author_id );
			$new_stars = $stars + 1;
			update_user_meta( $author_id, 'smbbpress-support-stars', $new_stars );
		}

		$new_stars = $this->_get_post_stars( $reply_id );

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode(
			[
				'stars' => $new_stars,
			]
		);
		die();
	}

	/**
	 * Display stars to profile page.
	 */
	public function _bbp_template_after_user_profile() {
		?>
		<div class="bbp-user-section">
			<span></span>
			<hr>
			<h3><?php esc_html_e( 'Additional Information', 'snow-monkey-bbpress-support' ); ?></h3>
			<p>
				<?php
				$user  = bbpress()->displayed_user;
				$stars = $this->_get_user_stars( $user->ID );
				?>
				<?php esc_html_e( 'Total likes', 'snow-monkey-bbpress-support' ); ?>: <?php echo esc_html( $stars ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Add bbp_get_reply_author_link callback.
	 */
	public function _add_user_stars_to_replies() {
		add_filter( 'bbp_get_reply_author_link', [ $this, '_add_user_stars_for_replies_user' ], 10, 2 );
	}

	/**
	 * Remove bbp_get_reply_author_link callback.
	 */
	public function _stop_add_user_stars_to_replies() {
		remove_filter( 'bbp_get_reply_author_link', [ $this, '_add_user_stars_for_replies_user' ], 10, 2 );
	}

	/**
	 * Display stars for user.
	 *
	 * @param string $author_link The author link.
	 * @param array  $r           Arra of default value.
	 * @return string
	 */
	public function _add_user_stars_for_replies_user( $author_link, $r ) {
		$reply_id  = bbp_get_reply_id( $r['post_id'] );
		$author_id = bbp_get_reply_author_id( $reply_id );

		if ( bbp_is_reply_anonymous( $reply_id ) ) {
			return $author_link;
		}

		ob_start();
		?>
		<span class="smbbpress-stars">
			<span class="smbbpress-stars__stars"><?php echo wp_kses_post( $this->_get_icon() ); ?></span>
			<span class="smbbpress-stars__count"><?php echo wp_kses_post( $this->_get_user_stars( $author_id ) ); ?></span>
		</span>
		<?php
		$user_stars = ob_get_clean();

		return $author_link . $user_stars;
	}

	/**
	 * Return stars count of the post.
	 *
	 * @param int $post_id The post ID.
	 * @return int
	 */
	protected function _get_post_stars( $post_id ) {
		$stars = get_post_meta( $post_id, 'smbbpress-support-stars', true );
		$stars = $stars ? $stars : 0;
		return $stars;
	}

	/**
	 * Return stars count of the user.
	 *
	 * @param int $user_id The user ID.
	 * @return int
	 */
	protected function _get_user_stars( $user_id ) {
		$stars = get_user_meta( $user_id, 'smbbpress-support-stars', true );
		$stars = $stars ? $stars : 0;
		return $stars;
	}

	/**
	 * Return icon.
	 *
	 * @return string
	 */
	protected function _get_icon() {
		$icon = '&hearts;';
		$icon = apply_filters( 'snow_monkey_bbpress_support_replies_stars_icon', $icon );
		return $icon;
	}
}
