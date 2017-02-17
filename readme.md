# WooCommerce Custom Product Tabs Lite
Contributors: @SkyVerge, @maxrice, @tamarazuk, @chasewiseman, @nekojira  
Tags: woocommerce, product tabs  
Requires at least: 4.1  
Tested up to: 4.5.2  
Requires WooCommerce at least: 2.4.13  
Tested WooCommerce up to: 2.6.0  
Stable tag: 1.5.0  

This plugin extends WooCommerce by allowing a custom product tab to be created with any content.

### Description

This plugin extends [WooCommerce](http://www.woothemes.com/woocommerce/) to allow a custom product tab to be added to single product pages with arbitrary content. The new custom tab may contain text, html (such as embedded videos), or shortcodes, and will appear between the "Additional Information" and "Reviews" tabs.

> Requires WooCommerce 2.4.13 or newer

### Features

 - Add a single custom tab to each product in your shop  
 - Insert any desired content into custom tabs to provide product specifications, shipping info, or more  
 - Custom tabs can accept shortcodes or HTML content &ndash; great for embedding a marketing video or inquiry form  

### Support Details

We do support our free plugins and extensions, but please understand that support for premium products takes priority. We typically check the forums every few days (with a maximum delay of one week).

### WooCommerce Tab Manager

To easily add multiple tabs, share tabs between products, and more features, please consider upgrading to the premium [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/), available from the official WooThemes store.

#### More Details
 - See the [product page](http://www.skyverge.com/product/woocommerce-custom-product-tabs-lite/) for full details.  
 - Check out the pro version at WooThemes: [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/)  
 - View more of SkyVerge's [free WooCommerce extensions](http://profiles.wordpress.org/skyverge/)  
 - View all [SkyVerge WooCommerce extensions](http://www.skyverge.com/shop/)  

Interested in contributing? You can [find the project on GitHub](https://github.com/skyverge/woocommerce-custom-product-tabs-lite) and contributions are welcome :)

## Installation

1. Upload the entire 'woocommerce-custom-product-tabs-lite' folder to the '/wp-content/plugins/' directory, **or** upload the zip file via Plugins &gt; Add New  
2. Activate the plugin through the 'Plugins' menu in WordPress  
3. Edit a product, then click on 'Custom Tab' within the 'Product Data' panel  

## Screenshots

1. Adding a custom tab to a product in the admin  
2. The custom tab displayed on the frontend  

## Frequently Asked Questions

#### Can I add more than tab, or change the order of the tabs?

This free version does not have that functionality, but you can with the premium [WooCommerce Tab Manager](http://www.woothemes.com/products/woocommerce-tab-manager/). This allows you to add multiple tabs, share tabs, edit core tabs, or re-order tab display.

#### How do I hide the tab heading?

The tab heading is shown before the tab content and is the same string as the tab title.  An easy way to hide this is to add the following to the bottom of your theme's functions.php or wherever you keep custom code:

`add_filter( 'woocommerce_custom_product_tabs_lite_heading', '__return_empty_string' );`

#### My tab content isn't showing properly, how do I fix it?

Be sure that (1) your HTML is valid -- try putting your tab content into a blog post draft and see how it renders. (2) Be sure any shortcodes will expand in your blog post draft as well; if they don't work properly there, they won't work in your custom tab.

#### Can I set the same tab title for all products?

Yep, there's the `woocommerce_custom_product_tabs_lite_title` that passes in the tab title for you to change.  
This filter also passes in the `$product` and class instance if you'd like to change this conditionally.

Here's how you can set one title for all custom tabs, regardless of what title is entered on the product page (can go in the bottom of functions.php or where you keep custom code):

```
function sv_change_custom_tab_title( $title ) {
	$title = 'Global tab title';
	return $title;
}
add_filter( 'woocommerce_custom_product_tabs_lite_title', 'sv_change_custom_tab_title' );
```