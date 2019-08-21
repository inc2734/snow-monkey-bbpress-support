<?php
/**
 * Plugin name: Snow Monkey bbPress Support
 * Version: 0.5.0
 * Text Domain: snow-monkey-bbpress-support
 * Domain Path: /languages/
 *
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport;

use Inc2734\WP_GitHub_Plugin_Updater\Bootstrap as Updater;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Bootstrap {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, '_bootstrap' ] );
	}

	public function _bootstrap() {
		load_plugin_textdomain( 'snow-monkey-bbpress-support', false, basename( __DIR__ ) . '/languages' );

		add_action( 'init', [ $this, '_activate_autoupdate' ] );

		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			add_action( 'admin_notices', [ $this, '_admin_notice_no_snow_monkey' ] );
			return;
		}

		if ( ! class_exists( 'bbPress' ) ) {
			add_action( 'admin_notices', [ $this, '_admin_notice_no_bbpress' ] );
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
		new App\SubscribeLink();
		new App\Comments();
		new App\Rest();

		new App\Controller\Admin();
		new App\Controller\Front();
		new App\Controller\Topic();
	}

	/**
	 * Activate auto update using GitHub
	 *
	 * @return [void]
	 */
	public function _activate_autoupdate() {
		new Updater( plugin_basename( __FILE__ ), 'inc2734', 'snow-monkey-bbpress-support' );
	}

	/**
	 * Admin notice for no Snow Monkey
	 *
	 * @return void
	 */
	public function _admin_notice_no_snow_monkey() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( '[Snow Monkey bbPress Support] Needs the Snow Monkey.', 'snow-monkey-bbpress-support' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Admin notice for no bbPress
	 *
	 * @return void
	 */
	public function _admin_notice_no_bbpress() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( '[Snow Monkey bbPress Support] Needs the bbPress.', 'snow-monkey-bbpress-support' ); ?>
			</p>
		</div>
		<?php
	}
}

require_once( __DIR__ . '/vendor/autoload.php' );
new \Snow_Monkey\Plugin\bbPressSupport\Bootstrap();
