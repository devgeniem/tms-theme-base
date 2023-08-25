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
            'category'         => [
                'label'        => 'Kategoria',
                'instructions' => '',
            ],
            'area'        => [
                'label'        => 'Alue',
                'instructions' => '',
            ],
            'target'       => [
                'label'        => 'Kohderyhmä',
                'instructions' => '',
            ],
            'tag'       => [
                'label'        => 'Tag',
                'instructions' => '',
            ],
            'text'            => [
                'label'        => 'Vapaasanahaku',
                'instructions' => '',
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

        $category_field = ( new Field\Select( $strings['category']['label'] ) )
            ->set_key( "${key}_category" )
            ->set_name( 'category' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['category']['instructions'] );

        $area_field = ( new Field\Select( $strings['area']['label'] ) )
            ->set_key( "${key}_area" )
            ->set_name( 'area' )
            ->use_ui()
            ->allow_null()
            ->allow_multiple()
            ->use_ajax()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['area']['instructions'] );

        $target_field = ( new Field\Select( $strings['target']['label'] ) )
            ->set_key( "{$key}_target" )
            ->set_name( 'target' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['target']['instructions'] );

        $tag_field = ( new Field\Select( $strings['tag']['label'] ) )
            ->set_key( "{$key}_tag" )
            ->set_name( 'tag' )
            ->use_ui()
            ->use_ajax()
            ->allow_null()
            ->allow_multiple()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['tag']['instructions'] );

        $text_field = ( new Field\Text( $strings['text']['label'] ) )
            ->set_key( "${key}_text" )
            ->set_name( 'text' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['text']['instructions'] );

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
            $category_field,
            $area_field,
            $target_field,
            $tag_field,
            $text_field,
            $page_size_field,
            $show_images_field,
            $all_events_link_field,
        ];
    }
}
