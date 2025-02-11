<?php
/**
 * Ecomus Blog Header functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ecomus Post
 *
 */
class Shop_Header {

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
		add_action('ecomus_page_header_content', array( $this, 'description' ), 20);

		add_filter('ecomus_page_header_classes', array( $this, 'classes' ));
		add_filter('ecomus_get_page_header_elements', array( $this, 'elements' ));
	}

	/**
	 * Get description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function description( $description ) {
		if( ! in_array( 'description', \Ecomus\Page_Header::get_items() ) ) {
			return;
		}

		ob_start();
		if( function_exists('is_shop') && is_shop() ) {
			woocommerce_product_archive_description();
		}

		$description = ob_get_clean();

		if ( is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$description = $term->description;
			}
		}

		if( $description ) {
			$description = wp_trim_words( $description, Helper::get_option( 'shop_header_number_words' ) );
			echo '<div class="page-header__description text-center">'. $description .'</div>';
		}
	}

	/**
	 * Page Header Classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function classes( $classes ) {
		$classes .= ' page-header--shop';

		return $classes;
	}

	/**
	 * Page Header Elements
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function elements( $items ) {
		$items = \Ecomus\Helper::get_option('shop_header') ? (array) \Ecomus\Helper::get_option( 'shop_header_els' ) : [];

		return apply_filters('ecomus_shop_header_elements', $items);
	}
}
