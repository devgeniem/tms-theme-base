<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Traits\Components;

/**
 * Class AccordionFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class AccordionFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    use Components;

    /**
     * Define formatter name
     */
    const NAME = 'Accordion';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/accordion/data',
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
        if ( empty( $data['sections'] ) ) {
            return $data;
        }

        $sections = $data['sections'] ?? [];
        $sections = array_filter( $sections, fn( $item ) => ! empty( $item['section_content'] ) );

        $data['sections'] = array_map( function ( $section ) {
            $section['ID']              = wp_unique_id();
            $section['section_content'] = $this->handle_layouts( $section['section_content'] );

            return $section;
        }, $sections );

        return $data;
    }
}
