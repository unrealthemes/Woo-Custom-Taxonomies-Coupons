<?php

final class Ut_Woo_Taxonomies_Coupons {

	/**
	 * Ut_Woo_Taxonomies_Coupons constructor.
	 */
	public function __construct() {

		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 *
	 */
	public function define_admin_hooks() {
		
		add_action( 'add_meta_boxes', [ $this, 'create_meta_box' ] );
		add_action( 'save_post_shop_coupon', [ $this, 'save_coupon' ] );
	}

	/**
	 * @param $coupon_id
	 */
	public function save_coupon( $coupon_id ) {

		$data = [];
		$data[ 'woo_coupon' ]            = ( isset( $_POST[ 'woo_coupon' ] ) ) ? $_POST[ 'woo_coupon' ] : null ;
		$data[ 'enable-woo-tax-coupon' ] = ( isset( $_POST[ 'enable-woo-tax-coupon' ] ) ) ? $_POST[ 'enable-woo-tax-coupon' ] : null ;
		$data[ 'selected_taxonomies' ]   = ( isset( $_POST[ 'selected_taxonomies' ] ) ) ? $_POST[ 'selected_taxonomies' ] : null ;

		$this->save_coupon_meta( $coupon_id, sanitize_post( $data ) );
	}

	/**
	 * @param $coupon_id
	 * @param $data
	 */
	public function save_coupon_meta( $coupon_id, $data ) {

		if ( ! wp_verify_nonce( $data[ 'woo_coupon' ], 'woo_coupon' ) ) {
			return;
		}

		if ( ! is_null( $data[ 'enable-woo-tax-coupon' ] ) && $data[ 'enable-woo-tax-coupon' ] === 'on' ) {
			update_post_meta( $coupon_id, 'ut_tax_enabled', 'yes' );
			update_post_meta( $coupon_id, 'ut_products_taxonomies', $data[ 'selected_taxonomies' ] );
		} else {
			update_post_meta( $coupon_id, 'ut_tax_enabled', 'no' );
		}
	}

	/**
	 *
	 */
	public function create_meta_box() {

		global $post;
		$title = __( 'Coupon details', 'woo-taxonomies-coupons' );
		add_meta_box(
			'woo-tax-coupon',
			$title,
			[ $this, 'create_meta_box_html' ],
			'shop_coupon'
		);
	}

	/**
	 * @param $coupon WP_Post
	 */
	public function create_meta_box_html( $coupon ) {

		$options = get_post_meta( $coupon->ID, 'ut_tax_enabled', true );
		echo ( new Ut_View_Maker() )->ut_create_meta_box_html( $coupon, $options );
	}

	/**
	 *
	 */
	public function define_public_hooks() {

	}

}