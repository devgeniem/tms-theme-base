<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use \Geniem\ACF\Exception;
use \Geniem\ACF\Field;
use \Geniem\ACF\Group;
use \Geniem\ACF\RuleGroup;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class SettingsGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class SettingsGroup {

    /**
     * SettingsGroup constructor.
     */
    public function __construct() {
        add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = _x( 'Site settings', 'theme ACF', 'tms-theme-base' );

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_site_settings' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Settings::SLUG );

            $field_group
                ->add_rule_group( $rule_group )
                ->set_position( 'normal' )
                ->set_hidden_elements(
                    [
                        'discussion',
                        'comments',
                        'format',
                        'send-trackbacks',
                    ]
                );

            $field_group->add_fields(
                apply_filters(
                    'tms/acf/group/' . $field_group->get_key() . '/fields',
                    [
                        $this->get_header_fields( $field_group->get_key() ),
                        $this->get_map_fields( $field_group->get_key() ),
                        $this->get_social_media_sharing_fields( $field_group->get_key() ),
                    ]
                )
            );

            $field_group = apply_filters(
                'tms/acf/group/' . $field_group->get_key(),
                $field_group
            );

            $field_group->register();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTraceAsString() );
        }
    }

    /**
     * Get header fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_header_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'  => _x( 'Header', 'theme ACF', 'tms-theme-base' ),
            'logo' => [
                'title'        => _x( 'Site logo', 'theme ACF', 'tms-theme-base' ),
                'instructions' => _x( 'Add site logo here.', 'theme ACF', 'tms-theme-base' ),
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $logo = ( new Field\Image( $strings['logo']['title'] ) )
            ->set_key( "${key}_logo" )
            ->set_name( 'logo' )
            ->set_instructions( $strings['logo']['instructions'] );

        $tab->add_field( $logo );

        return $tab;
    }

    /**
     * Get map fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_map_fields( string $key ) : Field\Tab {
        $strings = [
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

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

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

        $tab->add_fields( [
            $map_placeholder_field,
            $map_button_text_field,
        ] );

        return $tab;
    }

    /**
     * Get social media sharing fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_social_media_sharing_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'           => 'Sosiaalinen media',
            'some_channels' => [
                'title'        => 'Kanavat',
                'instructions' => 'Valitse käytössä olevat kanavat',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

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

        $tab->add_fields( [
            $some_channels_field,
        ] );

        return $tab;
    }
}

( new SettingsGroup() );
