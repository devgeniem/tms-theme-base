<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;

/**
 * Class GravityFormsFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class GravityFormsFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'GravityForms';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/gravityform/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout or block data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data = [] ) : array {
        $data['gravityform'] = $this->get_gravityform( $data );

        return $data;
    }

    /**
     * Get gravityform.
     *
     * @param array $data Formatter data.
     *
     * @return string
     */
    private function get_gravityform( array $data ) : string {
        $form                = '';
        $display_title       = true;
        $display_description = true;
        $display_inactive    = false;
        $field_values        = null;
        $ajax                = false;
        $tabindex            = 0;
        $echo                = false;

        if ( function_exists( 'gravity_form' ) ) {
            $form = gravity_form(
                $data['form'] ?? null,
                $display_title,
                $display_description,
                $display_inactive,
                $field_values,
                $ajax,
                $tabindex,
                $echo
            );
        }

        if ( empty( $form ) ) {
            return '';
        }

        return $form;
    }
}
