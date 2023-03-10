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

        \add_filter(
            'wp_get_attachment_image_src',
            \Closure::fromCallable( [ $this, 'set_svg_dimensions' ] ),
            10,
            4
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

        \add_image_size( 'single', 1170, 520, 1 );
        \add_image_size( 'fullhd', 1920, 9999 );

        \add_image_size( 'medium_vertical', 9999, 320 );

        \remove_image_size( '1536x1536' );
        \remove_image_size( '2048x2048' );
    }

    /**
     * This filters out unnecessary image sizes.
     *
     * @return array The filtered sizes array.
     */
    private function filter_sizes() : array {
        return [
            'thumbnail',
            'medium',
            'medium_large',
            'large',
            'single',
            'fullhd',
        ];
    }

    /**
     * Get default image ID.
     *
     * @return string|null
     */
    public static function get_default_image_id() : ?string {
        return Settings::get_setting( 'default_image' );
    }

    /**
     * Get the dimensions for SVG images.
     *
     * @param array|false  $image         Array of image data.
     * @param int|string   $attachment_id Image attachment ID.
     * @param string|int[] $size          Requested image size.
     * @param boolean      $icon          Whether the image should be treated as an icon.
     *
     * @return array|false
     */
    private function set_svg_dimensions( $image, $attachment_id, $size, bool $icon  ) { // phpcs:ignore
        // Faulty image or has size, bail early
        if ( ! is_array( $image ) || $image[1] > 1 ) {
            return $image;
        }

        // Not SVG, bail early
        if ( ! preg_match( '/\.svg$/i', $image[0] ) ) {
            return $image;
        }

        // Size is already OK
        if ( is_array( $size ) ) {
            $image[1] = $size[0];
            $image[2] = $size[1];
        }

        else {
            // Get the file ignoring SSL
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ] );

            libxml_set_streams_context( $context );
            $xml = simplexml_load_file( $image[0] );

            // Set dimensions
            if ( $xml !== false ) {
                $attr     = $xml->attributes();
                $viewbox  = explode( ' ', $attr->viewBox ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
                $image[1] = isset( $attr->width ) && preg_match( '/\d+/', $attr->width, $value )
                    ? (int) $value[0]
                    : ( count( $viewbox ) === 4 ? (int) $viewbox[2] : null );
                $image[2] = isset( $attr->height ) && preg_match( '/\d+/', $attr->height, $value )
                    ? (int) $value[0]
                    : ( count( $viewbox ) === 4 ? (int) $viewbox[3] : null );
            }
            // Could not load the xml
            else {
                $image[1] = null;
                $image[2] = null;
            }
        }

        return $image;
    }
}
