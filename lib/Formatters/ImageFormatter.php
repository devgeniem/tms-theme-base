<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class ImageFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ImageFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Image';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/image/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format block data
     *
     * @param array $data ACF Block data.
     *
     * @return array
     */
    public static function format( array $data ) : array {
        $block = $data['__filter_attributes']['block'] ?? [];
        unset( $data['__filter_attributes'] );

        $image                  = (array) ( $data['image'] ?? [] );
        $image_id               = (int) ( $image['id'] ?? 0 );
        $is_clickable           = (bool) ( $data['is_clickable'] ?? false );
        $data['author_name']    = null;
        $data['image_url_orig'] = null;

        if ( $image_id > 0 ) {
            $data['image_url_orig'] = $is_clickable ? ( $image['url'] ?? null ) : null;
        }

        $data = self::get_image_artist( $data, (array) ( $data['image'] ?? null ) );

        if ( $block['supports']['align'] ?? false ) {
            $data['align'] = $block['align'] ?? '';
        }

        $data['image_caption'] = wp_strip_all_tags( $data['caption'] ?? '', true );

        if ( ! empty( $data['author_name'] ) ) {
            $data['image_caption'] .= ' (' . $data['author_name'] . ')';
        }

        $data['wrapper_class'] = 'is-align-wide mt-6 mb-6';

        return $data;
    }

    /**
     * Builds all necessary fields for artist name display.
     *
     * @param array $row   Repeater row, or similar.
     * @param array $image Image to get details from.
     *
     * @return array
     */
    public static function get_image_artist( array $row, array $image ) : array {
        $row['author_name'] = null;
        $row['artist_name'] = null;
        // If we have title and artist filled, we'll fill this with the data.
        $row['image_title_and_artist'] = null;

        if ( empty( $image ) ) {
            return $row;
        }

        $image_id = $image['id'] ?? false;

        $row['author_name'] = trim( get_field( 'author_name', $image_id ) );
        $row['artist_name'] = trim( get_field( 'artist_name', $image_id ) );
        $row['image_title'] = trim( $image['title'] ?? '' );

        if ( ! empty( $row['artist_name'] ) && ! empty( $row['image_title'] ) ) {
            $row['image_title_and_artist'] = sprintf(
                '%s: %s',
                $row['artist_name'],
                $row['image_title']
            );
        }

        return $row;
    }
}
