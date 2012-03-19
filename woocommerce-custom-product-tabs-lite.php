<?php
/*
Plugin Name: WooCommerce Custom Product Tabs Lite
Plugin URI: http://www.foxrunsoftware.net/articles/wordpress/woocommerce-custom-product-tabs/
Description: Extends WooCommerce to add a custom product view page tab
Version: 1.0.1
Author: Justin Stern
Author URI: http://www.foxrunsoftware.net
License: GPL2
*/

/*  Copyright (C) 2012  Justin Stern  (email : justin@foxrunsoftware.net)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if (!class_exists('WoocommerceCustomProductTabsLite')) :

class WoocommerceCustomProductTabsLite {
	private $tab_data = false;
	const VERSION = "1.0.1";
	
	/**
	 * Gets things started by adding an action to initialize this plugin once
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		add_action( 'woocommerce_init', array(&$this, 'init' ));
		
		// Installation
		if (is_admin() && !defined('DOING_AJAX')) $this->install();
	}
	
	/**
	 * Init WooCommerce Product Tabs Lite extension once we know WooCommerce is active
	 */
	public function init() {
		// backend stuff
		add_action('woocommerce_product_write_panel_tabs', array($this, 'product_write_panel_tab'));
		add_action('woocommerce_product_write_panels',	 array($this, 'product_write_panel'));
		add_action('woocommerce_process_product_meta',	 array($this, 'product_save_data'), 10, 2);
		
		// frontend stuff
		add_action('woocommerce_product_tabs',	   array($this, 'custom_product_tabs'),	   25);  // in between the attributes and reviews panels
		add_action('woocommerce_product_tab_panels', array($this, 'custom_product_tabs_panel'), 25);
	}
	
	/**
	 * Write the custom tab on the product view page.  In WooCommerce these are
	 * handled by templates.
	 */
	public function custom_product_tabs() {
		global $product;
		
		if($this->product_has_custom_tabs($product)) {
			foreach($this->tab_data as $tab) {
				echo "<li><a href=\"#{$tab['id']}\">".__($tab['title'])."</a></li>";
			}
		}
	}
	
	/**
	 * Write the custom tab panel on the product view page.  In WooCommerce these
	 * are handled by templates.
	 */
	public function custom_product_tabs_panel() {
		global $product;
		
		if($this->product_has_custom_tabs($product)) {
			foreach($this->tab_data as $tab) {
				echo '<div class="panel" id="'.$tab['id'].'">';
				echo '<h2>' . $tab['title'] . '</h2>';
				echo $tab['content'];
				echo '</div>';
			}
		}
	}
	
	/**
	 * Lazy-load the product_tabs meta data, and return true if it exists,
	 * false otherwise
	 * 
	 * @return true if there is custom tab data, false otherwise
	 */
	private function product_has_custom_tabs($product) {
		if($this->tab_data === false) {
			$this->tab_data = maybe_unserialize( get_post_meta($product->id, 'frs_woo_product_tabs', true) );
		}
		// tab must at least have a title to exist
		return !empty($this->tab_data) && !empty($this->tab_data[0]) && !empty($this->tab_data[0]['title']);
	}
	
	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 */
	public function product_write_panel_tab() {
		echo "<li><a style=\"color:#555555;line-height:16px;padding:9px;text-shadow:0 1px 1px #FFFFFF;\" href=\"#product_tabs\">".__('Custom Tab')."</a></li>";
	}
	
	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 */
	public function product_write_panel() {
		global $post;  // the product
		
		// pull the custom tab data out of the database
		$tab_data = maybe_unserialize( get_post_meta($post->ID, 'frs_woo_product_tabs', true) );
		
		if(empty($tab_data)) {
			$tab_data[] = array('title' => '', 'content' => '');
		}
		
		foreach($tab_data as $tab) {
			// display the custom tab panel
			echo '<div id="product_tabs" class="panel woocommerce_options_panel">';
			echo woocommerce_wp_text_input( array( 'id' => '_tab_title', 'label' => __('Tab Title'), 'description' => __('Required for tab to be visible'), 'value' => $tab['title'] ) );
			echo woocommerce_wp_textarea_input( array( 'id' => '_tab_content', 'label' => __('Content'), 'placeholder' => __('HTML and text to display.'), 'value' => $tab['content'] ) );
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
		
		$tab_title   = stripslashes($_POST['_tab_title']);
		$tab_content = stripslashes($_POST['_tab_content']);
		
		if(empty($tab_title) && empty($tab_content) && get_post_meta($post_id, 'frs_woo_product_tabs', true)) {
			// clean up if the custom tabs are removed
			delete_post_meta($post_id, 'frs_woo_product_tabs');
		} elseif(!empty($tab_title) || !empty($tab_content)) {
			$tab_data = array();
			
			$tab_id = '';
			if($tab_title) {
				// convert the tab title into an id string
				$tab_id = strtolower($tab_title);
				$tab_id = preg_replace("/[^\w\s]/",'',$tab_id);  // remove non-alphas, numbers, underscores or whitespace 
				$tab_id = preg_replace("/_+/", ' ', $tab_id);	// replace all underscores with single spaces
				$tab_id = preg_replace("/\s+/", '-', $tab_id);   // replace all multiple spaces with single dashes
				$tab_id = 'tab-'.$tab_id;						// prepend with 'tab-' string
			}
			
			// save the data to the database
			$tab_data[] = array('title' => $tab_title,
								'id' => $tab_id,
								'content' => $tab_content);
			update_post_meta($post_id, 'frs_woo_product_tabs', $tab_data);
		}
	}
	
	/**
	 * Run every time since the activation hook is not executed when updating a plugin
	 */
	private function install() {
		if(get_option('woocommerce_custom_product_tabs_lite_db_version') != WoocommerceCustomProductTabsLite::VERSION) {
			$this->upgrade();
			
			// new version number
			update_option('woocommerce_custom_product_tabs_lite_db_version', WoocommerceCustomProductTabsLite::VERSION);
		}
	}
	
	/**
	 * Run when plugin version number changes
	 */
	private function upgrade() {
		global $wpdb;
		if(!get_option('woocommerce_custom_product_tabs_lite_db_version')) {
			// this is one of the couple of original users who installed before I had a version option in the db
			//  rename the post meta option 'product_tabs' to 'frs_woo_product_tabs'
			$wpdb->query("UPDATE {$wpdb->postmeta} SET meta_key='frs_woo_product_tabs' WHERE meta_key='product_tabs';");
		}
	}
	
	/**
	 * Runs various functions when the plugin first activates (and every time
	 * its activated after first being deactivated), and verifies that
	 * the WooCommerce plugin is installed and active
	 * 
	 * @see register_activation_hook()
	 * @link http://codex.wordpress.org/Function_Reference/register_activation_hook
	 */
	public static function on_activation() {
		// checks if the woocommerce plugin is running and disables this plugin if it's not (and displays a message)
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__('The WooCommerce Product Tabs Lite requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> first. <a href="'.admin_url('plugins.php').'"> <br> &laquo; Go Back</a>'));
		}
		
		// set version number
		update_option('woocommerce_custom_product_tabs_lite_db_version', WoocommerceCustomProductTabsLite::VERSION);
	}
}

/**
 * instantiate class
 */
$woocommerce_product_tabs_lite = new WoocommerceCustomProductTabsLite();

endif; // class exists check

/**
 * run the plugin activation hook
 */
register_activation_hook(__FILE__, array('WoocommerceCustomProductTabsLite', 'on_activation'));
