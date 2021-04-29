<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Assets
 *
 * @package TMS\Theme\Base
 */
class Assets implements Interfaces\Controller {

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'wp_enqueue_scripts',
            \Closure::fromCallable( [ $this, 'enqueue_assets' ] ),
            100
        );

        \add_action(
            'admin_enqueue_scripts',
            \Closure::fromCallable( [ $this, 'admin_assets' ] ),
            100
        );

        \add_action(
            'wp_default_scripts',
            \Closure::fromCallable( [ $this, 'disable_jquery_migrate' ] )
        );

        \add_action(
            'wp_footer',
            \Closure::fromCallable( [ $this, 'include_svg_icons' ] )
        );

        \add_action(
            'enqueue_block_editor_assets',
            \Closure::fromCallable( [ $this, 'editor' ] )
        );

        \add_action(
            'admin_init',
            \Closure::fromCallable( [ $this, 'add_editor_styles' ] )
        );
    }

    /**
     * This adds custom styling to ACF Wysiwygs. Remove if nor needed.
     *
     * @return void
     */
    private function add_editor_styles() : void {
        \add_editor_style( 'custom-editor-styles.css' );
    }

    /**
     * Theme assets. These have automatic cache busting.
     */
    private function enqueue_assets() : void {
        $main_css_mod_time  = static::get_theme_asset_mod_time( 'main.css' );
        $main_js_mod_time   = static::get_theme_asset_mod_time( 'main.js' );
        $vendor_js_mod_time = static::get_theme_asset_mod_time( 'vendor.js' );

        \wp_enqueue_style(
            'theme-css',
            DPT_ASSET_URI . '/main.css',
            [],
            $main_css_mod_time,
            'all'
        );

        \wp_enqueue_script(
            'vendor-js',
            DPT_ASSET_URI . '/vendor.js',
            [ 'jquery' ],
            $vendor_js_mod_time,
            true
        );

        \wp_enqueue_script(
            'theme-js',
            DPT_ASSET_URI . '/main.js',
            [ 'jquery', 'vendor-js' ],
            $main_js_mod_time,
            true
        );

        \wp_dequeue_style( 'wp-block-library' );

        \wp_enqueue_style(
            'fontawesome',
            'https://pro.fontawesome.com/releases/v5.13.0/css/all.css',
            [],
            '5.13.0',
            false
        );
    }

    /**
     * This adds assets (JS and CSS) to gutenberg in admin.
     *
     * @return void
     */
    private function editor() : void {
        $css_mod_time = static::get_theme_asset_mod_time( 'editor.css' );
        $js_mod_time  = static::get_theme_asset_mod_time( 'editor.js' );

        if ( file_exists( DPT_ASSET_CACHE_URI . '/editor.js' ) ) {
            \wp_enqueue_script(
                'editor-js',
                DPT_ASSET_URI . '/editor.js',
                [
                    'wp-i18n',
                    'wp-blocks',
                    'wp-dom-ready',
                    'wp-edit-post',
                ],
                $js_mod_time,
                true
            );
        }

        if ( file_exists( DPT_ASSET_CACHE_URI . '/editor.css' ) ) {
            \wp_enqueue_style(
                'editor-css',
                DPT_ASSET_URI . '/editor.css',
                [],
                $css_mod_time,
                'all'
            );
        }
    }

    /**
     * Admin assets.
     */
    private function admin_assets() : void {
        $css_mod_time = static::get_theme_asset_mod_time( 'admin.css' );
        $js_mod_time  = static::get_theme_asset_mod_time( 'admin.js' );

        \wp_enqueue_script(
            'admin-js',
            DPT_ASSET_URI . '/admin.js',
            [
                'jquery',
                'wp-data',
                'wp-core-data',
                'wp-editor',
            ],
            $js_mod_time,
            true
        );

        \wp_enqueue_style(
            'admin-css',
            DPT_ASSET_URI . '/admin.css',
            [],
            $css_mod_time,
            'all'
        );
    }

    /**
     * This function disables jQuery Migrate.
     *
     * @param \WP_Scripts $scripts The scripts object.
     *
     * @return void
     */
    private function disable_jquery_migrate( $scripts ) : void {
        if ( ! empty( $scripts->registered['jquery'] ) ) {
            $scripts->registered['jquery']->deps = array_diff(
                $scripts->registered['jquery']->deps,
                [ 'jquery-migrate' ]
            );
        }
    }

    /**
     * Add SVG definitions to footer.
     */
    private function include_svg_icons() : void {
        $svg_icons_path = \get_template_directory() . '/assets/dist/icons.svg';

        if ( file_exists( $svg_icons_path ) ) {
            include_once $svg_icons_path;
        }
    }

    /**
     * This enables cache busting for theme CSS and JS files by
     * returning a microtime timestamp for the given files.
     * If the file is not found for some reason, it uses the theme version.
     *
     * @param string $filename The file to check.
     *
     * @return int|string A microtime amount or the theme version.
     */
    private static function get_theme_asset_mod_time( $filename = '' ) {
        return file_exists( DPT_ASSET_CACHE_URI . '/' . $filename )
            ? filemtime( DPT_ASSET_CACHE_URI . '/' . $filename )
            : DPT_THEME_VERSION;
    }
}
