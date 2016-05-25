<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs Lite
 * Plugin URI: https://www.skyverge.com/product/woocommerce-custom-product-tabs-lite/
 * Description: Extends WooCommerce to add a custom product view page tab
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com/
 * Version: 1.5.0
 * Tested up to: 4.4
 * Text Domain: woocommerce-custom-product-tabs-lite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2016 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Custom-Product-Tabs-Lite
 * @author      SkyVerge
 * @category    Plugin
 * @copyright   Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// Check if WooCommerce is active and bail if it's not
if ( ! WooCommerceCustomProductTabsLite::is_woocommerce_active() ) {
	return;
}

class WooCommerceCustomProductTabsLite {

	private $tab_data = false;

	/** plugin version number */
	const VERSION = '1.5.0';

	/** @var WooCommerceCustomProductTabsLite single instance of this plugin */
	protected static $instance;

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_custom_product_tabs_lite_db_version';


	/**
	 * Gets things started by adding an action to initialize this plugin once
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();

		add_action( 'init',             array( $this, 'load_translation' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.5.0
	 */
	public function __clone() {

		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'woocommerce-custom-product-tabs-lite' ), 'WooCommerce Custom Product Tabs Lite' ), '1.5.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.5.0
	 */
	public function __wakeup() {

		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'woocommerce-custom-product-tabs-lite' ), 'WooCommerce Custom Product Tabs Lite' ), '1.5.0' );
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
	 * Init WooCommerce Product Tabs Lite extension once we know WooCommerce is active
	 */
	public function init() {
		// backend stuff
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tab' ) );
		add_action( 'woocommerce_product_write_panels',     array( $this, 'product_write_panel' ) );
		add_action( 'woocommerce_process_product_meta',     array( $this, 'product_save_data' ), 10, 2 );

		// frontend stuff
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs' ) );

		// allow the use of shortcodes within the tab content
		add_filter( 'woocommerce_custom_product_tabs_lite_content', 'do_shortcode' );
	}


	/** Frontend methods ******************************************************/


	/**
	 * Add the custom product tab
	 *
	 * $tabs structure:
	 * Array(
	 *   id => Array(
	 *     'title'    => (string) Tab title,
	 *     'priority' => (string) Tab priority,
	 *     'callback' => (mixed) callback function,
	 *   )
	 * )
	 *
	 * @since 1.2.0
	 * @param array $tabs array representing the product tabs
	 * @return array representing the product tabs
	 */
	public function add_custom_product_tabs( $tabs ) {
		global $product;

		if ( $this->product_has_custom_tabs( $product ) ) {
			foreach ( $this->tab_data as $tab ) {
				$tab_title = __( $tab['title'], 'woocommerce-custom-product-tabs-lite' );
				$tabs[ $tab['id'] ] = array(
					'title'    => apply_filters( 'woocommerce_custom_product_tabs_lite_title', $tab_title, $product, $this ),
					'priority' => 25,
					'callback' => array( $this, 'custom_product_tabs_panel_content' ),
					'content'  => $tab['content'],  // custom field
				);
			}
		}

		return $tabs;
	}


	/**
	 * Render the custom product tab panel content for the given $tab
	 *
	 * $tab structure:
	 * Array(
	 *   'title'    => (string) Tab title,
	 *   'priority' => (string) Tab priority,
	 *   'callback' => (mixed) callback function,
	 *   'id'       => (int) tab post identifier,
	 *   'content'  => (sring) tab content,
	 * )
	 *
	 * @param string $key tab key
	 * @param array $tab tab data
	 *
	 * @param array $tab the tab
	 */
	public function custom_product_tabs_panel_content( $key, $tab ) {

		// allow shortcodes to function
		$content = apply_filters( 'the_content', $tab['content'] );
		$content = str_replace( ']]>', ']]&gt;', $content );

		echo apply_filters( 'woocommerce_custom_product_tabs_lite_heading', '<h2>' . $tab['title'] . '</h2>', $tab );
		echo apply_filters( 'woocommerce_custom_product_tabs_lite_content', $content, $tab );
	}


	/** Admin methods ******************************************************/


	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 */
	public function product_write_panel_tab() {
		echo "<li class=\"product_tabs_lite_tab\"><a href=\"#woocommerce_product_tabs_lite\">" . __( 'Custom Tab', 'woocommerce-custom-product-tabs-lite' ) . "</a></li>";
	}


	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 */
	public function product_write_panel() {
		global $post;
		// the product

		// pull the custom tab data out of the database
		$tab_data = maybe_unserialize( get_post_meta( $post->ID, 'frs_woo_product_tabs', true ) );

		if ( empty( $tab_data ) ) {
			$tab_data[] = array( 'title' => '', 'content' => '' );
		}

		foreach ( $tab_data as $tab ) {
			// display the custom tab panel
			echo '<div id="woocommerce_product_tabs_lite" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_tab_title', 'label' => __( 'Tab Title', 'woocommerce-custom-product-tabs-lite' ), 'description' => __( 'Required for tab to be visible', 'woocommerce-custom-product-tabs-lite' ), 'value' => $tab['title'] ) );
			$this->woocommerce_wp_textarea_input( array( 'id' => '_wc_custom_product_tabs_lite_tab_content', 'label' => __( 'Content', 'woocommerce-custom-product-tabs-lite' ), 'placeholder' => __( 'HTML and text to display.', 'woocommerce-custom-product-tabs-lite' ), 'value' => $tab['content'], 'style' => 'width:70%;height:21.5em;' ) );
			echo '</div>';
		}
	}


	/**
	 * Saves the data inputed into the product boxes, as post meta data
	 * identified by the name 'frs_woo_product_tabs'
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 */
	public function product_save_data( $post_id, $post ) {

		$tab_title = stripslashes( $_POST['_wc_custom_product_tabs_lite_tab_title'] );
		$tab_content = stripslashes( $_POST['_wc_custom_product_tabs_lite_tab_content'] );

		if ( empty( $tab_title ) && empty( $tab_content ) && get_post_meta( $post_id, 'frs_woo_product_tabs', true ) ) {
			// clean up if the custom tabs are removed
			delete_post_meta( $post_id, 'frs_woo_product_tabs' );
		} elseif ( ! empty( $tab_title ) || ! empty( $tab_content ) ) {
			$tab_data = array();

			$tab_id = '';
			if ( $tab_title ) {
				if ( strlen( $tab_title ) != strlen( utf8_encode( $tab_title ) ) ) {
					// can't have titles with utf8 characters as it breaks the tab-switching javascript
					$tab_id = "tab-custom";
				} else {
					// convert the tab title into an id string
					$tab_id = strtolower( $tab_title );
					$tab_id = preg_replace( "/[^\w\s]/", '', $tab_id );
					// remove non-alphas, numbers, underscores or whitespace
					$tab_id = preg_replace( "/_+/", ' ', $tab_id );
					// replace all underscores with single spaces
					$tab_id = preg_replace( "/\s+/", '-', $tab_id );
					// replace all multiple spaces with single dashes
					$tab_id = 'tab-' . $tab_id;
					// prepend with 'tab-' string
				}
			}

			// save the data to the database
			$tab_data[] = array( 'title' => $tab_title, 'id' => $tab_id, 'content' => $tab_content );
			update_post_meta( $post_id, 'frs_woo_product_tabs', $tab_data );
		}
	}


	private function woocommerce_wp_textarea_input( $field ) {
		global $thepostid, $post;

		if ( ! $thepostid ) $thepostid = $post->ID;
		if ( ! isset( $field['placeholder'] ) ) $field['placeholder'] = '';
		if ( ! isset( $field['class'] ) ) $field['class'] = 'short';
		if ( ! isset( $field['value'] ) ) $field['value'] = get_post_meta( $thepostid, $field['id'], true );

		echo '<p class="form-field ' . $field['id'] . '_field"><label style="display:block;" for="' . $field['id'] . '">' . $field['label'] . '</label><textarea class="' . $field['class'] . '" name="' . $field['id'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" rows="2" cols="20"' . (isset( $field['style'] ) ? ' style="' . $field['style'] . '"' : '') . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

		if ( isset( $field['description'] ) && $field['description'] ) {
			echo '<span class="description">' . $field['description'] . '</span>';
		}

		echo '</p>';
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Custom Product Tabs Lite Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.4.0
	 * @see wc_custom_product_tabs_lite()
	 * @return WooCommerceCustomProductTabsLite
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Lazy-load the product_tabs meta data, and return true if it exists,
	 * false otherwise
	 *
	 * @return true if there is custom tab data, false otherwise
	 */
	private function product_has_custom_tabs( $product ) {
		if ( false === $this->tab_data ) {
			$this->tab_data = maybe_unserialize( get_post_meta( $product->id, 'frs_woo_product_tabs', true ) );
		}
		// tab must at least have a title to exist
		return ! empty( $this->tab_data ) && ! empty( $this->tab_data[0] ) && ! empty( $this->tab_data[0]['title'] );
	}


	/**
	 * Checks if WooCommerce is active
	 *
	 * @since  1.0
	 * @return bool true if WooCommerce is active, false otherwise
	 */
	public static function is_woocommerce_active() {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

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


/**
 * The WooCommerceCustomProductTabsLite global object
 * @deprecated 1.4.0
 * @name $woocommerce_product_tabs_lite
 * @global WooCommerceCustomProductTabsLite $GLOBALS['woocommerce_product_tabs_lite']
 */
$GLOBALS['woocommerce_product_tabs_lite'] = wc_custom_product_tabs_lite();
