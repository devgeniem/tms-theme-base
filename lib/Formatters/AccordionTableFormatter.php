<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Traits\Components;

/**
 * Class AccordionTableFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class AccordionTableFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    use Components;

    /**
     * Define formatter name
     */
    const NAME = 'AccordionTable';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/accordion_table/data',
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
        if ( empty( $data['table'] ) || empty( $data['table'][0] ) ) {
            return $data;
        }

        $table_post_id     = $data['table'][0];
        $tablepress_tables = json_decode( \get_option( 'tablepress_tables' ), true );
        $tables            = $tablepress_tables['table_post'] ?? [];

        if ( ! empty( $tables ) ) {
            $id = array_search( $table_post_id, $tables );

            if ( false !== $id ) {
                $data['table_markup'] = \do_shortcode( '[table id=' . $id . ' responsive="scroll" /]' );
            }
        }

        return $data;
    }
}
