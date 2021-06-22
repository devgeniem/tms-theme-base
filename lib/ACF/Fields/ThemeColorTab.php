<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use \Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class ThemeColorTab
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ThemeColorTab extends \Geniem\ACF\Field\Tab {
    /**
     * Where should the tab switcher be located
     *
     * @var string
     */
    protected $placement = 'left';
    /**
     * UI Strings.
     *
     * @var array
     */
    private $strings = [
        'tab'             => 'Teeman ulkoasu',
        'color_selection' => [
            'title'        => 'Väriteeman valinta',
            'instructions' => '',
        ],
    ];

    /**
     * ThemeColorTab constructor.
     *
     * @param string      $label Label for the field.
     * @param string|null $key   Key for the field.
     * @param string|null $name  Name for the field.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        if ( ! empty( $label ) ) {
            $this->strings['tab'] = $label;
        }

        parent::__construct( $this->strings['tab'], $key, $name );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) : void {
        try {
            $this->set_label( $this->strings['tab'] );

            $theme_colors = apply_filters( 'tms/theme/theme_colors', [
                'tunnelma'      => 'Tunnelma (oletus, Punainen/vaalean siniharmaa)',
                'tummavesi'     => 'Tumma Vesi (Tumman sininen/vaalea petrooli)',
                'tyo'           => 'Työ (Työn sininen/vaalea siniharmaa)',
                'muutos'        => 'Muutos (Turkoosin sininen/vaalea siniharmaa)',
                'havunvihrea'   => 'Havun vihreä (Tumma vihreä)',
                'paahde'        => 'Paahde (Tumma keltainen)',
                'vaaleanvihrea' => 'Vaalean vihreä (Vaalean vihreä)',
                'turkoosi'      => 'Turkoosi (Vaalean sininen)',
            ] );

            $theme_default_color = apply_filters(
                'tms/theme/theme_default_color',
                'tunnelma'
            );

            $color_theme_select = ( new Field\Select( $this->strings['color_selection']['title'] ) )
                ->set_key( $key . '_theme_color' )
                ->set_name( 'theme_color' )
                ->set_choices( $theme_colors )
                ->set_default_value( $theme_default_color )
                ->set_instructions( $this->strings['color_selection']['instructions'] );

            $this->add_fields( [
                $color_theme_select,
            ] );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
