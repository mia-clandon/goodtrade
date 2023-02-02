// noinspection JSUnresolvedVariable,JSValidateTypes,EqualityComparisonWithCoercionJS,JSIncompatibleTypesComparison,JSUnresolvedFunction

"use strict";
/**
 * @author Артём Широких kowapssupport@gmail.com
 * @type {webpack}
 */

let webpack = require('webpack')
    , NODE_ENV = process.env.NODE_ENV || 'development'
    , lib_path = __dirname + '/frontend/app/libs/'
    , vendor_path = __dirname + '/vendor/bower/';

let config = {

    // директория со скриптами приложения.
    context: __dirname + '/frontend/app',

    // точки входа в приложение.
    entry: {
        "frontend_site_index": "./pages/site/index",
        "frontend_site_join": "./pages/site/join",
        "frontend_site_logged": "./pages/site/logged",
        "frontend_firm_show": "./pages/firm/show",
        "frontend_product_show": "./pages/product/show",
        "frontend_category_show": "./pages/category/show",
        "frontend_compare_index": './pages/compare/index',
        "frontend_search_index": './pages/search/index',
        "frontend_favorite_index": './pages/favorite/index',
        "frontend_page_show": './pages/page/show',
        "cabinet_company_index": "./pages/cabinet/company/index",
        "cabinet_product_index": "./pages/cabinet/product/index",
        "cabinet_product_add": "./pages/cabinet/product/add",
        "cabinet_product_add_price": "./pages/cabinet/product/add_price",
        "cabinet_profile_index": "./pages/cabinet/profile/index",
        "commercial_response": "./pages/commercial/response",
        "common": "./pages/common",
    },

    // выходные файлы.
    output: {
        path: __dirname + '/frontend/web/app/pages',
        filename: "[name]_bundle.js",
        library: "[name]"
    },

    // watch: true,
    //
    // watchOptions: {
    //     aggregateTimeout: 300,
    // },

    devtool: NODE_ENV == 'production' ? 'cheap-module-source-map' : false,

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/, /libs/],
                use: [{
                    loader: 'babel-loader',
                    options: { presets: ['es2015'] },
                }],
            },
        ],
    },

    // плагины.
    plugins: [
        new webpack.DefinePlugin({
            NODE_ENV: JSON.stringify(NODE_ENV),
        }),
    ]
};

if (NODE_ENV == 'production') {
    config.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            mangle: {
                except: ['$super', '$', 'exports', 'require']
            },
            output: {
                comments: false
            }
        })
    );
}

module.exports = config;