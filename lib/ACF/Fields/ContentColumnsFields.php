<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class ContentColumnsFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ContentColumnsFields extends Field\Group {

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
            'aspect_ratio'   => [
                'label'        => 'Mittasuhteet',
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
            'display_artist' => [
                'label'        => 'Kuvan tekijätiedot',
                'instructions' => 'Näytetäänkö kuvan alla kuvan tekijätiedot?',
                'on'           => 'Näytetään',
                'off'          => 'Ei näytetä',
            ],
            'display_caption' => [
                'label'        => 'Kuvateksti',
                'instructions' => 'Näytetäänkö kuvan alla kuvateksti?',
                'on'           => 'Näytetään',
                'off'          => 'Ei näytetä',
            ],
            'display_author'  => [
                'label'        => 'Kuvaajan tiedot',
                'instructions' => 'Näytetäänkö kuvan alla kuvaajan nimi?',
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

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 100 )
            ->redipress_include_search()
            ->set_instructions( $strings['title']['instructions'] );

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "{$key}_image" )
            ->set_name( 'image' )
            ->set_wrapper_width( 45 )
            ->set_instructions( $strings['image']['instructions'] );

        $description_field = ( new Field\Wysiwyg( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_toolbar( [ 'bold', 'italic' ] )
            ->disable_media_upload()
            ->set_wrapper_width( 55 )
            ->redipress_include_search()
            ->set_instructions( $strings['description']['instructions'] );

        $layout_field = ( new Field\Radio( $strings['layout']['label'] ) )
            ->set_key( "{$key}_layout" )
            ->set_name( 'layout' )
            ->set_choices( [
                'is-image-first' => 'Kuva ensin',
                'is-text-first'  => 'Teksti ensin',
            ] )
            ->set_wrapper_width( 25 )
            ->set_instructions( $strings['layout']['instructions'] );

        $aspect_ratio_field = ( new Field\Radio( $strings['aspect_ratio']['label'] ) )
            ->set_key( "{$key}_aspect_ratio" )
            ->set_name( 'aspect_ratio' )
            ->set_choices( [
                '50-50' => '50/50',
                '30-70' => '30/70',
                '70-30' => '70/30',
            ] )
            ->set_wrapper_width( 25 )
            ->set_instructions( $strings['aspect_ratio']['instructions'] );

        $display_artist_field = ( new Field\TrueFalse( $strings['display_artist']['label'] ) )
            ->set_key( "{$key}_display_artist" )
            ->set_name( 'display_artist' )
            ->set_wrapper_width( 25 )
            ->use_ui()
            ->set_ui_off_text( $strings['display_artist']['off'] )
            ->set_ui_on_text( $strings['display_artist']['on'] )
            ->set_instructions( $strings['display_artist']['instructions'] );

        $display_caption_field = ( new Field\TrueFalse( $strings['display_caption']['label'] ) )
            ->set_key( "{$key}_display_caption" )
            ->set_name( 'display_caption' )
            ->set_wrapper_width( 25 )
            ->use_ui()
            ->set_ui_off_text( $strings['display_caption']['off'] )
            ->set_ui_on_text( $strings['display_caption']['on'] )
            ->set_instructions( $strings['display_caption']['instructions'] );

        $display_author_field = ( new Field\TrueFalse( $strings['display_author']['label'] ) )
            ->set_key( "{$key}_display_author" )
            ->set_name( 'display_author' )
            ->set_wrapper_width( 100 )
            ->use_ui()
            ->set_ui_off_text( $strings['display_author']['off'] )
            ->set_ui_on_text( $strings['display_author']['on'] )
            ->set_instructions( $strings['display_author']['instructions'] );

        $rows_field->add_fields( [
            $title_field,
            $image_field,
            $description_field,
            $layout_field,
            $aspect_ratio_field,
            $display_artist_field,
            $display_caption_field,
            // $display_author_field,
        ] );

        return [
            $rows_field,
        ];
    }
}
