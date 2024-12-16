

<?php
/**
 * Variable subscription product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce-Subscriptions/Templates
 * @version 2.0.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function wc_dropdown_variation_attribute_options_sub( $args = array() ) { 
    $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array( 
        'options' => false,  
        'attribute' => false,  
        'product' => false,  
        'selected' => false,  
        'name' => '',  
        'id' => '',  
        'class' => '',  
        'show_option_none' => __( 'Choose an option', 'woocommerce' ) 
 ) ); 

    $options = $args['options']; 
    $product = $args['product']; 
    $attribute = $args['attribute']; 
    $name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute ); 
    $id = $args['id'] ? $args['id'] : sanitize_title( $attribute ); 
    $class = $args['class']; 

    if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) { 
        $attributes = $product->get_variation_attributes(); 
        $options = $attributes[ $attribute ]; 
    } 

   // echo '<fieldset id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">'; 

/*    if ( $args['show_option_none'] ) { 
*        echo '<option value="">' . esc_html( $args['show_option_none'] ) . '</option>'; 
    } */

    if ( ! empty( $options ) ) { 
        if ( $product && taxonomy_exists( $attribute ) ) { 
            // Get terms if this is a taxonomy - ordered. We need the names too. 
            $terms = wc_get_product_terms( $product->id, $attribute, array( 'fields' => 'all' ) ); 

            foreach ( $terms as $term ) { 
                if ( in_array( $term->slug, $options ) ) { 
                    print_attribute_radio( $checked_value, $term->slug, $term->name, $sanitized_name );
                } 
            } 
        } else { 
            foreach ( $options as $option ) { 
                // This handles < 2.4.0 bw compatibility where text attributes were not sanitized. 
                $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false ); //this should probably be changed to checked similar to how the plugin you wrote has it.
                $value = esc_attr( $option );
                $id = esc_attr( $attribute . '_v_' . $value );
                $modified_label = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
                printf ('<div><input type="radio" name="%1$s" value="%2$s" id="%3$s"><label for="%3$s">%4$s</label></div>', $name, $value, $id, $modified_label ); // need to look at switching selected to checked
            } 
        } 
    } 

   // echo '</fieldset>'; 
} 
global $product;

$attribute_keys = array_keys( $attributes );
$user_id = get_current_user_id();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce-subscriptions' ); ?></p>
	<?php else : ?>
		<?php if ( ! $product->is_purchasable() && 0 != $user_id && 'no' != $product->limit_subscriptions && ( ( 'active' == $product->limit_subscriptions && wcs_user_has_subscription( $user_id, $product->id, 'on-hold' ) ) || $user_has_subscription = wcs_user_has_subscription( $user_id, $product->id, $product->limit_subscriptions ) ) ) : ?>
			<?php if ( 'any' == $product->limit_subscriptions && $user_has_subscription && ! wcs_user_has_subscription( $user_id, $product->id, 'active' ) && ! wcs_user_has_subscription( $user_id, $product->id, 'on-hold' ) ) : // customer has an inactive subscription, maybe offer the renewal button ?>
				<?php $resubscribe_link = wcs_get_users_resubscribe_link_for_product( $product->id ); ?>
				<?php if ( ! empty( $resubscribe_link ) ) : ?>
					<a href="<?php echo esc_url( $resubscribe_link ); ?>" class="button product-resubscribe-link"><?php esc_html_e( 'Resubscribe', 'woocommerce-subscriptions' ); ?></a>
				<?php endif; ?>
			<?php else : ?>
				<p class="limited-subscription-notice notice"><?php esc_html_e( 'You have an active subscription to this product already.', 'woocommerce-subscriptions' ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<table class="variations" cellspacing="0">
				<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></label></td>
						<td class="value">
							<?php
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
							wc_dropdown_variation_attribute_options_sub( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
							echo wp_kses( end( $attribute_keys ) === $attribute_name ? '<a class="reset_variations" href="#">' . __( 'Clear selection', 'woocommerce-subscriptions' ) . '</a>' : '', array( 'a' => array( 'class' => array(), 'href' => array() ) ) );
							?>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

			<div class="single_variation_wrap" style="display:none;">
				<?php
				/**
				 * woocommerce_before_single_variation Hook
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook
				 */
				do_action( 'woocommerce_after_single_variation' );
				?>
			</div>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

