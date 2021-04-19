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
            \Closure::fromCallable( [ $this, 'add_supported_functionality' ] )
        );

        \add_filter(
            'get_site_icon_url',
            \Closure::fromCallable( [ $this, 'favicon_url' ] ),
            10,
            3
        );

        \remove_theme_support( 'core-block-patterns' );
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
     * Returns favicon url. This overrides the theme settings and we prefer to use favicon
     * straight by hardcoding the file url.
     *
     * @return string
     */
    private function favicon_url() : string {
        return DPT_ASSETS_URI . '/images/favicon.png';
    }
}
