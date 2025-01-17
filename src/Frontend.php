<?php
/**
 * Frontend logic
 *
 * @since 1.9.1-dev.1
 */

namespace WooCommerceCustomProductTabsLite;

use WC_Product;

class Frontend
{
	/** @var bool|array tab data */
	private $tab_data = false;

	public function addHooks()
	{
		add_filter('woocommerce_product_tabs', [$this, 'add_custom_product_tabs']);

		// allow the use of shortcodes within the tab content
		add_filter( 'woocommerce_custom_product_tabs_lite_content', 'do_shortcode' );
	}

	/**
	 * Lazy-load the product_tabs meta data, and return true if it exists,
	 * false otherwise
	 *
	 * @param \WC_Product $product the product object
	 * @return true if there is custom tab data, false otherwise
	 */
	public function product_has_custom_tabs( $product ) {

		if ( false === $this->tab_data ) {
			$this->tab_data = wc_custom_product_tabs_lite()->plugin->productTabsMetaHandler->getMeta($product);
		}

		// tab must at least have a title to exist
		return ! empty( $this->tab_data ) && ! empty( $this->tab_data[0] ) && ! empty( $this->tab_data[0]['title'] );
	}

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

		if ( ! $product instanceof WC_Product ) {
			return $tabs;
		}

		if ( $this->product_has_custom_tabs( $product ) ) {

			foreach ( $this->tab_data as $tab ) {
				$tab_title = __( $tab['title'], 'woocommerce-custom-product-tabs-lite' );
				$tabs[ $tab['id'] ] = array(
					'title'    => apply_filters( 'woocommerce_custom_product_tabs_lite_title', $tab_title, $product, $this ),
					'priority' => 25,
					'callback' => [$this, 'custom_product_tabs_panel_content'],
					'content'  => $tab['content'],  // custom field
				);
			}
		}

		return $tabs;
	}

	/**
	 * Renders the custom product tab panel content for the given $tab.
	 *
	 * @see WooCommerceCustomProductTabsLite::add_custom_product_tabs() callback
	 *
	 * $tab structure:
	 * Array(
	 *   'title'    => (string) Tab title,
	 *   'priority' => (string) Tab priority,
	 *   'callback' => (mixed) callback function,
	 *   'id'       => (int) tab post identifier,
	 *   'content'  => (string) tab content,
	 * )
	 *
	 * @param string $key tab key
	 * @param array $tab tab data
	 */
	public function custom_product_tabs_panel_content( $key, $tab ) {

		// allow shortcodes to function
		$content = apply_filters( 'the_content', $tab['content'] );
		$content = str_replace( ']]>', ']]&gt;', $content );

		echo wp_kses_post( apply_filters( 'woocommerce_custom_product_tabs_lite_heading', '<h2>' . esc_html( $tab['title'] ) . '</h2>', $tab ) );
		echo wp_kses_post( apply_filters( 'woocommerce_custom_product_tabs_lite_content', $content, $tab ) );
	}
}
