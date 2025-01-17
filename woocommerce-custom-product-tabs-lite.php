<?php
/**
 * Plugin Name: Custom Product Tabs Lite for WooCommerce
 * Plugin URI: https://www.skyverge.com/product/woocommerce-custom-product-tabs-lite/
 * Description: Extends WooCommerce to add a custom product view page tab
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com/
 * Version: 1.9.1-dev.1
 * Tested up to: 6.6.2
 * Text Domain: woocommerce-custom-product-tabs-lite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2023, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * WC requires at least: 3.9.4
 * WC tested up to: 9.3.3
 */

use WooCommerceCustomProductTabsLite\Helpers\ProductTabsMetaHandler;
use WooCommerceCustomProductTabsLite\Plugin;

defined( 'ABSPATH' ) or exit;

// Check if WooCommerce is active & at least the minimum version, and bail if it's not
if ( ! WooCommerceCustomProductTabsLite::is_plugin_active( 'woocommerce.php' ) || version_compare( get_option( 'woocommerce_db_version' ), WooCommerceCustomProductTabsLite::MIN_WOOCOMMERCE_VERSION, '<' ) ) {
	add_action( 'admin_notices', array( 'WooCommerceCustomProductTabsLite', 'render_woocommerce_requirements_notice' ) );
	return;
}

/**
 * Main plugin class WooCommerceCustomProductTabsLite.
 *
 * @since 1.0.0
 */
class WooCommerceCustomProductTabsLite {

	/** plugin version number */
	const VERSION = '1.9.1-dev.1';

	/** required WooCommerce version number */
	const MIN_WOOCOMMERCE_VERSION = '3.9.4';

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_custom_product_tabs_lite_db_version';

	/** @var WooCommerceCustomProductTabsLite single instance of this plugin */
	protected static $instance;

	public Plugin $plugin;

	public ProductTabsMetaHandler $metaHandler;

	/**
	 * Gets things started by adding an action to initialize this plugin once WooCommerce is known to be active and initialized.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$this->install();
		}

		$this->includes();
		$this->addHooks();
	}

	/**
	 * Loads required files.
	 *
	 * @return void
	 */
	public function includes()
	{
		require_once(__DIR__ . '/src/Plugin.php');
		require_once(__DIR__ . '/src/Helpers/ProductTabsHelper.php');
	}


	/**
	 * Registers hook callbacks to bootstrap the plugin.
	 *
	 * @return void
	 */
	public function addHooks()
	{
		add_action('init',             [$this, 'load_translation']);
		add_action('woocommerce_init', [$this, 'init']);

		// handle HPOS compatibility
		add_action('before_woocommerce_init', [$this, 'handle_hpos_compatibility']);
	}

	public function init()
	{
		$this->plugin = new Plugin;
		$this->metaHandler = new ProductTabsMetaHandler;

		$this->plugin->init();
	}

	/**
	 * Declares HPOS compatibility.
	 *
	 * @since 1.8.0
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function handle_hpos_compatibility()
	{
		if ( class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.5.0
	 */
	public function __clone() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'woocommerce-custom-product-tabs-lite' ), 'Custom Product Tabs Lite for WooCommerce' ), '1.5.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.5.0
	 */
	public function __wakeup() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'woocommerce-custom-product-tabs-lite' ), 'Custom Product Tabs Lite for WooCommerce' ), '1.5.0' );
	}


	/**
	 * Load translations
	 *
	 * @since 1.2.5
	 */
	public function load_translation() {
		// localization
		load_plugin_textdomain( 'woocommerce-custom-product-tabs-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
	}


	/**
	 * Main Custom Product Tabs Lite Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.4.0
	 * @see wc_custom_product_tabs_lite()
	 * @return WooCommerceCustomProductTabsLite
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Helper function to determine whether a plugin is active.
	 *
	 * @since 1.6.3
	 *
	 * @param string $plugin_name plugin name, as the plugin-filename.php
	 * @return boolean true if the named plugin is installed and active
	 */
	public static function is_plugin_active( $plugin_name ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$plugin_filenames = array();

		foreach ( $active_plugins as $plugin ) {

			if ( false !== strpos( $plugin, '/' ) ) {

				// normal plugin name (plugin-dir/plugin-filename.php)
				list( , $filename ) = explode( '/', $plugin );

			} else {

				// no directory, just plugin file
				$filename = $plugin;
			}

			$plugin_filenames[] = $filename;
		}

		return in_array( $plugin_name, $plugin_filenames );
	}


	/**
	 * Renders a notice when WooCommerce is inactive or version is outdated.
	 *
	 * @since 1.6.0
	 */
	public static function render_woocommerce_requirements_notice() {

		$message = sprintf(
			/* translators: Placeholders: %1$s - <strong>, %2$s - </strong>, %3$s - version number, %4$s + %6$s - <a> tags, %5$s - </a> */
			esc_html__( '%1$sCustom Product Tabs Lite for WooCommerce is inactive.%2$s This plugin requires WooCommerce %3$s or newer. Please %4$sinstall WooCommerce %3$s or newer%5$s, or %6$srun the WooCommerce database upgrade%5$s.', 'woocommerce-custom-product-tabs-lite' ),
			'<strong>',
			'</strong>',
			self::MIN_WOOCOMMERCE_VERSION,
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'</a>',
			'<a href="' . admin_url( 'plugins.php?do_update_woocommerce=true' ) . '">'
		);

		printf( '<div class="error"><p>%s</p></div>', $message );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 1.0.0
	 */
	private function install() {

		$installed_version = get_option( self::VERSION_OPTION_NAME );

		// installed version lower than plugin version?
		if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			// new version number
			update_option( self::VERSION_OPTION_NAME, self::VERSION );
		}
	}


}


/**
 * Returns the One True Instance of Custom Product Tabs Lite
 *
 * @since 1.4.0
 * @return \WooCommerceCustomProductTabsLite
 */
function wc_custom_product_tabs_lite() {
	return WooCommerceCustomProductTabsLite::instance();
}


// fire it up!
wc_custom_product_tabs_lite();
