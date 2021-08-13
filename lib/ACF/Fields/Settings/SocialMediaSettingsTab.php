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
 * Class SocialMediaSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class SocialMediaSettingsTab extends Tab {

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
        'tab'           => 'Sosiaalinen media',
        'some_channels' => [
            'title'        => 'Kanavat',
            'instructions' => 'Valitse käytössä olevat kanavat',
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

            $some_channels_field = ( new Field\Checkbox( $strings['some_channels']['title'] ) )
                ->set_key( "${key}_some_channels" )
                ->set_name( 'some_channels' )
                ->set_choices( [
                    'facebook'  => 'Facebook',
                    'email'     => 'Sähköposti',
                    'whatsapp'  => 'WhatsApp',
                    'twitter'   => 'Twitter',
                    'linkedin'  => 'LinkedIn',
                    'vkontakte' => 'VKontakte',
                    'line'      => 'LINE',
                    'link'      => 'Linkki',
                ] )
                ->set_instructions( $strings['some_channels']['instructions'] );

            $this->add_fields( [
                $some_channels_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
