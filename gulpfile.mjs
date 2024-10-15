/*eslint camelcase: ["error", {properties: "never"}]*/

import { src, dest, watch, series } from 'gulp';
import wpPot from 'gulp-wp-pot';
import checktextdomain from 'gulp-checktextdomain';
import gulpSass from 'gulp-sass';
import autoprefixer from 'gulp-autoprefixer';
import del from 'del';
import { exec } from 'child_process';
import sass from 'sass';

// Set the Sass compiler
const sassCompiler = gulpSass(sass);

const paths = {
	admin: {
		src: 'assets/scss/**/*.scss',
		dest: 'assets/css',
	},
	pot: {
		src: [ '**/*.php', '!node_modules/**/*', '!vendor/**/*' ],
		dest: 'languages/fast-indexing-api.pot',
	},
	vendor: 'vendor/google/apiclient-services/src/**', // Path for cleanup
};

// Function to install Composer dependencies
function composerInstall(cb) {
	exec('composer install', (err, stdout, stderr) => {
		console.log(stdout);
		console.error(stderr);
		cb(err);
	});
}

// Function to clean up unnecessary files from Google API services
function cleanup() {
	return del([
		'vendor/google/apiclient-services/src/**',
		'!vendor/google/apiclient-services/src/Indexing',
		'!vendor/google/apiclient-services/src/Indexing/**',
		'!vendor/google/apiclient-services/src/Indexing.php',
	]);
}

// Function to prefix PHP classes using PHP-Scoper
function phpScoper(cb) {
	exec('php-scoper add-prefix -n', (err, stdout, stderr) => {
		console.log(stdout);
		console.error(stderr);
		cb(err);
	});
}

// Function to dump Composer autoload file with "vendor-dir" set to "vendor-prefixed"
function dumpAutoload(cb) {
	exec('COMPOSER_VENDOR_DIR=vendor-prefixed composer dump-autoload', (err, stdout, stderr) => {
		console.log(stdout);
		console.error(stderr);
		cb(err);
	} );
}

// Function to replace vendor-prefixed/google/apiclient/src/aliases.php with an empty file
function deleteAliases(cb) {
	exec('echo "<?php" > vendor-prefixed/google/apiclient/src/aliases.php', (err, stdout, stderr) => {
		console.log(stdout);
		console.error(stderr);
		cb(err);
	});
}

// Main task for Composer install, cleanup, and PHP-Scoper
function composeAndScope(cb) {
	series(composerInstall, cleanup, phpScoper, dumpAutoload, deleteAliases)(cb);
}

// SASS to CSS
function adminCSS() {
	return src(paths.admin.src, { sourcemaps: false })
		.pipe(
			sassCompiler({ outputStyle: 'compressed' }).on('error', sassCompiler.logError)
		)
		.pipe(autoprefixer())
		.pipe(dest(paths.admin.dest, { sourcemaps: '.' }));
}

// Watch files for changes
function watchFiles() {
	watch(paths.admin.src, adminCSS);
}

// POT file generation
function pot() {
	return src(paths.pot.src)
		.pipe(
			wpPot({
				domain: 'fast-indexing-api',
				lastTranslator: 'Rank Math',
				noFilePaths: true,
				team: 'Rank Math',
			})
		)
		.pipe(dest(paths.pot.dest));
}

// Check text domain
function ct() {
	return src(paths.pot.src).pipe(
		checktextdomain({
			text_domain: ['rank-math'],
			keywords: [
				'__:1,2d',
				'_e:1,2d',
				'_x:1,2c,3d',
				'_ex:1,2c,3d',
				'esc_html__:1,2d',
				'esc_html_e:1,2d',
				'esc_html_x:1,2c,3d',
				'esc_attr__:1,2d',
				'esc_attr_e:1,2d',
				'esc_attr_x:1,2c,3d',
				'_n:1,2,4d',
				'_nx:1,2,4c,5d',
				'_n_noop:1,2,3d',
				'_nx_noop:1,2,3c,4d',
			],
		})
	);
}

// Export all tasks
export { ct, pot, adminCSS, watchFiles as watch, composeAndScope };
