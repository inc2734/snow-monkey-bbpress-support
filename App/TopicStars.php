<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class TopicStars {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'inc2734_wp_view_controller_expand_get_template_part', [ $this, '_expand_get_template_part' ], 11, 2 );
		add_action( 'snow_monkey_template_part_render_template-parts/content/entry/header/header', [ $this, '_display_topic_star' ] );
		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ] );
		add_action( 'wp_ajax_snow_monkey_bbpress_support_topic_star', [ $this, '_update_stars' ] );
		add_action( 'wp_ajax_nopriv_snow_monkey_bbpress_support_topic_star', [ $this, '_update_stars' ] );
		add_action( 'bbp_template_after_user_profile', [ $this, '_bbp_template_after_user_profile' ] );
		add_action( 'bbp_theme_after_topic_title', [ $this, '_bbp_theme_after_topic_title' ] );
	}

	/**
	 * Expand get_template_part().
	 *
	 * @param boolean $expand If true, expand get_template_part().
	 * @param array   $args   The template part args.
	 * @return boolean
	 */
	public function _expand_get_template_part( $expand, $args ) {
		if ( 'template-parts/content/entry/header/header' === $args['slug'] ) {
			return true;
		}
		return $expand;
	}

	/**
	 * Display stars to after topic title.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public function _display_topic_star( $html ) {
		if ( ! bbp_is_single_topic() ) {
			return $html;
		}

		if ( ! apply_filters( 'snow_monkey_bbpress_support_display_topic_stars', '__return_true', bbp_get_topic_id() ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		$author_id    = bbp_get_topic_author_id();
		$button_tag   = 0 < $author_id && 0 < $current_user->ID && (int) $current_user->ID !== (int) $author_id ? 'button' : 'span';
		$stars        = $this->_get_topic_stars( bbp_get_topic_id() );
		$stars_users  = $this->_get_topic_stars_users( bbp_get_topic_id() );
		$stars_users  = $this->_user_ids_to_names( $stars_users );
		$icon         = $this->_get_icon();

		ob_start();
		?>
		<div class="smbbpress-stars-wrapper">
			<div class="smbbpress-stars-wrapper__button">
				<<?php echo esc_html( $button_tag ); ?> class="smbbpress-stars smbbpress-topic-stars" data-topic-id="<?php bbp_topic_id(); ?>" data-topic-author="<?php echo esc_attr( $author_id ); ?>" title="<?php echo esc_attr_e( 'Like this topic', 'snow-monkey-bbpress-support' ); ?>">
					<span class="smbbpress-stars__stars">
						<?php echo wp_kses_post( $icon ); ?>
					</span>
					<span class="smbbpress-stars__count">
						<?php echo esc_html( $stars ); ?>
					</span>
				</<?php echo esc_html( $button_tag ); ?>>
			</div>
			<div class="smbbpress-stars-wrapper__users">
				<div class="smbbpress-stars-users">
					<span class="smbbpress-stars-users__label"><?php esc_html_e( 'Who liked:', 'snow-monkey-bbpress-support' ); ?></span>
					<span class="smbbpress-stars-users__users">
						<?php if ( 0 === $stars || empty( $stars_users ) ) : ?>
							<span class="smbbpress-stars-users__no-user-label"><?php esc_html_e( 'No user', 'snow-monkey-bbpress-support' ); ?></span>
						<?php else : ?>
							<?php foreach ( $stars_users as $user_id => $name ) : ?>
								<div class="smbbpress-stars-users__user">
									<a href="<?php echo esc_attr( esc_url( bbp_get_user_profile_url( $user_id ) ) ); ?>" title="<?php echo esc_attr( $name ); ?>">
										<?php echo get_avatar( $user_id, 96, '', $name ); ?>
									</a>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
		<?php
		$topic_star = ob_get_clean();
		return str_replace( '</header>', $topic_star . '</header>', $html );
	}

	/**
	 * Add endpoint for stars.
	 */
	public function _wp_enqueue_scripts() {
		wp_localize_script(
			'snow-monkey-bbpress-support',
			'SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS',
			[
				'endpoint' => admin_url( 'admin-ajax.php' ),
				'action'   => 'snow_monkey_bbpress_support_topic_star',
				'secure'   => wp_create_nonce( 'SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS' ),
			]
		);
	}

	/**
	 * Update stars.
	 */
	public function _update_stars() {
		check_ajax_referer( 'SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS', 'secure' );

		$topic_id     = $_POST['topicId'];
		$author_id    = $_POST['authorId'];
		$current_user = wp_get_current_user();

        if ( 0 < $topic_id && 0 < $author_id && 0 < $current_user->ID && (int) $current_user->ID !== (int) $author_id && ! in_array( $current_user->ID, $this->_get_topic_stars_users( $topic_id ), true ) ) {
			$stars     = $this->_get_topic_stars( $topic_id );
			$new_stars = $stars + 1;
			update_post_meta( $topic_id, 'smbbpress-support-topic-stars', $new_stars );

			$users     = $this->_get_topic_stars_users( $topic_id );
			$users[]   = $current_user->ID;
			$new_users = array_unique( $users );
			update_post_meta( $topic_id, 'smbbpress-support-topic-stars-users', $new_users );

			$stars     = $this->_get_user_stars( $author_id );
			$new_stars = $stars + 1;
			update_user_meta( $author_id, 'smbbpress-support-topic-stars', $new_stars );
		}

		$new_stars = $this->_get_topic_stars( $topic_id );

		$stars_users     = $this->_get_topic_stars_users( $topic_id );
		$stars_users     = $this->_user_ids_to_names( $stars_users );
		$new_stars_users = '';

		foreach ( $stars_users as $id => $name ) {
			$new_stars_users .= '<a href="' . esc_attr( esc_url( bbp_get_user_profile_url( $id ) ) ) . '">' . esc_html( $name ) . '</a>';
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode(
			[
				'stars' => $new_stars,
				'users' => $new_stars_users,
			]
		);
		die();
	}

	/**
	 * Display stars to profile page.
	 */
	public function _bbp_template_after_user_profile() {
		?>
		<p>
			<?php
			$user  = bbpress()->displayed_user;
			$stars = $this->_get_user_stars( $user->ID );
			?>
			<?php esc_html_e( 'Total likes (Topics)', 'snow-monkey-bbpress-support' ); ?>: <?php echo esc_html( $stars ); ?>
		</p>
		<?php
	}

	/**
	 * Display the topic stars to topic loop.
	 */
	public function _bbp_theme_after_topic_title() {
		?>
		<div class="smbbpress-stars-wrapper">
			<div class="smbbpress-stars-wrapper__button">
				<div class="smbbpress-stars">
					<span class="smbbpress-stars__stars"><?php echo wp_kses_post( $this->_get_icon() ); ?></span>
					<span class="smbbpress-stars__count"><?php echo wp_kses_post( $this->_get_topic_stars( bbp_get_topic_id() ) ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Return stars count of the topic.
	 *
	 * @param int $topic_id The topic ID.
	 * @return int
	 */
	protected function _get_topic_stars( $topic_id ) {
		$stars = get_post_meta( $topic_id, 'smbbpress-support-topic-stars', true );
		$stars = $stars ? $stars : 0;
		return $stars;
	}

	/**
	 * Return stars users of the topic.
	 *
	 * @param int $topic_id The topic ID.
	 * @return array
	 */
	protected function _get_topic_stars_users( $topic_id ) {
		$users = get_post_meta( $topic_id, 'smbbpress-support-topic-stars-users', true );
		$users = $users ? $users : [];
		return $users;
	}

	/**
	 * Return user display names.
	 *
	 * @param array $user_ids Array of user ID.
	 * @return array $names
	 */
	protected function _user_ids_to_names( $user_ids ) {
		$names = [];
		foreach ( $user_ids as $user_id ) {
			$userdata          = get_userdata( $user_id );
			$names[ $user_id ] = $userdata->display_name;
		}
		return $names;
	}

	/**
	 * Return stars count of the user.
	 *
	 * @param int $user_id The user ID.
	 * @return int
	 */
	protected function _get_user_stars( $user_id ) {
		$stars = get_user_meta( $user_id, 'smbbpress-support-topic-stars', true );
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
