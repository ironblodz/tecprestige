<?php
/**
 * Template file for displaying login icon on mobile header.
 *
 * @package Konte
 */

?>
<div class="header-account header-mobile-account">
	<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
		<?php echo is_user_logged_in() ? '' : ' data-toggle="off-canvas" data-target="login-panel"'; ?>>
		<?php konte_svg_icon( 'icon=account' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'My Account', 'konte' )?></span>
	</a>
</div>