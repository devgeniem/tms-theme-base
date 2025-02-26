<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Controller;
use TMS\Theme\Base\Settings;

/**
 * Class Cookiebot
 *
 * @package TMS\Theme\Base
 */
class Cookiebot implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'script_loader_tag',
            \Closure::fromCallable( [ $this, 'add_data_attribute' ] ),
        10, 2 );

        add_filter( 'the_seo_framework_sitemap_additional_urls',
            \Closure::fromCallable( [ $this, 'add_cb_urls_to_sitemap' ] ),
        10, 2 );

    }

    /**
     * Add data attribute data-cookieconsent="ignore" to
     * ignore script by cookiebot.
     * This prevents Cookiebot to block the scripts.
     *
     * @param string $tag Script tag.
     * @param string $handle Script handle name.
     * @return string The script tag.
     */
    private function add_data_attribute( $tag, $handle ) {
        $scripts_to_ignore_by_cookiebot = [
            'jquery-core',
            'dustpress',
            'jquery.jsonview',
            'hoverintent-js',
            'admin-bar',
            'dustpress_debugger',
            'tms-plugin-materials-public-js',
            'vendor-js',
            'theme-js',
            'ina-logout-js',
            'wp-dom-ready',
            'wp-hooks',
            'wp-i18n',
            'wp-a11y',
        ];
        if ( ! in_array( $handle, $scripts_to_ignore_by_cookiebot, true ) ) {
            return $tag;
        }

        $tag = str_replace( '>', ' data-cookieconsent="ignore">', $tag );
        return $tag;
    }


    /**
     * Add custom URLs to sitemap for Cookiebot's scanner
     *
     * @param string $custom_urls URL array to add.
     * @return array Custom urls array for Cookiebot scanner.
     */
    private function add_cb_urls_to_sitemap( $custom_urls = [] ) {

        $columns = Settings::get_setting( 'sitemap_links' );

        if ( empty( $columns ) ) {
            return null;
        }

        foreach ( $columns as $col ) {
            if ( ! empty( $col['sitemap_link']['url'] ) ) {
                $custom_urls [] = $col['sitemap_link']['url'];
            }
        }

        return $custom_urls;
    }


}
