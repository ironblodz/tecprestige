<?php
/**
 * Template part for displaying the search icon
 *
 * @package Konte
 */
?>

<div class="header-search <?php echo esc_attr( konte_get_option( 'header_search_style' ) ) ?>">
	<?php if ( 'icon-modal' == konte_get_option( 'header_search_style' ) ) : ?>
		<span class="svg-icon icon-search search-icon" data-toggle="modal" data-target="search-modal">
			<svg role="img"><use href="#search" xlink:href="#search"></use></svg>
		</span>
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'konte' ) ?></span>
	<?php else: ?>
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
			<label>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'konte' ) ?></span>
				<?php konte_svg_icon( 'icon=search&class=search-icon' ) ?>
				<input type="text" name="s" class="search-field" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search', 'konte' ) ?>" autocomplete="off">
				<?php if ( $type = konte_get_option( 'header_search_type' ) ) : ?>
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ) ?>">
				<?php endif; ?>
			</label>
		</form>

		<?php konte_search_quicklinks( konte_get_option( 'header_search_type' ) ); ?>
	<?php endif; ?>
</div>
