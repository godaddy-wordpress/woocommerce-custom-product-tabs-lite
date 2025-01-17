<?php
/**
 * Plugin init
 *
 * @since 1.9.1-dev.1
 */

namespace WooCommerceCustomProductTabsLite;

use WooCommerceCustomProductTabsLite\Helpers\ProductTabsMetaHandler;

class Plugin
{
	private Admin $admin;

	private Frontend $frontend;

	public ProductTabsMetaHandler $productTabsMetaHandler;

	/**
	 * Initializes the rest of the plugin.
	 *
	 * @return void
	 */
	public function init()
	{
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
		require_once(__DIR__ . '/Admin.php');
		require_once(__DIR__ . '/Frontend.php');
		require_once(__DIR__ . '/Helpers/ProductTabsMetaHandler.php');

		$this->admin = new Admin;
		$this->frontend = new Frontend;

		$this->productTabsMetaHandler = new ProductTabsMetaHandler;
	}

	public function addHooks()
	{
		$this->admin->addHooks();
		$this->frontend->addHooks();
		$this->productTabsMetaHandler->addHooks();
	}
}
