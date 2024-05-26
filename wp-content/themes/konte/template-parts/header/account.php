<?php
/**
 * Template part for displaying the sign-in
 *
 * @package Konte
 */

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>
<div class="header-account header-account--<?php echo esc_attr( konte_get_option( 'header_account_display' ) ); ?>">
	<?php if ( is_user_logged_in() ) : ?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>">
			<?php
			if ( 'icon' == konte_get_option( 'header_account_display' ) ) {
				konte_svg_icon( 'icon=account' );
				printf( '<span class="screen-reader-text">%s</span>', esc_html__( 'My Account', 'konte' ) );
			} else {
				esc_html_e( 'My Account', 'konte' );
			}
			?>
		</a>

		<div class="account-links">
			<ul>
				<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
					<li class="account-link--<?php echo esc_attr( $endpoint ); ?>">
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="underline-hover"><?php echo esc_html( $label ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else : ?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" data-toggle="<?php echo 'panel' == konte_get_option( 'header_account_behaviour' ) && ! is_account_page() ? 'off-canvas' : 'link'; ?>" data-target="login-panel">
			<?php
			if ( 'icon' == konte_get_option( 'header_account_display' ) ) {
				konte_svg_icon( 'icon=account' );
				printf( '<span class="screen-reader-text">%s</span>', esc_html__( 'Sign in', 'konte' ) );
			} else {
				esc_html_e( 'Sign in', 'konte' );
			}
			?>
		</a>
	<?php endif; ?>
</div>
