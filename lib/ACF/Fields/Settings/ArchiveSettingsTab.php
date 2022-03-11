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
 * Class ArchiveSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class ArchiveSettingsTab extends Tab {

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
        'tab'                     => 'Arkistot',
        'archive_use_images'      => [
            'title'        => 'Kuvat käytössä',
            'instructions' => '',
        ],
        'archive_view_type'       => [
            'title'        => 'Listaustyyli',
            'instructions' => '',
        ],
        'archive_hide_categories' => [
            'title'        => 'Piilota kategoriat',
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
            $use_images_field = ( new Field\TrueFalse( $strings['archive_use_images']['title'] ) )
                ->set_key( "${key}_archive_use_images" )
                ->set_name( 'archive_use_images' )
                ->set_default_value( true )
                ->use_ui()
                ->set_wrapper_width( 33 )
                ->set_instructions( $strings['archive_use_images']['instructions'] );

            $view_type_field = ( new Field\Radio( $strings['archive_view_type']['title'] ) )
                ->set_key( "${key}_archive_view_type" )
                ->set_name( 'archive_view_type' )
                ->set_choices( [
                    'grid' => 'Ruudukko',
                    'list' => 'Lista',
                ] )
                ->set_default_value( 'grid' )
                ->set_wrapper_width( 33 )
                ->set_instructions( $strings['archive_view_type']['instructions'] );

            $hide_categories_field = ( new Field\TrueFalse( $strings['archive_hide_categories']['title'] ) )
                ->set_key( "${key}_archive_hide_categories" )
                ->set_name( 'archive_hide_categories' )
                ->use_ui()
                ->set_wrapper_width( 34 )
                ->set_instructions( $strings['archive_hide_categories']['instructions'] );

            $this->add_fields( [
                $use_images_field,
                $view_type_field,
                $hide_categories_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
