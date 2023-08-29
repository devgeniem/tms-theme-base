<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use Closure;
use TMS\Theme\Base\Interfaces\Controller;
use TMS\Theme\Base\PostType\DynamicEvent;
use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Localization;

/**
 * Class Eventz
 *
 * @package TMS\Theme\Base
 */
class Eventz implements Controller {

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
        $client  = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );

        $params = [
            'q'           => $params['text'] ?? '',
            'areas'       => $params['area'] ? implode( ',', $params['area'] ) : '',
            'category_id' => $params['category'] ? implode( ',', $params['category'] ) : '',
            'targets'     => $params['target'] ? implode( ',', $params['target'] ) : '',
            'tags'        => $params['tag'] ? implode( ',', $params['tag'] ) : '',
        ];

        $params = array_filter( $params );

        try {
            $cache_key = 'events-' . md5( wp_json_encode( $params ) );
            $events    = wp_cache_get( $cache_key );

            if ( ! $events ) {
                $lang_key = Localization::get_current_language();
                $events = $client->search_events( $params, $lang_key );
                wp_cache_set( $cache_key, $events, '', MINUTE_IN_SECONDS * 15 );
            }

            $events = array_map( function ( $item ) use ( $event ) {
                $start_time        = static::get_as_datetime( $item->event->start );
                $item->select_name = $item->name . ' - ' . $start_time->format( 'j.n.Y' );
                $item->selected    = $item->_id === $event;

                return $item;
            }, $events->items );
        }
        catch ( EventzException | \JsonException $e ) {
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

        if ( ! empty( $event->topics ) ) {
            $topics = [];

            foreach ( $event->topics as $topic ) {
                $topics[] = [
                    'name' => $topic ?? null,
                ];
            }
        }

        if ( ! empty( $event->images->imageMobile->url ) ) {
            $image = $event->images->imageMobile->url;
        }

        return [
            'name'               => $event->name ?? null,
            'short_description'  => static::get_short_description( $event ) ?? null,
            'description'        => nl2br( $event->description ) ?? null,
            'date_title'         => __( 'Dates', 'tms-theme-tredu' ),
            'date'               => static::get_event_date( $event ),
            'dates'              => static::get_event_dates( $event ),
            'recurring'          => ! empty( $event->event->dates ),
            'time_title'         => __( 'Time', 'tms-theme-tredu' ),
            'time'               => static::get_event_time( $event ),
            'location_title'     => __( 'Location', 'tms-theme-tredu' ),
            'location'           => static::get_event_location( $event ),
            'price_title'        => __( 'Price', 'tms-theme-tredu' ),
            'price'              => static::get_event_price_info( $event, $lang_key ),
            'area_title'         => __( 'Area', 'tms-theme-tredu' ),
            'areas'              => static::get_area_info( $event ),
            'target_title'       => __( 'Target', 'tms-theme-tredu' ),
            'targets'            => static::get_target_info( $event ),
            'tags_title'         => __( 'Tags', 'tms-theme-tredu' ),
            'tags'               => static::get_tag_info( $event ),
            'keywords'           => $topics ?? null,
            'primary_keyword'    => empty( $topics ) ? null : $topics[0],
            'links_title'        => __( 'Links', 'tms-theme-tredu' ),
            'links'              => $event->links,
            'image'              => $image ?? null,
            'url'                => static::get_event_url( $event->_id ),
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
        $start_time = static::get_as_datetime( $event->event->start );
        $end_time   = static::get_as_datetime( $event->event->end );

        $event->name        = $event->name ?? null;
        $event->description = $event->description ?? null;

        if ( $start_time ) {
            $event->startDate = $start_time->format( 'Y-m-d' ); // phpcs:ignore
        }

        if ( $end_time ) {
            $event->endDate = $end_time->format( 'Y-m-d' ); // phpcs:ignore
        }

        $event->address = $event->locations[0]->address;

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
        if ( empty( $event->event->start ) ) {
            return null;
        }

        $start_time  = static::get_as_datetime( $event->event->start );
        $end_time    = static::get_as_datetime( $event->event->end );
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
        if ( empty( $event->event->start ) ) {
            return null;
        }

        $start_time  = static::get_as_datetime( $event->event->start );
        $end_time    = static::get_as_datetime( $event->event->end );
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
    public static function get_event_location( $event ) {

        return [
            'name' => $event->locations[0]->address ?? null,
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
            $dt = new \DateTime( $value );
            $dt->setTimezone( new \DateTimeZone( 'Europe/Helsinki' ) );

            return $dt;
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
     *
     * @return array|null
     */
    public static function get_event_price_info( $event ) : ?array {
        if ( empty( $event ) ) {
            return null;
        }

        $price = $event->price ?? null;

        if ( empty( $price ) || $event->price->isFree ) {
            $price = __( 'Free', 'tms-theme-tredu' );
        } else {
            $min = $event->price->min;
            $max = $event->price->max;

            if ( $min === 0 && $max > 0 ) {
                $price = $max . '€';
            } elseif ( $max === 0 && $min > 0 ) {
                $price = $min . '€';
            } else {
                $price = $min .'-'. $max . '€';
            }
        }

        return [
            [
                'is_free'     => null,
                'price'       => $price,
                'info_url'    => [
                    'title' => __( 'Additional information', 'tms-theme-tredu' ),
                    'url'   => null,
                ],
                'description' => null,
            ]
        ];
    }

    /**
     * Get area info
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_area_info( object $event ) : string {
        return implode( ',', $event->targets );
    }

    /**
     * Get targets info
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_target_info( object $event ) : string {
        return implode( ',', $event->targets );
    }

    /**
     * Get targets info
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_tag_info( object $event ) : string {
        return implode( ',', $event->tags );
    }

    /**
     * Get event dates info
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_event_dates( $event ) {
        $dates = [];

        if ( empty ( $event->event->dates ) ) {
            return $dates;
        }

        foreach( $event->event->dates as $date ) {
            $dates[] = [
                'date'      => self::compare_dates( $date->start, $date->end ),
                'isSoldOut' => $date->isSoldOut,
            ];
        }

        return $dates;
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

    /**
     * Get event date
     *
     * @param object $event Event object.
     *
     * @return string|null
     */
    public static function compare_dates( $start, $end ) {
        if ( empty( $start ) ) {
            return null;
        }

        $start_time  = static::get_as_datetime( $start );
        $end_time    = static::get_as_datetime( $end );
        $date_format = 'j.n.Y H.i';

        if ( $start_time && $end_time && $start_time->diff( $end_time )->days >= 1 ) {
            return sprintf(
                '%s - %s',
                $start_time->format( $date_format ),
                $end_time->format( $date_format )
            );
        }

        return sprintf(
            '%s - %s',
            $start_time->format( 'j.n.Y H.i' ),
            $end_time->format( 'H.i' )
        );
    }

    /**
     * Generate short description.
     *
     * @param object $event Event object.
     *
     * @return string|null
     */
    public static function get_short_description( $event ) {

        if ( empty ( $event->description ) ) {
            return null;
        }

        // Define a regular expression pattern to match the first two sentences
        $pattern = '/^(.*?[.!?])\s+(.*?[.!?])/';

        // Use preg_match() to find the first two sentences
        if ( preg_match( $pattern, $event->description, $matches ) ) {
            return $matches[1] . ' ' . $matches[2];
        }
    }
}
