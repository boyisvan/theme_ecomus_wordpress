<?php

/**
 * Template part for displaying the cart icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

$counter = ! empty(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$counter_class = $counter == 0 ? 'empty-counter' : '';
?>

<a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" class="em-button em-button-text em-button-icon header-cart__icon" data-toggle="off-canvas" data-target="cart-panel">
	<?php echo Ecomus\Icon::inline_svg( 'icon=shopping-bag' ); ?>
	<span class="header-counter header-cart__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $counter ) ?></span>
	<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'ecomus' ) ?></span>
</a>
