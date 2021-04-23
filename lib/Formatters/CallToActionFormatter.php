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
class CallToActionFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'CallToAction';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'acf/layout/call_to_action/data',
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
    public static function format( array $layout ) : array {
        foreach ( $layout['rows'] as $row_key => $row ) {
            $layout['rows'][ $row_key ]['text_column_class'] = 'is-5-desktop';

            if ( $row['layout'] === 'is-text-first' ) {
                $layout['rows'][ $row_key ]['container_class']    = 'is-reversed-desktop';
                $layout['rows'][ $row_key ]['text_column_class'] .= ' is-offset-1-desktop';
            }
        }

        return $layout;
    }
}
