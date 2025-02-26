<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class CallToActionFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class CallToActionFields extends Field\Group {

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
            'rows'           => [
                'label'        => 'Nostot',
                'instructions' => '',
                'button'       => 'Lisää rivi',
            ],
            'layout'         => [
                'label'        => 'Asettelu',
                'instructions' => '',
            ],
            'image'          => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
            'title'          => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'    => [
                'label'        => 'Teksti',
                'instructions' => '',
            ],
            'link'           => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
            'display_artist' => [
                'label'        => 'Kuvan tekijätiedot',
                'instructions' => 'Näytetäänkö kuvan alla kuvan tekijätiedot?
                Tekijätietoina näytetään “Taiteilijan nimi: Kuvan otsikko”, molemmat kentät tulee olla täytettynä tämän tiedon näyttämiseksi',
                'on'           => 'Näytetään',
                'off'          => 'Ei näytetä',
            ],
            'display_caption' => [
                'label'        => 'Kuvateksti',
                'instructions' => 'Näytetäänkö kuvan alla kuvateksti?',
                'on'           => 'Näytetään',
                'off'          => 'Ei näytetä',
            ],
        ];

        $key = $this->get_key();

        $rows_field = ( new Field\Repeater( $strings['rows']['label'] ) )
            ->set_key( "{$key}_rows" )
            ->set_name( 'rows' )
            ->set_min( 1 )
            ->set_max( 6 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "{$key}_numbers" )
            ->set_name( 'image' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['image']['instructions'] );

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_rows( 4 )
            ->set_new_lines( 'wpautop' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['description']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "{$key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $layout_field = ( new Field\Radio( $strings['layout']['label'] ) )
            ->set_key( "{$key}_layout" )
            ->set_name( 'layout' )
            ->set_choices( [
                'is-image-first' => 'Kuva ensin',
                'is-text-first'  => 'Teksti ensin',
            ] )
            ->set_wrapper_width( 33 )
            ->set_instructions( $strings['layout']['instructions'] );

        $display_artist_field = ( new Field\TrueFalse( $strings['display_artist']['label'] ) )
            ->set_key( "{$key}_display_artist" )
            ->set_name( 'display_artist' )
            ->set_wrapper_width( 33 )
            ->use_ui()
            ->set_ui_off_text( $strings['display_artist']['off'] )
            ->set_ui_on_text( $strings['display_artist']['on'] )
            ->set_instructions( $strings['display_artist']['instructions'] );

        $display_caption_field = ( new Field\TrueFalse( $strings['display_caption']['label'] ) )
            ->set_key( "{$key}_display_caption" )
            ->set_name( 'display_caption' )
            ->set_wrapper_width( 33 )
            ->use_ui()
            ->set_ui_off_text( $strings['display_caption']['off'] )
            ->set_ui_on_text( $strings['display_caption']['on'] )
            ->set_instructions( $strings['display_caption']['instructions'] );

        $rows_field->add_fields( [
            $image_field,
            $title_field,
            $description_field,
            $link_field,
            $layout_field,
            $display_artist_field,
            $display_caption_field,
        ] );

        return [
            $rows_field,
        ];
    }
}
