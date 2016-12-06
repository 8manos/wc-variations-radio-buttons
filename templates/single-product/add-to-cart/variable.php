<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 *
 * Modified to use radio buttons instead of dropdowns
 * @author 8manos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'print_attribute_radio' ) ) {
	function print_attribute_radio( $checked_value, $value, $label, $name ) {
		// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
		$checked = sanitize_title( $checked_value ) === $checked_value ? checked( $checked_value, sanitize_title( $value ), false ) : checked( $checked_value, $value, false );

		$input_name = 'attribute_' . esc_attr( $name ) ;
		$esc_value = esc_attr( $value );
		$id = esc_attr( $name . '_v_' . $value );
		$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label );
		printf( '<div class="variations-data"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s class="variations-radio"><label for="%3$s" class="variations-label">%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label );
	}
}

global $product;

$attribute_keys = array_keys( $attributes );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody class="variations-tbody">
				<?php foreach ( $attributes as $name => $options ) : ?>
					<tr class="variations-head">
						<td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></td></tr><tr class="variations-row">
						<?php
						$sanitized_name = sanitize_title( $name );
						if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) {
							$checked_value = $_REQUEST[ 'attribute_' . $sanitized_name ];
						} elseif ( isset( $selected_attributes[ $sanitized_name ] ) ) {
							$checked_value = $selected_attributes[ $sanitized_name ];
						} else {
							$checked_value = '';
						}
						?>
						<td class="value">
							<?php
							if ( ! empty( $options ) ) {
								if ( taxonomy_exists( $name ) ) {
									// Get terms if this is a taxonomy - ordered. We need the names too.
									$terms = wc_get_product_terms( $product->id, $name, array( 'fields' => 'all' ) );

									foreach ( $terms as $term ) {
										if ( ! in_array( $term->slug, $options ) ) {
											continue;
										}
										print_attribute_radio( $checked_value, $term->slug, $term->name, $sanitized_name );
									}
								} else {
									foreach ( $options as $option ) {
										print_attribute_radio( $checked_value, $option, $option, $sanitized_name );
									}
								}
							}

							echo end( $attribute_keys ) === $name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap">
			<?php do_action( 'woocommerce_before_single_variation' ); ?>

			<?php
			if ( has_action( 'woocommerce_single_variation' ) ) {
				do_action( 'woocommerce_single_variation' );
			} else {
				// Backwards compatibility with WC < 2.4
			?>
				<div class="woocommerce-variation single_variation"></div>

				<div class="woocommerce-variation-add-to-cart variations_button">
					<?php if ( ! $product->is_sold_individually() ) : ?>
						<?php woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) ); ?>
					<?php endif; ?>
					<button type="submit" class="single_add_to_cart_button button alt otto"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
					<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->id ); ?>" />
					<input type="hidden" name="product_id" value="<?php echo absint( $product->id ); ?>" />
					<input type="hidden" name="variation_id" class="variation_id" value="0" />
				</div>
			<?php } ?>

			<?php do_action( 'woocommerce_after_single_variation' ); ?>
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
