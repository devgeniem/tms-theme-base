<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class AccessibilityIconLinksFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class AccessibilityIconLinksFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'AccessibilityIconLinks';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/acc_icon_links/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/acc_icon_links/data',
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
        if ( empty( $layout['rows'] ) ) {
            return $layout;
        }

        foreach ( $layout['rows'] as $key => $row ) {
            if ( ! empty( $layout['rows'][ $key ]['link']['icon'] ) ) {
                $layout['rows'][ $key ]['link']['icon'] = '';
            }
            if ( isset( $row['link']['target'] ) && '_blank' === $row['link']['target'] ) {
                $layout['rows'][ $key ]['link']['icon'] = 'external';
                $layout['rows'][ $key ]['icon_classes'] = 'icon--medium is-inline-block';
            }
        }

        return $layout;
    }
}
