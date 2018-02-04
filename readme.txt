=== WC Variations Radio Buttons ===
Contributors: mantish
Donate link: mailto:paypal@8manos.com
Tags: woocommerce, variations, woocommerce variations, radio buttons, variations radio, variations radio buttons
Requires at least: 4.4
Tested up to: 4.9.2
Stable tag: 2.0.1
WC requires at least: 3.0
WC tested up to: 3.2.6
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

Source code and contributions at [github](https://github.com/8manos/wc-variations-radio-buttons)

== Changelog ==

= 2.0.1 =
* Fix issues in archive pages, where multiple products are present.
* Fix several issues with the gallery.
* Fix product dimensions and weight.
* Radio buttons disabled for product bundles.
* Add class so each attribute can be styled.
* Add the attribute name to the `woocommerce_variation_option_name` filter.

= 2.0.0 =
* Updated to match changes in WooCommerce 3.0.
* This version is not compatible with WooCommerce 2.x.
* Fix issues with the variation image not changing.

= 1.1.5 =
* Fix error when using product_page WooCommerce shortcode.
* Show alert when clicking disabled add to cart button.

= 1.1.4 =
* Avoid overwriting of custom add-to-cart button.

= 1.1.3 =
* Fixes clear button that got screwed on last version.

= 1.1.2 =
* Updated to match changes in WooCommerce 2.5.
* Image disappearing in certain themes fixed.

= 1.1.1 =
* Support for responsive images as in WordPress 4.4 and WooCommerce 2.4.11.
* Checked compatibility with WooCommerce 2.5.0-RC1.

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