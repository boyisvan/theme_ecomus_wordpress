<?php
/**
 * Header Main functions and definitions.
 *
 * @package Ecomus
 */

 namespace Ecomus\Header;

use Ecomus\Helper;

use function WPML\FP\Strings\replace;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header Main initial
 *
 */
class Main {
	/**
	 * Instance
	 *
	 * @var $instance
	 */
	protected static $instance = null;

	protected static $header_layout = null;

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

	}

	/**
	 * Get the header.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		$layout = self::get_layout();

		if ( 'custom' != $layout ) {
			$this->prebuild( $layout );
		} else {
			$options = array();

			// Header main.
			$sections = array(
				'left'   => Helper::get_option( 'header_main_left' ),
				'center' => Helper::get_option( 'header_main_center' ),
				'right'  => Helper::get_option( 'header_main_right' ),
			);

			$classes = $this->header_classes( 'main', array( 'header-main', 'header-contents' ) );

			if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_bottom' ) {
				$classes .= ' header-sticky';
			}

			$this->contents( $sections, $options, array( 'class' => $classes ) );

			// Header bottom.
			$sections = array(
				'left'   => Helper::get_option( 'header_bottom_left' ),
				'center' => Helper::get_option( 'header_bottom_center' ),
				'right'  => Helper::get_option( 'header_bottom_right' ),
			);

			$classes = $this->header_classes( 'bottom', array( 'header-bottom', 'header-contents' ) );

			if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_main' ) {
				$classes .= ' header-sticky';
			}

			$this->contents( $sections, $options, array( 'class' => $classes ) );
		}
	}

	/**
	 * Get the header layout.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_layout() {
		if( isset( self::$header_layout )  ) {
			return self::$header_layout;
		}

		$present = Helper::get_option( 'header_present' );
		if ( $present ) {
			self::$header_layout = 'prebuild' == $present ? Helper::get_option( 'header_version' ) : 'custom';
		} else {
			self::$header_layout = 'v1';
		}

		self::$header_layout = apply_filters( 'ecomus_get_header_layout', self::$header_layout );

		return self::$header_layout;
	}

	/**
	 * Display pre-build header
	 *
	 * @since 1.0.0
	 *
	 * @param string $version
	 */
	public function prebuild( $version = 'v1' ) {
		$sections 		= $this->get_prebuild( $version );

		$classes = $this->header_classes( 'main', array( 'header-main', 'header-contents' ) );

		if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_bottom' ) {
			$classes .= ' header-sticky';
		}

		$this->contents( $sections['main'], $sections['main_options'], array( 'class' => $classes ) );

		$classes = $this->header_classes( 'bottom', array( 'header-bottom', 'header-contents' ) );

		if( Helper::get_option( 'header_sticky' ) && Helper::get_option( 'header_sticky_el' ) !== 'header_main' ) {
			$classes .= ' header-sticky';
		}

		$this->contents( $sections['bottom'], $sections['bottom_options'], array( 'class' => $classes ) );
	}

	/**
	 * Display pre-build header
	 *
	 * @since 1.0.0
	 *
	 * @param string $version
	 */
	public function get_prebuild( $version = 'v1' ) {
		switch ( $version ) {
			case 'v1':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
					),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array();
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v2':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'secondary-menu' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array();
				$bottom_sections = array(
					'left'   => array(),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => array()
				);
				$bottom_options = array();
				break;
			case 'v3':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'currency' ),
						array( 'item' => 'language' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array();
				$bottom_sections = array(
					'left'   => array(),
					'center' =>  array(
						array( 'item' => 'primary-menu' ),
					),
					'right'  => array()
				);
				$bottom_options = array();
				break;
			case 'v4':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'primary-menu' ),
					),
					'center' =>  array(
						array( 'item' => 'logo' ),
					),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array();
				$bottom_sections = array();
				$bottom_options = array();
				break;
			case 'v5':
				$main_sections   = array(
					'left'   => array(
						array( 'item' => 'logo' ),
						array( 'item' => 'primary-menu' ),
					),
					'center' =>  array(),
					'right'  => $this->get_header_items(array('search', 'account', 'compare', 'wishlist', 'cart'))
				);
				$main_options = array();
				$bottom_sections = array();
				$bottom_options = array();
				break;
			default:
				$main_sections   = array();
				$main_options = array();
				$bottom_sections = array();
				$bottom_options = array();
				break;
		}

		return apply_filters( 'ecomus_prebuild_header', array( 'main' => $main_sections, 'main_options' => $main_options, 'bottom' => $bottom_sections, 'bottom_options' => $bottom_options ), $version );
	}

	/**
	 * Display header attributes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function get_header_items( $atts = array('search') ) {
		$items = array();
		foreach( $atts as $item ) {
			if( 'logo' === $item ) {
				$items[] =	array( 'item' => 'logo' );
			}
			$key = str_replace( '-', '_', $item );
			if( Helper::get_option('header_prebuild_' . $key) ) {
				$items[] =	array( 'item' => $item );
			}
		}

		return $items;
	}

	/**
	 * Display header items
	 *
	 * @since 1.0.0
	 *
	 * @param string $sections, $atts
	 */
	public function contents( $sections, $options, $atts = array() ) {
		if ( false == array_filter( $sections ) ) {
			return;
		}

		$classes = array();
		if ( isset( $atts['class'] ) ) {
			$classes = (array) $atts['class'];
			unset( $atts['class'] );
		}

		if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
			unset( $sections['left'] );
			unset( $sections['right'] );
		}

		if ( ! empty( $sections['center'] ) ) {
			$classes[]    = 'has-center';

			if ( empty( $sections['left'] ) && empty( $sections['right'] ) ) {
				$classes[] = 'no-sides';
			}
		} else {
			$classes[] = 'no-center';
			unset( $sections['center'] );

			if ( empty( $sections['left'] ) ) {
				unset( $sections['left'] );
			}

			if ( empty( $sections['right'] ) ) {
				unset( $sections['right'] );
			}
		}

		$attr = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		foreach ( $atts as $name => $value ) {
			$attr .= ' ' . $name . '="' . esc_attr( $value ) . '"';
		}
		?>
		<div <?php echo ! empty( $attr ) ? $attr : ''; ?>>
			<div class="site-header__container <?php echo esc_attr( apply_filters( 'ecomus_header_container_classes', 'em-container' ) ) ?>">
				<?php foreach ( $sections as $section => $items ) : ?>
					<?php
					$class      = [];
					$item_names = wp_list_pluck( $items, 'item' );

					if ( in_array( 'primary-menu', $item_names ) ) {
						$class[] = 'has-menu';
					}
					?>

					<div class="header-<?php echo esc_attr( $section ); ?>-items header-items <?php echo esc_attr( implode( ' ', $class ) ); ?>">
						<?php $this->items( $items, $options ); ?>
					</div>

				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Display header items
	 *
	 * @since 1.0.0
	 *
	 * @param array $items
	 * @param array $options
	 */
	public function items( $items, $options ) {
		if ( empty( $items ) ) {
			return;
		}

		foreach ( $items as $item ) {
			$item['item'] = $item['item'] ? $item['item'] : key( \Ecomus\Options::header_items_option() );
			$template_file = $item['item'];
			$args = array();
			$load_file = true;

			switch ( $item['item'] ) {
				case 'logo':
					$args = $this->logo_options( $options );
					break;
				case 'primary-menu':
					$args = $this->primary_menu_options( $options );
					break;
				case 'search':
					\Ecomus\Theme::set_prop( 'modals', 'search' );
					break;
				case 'cart':
					\Ecomus\Theme::set_prop( 'panels', 'cart' );
					break;
				case 'account':
					if( function_exists('is_account_page') && is_account_page() ) {
						break;
					}
					if( ! is_user_logged_in() ) {
						\Ecomus\Theme::set_prop( 'modals', 'login' );
					} else {
						\Ecomus\Theme::set_prop( 'panels', 'account' );
					}
					break;
			}

			if ( $template_file && ! empty( $load_file )) {
				get_template_part( 'template-parts/header/' . $template_file, '', $args );
			}
		}
	}

	/**
	 * Logo options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */

	public function logo_options( $options ) {
		$args = array();
		$args['title'] = ! empty( $options ) && isset( $options['logo_title'] ) ? $options['logo_title'] : true;
		$options = isset( $options['logo'] ) ? $options['logo'] : '';
		$args['type'] = ! empty( $options ) && isset( $options['type'] ) ? $options['type'] : Helper::get_option( 'logo_type' );
		$args['type'] = apply_filters( 'ecomus_header_logo_type', $args['type'] );
		$args['logo_light'] = ! empty( $options ) && isset( $options['logo_light'] ) ? $options['logo_light'] : '';
		return $args;
	}

	/**
	 * Primary Menu options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options
	 * @return array $args
	 */
	public function primary_menu_options( $options ) {
		$options = isset( $options['primary_menu'] ) ? $options['primary_menu'] : '';
		$args = array();

		$args['menu_class'] = ! empty( $options ) && isset( $options['menu_class'] ) ? $options['menu_class'] : true;

		$args['container_class'] = ' primary-navigation';

		return $args;
	}

	/**
	 * Return classe
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes
	 * @return array $args
	 */

	public function header_classes( $section, $classes = array() ) {
		return implode( ' ', $classes );
	}

	/**
	 * Display the site branding title
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @return void
	 */
	public static function site_branding_title( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-title';

		// HTML tag for this title.
		$tag = is_front_page() || is_home() ? 'h1' : 'p';
		$tag = apply_filters( 'ecomus_site_branding_title_tag', $tag, $args );

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$title = sprintf(
			'<%1$s class="%2$s"><a href="%3$s" rel="home">%4$s</a></%1$s>',
			$tag,
			esc_attr( trim( $class ) ),
			esc_url( home_url( '/' ) ),
			get_bloginfo( 'name' )
		);

		if ( ! $args['echo'] ) {
			return $title;
		}

		echo apply_filters( 'ecomus_site_branding_title_html', $title );
	}

	/**
	 * Display the site branding description
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @return void
	 */
	public static function site_branding_description( $args = array() ) {
		$text = get_bloginfo( 'description', 'display' );

		if ( empty( $text ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo'  => true,
		) );

		// Ensure included a space at beginning.
		$class = ' site-description';

		if ( is_array( $args['class'] ) ) {
			$class = implode( ' ', $args['class'] ) . $class;
		} elseif ( is_string( $args['class'] ) ) {
			$class = $args['class'] . $class;
		}

		$description = sprintf(
			'<p class="%s">%s</p>',
			esc_attr( trim( $class ) ),
			wp_kses_post( $text )
		);

		if ( ! $args['echo'] ) {
			return $description;
		}

		echo apply_filters( 'site_branding_description_html', $description );
	}
}