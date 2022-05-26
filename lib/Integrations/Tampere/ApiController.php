<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Tredu\Integrations\Tampere;

use TMS\Theme\Tredu\Logger;

/**
 * Tampere API Controller
 */
abstract class ApiController {

    /**
     * Output file path.
     */
    const OUTPUT_PATH = '/tmp/';

    /**
     * Get API base url
     *
     * @return string|null
     */
    protected function get_api_base_url() : ?string {
        $url = env( 'TAMPERE_API_URL' );

        if ( DPT_PLL_ACTIVE && pll_current_language() === 'en' ) {
            $url .= '/en';
        }

        $url .= '/api/node';

        return $url;
    }

    /**
     * Get endpoint slug
     *
     * @return string
     */
    abstract protected function get_slug() : string;

    /**
     * Do an API request
     *
     * @param string|array $path         Request path.
     * @param array        $params       Request query parameters.
     * @param array        $request_args Request args.
     *
     * @return bool|mixed
     */
    public function do_request( $path, array $params = [], array $request_args = [] ) {
        $base_url = $this->get_api_base_url();

        if ( empty( $base_url ) ) {
            return false;
        }

        if ( is_array( $path ) ) {
            $path = implode( '/', $path );
        }

        $request_url = \add_query_arg(
            $params,
            sprintf(
                '%s/%s?',
                $base_url,
                $path
            )
        );

        $cache_key = 'tampere-drupal-' . md5( $request_url );
        $response  = \wp_cache_get( $cache_key, 'API', true );

        if ( ! empty( $response ) ) {
            return $response;
        }

        $response = \wp_remote_get( $request_url, $request_args );

        if ( 200 !== \wp_remote_retrieve_response_code( $response ) ) {
            ( new Logger() )->error( print_r( $response, true ) ); // phpcs:ignore

            return false;
        }

        $response_body_json = \json_decode( wp_remote_retrieve_body( $response ) );

        if ( ! empty( $response_body_json ) ) {
            wp_cache_set( $cache_key, $response_body_json, 'API', MINUTE_IN_SECONDS * 15 );
        }

        return $response_body_json;
    }

    /**
     * Is the API response valid.
     *
     * @param mixed $response API response body.
     *
     * @return bool
     */
    public function is_valid_response( $response ) : bool {
        return ! ( ! $response || empty( $response ) );
    }

    /**
     * Get all pages from API
     *
     * @return mixed
     */
    public function get() {
        $cache_key = 'tampere-drupal-' . $this->get_slug();

        if ( DPT_PLL_ACTIVE ) {
            $cache_key .= '-' . pll_current_language();
        }

        $results = \wp_cache_get( $cache_key, 'API' );

        if ( $results ) {
            return $results;
        }
        else {
            $file_results = $this->read_from_file( "$cache_key.json" );

            if ( ! empty( $file_results ) ) {
                \wp_cache_set( $cache_key, $file_results, 'API', HOUR_IN_SECONDS * 6 );

                return $file_results;
            }
        }

        $args = [
            'headers' => [],
            'timeout' => 30,
        ];

        $basic_auth_key = env( 'TAMPERE_API_AUTH' );

        if ( ! empty( $basic_auth_key ) ) {
            $args['headers']['Authorization'] = 'Basic ' . base64_encode( $basic_auth_key ); // phpcs:ignore
        }

        $params = [
            'filter[status]' => 1,
            'page[limit]'    => 50,
        ];

        $results = $this->do_get( $this->get_slug(), [], $params, $args );

        if ( ! empty( $results ) ) {
            wp_cache_set( $cache_key, $results, 'API', HOUR_IN_SECONDS * 6 );

            $this->save_to_file( $results, "$cache_key.json" );
        }

        return $results;
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

        $data        = array_merge( $data, $response->data ?? [] );
        $query_parts = $this->get_link_query_parts(
            $response->links->next->href ?? ''
        );

        return empty( $query_parts )
            ? $data
            : $this->do_get( $slug, $data, $query_parts ?? [], $args );
    }

    /**
     * Get query params from link
     *
     * @param string $href Link.
     *
     * @return array
     */
    protected function get_link_query_parts( string $href ) : array {
        $parts = wp_parse_url( $href );

        if ( ! isset( $parts['query'] ) ) {
            return [];
        }

        parse_str( $parts['query'], $query_parts );

        return $query_parts;
    }

    /**
     * Attempt to read response from file.
     *
     * @param string $filename File name.
     *
     * @return false|mixed
     */
    protected function read_from_file( $filename ) {
        $file = self::OUTPUT_PATH . $filename;

        if ( ! file_exists( $file ) ) {
            return false;
        }

        $file_contents = file_get_contents( $file );

        return ! empty( $file_contents ) ? json_decode( $file_contents, true ) : false;
    }

    /**
     * Encode data to JSON & write to file.
     *
     * @param array  $data     Data.
     * @param string $filename File name.
     *
     * @return bool True on success.
     */
    protected function save_to_file( $data, $filename ) : bool {
        $success = ! empty( file_put_contents( self::OUTPUT_PATH . $filename, json_encode( $data ) ) );

        if ( ! $success ) {
            ( new Logger() )->error( 'TMS\Theme\Tredu\Integrations\Tampere\ApiController: Failed to write JSON file.' );
        }

        return $success;
    }
}
