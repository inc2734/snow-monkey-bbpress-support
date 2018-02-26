<?php
/**
 * Plugin name: Snow Monkey bbPress Support
 * Version: 0.1.1
 * Text Domain: snow-monkey-bbpress-support
 * Domain Path: /language/
 *
 * @todo 翻訳ファイル
 * @todo テスト組み込み
 *
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport;

class Bootstrap {

	public function __construct() {
		require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

		add_action( 'plugins_loaded', [ $this, '_bootstrap' ] );
	}

	public function _bootstrap() {
		load_plugin_textdomain( 'snow-monkey-bbpress-support', false, basename( __DIR__ ) . '/languages' );

		add_action( 'init', [ $this, '_activate_autoupdate' ] );

		new App\Assets();
		new App\Sidebar();
		new App\AdminBar();
		new App\Notice();
		new App\NavMenu();
		new App\Breadcrumbs();
		new App\DocumentTitle();

		new App\Controller\Admin();
		new App\Controller\Front();
		new App\Controller\Topic();
	}

	public function _activate_autoupdate() {
		$plugin_slug = plugin_basename( __FILE__ );
		$gh_user = 'inc2734';
		$gh_repo = 'snow-monkey-bbpress-support';
		new \Miya\WP\GH_Auto_Updater( $plugin_slug, $gh_user, $gh_repo );
	}
}

$theme = wp_get_theme();
if ( 'snow-monkey' === $theme->template || 'snow-monkey/resources' === $theme->template ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'bbpress/bbpress.php' ) ) {
		new \Snow_Monkey\Plugin\bbPressSupport\Bootstrap();
	}
}
