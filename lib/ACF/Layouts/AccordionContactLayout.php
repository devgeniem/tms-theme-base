<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Fields\ContactsFields;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionContactLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionContactLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_accordion_contact';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Yhteystiedot',
            $key . self::KEY,
            'accordion_contacts'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new ContactsFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $layout_fields = $fields->get_fields();

            unset( $layout_fields['title'] );
            unset( $layout_fields['description'] );

            $this->add_fields(
                $this->filter_layout_fields( $layout_fields, $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
