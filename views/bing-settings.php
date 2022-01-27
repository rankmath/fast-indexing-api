<?php
/**
 * BING URL Submission API Settings page contents.
 *
 * @package Instant Indexing
 */

?>
<div class="wrap rank-math-wrap">
	<h1><?php esc_attr_e( 'IndexNow API Settings', 'fast-indexing-api' ); ?></h1>
	<p class="description"><?php esc_attr_e( 'The IndexNow API allows you to submit URLs to Bing and Yandex for indexing.', 'fast-indexing-api' ); ?></p>
	<form enctype="multipart/form-data" method="POST" action="">
		<?php wp_nonce_field( 'giapi-save', '_wpnonce', true, true ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Submit Posts IndexNow:', 'fast-indexing-api' ); ?>
					<p class="description"><?php esc_html_e( 'Submit posts from these post types automatically to the IndexNow API when a post is published or edited.', 'fast-indexing-api' ); ?></p>
				</th>
				<td><?php $this->post_types_checkboxes( 'bing' ); ?></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
