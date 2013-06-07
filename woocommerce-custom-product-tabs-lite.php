<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs Lite
 * Plugin URI: http://www.foxrunsoftware.net/articles/wordpress/woocommerce-custom-product-tabs/
 * Description: Extends WooCommerce to add a custom product view page tab
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 1.2.3
 * Tested up to: 3.5
 *
 * Copyright: (c) 2012-2013 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Custom-Product-Tabs-Lite
 * @author      SkyVerge
 * @category    Plugin
 * @copyright   Copyright (c) 2012-2013, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active and bail if it's not
if ( ! WoocommerceCustomProductTabsLite::is_woocommerce_active() )
	return;

/**
 * The WoocommerceCustomProductTabsLite global object
 * @name $woocommerce_product_tabs_lite
 * @global WoocommerceCustomProductTabsLite $GLOBALS['woocommerce_product_tabs_lite']
 */
$GLOBALS['woocommerce_product_tabs_lite'] = new WoocommerceCustomProductTabsLite();

class WoocommerceCustomProductTabsLite {

	private $tab_data = false;

	/** plugin version number */
	const VERSION = "1.2.3";

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_custom_product_tabs_lite_db_version';


	/**
	 * Gets things started by adding an action to initialize this plugin once
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();

		add_action( 'woocommerce_init', array( $this, 'init' ) );
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
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0" ) >= 0 ) {
			// WC >= 2.0
			add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs' ) );
		} else {
			// WC < 2.0
			add_action( 'woocommerce_product_tabs', array( $this, 'custom_product_tabs' ), 25 );
			// in between the attributes and reviews panels
			add_action( 'woocommerce_product_tab_panels', array( $this, 'custom_product_tabs_panel' ), 25 );
		}

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
				$tabs[ $tab['id'] ] = array(
					'title'    => $tab['title'],
					'priority' => 25,
					'callback' => array( $this, 'custom_product_tabs_panel_content' ),
					'content'  => $tab['content'],  // custom field
				);
			}
		}

		return $tabs;
	}


	/**
	 * Write the custom tab on the product view page.  In WooCommerce these are
	 * handled by templates.
	 *
	 * WC < 2.0
	 */
	public function custom_product_tabs() {
		global $product;

		if ( $this->product_has_custom_tabs( $product ) ) {
			foreach ( $this->tab_data as $tab ) {
				echo "<li><a href=\"#{$tab['id']}\">" . __( $tab['title'] ) . "</a></li>";
			}
		}
	}


	/**
	 * Write the custom tab panel on the product view page.  In WooCommerce these
	 * are handled by templates.
	 *
	 * WC < 2.0
	 */
	public function custom_product_tabs_panel() {
		global $product;

		if ( $this->product_has_custom_tabs( $product ) ) {
			foreach ( $this->tab_data as $tab ) {
				echo '<div class="panel" id="' . $tab['id'] . '">';
				$this->custom_product_tabs_panel_content( $tab['id'], $tab );
				echo '</div>';
			}
		}
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
		echo apply_filters( 'woocommerce_custom_product_tabs_lite_heading', '<h2>' . $tab['title'] . '</h2>', $tab );
		echo apply_filters( 'woocommerce_custom_product_tabs_lite_content', $tab['content'], $tab );
	}


	/** Admin methods ******************************************************/


	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 */
	public function product_write_panel_tab() {
		echo "<li class=\"product_tabs_lite_tab\"><a href=\"#woocommerce_product_tabs_lite\">" . __( 'Custom Tab' ) . "</a></li>";
	}


	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 */
	public function product_write_panel() {
		global $post;
		// the product

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			$style = 'padding:5px 5px 5px 28px;background-repeat:no-repeat;background-position:5px 7px;';
			$active_style = '';
		} else {
			$style = 'padding:9px 9px 9px 34px;line-height:16px;border-bottom:1px solid #d5d5d5;text-shadow:0 1px 1px #fff;color:#555555;background-repeat:no-repeat;background-position:9px 9px;';
			$active_style = '#woocommerce-product-data ul.product_data_tabs li.product_tabs_lite_tab.active a { border-bottom: 1px solid #F8F8F8; }';
		}
		?>
		<style type="text/css">
			#woocommerce-product-data ul.product_data_tabs li.product_tabs_lite_tab a { <?php echo $style; ?> }
			<?php echo $active_style; ?>
		</style>
		<?php

		// pull the custom tab data out of the database
		$tab_data = maybe_unserialize( get_post_meta( $post->ID, 'frs_woo_product_tabs', true ) );

		if ( empty( $tab_data ) ) {
			$tab_data[] = array( 'title' => '', 'content' => '' );
		}

		foreach ( $tab_data as $tab ) {
			// display the custom tab panel
			echo '<div id="woocommerce_product_tabs_lite" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_tab_title', 'label' => __( 'Tab Title' ), 'description' => __( 'Required for tab to be visible' ), 'value' => $tab['title'] ) );
			$this->woocommerce_wp_textarea_input( array( 'id' => '_wc_custom_product_tabs_lite_tab_content', 'label' => __( 'Content' ), 'placeholder' => __( 'HTML and text to display.' ), 'value' => $tab['content'], 'style' => 'width:70%;height:21.5em;' ) );
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

		if ( isset( $field['description'] ) && $field['description'] )
			echo '<span class="description">' . $field['description'] . '</span>';

		echo '</p>';
	}


	/** Helper methods ******************************************************/


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

		if ( is_multisite() )
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

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
