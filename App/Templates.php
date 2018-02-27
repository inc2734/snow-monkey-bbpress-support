<?php
/**
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport\App;

class Templates {

	public function __construct() {
		add_filter( 'bbp_get_bbpress_template', [ $this, '_bbp_get_bbpress_template' ] );
	}

	/**
	 * Support singular.php ( For bbPress 2.5.x)
	 *
	 * @param  [array] $templates
	 * @return [array]
	 */
	public function _bbp_get_bbpress_template( $templates ) {
		if ( in_array( 'singular.php', $templates ) ) {
			return $templates;
		}

		$position = array_search( 'index.php', $templates );
		array_splice( $templates, $position, 0, [ 'singular.php' ] );

		return $templates;
	}
}
