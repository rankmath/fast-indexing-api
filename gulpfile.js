/*eslint camelcase: ["error", {properties: "never"}]*/

const { src, dest, watch, series } = require('gulp');
const wpPot = require('gulp-wp-pot');
const checktextdomain = require('gulp-checktextdomain');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const del = require('del');
const { exec } = require('child_process');

sass.compiler = require('node-sass');

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
	exec('php-scoper add-prefix', (err, stdout, stderr) => {
		console.log(stdout);
		console.error(stderr);
		cb(err);
	});
}

// Main task for Composer install, cleanup, and PHP-Scoper
function composeAndScope(cb) {
	series(composerInstall, cleanup, phpScoper)(cb);
}

// SASS to CSS
function adminCSS() {
	return src(paths.admin.src, { sourcemaps: false })
		.pipe(
			sass({ outputStyle: 'compressed' }).on('error', sass.logError)
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
exports.ct = ct;
exports.pot = pot;
exports.adminCSS = adminCSS;
exports.watch = watchFiles;
exports.composeAndScope = composeAndScope;
