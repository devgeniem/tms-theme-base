<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Fields\CallToActionFields;
use TMS\Theme\Base\Logger;

/**
 * Class CallToActionLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class CallToActionLayout extends Layout {

    /**
     * Layout key
     */
    const KEY = '_call_to_action';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Manuaaliset nostot',
            $key . self::KEY,
            'call_to_action'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new CallToActionFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $fields = apply_filters(
                'tms/acf/layout/' . self::KEY . '/fields',
                $fields->get_fields(),
                $this->get_key()
            );

            $fields = apply_filters(
                'tms/acf/layout/' . $this->get_key() . '/fields',
                $fields,
                $this->get_key()
            );

            $this->add_fields( $fields );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
