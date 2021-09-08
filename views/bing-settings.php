<?php
/**
 * BING URL Submission API Settings page contents.
 *
 * @package Instant Indexing
 */

?>
<div class="wrap rank-math-wrap">
	<h1><?php esc_attr_e( 'Bing URL Submission API Settings', 'fast-indexing-api' ); ?></h1>
	<form enctype="multipart/form-data" method="POST" action="">
		<?php wp_nonce_field( 'giapi-save', '_wpnonce', true, true ); ?>
		<table class="form-table">
			<tr valign="top" class="bing-api-key-row">
				<th scope="row">
					<?php esc_html_e( 'Bing API Key:', 'fast-indexing-api' ); ?>
					<p class="description"><?php esc_html_e( 'Paste your Bing URL Submission API Key in this field.', 'fast-indexing-api' ); ?></p>
					<div class="setup-guide-link-wrapper"><span class="dashicons dashicons-editor-help"></span> <a href="<?php echo $this->bing_guide_url; ?>" target="_blank"><?php esc_html_e( 'Read our setup guide', 'fast-indexing-api' ); ?></a></div>
				</th>
				<td>
					<input type="password" value="<?php echo esc_attr( $this->get_setting( 'bing_api_key' ) ); ?>" class="large-text" name="giapi_settings[bing_key]" >
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Submit Post Types to Bing:', 'fast-indexing-api' ); ?>
					<p class="description"><?php esc_html_e( 'Submit posts from these post types automatically to the Bing URL Submission API when a post is published or edited.', 'fast-indexing-api' ); ?></p>
				</th>
				<td><?php $this->post_types_checkboxes( 'bing' ); ?></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
