<?php
/**
 * Indexing API Console page contents.
 *
 * @package RM_GIAPI
 */

?>
<div class="wrap rank-math-wrap">
	<h2><?php echo get_admin_page_title(); ?></h2>

	<?php
	if ( ! $this->get_setting( 'json_key' ) ) {
		?>
		<p class="description"><?php printf( __( 'Please navigate to the %s page to configure the plugin.', 'rm-giapi' ), '<a href="' . admin_url( 'admin.php?page=rm-giapi-settings' ) . '">' . __( 'Indexing API Settings', 'rm-giapi' ) . '</a>' ); ?></p>
		<?php
		return;
	}
	?>

	<div class="giapi-limits">
		<p class="" style="line-height: 1.8"><a href="https://developers.google.com/search/apis/indexing-api/v3/quota-pricing" target="_blank"><strong><?php _e( 'API Limits:', 'rm-giapi' ); ?></strong></a><br>
		<code>PublishRequestsPerDayPerProject = <strong id="giapi-limit-publishperday"><?php echo $limits['publishperday']; ?></strong> / 200</code><br>
		<code>RequestsPerMinutePerProject = <strong id="giapi-limit-permin"><?php echo $limits['permin']; ?></strong> / 600</code><br>
		<code>MetadataRequestsPerMinutePerProject = <strong id="giapi-limit-metapermin"><?php echo $limits['metapermin']; ?></strong> / 180</code></p>
	</div>

	<form id="rm-giapi" class="wpform" method="post">
		<label for="giapi-url"><?php _e( 'URLs (one per line, up to 100):', 'rm-giapi' ); ?></label><br>
		<textarea name="url" id="giapi-url" class="regular-text code" style="min-width: 600px;" rows="5" data-gramm="false"><?php echo esc_textarea( $urls ); ?></textarea>
		<br><br>
		<label><?php _e( 'Action:', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="update" checked="checked" class="giapi-action"> <?php _e( 'Publish/update', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="remove" class="giapi-action"> <?php _e( 'Remove', 'rm-giapi' ); ?></label><br>
		<label><input type="radio" name="api_action" value="getstatus" class="giapi-action"> <?php _e( 'Get status', 'rm-giapi' ); ?></label><br><br>
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
		<a href="#" id="giapi-response-trigger" class="button button-secondary"><?php _e( 'Show Raw Response', 'rm-giapi' ); ?> <span class="dashicons dashicons-arrow-down-alt2" style="margin-top: 3px;"></span></a>
	</div>
	<div id="giapi-response-wrapper">
		<br>
		<textarea id="giapi-response" class="large-text code" rows="10" placeholder="<?php esc_attr_e( 'Response...', 'rm-giapi' ); ?>"></textarea>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var $responseTextarea = $('#giapi-response');
		var $submitButton = $('#giapi-submit');
		var $urlField = $('#giapi-url');
		var $actionRadio = $('.giapi-action');
		var $ufResponse = $('#giapi-response-userfriendly');
		var logResponse = function( info, url ) {
			var d = new Date();
			var n = d.toLocaleTimeString();
			var urls = $urlField.val().split('\n').filter(Boolean);
			var urls_str = urls[0];
			var is_batch = false;
			var action = $actionRadio.filter(':checked').val();
			if ( urls.length > 1 ) {
				urls_str = '(batch)';
				is_batch = true;
			}

			$ufResponse.removeClass('not-ready fail success').addClass('ready').find('.response-id').html('<strong>' + action + '</strong>' + ' ' + urls_str);
			if ( ! is_batch ) {
				if ( typeof info.error !== 'undefined' ) {
					$ufResponse.addClass('fail').find('.response-status').text('<?php echo esc_js( __( 'Error', 'rm-giapi' ) ); ?> '+info.error.code).siblings('.response-message').text(info.error.message);
				} else {
					var base = info;
					if ( typeof info.urlNotificationMetadata != 'undefined' ) {
						base = info.urlNotificationMetadata;
					}
					var d = new Date(base.latestUpdate.notifyTime);
					$ufResponse.addClass('success').find('.response-status').text('<?php echo esc_js( __( 'Success', 'rm-giapi' ) ); ?> ').siblings('.response-message').text('<?php echo esc_js( __( 'Last updated ', 'rm-giapi' ) ); ?> ' + d.toString());
				}
			} else {
				$ufResponse.addClass('success').find('.response-status').text('<?php echo esc_js( __( 'Success', 'rm-giapi' ) ); ?> ').siblings('.response-message').text('<?php echo esc_js( __( 'See response for details.', 'rm-giapi' ) ); ?>');
				$.each(info, function(index, val) {
					if ( typeof val.error !== 'undefined' ) {
						$ufResponse.addClass('fail').find('.response-status').text('<?php echo esc_js( __( 'Error', 'rm-giapi' ) ); ?> '+val.error.code).siblings('.response-message').text(val.error.message);
					}
				});
			}

			var rawdata = n + " " + action + " " + urls_str + "\n" + JSON.stringify(info, null, 2) + "\n" + "-".repeat(56);
			var current = $responseTextarea.val();
			$responseTextarea.val(rawdata + "\n" + current);
		};

		$('#giapi-response-trigger').click(function(e) {
				e.preventDefault();
				$(this).find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2')
				$('#giapi-response-wrapper').toggle();
		});

		$('#rm-giapi').submit(function(event) {
			event.preventDefault();
			$submitButton.attr('disabled', 'disabled');
			var input_url = $urlField.val();
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: { action: 'rm_giapi', url: input_url, api_action: $actionRadio.filter(':checked').val() },
			}).always(function(data) {
				logResponse( data, input_url );
				$submitButton.removeAttr('disabled');
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: { action: 'rm_giapi_limits' },
				})
				.done(function( data ) {
					$.each( data, function(index, val) {
						$('#giapi-limit-'+index).text(val);
					});
				});
			});
		});

		<?php if ( ! empty( $_GET['apiaction'] ) && ( ! empty( $_GET['apiurl'] ) || ! empty( $_GET['apipostid'] ) ) && wp_verify_nonce( $_GET['_wpnonce'], 'giapi-action' ) ) { ?>
			$('#rm-giapi').find('input.giapi-action[value="<?php echo sanitize_title( $_GET['apiaction'] ); ?>"]').prop('checked', true);
			$('#rm-giapi').submit();
		<?php } ?>
	});
</script>
