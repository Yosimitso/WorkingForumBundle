const webpack = require("webpack");
const path = require("path");
const glob = require('glob');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require('copy-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

let config = {
    watch: (process.env.NODE_ENV !== 'production'),
    entry: {
        app: './Resources/asset/js/webpack-app.js',
        forum: './Resources/asset/js/webpack-forum.js',
        thread: "./Resources/asset/js/webpack-thread.js",
        post: './Resources/asset/js/webpack-post.js',
        admin_rules: './Resources/asset/js/webpack-admin-rules.js',
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
                        loader: MiniCssExtractPlugin.loader
                    },
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
          filename: 'css/[name].min.css'
        }),
        new CopyPlugin({
            patterns: [
                { from: './Resources/asset/images', to: './images/' },
                { from: './Resources/asset/fonts', to: './fonts/' }
            ]
        })
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
