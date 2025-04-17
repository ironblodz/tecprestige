<div class="megamenu-modal__menu">
	<# if ( data.depth == 0 ) { #>
		<a href="#" class="media-menu-item {{ data.current === 'mega' ? 'active' : '' }}" data-panel="mega" data-title="<?php esc_attr_e( 'Mega Menu', 'konte-addons' ) ?>"><?php esc_html_e( 'Mega Menu', 'konte-addons' ) ?></a>
		<a href="#" class="media-menu-item {{ data.current === 'design' ? 'active' : '' }}" data-panel="design" data-title="<?php esc_attr_e( 'Mega Menu Design', 'konte-addons' ) ?>"><?php esc_html_e( 'Design', 'konte-addons' ) ?></a>
	<# } else if ( data.depth == 1 ) { #>
		<# if ( data.in_mega && 'tabs' === data.in_mega_mode ) { #>
			<a href="#" class="media-menu-item {{ data.current === 'tab-content' ? 'active' : '' }}" data-panel="tab-content" data-title="<?php esc_attr_e( 'Tab Content', 'konte-addons' ) ?>"><?php esc_html_e( 'Tab Content', 'konte-addons' ) ?></a>
		<# } else { #>
			<a href="#" class="media-menu-item {{ data.current === 'settings' ? 'active' : '' }}" data-panel="settings" data-title="<?php esc_attr_e( 'Menu Setting', 'konte-addons' ) ?>"><?php esc_html_e( 'Settings', 'konte-addons' ) ?></a>
			<a href="#" class="media-menu-item {{ data.current === 'content' ? 'active' : '' }}" data-panel="content" data-title="<?php esc_attr_e( 'Menu Content', 'konte-addons' ) ?>"><?php esc_html_e( 'Content', 'konte-addons' ) ?></a>
		<# } #>
		<a href="#" class="media-menu-item {{ data.current === 'design' ? 'active' : '' }}" data-panel="design" data-title="<?php esc_attr_e( 'Mega Column Design', 'konte-addons' ) ?>"><?php esc_html_e( 'Design', 'konte-addons' ) ?></a>
	<# } else { #>
		<a href="#" class="media-menu-item {{ data.current === 'content' ? 'active' : '' }}" data-panel="content" data-title="<?php esc_attr_e( 'Menu Content', 'konte-addons' ) ?>"><?php esc_html_e( 'Content', 'konte-addons' ) ?></a>
	<# } #>
	<a href="#" class="media-menu-item {{ data.current === 'icon' ? 'active' : '' }}" data-panel="icon" data-title="<?php esc_attr_e( 'Menu Icon', 'konte-addons' ) ?>"><?php esc_html_e( 'Icon', 'konte-addons' ) ?></a>
</div>