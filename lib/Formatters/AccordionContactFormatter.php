<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

/**
 * Class AccordionContactFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class AccordionContactFormatter extends ContactFormatter {

    /**
     * Define formatter name
     */
    const NAME = 'AccordionContact';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/accordion_contacts/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/layout/accordion_contacts/data',
            [ $this, 'format' ]
        );
    }
}
