<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Field\Tab;
use TMS\Theme\Base\Logger;

/**
 * Class MapSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class MapSettingsTab extends Tab {

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
        'tab'             => 'Kartat',
        'map_placeholder' => [
            'title'        => 'Kartan placeholder-kuva',
            'instructions' => '',
        ],
        'map_button_text' => [
            'title'        => 'Kartan näyttämisen toimintakehoite',
            'instructions' => '',
        ],
    ];

    /**
     * The constructor for tab.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        $label = $this->strings['tab'];

        parent::__construct( $label );

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
            $this->set_label( $strings['tab'] );

            $map_placeholder_field = ( new Field\Image( $strings['map_placeholder']['title'] ) )
                ->set_key( "${key}_map_placeholder" )
                ->set_name( 'map_placeholder' )
                ->set_return_format( 'id' )
                ->set_instructions( $strings['map_placeholder']['instructions'] );

            $map_button_text_field = ( new Field\Text( $strings['map_button_text']['title'] ) )
                ->set_key( "${key}_map_button_text" )
                ->set_name( 'map_button_text' )
                ->set_default_value( __( 'Open map', 'tms-theme-base' ) )
                ->set_instructions( $strings['map_button_text']['instructions'] );

            $this->add_fields( [
                $map_placeholder_field,
                $map_button_text_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
