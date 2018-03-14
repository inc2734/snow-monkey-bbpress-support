<?php
/**
 * Plugin name: Snow Monkey bbPress Support
 * Version: 0.2.1
 * Text Domain: snow-monkey-bbpress-support
 * Domain Path: /language/
 *
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport;

class Bootstrap {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, '_bootstrap' ] );
	}

	public function _bootstrap() {
		load_plugin_textdomain( 'snow-monkey-bbpress-support', false, basename( __DIR__ ) . '/languages' );

		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		$theme = wp_get_theme();
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			return;
		}

		new App\Avatar();
		new App\Assets();
		new App\Sidebar();
		new App\AdminBar();
		new App\Notice();
		new App\NavMenu();
		new App\Breadcrumbs();
		new App\DocumentTitle();
		new App\Templates();
		new App\Content();
		new App\Pagination();
		new App\Comments();

		new App\Controller\Admin();
		new App\Controller\Front();
		new App\Controller\Topic();

		add_action( 'init', [ $this, '_activate_autoupdate' ] );
	}

	/**
	 * Activate auto update using GitHub
	 *
	 * @return [void]
	 */
	public function _activate_autoupdate() {
		new \Inc2734\WP_GitHub_Plugin_Updater\GitHub_Plugin_Updater( plugin_basename( __FILE__ ), 'inc2734', 'snow-monkey-bbpress-support' );
	}
}

require_once( __DIR__ . '/vendor/autoload.php' );
new \Snow_Monkey\Plugin\bbPressSupport\Bootstrap();
