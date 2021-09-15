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
 * Class HeaderSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class HeaderSettingsTab extends Tab {

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
        'tab'              => 'Ylätunniste',
        'logo'             => [
            'title'        => 'Logo',
            'instructions' => '',
        ],
        'brand_logo'       => [
            'title'        => 'Tampere-logo',
            'instructions' => '',
        ],
        'tagline'          => [
            'title'        => 'Tagline',
            'instructions' => '',
        ],
        'lang_nav_display' => [
            'title'        => 'Kielivalikko',
            'instructions' => '',
        ],
        'hide_main_nav'    => [
            'title'        => 'Näytä vain hampurilaisvalikko',
            'instructions' => 'Kyllä-valinnan ollessa aktiivinen vain hampurilaisvalikko näytetään',
        ],
        'limit_nav_depth'  => [
            'title'        => 'Pudotusvalikko pois käytöstä',
            'instructions' => 'Päätason elementit toimivat linkkeinä, eivätkä avaa pudotusvalikkoa',
        ],
        'header_scripts'   => [
            'title'        => 'Ylätunnisteen custom-skriptit',
            'instructions' => '',
        ],
        'hide_search'      => [
            'title'        => 'Piilota hakutoiminto',
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
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
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
            $logo_field = ( new Field\Image( $strings['logo']['title'] ) )
                ->set_key( "${key}_logo" )
                ->set_name( 'logo' )
                ->set_return_format( 'id' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['logo']['instructions'] );

            $brand_logo_field = ( new Field\Image( $strings['brand_logo']['title'] ) )
                ->set_key( "${key}_brand_logo" )
                ->set_name( 'brand_logo' )
                ->set_wrapper_width( 50 )
                ->set_return_format( 'id' )
                ->set_instructions( $strings['brand_logo']['instructions'] );

            $tagline_field = ( new Field\Text( $strings['tagline']['title'] ) )
                ->set_key( "${key}_tagline" )
                ->set_name( 'tagline' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['tagline']['instructions'] );

            $lang_nav_display_field = ( new Field\Select( $strings['lang_nav_display']['title'] ) )
                ->set_key( "${key}_lang_nav_display" )
                ->set_name( 'lang_nav_display' )
                ->set_choices( [
                    'hide'       => 'Ei käytössä',
                    'dropdown'   => 'Pudotusvalikko',
                    'horizontal' => 'Vaakavalikko',
                ] )
                ->set_default_value( 'horizontal' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['lang_nav_display']['instructions'] );

            $hide_main_nav_field = ( new Field\TrueFalse( $strings['hide_main_nav']['title'] ) )
                ->set_key( "${key}_hide_main_nav" )
                ->set_name( 'hide_main_nav' )
                ->set_default_value( false )
                ->use_ui()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['hide_main_nav']['instructions'] );

            $limit_nav_depth_field = ( new Field\TrueFalse( $strings['limit_nav_depth']['title'] ) )
                ->set_key( "${key}_limit_nav_depth" )
                ->set_name( 'limit_nav_depth' )
                ->set_default_value( false )
                ->use_ui()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['limit_nav_depth']['instructions'] );

            $hide_search_field = ( new Field\TrueFalse( $strings['hide_search']['title'] ) )
                ->set_key( "${key}_hide_search" )
                ->set_name( 'hide_search' )
                ->set_default_value( false )
                ->use_ui()
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['hide_search']['instructions'] );

            $this->add_fields( [
                $logo_field,
                $brand_logo_field,
                $tagline_field,
                $lang_nav_display_field,
                $hide_main_nav_field,
                $limit_nav_depth_field,
                $hide_search_field,
            ] );

            if ( user_can( get_current_user_id(), 'unfiltered_html' ) ) {
                $header_scripts_field = ( new Field\Textarea( $strings['header_scripts']['title'] ) )
                    ->set_key( "${key}_header_scripts" )
                    ->set_name( 'header_scripts' )
                    ->set_wrapper_width( 50 )
                    ->set_instructions( $strings['header_scripts']['instructions'] );

                $this->add_field( $header_scripts_field );
            }
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
