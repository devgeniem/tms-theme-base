<?php
/**
 * Copyright (c) 2023. Geniem Oy
 */

namespace TMS\Theme\Base;

use Requests;
use Response;

/**
 * Class EventzClient
 *
 * @package Geniem\Eventz
 * @see https://backend.ver5.eventz.today/api/docs/ for API documentation.
 */
class EventzClient {

    /**
     * Type event.
     */
    public const TYPE_EVENT = 'event';

    /**
     * Type organizer.
     */
    public const TYPE_ORGANIZER = 'organizer';

    /**
     * API base URI.
     *
     * @var string
     */
    private string $base_uri;

    /**
     * API key.
     *
     * @var string
     */
    private string $api_key;

    /**
     * Last request response.
     *
     */
    private $last_response = null;

    /**
     * EventzClient constructor.
     *
     * @param string $base_uri API Base URI.
     * @param string $api_key API key.
     * @throws \InvalidArgumentException If base URI or API key is not valid.
     */
    public function __construct( string $base_uri, string $api_key ) {
        $base_uri = filter_var( $base_uri, FILTER_SANITIZE_URL );
        if ( empty( $base_uri ) || ! filter_var( $base_uri, FILTER_VALIDATE_URL ) ) {
            throw new \InvalidArgumentException( 'Base URI is not valid.' );
        }
        if ( empty( $api_key ) ) {
            throw new \InvalidArgumentException( 'API key is not valid.' );
        }

        $this->base_uri = rtrim( $base_uri, '/\\' ) . '/'; // Add trailing slash if missing.
        $this->api_key  = $api_key;
    }

    /**
     * Search events from API.
     * Language is required in this endpoint so we use Finnish as default.
     *
     * @param array  $params Search parameters.
     * @param string $lang Language, default Finnish.
     * @return array|false
     */
    public function search_events( array $params = [], string $lang = 'fi' ) {
        $params['type']     = self::TYPE_EVENT;
        $params['language'] = $lang;

        return $this->search( $params );
    }

    /**
     * Search organizers from API.
     * Language is required in this endpoint so we use Finnish as default.
     *
     * @param array  $params Search parameters.
     * @param string $lang Language, default Finnish.
     * @return array|false
     */
    public function search_organizers( array $params = [], string $lang = 'fi' ) {
        $params['type']     = self::TYPE_ORGANIZER;
        $params['language'] = $lang;

        return $this->search( $params );
    }

    /**
     * Search items from API.
     *
     * @param array $params Search parameters.
     * @return array|false
     */
    protected function search( array $params = [] ) {
        $endpoint = 'api/public/search';
        $body     = $this->get( $endpoint, $params );

        return $body->data ?? false;
    }

    /**
     * Get single item from API.
     * Language is required in this endpoint so we use Finnish as default.
     *
     * @param string $id Item ID.
     * @param string $lang Language.
     * @return \stdClass|false
     */
    public function get_item( string $id, string $lang = 'fi' ) {
        $endpoint = "api/public/item/{$id}";
        $params   = [
            'language' => $lang,
        ];

        $body = $this->get( $endpoint, $params );

        return $body->data ?? false;
    }

    /**
     * Get categories from API.
     * Language defaults to the site's default language.
     *
     * @param string $lang The language.
     * @return array|false
     */
    public function get_categories( string $lang = '' ) {
        $endpoint = 'api/public/site/categories';
        $params   = [];

        if ( ! empty( $lang ) ) {
            $params['language'] = $lang;
        }

        $body = $this->get( $endpoint, $params );

        return $body->data->site_categories ?? false;
    }

    /**
     * Get areas from API.
     * Language defaults to the site's default language.
     *
     * @param string $lang The language.
     * @return array|false
     */
    public function get_areas( string $lang = '' ) {
        $endpoint = 'api/public/site/areas';
        $params   = [];

        if ( ! empty( $lang ) ) {
            $params['language'] = $lang;
        }

        $body = $this->get( $endpoint, $params );

        return $body->data->areas ?? false;
    }

