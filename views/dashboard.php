<?php
/**
 * Rank Math Dashboard page contents.
 *
 * @package RM_GIAPI
 */

?>
<div class="rank-math-page">
	<div class="wrap rank-math-wrap">

		<span class="wp-header-end"></span>

		<h1><?php esc_html_e( 'Welcome to Rank Math!', 'google-indexing-api-by-rank-math' ); ?></h1>

		<div class="rank-math-text">
			<?php esc_html_e( 'The most complete WordPress SEO plugin to convert your website into a traffic generating machine.', 'google-indexing-api-by-rank-math' ); ?>
		</div>


		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="#" title="<?php esc_attr_e( 'Modules', 'google-indexing-api-by-rank-math' ); ?>"><?php esc_html_e( 'Modules', 'google-indexing-api-by-rank-math' ); ?></a>
			<a class="nav-tab" href="#" title="<?php esc_attr_e( 'Setup Wizard', 'google-indexing-api-by-rank-math' ); ?>"><?php esc_html_e( 'Setup Wizard', 'google-indexing-api-by-rank-math' ); ?></a>
			<a class="nav-tab" href="#" title="<?php esc_attr_e( 'Import &amp; Export', 'google-indexing-api-by-rank-math' ); ?>"><?php esc_html_e( 'Import &amp; Export', 'google-indexing-api-by-rank-math' ); ?></a>
		</h2>

		<div class="rank-math-ui module-listing">

		<div class="two-col">
			<div class="col">
				<div class="rank-math-box active">
					<span class="dashicons dashicons-admin-site-alt3"></span>
					<header>
						<h3><?php esc_html_e( 'Indexing API (Beta)', 'google-indexing-api-by-rank-math' ); ?></h3>
						<p><em><?php esc_html_e( 'Directly notify Google when pages are added, updated or removed. The Indexing API supports pages with either job posting or livestream structured data.', 'google-indexing-api-by-rank-math' ); ?> <a href="<?php echo esc_url( $this->setup_guide_url ); ?>" target="_blank"><?php esc_html_e( 'Read our setup guide', 'google-indexing-api-by-rank-math' ); ?></a></em></p>
						<a class="module-settings" href="<?php echo esc_url( admin_url( 'admin.php?page=rm-giapi' ) ); ?>"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
					</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-indexing-api" name="modules[]" value="indexing-api" checked="checked" readonly="readonly">
								<label for="module-indexing-api" class="indexing-api-label">
									<?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>
								</label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-dismiss"></span>
							<header>
								<h3><?php esc_html_e( '404 Monitor', 'google-indexing-api-by-rank-math' ); ?></h3>
								<p><em><?php esc_html_e( 'Records the URLs on which visitors &amp; search engines run into 404 Errors. You can also turn on Redirections to redirect the error causing URLs to other URLs.', 'google-indexing-api-by-rank-math' ); ?></em></p>
								<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
							</header>
							<div class="status wp-clearfix">
								<span class="rank-math-switch">
									<input type="checkbox" class="rank-math-modules" id="module-404-monitor" name="modules[]" value="404-monitor">
									<label for="module-404-monitor" class="">
										<?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>
									</label>
									<span class="input-loading"></span>
								</span>
								<label>
									<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
									<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
								</label>
							</div>
						</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-smartphone"></span>
						<header>
							<h3><?php esc_html_e( 'AMP', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Install the AMP plugin from WordPress.org to make Rank Math work with Accelerated Mobile Pages. It is required because AMP are different than WordPress pages and our plugin doesn\'t work with them out-of-the-box.', 'google-indexing-api-by-rank-math' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
								<label for="module-amp" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box ">
						<span class="dashicons dashicons-cart"></span>

						<header>
							<h3><?php esc_html_e( 'bbPress', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Add required meta tags on bbPress pages.', 'google-indexing-api-by-rank-math' ); ?></em></p>
						</header>

						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-bbpress" name="modules[]" value="bbpress" disabled="disabled">
								<label for="module-bbpress" class="rank-math-tooltip"><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?><span><?php esc_html_e( 'Please activate bbPress plugin to use this module.', 'google-indexing-api-by-rank-math' ); ?></span>                             </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-admin-links"></span>
						<header>
							<h3><?php esc_html_e( 'Link Counter', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Counts the total number of internal, external links, to and from links inside your posts.', 'google-indexing-api-by-rank-math' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-link-counter" name="modules[]" value="link-counter">
								<label for="module-link-counter" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>                                                                    </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?>                             <span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?> </span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-location-alt"></span>
						<header>
							<h3><?php esc_html_e( 'Local SEO &amp; Google Knowledge Graph', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Dominate the search results for local audience by optimizing your website and posts using this Rank Math module.', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-local-seo" name="modules[]" value="local-seo">
								<label for="module-local-seo" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>                                                                   </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-randomize"></span>
						<header>
							<h3><?php esc_html_e( 'Redirections', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Redirect non-existent content easily with 301 and 302 status code. This can help reduce errors and improve your site ranking.', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-redirections" name="modules[]" value="redirections">
								<label for="module-redirections" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>                                                                    </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-awards"></span>
						<header>
							<h3><?php esc_html_e( 'Rich Snippets', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Enable support for the Rich Snippets, which adds metadata to your website, resulting in rich search results and more traffic.', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-rich-snippet" name="modules[]" value="rich-snippet">
								<label for="module-rich-snippet" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-admin-users"></span>
						<header>
							<h3><?php esc_html_e( 'Role Manager', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'The Role Manager allows you to use internal WordPress\' roles to control which of your site admins can change Rank Math\'s settings', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-role-manager" name="modules[]" value="role-manager">
								<label for="module-role-manager" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-search"></span>
						<header>
							<h3><?php esc_html_e( 'Search Console', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Connect Rank Math with Google Search Console to see the most important information from Google directly in your WordPress dashboard.', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-search-console" name="modules[]" value="search-console">
								<label for="module-search-console" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?>                                                                  </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-chart-bar"></span>
							<header>
								<h3><?php esc_html_e( 'SEO Analysis', 'google-indexing-api-by-rank-math' ); ?></h3>
								<p><em><?php esc_html_e( 'Let Rank Math analyze your website and your website\'s content using 70+ different tests to provide tailor-made SEO Analysis to you.', 'google-indexing-api-by-rank-math' ); ?></em></p>
								<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
							</header>
							<div class="status wp-clearfix">
								<span class="rank-math-switch">
									<input type="checkbox" class="rank-math-modules" id="module-seo-analysis" name="modules[]" value="seo-analysis">
									<label for="module-seo-analysis" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?></label>
									<span class="input-loading"></span>
								</span>
								<label>
									<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
									<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
								</label>
							</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-networking"></span>
						<header>
							<h3><?php esc_html_e( 'Sitemap', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'Enable Rank Math\'s sitemap feature, which helps search engines index your website\'s content effectively.', 'google-indexing-api-by-rank-math' ); ?></em></p>
							<a class="module-settings" href="#"><?php esc_html_e( 'Settings', 'google-indexing-api-by-rank-math' ); ?></a>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-sitemap" name="modules[]" value="sitemap">
								<label for="module-sitemap" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?></label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
							</label>
						</div>
					</div>
				</div>

				<div class="col">
					<div class="rank-math-box">
						<span class="dashicons dashicons-cart"></span>
						<header>
							<h3><?php esc_html_e( 'WooCommerce', 'google-indexing-api-by-rank-math' ); ?></h3>
							<p><em><?php esc_html_e( 'WooCommerce module to use Rank Math to optimize WooCommerce Product Pages.', 'google-indexing-api-by-rank-math' ); ?></em></p>
						</header>
						<div class="status wp-clearfix">
							<span class="rank-math-switch">
								<input type="checkbox" class="rank-math-modules" id="module-woocommerce" name="modules[]" value="woocommerce">
								<label for="module-woocommerce" class=""><?php esc_html_e( 'Toggle', 'google-indexing-api-by-rank-math' ); ?><span><?php esc_html_e( 'Please activate WooCommerce plugin to use this module.', 'google-indexing-api-by-rank-math' ); ?></span>                             </label>
								<span class="input-loading"></span>
							</span>
							<label>
								<?php esc_html_e( 'Status:', 'google-indexing-api-by-rank-math' ); ?><span class="module-status active-text"><?php esc_html_e( 'Active', 'google-indexing-api-by-rank-math' ); ?></span>
								<span class="module-status inactive-text"><?php esc_html_e( 'Inactive', 'google-indexing-api-by-rank-math' ); ?> </span>
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
	$pluginpath   = 'seo-by-rank-math/rank-math.php';
	$pluginlink   = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $pluginpath ), 'activate-plugin_' . $pluginpath );
	$button_class = 'activate-now';
} else {
	$text         = __( 'Install for Free', 'schema-markup' );
	$pluginlink   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=seo-by-rank-math' ), 'install-plugin_seo-by-rank-math' );
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
				<a href="<?php echo esc_url( $pluginlink ); ?>" class="button button-primary <?php echo esc_attr( $button_class ); ?>" data-slug="seo-by-rank-math" data-name="Rank Math"><?php echo esc_html( $text ); ?></a>
			</div>
		</div>
	</div>
</div>
