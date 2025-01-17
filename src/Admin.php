<?php
/**
 * Admin logic
 *
 * @since 1.9.1-dev.1
 */

namespace WooCommerceCustomProductTabsLite;

class Admin
{
	public function addHooks()
	{
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tab' ) );
		add_action( 'woocommerce_product_data_panels',      array( $this, 'product_write_panel' ) );
		add_action( 'woocommerce_process_product_meta',     array( $this, 'product_save_data' ), 10, 2 );
	}

	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface.
	 *
	 * @since 1.0.0
	 */
	public function product_write_panel_tab() {
		echo "<li class=\"product_tabs_lite_tab\"><a href=\"#woocommerce_product_tabs_lite\"><span>" . esc_html__( 'Custom Tab', 'woocommerce-custom-product-tabs-lite' ) . "</span></a></li>";
	}

	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 *
	 * @since 1.0.0
	 */
	public function product_write_panel() {
		global $post;

		$product = wc_get_product( $post );

		// pull the custom tab data out of the database
		$tab_data = wc_custom_product_tabs_lite()->metaHandler->getMeta($product);

		if ( empty( $tab_data ) ) {

			// start with an array for PHP 7.1+
			$tab_data = array();

			$tab_data[] = array(
				'title'   => '',
				'content' => '',
			);
		}

		foreach ( $tab_data as $tab ) {
			// display the custom tab panel

			echo '<div id="woocommerce_product_tabs_lite" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';
			woocommerce_wp_text_input( array(
				'id'          => '_wc_custom_product_tabs_lite_tab_title',
				'label'       => __( 'Tab Title', 'woocommerce-custom-product-tabs-lite' ),
				'description' => __( 'Required for tab to be visible', 'woocommerce-custom-product-tabs-lite' ),
				'value'       => $tab['title'],
			));

			woocommerce_wp_textarea_input( array(
				'id'          => '_wc_custom_product_tabs_lite_tab_content',
				'label'       => __( 'Content', 'woocommerce-custom-product-tabs-lite' ),
				'placeholder' => __( 'HTML and text to display.', 'woocommerce-custom-product-tabs-lite' ),
				'value'       => $tab['content'],
				'style'       => 'width:70%;height:14.5em;',
			));
			echo '</div>';
		}
	}

	/**
	 * Saves the data input into the product boxes, as post meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 */
	public function product_save_data( $post_id, $post ) {

		// stripslashes() so that shortcodes with arguments can be  used
		$tab_content = wp_kses_post( stripslashes( $_POST['_wc_custom_product_tabs_lite_tab_content'] ) );
		$tab_title   = sanitize_text_field( $_POST['_wc_custom_product_tabs_lite_tab_title'] );
		$product     = wc_get_product( $post_id );

		if ( empty( $tab_title ) && empty( $tab_content ) && wc_custom_product_tabs_lite()->metaHandler->getMeta($product) ) {

			// clean up if the custom tabs are removed
			wc_custom_product_tabs_lite()->metaHandler->deleteMeta($product);
			$product->save();

		} elseif ( ! empty( $tab_title ) || ! empty( $tab_content ) ) {

			$tab_id = '';

			if ( $tab_title ) {

				if ( strlen( $tab_title ) !== strlen( mb_convert_encoding( $tab_title, 'UTF-8', mb_detect_encoding( $tab_title ) ) ) ) {

					// can't have titles with utf8 characters as it breaks the tab-switching javascript
					$tab_id = "tab-custom";

				} else {

					// convert the tab title into an id string
					$tab_id = strtolower( $tab_title );

					// remove non-alphas, numbers, underscores or whitespace
					$tab_id = preg_replace( "/[^\w\s]/", '', $tab_id );

					// replace all underscores with single spaces
					$tab_id = preg_replace( "/_+/", ' ', $tab_id );

					// replace all multiple spaces with single dashes
					$tab_id = preg_replace( "/\s+/", '-', $tab_id );

					// prepend with 'tab-' string
					$tab_id = 'tab-' . $tab_id;
				}
			}

			$tab_data = [
				[
					'title'   => $tab_title,
					'id'      => $tab_id,
					'content' => $tab_content,
				]
			];

			wc_custom_product_tabs_lite()->metaHandler->updateMeta($product, $tab_data);
			$product->save();
		}
	}

}
