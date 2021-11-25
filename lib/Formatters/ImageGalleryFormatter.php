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

        $row_count    = count( $data['rows'] );
        $column_class = 'gallery-columns-2 gallery-columns-3-tablet';
        $align_class  = 'is-align-full';

        if ( 1 === $row_count ) {
            $column_class = 'gallery-columns-1';
            $align_class  = '';
        }
        elseif ( 2 === $row_count ) {
            $column_class = 'gallery-columns-2';
            $align_class  = 'is-align-wide';
        }

        $data['column_class'] = $column_class;
        $data['align_class']  = $align_class;

        unset( $data['__filter_attributes'] );

        $data['gallery_id']   = wp_unique_id( 'image-gallery-' );
        $data['translations'] = ( new \Strings() )->s()['gallery'] ?? [];

        return $data;
    }
}
