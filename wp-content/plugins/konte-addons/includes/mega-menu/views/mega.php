<#
var items = _.filter( data.children, function( item ) {
	return item.subDepth == 0;
} );
#>

<div class="megamenu-modal__panel" data-panel="mega">
	<div class="megamenu-modal__panel-toolbar">
		<div class="megamenu-modal__panel-option">
			<label class="megamenu-modal__toggle">
				<input type="checkbox" value="1" {{ parseInt( data.megaData.mega ) ? 'checked' : '' }} name="{{ megaMenuFieldName( 'mega', data.data['menu-item-db-id'] ) }}" data-field="mega">
				<span class="megamenu-modal__toggle-ui"></span>
				<?php esc_html_e( 'Mega menu', 'konte-addons' ) ?>
			</label>
		</div>

		<div class="megamenu-modal__panel-option">
			<label>
				<span class="megamenu-modal__panel-option-label"><?php esc_html_e( 'Mode', 'konte-addons' ) ?></span>
				<select name="{{ megaMenuFieldName( 'mega_mode', data.data['menu-item-db-id'] ) }}" data-toggle_condition="mega_mode">
					<option value="default"><?php esc_html_e( 'Default', 'konte-addons' ) ?></option>
					<option value="grid" {{ 'grid' == data.megaData.mega_mode ? 'selected="selected"' : '' }}><?php esc_html_e( 'Grid', 'konte-addons' ) ?></option>
					<option value="tabs" {{ 'tabs' == data.megaData.mega_mode ? 'selected="selected"' : '' }}><?php esc_html_e( 'Tabs', 'konte-addons' ) ?></option>
				</select>
			</label>
		</div>

		<div class="megamenu-modal__panel-option">
			<label>
				<span class="megamenu-modal__panel-option-label"><?php esc_html_e( 'Width', 'konte-addons' ) ?></span>
				<select name="{{ megaMenuFieldName( 'width', data.data['menu-item-db-id'] ) }}" data-toggle_condition="mega_width">
					<option value="container"><?php esc_html_e( 'Default', 'konte-addons' ) ?></option>
					<option value="container-fluid" {{ 'container-fluid' == data.megaData.width ? 'selected="selected"' : '' }}><?php esc_html_e( 'Full width', 'konte-addons' ) ?></option>
					<option value="custom" {{ 'custom' == data.megaData.width ? 'selected="selected"' : '' }}><?php esc_html_e( 'Custom', 'konte-addons' ) ?></option>
				</select>
			</label>

			<label style="{{ 'custom' == data.megaData.width ? '' : 'display: none;' }}" data-toggle_mega_width="custom">
				<span class="screen-reader-text"><?php esc_html_e( 'Custom width', 'konte-addons' ) ?></span>
				<input type="text" name="{{ megaMenuFieldName( 'custom_width', data.data['menu-item-db-id'] ) }}" placeholder="<?php esc_attr_e( 'width', 'konte-addons' ) ?>" value="{{ data.megaData.custom_width }}" size="6">
			</label>
		</div>
	</div>

	<div class="megamenu-modal__submenu megamenu-modal__submenu--default {{ 'default' !== data.megaData.mega_mode ? 'hidden' : '' }}" data-toggle_mega_mode="default">
		<# _.each( items, function( item, index ) { #>

		<div class="megamenu-modal__submenu-column" data-width="{{ item.megaData.width }}">
			<ul>
				<li class="menu-item menu-item-depth-{{ item.subDepth }}" data-item_id="{{ item.data['menu-item-db-id'] }}">
					<span aria-label="{{ item.data['menu-item-title'] }}">{{{ item.data['menu-item-title'] }}}</span>
					<# if ( item.subDepth == 0 ) { #>
					<div class="megamenu-modal__submenu-column-actions">
						<button type="button" class="button-link megamenu-modal__submenu-settings"><?php esc_html_e( 'Settings', 'konte-addons' ) ?></button>
						<button class="megamenu-modal__column-width-handle" data-action="decrease"><i class="dashicons dashicons-arrow-left-alt2"></i></button>
						<span class="megamenu-modal__column-width-label"></span>
						<button class="megamenu-modal__column-width-handle" data-action="increase"><i class="dashicons dashicons-arrow-right-alt2"></i></button>
						<input type="hidden" name="{{ megaMenuFieldName( 'width', item.data['menu-item-db-id'] ) }}" value="{{ item.megaData.width }}" class="menu-item-width">
					</div>
					<# } #>
				</li>
			</ul>
		</div>

		<# } ) #>
	</div>

	<div class="megamenu-modal__submenu megamenu-modal__submenu--grid {{ 'grid' !== data.megaData.mega_mode ? 'hidden' : '' }}" data-toggle_mega_mode="grid">
		<div class="megamenu-modal-grid__inside"></div>

		<div class="megamenu-modal-grid__actions">
			<button type="button" class="button" data-action="add-row" data-options="<?php echo esc_attr( json_encode( Konte_Addons_Mega_Menu::default_row_options() ) ) ?>">
				<span class="dashicons dashicons-insert"></span>
				<span><?php esc_html_e( 'Add a row', 'konte-addons' ) ?></span>
			</button>
		</div>
	</div>

	<div class="megamenu-modal__submenu megamenu-modal__submenu--tabs {{ 'tabs' !== data.megaData.mega_mode ? 'hidden' : '' }}" data-toggle_mega_mode="tabs">
		<# if ( _.size( items ) <= 0 ) { #>
			<p><?php esc_html_e( 'There is no tab. Please add sub-items to create tabs.', 'konte-addons' ) ?></p>
		<# } else { #>
			<div class="megamenu-modal__submenu-tabs">
				<ul>

					<# _.each( items, function( item, index ) { #>

						<li class="megamenu-modal__submenu-tab {{ 0 === index ? 'active' : '' }}">
							<span role="tab">{{{ item.data['menu-item-title'] }}}</span>
							<# if ( item.subDepth == 0 ) { #>
							<div class="megamenu-modal__submenu-tabpanel" role="tabpanel">
								<div>
									<p><button type="button" class="button" data-item_id="{{ item.data['menu-item-db-id'] }}" data-action="edit-tab"><?php esc_html_e( 'Edit Tab', 'konte-addons' ) ?></button></p>
									<p class="description"><?php esc_html_e( 'Menu item', 'konte-addons' ) ?>: {{{ item.data['menu-item-title'] }}}</p>
								</div>
							</div>
							<# } #>
						</li>

					<# } ) #>

				</ul>
			</div>
		<# } #>
	</div>
</div>
