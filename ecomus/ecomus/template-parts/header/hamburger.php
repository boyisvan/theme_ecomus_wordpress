<?php
/**
 * Template part for displaying the hamburger menu
 *
 * @package Ecomus
 */

?>

<button class="header-hamburger hamburger-menu em-button-text" aria-label="<?php esc_attr_e('Header Hamburger', 'ecomus'); ?>" data-toggle="off-canvas" data-target="mobile-menu-panel">
	<?php echo \Ecomus\Icon::get_svg( 'hamburger', 'ui', 'class=hamburger__icon' ); ?>
</button>