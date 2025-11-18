/*eslint camelcase: ["error", {properties: "never"}]*/

const { src, dest, watch } = require( 'gulp' )
const wpPot = require( 'gulp-wp-pot' )
const checktextdomain = require( 'gulp-checktextdomain' )
const sass = require( 'gulp-sass' )( require( 'sass' ) )
const autoprefixer = require( 'gulp-autoprefixer' )
const rename = require( 'gulp-rename' )

const paths = {
	admin: {
		src: 'assets/scss/**/*.scss',
		dest: 'assets/css',
	},
	pot: {
		src: [ '**/*.php', '!node_modules/**/*', '!vendor/**/*' ],
		dest: 'languages/fast-indexing-api.pot',
	},
}

/**
 * Converting Admin SASS into CSS
 *  1. Applying autoprefixer
 *  2. Creatings sourcemaps
 *
 * @return {Object} Gulp source.
 */
function adminCSS() {
	return src( paths.admin.src, { sourcemaps: false } )
		.pipe(
			sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError )
		)
		.pipe( autoprefixer() )
		.pipe( dest( paths.admin.dest, { sourcemaps: '.' } ) )
}

function watchFiles() {
	watch( paths.admin.src, adminCSS )
}

function pot() {
	return src( paths.pot.src )
		.pipe(
			wpPot( {
				domain: 'fast-indexing-api',
				lastTranslator: 'Rank Math',
				noFilePaths: true,
				team: 'Rank Math',
			} )
		)
		.pipe( dest( paths.pot.dest ) )
}

// Quality Assurance --------------------------------------
function ct() {
	return src( paths.pot.src ).pipe(
		checktextdomain( {
			text_domain: [ 'rank-math' ],
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
		} )
	)
}

exports.ct = ct
exports.pot = pot
exports.adminCSS = adminCSS
exports.watch = watchFiles
