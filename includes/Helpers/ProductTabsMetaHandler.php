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

	public function addHooks()
	{
		add_filter('get_post_metadata', [$this, 'maybeConvertLegacyProductTabsMeta'], 10, 5);
	}

	/**
	 * Gets product meta.
	 *
	 * @param WC_Product $product
	 * @param string $context
	 * @return mixed
	 */
	public function getMeta(WC_Product $product, string $context = 'edit')
	{
		$meta = $product->get_meta(static::PRODUCT_TABS_META_KEY, true, $context);

		if (! empty($meta) && is_string($meta)) {
			return json_decode($meta, true);
		}

		return $this->maybeMigrateLegacyMeta($product);
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
		$product->update_meta_data(static::PRODUCT_TABS_META_KEY, json_encode($meta));
	}

	/**
	 * Deletes the products tab meta.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	public function deleteMeta(WC_Product $product, string $key)
	{
		$product->delete_meta_data($key);
	}

	/**
	 * Gets the legacy tabs meta.
	 *
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	private function getLegacyMeta(WC_Product $product)
	{
		$meta = $product->get_meta(static::LEGACY_PRODUCT_TABS_META_KEY);

		if ($meta && is_serialized($meta)) {
			return unserialize(trim($meta), ['allowed_classes' => false]);
		}

		return $meta;
	}

	/**
	 * (Maybe) migrates legacy product tabs meta to the new field.
	 *
	 * @param WC_Product $product
	 * @return mixed
	 */
	public function maybeMigrateLegacyMeta(WC_Product $product)
	{
		if ($meta = $this->getLegacyMeta($product)) {
			$this->deleteMeta($product, static::LEGACY_PRODUCT_TABS_META_KEY);
			$this->updateMeta($product, $meta);
		}

		return $meta;
	}

	/**
	 * (Maybe) converts legacy product tabs meta to new meta.
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

		if (! $product = wc_get_product($objectId)) {
			return $shortCircutValue;
		}

		return $this->maybeMigrateLegacyMeta($product);
	}
}
