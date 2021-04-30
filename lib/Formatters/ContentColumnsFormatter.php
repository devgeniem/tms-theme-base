<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class CallToActionFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ContentColumnsFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'ContentColumns';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/content_columns/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function format( array $layout ) : array {
        $aspect_ratios = [
            '50-50' => [
                'is-5-desktop',
                'is-5-desktop',
            ],
            '70-30' => [
                'is-7-desktop',
                'is-3-desktop',
            ],
            '30-70' => [
                'is-3-desktop',
                'is-7-desktop',
            ],
        ];

        if ( empty( $layout['rows'] ) || ! is_array( $layout['rows'] ) ) {
            return $layout;
        }

        $layout['rows'] = array_map( function ( $item ) use ( $aspect_ratios ) {
            $item['text_col_class'] = [ 'is-6' ];
            $item['img_col_class']  = [ 'is-6' ];
            $ratio                  = $item['aspect_ratio'];

            if ( array_key_exists( $ratio, $aspect_ratios ) ) {
                $item['text_col_class'][] = $aspect_ratios[ $ratio ][0];
                $item['img_col_class'][]  = $aspect_ratios[ $ratio ][1];
            }

            if ( $item['layout'] === 'is-text-first' ) {
                $item['item_class']       = 'is-reversed-tablet is-justify-content-flex-end';
                $item['text_col_class'][] = 'is-offset-1-desktop';
            }
            else {
                $item['img_col_class'][] = 'is-offset-1-desktop';
            }

            $item['text_col_class'] = implode( ' ', $item['text_col_class'] );
            $item['img_col_class']  = implode( ' ', $item['img_col_class'] );

            return $item;
        }, $layout['rows'] );

        return $layout;
    }
}
