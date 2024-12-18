<?php
/**
 * Plugin name: Snow Monkey bbPress Support
 * Version: 0.18.5
 * Tested up to: 6.7
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Description: This plugin makes Snow Monkey beautifully display bbPress and adds some features.
 * Author: inc2734
 * Author URI: https://2inc.org
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: snow-monkey-bbpress-support
 *
 * @package snow-monkey-bbpress-support
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\bbPressSupport;

define( 'SNOW_MONKEY_BBPRESS_SUPPORT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SNOW_MONKEY_BBPRESS_SUPPORT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

class Bootstrap {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, '_bootstrap' ) );
	}

	/**
	 * Bootstrap.
	 */
	public function _bootstrap() {
		add_action( 'init', array( $this, '_load_textdomain' ) );

		new App\Updater();

		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			add_action(
				'admin_notices',
				function () {
					?>
					<div class="notice notice-warning is-dismissible">
						<p>
							<?php esc_html_e( '[Snow Monkey bbPress Support] Needs the Snow Monkey.', 'snow-monkey-bbpress-support' ); ?>
						</p>
					</div>
					<?php
				}
			);
			return;
		}

		if ( ! class_exists( 'bbPress' ) ) {
			add_action( 'admin_notices', array( $this, '_admin_notice_no_bbpress' ) );
			return;
		}

		new App\Author();
		new App\Avatar();
		new App\Assets();
		new App\Sidebar();
		new App\AdminBar();
		new App\NavMenu();
		new App\Breadcrumbs();
		new App\DocumentTitle();
		new App\Templates();
		new App\Content();
		new App\Pagination();
		new App\SubscribeLink();
		new App\Comments();
		new App\Rest();
		new App\Search();
		new App\Post();

		if ( apply_filters( 'snow_monkey_bbpress_support_activate_notice_feature', '__return_true' ) ) {
			new App\Notice();
		}

		if ( apply_filters( 'snow_monkey_bbpress_support_activate_replies_stars_feature', '__return_true' ) ) {
			new App\Stars();
		}

		if ( apply_filters( 'snow_monkey_bbpress_support_activate_topic_stars_feature', '__return_true' ) ) {
			new App\TopicStars();
		}

		if ( apply_filters( 'snow_monkey_bbpress_support_activate_topic_close_link_feature', '__return_true' ) ) {
			new App\TopicCloseLink();
		}

		new App\Controller\Admin();
		new App\Controller\Front();
		new App\Controller\Customizer();
	}

	/**
	 * Load textdomain.
	 */
	public function _load_textdomain() {
		load_plugin_textdomain( 'snow-monkey-bbpress-support', false, basename( __DIR__ ) . '/languages' );
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

require_once __DIR__ . '/vendor/autoload.php';
new \Snow_Monkey\Plugin\bbPressSupport\Bootstrap();
