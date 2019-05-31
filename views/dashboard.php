<div class="rank-math-page">
	<div class="wrap rank-math-wrap">

		<span class="wp-header-end"></span>

		<h1><?php _e( 'Welcome to Rank Math!', 'rm-giapi' ); ?></h1>

		<div class="rank-math-text">
			<?php _e( 'The most complete WordPress SEO plugin to convert your website into a traffic generating machine.', 'rm-giapi' ); ?>
		</div>


		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="#" title="<?php esc_attr_e( 'Modules', 'rm-giapi' ); ?>"><?php _e( 'Modules', 'rm-giapi' ); ?></a>
			<a class="nav-tab" href="#" title="<?php esc_attr_e( 'Setup Wizard', 'rm-giapi' ); ?>"><?php _e( 'Setup Wizard', 'rm-giapi' ); ?></a>
			<a class="nav-tab" href="#" title="<?php esc_attr_e( 'Import &amp; Export', 'rm-giapi' ); ?>"><?php _e( 'Import &amp; Export', 'rm-giapi' ); ?></a>
		</h2>

		<div class="rank-math-ui module-listing">

		<div class="two-col">
			<div class="col">
				<div class="rank-math-box active">
					<span class="dashicons dashicons-admin-site-alt3"></span>
					<header>
						<h3><?php _e( 'Indexing API (Beta)', 'rm-giapi' ); ?></h3>
						<p><em><?php _e( 'Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'rm-giapi' ); ?> <a href="<?php echo $this->setup_guide_url; ?>" target="_blank"><?php _e( 'Read our setup guide', 'rm-giapi' ); ?></a></em></p>
						<a class="module-settings" href="<?php echo admin_url( 'admin.php?page=rm-giapi-settings' ); ?>"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
					</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-indexing-api" name="modules[]" value="indexing-api" checked="checked" readonly="readonly">
								<label for="module-indexing-api" class="indexing-api-label">
									<?php _e( 'Toggle', 'rm-giapi' ); ?>
								</label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-dismiss"></span>
							<header>
								<h3><?php _e( '404 Monitor', 'rm-giapi' ); ?></h3>
								<p><em><?php _e( 'Records the URLs on which visitors &amp; search engines run into 404 Errors. You can also turn on Redirections to redirect the error causing URLs to other URLs.', 'rm-giapi' ); ?></em></p>
								<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
							</header>
							<div class="status wp-clearfix">
								<span class="rank-math-switch">
									<input type="checkbox" class="rank-math-modules" id="module-404-monitor" name="modules[]" value="404-monitor">
									<label for="module-404-monitor" class="">
										<?php _e( 'Toggle', 'rm-giapi' ); ?>
									</label>
									<span class="input-loading"></span>
								</span>
								<label>
									<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
									<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
								</label>
							</div>
						</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-smartphone"></span>
						<header>
							<h3><?php _e( 'AMP', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Install the AMP plugin from WordPress.org to make Rank Math work with Accelerated Mobile Pages. It is required because AMP are different than WordPress pages and our plugin doesn\'t work with them out-of-the-box.', 'rm-giapi' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
								<label for="module-amp" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-cart"></span>

						<header>
							<h3><?php _e( 'bbPress', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Add required meta tags on bbPress pages.', 'rm-giapi' ); ?></em></p>
						</header>

						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-bbpress" name="modules[]" value="bbpress" disabled="disabled">
								<label for="module-bbpress" class="rank-math-tooltip"><?php _e( 'Toggle', 'rm-giapi' ); ?><span><?php _e( 'Please activate bbPress plugin to use this module.', 'rm-giapi' ); ?></span>                             </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-admin-links"></span>
						<header>
							<h3><?php _e( 'Link Counter', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Counts the total number of internal, external links, to and from links inside your posts.', 'rm-giapi' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-link-counter" name="modules[]" value="link-counter">
								<label for="module-link-counter" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?>                                                                    </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?>                             <span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?> </span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-location-alt"></span>
						<header>
							<h3><?php _e( 'Local SEO &amp; Google Knowledge Graph', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Dominate the search results for local audience by optimizing your website and posts using this Rank Math module.', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-local-seo" name="modules[]" value="local-seo">
								<label for="module-local-seo" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?>                                                                   </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-randomize"></span>
						<header>
							<h3><?php _e( 'Redirections', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Redirect non-existent content easily with 301 and 302 status code. This can help reduce errors and improve your site ranking.', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-redirections" name="modules[]" value="redirections">
								<label for="module-redirections" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?>                                                                    </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-awards"></span>
						<header>
							<h3><?php _e( 'Rich Snippets', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Enable support for the Rich Snippets, which adds metadata to your website, resulting in rich search results and more traffic.', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-rich-snippet" name="modules[]" value="rich-snippet">
								<label for="module-rich-snippet" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-admin-users"></span>
						<header>
							<h3><?php _e( 'Role Manager', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'The Role Manager allows you to use internal WordPress\' roles to control which of your site admins can change Rank Math\'s settings', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-role-manager" name="modules[]" value="role-manager">
								<label for="module-role-manager" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-search"></span>
						<header>
							<h3><?php _e( 'Search Console', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Connect Rank Math with Google Search Console to see the most important information from Google directly in your WordPress dashboard.', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-search-console" name="modules[]" value="search-console">
								<label for="module-search-console" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?>                                                                  </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-chart-bar"></span>
							<header>
								<h3><?php _e( 'SEO Analysis', 'rm-giapi' ); ?></h3>
								<p><em><?php _e( 'Let Rank Math analyze your website and your website\'s content using 70+ different tests to provide tailor-made SEO Analysis to you.', 'rm-giapi' ); ?></em></p>
								<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
							</header>
							<div class="status wp-clearfix">
								<span class="rank-math-switch">
									<input type="checkbox" class="rank-math-modules" id="module-seo-analysis" name="modules[]" value="seo-analysis">
									<label for="module-seo-analysis" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?></label>
									<span class="input-loading"></span>
								</span>
								<label>
									<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
									<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
								</label>
							</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-networking"></span>
						<header>
							<h3><?php _e( 'Sitemap', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'Enable Rank Math\'s sitemap feature, which helps search engines index your website\'s content effectively.', 'rm-giapi' ); ?></em></p>
							<a class="module-settings" href="#"><?php _e( 'Settings', 'rm-giapi' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-sitemap" name="modules[]" value="sitemap">
								<label for="module-sitemap" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-cart"></span>
						<header>
							<h3><?php _e( 'WooCommerce', 'rm-giapi' ); ?></h3>
							<p><em><?php _e( 'WooCommerce module to use Rank Math to optimize WooCommerce Product Pages.', 'rm-giapi' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-woocommerce" name="modules[]" value="woocommerce">
								<label for="module-woocommerce" class=""><?php _e( 'Toggle', 'rm-giapi' ); ?><span><?php _e( 'Please activate WooCommerce plugin to use this module.', 'rm-giapi' ); ?></span>                             </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php _e( 'Status:', 'rm-giapi' ); ?><span class="module-status active-text"><?php _e( 'Active', 'rm-giapi' ); ?></span>
								<span class="module-status inactive-text"><?php _e( 'Inactive', 'rm-giapi' ); ?> </span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
if ( file_exists( WP_PLUGIN_DIR . '/seo-by-rank-math' ) ) {
	$text         = __( 'Activate Now', 'schema-markup' );
	$path         = 'seo-by-rank-math/rank-math.php';
	$link         = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $path ), 'activate-plugin_' . $path );
	$button_class = 'activate-now';
} else {
	$text         = __( 'Install for Free', 'schema-markup' );
	$link         = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=seo-by-rank-math' ), 'install-plugin_seo-by-rank-math' );
	$button_class = 'install-now';
}
?>

<div class="rank-math-feedback-modal rank-math-ui try-rankmath-panel" id="rank-math-feedback-form">
	<div class="rank-math-feedback-content">
		<div class="plugin-card plugin-card-seo-by-rank-math">
			<span class="button-close dashicons dashicons-no-alt alignright"></span>
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<a href="https://rankmath.com/wordpress/plugin/seo-suite/" target="_blank">
						<?php esc_html_e( 'WordPress SEO Plugin – Rank Math', '404-monitor' ); ?>
						<img src="https://ps.w.org/seo-by-rank-math/assets/icon.svg" class="plugin-icon" alt="<?php esc_html_e( 'Rank Math SEO', '404-monitor' ); ?>">
						</a>
						<span class="vers column-rating">
							<a href="https://wordpress.org/support/plugin/seo-by-rank-math/reviews/" target="_blank">
								<div class="star-rating">
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
									<div class="star star-full" aria-hidden="true"></div>
								</div>
								<span class="num-ratings" aria-hidden="true">(195)</span>
							</a>
						</span>
					</h3>
				</div>

				<div class="desc column-description">
					<p><?php esc_html_e( 'Rank Math is a revolutionary SEO plugin that combines the features of many SEO tools in a single package & helps you multiply your traffic.', '404-monitor' ); ?></p>
				</div>
			</div>

			<div class="plugin-card-bottom">
				<div class="column-compatibility">
					<span class="compatibility-compatible"><strong><?php esc_html_e( 'Compatible', '404-monitor' ); ?></strong> <?php esc_html_e( 'with your version of WordPress', '404-monitor' ); ?></span>
				</div>
				<a href="<?php echo $link; ?>" class="button button-primary <?php echo $button_class; ?>" data-slug="seo-by-rank-math" data-name="Rank Math"><?php echo $text; ?></a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var dialog = $( '#rank-math-feedback-form' )

		dialog.on( 'click', '.button-close', function( event ) {
			event.preventDefault()
			dialog.fadeOut()
		})

		// Enable/Disable Modules
		$( '.module-listing .rank-math-box:not(.active)' ).on( 'click', function(e) {
			e.preventDefault();

			$( '#rank-math-feedback-form' ).fadeIn();

			return false;
		});

		$( '#rank-math-feedback-form' ).on( 'click', function( e ) {
			if ( 'rank-math-feedback-form' === e.target.id ) {
				$( this ).find( '.button-close' ).trigger( 'click' );
			}
		});

		$('a.nav-tab').not('.nav-tab-active').click(function(event) {
			$( '#rank-math-feedback-form' ).fadeIn();
		});

		// Install & Activate Rank Math from modal.
		var tryRankmathPanel = $( '.try-rankmath-panel' ),
				installRankmathSuccess;

		installRankmathSuccess = function( response ) {
			response.activateUrl += '&from=schema-try-rankmath';
			response.activateLabel = wp.updates.l10n.activatePluginLabel.replace( '%s', response.pluginName );
			tryRankmathPanel.find('.install-now').text('Activating...');
			window.location.href = response.activateUrl;
		};

		tryRankmathPanel.on( 'click', '.install-now', function( e ) {
			e.preventDefault();
			var args = {
				slug: $( e.target ).data( 'slug' ),
				success: installRankmathSuccess
			};
			wp.updates.installPlugin( args );
		} );
	});
</script>
