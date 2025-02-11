<?php
/**
 * WooCommerce additional settings.
 *
 * @package Ecomus
 */

namespace Ecomus\WooCommerce;

use \Ecomus\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class of Product Settings
 */
class Settings {
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
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'badges_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_badges_options' ) );

		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_attributes_tab' ] );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_attributes_options' ) );

		add_action( 'save_post', array( $this, 'save_product_data' ) );

		// Atribute
		add_action( 'wp_ajax_ecomus_wc_product_attributes', array( $this, 'wc_get_product_attributes' ) );
		add_action( 'wp_ajax_nopriv_ecomus_wc_product_attributes', array( $this, 'wc_get_product_attributes' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) && $screen->post_type == 'product' ) {
			wp_enqueue_script( 'ecomus_wc_settings_js', get_template_directory_uri() . '/assets/js/backend/woocommerce.js', array( 'jquery' ), '20220318', true );
			wp_localize_script(
				'ecomus_wc_settings_js',
				'ecomus_wc_settings',
				array(
					'search_tag_nonce'   => wp_create_nonce( 'search-tags' ),
				)
			);
		}
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function badges_tab( $tabs ) {
		$tabs['product_badges'] = [
			'label'    => esc_html__( 'Badges', 'ecomus' ),
			'target'   => 'product_badges_data',
			'class'    => [ 'product-badges-tab' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_badges_options() {
		?>
		<div id="product_badges_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group">
			<?php
				woocommerce_wp_checkbox( array(
					'id'          => '_is_new',
					'label'       => esc_html__( 'New product?', 'ecomus' ),
					'description' => esc_html__( 'Enable to set this product as a new product. A "New" badge will be added to this product.', 'ecomus' ),
				) );
			?>
			</div>
			<div class="options_group">
				<?php
					$post_custom = get_post_custom( get_the_ID());
					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_text',
							'label'    => esc_html__( 'Custom Badge Text', 'ecomus' ),
							'desc_tip'    => true,
							'description' => esc_html__( 'Enter this optional to show your badges.', 'ecomus' ),
						)
					);

					$bg_color = ( isset( $post_custom['custom_badges_bg'][0] ) ) ? $post_custom['custom_badges_bg'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_bg',
							'label'    => esc_html__( 'Custom Badge Background', 'ecomus' ),
							'description' => esc_html__( 'Pick background color for your badge', 'ecomus' ),
							'value'    => $bg_color,
						)
					);

					$color = ( isset( $post_custom['custom_badges_color'][0] ) ) ? $post_custom['custom_badges_color'][0] : '';
					woocommerce_wp_text_input(
						array(
							'id'       => 'custom_badges_color',
							'label'    => esc_html__( 'Custom Badge Color', 'ecomus' ),
							'description' => esc_html__( 'Pick color for your badge', 'ecomus' ),
							'value'    => $color,
						)
					);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add new product data tab for swatches
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_attributes_tab( $tabs ) {
		$tabs['product_attributes'] = [
			'label'    => esc_html__( 'Product Card Attributes', 'ecomus' ),
			'target'   => 'product_attributes_data',
			'class'    => [ 'product_attributes_tab', 'show_if_variable' ],
			'priority' => 61,
		];

		return $tabs;
	}

	/**
	 * Add more options to advanced tab.
	 */
	public static function product_attributes_options() {
		?>
		<div id="product_attributes_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group product-attributes-compare" id="ecomus-product-attributes">
			<?php
			self::get_product_attributes(get_the_ID());
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save product data.
	 *
	 * @param int $post_id The post ID.
	 */
	public static function save_product_data( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_is_new'] ) ) {
			delete_post_meta( $post_id, '_is_new' );
		} else {
			update_post_meta( $post_id, '_is_new', 'yes' );
		}

		if ( isset( $_POST['custom_badges_text'] ) ) {
			$woo_data = $_POST['custom_badges_text'];
			update_post_meta( $post_id, 'custom_badges_text', $woo_data );
		}

		if ( isset( $_POST['custom_badges_bg'] ) ) {
			$woo_data = $_POST['custom_badges_bg'];
			update_post_meta( $post_id, 'custom_badges_bg', $woo_data );
		}

		if ( isset( $_POST['custom_badges_color'] ) ) {
			$woo_data = $_POST['custom_badges_color'];
			update_post_meta( $post_id, 'custom_badges_color', $woo_data );
		}

		if ( isset( $_POST['ecomus_product_attribute'] ) ) {
			$woo_data = $_POST['ecomus_product_attribute'];
			update_post_meta( $post_id, 'ecomus_product_attribute', $woo_data );
		}

		if ( isset( $_POST['ecomus_product_attribute_number'] ) ) {
			$woo_data = intval($_POST['ecomus_product_attribute_number']);
			$woo_data = ! $woo_data ? '' : $woo_data;
			update_post_meta( $post_id, 'ecomus_product_attribute_number', $woo_data );
		}

		if ( isset( $_POST['ecomus_product_attribute_second'] ) ) {
			$woo_data = $_POST['ecomus_product_attribute_second'];
			update_post_meta( $post_id, 'ecomus_product_attribute_second', $woo_data );
		}

		if ( isset( $_POST['ecomus_product_attribute_number_second'] ) ) {
			$woo_data = intval($_POST['ecomus_product_attribute_number_second']);
			$woo_data = ! $woo_data ? '' : $woo_data;
			update_post_meta( $post_id, 'ecomus_product_attribute_number_second', $woo_data );
		}
	}

	/**
	 * Get Product Attributes AJAX function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wc_get_product_attributes() {
		$post_id = $_POST['post_id'];

		if ( empty( $post_id ) ) {
			return;
		}
		ob_start();
		$this->get_product_attributes($post_id);
		$response = ob_get_clean();
		wp_send_json_success( $response );
		die();
	}

	/**
	 * Get Product Attributes function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function get_product_attributes ($post_id) {
		$product_object = wc_get_product( $post_id );
		if( ! $product_object ) {
			return;
		}
		$attributes = $product_object->get_attributes();

		if( ! $attributes ) {
			return;
		}
		$options         = array();
		$options['']     = esc_html__( 'Default', 'ecomus' );
		$options['none'] = esc_html__( 'None', 'ecomus' );
		foreach ( $attributes as $attribute ) {
			$options[ sanitize_title( $attribute['name'] ) ] = wc_attribute_label( $attribute['name'] );
		}
		woocommerce_wp_select(
			array(
				'id'       => 'ecomus_product_attribute',
				'label'    => esc_html__( 'Primary Product Attribute', 'ecomus' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show the product attribute in the product card', 'ecomus' ),
				'options'  => $options
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'       => 'ecomus_product_attribute_number',
				'label'    => esc_html__( 'Primary Product Attribute Number', 'ecomus' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show number of the product attribute in the product card', 'ecomus' ),
				'options'  => $options
			)
		);

		woocommerce_wp_select(
			array(
				'id'       => 'ecomus_product_attribute_second',
				'label'    => esc_html__( 'Second Product Attribute', 'ecomus' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show the product attribute in the product card', 'ecomus' ),
				'options'  => $options
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'       => 'ecomus_product_attribute_number_second',
				'label'    => esc_html__( 'Second Product Attribute Number', 'ecomus' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Show number of the product attribute in the product card', 'ecomus' ),
				'options'  => $options
			)
		);
	}
}
