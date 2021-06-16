<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class ThemeSupports
 *
 * @package TMS\Theme\Base
 */
class ThemeSupports implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'after_setup_theme',
            \Closure::fromCallable( [ $this, 'add_supported_functionality' ] ),
            0
        );

        \add_filter(
            'get_site_icon_url',
            \Closure::fromCallable( [ $this, 'favicon_url' ] ),
            10,
            3
        );

        \add_filter(
            'query_vars',
            \Closure::fromCallable( [ $this, 'query_vars' ] )
        );

        \remove_theme_support( 'core-block-patterns' );

        \add_theme_support( 'disable-custom-colors' );

        \add_theme_support( 'editor-color-palette' );

        \add_theme_support( 'editor-font-sizes', [] );

        \add_filter(
            'block_editor_settings',
            \Closure::fromCallable( [ $this, 'disable_drop_cap' ] )
        );
    }

    /**
     * This adds all functionality.
     *
     * @return void
     */
    private function add_supported_functionality() : void {
        // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
        \add_theme_support( 'title-tag' );

        // http://codex.wordpress.org/Post_Thumbnails
        \add_theme_support( 'post-thumbnails' );

        // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
        \add_theme_support(
            'html5',
            [
                'caption',
                'comment-form',
                'comment-list',
                'gallery',
                'search-form',
            ]
        );
    }

    /**
     * Disable drop cap
     *
     * @param array $editor_settings Editor settings
     *
     * @return array
     */
    protected function disable_drop_cap( array $editor_settings ) : array {
        $editor_settings['__experimentalFeatures']['defaults']['typography']['dropCap'] = false;

        return $editor_settings;
    }

    /**
     * Returns favicon url. This overrides the theme settings and we prefer to use favicon
     * straight by hardcoding the file url.
     *
     * @return string
     */
    private function favicon_url() : string {
        return DPT_ASSETS_URI . '/images/favicon.png';
    }

    /**
     * Append custom query vars
     *
     * @param array $vars Registered query vars.
     *
     * @return array
     */
    protected function query_vars( $vars ) {
        $vars[] = 'filter-category';
        $vars[] = 'filter-tag';
        $vars[] = 'filter-month';
        $vars[] = 'filter-year';

        return $vars;
    }
}
