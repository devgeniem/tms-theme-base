<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Fields\HeroFields;
use TMS\Theme\Base\Logger;

/**
 * Class HeroLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class HeroLayout extends Layout {

    /**
     * Layout key
     */
    const KEY = '_hero';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Hero',
            $key . self::KEY,
            'hero'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new HeroFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $filtered_fields = apply_filters(
                'tms/acf/layout/' . self::KEY . '/fields',
                $fields->get_fields(),
                $this->get_key()
            );

            $filtered_fields = apply_filters(
                'tms/acf/layout/' . $this->get_key() . '/fields',
                $filtered_fields,
                $this->get_key()
            );

            $this->add_fields( $filtered_fields );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
