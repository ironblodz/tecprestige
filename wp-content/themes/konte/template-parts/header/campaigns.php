<?php
/**
 * Template part for displaying the campaign bar
 *
 * @package Konte
 */
?>
<div id="campaign-bar" class="campaign-bar">
	<div class="<?php echo esc_attr( apply_filters( 'konte_campaigns_container_class', konte_get_option( 'campaign_container' ) ) ); ?>">
		<div class="campaign-bar__campaigns">
			<?php konte_campaign_items(); ?>
		</div>
	</div>
</div>
