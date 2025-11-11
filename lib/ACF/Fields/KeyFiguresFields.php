<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class KeyFiguresFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class KeyFiguresFields extends Field\Group {

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
     * @throws Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'rows'             => [
                'label'        => 'Nostot',
                'instructions' => '',
                'button'       => 'Lisää rivi',
            ],
            'numbers'          => [
                'label'        => 'Rivin nostot',
                'instructions' => '',
                'button'       => 'Lisää nosto',
            ],
            'layout'           => [
                'label'        => 'Mittasuhteet',
                'instructions' => '',
            ],
            'number'           => [
                'label'        => 'Numero',
                'instructions' => 'Maksimissaan 10 merkkiä',
            ],
            'description'      => [
                'label'        => 'Kuvaus',
                'instructions' => 'Maksimissaan 200 merkkiä',
            ],
            'background_color' => [
                'label'        => 'Taustaväri',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $rows_field = ( new Field\Repeater( $strings['rows']['label'] ) )
            ->set_key( "{$key}_rows" )
            ->set_name( 'rows' )
            ->set_max( 3 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $layout_field = ( new Field\Radio( $strings['layout']['label'] ) )
            ->set_key( "{$key}_layout" )
            ->set_name( 'layout' )
            ->set_choices( [
                '50-50' => '50/50',
                '30-70' => '30/70',
                '70-30' => '70/30',
            ] )
            ->set_instructions( $strings['layout']['instructions'] );

        $numbers_field = ( new Field\Repeater( $strings['numbers']['label'] ) )
            ->set_key( "{$key}_numbers" )
            ->set_name( 'numbers' )
            ->set_max( 2 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['numbers']['button'] )
            ->set_instructions( $strings['numbers']['instructions'] );

        $number_field = ( new Field\Text( $strings['number']['label'] ) )
            ->set_key( "{$key}_number" )
            ->set_name( 'number' )
            ->set_required()
            ->set_wrapper_width( 30 )
            ->set_maxlength( 10 )
            ->set_instructions( $strings['number']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_wrapper_width( 70 )
            ->set_rows( 2 )
            ->set_maxlength( 200 )
            ->set_instructions( $strings['description']['instructions'] );

        $background_color_field = ( new Field\Select( $strings['background_color']['label'] ) )
            ->set_key( "{$key}_background_color" )
            ->set_name( 'background_color' )
            ->set_choices( [
                'black'     => 'Musta',
                'white'     => 'Valkoinen',
                'primary'   => 'Pääväri',
                'secondary' => 'Toissijainen väri',
            ] )
            ->set_default_value( 'has-background-black' )
            ->set_instructions( $strings['background_color']['instructions'] );

        $numbers_field->add_fields( [
            $number_field,
            $description_field,
            $background_color_field,
        ] );

        $rows_field->add_fields( [
            $layout_field,
            $numbers_field,
        ] );

        return [
            $rows_field,
        ];
    }
}
