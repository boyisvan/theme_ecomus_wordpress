<?php
/**
 * Template part for displaying the account icon
 *
 * @package Ecomus
 */
$toggle = is_user_logged_in() ? 'off-canvas' : 'modal';
$target = is_user_logged_in() ? 'account-panel' : 'login-modal';
?>

<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="em-button em-button-text em-button-icon header-account__icon" data-toggle="<?php echo esc_attr($toggle); ?>" data-target="<?php echo esc_attr($target); ?>">
	<?php echo \Ecomus\Icon::get_svg( 'account' ); ?>
	<span class="screen-reader-text"><?php esc_html_e( 'Account', 'ecomus' ) ?></span>
</a>
