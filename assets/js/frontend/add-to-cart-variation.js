/*!
 * Variations Plugin
 */
;(function ( $, window, document, undefined ) {

	$.fn.wc_variation_form = function () {

		$.fn.wc_variation_form.find_matching_variations = function( product_variations, settings ) {
			var matching = [];

			for ( var i = 0; i < product_variations.length; i++ ) {
				var variation = product_variations[i];
				var variation_id = variation.variation_id;

				if ( $.fn.wc_variation_form.variations_match( variation.attributes, settings ) ) {
					matching.push( variation );
				}
			}

			return matching;
		};

		$.fn.wc_variation_form.variations_match = function( attrs1, attrs2 ) {
			var match = true;

			for ( var attr_name in attrs1 ) {
				if ( attrs1.hasOwnProperty( attr_name ) ) {
					var val1 = attrs1[ attr_name ];
					var val2 = attrs2[ attr_name ];

					if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
						match = false;
					}
				}
			}

			return match;
		};

		// Unbind any existing events
		this.unbind( 'check_variations update_variation_values found_variation' );
		this.find( '.reset_variations' ).unbind( 'click' );
		this.find( '.variations input' ).unbind( 'change focusin' );

		// Bind events
		$form = this

			// On clicking the reset variation button
			.on( 'click', '.reset_variations', function( event ) {

				$( this ).closest( '.variations_form' ).find( '.variations input:checked' ).removeAttr('checked').change();

				var $sku = $( this ).closest( '.product' ).find( '.sku' ),
					$weight = $( this ).closest( '.product' ).find( '.product_weight' ),
					$dimensions = $( this ).closest( '.product' ).find( '.product_dimensions' );

				if ( $sku.attr( 'data-o_sku' ) )
					$sku.text( $sku.attr( 'data-o_sku' ) );

				if ( $weight.attr( 'data-o_weight' ) )
					$weight.text( $weight.attr( 'data-o_weight' ) );

				if ( $dimensions.attr( 'data-o_dimensions' ) )
					$dimensions.text( $dimensions.attr( 'data-o_dimensions' ) );

				return false;
			} )

			// Upon changing an option
			.on( 'change', '.variations input', function( event ) {

				$variation_form = $( this ).closest( '.variations_form' );

				if ( $variation_form.find( 'input.variation_id' ).length > 0 )
					$variation_form.find( 'input.variation_id' ).val( '' ).change();
				else
					$variation_form.find( 'input[name=variation_id]' ).val( '' ).change();

				$variation_form
					.trigger( 'woocommerce_variation_select_change' )
					.trigger( 'check_variations', [ '', false ] );

				$( this ).blur();

				if( $().uniform && $.isFunction( $.uniform.update ) ) {
					$.uniform.update();
				}

				// Custom event for when variation selection has been changed
				$variation_form.trigger( 'woocommerce_variation_has_changed' );

			} )

			// Check variations
			.on( 'check_variations', function( event, exclude, focus ) {
				var all_set = true,
					any_set = false,
					showing_variation = false,
					current_settings = {},
					$variation_form = $( this ),
					$reset_variations = $variation_form.find( '.reset_variations' );

				$variation_form.find( '.variations .value' ).each( function() {
					var $radios = $( this ).find( 'input' );
					var $checked_radio = $radios.filter(':checked');

					attribute_name = $radios.attr( 'name' );

					if( $checked_radio.length == 0 ){
						all_set = false;
					} else {
						any_set = true;
					}

					// Encode entities
					value = $checked_radio.val();

					// Add to settings array
					current_settings[ attribute_name ] = value;
				});

				var product_id = parseInt( $variation_form.data( 'product_id' ) ),
					all_variations = $variation_form.data( 'product_variations' );

				// Fallback to window property if not set - backwards compat
				if ( ! all_variations )
					all_variations = window.product_variations.product_id;
				if ( ! all_variations )
					all_variations = window.product_variations;
				if ( ! all_variations )
					all_variations = window['product_variations_' + product_id ];

				if ( all_set ) {

					var matching_variations = $.fn.wc_variation_form.find_matching_variations( all_variations, current_settings );
					var variation = matching_variations.shift();

					if ( variation ) {

						// Found - set ID

						// Get variation input by class, or by input name if class doesn't exist
						if ( $variation_form.find( 'input.variation_id' ).length > 0 )
							$variation_input = $variation_form.find( 'input.variation_id' );
						else
							$variation_input = $variation_form.find( 'input[name=variation_id]' );

						// Set ID
						$variation_input
							.val( variation.variation_id )
							.change();

						$variation_form.trigger( 'found_variation', [ variation ] );

					} else {

						// Nothing found - reset fields
						$variation_form.find( '.variations select' ).val( '' );

						$variation_form.trigger( 'reset_image' );

						alert( wc_add_to_cart_variation_params.i18n_no_matching_variations_text );

					}

				} else {

					$variation_form.trigger( 'reset_image' );

					$variation_form.find( '.single_variation_wrap' ).slideUp( 200 ).trigger( 'hide_variation' );

				}

				if ( any_set ) {

					if ( $reset_variations.css( 'visibility' ) === 'hidden' )
						$reset_variations.css( 'visibility', 'visible' ).hide().fadeIn();

				} else {

					$reset_variations.css( 'visibility', 'hidden' );
					$sku = $( this ).closest( '.product' ).find( '.sku' );
					$sku.text( $sku.attr( 'data-o_sku' ) );

				}

				$variation_form.trigger( 'check_available_attributes', [ all_variations ] );

			} )

			// Reset product image
			.on( 'reset_image', function( event ) {

				var $product = $(this).closest( '.product' ),
					$product_img = $product.find( 'div.images img:eq(0)' ),
					$product_link = $product.find( 'div.images a.zoom:eq(0)' ),
					o_src = $product_img.attr( 'data-o_src' ),
					o_title = $product_img.attr( 'data-o_title' ),
					o_alt = $product_img.attr( 'data-o_alt' ),
					o_href = $product_link.attr( 'data-o_href' );

				if ( o_src !== undefined ) {
					$product_img
						.attr( 'src', o_src );
				}

				if ( o_href !== undefined ) {
					$product_link
						.attr( 'href', o_href );
				}

				if ( o_title !== undefined ) {
					$product_img
						.attr( 'title', o_title );
					$product_link
						.attr( 'title', o_title );
				}

				if ( o_alt !== undefined ) {
					$product_img
						.attr( 'alt', o_alt );
				}
			} )

			// Disable / enable radio buttons, according to other checked attributes
			.on( 'check_available_attributes', function( event, variations ) {

				$variation_form = $( this ).closest( '.variations_form' );

				// Check each radio buttons group (attribute)
				$variation_form.find( '.value' ).each( function( index, el ) {

					// Get other attributes, if any
					$other_attrs = $variation_form.find( '.value' ).not(el);
					if ($other_attrs.length == 0) {
						//Even if there's only one attribute, some variations may be disabled 
						//through the 'woocommerce_variation_is_active' filter, so we still need to go through all variations..
						//	return;
					}

					var other_settings = {};

					// Loop through other attributes
					$other_attrs.each( function( index, other_el ) {
						var $radios = $( this ).find( 'input' );
						var attribute_name = $radios.attr( 'name' );
						var $checked_radio = $radios.filter(':checked');

						// Encode entities
						value = $checked_radio.val();

						// Add to settings array
						other_settings[ attribute_name ] = value;
					});

					// Get matching variations, according to the other attributes
					var matching_variations = $.fn.wc_variation_form.find_matching_variations( variations, other_settings );

					// Disable all radio options of this attribute, then we'll check if should be enabled
					var $current_radios = $( el ).find( 'input' );
					$current_radios.attr( 'disabled', 'disabled' );

					var current_attr_name = $current_radios.attr( 'name' );

					// Loop through variations
					for ( var num in matching_variations ) {

						if ( typeof( matching_variations[ num ] ) == 'undefined' || ! matching_variations[ num ].variation_is_active ) {
							continue;
						}

						var attributes = matching_variations[ num ].attributes;

						for ( var attr_name in attributes ) {
							if ( ! attributes.hasOwnProperty( attr_name ) || attr_name != current_attr_name  ) {
								continue;
							}

							var attr_val = attributes[ attr_name ];

							if ( attr_val ) {

								// Decode entities
								attr_val = $( '<div/>' ).html( attr_val ).text();

								// Add slashes
								attr_val = attr_val.replace( /'/g, "\\'" );
								attr_val = attr_val.replace( /"/g, "\\\"" );

								// Compare the meerkat
								$current_radios.filter( '[value="' + attr_val + '"]' ).removeAttr( 'disabled' );

							} else {
								$current_radios.removeAttr( 'disabled' );
							}
						}
					}

				});

				// Custom event for when variations have been updated
				$variation_form.trigger( 'woocommerce_update_variation_values' );

			} )

			// Show single variation details (price, stock, image)
			.on( 'found_variation', function( event, variation ) {
				var $variation_form = $( this ),
					$product = $( this ).closest( '.product' ),
					$product_img = $product.find( 'div.images img:eq(0)' ),
					$product_link = $product.find( 'div.images a.zoom:eq(0)' ),
					o_src = $product_img.attr( 'data-o_src' ),
					o_title = $product_img.attr( 'data-o_title' ),
					o_alt = $product_img.attr( 'data-o_alt' ),
					o_href = $product_link.attr( 'data-o_href' ),
					variation_image = variation.image_src,
					variation_link  = variation.image_link,
					variation_title = variation.image_title,
					variation_alt = variation.image_alt;

				$variation_form.find( '.variations_button' ).show();
				$variation_form.find( '.single_variation' ).html( variation.price_html + variation.availability_html );

				if ( o_src === undefined ) {
					o_src = ( ! $product_img.attr( 'src' ) ) ? '' : $product_img.attr( 'src' );
					$product_img.attr( 'data-o_src', o_src );
				}

				if ( o_href === undefined ) {
					o_href = ( ! $product_link.attr( 'href' ) ) ? '' : $product_link.attr( 'href' );
					$product_link.attr( 'data-o_href', o_href );
				}

				if ( o_title === undefined ) {
					o_title = ( ! $product_img.attr( 'title' ) ) ? '' : $product_img.attr( 'title' );
					$product_img.attr( 'data-o_title', o_title );
				}

				if ( o_alt === undefined ) {
					o_alt = ( ! $product_img.attr( 'alt' ) ) ? '' : $product_img.attr( 'alt' );
					$product_img.attr( 'data-o_alt', o_alt );
				}

				if ( variation_image && variation_image.length > 1 ) {
					$product_img
						.attr( 'src', variation_image )
						.attr( 'alt', variation_alt )
						.attr( 'title', variation_title );
					$product_link
						.attr( 'href', variation_link )
						.attr( 'title', variation_title );
				} else {
					$product_img
						.attr( 'src', o_src )
						.attr( 'alt', o_alt )
						.attr( 'title', o_title );
					$product_link
						.attr( 'href', o_href )
						.attr( 'title', o_title );
				}

				var $single_variation_wrap = $variation_form.find( '.single_variation_wrap' ),
					$sku = $product.find( '.product_meta' ).find( '.sku' ),
					$weight = $product.find( '.product_weight' ),
					$dimensions = $product.find( '.product_dimensions' );

				if ( ! $sku.attr( 'data-o_sku' ) )
					$sku.attr( 'data-o_sku', $sku.text() );

				if ( ! $weight.attr( 'data-o_weight' ) )
					$weight.attr( 'data-o_weight', $weight.text() );

				if ( ! $dimensions.attr( 'data-o_dimensions' ) )
					$dimensions.attr( 'data-o_dimensions', $dimensions.text() );

				if ( variation.sku ) {
					$sku.text( variation.sku );
				} else {
					$sku.text( $sku.attr( 'data-o_sku' ) );
				}

				if ( variation.weight ) {
					$weight.text( variation.weight );
				} else {
					$weight.text( $weight.attr( 'data-o_weight' ) );
				}

				if ( variation.dimensions ) {
					$dimensions.text( variation.dimensions );
				} else {
					$dimensions.text( $dimensions.attr( 'data-o_dimensions' ) );
				}

				$single_variation_wrap.find( '.quantity' ).show();

				if ( ! variation.is_purchasable || ! variation.is_in_stock || ! variation.variation_is_visible ) {
					$variation_form.find( '.variations_button' ).hide();
				}

				if ( ! variation.variation_is_visible ) {
					$variation_form.find( '.single_variation' ).html( '<p>' + wc_add_to_cart_variation_params.i18n_unavailable_text + '</p>' );
				}

				if ( variation.min_qty !== '' )
					$single_variation_wrap.find( '.quantity input.qty' ).attr( 'min', variation.min_qty ).val( variation.min_qty );
				else
					$single_variation_wrap.find( '.quantity input.qty' ).removeAttr( 'min' );

				if ( variation.max_qty !== '' )
					$single_variation_wrap.find( '.quantity input.qty' ).attr( 'max', variation.max_qty );
				else
					$single_variation_wrap.find( '.quantity input.qty' ).removeAttr( 'max' );

				if ( variation.is_sold_individually === 'yes' ) {
					$single_variation_wrap.find( '.quantity input.qty' ).val( '1' );
					$single_variation_wrap.find( '.quantity' ).hide();
				}

				$single_variation_wrap.slideDown( 200 ).trigger( 'show_variation', [ variation ] );

			});

		$form.trigger( 'wc_variation_form' );

		return $form;
	};

	$( function() {

		// wc_add_to_cart_variation_params is required to continue, ensure the object exists
		if ( typeof wc_add_to_cart_variation_params === 'undefined' )
			return false;

		$( '.variations_form' ).wc_variation_form();
		$( '.variations_form .variations input' ).change();
	});

})( jQuery, window, document );
