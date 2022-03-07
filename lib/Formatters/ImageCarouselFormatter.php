<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class ImageCarouselFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ImageCarouselFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'ImageCarousel';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/image_carousel/data',
            [ $this, 'format' ]
        );
        add_filter(
            'tms/acf/layout/image_carousel/data',
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
        if ( ! is_array( $data['rows'] ) || count( $data['rows'] ) < 4 ) {
            $data['rows'] = [];
            return $data;
        }

        $data['rows'] = array_map( static function ( $item ) {
            $item = \TMS\Theme\Base\Formatters\ImageFormatter::format( $item );

            $item       = apply_filters( 'tms/acf/block/image/data', $item );
            $item['id'] = wp_unique_id( 'image-carousel-item-' );

            unset( $item['__filter_attributes'], $item['wrapper_class'] );

            return $item;
        }, $data['rows'] );

        unset( $data['__filter_attributes'] );

        $data['carousel_id']  = wp_unique_id( 'image-carousel-' );
        $data['translations'] = ( new \Strings() )->s()['gallery'] ?? [];

        return $data;
    }
}
