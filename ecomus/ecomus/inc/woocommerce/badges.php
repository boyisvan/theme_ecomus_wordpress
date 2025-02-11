<?php
/**
 * Badges hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use Ecomus\Helper, Ecomus\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Badges
 */
class Badges {
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
		add_filter( 'ecomus_woocommerce_badges_class', array( $this, 'woocommerce_badges_class' ), 10, 2 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
		add_action( 'ecomus_product_before_loop_thumbnail', array( $this, 'badges' ), 2 );

		// Single product
		add_action( 'woocommerce_single_product_summary', array( $this, 'single_badges' ), 6 );
		add_action( 'ecomus_woocommerce_product_quickview_summary', array( $this, 'single_badges' ), 17 );

		// For product loop primary
		add_action( 'ecomus_product_loop_primary_thumbnail', array( $this, 'badges' ), 2 );
	}

	/**
	 * Product badges class
	 *
	 * @return void
	 */
	public function woocommerce_badges_class( $classes ) {
		if( Helper::get_option( 'badges_soldout_position' ) == 'center' ) {
			$classes .= ' sold-out--center';
		}

		if( in_array( \Ecomus\WooCommerce\Helper::get_product_card_layout(), ['3', '4'] ) ) {
			$classes .= ' woocommerce-badges--right';
		}

		return $classes;
	}

	/**
	 * Product badges.
	 */
	public static function badges( $product = null, $classes = '', $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$badges = array();
		$custom_badges       = maybe_unserialize( get_post_meta( $product->get_id(), 'custom_badges_text', true ) );
		if ( $custom_badges ) {
			$style    = '';
			$custom_badges_bg    = get_post_meta( $product->get_id(), 'custom_badges_bg', true );
			$custom_badges_color = get_post_meta( $product->get_id(), 'custom_badges_color', true );
			$bg_color = ! empty( $custom_badges_bg ) ? '--id--badge-custom-bg:' . $custom_badges_bg . ';' : '';
			$color    = ! empty( $custom_badges_color ) ? '--id--badge-custom-color:' . $custom_badges_color . ';' : '';

			if ( $bg_color || $color ) {
				$style = 'style="' . $color . $bg_color . '"';
			}

			$badges['html']['custom'] = '<span class="custom woocommerce-badge"' . $style . '>' . esc_html( $custom_badges ) . '</span>';
		} else {
			$badges = self::get_badges( $product, $args );
		}

		if ( ! empty( $badges['html'] ) ) {
			$classes = ! empty( $badges['class'] ) ? $badges['class'] : $classes;
			$countdown = ! empty( $badges['countdown'] ) ? $badges['countdown'] : '';
			printf( '<span class="woocommerce-badges %s">%s</span>%s', esc_attr( apply_filters( 'ecomus_woocommerce_badges_class', $classes ) ), implode( '', $badges['html'] ), $countdown );
		}
	}

	/**
	 * Single product badges.
	 */
	public static function single_badges( $product, $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}
		$args = wp_parse_args(
			$args,
			array(
				'badges_sale'           => Helper::get_option( 'product_badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'product_badges_sale_type' ),
				'badges_new'            => Helper::get_option( 'product_badges_new' ),
				'badges_featured'       => Helper::get_option( 'product_badges_featured' ),
				'badges_in_stock'       => Helper::get_option( 'product_badges_in_stock' ),
				'badges_soldout'        => Helper::get_option( 'product_badges_sold_out' ),
				'badges_pre_order'      => Helper::get_option( 'product_badges_pre_order' ),
				'is_single'	            => true,
				'badges_sale_countdown' => false,
			)
		);

		self::badges( $product, 'woocommerce-badges--single', $args );
	}

