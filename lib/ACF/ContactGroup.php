<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF;

use Geniem\ACF\Exception;
use Geniem\ACF\Group;
use Geniem\ACF\RuleGroup;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Layouts;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType;

/**
 * Class ContactGroup
 *
 * @package TMS\Theme\Base\ACF
 */
class ContactGroup {

    /**
     * PageGroup constructor.
     */
    public function __construct() {
        \add_action(
            'init',
            \Closure::fromCallable( [ $this, 'register_fields' ] )
        );

        \add_filter(
            'redipress/index/posts/schema_fields',
            \Closure::fromCallable( [ $this, 'add_redipress_fields' ] )
        );
    }

    /**
     * Register fields
     */
    protected function register_fields() : void {
        try {
            $group_title = 'Yhteystiedon tiedot';

            $field_group = ( new Group( $group_title ) )
                ->set_key( 'fg_contact' );

            $rule_group = ( new RuleGroup() )
                ->add_rule( 'post_type', '==', PostType\Contact::SLUG );

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
                    $this->get_contact_fields( $field_group->get_key() ),
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
     * Get components fields
     *
     * @param string $key Field group key.
     *
     * @return array
     * @throws Exception In case of invalid option.
     */
    protected function get_contact_fields( string $key ) : array {
        $strings = [
            'image'                     => [
                'title'        => 'Kuva',
                'instructions' => '',
            ],
            'title'                     => [
                'title'        => 'Titteli',
                'instructions' => '',
            ],
            'first_name'                => [
                'title'        => 'Etunimi',
                'instructions' => '',
            ],
            'last_name'                 => [
                'title'        => 'Sukunimi',
                'instructions' => '',
            ],
            'phone_repeater'            => [
                'title'        => 'Puhelinnumerot',
                'instructions' => '',
            ],
            'phone_text'                => [
                'title'        => 'Puhelinnumeron selite',
                'instructions' => '',
            ],
            'phone_number'              => [
                'title'        => 'Puhelinnumero',
                'instructions' => '',
            ],
            'email'                     => [
                'title'        => 'Sähköpostiosoite',
                'instructions' => '',
            ],
            'office'                    => [
                'title'        => 'Toimipiste',
                'instructions' => '',
            ],
            'domain'                    => [
                'title'        => 'Toimialue',
                'instructions' => '',
            ],
            'unit'                      => [
                'title'        => 'Yksikkö',
                'instructions' => '',
            ],
            'visiting_address'          => [
                'title'        => 'Käyntiosoite',
                'instructions' => '',
            ],
            'visiting_address_street'   => [
                'title'        => 'Käyntiosoite: Katuosoite ja numero / PL',
                'instructions' => '',
            ],
            'visiting_address_zip_code' => [
                'title'        => 'Käyntiosoite: Postinumero',
                'instructions' => '',
            ],
            'visiting_address_city'     => [
                'title'        => 'Käyntiosoite: Postitoimipaikka',
                'instructions' => '',
            ],
            'mail_address'              => [
                'title'        => 'Postiosoite',
                'instructions' => '',
            ],
            'mail_address_street'       => [
                'title'        => 'Postiosoite: Katuosoite ja numero / PL',
                'instructions' => '',
            ],
            'mail_address_zip_code'     => [
                'title'        => 'Postiosoite: Postinumero',
                'instructions' => '',
            ],
            'mail_address_city'         => [
                'title'        => 'Postiosoite: Postitoimipaikka',
                'instructions' => '',
            ],
            'additional_info_top'       => [
                'title'        => 'Lisätieto 1',
                'instructions' => 'Näytetään yhteystiedon tittelin yläpuolella',
            ],
            'additional_info_bottom'    => [
                'title'        => 'Lisätieto 2',
                'instructions' => ' Näytetään yhteystiedon tietojen viimeisenä',
            ],
        ];

        $image_field = ( new Field\Image( $strings['image']['title'] ) )
            ->set_key( "{$key}_image" )
            ->set_name( 'image' )
            ->set_return_format( 'id' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['image']['instructions'] );

        $title_field = ( new Field\Text( $strings['title']['title'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['title']['instructions'] );

        $first_name_field = ( new Field\Text( $strings['first_name']['title'] ) )
            ->set_key( "{$key}_first_name" )
            ->set_name( 'first_name' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['first_name']['instructions'] )
            ->set_required();

        $last_name_field = ( new Field\Text( $strings['last_name']['title'] ) )
            ->set_key( "{$key}_last_name" )
            ->set_name( 'last_name' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->redipress_add_queryable()
            ->set_instructions( $strings['last_name']['instructions'] )
            ->set_required();

        $phone_repeater_field = ( new Field\Repeater( $strings['phone_repeater']['title'] ) )
            ->set_key( "{$key}_phone_repeater" )
            ->set_name( 'phone_repeater' )
            ->set_min( 1 )
            ->set_max( 5 )
            ->set_instructions( $strings['phone_repeater']['instructions'] );

        $phone_text_field = ( new Field\Text( $strings['phone_text']['title'] ) )
            ->set_key( "{$key}_phone_text" )
            ->set_name( 'phone_text' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['phone_text']['instructions'] );

        $phone_repeater_field->add_field( $phone_text_field );

        $phone_number_field = ( new Field\Text( $strings['phone_number']['title'] ) )
            ->set_key( "{$key}_phone_number" )
            ->set_name( 'phone_number' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['phone_number']['instructions'] );

        $phone_repeater_field->add_field( $phone_number_field );

        $email_field = ( new Field\Email( $strings['email']['title'] ) )
            ->set_key( "{$key}_email" )
            ->set_name( 'email' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['email']['instructions'] );

        $office_field = ( new Field\Select( $strings['office']['title'] ) )
            ->set_key( "{$key}_office" )
            ->set_name( 'office' )
            ->set_wrapper_width( 50 )
            ->disable()
            ->set_instructions( $strings['office']['instructions'] );

        $domain_field = ( new Field\Text( $strings['domain']['title'] ) )
            ->set_key( "{$key}_domain" )
            ->set_name( 'domain' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['domain']['instructions'] );

        $unit_field = ( new Field\Text( $strings['unit']['title'] ) )
            ->set_key( "{$key}_unit" )
            ->set_name( 'unit' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['unit']['instructions'] );

        $visiting_message_field = ( new Field\Message( $strings['visiting_address']['title'] ) )
            ->set_key( "{$key}_visiting_address" )
            ->set_name( 'visiting_address' );

        $visiting_address_street_field = ( new Field\Text( $strings['visiting_address_street']['title'] ) )
            ->set_key( "{$key}_visiting_address_street" )
            ->set_name( 'visiting_address_street' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['visiting_address_street']['instructions'] );

        $visiting_address_zip_code_field = ( new Field\Text( $strings['visiting_address_zip_code']['title'] ) )
            ->set_key( "{$key}_visiting_address_zip_code" )
            ->set_name( 'visiting_address_zip_code' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['visiting_address_zip_code']['instructions'] );

        $visiting_address_city_field = ( new Field\Text( $strings['visiting_address_city']['title'] ) )
            ->set_key( "{$key}_visiting_address_city" )
            ->set_name( 'visiting_address_city' )
            ->set_instructions( $strings['visiting_address_city']['instructions'] );

        $mail_message_field = ( new Field\Message( $strings['mail_address']['title'] ) )
            ->set_key( "{$key}_mail_address" )
            ->set_name( 'mail_address' );

        $mail_address_street_field = ( new Field\Text( $strings['mail_address_street']['title'] ) )
            ->set_key( "{$key}_mail_address_street" )
            ->set_name( 'mail_address_street' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['mail_address_street']['instructions'] );

        $mail_address_zip_code_field = ( new Field\Text( $strings['mail_address_zip_code']['title'] ) )
            ->set_key( "{$key}_mail_address_zip_code" )
            ->set_name( 'mail_address_zip_code' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['mail_address_zip_code']['instructions'] );

        $mail_address_city_field = ( new Field\Text( $strings['mail_address_city']['title'] ) )
            ->set_key( "{$key}_mail_address_city" )
            ->set_name( 'mail_address_city' )
            ->set_instructions( $strings['mail_address_city']['instructions'] );

        $additional_info_top = ( new Field\Textarea( $strings['additional_info_top']['title'] ) )
            ->set_key( "{$key}_additional_info_top" )
            ->set_name( 'additional_info_top' )
            ->set_new_lines( 'wpautop' )
            ->set_rows( 4 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['additional_info_top']['instructions'] );

        $additional_info_bottom = ( new Field\Textarea( $strings['additional_info_bottom']['title'] ) )
            ->set_key( "{$key}_additional_info_bottom" )
            ->set_name( 'additional_info_bottom' )
            ->set_new_lines( 'wpautop' )
            ->set_rows( 4 )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['additional_info_bottom']['instructions'] );

        return [
            $image_field,
            $title_field,
            $first_name_field,
            $last_name_field,
            $phone_repeater_field,
            $email_field,
            $office_field,
            $domain_field,
            $unit_field,
            $visiting_message_field,
            $visiting_address_street_field,
            $visiting_address_zip_code_field,
            $visiting_address_city_field,
            $mail_message_field,
            $mail_address_street_field,
            $mail_address_zip_code_field,
            $mail_address_city_field,
            $additional_info_top,
            $additional_info_bottom,
        ];
    }

    /**
     * Add RediPress fields
     *
     * @param array $fields array of fields.
     *
     * @return mixed
     * @throws \Exception In case of missing option.
     */
    protected function add_redipress_fields( $fields ) {
        $fields[] = new \Geniem\RediPress\Entity\TextField( [
            'name'     => 'last_name',
            'sortable' => true,
        ] );

        return $fields;
    }
}

( new ContactGroup() );
