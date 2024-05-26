<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _x( 'Search for:', 'label', 'konte' ) ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'konte' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'konte' ); ?>">
		<?php konte_svg_icon( 'icon=search' ) ?>
		<span class="button-text screen-reader-text"><?php esc_html_e( 'Search', 'konte' ); ?></span>
	</button>
</form>