	/**
	 * Get product badges.
	 *
	 * @return array
	 */
	public static function get_badges( $product = array(), $args = array() ) {
		if( empty( $product ) ) {
			global $product;
		}

		$args = wp_parse_args(
			$args,
			array(
				'badges_soldout'        => Helper::get_option( 'badges_soldout' ),
				'badges_soldout_text'   => esc_html__( 'Sold out', 'ecomus' ),
				'badges_sale'           => Helper::get_option( 'badges_sale' ),
				'badges_sale_type'      => Helper::get_option( 'badges_sale_type' ),
				'badges_sale_text'      => esc_html__( 'Sale', 'ecomus' ),
				'badges_new'            => Helper::get_option( 'badges_new' ),
				'badges_new_text'       => esc_html__( 'New', 'ecomus' ),
				'badges_featured'       => Helper::get_option( 'badges_featured' ),
				'badges_featured_text'  => esc_html__( 'Hot', 'ecomus' ),
				'badges_pre_order'      => Helper::get_option( 'badges_pre_order' ),
				'badges_pre_order_text' => esc_html__( 'Pre-Order', 'ecomus' ),
			)
		);

		$badges = array();
		$badges['class'] = $badges['countdown'] = '';

		if ( $args['badges_soldout'] && $product->get_stock_status() == 'outofstock' ) {
			$text               = ! empty( $args['badges_soldout_text'] ) ? $args['badges_soldout_text'] : esc_html__( 'Out Of Stock', 'ecomus' );
			$badges['html']['sold-out'] = '<span class="sold-out woocommerce-badge">' . esc_html( $text ) . '</span>';

			if( ! empty( $args['is_single'] ) ) {
				$badges['class']    = 'woocommerce-badges--single sold-out';
			} else {
				$badges['class']    = 'sold-out';
			}

		} else {
			if( $product->is_in_stock() && ! empty( $args['badges_in_stock'] ) && ! $product->is_on_backorder() && $product->is_purchasable() ) {
				$badges['html']['in_stock'] = '<span class="in-stock woocommerce-badge">' . esc_html__( 'In Stock', 'ecomus' ) . '</span>';
			}

			if ( $product->is_on_sale() && $args['badges_sale'] ) {
				$badges['html']['sale'] = self::sale_flash( $product, $args );

				$classes = 'ecomus-badges-sale__countdown';
				$classes .= Helper::get_option( 'product_card_attribute_second' ) !== 'none' && ! empty( $product->get_attribute( esc_attr( Helper::get_option( 'product_card_attribute_second' ) ) ) ) ? ' ecomus-badges-sale__attribute-second' : '';

				if( ! isset( $args['badges_sale_countdown'] ) && Helper::get_option( 'badges_sale_countdown' ) ) {
					$badges['countdown'] = \Ecomus\WooCommerce\Helper::get_product_countdown( '', '', $classes, $product );
				}
			}

			else if ( $args['badges_new'] && in_array( $product->get_id(), WooCommerce\General::ecomus_woocommerce_get_new_product_ids() ) ) {
				$text          = $args['badges_new_text'];
				$text          = empty( $text ) ? esc_html__( 'New', 'ecomus' ) : $text;
				$badges['html']['new'] = '<span class="new woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			else if ( $product->is_featured() && $args['badges_featured'] ) {
				$text               = $args['badges_featured_text'];
				$text               = empty( $text ) ? esc_html__( 'Hot', 'ecomus' ) : $text;
				$badges['html']['featured'] = '<span class="featured woocommerce-badge">' . esc_html( $text ) . '</span>';
			}

			if ( $args['badges_pre_order'] && $product->is_on_backorder() ) {
				$text          = $args['badges_pre_order_text'];
				$text          = empty( $text ) ? esc_html__( 'Pre-Order', 'ecomus' ) : $text;
				$badges['html']['pre_order'] = '<span class="pre-order woocommerce-badge">' . esc_html( $text ) . '</span>';
			}
		}


		$badges = apply_filters( 'ecomus_product_badges', $badges, $product );

		return $badges;
	}

	/**
	 * Sale badge.
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public static function sale_flash( $product, $args ) {
		if ( 'grouped' == $product->get_type() ) {
			return '';
		}
		$output = '';
		$type       = $args[ 'badges_sale_type' ];
		$text       =  ! empty( $args['badges_sale_text'] ) ? $args['badges_sale_text'] : esc_html__( 'Sale', 'ecomus' );
		$percentage = 0;
		$saved      = 0;

		if ( 'percent' == $type || false !== strpos( $text, '{%}' ) || false !== strpos( $text, '{$}' ) ) {
			if ( $product->get_type() == 'variable' ) {
				$available_variations = $product->get_available_variations();
				$max_percentage       = 0;
				$max_saved            = 0;
				$total_variations     = count( $available_variations );

				for ( $i = 0; $i < $total_variations; $i++ ) {
					$variation_id        = $available_variations[ $i ]['variation_id'];
					$variable_product    = new \WC_Product_Variation( $variation_id );
					$regular_price       = $variable_product->get_regular_price();
					$sales_price         = $variable_product->get_sale_price();
					$variable_saved      = $regular_price && $sales_price ? ( $regular_price - $sales_price ) : 0;
					$variable_percentage = $regular_price && $sales_price ? round( ( ( ( $regular_price - $sales_price ) / $regular_price ) * 100 ) ) : 0;

					if ( $variable_saved > $max_saved ) {
						$max_saved = $variable_saved;
					}

					if ( $variable_percentage > $max_percentage ) {
						$max_percentage = $variable_percentage;
					}
				}

				$saved      = $max_saved ? $max_saved : $saved;
				$percentage = $max_percentage ? $max_percentage : $percentage;
			} elseif ( $product->get_regular_price() != 0 ) {
				$saved      = $product->get_regular_price() - $product->get_sale_price();
				$percentage = round( ( $saved / $product->get_regular_price() ) * 100 );
			}
		}

		if ( 'percent' == $type ) {
			$output = '<span class="onsale woocommerce-badge">-' . $percentage . '%</span>';
		} else {
			$output = '<span class="onsale woocommerce-badge">' . wp_kses_post( $text ) . '</span>';
		}

		return $output;
	}

	/**
	 * Get sold of product deal
	 */
	public static function get_sold() {
		global $product;

		$limit = get_post_meta( $product->get_id(), '_deal_quantity', true );
		$sold  = intval( get_post_meta( $product->get_id(), '_deal_sales_counts', true ) );

		$output = ! empty( $limit ) ? '(' . esc_html( $sold ) . '/' . esc_html( $limit ) . ' ' . esc_html__( 'sold', 'ecomus' ) . ')' : '';

		return $output;
	}
}
