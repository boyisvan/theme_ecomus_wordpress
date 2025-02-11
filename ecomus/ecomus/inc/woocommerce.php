<?php
/**
 * Woocommerce functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Woocommerce initial
 *
 */
class WooCommerce {
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
		$this->init();
		add_action( 'after_setup_theme', array( $this, 'woocommerce_setup' ) );
		add_action( 'wp', array( $this, 'add_actions' ), 10 );

		add_filter( 'woocommerce_get_script_data', array( $this, 'get_script_data' ), 10, 2 );
		add_filter( 'woocommerce_get_image_size_gallery_thumbnail', array( $this, 'get_gallery_thumbnail_size' ) );
	}

	/**
	 * WooCommerce Init
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		\Ecomus\WooCommerce\General::instance();
		\Ecomus\WooCommerce\Settings::instance();
		\Ecomus\WooCommerce\Sidebar::instance();
		\Ecomus\WooCommerce\Customizer::instance();
		\Ecomus\WooCommerce\Dynamic_CSS::instance();
		\Ecomus\WooCommerce\Loop\Product_Card::instance();
		\Ecomus\WooCommerce\Badges::instance();
		\Ecomus\WooCommerce\Mini_Cart::instance();
		\Ecomus\WooCommerce\Single_Product_Summary::instance();
		\Ecomus\WooCommerce\Login::instance();

		if( is_admin() ) {
			\Ecomus\WooCommerce\Track_Order::instance();
		}
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		if ( function_exists('wcboost_wishlist') ) {
			\Ecomus\WooCommerce\Wishlist::instance();
		}

		if ( function_exists('wcboost_products_compare') ) {
			\Ecomus\WooCommerce\Compare::instance();
		}

		if( function_exists('is_account_page') && is_account_page() ) {
			\Ecomus\WooCommerce\My_Account::instance();
		}

		if ( \Ecomus\Helper::is_catalog() ) {
			\Ecomus\WooCommerce\Catalog::instance();
			\Ecomus\WooCommerce\Shop_Header::instance();
		}

		if ( function_exists('is_cart') && is_cart() ) {
			\Ecomus\WooCommerce\Cart::instance();
		}

		if ( function_exists('is_checkout') && is_checkout() ) {
			\Ecomus\WooCommerce\Checkout::instance();
		}

		if ( apply_filters('ecomus_load_single_product_layout', is_singular( 'product' ) ) ) {
			\Ecomus\WooCommerce\Single_Product\Product_Layout::instance();
		}

		if( function_exists( 'wcboost_variation_swatches' ) ) {
			\Ecomus\WooCommerce\Loop\Product_Attribute::instance();
		}

		\Ecomus\WooCommerce\Loop\Quick_Add::instance();

		if( Helper::get_option( 'product_card_quick_view' ) ) {
			\Ecomus\WooCommerce\Loop\Quick_View::instance();
		}

		\Ecomus\WooCommerce\Product_Notices::instance();

	}

		/**
	 * WooCommerce setup function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function woocommerce_setup() {
		add_theme_support( 'woocommerce', array(
			'product_grid' => array(
				'default_rows'    => 4,
				'min_rows'        => 2,
				'max_rows'        => 20,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 7,
			),
			'wishlist' => array(
				'single_button_position' => 'theme',
				'loop_button_position'   => 'theme',
				'button_type'            => 'theme',
			),
		) );

		add_theme_support( 'wc-product-gallery-slider' );

		if ( Helper::get_option( 'product_image_lightbox' ) ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 * @return array|bool
	 */
	public function get_script_data( $params, $handle ) {
		if( $handle == 'wc-single-product' ) {
			$params['flexslider_enabled'] = false;
			$params['photoswipe_enabled'] = false;
		}

		return $params;
	}

	/**
	 * Set the gallery thumbnail size.
	 *
	 * @since 1.0.0
	 *
	 * @param array $size Image size.
	 *
	 * @return array
	 */
	public function get_gallery_thumbnail_size( $size ) {
		$size['width'] = 130;
		$size['height'] = 0;
		$size['crop']   = 1;

		return $size;
	}
}
