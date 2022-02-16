<?php

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use TMS\Theme\Base\ACF\Fields\ContactsFields;
use TMS\Theme\Base\Logger;

/**
 * Class ContactLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class ContactsLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_contacts';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Yhteystiedot',
            $key . self::KEY,
            'contacts'
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
            $this->add_fields(
                $this->filter_layout_fields( $fields->get_fields(), $this->get_key(), self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
