<?php
/**
 * Posts functions and definitions.
 *
 * @package Ecomus
 */

namespace Ecomus\Blog;

use Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts initial
 *
 */
class Archive {
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
		$this->load_sections();
	}

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_sections() {
		// Blog content layout
		add_filter('ecomus_site_layout', array( $this, 'layout' ));


		add_filter( 'post_class', array( $this, 'post_classes' ), 10, 3 );

		// Sidebar
		add_filter('ecomus_get_sidebar', array( $this, 'sidebar' ), 10 );

		// Body Class
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Blog Page
		add_filter( 'ecomus_content_template_part', array( $this, 'blog_page_template' ) );

		// Navigation
		add_action( 'ecomus_after_archive_content', array( $this, 'navigation' ), 30 );
		add_action( 'ecomus_after_search_loop', array( $this, 'navigation' ), 30 );

	}

	/**
	 * Layout
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function layout( $layout ) {
		if( 'grid' == Helper::get_option( 'blog_layout' ) || ! is_active_sidebar( 'blog-sidebar' ) ){
			return $layout;
		}

		$layout = Helper::get_option( 'blog_sidebar' );

		return $layout;
	}


	/**
	 * Add a class of blog layout to posts
	 *
	 * @param array $classes
	 * @param array $class
	 * @param int   $post_id
	 *
	 * @return mixed
	 */
	public function post_classes( $classes, $class, $post_id ) {
		if ('post' != get_post_type( $post_id ) || ! is_main_query() ) {
			return $classes;
		}

		if ( Helper::get_option( 'blog_layout' ) !== 'list' ) {
			$classes[] = 'em-post-grid';
		} else {
			$classes[] = 'em-post-list';
		}

		if( Helper::get_option( 'blog_layout' ) == 'grid' ) {
			$classes[] = 'em-col em-xs-12 em-sm-6 em-md-6 em-lg-4';
		}

		if( Helper::get_option( 'blog_layout' ) == 'list' ) {
			$classes[] = 'em-col';
		}

		if( Helper::get_option( 'blog_layout' ) == 'classic' ) {
			$classes[] = 'em-col em-xs-12 em-sm-6';
		}

		return $classes;
	}


	/**
	 * Get Sidebar
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function sidebar() {
		if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
			return false;
		}

		if ( Helper::get_option( 'blog_layout' ) == 'grid' ) {
			return false;
		}

		return true;
	}

	/**
	 * Classes Body
	 */
	public function body_classes( $classes ) {
		$classes[] = 'ecomus-blog-page';
		$classes[] = 'blog-' . Helper::get_option( 'blog_layout' );

		if ( Helper::get_option( 'blog_layout' ) == 'grid' || ! is_active_sidebar( 'blog-sidebar' ) ) {
			$classes[] = 'no-sidebar';
		} else {
			$classes[] = 'em-blog-sidebar';
		}

		return $classes;
	}

	/**
	 * Blog page template
	 *
	 * @return void
	 */
	public function blog_page_template() {
		if( Helper::get_option( 'blog_layout' ) == 'list' ) {
			return 'list';
		}

		return;
	}

	/**
	 * Navigation
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function navigation() {
		$args = array(
			'end_size'  => 3,
			'prev_text' => \Ecomus\Icon::get_svg( 'left-mini', 'ui', 'class=ecomus-pagination__arrow' ),
			'next_text' => \Ecomus\Icon::get_svg( 'right-mini', 'ui', 'class=ecomus-pagination__arrow' ),
			'class' => 'em-button-outline'
		);

		the_posts_pagination( $args );
	}
}