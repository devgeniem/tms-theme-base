<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Field\Tab;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class EventsSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class EventsSettingsTab extends Tab {

    /**
     * Where should the tab switcher be located
     *
     * @var string
     */
    protected $placement = 'left';

    /**
     * Tab strings.
     *
     * @var array
     */
    protected $strings = [
        'tab'                           => 'Tapahtumat',
        'events_default_image'          => [
            'title'        => 'Oletuskuva',
            'instructions' => '',
        ],
        'events_default_image_credits'  => [
            'title'        => 'Oletuskuvan kuvaajatieto',
            'instructions' => '',
        ],
        'events_page'                   => [
            'title'        => 'Tapahtuma-sivu',
            'instructions' => 'Sivu, jolle on valittu Tapahtuma-sivupohja',
        ],
        'show_related_events_calendars' => [
            'title'        => 'Näytä muut sivuston tapahtumakalenterit',
            'instructions' => 'Tapahtumakalenterin yläosassa näytetään automaattisesti
                linkit muille saman sivuston sivuille, joilla on käytössä tapahtumakalenteri-sivupohja',
        ],
    ];

    /**
     * The constructor for tab.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
        parent::__construct( $this->strings['tab'] );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) {
        $strings = $this->strings;

        try {
            $image_field = ( new Field\Image( $strings['events_default_image']['title'] ) )
                ->set_key( "${key}_events_default_image" )
                ->set_name( 'events_default_image' )
                ->set_return_format( 'id' )
                ->set_instructions( $strings['events_default_image']['instructions'] );

            $image_credits_field = ( new Field\Text( $strings['events_default_image_credits']['title'] ) )
                ->set_key( "${key}_events_default_image_credits" )
                ->set_name( 'events_default_image_credits' )
                ->set_instructions( $strings['events_default_image_credits']['instructions'] );

            $events_page_field = ( new Field\PostObject( $strings['events_page']['title'] ) )
                ->set_key( "${key}_events_page" )
                ->set_name( 'events_page' )
                ->set_post_types( [ PostType\Page::SLUG ] )
                ->set_return_format( 'id' )
                ->set_instructions( $strings['events_page']['instructions'] );

            $show_event_calendars_field = ( new Field\TrueFalse( $strings['show_related_events_calendars']['title'] ) )
                ->set_key( "${key}_show_related_events_calendars" )
                ->set_name( 'show_related_events_calendars' )
                ->use_ui()
                ->set_default_value( false )
                ->set_instructions( $strings['show_related_events_calendars']['instructions'] );

            $this->add_fields( [
                $image_field,
                $image_credits_field,
                $events_page_field,
                $show_event_calendars_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
