<?php

/**
 * Template part for display primary menu
 *
 * @package Konte
 */
?>
<nav id="primary-menu" class="main-navigation primary-navigation">
	<?php
	wp_nav_menu(array(
		'theme_location' => 'primary',
		'container'      => null,
		'menu_class'     => 'menu nav-menu',
	));
	?>

	<div class="social-menu-legal">
		<img src="/wp-content/themes/konte/images/facebook.svg" alt="facebook">
		<img src="/wp-content/themes/konte/images/instagram.svg" alt="instagram">
		<img src="/wp-content/themes/konte/images/youtube.svg" alt="youtube">

	</div>


	<?php
	$menu_legal_items = wp_get_nav_menu_items('menu-legal');

	if ($menu_legal_items) {
		echo '<div class="legal-menu">';
		foreach ($menu_legal_items as $item) {
			echo '<p> <a class="" href="' . $item->url . '">' . $item->title . '</a></p>';
		}
		echo '</div>';
	}
	?>

	<div class="legal-bottom-fixed">
		<div class="centro2020">
			<img src="/wp-content/themes/konte/images/centro2020.png" alt="Centro2020">
		</div>

		<p class="legal-copyright">2024 Tecprestige Lda. Todos os direitos reservados.</p>
	</div>

	<style>
		.legal-bottom-fixed {
			position: absolute;
			bottom: 0;
		}

		.centro2020 {
			width: 100%;
			background-color: white;
		}

		.centro2020 img {
			padding: 10px 20px;
			display: flex;
			justify-content: center;
		}

		.legal-copyright {
			margin-top: 10px;
			font-weight: 600;
			font-size: 11px;
			text-align: center;
		}

		.social-menu-legal {
			margin-top: 15px;
			display: flex;
			justify-content: center;
		}

		.social-menu-legal img {
			margin: 4px;
			height: 36px;
		}

		.legal-menu {
			padding-top: 15px;
			text-align: center;
		}

		.legal-menu p, .legal-menu a {
			font-size: 12px;
			font-weight: 600;
			margin: 0px 0px 2px 0px;
		}
	</style>

</nav>