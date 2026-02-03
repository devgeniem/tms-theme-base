<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class TableFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class TableFields extends \Geniem\ACF\Field\Group {

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title' => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'table' => [
                'label'        => 'Taulukko',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $table_field = ( new Field\Relationship( $strings['table']['label'] ) )
            ->set_key( "{$key}_table" )
            ->set_name( 'table' )
            ->set_post_types( [ PostType\TablePress::SLUG ] )
            ->set_filters( [ 'search' ] )
            ->set_return_format( 'id' )
            ->set_max( 1 )
            ->set_instructions( $strings['table']['instructions'] );

        return [
            $title_field,
            $table_field,
        ];
    }
}
