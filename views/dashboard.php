<?php
/**
 * Rank Math Dashboard page contents.
 *
 * @package Instant Indexing
 */

?>
<div id="wpbody-content" class="rank-math-page" style="background: initial;">
	<div class="rank-math-header">
		<div class="rank-math-logo">
			<a href="#"><i class="rm-icon rm-icon-rank-math"></i></a>
		</div>
		<h1 class="rank-math-logo-text">Rank Math SEO</h1>
		<a href="https://rankmath.com/kb/?utm_source=Plugin&amp;utm_medium=Instant%20Indexing%20RM%20Header%20KB%20Icon&amp;utm_campaign=WP" title="<?php esc_attr_e( 'Rank Math Knowledge Base', 'fast-indexing-api' ); ?>" target="_blank" class="button rank-math-help"><i class="rm-icon rm-icon-help"></i></a>
	</div>
	<div class="rank-math-breadcrumbs-wrap">
		<div class="rank-math-breadcrumbs">
			<span><?php esc_html_e( 'Dashboard', 'fast-indexing-api' ); ?></span>
			<span class="divider">/</span>
			<span class="active"><?php esc_html_e( 'Modules', 'fast-indexing-api' ); ?></span>
		</div>
	</div>
	<div class="wrap rank-math-wrap dashboard">
		<span class="wp-header-end"></span>
		<div class="rank-math-tab-nav" role="tablist" aria-orientation="horizontal">
			<a class="rank-math-tab is-active" href="#" title="<?php esc_attr_e( 'Modules', 'fast-indexing-api' ); ?>">
			<?php esc_html_e( 'Modules', 'fast-indexing-api' ); ?>
			</a>
			<a class="rank-math-tab" href="#" title="<?php esc_attr_e( 'Help', 'fast-indexing-api' ); ?>">
			<?php esc_html_e( 'Help', 'fast-indexing-api' ); ?>
			</a>
			<a class="rank-math-tab" href="#" title="<?php esc_attr_e( 'Setup Wizard', 'fast-indexing-api' ); ?>">
			<?php esc_html_e( 'Setup Wizard', 'fast-indexing-api' ); ?>
			</a>
			<a class="rank-math-tab" href="#" title="<?php esc_attr_e( 'Import &amp; Export', 'fast-indexing-api' ); ?>">
			<?php esc_html_e( 'Import &amp; Export', 'fast-indexing-api' ); ?>
			</a>
		</div>
		<div class="rank-math-ui module-listing dashboard-wrapper">
			<div class="grid">
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-404"></i>
					<header>
						<h3><?php esc_html_e( '404 Monitor', 'fast-indexing-api' ); ?></h3>
						<p><?php esc_html_e( 'Records the URLs on which visitors &amp; search engines run into 404 Errors. You can also turn on Redirections to redirect the error causing URLs to other URLs.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-404-monitor" name="modules[]" value="404-monitor">
							<label for="module-404-monitor" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-acf"></i>
					<header>
						<h3>
							<?php esc_html_e( 'ACF', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'ACF support helps Rank Math SEO read and analyze content written in the Advanced Custom Fields. If your theme uses ACF, you should enable this option.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-mobile"></i>
					<header>
						<h3>
							<?php esc_html_e( 'AMP', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Install an AMP plugin to make Rank Math work with Accelerated Mobile Pages. Rank Math automatically adds required meta tags in all the AMP pages.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box ">
					<i class="rm-icon rm-icon-search-console"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Analytics', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Connect Rank Math with Google Search Console to see the most important information from Google directly in your WordPress dashboard.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-analytics" name="modules[]" value="analytics">
							<label for="module-analytics" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-users"></i>
					<header>
						<h3>
							<?php esc_html_e( 'bbPress', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Add proper Meta tags to your bbPress forum posts, categories, profiles, etc. Get more options to take control of what search engines see and how they see it.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-comments"></i>
					<header>
						<h3>
							<?php esc_html_e( 'BuddyPress', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Enable the BuddyPress module for Rank Math SEO to make your BuddyPress forum SEO friendly by adding proper meta tags to all forum pages.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-images"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Image SEO', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Advanced Image SEO options to supercharge your website. Automate the task of adding the ALT and Title tags to your images on the fly.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-image-seo" name="modules[]" value="image-seo">
							<label for="module-image-seo" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-instant-indexing"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Instant Indexing', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Directly notify search engines(Bing) when pages are added, updated or removed.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-instant-indexing" name="modules[]" value="instant-indexing" checked="checked">
							<label for="module-instant-indexing" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box ">
					<i class="rm-icon rm-icon-link"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Link Counter', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Counts the total number of internal, external links, to and from links inside your posts. You can also see the same count in the Posts List Page.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-link-counter" name="modules[]" value="link-counter">
							<label for="module-link-counter" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-local-seo"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Local SEO &amp; Knowledge Graph', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Dominate the search results for the local audiences by optimizing your website for Local SEO and it also helps you to add code related to Knowledge Graph.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-local-seo" name="modules[]" value="local-seo">
							<label for="module-local-seo" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-redirection"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Redirections', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Redirect non-existent content easily with 301 and 302 status code. This can help improve your site ranking. Also supports many other response codes.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-redirections" name="modules[]" value="redirections">
							<label for="module-redirections" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box ">
					<i class="rm-icon rm-icon-schema"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Schema (Structured Data)', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Enable support for the structured data, which adds Schema code in your website, resulting in rich search results, better CTR and more traffic.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-rich-snippet" name="modules[]" value="rich-snippet">
							<label for="module-rich-snippet" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-role-manager"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Role Manager', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'The Role Manager allows you to use WordPress roles to control which of your site users can have edit or view access to Rank Math\'s settings.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-role-manager" name="modules[]" value="role-manager">
							<label for="module-role-manager" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box ">
					<i class="rm-icon rm-icon-analyzer"></i>
					<header>
						<h3>
							<?php esc_html_e( 'SEO Analysis', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Let Rank Math analyze your website and your website\'s content using 70+ different tests to provide tailor-made SEO Analysis to you.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary"><?php esc_html_e( 'Settings', 'fast-indexing-api' ); ?></a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-seo-analysis" name="modules[]" value="seo-analysis">
							<label for="module-seo-analysis" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box ">
					<i class="rm-icon rm-icon-sitemap"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Sitemap', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Enable Rank Math\'s sitemap feature, which helps search engines intelligently crawl your website\'s content. It also supports hreflang tag.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<a href="#" class="module-settings button button-secondary">Settings</a>
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-sitemap" name="modules[]" value="sitemap">
							<label for="module-sitemap" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-stories"></i>
					<header>
						<h3>
							<?php esc_html_e( 'Google Web Stories', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Make any Story created with the Web Stories WordPress plugin SEO-Ready with automatic support for Schema and Meta tags.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
				<div class="rank-math-box">
					<i class="rm-icon rm-icon-cart"></i>
					<header>
						<h3>
							<?php esc_html_e( 'WooCommerce', 'fast-indexing-api' ); ?>
						</h3>
						<p><?php esc_html_e( 'Optimize WooCommerce Pages for Search Engines by adding required metadata and Product Schema which will make your site stand out in the SERPs.', 'fast-indexing-api' ); ?></p>
					</header>
					<div class="status wp-clearfix">
						<span class="cmb2-toggle">
							<input type="checkbox" class="rank-math-modules" id="module-amp" name="modules[]" value="amp">
							<label for="module-amp" class="cmb2-slider ">
								<svg width="3" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6" class="toggle_on" role="img" aria-hidden="true" focusable="false">
									<path d="M0 0h2v6H0z"></path>
								</svg>
								<svg width="8" height="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" class="toggle_off" role="img" aria-hidden="true" focusable="false">
									<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
								</svg>
							</label>
							<span class="input-loading"></span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php
	if ( file_exists( WP_PLUGIN_DIR . '/seo-by-rank-math' ) ) {
		$text         = __( 'Activate Now', 'fast-indexing-api' );
		$pluginpath   = 'seo-by-rank-math/rank-math.php';
		$pluginlink   = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $pluginpath ), 'activate-plugin_' . $pluginpath );
		$button_class = 'activate-now';
	} else {
		$text         = __( 'Install for Free', 'fast-indexing-api' );
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
						<img src="<?php echo esc_url( RM_GIAPI_URL . 'assets/img/icon.svg' ); ?>" class="plugin-icon" alt="<?php esc_html_e( 'Rank Math SEO', '404-monitor' ); ?>">
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
								<span class="num-ratings" aria-hidden="true">(3,537)</span>
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