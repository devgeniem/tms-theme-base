<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;

/**
 * Class EventsFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class EventsFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Events';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/events/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $layout ACF Layout data.
     * @param bool  $all    Get all results.
     *
     * @return array
     */
    public function format( array $layout, bool $all = false ) : array {
        $query_params            = $this->format_query_params( $layout );
        $query_params['include'] = 'organization,location,keywords';
        $events                  = $this->get_events( $query_params, $all );

        if ( empty( $events ) ) {
            return $layout;
        }

        if ( ! $layout['show_images'] ) {
            $events = array_map( function ( $item ) {
                $item['image'] = false;

                return $item;
            }, $events );
        }
        else {
            $default_image = Settings::get_setting( 'events_default_image' );

            if ( ! empty( $default_image ) ) {
                $events = array_map( function ( $item ) use ( $default_image ) {
                    if ( empty( $item['image'] ) ) {
                        $item['image'] = wp_get_attachment_image_url( $default_image, 'large' );
                    }

                    return $item;
                }, $events );
            }
        }

        $layout['events'] = $events;

        return $layout;
    }

    /**
     * Format query params
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    private function format_query_params( array $layout ) : array {
        $query_params = [
            'start'     => null,
            'end'       => null,
            'keyword'   => null,
            'location'  => null,
            'publisher' => null,
            'sort'      => null,
            'page_size' => null,
            'text'      => null,
        ];

        foreach ( $layout as $key => $value ) {
            if ( ! array_key_exists( $key, $query_params ) ) {
                continue;
            }

            if ( empty( $value ) ) {
                unset( $query_params[ $key ] );
            }
            else {
                $query_params[ $key ] = is_array( $value )
                    ? implode( ',', $value )
                    : $value;
            }
        }

        return $query_params;
    }

    /**
     * Get events
     *
     * @param array $query_params API query params.
     * @param bool  $all          Get all results.
     *
     * @return array|null
     */
    private function get_events( array $query_params, bool $all = false ) : ?array {
        $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        try {
            $response = $all
                ? $client->get_all( 'event', $query_params )
                : $client->get( 'event', $query_params );

            if ( empty( $response ) ) {
                return null;
            }

            return array_map( fn( $item ) => LinkedEvents::normalize_event( $item ), $response );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }
}
