<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class SubpageFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class SubpageFields extends \Geniem\ACF\Field\Group {

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
            'title'            => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'background_color' => [
                'label'        => 'Taustaväri',
                'instructions' => '',
            ],
            'display_image'    => [
                'label'        => 'Kuvat käytössä',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $background_color_field = ( new Field\Select( $strings['background_color']['label'] ) )
            ->set_key( "${key}_background_color" )
            ->set_name( 'background_color' )
            ->set_choices( [
                'black'     => 'Musta',
                'white'     => 'Valkoinen',
                'primary'   => 'Pääväri',
                'secondary' => 'Toissijainen väri',
            ] )
            ->set_default_value( 'has-background-black' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['background_color']['instructions'] );

        $display_image_field = ( new Field\Radio( $strings['display_image']['label'] ) )
            ->set_key( "${key}_display_image" )
            ->set_name( 'display_image' )
            ->set_choices( [
                false => 'Ei',
                true  => 'Kyllä',
            ] )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['display_image']['instructions'] );

        return [
            $title_field,
            $background_color_field,
            $display_image_field,
        ];
    }
}
