<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Traits\Components;

/**
 * Class AccordionImageFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class AccordionImageFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    use Components;

    /**
     * Define formatter name
     */
    const NAME = 'AccordionImage';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/accordion_image/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $data ACF Layout data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        if ( empty( $data['image'] ) ) {
            return $data;
        }

        // Set the image caption
        $caption = ! empty( $data['caption'] ) ? $data['caption'] : null;

        if ( empty( $caption ) ) {
            $caption = ! empty( $data['image']['caption'] ) ? $data['image']['caption'] : null;
        }

        $data['caption'] = $caption;

        // Return only image id
        $data['image'] = $data['image']['id'];

        return $data;
    }
}
