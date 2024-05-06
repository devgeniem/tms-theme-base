<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

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
        'default_image'   => [
            'title'        => 'Oletuskuva',
            'instructions' => '',
        ],
        'use_hero_museum' => [
            'title'        => 'Ota käyttöön museosivustojen hero',
            'instructions' => 'Tällä valinnalla saat otettua käyttöön museosivustojen heron etusivulle.',
        ],
    ];

    /**
     * Available color themes and their names.
     *
     * @var array
     */
    public static array $available_themes = [
        'tunnelma'        => 'Tunnelma (Värimalli on käytössä oletuksena. Punainen ja vaalea siniharmaa.)',
        'tummavesi'       => 'Tumma Vesi (Tumma vesi ja vaalea petrooli)',
        'tyo'             => 'Työ (Sininen ja vaalean sininen)',
        'havu'            => 'Havu (Tumman vihreä)',
        'teraksensininen' => 'Teräksen sininen (Teräksen sininen ja vaalea "muutos"-pinkki)',
        'paahde'          => 'Paahde (Keltaoranssi)',
        'kokemus'         => 'Kokemus (Vaalean vihreä)',
        'neutraali'       => 'Neutraali (Vaalea, neutraali värimalli, jossa korkea kontrasti)',
    ];

    /**
     * ThemeColorTab constructor.
     *
     * @param string      $label Label for the field.
     * @param string|null $key   Key for the field.
     * @param string|null $name  Name for the field.
     */
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
        if ( ! empty( $label ) ) {
            $this->strings['tab'] = $label;
        }

        parent::__construct( $this->strings['tab'] );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) : void {
        try {
            $theme_colors = apply_filters( 'tms/theme/theme_colors', self::$available_themes );

            $theme_default_color = apply_filters(
                'tms/theme/theme_default_color',
                DEFAULT_THEME_COLOR
            );

            $color_theme_select = ( new Field\Select( $this->strings['color_selection']['title'] ) )
                ->set_key( "{$key}_theme_color" )
                ->set_name( 'theme_color' )
                ->set_choices( $theme_colors )
                ->set_default_value( $theme_default_color )
                ->set_instructions( $this->strings['color_selection']['instructions'] );

            $image_field = ( new Field\Image( $this->strings['default_image']['title'] ) )
                ->set_key( "{$key}_default_image" )
                ->set_name( 'default_image' )
                ->set_return_format( 'id' )
                ->set_instructions( $this->strings['default_image']['instructions'] );

            $use_hero_museum = ( new Field\TrueFalse( $this->strings['use_hero_museum']['title'] ) )
                ->set_key( "{$key}_use_hero_museum" )
                ->set_name( 'use_hero_museum' )
                ->use_ui()
                ->set_instructions( $this->strings['use_hero_museum']['instructions'] );

            $this->add_fields( [
                $color_theme_select,
                $image_field,
                $use_hero_museum,
            ] );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
