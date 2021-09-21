<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Integrations\Tampere;

/**
 * Person API Controller
 */
class PersonApiController extends ApiController {

    /**
     * API slug
     */
    const SLUG = 'person';

    /**
     * Get endpoint slug
     *
     * @return string
     */
    protected function get_slug() : string {
        return self::SLUG;
    }

    /**
     * Validate results set from API.
     *
     * @param mixed $contacts Contacts from API.
     *
     * @return array
     */
    public function validate_result_set( $contacts ) : array {
        if ( empty( $contacts ) || ! is_array( $contacts ) ) {
            return [];
        }

        return array_filter( $contacts, function ( $contact ) {
            if ( ! isset( $contact->field_first_names, $contact->field_last_name ) ) {
                return false;
            }

            return true;
        } );
    }
}
