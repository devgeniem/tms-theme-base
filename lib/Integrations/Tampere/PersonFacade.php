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
     * @return array
     */
    public function to_contact() : array {
        return [
            'id'                        => $this->fields->id ?? '',
            'image'                     => $this->fields->field_image->field_media_image->image_full_url ?? '',
            'first_name'                => $this->fields->field_first_names ?? '',
            'last_name'                 => $this->fields->field_last_name ?? '',
            'title'                     => $this->fields->field_hr_title->name ?? '',
            'phone_repeater'            => '',
            'email'                     => $this->fields->field_email ?? '',
            'additional_info_top'       => $this->fields->field_additional_information ?? '',
            'visiting_address_street'   => $this->fields->field_address_street->address_line1 ?? '',
            'visiting_address_zip_code' => $this->fields->field_address_street->postal_code ?? '',
            'visiting_address_city'     => $this->fields->field_address_street->locality ?? '',
            'mail_address_street'       => $this->fields->field_address_postal->address_line1 ?? '',
            'mail_address_zip_code'     => $this->fields->field_address_postal->postal_code ?? '',
            'mail_address_city'         => $this->fields->field_address_postal->locality ?? '',
            'domain'                    => $this->fields->field_hr_cost_center->name ?? '',
            'unit'                      => $this->fields->field_hr_organizational_unit->name ?? '',
            'office'                    => $this->fields->field_place->title ?? '',
        ];
    }
}
