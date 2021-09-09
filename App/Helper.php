<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Helper {

	/**
	 * Return true when the user can access dashboard.
	 *
	 * @return boolean
	 */
	public static function is_prevent_admin_access() {
		$is_prevent_admin_access = ! current_user_can( 'edit_posts' );
		return apply_filters(
			'snow_monkey_bbpress_support_prevent_admin_access',
			$is_prevent_admin_access
		);
	}
}
