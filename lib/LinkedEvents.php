<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use Closure;
use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use TMS\Theme\Base\Interfaces\Controller;
use TMS\Theme\Base\PostType\DynamicEvent;

/**
 * Class LinkedEvents
 *
 * @package TMS\Theme\Base
 */
class LinkedEvents implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_action(
            'wp_ajax_event_search',
            Closure::fromCallable( [ $this, 'admin_event_search_callback' ] )
        );
    }

    /**
     * Admin event search callback
     */
    protected function admin_event_search_callback() : void {
        $params  = $_GET['params'] ?? []; // phpcs:ignore
        $post_id = $_GET['post_id'] ?? 0; // phpcs:ignore
        $event   = get_field( 'event', $post_id );
        $client  = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        $empty_params = array_filter( $params, fn( $item ) => empty( $item ) );

        if ( count( $empty_params ) === count( $params ) ) {
            wp_send_json( [] );
        }

        try {
            $cache_key = 'events-' . md5( wp_json_encode( $params ) );
            $events    = wp_cache_get( $cache_key );

            if ( ! $events ) {
                $events = $client->get_all( 'event', $params );
                wp_cache_set( $cache_key, $events, '', MINUTE_IN_SECONDS * 15 );
            }

            $events = array_map( function ( $item ) use ( $event ) {
                $start_time        = static::get_as_datetime( $item->start_time );
                $item->select_name = $item->name->fi . ' - ' . $start_time->format( 'j.n.Y' );
                $item->selected    = $item->id === $event;

                return $item;
            }, $events );
        }
        catch ( LinkedEventsException $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
        catch ( \JsonException $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        wp_send_json( $events ?? [] );
    }

    /**
     * Normalize event data
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function normalize_event( $event ) : array {
        $lang_key = Localization::get_current_language();

        if ( ! empty( $event->keywords ) ) {
            $keywords = [];

            foreach ( $event->keywords as $keyword ) {
                $keywords[] = [
                    'name' => $keyword->name->{$lang_key} ?? null,
                ];
            }
        }

        if ( ! empty( $event->images ) ) {
            $image = $event->images[0]->url;
        }

        return [
            'name'              => $event->name->{$lang_key} ?? null,
            'short_description' => $event->short_description->{$lang_key} ?? null,
            'description'       => nl2br( $event->description->{$lang_key} ) ?? null,
            'date_title'        => __( 'Dates', 'tms-theme-base' ),
            'date'              => static::get_event_date( $event ),
            'time_title'        => __( 'Time', 'tms-theme-base' ),
            'time'              => static::get_event_time( $event ),
            'location_title'    => __( 'Location', 'tms-theme-base' ),
            'location'          => static::get_event_location( $event, $lang_key ),
            'price_title'       => __( 'Price', 'tms-theme-base' ),
            'price'             => static::get_event_price_info( $event, $lang_key ),
            'provider_title'    => __( 'Organizer', 'tms-theme-base' ),
            'provider'          => static::get_provider_info( $event ),
            'keywords'          => $keywords ?? null,
            'primary_keyword'   => empty( $keywords ) ? null : $keywords[0],
            'image'             => $image ?? null,
            'url'               => static::get_event_url( $event->id ),
        ];
    }

    /**
     * Get event data for json+ld
     *
     * @param object $event Event object.
     *
     * @return false|string
     */
    public static function get_json_ld_data( $event ) {
        $lang_key   = Localization::get_current_language();
        $start_time = static::get_as_datetime( $event->start_time );
        $end_time   = static::get_as_datetime( $event->end_time );

        $event->name        = $event->name->{$lang_key};
        $event->description = ( $event->description->{$lang_key} );

        if ( $start_time ) {
            $event->startDate = $start_time->format( 'Y-m-d' );
        }

        if ( $end_time ) {
            $event->endDate = $end_time->format( 'Y-m-d' );
        }

        $event->location->address = $event->location->street_address->{$lang_key};

        return wp_json_encode( $event );
    }

    /**
     * Get event date
     *
     * @param object $event Event object.
     *
     * @return string|null
     */
    public static function get_event_date( $event ) {
        if ( empty( $event->start_time ) ) {
            return null;
        }

        $start_time  = static::get_as_datetime( $event->start_time );
        $end_time    = static::get_as_datetime( $event->end_time );
        $date_format = get_option( 'date_format' );

        if ( $start_time && $end_time && $start_time->diff( $end_time )->days >= 1 ) {
            return sprintf(
                '%s - %s',
                $start_time->format( $date_format ),
                $end_time->format( $date_format )
            );
        }

        return $start_time->format( $date_format );
    }

    /**
     * Get event time
     *
     * @param object $event Event object.
     *
     * @return string|null
     */
    public static function get_event_time( $event ) {
        if ( empty( $event->start_time ) ) {
            return null;
        }

        $start_time  = static::get_as_datetime( $event->start_time );
        $end_time    = static::get_as_datetime( $event->end_time );
        $time_format = 'H.i';

        if ( $start_time && $end_time ) {
            return sprintf(
                '%s - %s',
                $start_time->format( $time_format ),
                $end_time->format( $time_format )
            );
        }

        return $start_time->format( $time_format );
    }

    /**
     * Get event location
     *
     * @param object $event    Event object.
     * @param string $lang_key Language key.
     *
     * @return array
     */
    public static function get_event_location( $event, $lang_key ) {
        return [
            'name'        => $event->location->name->{$lang_key} ?? null,
            'description' => $event->location->description->{$lang_key} ?? null,
            'extra_info'  => $event->location_extra_info->{$lang_key} ?? null,
            'info_url'    => [
                'title' => __( 'Additional information', 'tms-theme-base' ),
                'link'  => $event->location->info_url->{$lang_key} ?? null,
            ],
        ];

    }

    /**
     * Get string as date time.
     *
     * @param string $value Date time string.
     *
     * @return \DateTime|null
     */
    public static function get_as_datetime( $value ) {
        try {
            return new \DateTime( $value );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get event price info
     *
     * @param object $event    Event object.
     * @param string $lang_key Language key.
     *
     * @return array|null
     */
    public static function get_event_price_info( $event, $lang_key ) : ?array {
        if ( empty( $event ) && empty( $event->offers ) ) {
            return null;
        }

        return array_map( function ( $offer ) use ( $lang_key ) {
            $price = $offer->price->{$lang_key} ?? null;

            if ( empty( $price ) && $offer->is_free ) {
                $price = __( 'Free', 'tms-theme-base' );
            }

            return [
                'is_free'     => $offer->is_free ?? null,
                'price'       => $price,
                'info_url'    => [
                    'title' => __( 'Additional information', 'tms-theme-base' ),
                    'url'   => $offer->info_url->{$lang_key} ?? null,
                ],
                'description' => $offer->description->{$lang_key} ?? null,
            ];

        }, $event->offers );
    }

    /**
     * Get provider info
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_provider_info( object $event ) : array {
        return [
            'name'  => $event->provider_name ?? null,
            'email' => $event->provider_email ?? null,
            'phone' => $event->provider_phone ?? null,
            'link'  => [
                'url'   => $event->provider_link ?? null,
                'title' => __( 'Additional info', 'tms-theme-base' ),
            ],
        ];
    }

    /**
     * Get event url
     *
     * @param string $event_id Event's API ID.
     *
     * @return string
     */
    public static function get_event_url( string $event_id ) : string {
        $dynamic_events = DynamicEvent::get_link_list();

        if ( isset( $dynamic_events[ $event_id ] ) ) {
            return $dynamic_events[ $event_id ];
        }

        $event_page = Settings::get_setting( 'events_page' );

        if ( $event_page ) {
            return add_query_arg(
                [
                    'event-id' => $event_id,
                ],
                get_permalink( $event_page )
            );
        }

        return '#';
    }
}
