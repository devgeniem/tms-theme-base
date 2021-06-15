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
                        $this->get_footer_fields( $field_group->get_key() ),
                        $this->get_map_fields( $field_group->get_key() ),
                        $this->get_social_media_sharing_fields( $field_group->get_key() ),
                        $this->get_404_fields( $field_group->get_key() ),
                        $this->get_archive_fields( $field_group->get_key() ),
                        $this->get_events_fields( $field_group->get_key() ),
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
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

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

        $tab->add_fields( [
            $logo_field,
            $brand_logo_field,
            $tagline_field,
            $lang_nav_display_field,
            $hide_main_nav_field,
            $limit_nav_depth_field,
        ] );

        return $tab;
    }

    /**
     * Get footer fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_footer_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'           => 'Alatunniste',
            'footer_logo'   => [
                'title'        => 'Logo',
                'instructions' => '',
            ],
            'contact_title' => [
                'title'        => 'Yhteystietojen osoite',
                'instructions' => '',
            ],
            'address'       => [
                'title'        => 'Osoite',
                'instructions' => '',
            ],
            'email'         => [
                'title'        => 'Sähköposti',
                'instructions' => '',
            ],
            'phone'         => [
                'title'        => 'Puhelinnumero',
                'instructions' => '',
            ],
            'link_columns'  => [
                'title'        => 'Linkkipalstat',
                'instructions' => '',
                'button_label' => 'Lisää linkkipalsta',
            ],
            'column_title'  => [
                'title'        => 'Otsikko',
                'instructions' => '',
            ],
            'link_column'   => [
                'title'        => 'Linkkipalsta',
                'instructions' => '',
                'button_label' => 'Lisää linkki',
            ],
            'link'          => [
                'title'        => 'Linkki',
                'instructions' => '',
            ],
            'privacy_links' => [
                'title'        => 'Tietosuojalinkit',
                'instructions' => 'Saavutettavuusselosteet ja tietosuojalinkit',
                'button_label' => 'Lisää linkki',
            ],
            'privacy_link'  => [
                'title'        => 'Linkki',
                'instructions' => '',
            ],
            'hero_credits'  => [
                'title'        => 'Etusivun hero-kuvan tekijätieto tai kuvaajan nimi',
                'instructions' => '',
            ],
            'copyright'     => [
                'title'        => 'Copyright-teksti',
                'instructions' => '&copy; ja vuosi lisätään automaattisesti syötettyä tekstiä ennen',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $logo_field = ( new Field\Image( $strings['footer_logo']['title'] ) )
            ->set_key( "${key}_footer_logo" )
            ->set_name( 'footer_logo' )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['footer_logo']['instructions'] );

        $contact_title_field = ( new Field\Text( $strings['contact_title']['title'] ) )
            ->set_key( "${key}_contact_title" )
            ->set_name( 'contact_title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['contact_title']['instructions'] );

        $address_field = ( new Field\Textarea( $strings['address']['title'] ) )
            ->set_key( "${key}_address" )
            ->set_name( 'address' )
            ->set_new_lines( 'wpautop' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['address']['instructions'] );

        $email_field = ( new Field\Email( $strings['email']['title'] ) )
            ->set_key( "${key}_email" )
            ->set_name( 'email' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['email']['instructions'] );

        $phone_field = ( new Field\Text( $strings['phone']['title'] ) )
            ->set_key( "${key}_phone" )
            ->set_name( 'phone' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['phone']['instructions'] );

        $link_columns_field = ( new Field\Repeater( $strings['link_columns']['title'] ) )
            ->set_key( "${key}_link_columns" )
            ->set_name( 'link_columns' )
            ->set_layout( 'block' )
            ->set_max( 3 )
            ->set_button_label( $strings['link_columns']['button_label'] )
            ->set_instructions( $strings['link_columns']['instructions'] );

        $column_title_field = ( new Field\Text( $strings['column_title']['title'] ) )
            ->set_key( "${key}_column_title" )
            ->set_name( 'column_title' )
            ->set_instructions( $strings['column_title']['instructions'] );

        $link_columns_field->add_field( $column_title_field );

        $link_column_field = ( new Field\Repeater( $strings['link_column']['title'] ) )
            ->set_key( "${key}_link_column" )
            ->set_name( 'link_column' )
            ->set_button_label( $strings['link_column']['button_label'] )
            ->set_instructions( $strings['link_column']['instructions'] );

        $link_columns_field->add_field( $link_column_field );

        $link_field = ( new Field\Link( $strings['link']['title'] ) )
            ->set_key( "${key}_link" )
            ->set_name( 'link' )
            ->set_instructions( $strings['link']['instructions'] );

        $link_column_field->add_field( $link_field );

        $privacy_links_field = ( new Field\Repeater( $strings['privacy_links']['title'] ) )
            ->set_key( "${key}_privacy_links" )
            ->set_name( 'privacy_links' )
            ->set_button_label( $strings['privacy_links']['button_label'] )
            ->set_instructions( $strings['privacy_links']['instructions'] );

        $privacy_link_field = ( new Field\Link( $strings['privacy_link']['title'] ) )
            ->set_key( "${key}_privacy_link" )
            ->set_name( 'privacy_link' )
            ->set_instructions( $strings['privacy_link']['instructions'] );

        $privacy_links_field->add_field( $privacy_link_field );

        $hero_credits_field = ( new Field\Text( $strings['hero_credits']['title'] ) )
            ->set_key( "${key}_hero_credits" )
            ->set_name( 'hero_credits' )
            ->set_instructions( $strings['hero_credits']['instructions'] );

        $copyright_field = ( new Field\Text( $strings['copyright']['title'] ) )
            ->set_key( "${key}_copyright" )
            ->set_name( 'copyright' )
            ->set_instructions( $strings['copyright']['instructions'] );

        $tab->add_fields( [
            $logo_field,
            $contact_title_field,
            $address_field,
            $email_field,
            $phone_field,
            $link_columns_field,
            $privacy_links_field,
            $hero_credits_field,
            $copyright_field,
        ] );

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

    /**
     * Get 404 page fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_404_fields( string $key ) : Field\Tab {
        $strings = [
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
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

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
            ->set_instructions( $strings['404_image']['instructions'] );

        $tab->add_fields( [
            $title_field,
            $description_field,
            $image_field,
        ] );

        return $tab;
    }

    /**
     * Get archive fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_archive_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                => 'Arkistot',
            'archive_use_images' => [
                'title'        => 'Kuvat käytössä',
                'instructions' => '',
            ],
            'archive_view_type'  => [
                'title'        => 'Listaustyyli',
                'instructions' => '',
            ],
        ];

        $tab = ( new Field\Tab( $strings['tab'] ) )
            ->set_placement( 'left' );

        $use_images_field = ( new Field\TrueFalse( $strings['archive_use_images']['title'] ) )
            ->set_key( "${key}_archive_use_images" )
            ->set_name( 'archive_use_images' )
            ->set_default_value( true )
            ->use_ui()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['archive_use_images']['instructions'] );

        $view_type_field = ( new Field\Radio( $strings['archive_view_type']['title'] ) )
            ->set_key( "${key}_archive_view_type" )
            ->set_name( 'archive_view_type' )
            ->set_choices( [
                'grid' => 'Ruudukko',
                'list' => 'Lista',
            ] )
            ->set_default_value( 'grid' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['archive_view_type']['instructions'] );

        $tab->add_fields( [
            $use_images_field,
            $view_type_field,
        ] );

        return $tab;
    }

    /**
     * Get events fields
     *
     * @param string $key Field group key.
     *
     * @return Field\Tab
     * @throws Exception In case of invalid option.
     */
    protected function get_events_fields( string $key ) : Field\Tab {
        $strings = [
            'tab'                  => 'Tapahtumat',
            'events_default_image' => [
                'title'        => 'Oletuskuva',
                'instructions' => '',
            ],
            'events_page'          => [
                'title'        => 'Tapahtuma-sivu',
                'instructions' => 'Sivu, jolle on valittu Tapahtuma-sivupohja',
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

        $tab->add_fields( [
            $image_field,
            $events_page_field,
        ] );

        return $tab;
    }
}

( new SettingsGroup() );
