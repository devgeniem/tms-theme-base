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
 * Class Error404SettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class Error404SettingsTab extends Tab {

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
        'tab'             => '404-sivu',
        '404_title'       => [
            'title'        => 'Otsikko',
            'instructions' => '',
        ],
        '404_description' => [
            'title'        => 'Kuvaus',
            'instructions' => '',
        ],
        '404_image'       => [
            'title'        => 'Kuva',
            'instructions' => '',
        ],
        '404_alignment'   => [
            'title'        => 'Tekstin tasaus',
            'instructions' => '',
            'choices'      => [
                'has-text-left' => 'Tasaa kuvausteksti vasemmalle',
            ],
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
            $title_field = ( new Field\Text( $strings['404_title']['title'] ) )
                ->set_key( "${key}_404_title" )
                ->set_name( '404_title' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['404_title']['instructions'] );

            $description_field = ( new Field\ExtendedWysiwyg( $strings['404_description']['title'] ) )
                ->set_key( "${key}_404_description" )
                ->set_name( '404_description' )
                ->set_tabs( 'visual' )
                ->set_toolbar(
                    [
                        'bold',
                        'italic',
                        'link',
                        'pastetext',
                        'removeformat',
                    ]
                )
                ->disable_media_upload()
                ->set_height( 200 )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['404_description']['instructions'] );

            $image_field = ( new Field\Image( $strings['404_image']['title'] ) )
                ->set_key( "${key}_404_image" )
                ->set_name( '404_image' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['404_image']['instructions'] );

            $alignment_field = ( new Field\Checkbox( $strings['404_alignment']['title'] ) )
                ->set_key( "${key}_404_alignment" )
                ->set_name( '404_alignment' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['404_alignment']['instructions'] )
                ->set_choices( $strings['404_alignment']['choices'] );

            $this->add_fields(
                apply_filters(
                    'tms/acf/tab/error404/fields',
                    [
                        $title_field,
                        $description_field,
                        $image_field,
                        $alignment_field,
                    ]
                )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
