<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Settings;

/**
 * Class MapFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class MapFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Map';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/map/data',
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
        $placeholder = Settings::get_setting( 'map_placeholder' );

        $layout['id']              = wp_unique_id( 'map-' );
        $layout['map_button_text'] = Settings::get_setting( 'map_button_text' );
        $layout['placeholder']     = $placeholder ?: false;

        preg_match( '/src="([^"]+)"/', $layout['embed'], $match );

        $layout['embed_url'] = ! empty( $match[1] )
            ? $match[1]
            : false;

        return $layout;
    }
}
