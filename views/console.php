<?php
/**
 * Indexing API Console page contents.
 *
 * @package Instant Indexing
 */

?>
<div class="wrap rank-math-wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
	if ( ! $this->get_setting( 'json_key' ) && ! $this->get_setting( 'bing_key' ) ) {
		?>
		<p class="description">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %s is a link to the plugin settings tab. */
					__( 'Please navigate to the %s tab to configure the plugin.', 'fast-indexing-api' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=instant-indexing' ) ) . '">' . __( 'Settings', 'fast-indexing-api' ) . '</a>'
				)
			);
			?>
		</p>
		</div>
		<?php
		return;
	}
	?>

		<div class="giapi-limits">
			<?php if ( $this->get_setting( 'json_key' ) ) { ?>
				<p class="" style="line-height: 1.8"><a href="https://developers.google.com/search/apis/indexing-api/v3/quota-pricing" target="_blank"><strong><?php esc_html_e( 'Google API Remaining Quota:', 'fast-indexing-api' ); ?></strong></a><br>
				<code><?php esc_html_e( 'PublishRequestsPerDayPerProject', 'fast-indexing-api' ); ?> = <strong id="giapi-limit-publishperday"><?php echo absint( $limits['publishperday'] ); ?></strong> / <?php echo absint( $limits['publishperday_max'] ); ?></code><br>
				<code><?php esc_html_e( 'RequestsPerMinutePerProject', 'fast-indexing-api' ); ?> = <strong id="giapi-limit-permin"><?php echo absint( $limits['permin'] ); ?></strong> / <?php echo absint( $limits['permin_max'] ); ?></code><br>
				<code><?php esc_html_e( 'MetadataRequestsPerMinutePerProject', 'fast-indexing-api' ); ?> = <strong id="giapi-limit-metapermin"><?php echo absint( $limits['metapermin'] ); ?></strong> / <?php echo absint( $limits['metapermin_max'] ); ?></code></p>
			<?php } ?>
			<?php if ( $this->get_setting( 'bing_key' ) ) { ?>
				<p class="" style="line-height: 1.8;"><a href="https://www.bing.com/webmasters/help/submit-urls-to-bing-62f2860a" target="_blank"><strong><?php esc_html_e( 'Bing API Remaining Quota:', 'fast-indexing-api' ); ?></strong></a><br>
				<code><?php esc_html_e( 'UrlSubmissionQuota', 'fast-indexing-api' ); ?> = <strong id="giapi-limit-metapermin"><?php echo absint( $limits['bing_submitperday'] ); ?></strong> / <?php echo absint( $limits['bing_submitperday_max'] ); ?></code></p>
			<?php } ?>
		</div>

	<form id="instant-indexing" class="wpform" method="post">
		<label for="giapi-url"><?php esc_html_e( 'URLs (one per line, up to 100 for Google and 500 for Bing):', 'fast-indexing-api' ); ?></label><br>
		<textarea name="url" id="giapi-url" class="regular-text code" rows="5" data-gramm="false"><?php echo esc_textarea( $urls ); ?></textarea>
		<br><br>
		<label><?php esc_html_e( 'Action:', 'fast-indexing-api' ); ?></label><br>
		<?php if ( $this->get_setting( 'json_key' ) ) { ?>
			<label><input type="radio" name="api_action" value="update" class="giapi-action" <?php checked( $selected_action, 'update' ); ?>> <?php esc_html_e( 'Google: Publish/update URL', 'fast-indexing-api' ); ?></label><br>
			<label><input type="radio" name="api_action" value="remove" class="giapi-action" <?php checked( $selected_action, 'remove' ); ?>> <?php esc_html_e( 'Google: Remove URL', 'fast-indexing-api' ); ?></label><br>
			<label><input type="radio" name="api_action" value="getstatus" class="giapi-action" <?php checked( $selected_action, 'getstatus' ); ?>> <?php esc_html_e( 'Google: Get URL status', 'fast-indexing-api' ); ?></label><br>
		<?php } ?>
		<?php if ( $this->get_setting( 'bing_key' ) ) { ?>
			<label><input type="radio" name="api_action" value="bing_update" class="giapi-action" <?php checked( $selected_action, 'bing_submit' ); ?>> <?php esc_html_e( 'Bing: Submit URL', 'fast-indexing-api' ); ?></label><br>
		<?php } ?>
		<input type="submit" id="giapi-submit" class="button button-primary" value="<?php esc_attr_e( 'Send to API', 'fast-indexing-api' ); ?>">
	</form>
	<div id="giapi-response-userfriendly" class="not-ready">
		<br>
		<hr>
		<div class="response-box">
			<code class="response-id"></code>
			<h4 class="response-status"></h4>
			<p class="response-message"></p>
		</div>
		<a href="#" id="giapi-response-trigger" class="button button-secondary"><?php esc_html_e( 'Show Raw Response', 'fast-indexing-api' ); ?> <span class="dashicons dashicons-arrow-down-alt2" style="margin-top: 3px;"></span></a>
	</div>
	<div id="giapi-response-wrapper">
		<br>
		<textarea id="giapi-response" class="large-text code" rows="10" placeholder="<?php esc_attr_e( 'Response...', 'fast-indexing-api' ); ?>"></textarea>
	</div>
</div>
