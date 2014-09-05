/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var config = {};

	// Makepot
	config.makepot = {
		makepot: {
			options: {
				cwd: '',
				domainPath: 'i18n/languages',
				potFilename: 'woocommerce-custom-product-tabs-lite.pot',
				potHeaders: { 'report-msgid-bugs-to': 'https://github.com/skyverge/woocommerce-custom-product-tabs-lite/issues' },
				processPot: function( pot ) {
					delete pot.headers['x-generator'];
					return pot;
				}, // jshint ignore:line
				type: 'wp-plugin',
				updateTimestamp: false
			}
		}
	};


	return config;
};
