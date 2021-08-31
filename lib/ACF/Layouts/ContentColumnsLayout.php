<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use TMS\Theme\Base\ACF\Fields\ContentColumnsFields;
use TMS\Theme\Base\Logger;

/**
 * Class ContentColumnsLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class ContentColumnsLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_content_columns';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Palstat',
            $key . self::KEY,
            'content_columns'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $fields = new ContentColumnsFields(
            $this->get_label(),
            $this->get_key(),
            $this->get_name()
        );

        try {
            $this->add_fields(
                $this->filter_layout_fields( $fields->get_fields(), $this->get_key() )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
