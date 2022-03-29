<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Assets;

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

        // get icons list in order to give a title for
        // items missing title
        $icons = Assets::get_accessibility_icons();

        // maybe add link icon and item title to row items
        foreach ( $layout['rows'] as $key => $row ) {
            if ( ! empty( $layout['rows'][ $key ]['link'] ) ) {
                $layout['rows'][ $key ]['link']['icon'] = 'chevron-right';
            }

            $title = $layout['rows'][ $key ]['title'];
            $icon  = $layout['rows'][ $key ]['acc_icon'];

            if ( empty( $title ) && ! empty( $icon ) ) {
                $layout['rows'][ $key ]['title'] = ! empty( $icons[ $icon ] ) ? $icons[ $icon ] : '';
            }
        }

        return $layout;
    }
}
