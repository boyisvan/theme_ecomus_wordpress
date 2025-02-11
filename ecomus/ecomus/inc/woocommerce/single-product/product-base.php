<?php
/**
 * Single Product Layout hooks.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce\Single_Product;

use Ecomus\Helper;
use Ecomus\Icon;

use function WPML\FP\Strings\remove;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class product layout of Single Product
 */
class Product_Base {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	/**
	 * Post ID
	 *
	 * @var $post_id
	 */
	protected static $post_id = null;

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
		self::$post_id = get_the_ID();
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 20 );
		add_filter( 'ecomus_wp_script_data', array( $this, 'single_product_script_data' ), 10, 3 );

		add_action( 'woocommerce_before_single_product', array( $this, 'add_post_class' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'remove_post_class' ) );

		// Page Header
		add_filter( 'ecomus_get_page_header_elements', array( $this, 'page_header_elements' ) );

		// Breadcrumb Navigation.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'breadcrumb_navigation'), 1 );

		// Gallery summary wrapper
		add_action( 'woocommerce_before_single_product_summary', array(	$this, 'open_gallery_summary_wrapper' ), 1 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'close_gallery_summary_wrapper' ), 2 );

		// Gallery thumbnail
		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 20, 1 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'product_gallery_thumbnails' ), 20 );

		// Replace the default sale flash.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );

		// Taxonomy and Brand
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_taxonomy' ), 2 );

		// Change rating
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 3 );

		// Price
		add_action('woocommerce_single_product_summary', array( $this, 'open_product_price' ), 9 );
		add_action('woocommerce_single_product_summary', array( $this, 'close_product_price' ), 12 );

		//add_action( 'woocommerce_single_product_summary', array( $this, 'stock' ), 11 );
		add_filter( 'woocommerce_get_availability', array( $this, 'change_text_stock' ), 1, 2 );

		// Format Sale Price
		add_action('woocommerce_single_product_summary', array( $this, 'add_format_sale_price' ), 5 );
		add_action('woocommerce_single_product_summary', array( $this, 'remove_format_sale_price' ), 60 );

		// Remove excerpt
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'short_description' ), 20 );

		// Add product countdown
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_countdown' ), 25 );

		// Quantity
		add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'quantity_label' ), 5 );

		// Featured button
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'open_product_featured_buttons' ), 20 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'close_product_featured_buttons' ), 22 );

		// Extra link
		add_action( 'woocommerce_single_product_summary', array( $this, 'open_product_extra_link' ), 32 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'close_product_extra_link' ), 35 );

		// Extra content
		add_action( 'woocommerce_single_product_summary', array( $this, 'product_extra_content' ), 70 );

		// Position columns of product group
		add_action( 'woocommerce_grouped_product_columns', array( $this, 'grouped_product_columns' ), 10, 2 );

		// Add data sale date
		add_action( 'woocommerce_single_variation', array( $this, 'data_product_variable' ) );

		// Product Tabs
		if( in_array( Helper::get_option( 'product_tabs_layout' ), [ 'accordion', 'list' ] ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_tabs' ), 10 );
		}

		// Single product sidebar panel
		add_action( 'ecomus_after_close_site_footer', array( $this, 'single_product_sidebar_panel' ), 1 );

		// Change add to cart text
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'product_single_add_to_cart_text' ) );
	}

	/**
	 * WooCommerce specific scripts & stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function scripts() {
		$args = array(
			'jquery'
		);

		if( Helper::get_option( 'product_image_zoom' ) !== 'none' ) {
			wp_enqueue_style( 'driff-style', get_template_directory_uri() . '/assets/css/plugins/drift-basic.css');
			wp_enqueue_script( 'driff-js', get_template_directory_uri() . '/assets/js/plugins/drift.min.js', array(), '', true );

			$args[] = 'driff-js';
		}
		
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'ecomus-single-product', get_template_directory_uri() . '/assets/js/woocommerce/single-product' . $debug . '.js', $args, '20220622', array('strategy' => 'defer') );
	}

	/**
	 * Single product script data.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function single_product_script_data( $data ) {
		$data['product_gallery_slider'] = self::product_gallery_is_slider();
		$data['product_tabs_layout']    = Helper::get_option( 'product_tabs_layout' );
		$data['product_image_zoom']     = Helper::get_option( 'product_image_zoom' );
		$data['product_image_lightbox'] = Helper::get_option( 'product_image_lightbox' );

		if( Helper::get_option( 'product_tabs_layout' ) == 'accordion' ) {
			$data['product_tabs_status'] = Helper::get_option( 'product_tabs_status' );
		}

		return $data;
	}

	public function add_post_class() {
		add_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	public function remove_post_class() {
		$rm_filter = 'remove_filter';
		$rm_filter( 'post_class', array( $this, 'product_class' ), 10, 3 );
	}

	/**
	 * Adds classes to products
     *
	 * @since 1.0.0
	 *
	 * @param string $class Post class.
	 *
	 * @return array
	 */
	public function product_class( $classes ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $classes;
		}

		global $product;

		if( Helper::get_option( 'product_tabs_layout' ) == 'vertical' ) {
			$classes[] = 'woocommerce-tabs--vertical';
		}

		if( $product->is_on_backorder() ) {
			$classes[] = 'is-pre-order';
		}

		if( class_exists( '\WCBoost\Wishlist\Frontend') ) {
			$classes[] = 'has-wishlist';
		}

		if( class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			$classes[] = 'has-compare';
		}

		return $classes;
	}

	/**
	 * Products header.
	 *
	 *  @return void
	 */
	public function page_header_elements( $items ) {
		$items = [];

		return $items;
	}

	/**
	 * Navigation
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function breadcrumb_navigation() {
		global $product;

		$term        = $product->get_category_ids()[0];
		$taxonomy    = 'product_cat';
		$prevProduct = get_previous_post( true, '', $taxonomy );
		$nextProduct = get_next_post( true, '', $taxonomy );
		?>
		<div class="ecomus-breadcrumb-navigation-wrapper">
			<?php woocommerce_breadcrumb(); ?>

			<div class="product-navigation">
				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" title="<?php echo esc_html( $nextProduct->post_title ); ?>" data-text="<?php echo esc_html( $nextProduct->post_title ); ?>">
							<?php echo Icon::get_svg( 'right-mini' ); ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" title="<?php echo esc_html( $prevProduct->post_title ); ?>" data-text="<?php echo esc_html( $prevProduct->post_title ); ?>">
							<?php echo Icon::get_svg( 'left-mini' ); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<a class="product-navigation__button" href="<?php echo get_term_link( $term, $taxonomy ); ?>" title="<?php echo get_term( $term )->name; ?>" data-text="<?php echo esc_html( 'Back to', 'ecomus' ) . ' ' . get_term( $term )->name; ?>">
					<?php echo Icon::get_svg( 'object-column' ); ?>
				</a>

				<?php if( is_rtl() ) : ?>
					<?php if( ! empty( $prevProduct ) ) : ?>
						<a class="product-navigation__button" href="<?php echo esc_url( get_permalink( $prevProduct ) ); ?>" title="<?php echo esc_html( $prevProduct->post_title ); ?>" data-text="<?php echo esc_html( $prevProduct->post_title ); ?>">
							<?php echo Icon::get_svg( 'left-mini' ); ?>
						</a>
					<?php endif; ?>
				<?php else : ?>
					<?php if( ! empty( $nextProduct ) ) : ?>
						<a class="product-navigation__button" href="<?php echo esc_url( get_permalink( $nextProduct ) ); ?>" title="<?php echo esc_html( $nextProduct->post_title ); ?>" data-text="<?php echo esc_html( $nextProduct->post_title ); ?>">
							<?php echo Icon::get_svg( 'right-mini' ); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Open gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_gallery_summary_wrapper() {
		$data = '';
		$data = apply_filters( 'ecomus_product_gallery_summary_data', $data );
		echo '<div class="product-gallery-summary"'. esc_attr( $data ) .'>';
	}

	/**
	 * Close gallery summary wrapper
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_gallery_summary_wrapper() {
		echo '</div>';
	}

	/**
	 * Single product image gallery classes
	 *
	 * @param array $args
	 * @return array
	 */
	public function single_product_image_gallery_classes( $classes ) {
		global $product;

		if( empty( Helper::get_option( 'product_gallery_layout' ) ) ) {
            $classes[] = 'woocommerce-product-gallery--vertical';
        } elseif ( Helper::get_option( 'product_gallery_layout' ) == 'right-thumbnails' ) {
			$classes[] = 'woocommerce-product-gallery--vertical';
			$classes[] = 'woocommerce-product-gallery--vertical-right';
		} elseif( in_array( Helper::get_option( 'product_gallery_layout' ), array( 'grid-1', 'grid-2', 'stacked' ) ) ) {
            $classes[] = 'woocommerce-product-gallery--grid';
            $classes[] = 'woocommerce-product-gallery--' . esc_attr( Helper::get_option( 'product_gallery_layout' ) );
        } else {
			$classes[] = 'woocommerce-product-gallery--horizontal';
		}

		$key = array_search( 'images', $classes );
		if ( $key !== false ) {
			unset( $classes[ $key ] );
		}

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && $product->get_image_id() ) {
			$classes[] = 'woocommerce-product-gallery--has-thumbnails';
		}

		if( Helper::get_option( 'product_image_zoom' ) !== 'none' ) {
			$classes[] = 'woocommerce-product-gallery--has-zoom';
		}

		return $classes;
	}

	/**
	 * Product gallery thumbnails
	 *
	 * @return void
	 */
	public function product_gallery_thumbnails() {
		global $product;

		$attachment_ids = apply_filters( 'ecomus_single_product_gallery_image_ids', $product->get_gallery_image_ids() );

		if ( $attachment_ids && $product->get_image_id() ) {
			add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );

			echo '<div class="ecomus-product-gallery-thumbnails">';
				echo apply_filters( 'ecomus_product_get_gallery_image', wc_get_gallery_image_html( $product->get_image_id() ), 1 );
				$index = 2;
				foreach ( $attachment_ids as $attachment_id ) {
					echo apply_filters( 'ecomus_product_get_gallery_thumbnail', wc_get_gallery_image_html( $attachment_id ), $index );
					$index++;
				}

			echo '</div>';
			$rm_filter = 'remove_filter';
			$rm_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false' );
		}
	}

	/**
	 * Get product taxonomy
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function product_taxonomy( $taxonomy = 'product_cat' ) {
		global $product;

		$taxonomy = Helper::get_option( 'product_taxonomy' );
		if( empty($taxonomy ) ) {
			return;
		}

		$taxonomy = empty($taxonomy) ? 'product_cat' : $taxonomy;
		$terms = get_the_terms( $product->get_id(), $taxonomy );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			echo sprintf(
				'<div class="meta meta-cat">%s <a href="%s">%s</a></div>',
				$taxonomy == 'product_brand' ? esc_html__( 'Brand:', 'ecomus' ) : esc_html__( 'Category:', 'ecomus' ),
				esc_url( get_term_link( $terms[0] ), $taxonomy ),
				esc_html( $terms[0]->name ) );
		}
	}


	/**
	 * Quantity label
	 *
	 * @return void
	 */
	public function quantity_label() {
		if ( ! apply_filters( 'ecomus_show_quantity_label', true ) ) {
			return;
		}

		global $product;

		if( ! $product->is_sold_individually() ) {
			echo '<div class="quantity__label em-font-semibold">' . esc_html__( 'Quantity:', 'ecomus') . '</div>';
		}
	}


	/**
	 * Featured button open
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function open_product_featured_buttons() {
		if( class_exists( '\WCBoost\Wishlist\Frontend') || class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			echo '<div class="product-featured-icons product-featured-icons--single-product">';
		}
	}

	/**
	 * Featured button close
	 *
	 * @static
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function close_product_featured_buttons() {
		if( class_exists( '\WCBoost\Wishlist\Frontend') || class_exists( '\WCBoost\ProductsCompare\Frontend') ) {
			echo '</div>';
		}
	}

	/**
	 * Check if product gallery is slider.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function product_gallery_is_slider() {
		$support = true;

		if( in_array( Helper::get_option( 'product_gallery_layout' ), array( 'grid-1', 'grid-2', 'stacked' ) ) ) {
			$support = false;
		}

		return apply_filters( 'ecomus_product_gallery_is_slider', $support );
	}

	/**
	 * Product Short Description
	 *
	 * @return  void
	 */
	public static function short_description() {
		if( ! Helper::get_option( 'product_description' ) ) {
			return;
		}

		global $product;

		$content = $product->get_short_description();
		if( empty( $content ) ) {
			return;
		}
		echo '<div class="short-description">';
			$option = array(
				'more'   => esc_html__( 'Show More', 'ecomus' ),
				'less'   => esc_html__( 'Show Less', 'ecomus' )
			);

			echo sprintf('<div class="short-description__content">%s</div>', wp_kses_post( do_shortcode($content) ));
			echo sprintf('
				<button class="short-description__more em-button-text show hidden" data-settings="%s">%s</button>',
				htmlspecialchars(json_encode( $option )),
				esc_html__('Show More', 'ecomus')
			);
		echo '</div>';

	}

	/**
	 * Product countdown
	 *
	 * @param string $output  The sale flash HTML.
	 * @param object $post    The post object.
	 * @param object $product The product object.
	 *
	 * @return string
	 */
	public function product_countdown() {
		global $product;

		if ( 'grouped' == $product->get_type() ) {
			return '';
		}

		$sale = array(
			'weeks'   => esc_html__( 'Weeks', 'ecomus' ),
			'week'    => esc_html__( 'Week', 'ecomus' ),
			'days'    => esc_html__( 'Days', 'ecomus' ),
			'day'     => esc_html__( 'Day', 'ecomus' ),
			'hours'   => esc_html__( 'Hours', 'ecomus' ),
			'hour'    => esc_html__( 'Hour', 'ecomus' ),
			'minutes' => esc_html__( 'Mins', 'ecomus' ),
			'minute'  => esc_html__( 'Min', 'ecomus' ),
			'seconds' => esc_html__( 'Secs', 'ecomus' ),
			'second'  => esc_html__( 'Sec', 'ecomus' ),
		);

		$text = \Ecomus\Icon::get_svg( 'clock', 'ui', 'class=em-countdown-icon em-vibrate' ) . '<span class="em-countdown-text em-font-bold">' . esc_html__( 'Hurry Up! Sale ends in:', 'ecomus' ) . '</span>';

		$classes = 'em-countdown-single-product';

		if ( $product->is_on_sale() ) {
			echo \Ecomus\WooCommerce\Helper::get_product_countdown( $sale, $text, $classes );
		}
	}

	/**
	 * Data product variable
	 *
	 * @return void
	 */
	public function data_product_variable() {
		global $product;

		if( ! $product->is_type('variable') ) {
			return;
		}

		$sale = array(
			'weeks'   => esc_html__( 'Weeks', 'ecomus' ),
			'week'    => esc_html__( 'Week', 'ecomus' ),
			'days'    => esc_html__( 'Days', 'ecomus' ),
			'day'     => esc_html__( 'Day', 'ecomus' ),
			'hours'   => esc_html__( 'Hours', 'ecomus' ),
			'hour'    => esc_html__( 'Hour', 'ecomus' ),
			'minutes' => esc_html__( 'Mins', 'ecomus' ),
			'minute'  => esc_html__( 'Min', 'ecomus' ),
			'seconds' => esc_html__( 'Secs', 'ecomus' ),
			'second'  => esc_html__( 'Sec', 'ecomus' ),
		);

		$text = \Ecomus\Icon::get_svg( 'clock', 'ui', 'class=em-countdown-icon em-vibrate' ) . '<span class="em-countdown-text em-font-bold">' . esc_html__( 'Hurry Up! Sale ends in:', 'ecomus' ) . '</span>';
		echo '<div class="em-product-item__data">';
			$variation_ids = $product->get_visible_children();
			foreach( $variation_ids as $variation_id ) {
				$variation = wc_get_product( $variation_id );

				if ( $variation->is_on_sale() ) {
					$date_on_sale_to  = $variation->get_date_on_sale_to();

					if( ! empty( $date_on_sale_to ) ) {
						$date_on_sale_to = strtotime( $date_on_sale_to );
						echo \Ecomus\WooCommerce\Helper::get_product_countdown( $sale, $text, 'em-countdown-single-product--variable variation-id-' . esc_attr( $variation_id ), null, $date_on_sale_to );
					}
				}

				$button_text = $variation->single_add_to_cart_text();
				if( $variation->is_on_backorder() ) {
					$button_text = esc_html__( 'Pre-order', 'ecomus' );
				}

				if( ! $variation->is_in_stock() ) {
					$button_text = esc_html__( 'Sold out', 'ecomus' );
				}
				
				echo '<div class="em-addtocart-text-single-product--variable" data-variation_id="' . esc_attr( $variation_id ) . '">'. esc_html( $button_text ) . '</div>';
				echo '<div class="em-badges-single-product--variable" data-variation_id="' . esc_attr( $variation_id ) . '">';
					\Ecomus\WooCommerce\Badges::single_badges( $variation );
				echo '</div>';
			}
		echo '</div>';
	}

	/**
	 * Position columns of product group
	 *
	 * @return array
	 */
	public function grouped_product_columns( $position, $product ) {
		$position = array(
			'label',
			'quantity',
			'price',
		);

		return $position;
	}

	/**
	 * Single product sidebar panel
	 *
	 * @return void
	 */
	public function single_product_sidebar_panel() {
		if( ! is_active_sidebar( 'single-product-sidebar' ) ) {
			return;
		}

		if( Helper::get_option( 'product_sidebar' ) == 'no-sidebar' ) {
			return;
		}

		$position = Helper::get_option( 'product_sidebar' ) == 'sidebar-content' ? 'left': 'right';

		\Ecomus\Theme::set_prop( 'panels', 'single-product-sidebar' );

		?>
			<div class="single-product-sidebar-panel__button sidebar-panel__button sidebar-panel__button--<?php echo esc_attr( $position ); ?> em-fixed em-flex em-flex-align-center" data-toggle="off-canvas" data-target="single-product-sidebar-panel">
				<?php echo \Ecomus\Icon::get_svg( 'sidebar' ); ?>
				<span class="button-text em-font-medium"><?php esc_html_e( 'Open Sidebar', 'ecomus' ); ?></span>
			</div>
		<?php
	}

	/**
	 * Show product tabs type dropdowm, list
	 *
	 * @return void
	 */
	public function product_tabs() {
		$product_tabs = woocommerce_default_product_tabs();

		if( empty( $product_tabs ) ) {
			return;
		}

		$type = 'dropdown';
		if( Helper::get_option( 'product_tabs_layout' ) == 'list' ) {
			$type = 'list';
		}

		$arrKey = array_keys($product_tabs);
		$lastKey = end($arrKey);
		$i = 0;
		foreach( $product_tabs as $key => $product_tab ) :
			$firstKey = ( $i == 0 ) ? $key : '';
			$tab_class = ( $key == $firstKey && Helper::get_option( 'product_tabs_status' ) == 'first' ) ? 'wc-tabs-first--opened' : '';
			$tab_class .= ( $key == $lastKey ) ? ' last' : '';

			$title_class = ( $key == $firstKey && Helper::get_option( 'product_tabs_status' ) == 'first' ) ? 'active' : '';
		?>
			<div id="tab-<?php echo esc_attr( $key ); ?>" class="woocommerce-tabs ecomus-woocommerce-tabs woocommerce-tabs--<?php echo esc_attr( $type ); ?> woocommerce-tabs--<?php echo esc_attr( $key ); ?> <?php echo esc_attr($tab_class) ?>">
				<div class="woocommerce-tabs-title em-font-semibold em-color-dark <?php echo esc_attr($title_class); ?>"><?php echo esc_html( $product_tab['title'] ); ?></div>
				<div class="woocommerce-tabs-content">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			</div>
		<?php
		$i++;
		endforeach;
	}

	/**
	 * Open product extra link
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_product_extra_link() {
		echo '<div class="ecomus-product-extra-link">';
	}

	/**
	 * Close product extra link
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_product_extra_link() {
		echo '</div>';
	}

	/**
	 * Add product extra content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_extra_content() {
		$sidebar = 'single-product-extra-content';
		if ( is_active_sidebar( $sidebar ) ) {
			echo '<div class="single-product-extra-content">';
				ob_start();
				dynamic_sidebar( $sidebar );
				$output = ob_get_clean();
				echo apply_filters( 'ecomus_single_product_extra_content', $output );
			echo '</div>';
		}
	}

	/**
	 * Format a sale price for display.
	 *
	 * @param  string $regular_price Regular price.
	 * @param  string $sale_price    Sale price.
	 * @return string
	 */
	public function format_sale_price( $price, $regular_price, $sale_price ) {
		if( empty( $regular_price ) || empty( $sale_price ) ) {
			return $price;
		}

		if(  $regular_price <  $sale_price ) {
			return $price;
		}

		if(  ! is_numeric($regular_price) || ! is_numeric($sale_price) ) {
			return $price;
		}

		$sale_percentage      = round( ( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 ) );
		return $this->price_save( $price, $sale_percentage );

	}

	/**
	 * Format Variable Price
	 *
	 * @param string $regular_price
	 * @param string $sale_price
	 * @return void
	 */
	public function format_variable_price($price, $product) {
		$available_variations = $product->get_available_variations();
		$sale_percentage = 0;
		foreach ( $available_variations as $variation_product ) {
			$regular_price       = $variation_product['display_regular_price'];
			$sale_price         = $variation_product['display_price'];
			$variation_sale_percentage      = $regular_price && $sale_price ? round( ( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 ) ) : 0;

			if ( $variation_sale_percentage > $sale_percentage ) {
				$sale_percentage = $variation_sale_percentage;
			}
		}
		return $this->price_save( $price, $sale_percentage );
	}

	/**
	 * Price Save
	 *
	 * @param string $regular_price
	 * @param string $sale_price
	 * @return void
	 */
	public  function price_save( $price, $sale_percentage ) {
		if( empty( $sale_percentage ) ) {
			return $price;
		}

		$sale_percentage = apply_filters( 'ecomus_sale_percentage' , $sale_percentage . '%' . ' ' . esc_html('OFF', 'ecomus'), $sale_percentage );

		return  $price . '<span class="sale-off em-font-bold">' . $sale_percentage . '</span>';
	}

	public function open_product_price() {
		echo '<div class="ecomus-product-price">';
	}

	public function close_product_price() {
		echo '</div>';
	}

	/**
	 * Add Format Sale price
	 **/
	public function add_format_sale_price() {
		add_filter( 'woocommerce_format_sale_price', array( $this, 'format_sale_price' ), 10, 3 );
		add_filter( 'woocommerce_variable_price_html', array( $this, 'format_variable_price' ), 10, 2 );
	}

	/**
	 * Remove Format Sale price
	 **/
	public function remove_format_sale_price() {
		$rm_filter = 'remove_filter';
		$rm_filter( 'woocommerce_format_sale_price', array( $this, 'format_sale_price' ), 10, 3 );
		$rm_filter( 'woocommerce_variable_price_html', array( $this, 'format_variable_price' ), 10, 2 );
	}

	/**
	 * Show stock
     *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function stock() {
		global $product;
		if( $product->is_type( 'grouped' ) ) {
			return;
		}

		echo '<div class="ecomus-product-availability">' . wc_get_stock_html( $product ) .'</div>';
	}

	/**
	 * Change Text In Stock
	 *
	 * @return array
	 */
	public static function change_text_stock( $availability, $product ) {
		if ( $product->is_in_stock() ) {
			if( empty( $availability['availability'] ) && ! $product->managing_stock() && ! $product->is_on_backorder( 1 ) ) {
				$availability['availability'] = __('Available in stock', 'ecomus');
			}
		}

		return $availability;
	}

	/**
	 * Change add to cart text
	 *
	 * @return void
	 */
	public function product_single_add_to_cart_text( $text ) {
		global $product;
		
		if( $product->is_on_backorder() ) {
			$text = esc_html__( 'Pre-order', 'ecomus' );
		}

		if( ! $product->is_in_stock() ) {
			$text = esc_html__( 'Sold Out', 'ecomus' );
		}

		return $text;
	}
}
