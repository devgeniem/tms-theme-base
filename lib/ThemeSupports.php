<?php

namespace TMS\Theme\Base;

use Closure;
use PageContacts;
use PageEventsSearch;
use Search;
use function add_action;
use function add_filter;
use function add_theme_support;
use function remove_theme_support;

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
        add_action(
            'after_setup_theme',
            Closure::fromCallable( [ $this, 'add_supported_functionality' ] ),
            0
        );

        add_filter(
            'get_site_icon_url',
            Closure::fromCallable( [ $this, 'favicon_url' ] ),
            10,
            3
        );

        add_filter(
            'query_vars',
            Closure::fromCallable( [ $this, 'query_vars' ] )
        );

        remove_action( 'wp_scheduled_auto_draft_delete', 'wp_delete_auto_drafts' );

        remove_theme_support( 'core-block-patterns' );

        add_theme_support( 'disable-custom-colors' );

        add_theme_support( 'editor-color-palette' );

        add_theme_support( 'editor-font-sizes', [] );

        add_filter(
            'block_editor_settings_all',
            Closure::fromCallable( [ $this, 'disable_drop_cap' ] )
        );

        \add_action( 'wp_head', \Closure::fromCallable( [ $this, 'detect_js' ] ), 0 );

        \add_filter( 'tms/theme/settings/material_default_image', [ $this, 'get_material_default_image' ] );

        \add_action( 'wp_head', [ $this, 'add_meta_tags' ] );
    }

    /**
     * This adds all functionality.
     *
     * @return void
     */
    private function add_supported_functionality() : void {
        // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
        add_theme_support( 'title-tag' );

        // http://codex.wordpress.org/Post_Thumbnails
        add_theme_support( 'post-thumbnails' );

        // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
        add_theme_support(
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
     * @param array $editor_settings Editor settings.
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
        $settings = Settings::get_settings();
        $icon_id  = $settings['favicon'] ?? false;

        if ( ! empty( $icon_id ) ) {
            return \wp_get_attachment_url( $icon_id );
        }

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
        $vars[] = 'event-id';
        $vars[] = Search::SEARCH_CPT_QUERY_VAR;
        $vars[] = Search::SEARCH_START_DATE;
        $vars[] = Search::SEARCH_END_DATE;
        $vars[] = PageContacts::SEARCH_QUERY_VAR;
        $vars[] = PageEventsSearch::EVENT_SEARCH_TEXT;
        $vars[] = PageEventsSearch::EVENT_SEARCH_START_DATE;
        $vars[] = PageEventsSearch::EVENT_SEARCH_END_DATE;

        return $vars;
    }

    /**
     * Handles JavaScript detection
     *
     * Converts class `no-js` into `js` to the root `<html>` element when JavaScript is detected.
     */
    private function detect_js() {
        echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
    }

    /**
     * Get material default image
     *
     * @return mixed
     */
    public function get_material_default_image() {
        return Settings::get_setting( 'material_default_image' );
    }

    /**
     * Add meta tags
     *
     * @return void
     */
    public function add_meta_tags() : void {
        global $post;

        if ( ! $post || is_archive() || is_search() ) {
            return;
        }

        printf( '<meta name="pageID" content="%s" />', $post->ID ); // phpcs:ignore
    }
}

