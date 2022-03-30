<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Integrations\Tampere;

/**
 * PersonFacade for API to WP
 */
class PersonFacade {

    /**
     * Fields
     *
     * @var object
     */
    private object $fields;

    /**
     * Constructor
     *
     * @param object $fields API Contact fields.
     */
    public function __construct( object $fields ) {
        return $this->set_fields( $fields );
    }

    /**
     * Set fields
     *
     * @param object $fields API Contact fields.
     *
     * @return object
     */
    public function set_fields( object $fields ) : object {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get fields
     *
     * @return object
     */
    public function get_fields() : object {
        return $this->fields;
    }

    /**
     * Format API response item to contact
     *
     * @param string|null $default_image Default image url.
     *
     * @return array
     */
    public function to_contact( ?string $default_image = null ) : array {
        $fields = $this->fields;

        $data = [
            'id'                        => $fields->id ?? '',
            'image'                     => $fields->field_image->field_media_image->image_full_url ?? $default_image,
            'first_name'                => $fields->field_first_names ?? '',
            'last_name'                 => $fields->field_last_name ?? '',
            'title'                     => $fields->field_hr_title->name ?? '',
            'phone_repeater'            => [],
            'email'                     => $fields->field_email ?? '',
            'additional_info_top'       => $fields->field_additional_information ?? '',
            'visiting_address_street'   => $fields->field_address_street->address_line1 ?? '',
            'visiting_address_zip_code' => $fields->field_address_street->postal_code ?? '',
            'visiting_address_city'     => $fields->field_address_street->locality ?? '',
            'mail_address_street'       => $fields->field_address_postal->address_line1 ?? '',
            'mail_address_zip_code'     => $fields->field_address_postal->postal_code ?? '',
            'mail_address_city'         => $fields->field_address_postal->locality ?? '',
            'domain'                    => $fields->field_hr_cost_center->name ?? '',
            'unit'                      => $fields->field_hr_organizational_unit->name ?? '',
            'office'                    => $fields->field_place->title ?? '',
        ];

        $data = $this->handle_phone_numbers( $data, $fields );

        return $data;
    }

    /**
     * Handle contact phone numbers
     *
     * @param array  $data   Normalized contact data.
     * @param object $fields API contact fields.
     *
     * @return array
     */
    private function handle_phone_numbers( array $data, $fields ) : array {
        if ( ! empty( $fields->field_phone ) ) {
            $data['phone_repeater'][] = [
                'phone_text'   => $fields->phone_supplementary ?? '',
                'phone_number' => $fields->field_phone,
            ];
        }

        if ( ! empty( $fields->field_additinal_phones ) ) {
            foreach ( $fields->field_additinal_phones as $phone ) {
                $data['phone_repeater'][] = [
                    'phone_text'   => $phone->telephone_supplementary ?? '',
                    'phone_number' => $phone->telephone_number,
                ];
            }
        }

        return $data;
    }
}
