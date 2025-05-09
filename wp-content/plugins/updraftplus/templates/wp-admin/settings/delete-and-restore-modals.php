<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<div id="updraft-message-modal" title="UpdraftPlus">
	<div id="updraft-message-modal-innards">
	</div>
</div>

<div id="updraft-delete-modal" title="<?php esc_html_e('Delete backup set', 'updraftplus');?>"> 
	<form id="updraft_delete_form" method="post">
		<p id="updraft_delete_question_singular">
			<?php
			/* translators: %s: Item to be removed */
			printf(esc_html__('Are you sure that you wish to remove %s from UpdraftPlus?', 'updraftplus'), esc_html__('this backup set', 'updraftplus'));
			?> 
		</p>
		<p id="updraft_delete_question_plural" class="updraft-hidden" style="display:none;">
			<?php
			/* translators: %s: Item to be removed */
			printf(esc_html__('Are you sure that you wish to remove %s from UpdraftPlus?', 'updraftplus'), esc_html__('these backup sets', 'updraftplus'));
			?> 
		</p>
		<fieldset>
			<input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('updraftplus-credentialtest-nonce'));?>"> 
			<input type="hidden" name="action" value="updraft_ajax">
			<input type="hidden" name="subaction" value="deleteset">
			<input type="hidden" name="backup_timestamp" value="0" id="updraft_delete_timestamp">
			<input type="hidden" name="backup_nonce" value="0" id="updraft_delete_nonce">
			<div id="updraft-delete-remote-section">
				<input checked="checked" type="checkbox" name="delete_remote" id="updraft_delete_remote" value="1"> <label for="updraft_delete_remote"><?php esc_html_e('Also delete from remote storage', 'updraftplus');?></label><br> 
			</div>
		</fieldset>
	</form>
</div>

