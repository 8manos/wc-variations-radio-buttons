<?php
/**
 * Plugin Name: WC Variations Radio Buttons
 * Plugin URI:  https://wordpress.org/plugins/wc-variations-radio-buttons/
 * Description: Variations Radio Buttons for WooCommerce. Let your customers choose product variations using radio buttons instead of dropdowns.
 * Version:     2.0.1
 * Author:      8manos
 * Author URI:  http://8manos.com
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check if WooCommerce is active
if ( is_plugin_active( 'woocommerce/woocommerce.php') ) {

	class WC_Radio_Buttons {
		// plugin version
		const VERSION = '2.0.0';

		private $plugin_path;
		private $plugin_url;

		public function __construct() {
			add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 10, 3 );

			//js scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 999 );
		}

		public function get_plugin_path() {

			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = plugin_dir_path( __FILE__ );
		}

		public function get_plugin_url() {

			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = plugin_dir_url( __FILE__ );
		}

		public function locate_template( $template, $template_name, $template_path ) {
			global $woocommerce;

			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = $this->get_plugin_path() . 'templates/';

			// Look within passed path within the theme - this is priority
			$template = locate_template( array(
				$template_path . $template_name,
				$template_name
			) );

			// Modification: Get the template from this plugin, if it exists
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			// Use default template
			if ( ! $template ) {
				$template = $_template;
			}

			return $template;
		}

		function load_scripts() {
			// Don't load JS if current product type is bundle to prevent the page from not working
			if (!(wc_get_product() && wc_get_product()->is_type('bundle'))) {
				wp_deregister_script( 'wc-add-to-cart-variation' );
				wp_register_script( 'wc-add-to-cart-variation', $this->get_plugin_url() . 'assets/js/frontend/add-to-cart-variation.js', array( 'jquery', 'wp-util' ), self::VERSION );
			}
		}
	}

	new WC_Radio_Buttons();

	if ( ! function_exists( 'print_attribute_radio' ) ) {
		function print_attribute_radio( $checked_value, $value, $label, $name ) {
			global $product;

			$input_name = 'attribute_' . esc_attr( $name ) ;
			$esc_value = esc_attr( $value );
			$id = esc_attr( $name . '_v_' . $value . $product->get_id() ); //added product ID at the end of the name to target single products
			$checked = checked( $checked_value, $value, false );
			$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label, esc_attr( $name ) );
			printf( '<div><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label );
		}
	}
}
