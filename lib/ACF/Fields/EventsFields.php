<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class EventsFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class EventsFields extends \Geniem\ACF\Field\Group {

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
            'title'           => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'start'           => [
                'label'        => 'Alkupäivämäärä',
                'instructions' => '',
            ],
            'end'             => [
                'label'        => 'Loppupäivämäärä',
                'instructions' => '',
            ],
            'starts_today'    => [
                'label'        => 'Alkaa tänään',
                'instructions' => 'Alkupäivämääränä käytetään kuluvaa päivää',
            ],
            'keyword'         => [
                'label'        => 'Avainsana',
                'instructions' => '',
            ],
            'location'        => [
                'label'        => 'Tapahtumapaikka',
                'instructions' => '',
            ],
            'publisher'       => [
                'label'        => 'Julkaisija',
                'instructions' => '',
            ],
            'text'            => [
                'label'        => 'Vapaasanahaku',
                'instructions' => '',
            ],
            'sort'            => [
                'label'        => 'Järjestys',
                'instructions' => 'Järjestys pakotettu: Päättymisaika (nouseva)',
                'choices'      => [
                    ''                    => 'Kustomoitu järjestys',
                    'end_time'            => 'Päättymisaika (nouseva)',
                    '-end_time'           => 'Päättymisaika (laskeva)',
                    'start_time'          => 'Alkamisaika (nouseva)',
                    '-start_time'         => 'Alkamisaika (laskeva)',
                    'duration'            => 'Kesto (nouseva)',
                    '-duration'           => 'Kesto (laskeva)',
                    'last_modified_time'  => 'Viimeksi muokattu (nouseva)',
                    '-last_modified_time' => 'Viimeksi muokattu (laskeva)',
                ],
                'default'      => '',
            ],
            'page_size'       => [
                'label'        => 'Näytettävien tapahtumien määrä',
                'instructions' => '',
            ],
            'show_images'     => [
                'label'        => 'Näytä kuvat',
                'instructions' => '',
            ],
            'all_events_link' => [
                'label'        => '"Katso kaikki tapahtumat" -linkki',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $start_field = ( new Field\DatePicker( $strings['start']['label'] ) )
            ->set_key( "${key}_start" )
            ->set_name( 'start' )
            ->set_display_format( 'j.n.Y' )
            ->set_return_format( 'Y-m-d' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['start']['instructions'] );

        $end_field = ( new Field\DatePicker( $strings['end']['label'] ) )
            ->set_key( "${key}_end" )
            ->set_name( 'end' )
            ->set_display_format( 'j.n.Y' )
            ->set_return_format( 'Y-m-d' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['end']['instructions'] );

        $starts_today_field = ( new Field\TrueFalse( $strings['starts_today']['label'] ) )
            ->set_key( "${key}_starts_today" )
            ->set_name( 'starts_today' )
            ->use_ui()
            ->set_default_value( false )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['starts_today']['instructions'] );

        $keyword_field = ( new Field\Select( $strings['keyword']['label'] ) )
            ->set_key( "${key}_keyword" )
            ->set_name( 'keyword' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['keyword']['instructions'] );

        $location_field = ( new Field\Select( $strings['location']['label'] ) )
            ->set_key( "${key}_location" )
            ->set_name( 'location' )
            ->use_ui()
            ->allow_null()
            ->use_ajax()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['location']['instructions'] );

        $publisher_field = ( new Field\Select( $strings['publisher']['label'] ) )
            ->set_key( "${key}_publisher" )
            ->set_name( 'publisher' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['publisher']['instructions'] );

        $text_field = ( new Field\Text( $strings['text']['label'] ) )
            ->set_key( "${key}_text" )
            ->set_name( 'text' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['text']['instructions'] );

        $sort_field = ( new Field\Select( $strings['sort']['label'] ) )
            ->set_key( "${key}_sort" )
            ->set_name( 'sort' )
            ->set_choices( $strings['sort']['choices'] )
            ->set_default_value( $strings['sort']['default'] )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['sort']['instructions'] );

        $page_size_field = ( new Field\Number( $strings['page_size']['label'] ) )
            ->set_key( "${key}_page_size" )
            ->set_name( 'page_size' )
            ->set_min( 3 )
            ->set_max( 12 )
            ->set_default_value( 6 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['page_size']['instructions'] );

        $show_images_field = ( new Field\TrueFalse( $strings['show_images']['label'] ) )
            ->set_key( "${key}_show_images" )
            ->set_name( 'show_images' )
            ->use_ui()
            ->set_default_value( true )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['show_images']['instructions'] );

        $all_events_link_field = ( new Field\Link( $strings['all_events_link']['label'] ) )
            ->set_key( "${key}_all_events_link" )
            ->set_name( 'all_events_link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['all_events_link']['instructions'] );

        return [
            $title_field,
            $start_field,
            $end_field,
            $starts_today_field,
            $keyword_field,
            $location_field,
            $publisher_field,
            $text_field,
            $sort_field,
            $page_size_field,
            $show_images_field,
            $all_events_link_field,
        ];
    }
}
