<?php
/**
 * Indexing API Console page contents.
 *
 * @package RM_GIAPI
 */

?>
<div class="wrap rank-math-wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
	if ( ! $this->get_setting( 'json_key' ) ) {
		?>
		<p class="description">
			<?php
			wp_kses_post(
				sprintf(
					/* translators: %s is a link to the plugin settings. */
					__( 'Please navigate to the %s page to configure the plugin.', 'rm-giapi' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=rm-giapi-settings' ) ) . '">' . __( 'Indexing API Settings', 'rm-giapi' ) . '</a>'
				)
			);
			?>
		</p>
		<?php
		return;
	}
	?>

	<div class="giapi-limits">
		<p class="" style="line-height: 1.8"><a href="https://developers.google.com/search/apis/indexing-api/v3/quota-pricing" target="_blank"><strong><?php esc_html_e( 'API Limits:', 'rm-giapi' ); ?></strong></a><br>
		<code>PublishRequestsPerDayPerProject = <strong id="giapi-limit-publishperday"><?php echo absint( $limits['publishperday'] ); ?></strong> / 200</code><br>
		<code>RequestsPerMinutePerProject = <strong id="giapi-limit-permin"><?php echo absint( $limits['permin'] ); ?></strong> / 600</code><br>
		<code>MetadataRequestsPerMinutePerProject = <strong id="giapi-limit-metapermin"><?php echo absint( $limits['metapermin'] ); ?></strong> / 180</code></p>
	</div>

	<form id="rm-giapi" class="wpform" method="post">
		<label for="giapi-url"><?php esc_html_e( 'URLs (one per line, up to 100):', 'rm-giapi' ); ?></label><br>
		<textarea name="url" id="giapi-url" class="regular-text code" style="min-width: 600px;" rows="5" data-gramm="false"><?php echo esc_textarea( $urls ); ?></textarea>
		<br><br>
		<label><?php esc_html_e( 'Action:', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="update" class="giapi-action" <?php checked( $selected_action, 'update' ); ?>> <?php esc_html_e( 'Publish/update', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="remove" class="giapi-action" <?php checked( $selected_action, 'remove' ); ?>> <?php esc_html_e( 'Remove', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="getstatus" class="giapi-action" <?php checked( $selected_action, 'getstatus' ); ?>> <?php esc_html_e( 'Get status', 'rm-giapi' ); ?></label><br><br>
		<input type="submit" id="giapi-submit" class="button button-primary" value="<?php esc_attr_e( 'Send to API', 'rm-giapi' ); ?>">
	</form>
	<div id="giapi-response-userfriendly" class="not-ready">
		<br>
		<hr>
		<div class="response-box">
			<code class="response-id"></code>
			<h4 class="response-status"></h4>
			<p class="response-message"></p>
		</div>
		<a href="#" id="giapi-response-trigger" class="button button-secondary"><?php esc_html_e( 'Show Raw Response', 'rm-giapi' ); ?> <span class="dashicons dashicons-arrow-down-alt2" style="margin-top: 3px;"></span></a>
	</div>
	<div id="giapi-response-wrapper">
		<br>
		<textarea id="giapi-response" class="large-text code" rows="10" placeholder="<?php esc_attr_e( 'Response...', 'rm-giapi' ); ?>"></textarea>
	</div>
</div>
