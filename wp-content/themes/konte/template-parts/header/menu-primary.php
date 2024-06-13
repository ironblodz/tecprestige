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

	<form method="get" action="/" class="input-search-wrap">
		<input name="s" type="text" class="input-search" placeholder="Procurar...">
		<input type="text" value="product" name="post_type" hidden>
	</form>

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

		.legal-menu p,
		.legal-menu a {
			font-size: 12px;
			font-weight: 600;
			margin: 0px 0px 2px 0px;
		}

		.action-menu-btn {
			padding: 4px 8px;
			background-color: #0077b5;
			border-radius: 15px;
			color: white !important;
			text-decoration: none;
			color: white !important;
			margin: 20px 6px;
			font-size: 1rem;
			display: flex;
			align-items: center;
		}

		.action-menu-btn img {
			height: 20px;
			margin-right: 4px;
		}

		@media screen and (max-width: 1200px) {
			.action-menu-wrap {
				display: none !important;
			}

			.site-branding .logo img {
				left: 60px;
				max-width: 180px;
			}
		}


		.action-menu-wrap {
			width: 100%;
			display: flex;
			justify-content: center;
		}

		.site-branding {
			width: 100%;
		}


		/** REMAKE MENU */

		.header-left-items.header-items.has-menu{
			display: block !important;
		}

		.site-branding .logo img {
			position: unset !important;
		}

		.header-main, .header-v10 .site-branding, .header-v10 .header-main .header-right-items {
    		height: auto !important;
		}

		.site-branding .logo-light {
			display: none !important;
		}

		.header-v10 .header-main .logo {
		display: flex;
    	justify-content: center;
		}
	</style>

</nav>