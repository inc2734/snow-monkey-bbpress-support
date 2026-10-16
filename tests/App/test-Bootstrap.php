<?php
/**
 * @package snow-monkey-bbpress-support
 */

namespace Snow_Monkey\Plugin\bbPressSupport\tests\App;

class Bootstrap extends \WP_UnitTestCase {

	/**
	 * Register bundled translations before sidebar strings trigger JIT loading.
	 */
	public function test_bootstrap_registers_translations_before_widgets() {
		global $wp_textdomain_registry;

		$domain   = 'snow-monkey-bbpress-support';
		$registry = $wp_textdomain_registry;
		$locale   = static function () {
			return 'ja';
		};
		unload_textdomain( $domain, true );
		$wp_textdomain_registry = new \WP_Textdomain_Registry();
		$wp_textdomain_registry->set_custom_path( $domain, SNOW_MONKEY_BBPRESS_SUPPORT_PATH );
		add_filter( 'pre_determine_locale', $locale );

		try {
			$bootstrap = new \Snow_Monkey\Plugin\bbPressSupport\Bootstrap();
			$bootstrap->_bootstrap();
			$sidebar = new \Snow_Monkey\Plugin\bbPressSupport\App\Sidebar();
			$sidebar->_widgets_init();

			$this->assertSame( 'トピックを閉じる', __( 'Close this topic', 'snow-monkey-bbpress-support' ) );
			$this->assertSame( 'トピックを開く', __( 'Open this topic', 'snow-monkey-bbpress-support' ) );
		} finally {
			remove_filter( 'pre_determine_locale', $locale );
			unload_textdomain( $domain, true );
			$wp_textdomain_registry = $registry;
			\WP_Translation_Controller::get_instance()->set_locale( determine_locale() );
		}
	}
}
