<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class IconLinksFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class IconLinksFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'IconLinks';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/icon_links/data',
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
            if ( isset( $row['link']['target'] ) && '_blank' === $row['link']['target'] ) {
                $layout['rows'][ $key ]['link']['icon'] = 'external';
                $layout['rows'][ $key ]['icon_classes'] = 'icon--medium is-inline-block';
            }
        }

        return $layout;
    }
}
