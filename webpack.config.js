const webpack = require("webpack");
const path = require("path");
const glob = require('glob');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require('copy-webpack-plugin');

let config = {
    entry: {
        thread: "./Resources/asset/js/webpack-thread.js",
        forum: './Resources/asset/js/webpack-forum.js',
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
                test: /\.(scss|css)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            name: './css/[name].[ext]'
                        }
                    },
                    'css-loader',
                    'sass-loader'
                ]
            },
            // {
            //     test: /\.ttf$/,
            //     use: [
            //         {
            //             loader: 'ttf-loader',
            //             options: {
            //                 name: './font/[name].[ext]',
            //             },
            //         }
            //         ]
            // },
            {
                test: /\.(ttf|woff|eot)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: './font/[name].[ext]',
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
                        },
                    }
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
          filename: 'css/[name].css'  
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
        // new webpack.optimize.UglifyJsPlugin(),
        new OptimizeCSSAssets()
    );
}