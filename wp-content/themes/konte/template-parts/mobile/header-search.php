<?php
/**
 * Template file for displaying search icon on mobile header.
 *
 * @package Konte
 */
?>
<div class="header-search header-mobile-search icon-modal">
	<span class="svg-icon icon-search search-icon" data-toggle="modal" data-target="search-modal">
		<svg role="img"><use href="#search" xlink:href="#search"></use></svg>
	</span>
	<span class="screen-reader-text"><?php esc_html_e( 'Search', 'konte' ) ?></span>
</div>