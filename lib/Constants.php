<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Constants
 *
 * This class controls all theme constants.
 *
 * @package TMS\Theme\Base
 */
class Constants implements Interfaces\Controller {

    /**
     * Constructor
     */
    public function __construct() {
        $this->define();
    }

    /**
     * Define theme constants.
     */
    private function define() {
        // Define the stylesheet path.
        if ( ! defined( 'DPT_STYLESHEET_DIR' ) ) {
            define( 'DPT_STYLESHEET_DIR', \get_stylesheet_directory() );
        }

        // Define the asset path.
        if ( ! defined( 'DPT_ASSET_CACHE_URI' ) ) {
            define( 'DPT_ASSET_CACHE_URI', DPT_STYLESHEET_DIR . '/assets/dist' );
        }

        // Returns /app/themes/{current theme} -- no need to reference other domains here.
        $themes_root = wp_parse_url( \get_template_directory_uri(), PHP_URL_PATH );

        // Define the assets path.
        if ( ! defined( 'DPT_ASSETS_URI' ) ) {
            define( 'DPT_ASSETS_URI', $themes_root . '/assets' );
        }

        // Define the assets dist path.
        if ( ! defined( 'DPT_ASSET_URI' ) ) {
            define( 'DPT_ASSET_URI', $themes_root . '/assets/dist' );
        }

        // Set Polylang active state. Use this to check if Polylang plugin is active.
        define( 'DPT_PLL_ACTIVE', function_exists( 'pll_languages_list' ) );

        // Set Advanced Custom Fields active state. Use this to check if Advanced Custom Fields plugin is active.
        define( 'DPT_ACF_ACTIVE', class_exists( 'acf' ) );

        // Get the theme version number from the empty theme stylesheet
        if ( ! defined( 'DPT_THEME_VERSION' ) ) {
            $version = wp_get_theme()->get( 'Version' );
            define( 'DPT_THEME_VERSION', $version );
        }

        // Define whether lazyloading is on.
        if ( ! defined( 'DPT_LAZYLOADING' ) ) {
            define( 'DPT_LAZYLOADING', true );
        }

        // Set DustPress Comments active state. Use this to check if DustPress Comments plugin is active.
        if ( function_exists( 'DustPress\init_comments_helper' ) ) {
            define( 'DPT_COMMENTS_ACTIVE', true );
        }
        else {
            define( 'DPT_COMMENTS_ACTIVE', false );
        }
    }

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {}
}
