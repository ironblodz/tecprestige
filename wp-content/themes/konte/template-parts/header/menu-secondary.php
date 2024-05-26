<?php
/**
 * Template part for display secondary menu
 *
 * @package Konte
 */
?>
<nav id="secondary-menu" class="main-navigation secondary-navigation">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'secondary',
		'container'      => null,
		'menu_class'     => 'menu nav-menu',
	) );
	?>
</nav>
