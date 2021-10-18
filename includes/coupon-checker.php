<?php
/**
 * @param $valid
 * @param $product
 * @param $instance
 * @param $values
 *
 * @return bool
 */
function ut_filter_woo_coupon_is_valid_for_product( $valid, $product, $instance, $values ) {

	$options = get_post_meta( $instance->get_id(), 'ut_tax_enabled', true );

	if ( $options && $options === 'yes' ) {	
		$taxonomies = get_post_meta( $instance->get_id(), 'ut_products_taxonomies', true ); 

		if ( ! $taxonomies || ! is_array( $taxonomies ) ) {
			return false;
		}
		$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );

		if ( ! is_array( $taxonomy_objects ) ) {
			return false;
		}
		$all_product_terms = [];
		foreach ( $taxonomy_objects as $taxonomy_object ) {
			$product_terms = get_the_terms( $product->get_id(), $taxonomy_object->name );

			if ( is_array( $product_terms ) && count( $product_terms ) > 0 ) {	
				foreach ( $product_terms as $product_term ) {
					array_push( $all_product_terms, $product_term->term_id );
				}
			}
		}	
		foreach ( $taxonomies as $taxonomy ) {

			if ( in_array( $taxonomy, $all_product_terms ) ) {
				return true;
			}
		}	
		return false;
	} 

	return $valid;
}

add_filter( 'woocommerce_coupon_is_valid_for_product', 'ut_filter_woo_coupon_is_valid_for_product', 10, 4 ); 