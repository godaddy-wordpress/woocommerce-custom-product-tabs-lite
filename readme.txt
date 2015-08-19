=== WooCommerce Custom Product Tabs Lite ===
Contributors: maxrice, tamarazuk, SkyVerge
Tags: woocommerce, product tabs
Requires at least: 3.8
Tested up to: 4.2.3
Requires WooCommerce at least: 2.2
Tested WooCommerce up to: 2.4
Stable tag: 1.3.0

This plugin extends WooCommerce by allowing a custom product tab to be created with any content.

== Description ==

This plugin extends [WooCommerce](www.woothemes.com/woocommerce/) to allow a custom product tab to be added to single product pages with arbitrary content. The new custom tab may contain text, html, or shortcodes, and will appear between the 'Additional Information' and 'Reviews' tabs.

> Requires WooCommerce 2.2+

= Features =

 - Add a single custom tab to each product in your shop
 - Insert any desired content into custom tabs to provide product specifications, shipping info, or more
 - Custom tabs can accept shortcodes or HTML content &ndash; great for embedding a marketing video or inquiry form

To easily add multiple tabs, share tabs between products, and more features, please consider upgrading to the premium [Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/).

= Feedback =
* We are open to your suggestions and feedback - Thank you for using or trying out one of our plugins!
* Drop us a line at [www.skyverge.com](http://www.skyverge.com)

= Support Details =
We do support our free plugins and extensions, but please understand that support for premium products takes priority. We typically check the forums every few days (with a maximum delay of one week).

= More Details =
 - See the [product page](http://www.skyverge.com/product/woocommerce-custom-product-tabs-lite/) for full details.
 - Check out the pro version at WooThemes: [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/)
 - View more of SkyVerge's [free WooCommerce extensions](http://profiles.wordpress.org/skyverge/)
 - View all [SkyVerge WooCommerce extensions](http://www.skyverge.com/shop/)

== Installation ==

1. Upload the entire 'woocommerce-custom-product-tabs-lite' folder to the '/wp-content/plugins/' directory or upload the zip file via Plugins &gt; Add New
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit a product, then click on 'Custom Tab' within the 'Product Data' panel

== Screenshots ==

1. Adding a custom tab to a product in the admin
2. The custom tab displayed on the frontend

== Frequently Asked Questions ==

= Can I add more than tab, or change the order of the tabs? =

This free version does not have that functionality, but you can with the premium [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/).

= I already use the free plugin and now I want to upgrade to the premium Tab Manager, is that possible? =

Yes, the upgrade process form the free to the premium Tab Manager plugin is painless and easy.

= How do I hide the tab heading? =

The tab heading is shown before the tab content and is the same string as the tab title.  An easy way to hide this is to add the following to the bottom of your theme's functions.php:

`
add_filter( 'woocommerce_custom_product_tabs_lite_heading', '__return_empty_string' );
`

= Can I share tab content between more than one tab? =

This free version does not have that functionality, but you can create global tabs with the [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/).

= Can I set the same tab title for all products? =

Yep, there's the `woocommerce_custom_product_tabs_lite_title` that passes in the tab title for you to change. This filter also passes in the `$product` and class instance if you'd like to change this conditionally.

Here's how you can set one title for all custom tabs, regardless of what title is entered on the product page (can go in the bottom of functions.php):

`
function sv_change_custom_tab_title( $title ) {
 $title = 'Global tab title';
 return $title;
}
add_filter( 'woocommerce_custom_product_tabs_lite_title', 'sv_change_custom_tab_title' );
`

== Changelog ==

= 1.3.0 - 2015.07.28 =
 * Misc - WooCommerce 2.4 Compatibility

= 1.2.9 - 2015.05.14 =
 * Misc - version bump to fix SVN deploy issue

= 1.2.8 - 2015.05.14 =
 * Misc - added `woocommerce_custom_product_tabs_lite_title` filter to update tab titles

= 1.2.7 - 2015.02.09 =
 * Misc - WooCommerce 2.3 Compatibility

= 1.2.6 - 2014.09.05 =
 * Misc - WooCommerce 2.2 Compatibility

= 1.2.5 - 2014.01.22 =
 * Misc - WooCommerce 2.1 support
 * Localization - Text domain is now woocommerce-custom-product-tabs-lite

= 1.2.4 - 2013.08.26 =
 * Fix - Shortcode support in custom tab content

= 1.2.3 - 2013.06.06 =
 * Tweak - Changed admin field names to improve compatibility with other custom tab plugins

= 1.2.2 - 2013.05.15 =
 * Fix - Unicode characters supported in tab title

= 1.2.1 - 2013.04.26 =
 * Tweak - Minor code and documentation update

= 1.2.0 - 2013.02.16 =
 * WooCommerce 2.0 Compatiblity

= 1.1.0 - 2012.04.23 =
 * Feature - Shortcodes enabled for tab content

= 1.0.3 - 2012.03.19 =
 * Feature - Tab content textarea is larger for easier input

= 1.0.2 - 2012.03.19 =
 * Fix - Fixes an admin bug introduced by the 1.0.1 release (thanks to Cabbola)

= 1.0.1 - 2012.03.15 =
 * Fix - Fixes T_PAAMAYIM_NEKUDOTAYIM error (thanks daveshine)

= 1.0.0 - 2012.03.15 =
 * Misc - Code cleanup

= 0.1 - 2012.03.07 =
 * Initial release
