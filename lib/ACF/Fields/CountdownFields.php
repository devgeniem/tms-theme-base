<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\ConditionalLogicGroup;
use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class CountdownFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class CountdownFields extends Field\Group {

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
            'title'           => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'target_datetime' => [
                'label'        => 'Ajankohta',
                'instructions' => '',
            ],
            'type'            => [
                'label'        => 'Formaatti',
                'instructions' => '',
                'choices'      => [
                    'countdown_seconds' => 'Laskuri (päivät, tunnit, minuutit, sekunnit)',
                    'countdown'         => 'Laskuri (päivät, tunnit, minuutit)',
                    'countdown_date'    => 'Laskuri (päivät)',
                    'date'              => 'Päivämäärä',
                ],
            ],
            'expired_text' => [
                'label'        => 'Päättyneen laskurin teksti',
                'instructions' => 'Teksti, joka esitetään kun laskuri on pysähtynyt.',
            ],
            'location'        => [
                'label'        => 'Sijainti',
                'instructions' => '',
            ],
            'image'           => [
                'label'        => 'Taustakuva',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $quote_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $expired_text_field = ( new Field\Text( $strings['expired_text']['label'] ) )
            ->set_key( "{$key}_expired_text" )
            ->set_name( 'expired_text' )
            ->set_instructions( $strings['expired_text']['instructions'] );

        $location_field = ( new Field\Text( $strings['location']['label'] ) )
            ->set_key( "{$key}_location" )
            ->set_name( 'location' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['location']['instructions'] );

        $type_field = ( new Field\Select( $strings['type']['label'] ) )
            ->set_key( "{$key}_type" )
            ->set_name( 'type' )
            ->set_wrapper_width( 33 )
            ->set_choices( $strings['type']['choices'] )
            ->set_instructions( $strings['type']['instructions'] );

        $target_datetime_field = ( new Field\DateTimePicker( $strings['target_datetime']['label'] ) )
            ->set_key( "{$key}_target_datetime" )
            ->set_name( 'target_datetime' )
            ->set_display_format( 'd.m.Y H:i' )
            ->set_return_format( 'U' )
            ->set_wrapper_width( 33 )
            ->set_instructions( $strings['target_datetime']['instructions'] );

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "{$key}_image" )
            ->set_name( 'image' )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 33 )
            ->set_instructions( $strings['image']['instructions'] );

        return [
            $quote_field,
            $location_field,
            $expired_text_field,
            $type_field,
            $target_datetime_field,
            $image_field,
        ];
    }
}
