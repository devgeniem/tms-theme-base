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
        foreach ( $layout['rows'] as $row_key => $row ) {
            if ( $row['layout'] === 'is-text-first' ) {
                $layout['rows'][ $row_key ]['item_class']     = 'is-reversed-desktop is-justify-content-flex-end';
                $layout['rows'][ $row_key ]['text_col_class'] = 'is-offset-1-desktop';
            }
            else {
                $layout['rows'][ $row_key ]['img_col_class'] = 'is-offset-1-desktop';
            }
        }

        return $layout;
    }
}
