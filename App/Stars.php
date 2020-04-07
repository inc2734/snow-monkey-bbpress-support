<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Stars {

	public function __construct() {
		add_action( 'bbp_theme_after_reply_content', [ $this, '_bbp_theme_after_reply_content' ] );
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
		add_action( 'wp_ajax_snow_monkey_bbpress_support_star', [ $this, '_update_stars' ] );
		add_action( 'wp_ajax_nopriv_snow_monkey_bbpress_support_star', [ $this, '_update_stars' ] );
		add_action( 'bbp_template_after_user_profile', [ $this, '_bbp_template_after_user_profile' ] );
	}

	public function _bbp_theme_after_reply_content() {
		if ( ! apply_filters( 'snow_monkey_bbpress_support_display_replies_stars', '__return_true', get_the_ID() ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		$author_id    = get_the_author_meta( 'ID' );
		$button_tag   = 0 < $author_id && 0 < $current_user->ID && $current_user->ID != $author_id ? 'button' : 'span';
		$stars        = get_post_meta( get_the_ID(), 'smbbpress-support-stars', true );
		$stars        = $stars ? $stars : 0;

		$icon = sprintf(
			'<span class="fas fa-heart" aria-hidden="true" title="%1$s"></span>',
			esc_attr__( 'Like', 'snow-monkey-bbpress-support' )
		);
		$icon = apply_filters( 'snow_monkey_bbpress_support_replies_stars_icon', $icon );
		?>
		<div class="u-text-right">
			<<?php echo esc_html( $button_tag ); ?> class="smbbpress-stars" data-reply-id="<?php the_ID(); ?>" data-reply-author="<?php echo esc_attr( $author_id ); ?>">
				<span class="smbbpress-stars__stars">
					<?php echo esc_html( $icon ); ?>
				</span>
				<span class="smbbpress-stars__count">
					<?php echo esc_html( $stars ); ?>
				</span>
			</<?php echo esc_html( $button_tag ); ?>>
		</div>
		<?php
	}

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
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function _update_stars() {
		check_ajax_referer( 'SNOW_MONKEY_BBPRESS_SUPPORT', 'secure' );

		$reply_id     = $_POST['replyId'];
		$author_id    = $_POST['authorId'];
		$current_user = wp_get_current_user();

		if ( 0 < $reply_id && 0 < $author_id && 0 < $current_user->ID && $current_user->ID != $author_id ) {
			$stars     = get_post_meta( $reply_id, 'smbbpress-support-stars', true );
			$stars     = $stars ? $stars : 0;
			$new_stars = $stars + 1;
			update_post_meta( $reply_id, 'smbbpress-support-stars', $new_stars );

			$stars     = get_user_meta( $author_id, 'smbbpress-support-stars', true );
			$stars     = $stars ? $stars : 0;
			$new_stars = $stars + 1;
			update_user_meta( $author_id, 'smbbpress-support-stars', $new_stars );
		}

		$new_stars = get_post_meta( $reply_id, 'smbbpress-support-stars', true );
		$new_stars = $new_stars ? $new_stars : 0;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode(
			[
				'stars' => $new_stars,
			]
		);
		die();
	}

	public function _bbp_template_after_user_profile() {
		?>
		<div class="bbp-user-section">
			<span></span>
			<hr>
			<h3><?php esc_html_e( 'Additional Information', 'snow-monkey-bbpress-support' ); ?></h3>
			<p>
				<?php
				$user  = bbpress()->displayed_user;
				$stars = get_user_meta( $user->ID, 'smbbpress-support-stars', true );
				$stars = $stars ? $stars : 0;
				?>
				<?php esc_html_e( 'Total likes', 'snow-monkey-bbpress-support' ); ?>: <?php echo esc_html( $stars ); ?>
			</p>
		</div>
		<?php
	}
}
