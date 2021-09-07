/*eslint camelcase: ["error", {properties: "never"}]*/

const { src, dest, watch } = require( 'gulp' )
const sass = require( 'gulp-sass' )(require('sass'))
const autoprefixer = require( 'gulp-autoprefixer' )

sass.compiler = require( 'node-sass' )

const paths = {
	admin: {
		src: 'assets/scss/**/*.scss',
		dest: 'assets/css',
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

exports.adminCSS = adminCSS
exports.watch = watchFiles
