<?php
/**
 * Template part for display primary menu
 *
 * @package Konte
 */
?>
<nav id="primary-menu" class="main-navigation primary-navigation">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'container'      => null,
		'menu_class'     => 'menu nav-menu',
	) );
	?>
</nav>
