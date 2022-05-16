<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Integrations\Tampere;

use TMS\Theme\Base\Settings;

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
            if ( ! isset( $contact['first_name'], $contact['last_name'] ) ) {
                return false;
            }

            return true;
        } );
    }

    /**
     * Result set callback
     *
     * @param array $results API results.
     *
     * @return array[]
     */
    public function result_set_callback( $results ) {
        $default_image = Settings::get_setting( 'contacts_default_image' );

        return array_map( function ( $contact ) use ( $default_image ) {
            $facade = new PersonFacade( $contact );

            return $facade->prune( $facade->to_contact( $default_image ) );
        }, $results );
    }

    /**
     * Recursively get all pages from API.
     *
     * @param string $slug   API slug.
     * @param array  $data   Fetched persons.
     * @param array  $params Query params.
     * @param array  $args   Request arguments.
     *
     * @return array
     */
    protected function do_get( string $slug, array $data = [], array $params = [], array $args = [] ) {
        $response = $this->do_request( $slug, $params, $args );

        if ( ! $this->is_valid_response( $response ) ) {
            return $data;
        }

        $data = array_merge(
            $data,
            $this->result_set_callback( $response->data ?? [] )
        );

        $query_parts = $this->get_link_query_parts(
            $response->links->next->href ?? ''
        );

        return empty( $query_parts )
            ? $data
            : $this->do_get( $slug, $data, $query_parts ?? [], $args );
    }
}
