<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Avatar {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'get_avatar', [ $this, '_get_avatar' ], 10, 5 );
	}

	/**
	 * If using facebook of Gianism, facebook user picture is used as avatar.
	 *
	 * @param string $img         HTML for the user's avatar. Default null.
	 * @param string $id_or_email The avatar to retrieve. Accepts a user_id, Gravatar MD5 hash,
	 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param int    $size        Square avatar width and height in pixels to retrieve.
	 * @param string $default     URL for the default image or a default type. Accepts '404',
	 *                            'retro', 'monsterid', 'wavatar', 'indenticon', 'mystery', 'mm',
	 *                            'mysteryman', 'blank', or 'gravatar_default'.
	 *                            Default is the value of the 'avatar_default' option,
	 *                            with a fallback of 'mystery'.
	 * @param string $alt         Alternative text to use in the avatar image tag. Default empty.
	 * @return string
	 */
	public function _get_avatar( $img, $id_or_email, $size, $default, $alt ) {
		if ( ! is_bbpress() ) {
			return $img;
		}

		if ( ! is_int( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			if ( ! $user ) {
				return $img;
			}
			$id_or_email = $user->ID;
		}

		$_wpg_facebook_id = get_the_author_meta( '_wpg_facebook_id', $id_or_email );
		if ( ! $_wpg_facebook_id ) {
			return $img;
		}

		$img = sprintf(
			'<img src="https://graph.facebook.com/%1$s/picture?type=large" alt="%2$s" width="%3$s" height="%3$s" class="avatar photo" />',
			esc_attr( $_wpg_facebook_id ),
			esc_attr( $alt ),
			esc_attr( $size )
		);

		return $img;
	}
}
