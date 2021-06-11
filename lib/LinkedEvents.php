<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use Closure;
use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use TMS\Theme\Base\Interfaces\Controller;

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
        $params = $_GET['params']; // phpcs:ignore
        $client = new LinkedEventsClient( PIRKANMAA_EVENTS_API_URL );

        try {
            $events = $client->get_all( 'event', $params );
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

        return [
            'name'              => $event->name->{$lang_key},
            'short_description' => $event->short_description->{$lang_key},
            'description'       => nl2br( $event->description->{$lang_key} ),

            'date_title' => __( 'Dates', 'tms-theme-base' ),
            'date'       => static::get_event_date( $event ),

            'time_title' => __( 'Time', 'tms-theme-base' ),
            'time'       => static::get_event_time( $event ),

            'location_title' => __( 'Location', 'tms-theme-base' ),
            'location'       => static::get_event_location( $event, $lang_key ),

            'price_title' => __( 'Price', 'tms-theme-base' ),
            'price'       => static::get_event_price_info( $event, $lang_key ),

            'provider_title' => __( 'Organizer', 'tms-theme-base' ),
            'provider'       => static::get_provider_info( $event ),
        ];
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
        $time_format = 'H:i';

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
            'name'        => $event->location->name->{$lang_key},
            'description' => $event->location->description->{$lang_key},
            'extra_info'  => $event->location_extra_info->{$lang_key},
            'info_url'    => [
                'title' => __( 'Additional information', 'tms-theme-base' ),
                'link'  => $event->location->info_url->{$lang_key},
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
            $price = $offer->price->{$lang_key};

            if ( empty( $price ) && $offer->is_free ) {
                $price = __( 'Free', 'tms-theme-base' );
            }

            return [
                'is_free'     => $offer->is_free,
                'price'       => $price,
                'info_url'    => [
                    'title' => __( 'Additional information', 'tms-theme-base' ),
                    'url'   => $offer->info_url->{$lang_key},
                ],
                'description' => $offer->description->{$lang_key},
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
            'name'  => $event->provider_name,
            'email' => $event->provider_email,
            'phone' => $event->provider_phone,
            'link'  => [
                'url'   => $event->provider_link,
                'title' => __( 'Additional info', 'tms-theme-base' ),
            ],
        ];
    }
}
