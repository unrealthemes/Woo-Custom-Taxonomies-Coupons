<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Ut_View_Maker {

	/**
	 * @param $coupon WP_Post
	 *
	 * @param bool $options
	 *
	 * @return string
	 */
	public function ut_create_meta_box_html( $coupon, $options ) {

		$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );
		$saved_taxonomies = get_post_meta( $coupon->ID, 'ut_products_taxonomies', true );
		$all_terms = [];
		foreach ( $taxonomy_objects as $taxonomy_object ) {
			$terms = get_terms( [
				'taxonomy'   => $taxonomy_object->name,
				'hide_empty' => false,
			] );
			$all_terms[ $taxonomy_object->label ] = $terms;
		}
		ob_start();
        ?>

        <style>
            #select2-selected-taxonomies-results .select2-results__group {
                background: #eeeeee;
                color: #555;
            }
        </style>

        <div class="options_group">
			<?php wp_nonce_field( 'woo_coupon', 'woo_coupon' ); ?>
            <table class="wp-list-table widefat fixed striped pages">
                <tr>
                    <td>
                        <label for="enable-woo-tax-coupon">
							<?php _e( 'Enable Woo Taxonomies Coupons For This Coupon?', 'woo-taxonomies-coupons' ); ?>
                        </label>
                    </td>
                    <td>
                        <input type="checkbox" class="checkbox" name="enable-woo-tax-coupon" id="enable-woo-tax-coupon" data-checked="<?php echo $options; ?>" <?php if ( $options === 'yes' ) { echo 'checked'; } ?> />
                    </td>
                </tr>
            </table>
            <br>
            <table class="wp-list-table widefat fixed striped pages" id="if_product_tax_enabled">
                <tr>
                    <td>
                        <label for="woo-tax-cop-select">
							<?php _e( 'Select Taxonomies', 'woo-taxonomies-coupons' ); ?>
                        </label>
                    </td>
                    <td>
                        <select multiple="multiple" 
                                class="coupon-taxonomy" 
                                id="selected-taxonomies" 
                                style="width: 100%;" 
                                name="selected_taxonomies[]">
                            <option></option>

							<?php 
                            foreach ( (array)$all_terms as $label => $terms ) : 

                                if ( ! empty( $terms ) ) :
                                ?>

                                    <optgroup label="<?php echo esc_attr( $label ); ?>:">

                                        <?php 
                                        foreach ( (array)$terms as $term ) : 
                                            $selected = ( in_array( $term->term_id, $saved_taxonomies ) ) ? 'selected' : '';
                                            ?>

                                                <option value="<?php echo esc_attr( $term->term_id ) ; ?>" <?php echo esc_attr( $selected ); ?>>
                                                    &nbsp &nbsp
                                                    <?php echo $term->name; ?>
                                                </option>

    									   <?php 
                                        endforeach;
                                        ?>

                                    </optgroup>

                                <?php 
                                endif;

                            endforeach; 
                            ?>

                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <script>
            jQuery(document).ready( function ($) {

                $('.coupon-taxonomy').select2({
                    placeholder: '<?php _e( 'Select ...', 'woo-taxonomies-coupons' ); ?>',
                    multiple: true,
                    dropdownAutoWidth: true,
                    allowClear: true
                });

                $("#enable-woo-tax-coupon").change(function () {

                    if (this.checked) {
                        $('#if_product_tax_enabled').slideDown();
                    } else {
                        // TODO : Clear the Input and hide
                        $('#if_product_tax_enabled').slideUp();
                    }
                });

				<?php if ( $options === 'yes' ) : ?>
                    $('#if_product_tax_enabled').slideDown();
				<?php else : ?>
                    $('#if_product_tax_enabled').slideUp();
				<?php endif; ?>
            });
        </script>

		<?php 
        $html = ob_get_contents();
		ob_clean();

		return $html;
	}

}