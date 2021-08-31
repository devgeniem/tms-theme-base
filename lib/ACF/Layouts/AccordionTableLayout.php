<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class AccordionTableLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionTableLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_accordion_table';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Taulukko',
            $key . self::KEY,
            'accordion_table'
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
            'table' => [
                'label'        => 'Taulukko',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        try {
            $table_field = ( new Field\Relationship( $strings['table']['label'] ) )
                ->set_key( "${key}_table" )
                ->set_name( 'table' )
                ->set_post_types( [ PostType\TablePress::SLUG ] )
                ->set_filters( [ 'search' ] )
                ->set_return_format( 'id' )
                ->set_max( 1 )
                ->set_instructions( $strings['table']['instructions'] );

            $fields = [ $table_field ];
            $this->add_fields(
                $this->filter_layout_fields( $fields, $key )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
