const resolve = require( 'path' ).resolve
const TerserPlugin = require( 'terser-webpack-plugin' )

const externals = {
	jquery: 'jQuery',
}

const alias = {
	'@root': resolve( __dirname, './assets/js/' )
}

const entryPoints = {
	plugin: {
		console: './assets/js/console.js',
		dashboard: './assets/js/dashboard.js'
	}
}

const paths = {
	plugin: './assets/js'
}

module.exports = function( env, arg ) {
	const mode =
		( env && env.environment ) ||
		process.env.NODE_ENV ||
		arg.mode ||
		'production'

	const what = arg.what || 'plugin'

	return {
		devtool:
			mode === 'development' ? 'cheap-module-eval-source-map' : false,
		entry: entryPoints[ what ],
		output: {
			path: resolve( __dirname, paths[ what ] ),
			filename: '[name].min.js',
		},
		resolve: {
			alias,
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					exclude: /(node_modules|bower_components)/,
					loader: 'babel-loader',
					options: {
						cacheDirectory: true,
						presets: [ '@babel/preset-env' ],
					},
				},
				{
					test: /.svg$/,
					use: [ { loader: 'svg-react-loader' } ],
				},
			],
		},
		externals,
		optimization: {
			minimize: true,
			minimizer: [ new TerserPlugin( {
				parallel: true,
				extractComments: false,
				terserOptions: {
					output: {
						comments: false,
					},
				},
			} ) ],
		},
	}
}
