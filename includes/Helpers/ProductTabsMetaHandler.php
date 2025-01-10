<?php
/**
 * Product Tabs Meta Handler
 *
 * @since 1.9.1-dev.1
 */
namespace WooCommerceCustomProductTabsLite\Helpers;

use WC_Product;

class ProductTabsMetaHandler
{
	const PRODUCT_TABS_META_KEY = '_wc_custom_product_tabs_lite_product_tabs';
	const LEGACY_PRODUCT_TABS_META_KEY = 'frs_woo_product_tabs';

	public function __construct()
	{
		$this->addHooks();
	}

	public function addHooks()
	{
		add_filter('get_post_metadata', [$this, 'maybeConvertLegacyProductTabsMeta'], 10, 5);
	}

	/**
	 * Gets product meta.
	 *
	 * @param WC_Product $product
	 * @return mixed
	 */
	public function getMeta(WC_Product $product, string $context = 'edit')
	{
		$this->maybeMigrateLegacyMeta($product);

		$meta = $product->get_meta(static::PRODUCT_TABS_META_KEY, true, $context);

		if (is_string($meta)) {
			return json_decode($meta, true);
		}

		return $meta;
	}

	/**
	 * Updates product tabs meta.
	 *
	 * @param WC_Product $product
	 * @param array      $meta
	 * @return void
	 */
	public function updateMeta(WC_Product $product, array|string $meta) : void
	{
		$product->update_meta_data(self::PRODUCT_TABS_META_KEY, json_encode(maybe_unserialize($meta)));
	}

	/**
	 * (Maybe) migrates legacy product tabs meta to the new field.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	public function maybeMigrateLegacyMeta(WC_Product $product)
	{
		if ($meta = $product->get_meta(static::LEGACY_PRODUCT_TABS_META_KEY)) {
			$meta = maybe_unserialize($meta);

			$this->deleteLegacyMeta($product);
			$this->updateMeta($product, $meta);
		}
	}

	/**
	 * Deletes the products tab meta.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	public function deleteMeta(WC_Product $product)
	{
		$product->delete_meta_data(static::PRODUCT_TABS_META_KEY);
	}

	/**
	 * Deletes legacy product tabs meta.
	 *
	 * @param WC_Product $product
	 * @return mixed
	 */
	private function deleteLegacyMeta(WC_Product $product)
	{
		$product->delete_meta_data(self::LEGACY_PRODUCT_TABS_META_KEY);
	}

	/**
	 * Maybe converts legacy product tabs meta to new meta.
	 *
	 * @param null $shortCircutValue The short-circuit meta value. Defualt null affects nothing.
	 * @param int $objectId Object ID.
	 * @param string $metaKey Meta key.
	 * @param bool $single Whether to return only the first value.
	 * @param string $metaType Meta type.
	 * @return mixed
	 */
	public function maybeConvertLegacyProductTabsMeta($shortCircutValue, $objectId, $metaKey, $single, $metaType)
	{
		if (static::LEGACY_PRODUCT_TABS_META_KEY !== $metaKey) {
			return $shortCircutValue;
		}

		$product = wc_get_product($objectId);

		return $this->getMeta($product);
	}
}
