<?php
/**
 * Frontend functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header initial
 *
 */
class Frontend {
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
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_action( 'wp_head', array( $this, 'ecomus_preload_fonts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'ecomus_before_site', array( $this, 'ecomus_include_svg_icons' ) );

		add_action( 'ecomus_after_site_content_open', array( $this, 'open_site_content_container' ) );
		add_action( 'ecomus_before_site_content_close', array( $this, 'close_site_content_container' ), 30 );

		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		$classes[] = $this->content_layout();

		// Add a class of rtl background
		if ( intval( Helper::get_option( 'rtl_smart' ) ) && is_rtl() ) {
			$classes[] = 'ecomus-rtl-smart';
		}

		return $classes;
	}

	/**
	 * Add font
	 */
	public function ecomus_preload_fonts() {
		Helper::get_fonts();
	}

	/**
	 * Get site layout
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function content_layout() {
		$layout = 'no-sidebar';

		return apply_filters( 'ecomus_site_layout', $layout );
	}

	/**
	 * Print the open tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function open_site_content_container() {
		if( Helper::is_built_with_elementor() ) {
			return;
		}

		$classes = apply_filters( 'ecomus_site_content_container_class', 'em-container' );
		echo '<div class="' . esc_attr( $classes ) . ' clearfix ">';
	}

	/**
	 * Print the close tags of site content container
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function close_site_content_container() {
		if( Helper::is_built_with_elementor() ) {
			return;
		}

		echo '</div>';
	}


	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_style( 'swiper', get_template_directory_uri() . '/assets/css/plugins/swiper.min.css', array(), '8.5.4');

		$style_file = is_rtl() ? 'style-rtl.css' : 'style.css';
		wp_enqueue_style( 'ecomus', apply_filters( 'ecomus_get_style_directory_uri', get_template_directory_uri() ) . '/' . $style_file, array(
			'swiper',
		), '20240809' );

		do_action( 'ecomus_after_enqueue_style' );

		/**
		 * Register and enqueue scripts
		 */
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/plugins/html5shiv.min.js', array(), '3.7.2' );
		wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

		wp_enqueue_script( 'respond', get_template_directory_uri() . '/assets/js/plugins/respond.min.js', array(), '1.4.2' );
		wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );

		wp_register_script( 'swiper', get_template_directory_uri() . '/assets/js/plugins/swiper.min.js', array( 'jquery' ), '8.5.4', true );

		wp_register_script( 'headroom', get_template_directory_uri() . '/assets/js/plugins/headroom.min.js', array(), '0.9.3', true );

		if ( ( \Ecomus\Helper::get_option( 'header_sticky' ) || \Ecomus\Helper::get_option( 'header_mobile_sticky' ) ) && 'up' == \Ecomus\Helper::get_option( 'header_sticky_on' ) ) {
			wp_enqueue_script( 'headroom' );
		}

		wp_enqueue_script( 'notify', get_template_directory_uri() . '/assets/js/plugins/notify.min.js', array(), '1.0.0', true );
		wp_enqueue_script( 'ecomus', get_template_directory_uri() . "/assets/js/scripts" . $debug . ".js", array(
			'jquery',
			'swiper',
			'imagesloaded',
		), '20241003', array('strategy' => 'defer') );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		$ecomus_data = array(
			'direction'                                   => is_rtl() ? 'true' : 'false',
			'ajax_url'                                    => class_exists( 'WC_AJAX' ) ? \WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'admin_ajax_url' 							  => admin_url('admin-ajax.php'),
			'nonce'                                       => wp_create_nonce( '_ecomus_nonce' ),
			'header_search_products'                      => Helper::get_option( 'header_search_products' ),
			'header_search_product_limit'                 => Helper::get_option( 'header_search_product_limit' ),
			'header_sticky'                               => Helper::get_option( 'header_sticky' ),
			'header_sticky_on'                            => Helper::get_option( 'header_sticky_on' ),
			'header_mobile_sticky'                        => Helper::get_option( 'header_mobile_sticky' ),
			'header_mobile_menu_open_primary_submenus_on' => Helper::get_option( 'header_mobile_menu_open_primary_submenus_on' ),
			'product_description_lines'   => ! empty( Helper::get_option( 'product_description_lines' ) ) ? intval( Helper::get_option( 'product_description_lines') ) : 4,

		);

		$ecomus_data = apply_filters( 'ecomus_wp_script_data', $ecomus_data );

		wp_localize_script(
			'ecomus', 'ecomusData', $ecomus_data
		);

	}

	/**
	 * Add icon list as svg after <body> tag and hide it
	 */
	public function ecomus_include_svg_icons() {
		echo '<div id="svg-defs" class="svg-defs hidden" aria-hidden="true" tabindex="-1">';
			\Ecomus\Icon::inline_icons();
		echo '</div>';
	}

	function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();
	}

}
