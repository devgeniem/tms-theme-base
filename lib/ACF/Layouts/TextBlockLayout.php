<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field\Flexible\Layout;
use TMS\Theme\Base\ACF\Fields\TextBlockFields;
use TMS\Theme\Base\Logger;

/**
 * Class TextBlockLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class TextBlockLayout extends Layout {

    /**
     * Layout key
     */
    const KEY = '_textblock';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Tekstikappale',
            $key . self::KEY,
            'textblock'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new TextBlockFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $this->add_fields(
                apply_filters(
                    'tms/acf/layout/' . $this->get_key() . '/fields',
                    $fields->get_fields()
                )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
