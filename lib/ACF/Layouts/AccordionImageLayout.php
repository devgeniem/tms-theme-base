<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionImageLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionImageLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_accordion_image';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Kuva',
            $key . self::KEY,
            'accordion_image'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $strings = [
            'image' => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "${key}_image" )
            ->set_name( 'image' )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['image']['instructions'] );

        try {
            $fields = [ $image_field ];
            $this->add_fields(
                $this->filter_layout_fields( $fields, $key )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
