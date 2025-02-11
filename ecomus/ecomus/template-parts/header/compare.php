<?php
/**
 * Template part for displaying the compare icon
 *
 * @package Ecomus
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WCBoost\ProductsCompare\Plugin' ) ) {
	return;
}

$count = \WCBoost\ProductsCompare\Plugin::instance()->list->count_items();
$class = $count == 0 ? 'empty-counter' : '';
?>

<a class="em-button em-button-text em-button-icon header-compare__icon" role="button" href="<?php echo esc_url( wc_get_page_permalink( 'compare' ) ); ?>">
	<?php echo Ecomus\Icon::inline_svg( 'icon=cross-arrow' ); ?>
	<span class="header-counter header-compare__counter <?php echo esc_attr($class); ?>"><?php echo esc_html($count); ?></span>
	<span class="screen-reader-text"><?php esc_html_e( 'Compare', 'ecomus' ) ?></span>
</a>