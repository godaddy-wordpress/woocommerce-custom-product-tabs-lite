<?php
/**
 * Product Tabs Meta Handler
 *
 * @since 1.9.1
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
	 * @param WC_Product  $product Passed by reference.
	 * @param array|mixed $meta
	 * @return void
	 */
	public function updateMeta(WC_Product &$product, $meta) : void
	{
		$product->update_meta_data(static::PRODUCT_TABS_META_KEY, json_encode($meta));
	}

	/**
	 * Deletes the products tab meta.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	public function deleteMeta(WC_Product $product) : void
	{
		$product->delete_meta_data(static::PRODUCT_TABS_META_KEY);
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
	 * Deletes legacy product tabs meta.
	 *
	 * @param WC_Product $product
	 * @return void
	 */
	private function deleteLegacyMeta(WC_Product $product)
	{
		$product->delete_meta_data(static::LEGACY_PRODUCT_TABS_META_KEY);
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
			$this->deleteLegacyMeta($product);
			$this->updateMeta($product, $meta);

			$product->save();

			return $meta;
		}

		return false;
	}

	/**
	 * (Maybe) converts legacy product tabs meta to new meta.
	 *
	 * @param null $shortCircuitValue The short-circuit meta value. Default null affects nothing.
	 * @param int $objectId Object ID.
	 * @param string $metaKey Meta key.
	 * @param bool $single Whether to return only the first value.
	 * @param string $metaType Meta type.
	 * @return mixed
	 */
	public function maybeConvertLegacyProductTabsMeta($shortCircuitValue, $objectId, $metaKey, $single, $metaType)
	{
		if (static::LEGACY_PRODUCT_TABS_META_KEY !== $metaKey) {
			return $shortCircuitValue;
		}

		if (! $product = wc_get_product($objectId)) {
			return $shortCircuitValue;
		}

		if($migrated = $this->maybeMigrateLegacyMeta($product)) {
			$meta = $migrated;
		} else {
			$meta = $this->getMeta($product);
		}

		/**
		 * The filter expects the content to be in an extra array, and {@see get_metadata_raw()} will handle pulling
		 * out the first value for us.
		 */
		return [$meta];
	}
}
