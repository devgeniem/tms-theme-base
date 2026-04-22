<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\BlogArticle;

/**
 * Class ContactsSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class ContactsSettingsTab extends \Geniem\ACF\Field\Tab {

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
        'tab'                    => 'Yhteystiedot',
        'contacts_default_image' => [
            'title'        => 'Oletuskuva',
            'instructions' => 'Yhteystiedon oletuskuva',
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
            $contacts_default_image_field = ( new Field\Image( $strings['contacts_default_image']['title'] ) )
                ->set_key( "{$key}_contacts_default_image" )
                ->set_name( 'contacts_default_image' )
                ->set_return_format( 'id' )
                ->set_instructions( $strings['contacts_default_image']['instructions'] );

            $this->add_fields( [
                $contacts_default_image_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
