<?php
/**
 * Template part for display socials menu
 *
 * @package Konte
 */
?>
<div class="header-social-icons socials-menu">
	<?php
	if ( has_nav_menu( 'socials' ) ) {
        wp_nav_menu( array(
            'theme_location' => 'socials',
            'container'      => null,
            'menu_id'        => 'header-socials',
            'depth'          => 1,
            'link_before'    => '<span>',
            'link_after'     => '</span>',
        ) );
    }
	?>
</div>
