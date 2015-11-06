=== WC Variations Radio Buttons ===
Contributors: mantish
Tags: woocommerce, variations, woocommerce variations, radio buttons, variations radio, variations radio buttons
Requires at least: 4.1
Tested up to: 4.4
Stable tag: 1.1.0
WC requires at least: 2.4
WC tested up to: 2.4.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Variations Radio Buttons for WooCommerce. Let your customers choose product variations using radio buttons instead of dropdowns.

== Description ==

Radio buttons are more friendly than dropdown selects. This plugin will help you have a better UX in your WooCommerce site, as your customers will see all your variations without having to click on a dropdown.

The radio buttons will be toggled dinamically so your customers won't be able to choose non existent variations.

### Theme Compatibility

This plugin may not work out of the box with some themes. This is due to a theme having its own template for variation selection (single-product/add-to-cart/variable.php).

To make this plugin work with a non compatible theme, a [child theme](http://themify.me/docs/child-themes) has to be created.
Then copy plugins/wc-variations-radio-buttons/templates/single-product/add-to-cart/variable.php to themes/your-child-theme/woocommerce/single-product/add-to-cart/variable.php

### Github

Contribute at https://github.com/8manos/wc-variations-radio-buttons

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.1.0 =
* Full compatibility with WooCommerce 2.4.
* Variations are called via AJAX when there are many variations.
* Changed version requirements: At least WordPress 4.1 and WooCommerce 2.4.

= 1.0.3 =
* Now works with woocommerce_variation_is_active filter.
* Fix bug that prevented the plugin to work with custom attributes.
* Hopefuly no more "The plugin does not have a valid header." message.

= 1.0.2 =
* Now works better with some themes.

= 1.0.1 =
* WooCommerce 2.4 compatibility.

= 1.0 =
* First release.