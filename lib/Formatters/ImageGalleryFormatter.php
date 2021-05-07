<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class ImageGalleryFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ImageGalleryFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'ImageGallery';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/image_gallery/data',
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
    public function format( array $data ) : array {
        $data['rows'] = array_map( static function ( $item ) {
            $item = \TMS\Theme\Base\Formatters\ImageFormatter::format( $item );

            $item       = apply_filters( 'tms/acf/block/image/data', $item );
            $item['id'] = wp_unique_id( 'image-gallery-item-' );

            unset( $item['__filter_attributes'] );

            return $item;
        }, $data['rows'] );

        unset( $data['__filter_attributes'] );

        $data['gallery_id']   = wp_unique_id( 'image-gallery-' );
        $data['translations'] = ( new \Strings() )->s()['gallery'] ?? [];

        return $data;
    }
}
