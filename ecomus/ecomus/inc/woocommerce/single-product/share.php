<?php
/**
 * Single Product hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Single_Product;

use Ecomus\Helper;
use Ecomus\Icon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Single Product
 */
class Share {
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
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_share' ), 34 );

		add_action( 'ecomus_after_close_site_footer', array( $this, 'product_share_content' ) );
	}

	/**
	 * Product Share
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_share() {
		echo '<a href="#" class="ecomus-extra-link-item ecomus-extra-link-item--share em-font-semibold" data-toggle="modal" data-target="product-share-modal">'. Icon::get_svg( 'share' ) . esc_html__( 'Share', 'ecomus' ) . '</a>';
	}

	/**
	 * Product Share content
	 */
	public function product_share_content() {
		if( ! apply_filters( 'ecomus_product_share_content', true ) ) {
			return;
		}

		$output = Helper::share_socials();

		get_template_part( 'template-parts/modals/product-share', '', $output );
	}
}
