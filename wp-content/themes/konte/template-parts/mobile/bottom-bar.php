<?php
/**
 * Template file for displaying mobile bottom bar
 *
 * @package Konte
 */

$items = konte_get_option( 'mobile_bottom_bar_items' );
?>
<div id="mobile-bottom-bar" class="mobile-bottom-bar mobile-bottom-bar--<?php echo esc_attr( konte_get_option( 'mobile_bottom_bar_items_display' ) ); ?>">
	<nav class="mobile-bottom-nav">
		<ul>
			<?php foreach ( $items as $item ) : ?>
				<li class="mobile-bottom-bar-item mobile-bottom-bar__<?php echo esc_attr( $item ); ?>">
					<?php konte_mobile_bottom_bar_item( $item ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>
