<div class="megamenu-modal-grid__row is-empty" data-id="{{ data.id }}">
	<div class="megamenu-modal-grid__row-actions">
		<span data-action="sort">
			<span class="dashicons dashicons-sort"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Sort this row', 'konte-addons' ) ?></span>
		</span>
		<button class="button-link" data-action="add-column" data-options="<?php echo esc_attr( json_encode( Konte_Addons_Mega_Menu::default_column_options() ) )?>">
			<span class="dashicons dashicons-plus-alt2"></span>
			<span><?php esc_html_e( 'Add Column', 'konte-addons' ) ?></span>
		</button>
		<button type="button" class="button-link" data-action="toggle-options">
			<span class="dashicons dashicons-ellipsis"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle options', 'konte-addons' ) ?></span>
		</button>
		<ul class="megamenu-modal-grid__row-options">
			<li><button type="button" class="button-link" data-action="options"><?php esc_html_e( 'Options', 'konte-addons' ) ?></button></li>
			<li><button type="button" class="button-link" data-action="delete"><?php esc_html_e( 'Delete', 'konte-addons' ) ?></button></li>
		</ul>
	</div>

	<div class="megamenu-modal-grid__row-inside">

	</div>
</div>
