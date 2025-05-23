=== Custom Product Tabs Lite for WooCommerce ===
Contributors: skyverge, maxrice, tamarazuk, chasewiseman, nekojira, beka.rice
Tags: woocommerce, product tabs, custom tab, woo commerce tab
Requires at least: 5.6
Requires PHP: 7.4
Tested up to: 6.8
Stable tag: 1.9.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin extends WooCommerce by allowing a custom product tab to be created with any content.

== Description ==

This plugin extends [WooCommerce](http://woocommerce.com/) to allow a custom product tab to be added to single product pages with arbitrary content. The new custom tab may contain text, html (such as embedded videos), or shortcodes, and will appear between the "Additional Information" and "Reviews" tabs.

> Requires WooCommerce 3.9.4 or newer

= Features =

 - Add a single custom tab to each product in your shop
 - Insert any desired content into custom tabs to provide product specifications, shipping info, or more
 - Custom tabs can accept shortcodes or HTML content &ndash; great for embedding a marketing video or inquiry form!

= Support Details =

We do support our free plugins and extensions, but please understand that support for premium products takes priority. We typically check the forums every few days (with a maximum delay of one week).

= WooCommerce Tab Manager =

To easily add multiple tabs, share tabs between products, and more features, please consider upgrading to the premium [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/), available from the official WooCommerce.com store.

= More Details =
 - See the [product page](http://www.skyverge.com/product/woocommerce-custom-product-tabs-lite/) for full details.
 - Check out the pro version at WooThemes: [WooCommerce Tab Manager](http://woocommerce.com/products/woocommerce-tab-manager/)
 - View more of SkyVerge's [free WooCommerce extensions](http://profiles.wordpress.org/skyverge/)
 - View all [SkyVerge WooCommerce extensions](http://www.skyverge.com/shop/)

Interested in contributing? You can [find the project on GitHub](https://github.com/skyverge/woocommerce-custom-product-tabs-lite) and contributions are welcome :)

== Installation ==

1. Upload the entire 'woocommerce-custom-product-tabs-lite' folder to the '/wp-content/plugins/' directory, **or** upload the zip file via Plugins &gt; Add New
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Edit a product, then click on 'Custom Tab' within the 'Product Data' panel

== Screenshots ==

1. Adding a custom tab to a product in the admin
2. The custom tab displayed on the frontend

== Frequently Asked Questions ==

= Can I add more than tab, or change the order of the tabs? =

This free version does not have that functionality, but you can with the premium [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/). This allows you to add multiple tabs, share tabs, edit core tabs, or re-order tab display.

= How do I hide the tab heading? =

The tab heading is shown before the tab content and is the same string as the tab title.  An easy way to hide this is to add the following to the bottom of your theme's functions.php or wherever you keep custom code:

`
add_filter( 'woocommerce_custom_product_tabs_lite_heading', '__return_empty_string' );
`

= My tab content isn't showing properly, how do I fix it? =

Be sure that (1) your HTML is valid -- try putting your tab content into a blog post draft and see how it renders. (2) Be sure any shortcodes will expand in your blog post draft as well; if they don't work properly there, they won't work in your custom tab.

= Can I set the same tab title for all products? =

Yep, there's the `woocommerce_custom_product_tabs_lite_title` that passes in the tab title for you to change. This filter also passes in the `$product` and class instance if you'd like to change this conditionally.

Here's how you can set one title for all custom tabs, regardless of what title is entered on the product page (can go in the bottom of functions.php or where you keep custom code):

`
function sv_change_custom_tab_title( $title ) {
 $title = 'Global tab title';
 return $title;
}
add_filter( 'woocommerce_custom_product_tabs_lite_title', 'sv_change_custom_tab_title' );
`

== Changelog ==

= 2025.01.22 - version 1.9.1 =
* Misc - Standardize product tabs meta storage

= 2024.10.29 - version 1.9.0 =
* Fix - Ensure PHP 8.2+ compatibility
* Misc - Declare WordPress 6.7
* Misc -  Declare WooCommerce 9.3.3 compatibility

= 2023.07.28 - version 1.8.0 =
 * Misc - Add compatibility for WooCommerce High Performance Order Storage (HPOS)

= 2022.12.02 - version 1.7.8 =
 * Fix - Allow shortcodes with parameters
 * Misc - Require PHP 7.4 and WordPress 5.6

= 2022.08.03 - version 1.7.7 =
 * Fix - Sanitize input

= 2022.07.31 - version 1.7.6 =
 * Misc - Rename to Custom Product Tabs Lite for WooCommerce

= 2022.03.01 - version 1.7.5 =
 * Misc - Require WooCommerce 3.9.4 or newer

= 2020.05.04 - version 1.7.4 =
 * Misc - Add support for WooCommerce 4.1

= 2020.03.10 - version 1.7.3 =
 * Misc - Add support for WooCommerce 4.0

= 2020.02.05 - version 1.7.2 =
 * Misc - Add support for WooCommerce 3.9

= 2019.11.11 - version 1.7.1 =
 * Misc - Add support for WooCommerce 3.8

= 2019.08.15 - version 1.7.0
 * Misc - Add support for WooCommerce 3.7
 * Misc - Remove support for WooCommerce 2.6

= 2019.06.12 - version 1.6.4
 * Misc - Declare WooCommerce 3.6 compatibility

= 2018.08.10 - version 1.6.3 =
 * Misc - Remove support for WooCommerce 2.5

= 1.6.2 - 2017.08.22 =
 * Fix: PHP warning when WooCommerce is outdated

= 1.6.1 - 2017.04.03 =
 * Fix - Errors while editing products when WooCommerce Tab Manager is also active

= 1.6.0 - 2017.03.23 =
 * Misc - Added support for WooCommerce 3.0
 * Misc - Removed support for WooCommerce 2.4

= 1.5.0 - 2016.05.24 =
 * Misc - Added support for WooCommerce 2.6
 * Misc - Removed support for WooCommerce 2.3

= 1.4.0 - 2016.01.20 =
 * Misc - Added support for WooCommerce 2.5
 * Misc - Removed support for WooCommerce 2.2

= 1.3.1 - 2015.10.02 =
 * Misc - Use text domain strings

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