<div class="updraft_restore_container" style="display: none;">
	<div class="updraft_restore_main--header"><?php esc_html_e('UpdraftPlus Restoration', 'updraftplus'); ?> — <strong><?php esc_html_e('Restore files from', 'updraftplus');?>:</strong> <span class="updraft_restore_date"></span></div> 
	<div class="updraft_restore_main">
		<div id="updraft-restore-modal" title="UpdraftPlus - <?php esc_html_e('Restore backup', 'updraftplus');?>"> 

			<div class="updraft-restore-modal--stage updraft--flex" id="updraft-restore-modal-stage2">
				<div class="updraft--two-halves">
					<p><strong><?php esc_html_e('Retrieving (if necessary) and preparing backup files...', 'updraftplus');?></strong></p> 
					<div id="updraft-restore-modal-stage2a"></div>
					<div id="ud_downloadstatus2"></div>
				</div>
			</div>

			<div class="updraft-restore-modal--stage updraft--flex" id="updraft-restore-modal-stage1">
				<div class="updraft--one-half updraft-color--very-light-grey">
					<p><?php echo esc_html(__("Restoring will replace this site's themes, plugins, uploads, database and/or other content directories (according to what is contained in the backup set, and your selection).", 'updraftplus').' '.__('Choose the components to restore', 'updraftplus')); ?>:</p> 
					<p><em><a href="<?php echo esc_url(apply_filters('updraftplus_com_link', "https://updraftplus.com/faqs/what-should-i-understand-before-undertaking-a-restoration/"));?>" target="_blank"><?php esc_html_e('Do read this helpful article of useful things to know before restoring.', 'updraftplus');?></a></em></p> 
				</div>
				<div class="updraft--one-half">
					<form id="updraft_restore_form" method="post">
						<fieldset>
							<input type="hidden" name="action" value="updraft_restore">
							<input type="hidden" name="updraftplus_ajax_restore" value="start_ajax_restore">
							<input type="hidden" name="backup_timestamp" value="0" id="updraft_restore_timestamp">
							<input type="hidden" name="meta_foreign" value="0" id="updraft_restore_meta_foreign">
							<input type="hidden" name="updraft_restorer_backup_info" value="" id="updraft_restorer_backup_info">
							<input type="hidden" name="updraft_restorer_restore_options" value="" id="updraft_restorer_restore_options">
							<?php

								// The 'off' check is for badly configured setups - http://wordpress.org/support/topic/plugin-wp-super-cache-warning-php-safe-mode-enabled-but-safe-mode-is-off
								if ($updraftplus->detect_safe_mode()) {
									echo wp_kses_post("<p><em>".__("Your web server has PHP's so-called safe_mode active.", 'updraftplus').' '.__('This makes time-outs much more likely.', 'updraftplus').' '.__('You are recommended to turn safe_mode off, or to restore only one entity at a time', 'updraftplus').' <a href="'.esc_url(apply_filters('updraftplus_com_link', "https://updraftplus.com/faqs/i-want-to-restore-but-have-either-cannot-or-have-failed-to-do-so-from-the-wp-admin-console/")).'" target="_blank">'.__('or to restore manually', 'updraftplus').'.</a></em></p>');
								}
							?>
							<p><strong><?php esc_html_e('Choose the components to restore:', 'updraftplus'); ?></strong></p> 
							<?php
								$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

								foreach ($backupable_entities as $type => $info) {
									if (!isset($info['restorable']) || true == $info['restorable']) {
										$sdescrip = isset($info['shortdescription']) ? $info['shortdescription'] : $info['description'];
										echo '<div class="updraft-restore-item"><input id="updraft_restore_'.esc_attr($type).'" type="checkbox" name="updraft_restore[]" value="'.esc_attr($type).'"> <label id="updraft_restore_label_'.esc_attr($type).'" for="updraft_restore_'.esc_attr($type).'">'.esc_html($sdescrip).'</label><br>';
										do_action("updraftplus_restore_form_$type");
										echo '</div>';
									} else {
										$sdescrip = isset($info['shortdescription']) ? $info['shortdescription'] : $info['description'];
										echo "<div class=\"updraft-restore-item cannot-restore\"><em>".
										/* translators: %s: Entity that cannot be restored */
										esc_html(sprintf(__('The following entity cannot be restored automatically: "%s".', 'updraftplus'), $sdescrip))." ".
										esc_html__('You will need to restore it manually.', 'updraftplus')."</em><br>".'<input id="updraft_restore_'.esc_attr($type).'" type="hidden" name="updraft_restore[]" value="'.esc_attr($type).'">';
										echo '</div>';
									}
								}
							?>
							<div class="updraft-restore-item">
								<input id="updraft_restore_db" type="checkbox" name="updraft_restore[]" value="db"> <label for="updraft_restore_db"><?php esc_html_e('Database', 'updraftplus'); ?></label> 
								<div id="updraft_restorer_dboptions" class="udp-notice below-h2 updraft-restore-option updraft-hidden"><h4>
									<?php
									echo sprintf(
										/* translators: %s: Restoration type */
										esc_html__('%s restoration options:', 'updraftplus'),
										esc_html__('Database', 'updraftplus')
									);
									?>
								</h4> 
								<?php do_action("updraftplus_restore_form_db"); ?>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>

			<div class="updraft-restore--footer">
				<button type="button" class="button updraft-restore--cancel"><?php esc_html_e('Cancel', 'updraftplus'); ?></button> 
				<ul class="updraft-restore--stages">
					<li class="active"><span>1. <?php esc_html_e('Component selection', 'updraftplus'); ?></span></li> 
					<li><span>2. <?php esc_html_e('Verifications', 'updraftplus'); ?></span></li> 
					<li><span>3. <?php esc_html_e('Restoration', 'updraftplus'); ?></span></li> 
				</ul>
				<button type="button" class="button button-primary updraft-restore--next-step"><?php esc_html_e('Next', 'updraftplus'); ?></button>
			</div>

		</div>
	</div>
</div>
