<?php
/**
 * Hooks of cart.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of checkout template.
 */
class Cart {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Cross sell product
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sells_columns' ) );

		add_action( 'woocommerce_cart_is_empty', array( $this, 'cart_empty_text' ), 20 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
	}

	/**
	 * Change columns upsell
	 *
	 * @return void
	 */
	public function cross_sells_columns( $columns ) {
		$columns = 4;

		return $columns;
	}

	/**
	 * Add cart empty heading and sub heading
	 *
	 * @return void
	 */
	public function cart_empty_text() {
		echo sprintf(
			'<div class="em-cart-text-empty text-center"><h5>%s</h5><p>%s</p></div>',
			esc_html__( 'Your cart is empty', 'ecomus' ),
			esc_html__( 'You may check out all the available products and buy some in the shop', 'ecomus' )
		);
	}

	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
		$_product   = $cart_item['data'];
		if( WC()->cart->display_prices_including_tax() ) {
			$_product_regular_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_regular_price() ) ) );
			$_product_sale_price = floatval( wc_get_price_including_tax( $_product, array( 'price' => $_product->get_price() ) ) );
		} else {
			$_product_regular_price = floatval( $_product->get_regular_price() );
			$_product_sale_price = floatval( $_product->get_price() );
		}

		if( $_product_sale_price > 0 && $_product_regular_price > $_product_sale_price ) {
			$subtotal .= '<br/><span class="ecomus-price-saved">' . esc_html__( 'Save: ', 'ecomus' ) . wc_price( ( $_product_regular_price * $cart_item['quantity'] ) - ( $_product_sale_price * $cart_item['quantity'] ) ) .'</span>';
		}

		return $subtotal;
	}
}
