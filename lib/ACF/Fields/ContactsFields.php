<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Integrations\Tampere\PersonApiController;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\Contact;

/**
 * Class ContactsFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ContactsFields extends \Geniem\ACF\Field\Group {

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        add_filter( 'acf/load_field/name=api_contacts', function ( $field ) {
            $api      = new PersonApiController();
            $contacts = $api->validate_result_set( $api->get() );

            if ( empty( $contacts ) ) {
                return $field;
            }

            foreach ( $contacts as $contact ) {
                $field['choices'][ $contact->id ] = sprintf(
                    '%s %s',
                    $contact->field_first_names,
                    $contact->field_last_name
                );
            }

            return $field;
        } );
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title'        => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'  => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'contacts'     => [
                'label'        => 'Yhteystiedot',
                'instructions' => '',
            ],
            'api_contacts' => [
                'label'        => 'Tampere-sivuston yhteystiedot',
                'instructions' => '',
            ],
            'fields'       => [
                'label'         => 'Näytettävät kentät',
                'instructions'  => '',
                'choices'       => [
                    'image'                     => 'Kuva',
                    'title'                     => 'Titteli',
                    'first_name'                => 'Etunimi',
                    'last_name'                 => 'Sukunimi',
                    'phone_repeater'            => 'Puhelinnumero',
                    'email'                     => 'Sähköpostiosoite',
                    'office'                    => 'Toimipiste',
                    'domain'                    => 'Toimialue',
                    'unit'                      => 'Yksikkö',
                    'visiting_address_street'   => 'Käyntiosoite: Katuosoite ja numero / PL',
                    'visiting_address_zip_code' => 'Käyntiosoite: Postinumero',
                    'visiting_address_city'     => 'Käyntiosoite: Postitoimipaikka',
                    'mail_address_street'       => 'Postiosoite: Katuosoite ja numero / PL',
                    'mail_address_zip_code'     => 'Postiosoite: Postinumero',
                    'mail_address_city'         => 'Postiosoite: Postitoimipaikka',
                    'additional_info_top'       => 'Lisätieto 1',
                    'additional_info_bottom'    => 'Lisätieto 2',
                ],
                'default_value' => [
                    'title',
                    'first_name',
                    'last_name',
                    'phone_repeater',
                    'email',
                ],
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_rows( 4 )
            ->set_new_lines( 'wpautop' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['description']['instructions'] );

        $api_contacts_field = ( new Field\Select( $strings['api_contacts']['label'] ) )
            ->set_key( "${key}_api_contacts" )
            ->set_name( 'api_contacts' )
            ->allow_multiple()
            ->allow_null()
            ->use_ui()
            ->set_instructions( $strings['api_contacts']['instructions'] );

        $contacts_field = ( new Field\Relationship( $strings['contacts']['label'] ) )
            ->set_key( "${key}_contacts" )
            ->set_name( 'contacts' )
            ->set_post_types( [ Contact::SLUG ] )
            ->set_return_format( 'id' )
            ->set_instructions( $strings['contacts']['instructions'] );

        $fields_field = ( new Field\Checkbox( $strings['fields']['label'] ) )
            ->set_key( "${key}_fields" )
            ->set_name( 'fields' )
            ->set_choices( $strings['fields']['choices'] )
            ->set_default_value( $strings['fields']['default_value'] )
            ->set_instructions( $strings['fields']['instructions'] );

        return [
            $title_field,
            $description_field,
            $api_contacts_field,
            $contacts_field,
            $fields_field,
        ];
    }
}
