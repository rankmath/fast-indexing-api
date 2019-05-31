<div class="wrap rank-math-wrap">
	<h1><?php _e( 'Indexing API Settings', 'rm-giapi' ); ?></h1>
	<form enctype="multipart/form-data" method="POST" action="">
		<?php wp_nonce_field( 'giapi-save', '_wpnonce', true, true ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e( 'JSON Key:', 'rm-giapi' ); ?>
					<p class="description"><?php _e( 'Upload the Service Account JSON key file you obtained from Google API Console or paste its contents in the field.', 'rm-giapi' ); ?></p>
					<div style="display: inline-block; border: 1px solid #ccc; background: #fafafa; padding: 10px 10px 10px 6px; margin-top: 8px;"><span class="dashicons dashicons-editor-help"></span> <a href="<?php echo $this->setup_guide_url; ?>" target="_blank"><?php _e( 'Read our setup guide', 'rm-giapi' ); ?></a></div>
				</th>
				<td>
					<?php if ( file_exists( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ) { ?>
						<textarea name="giapi_settings[json_key]" class="large-text" rows="8" readonly="readonly"><?php echo esc_textarea( file_get_contents( plugin_dir_path( __FILE__ ) . 'rank-math-835b6feb842b.json' ) ); ?></textarea>
						<br>
						<p class="description"><?php _e( '<code>rank-math-835b6feb842b.json</code> file found in the plugin folder. You cannot change the JSON key from here until you delete or remame this file.', 'rm-giapi' ); ?></p>
					<?php } else { ?>
						<textarea name="giapi_settings[json_key]" class="large-text" rows="8"><?php echo esc_textarea( $this->get_setting( 'json_key' ) ); ?></textarea>
						<br>
						<label>
							<?php _e( 'Or upload JSON file: ', 'rm-giapi' ); ?>
							<input type="file" name="json_file" />
						</label>
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Post Types:', 'rm-giapi' ); ?>
					<p class="description"><?php _e( 'Submit posts from these post types automatically in the background when a post is published, edited, or deleted. Also adds action links to submit manually.', 'rm-giapi' ); ?></p>
				</th>
				<td><?php $this->post_types_checkboxes(); ?></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
