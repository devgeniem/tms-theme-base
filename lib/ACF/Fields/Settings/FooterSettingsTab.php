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
 * Class FooterSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class FooterSettingsTab extends Tab {

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

            $this->add_fields( [
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
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
