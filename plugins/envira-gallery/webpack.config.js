const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')

module.exports = {
    devtool: 'source-map',
    name:  'envira',
    entry: './assets/js/envira.js',
    output: {
        path: __dirname + "/assets/js/min/",
        filename: 'envira-min.js'
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /\.scss$/,
                exclude: /(node_modules|bower_components)/,
                use: ExtractTextPlugin.extract({
                    use: [{
                        loader: 'css-loader',
                        options: {sourceMap: true}
                    }, {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: true,
                            plugins: () => ([
                                require('autoprefixer')({
                                    browsers: ['last 2 versions', 'ie > 8'],
                                }),
                            ])
                        }
                    }, {
                        loader: 'sass-loader',
                        options: {sourceMap: true}
                    }]
                })
            }
        ]
    },
    externals: {
		jquery: 'jQuery'
	},
    plugins: [
        new webpack.ProvidePlugin({
           $: "jquery",
            jQuery: "jquery"
		}),
		new UglifyJsPlugin( { 
			uglifyOptions: {
				ie8: false,
				mangle: false,
				compress: false,
				warnings: false
      		} 
      })
    ]
};