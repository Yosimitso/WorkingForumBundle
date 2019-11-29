const webpack = require("webpack");
const path = require("path");
const glob = require('glob');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require('copy-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

let config = {
    watch: (process.env.NODE_ENV !== 'production') ? true : false,
    entry: {
        app: './Resources/asset/js/webpack-app.js',
        forum: './Resources/asset/js/webpack-forum.js',
        thread: "./Resources/asset/js/webpack-thread.js",
        post: './Resources/asset/js/webpack-post.js',
        theme_green: './Resources/asset/scss/theme_green.scss',
        theme_dark_blue: './Resources/asset/scss/theme_dark_blue.scss'
    },
    output: {
        path: path.resolve(__dirname, "./Resources/public"),
        filename: "[name].min.js"
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.(scss|css)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            name: 'css/[name].[ext]'
                        }
                    },
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(ttf|woff|eot)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: 'font/',
                            publicPath: '../font'
                        },
                    }
                ]
            },
            {
                test: /\.(svg|png)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: 'images/[name].[ext]',
                            outputPath: 'images/',
                            publicPath: '../images'
                        },
                    }
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
          filename: 'css/[name].min.css'
        }),
        new CopyPlugin([
            { from: './Resources/asset/images', to: './images/' }
        ])
    ]
}

module.exports = config;

if (process.env.NODE_ENV === 'production') {
    const OptimizeCSSAssets = require("optimize-css-assets-webpack-plugin");
    module.exports.plugins.push(
        new OptimizeCSSAssets(),
        new UglifyJsPlugin()
    );
}