    /**
     * Get tags from API.
     * Language defaults to the site's default language.
     *
     * @param string $lang The language.
     * @return array|false
     */
    public function get_tags( string $lang = '' ) {
        $endpoint = 'api/public/site/tags';
        $params   = [];

        if ( ! empty( $lang ) ) {
            $params['language'] = $lang;
        }

        $body = $this->get( $endpoint, $params );

        return $body->data->tags ?? false;
    }

    /**
     * Get targets from API.
     * Language defaults to the site's default language.
     *
     * @param string $lang The language.
     * @return array|false
     */
    public function get_targets( string $lang = '' ) {
        $endpoint = 'api/public/site/targets';
        $params   = [];

        if ( ! empty( $lang ) ) {
            $params['language'] = $lang;
        }

        $body = $this->get( $endpoint, $params );
        return $body->data->targets ?? false;
    }

    /**
     * Search events by host ids from API.
     * Language is required in this endpoint so we use Finnish as default.
     *
     * @param array  $params Search parameters.
     * @param string $lang Language, default Finnish.
     * @return array|false
     */
    public function search_events_by_host( array $params = [], string $lang = 'fi' ) {
        $endpoint           = 'api/public/content-by-host';
        $params['language'] = $lang;

        $body = $this->get( $endpoint, $params );
        return $body->data->items ?? false;
    }

    /**
     * Get items from the API.
     *
     * @param string $endpoint The endpoint.
     * @param array  $params The query parameters.
     * @return \stdClass|false
     */
    public function get( string $endpoint, array $params = [] ) {
        $api_url = $this->base_uri . $endpoint . '?' . $this->to_query_parameters( $params );
        $body    = $this->do_get_request( $api_url );

        return empty( $body )
            ? false
            : self::decode_contents( $body );
    }

    /**
     * Get full last response or one of its properties.
     *
     * @param string $property Property to get from the response.
     * @return mixed The full response or one of its properties, null if no request has been made.
     */
    public function get_last_response( $property = '' ) {
        if ( empty( $property ) ) {
            return $this->last_response;
        }

        return $this->last_response->{$property} ?? null;
    }

    /**
     * Decode the API Response.
     *
     * @param string $body Response body.
     *
     * @return \stdClass
     * @throws \JsonException If JSON Decode fails.
     */
    public static function decode_contents( string $body = '' ) : \stdClass {
        $body = json_decode( $body, false, 512, JSON_THROW_ON_ERROR );

        // Multiple items, like from searching.
        if ( isset( $body->meta, $body->data ) ) {
            return $body;
        }

        // Single item, or error message.
        $output       = new \stdClass();
        $output->data = $body;
        $output->meta = [];

        return $output;
    }

    /**
     * Inject API key to parameters and return query string.
     *
     * @param array $params The query parameters.
     * @return string
     */
    protected function to_query_parameters( array $params = [] ) {
        $default_params = [
            'apiKey' => $this->api_key,
        ];
        $params         = array_merge( $default_params, $params );

        return http_build_query( $params );
    }

    /**
     * Get request body from API by URL.
     *
     * @param string $api_url API URL to fetch from.
     * @return string|false
     * @throws EventzException If request fails.
     */
    public function do_get_request( string $api_url = '' ) {
        if ( empty( $api_url ) || ! filter_var( $api_url, FILTER_VALIDATE_URL ) ) {
            return false;
        }

        $headers = [];
        $options = [];

        $payload = Requests::get( $api_url );

        // Save last response to class property.
        $this->last_response = $payload;

        $status_code = $payload->status_code;

        if ( ! in_array( $status_code, [ 200, 201 ], true ) ) {
            throw new EventzException(
                sprintf( '%s: %s', $api_url, $payload->body ?? 'Unknown error' ),
                $status_code
            );
        }

        return $payload->body ?? '';
    }
}
