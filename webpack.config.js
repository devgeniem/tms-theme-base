const path = require( 'path' );
const webpack = require( 'webpack' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const CleanWebpackPlugin = require( 'clean-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const SpriteLoaderPlugin = require( 'svg-sprite-loader/plugin' );
const TerserPlugin = require( 'terser-webpack-plugin' );

// Check for production mode.
const isProduction = process.env.NODE_ENV === 'production';

// Project root folder.
const wpProjectPath = `${path.resolve( __dirname, '..', '..', '..', '..' )}`;

 // Local development server URL.
const wpProjectUrl = 'https://client-tms.test';

// Theme paths.
const themeName = path.basename( __dirname );
const themePath = `/web/app/themes/${themeName}`;
const themePublicPath = `/app/themes/${themeName}/assets/dist/`;
const themeFullPath = `${wpProjectPath}${themePath}`;
const themeEntry = `${themeFullPath}/assets/scripts/main.js`;
const themeAdminEntry = `${themeFullPath}/assets/scripts/admin.js`;
const themeOutput = `${themeFullPath}/assets/dist`;

// All loaders to use on assets.
const allModules = {
    rules: [
        {
            enforce: 'pre',
            test: /\.js$/,
            exclude: /node_modules/,
            use: {
                loader: 'eslint-loader',
                options: {
                    configFile: `${wpProjectPath}/.eslintrc.json`,
                    fix: false,
                    failOnWarning: false,
                    failonError: true
                }
            }
        },
        {
            test: /\.js$/,
            exclude: /node_modules/,
            use: {
                loader: 'babel-loader',
                options: {

                    // Do not use the .babelrc configuration file.
                    babelrc: false,

                    // The loader will cache the results of the loader in node_modules/.cache/babel-loader.
                    cacheDirectory: true,

                    // Enable latest JavaScript features.
                    presets: [ '@babel/preset-env' ],

                    // Enable dynamic imports.
                    plugins: [ '@babel/plugin-syntax-dynamic-import' ]
                }
            }
        },
        {
            test: /\.scss$/,
            use: [
                MiniCssExtractPlugin.loader,
                {
                    loader: 'css-loader',
                    options: {
                        sourceMap: true
                    }
                },
                {
                    loader: 'postcss-loader',
                    options: {
                        sourceMap: true
                    }
                },
                {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: true
                    }
                }
            ]
        },
        {
            test: /\.(gif|jpe?g|png|svg)(\?[a-z0-9=\.]+)?$/,
            exclude: [ /assets\/fonts/, /assets\/icons/, /node_modules/ ],
            use: [
                'file-loader?name=[name].[ext]',
                {
                    loader: 'image-webpack-loader',
                    options: {

                        // Disable imagemin for development build.
                        disable: ! isProduction,
                        mozjpeg: {
                            quality: 70
                        },
                        optipng: {
                            enabled: false
                        },
                        pngquant: {
                            quality: [ 0.7, 0.7 ]
                        },
                        gifsicle: {
                            interlaced: false
                        }
                    }
                }
            ]
        },
        {
            test: /\.(eot|svg|ttf|woff(2)?)(\?[a-z0-9=\.]+)?$/,
            exclude: [ /assets\/images/, /assets\/icons/, /node_modules/ ],
            use: 'file-loader?name=[name].[ext]'
        },
        {
            test: /assets\/icons\/.*\.svg(\?[a-z0-9=\.]+)?$/,
            use: [
                {
                    loader: 'svg-sprite-loader',
                    options: {
                        symbolId: 'icon-[name]',
                        extract: true,
                        spriteFilename: 'icons.svg'
                    }
                },
                {
                    loader: 'svgo-loader',
                    options: {
                        plugins: [
                            { removeTitle: true },
                            { removeAttrs: { attrs: [ 'path:fill', 'path:class' ] } }
                        ]
                    }
                }
            ]
        }
    ]
};

// All optimizations to use.
const allOptimizations = {
    runtimeChunk: false,
    splitChunks: {
        cacheGroups: {
            vendor: {
                test: /[\\/]node_modules[\\/]/,
                name: 'vendor',
                chunks: 'all'
            }
        }
    }
};

// All plugins to use.
const allPlugins = [

    // Use BrowserSync.
    new BrowserSyncPlugin({
        host: 'localhost',
        port: 3000,
        proxy: wpProjectUrl,
        files: [ {
            match: [
                '**/*.php',
                '**/*.dust'
            ]
        } ],
        notify: true,
        open: false
    },
    {
        reload: true
    }),

    // Convert JS to CSS.
    new MiniCssExtractPlugin({
        filename: '[name].css'
    }),

    // Create hidden SVG sprite with inline style.
    new SpriteLoaderPlugin({
        plainSprite: true,
        spriteAttrs: {
            style: 'display: none;'
        }
    }),

    // Provide jQuery instance for all modules.
    new webpack.ProvidePlugin({
        jQuery: 'jquery'
    })
];

// Use only for production build.
if ( isProduction ) {
    allOptimizations.minimizer = [

        // Optimize for production build.
        new TerserPlugin({
            cache: true,
            parallel: true,
            sourceMap: true,
            terserOptions: {
                output: {
                    comments: false
                },
                compress: {
                    warnings: false,
                    drop_console: true // eslint-disable-line camelcase
                }
            }
        })
    ];

    // Delete distribution folder for production build.
    allPlugins.push( new CleanWebpackPlugin() );
}

module.exports = [
    {
        entry: {
            main: [ themeEntry ],
            admin: [ themeAdminEntry ]
        },

        output: {
            path: themeOutput,
            publicPath: themePublicPath,
            filename: '[name].js'
        },

        module: allModules,

        optimization: allOptimizations,

        plugins: allPlugins,

        externals: {

            // Set jQuery to be an external resource.
            jquery: 'jQuery'
        },

        // Disable source maps for production build.
        devtool: isProduction ? '' : 'inline-source-map'
    }
];
