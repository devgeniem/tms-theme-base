<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Images
 *
 * This class controls theme image handling.
 *
 * @package TMS\Theme\Base
 */
class Images implements Interfaces\Controller {

    /**
     * Update this version number if you need to update image sizes.
     *
     * @var integer
     */
    private const VERSION = '1';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'after_setup_theme',
            \Closure::fromCallable( [ $this, 'image_sizes' ] )
        );

        \add_filter(
            'intermediate_image_sizes',
            \Closure::fromCallable( [ $this, 'filter_sizes' ] )
        );
    }

    /**
     * Add and update image sizes for theme images.
     *
     * @return void
     */
    private function image_sizes() {
        $version_from_db = \get_option( 'images_version_number' );

        // Only update options if version is changed manually.
        // This prevents unnecessary database queries.
        if ( $version_from_db !== self::VERSION ) {
            \update_option( 'images_version_number', self::VERSION );

            \update_option( 'thumbnail_size_w', 150 );
            \update_option( 'thumbnail_size_h', 150 );
            \update_option( 'thumbnail_crop', 1 );

            \update_option( 'medium_size_w', 320 );
            \update_option( 'medium_size_h', 9999 );

            \update_option( 'medium_large_size_w', 768 );
            \update_option( 'medium_large_size_h', 9999 );

            \update_option( 'large_size_w', 1024 );
            \update_option( 'large_size_h', 9999 );
        }

        \add_image_size( 'single', 1170, 520, 1);
        \add_image_size( 'fullhd', 1920, 9999 );

        \add_image_size( 'medium_vertical', 9999, 320 );

        \remove_image_size( '1536x1536' );
        \remove_image_size( '2048x2048' );
    }

    /**
     * This filters out unnecessary image sizes.
     *
     * @param array $sizes The filterable sizes array.
     *
     * @return array The filtered sizes array.
     */
    private function filter_sizes( $sizes ) : array {
        return [
            'thumbnail',
            'medium',
            'medium_large',
            'large',
            'single',
            'fullhd',
        ];
    }
}
