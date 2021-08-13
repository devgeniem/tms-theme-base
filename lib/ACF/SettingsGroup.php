<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use \Geniem\ACF\Exception;
use \Geniem\ACF\Field;
use \Geniem\ACF\Group;
use \Geniem\ACF\RuleGroup;
use TMS\Theme\Base\ACF\Fields\ArchiveSettingsTab;
use TMS\Theme\Base\ACF\Fields\BlogArticleSettingsTab;
use TMS\Theme\Base\ACF\Fields\ContactsSettingsTab;
use TMS\Theme\Base\ACF\Fields\Error404SettingsTab;
use TMS\Theme\Base\ACF\Fields\FooterSettingsTab;
use TMS\Theme\Base\ACF\Fields\HeaderSettingsTab;
use TMS\Theme\Base\ACF\Fields\MapSettingsTab;
use TMS\Theme\Base\ACF\Fields\SocialMediaSettingsTab;
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
                        new HeaderSettingsTab( '', $field_group->get_key() ),
                        new FooterSettingsTab( '', $field_group->get_key() ),
                        ( new Fields\ThemeColorTab( '', $field_group->get_key() ) ),
                        new MapSettingsTab( '', $field_group->get_key() ),
                        new SocialMediaSettingsTab( '', $field_group->get_key() ),
                        new Error404SettingsTab( '', $field_group->get_key() ),
                        new ArchiveSettingsTab( '', $field_group->get_key() ),
                        $this->get_events_fields( $field_group->get_key() ),
                        $this->get_page_fields( $field_group->get_key() ),
                        $this->get_exception_notice_fields( $field_group->get_key() ),
                        new BlogArticleSettingsTab( '', $field_group->get_key() ),
                        new ContactsSettingsTab( '', $field_group->get_key() ),
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
     * Get events fields
     * Get page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_events_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                           => 'Tapahtumat',
            'events_default_image'          => [
                'title'        => 'Oletuskuva',
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

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $image_field = ( new Field\Image( $strings['events_default_image']['title'] ) )
            ->set_key( "${key}_events_default_image" )
            ->set_name( 'events_default_image' )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['events_default_image']['instructions'] );

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

        $tab->add_fields( [
            $image_field,
            $events_page_field,
            $show_event_calendars_field,
        ] );

        return $tab;
    }

    /**
     * Get page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_page_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                       => 'Sisältösivut',
            'enable_sibling_navigation' => [
                'title'        => 'Rinnakkaissivujen navigointi',
                'instructions' => 'Esitetään sivujen alasivuilla ennen alatunnistetta.',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $display_siblings = ( new Field\TrueFalse( $strings['enable_sibling_navigation']['title'] ) )
            ->set_key( "${key}_enable_sibling_navigation" )
            ->set_name( 'enable_sibling_navigation' )
            ->set_default_value( false )
            ->use_ui()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['enable_sibling_navigation']['instructions'] );

        $tab->add_fields( [
            $display_siblings,
        ] );

        return $tab;
    }

    /**
     * Get exception notice fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_exception_notice_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'  => 'Poikkeusilmotus',
            'text' => [
                'title'        => 'Teksti',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $exception_text_field = ( new Field\Textarea( $strings['text']['title'] ) )
            ->set_key( "${key}_exception_text" )
            ->set_name( 'exception_text' )
            ->set_rows( 2 )
            ->set_wrapper_width( 50 )
            ->set_maxlength( 200 )
            ->set_instructions( $strings['text']['instructions'] );

        $tab->add_fields( [
            $exception_text_field,
        ] );

        return $tab;
    }
}

( new SettingsGroup() );
