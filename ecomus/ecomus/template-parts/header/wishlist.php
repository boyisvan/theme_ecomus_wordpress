<?php

/**
 * Template part for displaying the wishlist icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\Wishlist\Helper' ) ) {
	return;
}

$wishlist = \WCBoost\Wishlist\Helper::get_wishlist();

$wishlist_counter = intval( $wishlist->count_items() );
$counter_class = $wishlist_counter == 0 ? 'empty-counter' : '';
?>
<a href="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" class="em-button em-button-text em-button-icon header-wishlist__icon" role="button">
	<?php echo Ecomus\Icon::inline_svg( 'icon=heart' ); ?>
	<span class="header-counter header-wishlist__counter <?php echo esc_attr( $counter_class );?>"><?php echo esc_html( $wishlist_counter ); ?></span>
	<span class="screen-reader-text"><?php esc_html_e( 'Wishlist', 'ecomus' ) ?></span>
</a>