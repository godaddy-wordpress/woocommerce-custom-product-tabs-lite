/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var config = {};

	// Makepot
	config.wp_deploy = {
		deploy: {
			options: {
				plugin_slug: 'woocommerce-custom-product-tabs-lite',
				svn_user: 'SkyVerge',
				build_dir: 'build',
				assets_dir: 'wp-assets'
			}
		}
	};

	return config;
};
