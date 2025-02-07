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
                $events   = $client->search_events( $params, $lang_key );
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

        // Not recurring by default.
        $is_recurring = false;

        // Check if event has recurring dates or weekly entries
        if ( isset( $event->event->dates ) && count( $event->event->dates ) > 1 ) {
            $is_recurring = true;
        }
        elseif ( isset( $event->event->entries ) && count( $event->event->entries ) >= 1 ) {
            $is_recurring = true;
        }

        // Format event links
        $event_other_links          = [];
        $event_link_sign_up         = '';
        $event_link_purchase_ticket = '';
        if ( $event->links ) {
            foreach ( $event->links as $event_link ) {
                // Assign links to their own variables
                if ( $event_link->name === 'PurchaseTicket' ) {
                    $event_link->name           = \__( 'Purchase tickets', 'tms-theme-base' );
                    $event_link_purchase_ticket = $event_link;
                }
                else if ( $event_link->name === 'SignUp' ) {
                    $event_link->name   = \__( 'Sign up', 'tms-theme-base' );
                    $event_link_sign_up = $event_link;
                }
                else {
                    $event_other_links[] = $event_link;
                }
            }

            $event_other_links = array_map( function ( $event_link ) {
                if ( $event_link->name === 'HomePage' || $event_link->name === 'Homepage' ) {
                    $event_link->name = \__( 'Homepage', 'tms-theme-base' );
                }

                return $event_link;
            }, $event_other_links );

            // Remove null values from the array
            $event_other_links = array_filter( $event_other_links );
        }

        return [
            'name'                 => $event->name ?? null,
            'short_description'    => nl2br( $event->descriptionShort ) ?? null,
            'description'          => nl2br( $event->description ) ?? null,
            'date_title'           => __( 'Dates', 'tms-theme-base' ),
            'date'                 => static::get_event_date( $event ),
            'dates'                => static::get_event_dates( $event ),
            'entries'              => static::get_event_entries( $event ),
            'recurring'            => $is_recurring,
            'time_title'           => __( 'Time', 'tms-theme-base' ),
            'time'                 => static::get_event_time( $event ),
            // Include raw dates for possible sorting.
            'start_date_raw'       => static::get_as_datetime( $event->event->start ),
            'end_date_raw'         => static::get_as_datetime( $event->event->end ),
            'location_title'       => __( 'Location', 'tms-theme-base' ),
            'location'             => static::get_event_location( $event ),
            'multiple_locations'   => static::get_event_multiple_locations( $event ),
            'location_count'       => static::count_locations( $event ),
            'price_title'          => __( 'Price', 'tms-theme-base' ),
            'price'                => static::get_event_price_info( $event, $lang_key ),
            'provider_title'       => __( 'Organizer', 'tms-theme-base' ),
            'area_title'           => __( 'Area', 'tms-theme-base' ),
            'areas'                => static::get_area_info( $event ),
            'target_title'         => __( 'Target', 'tms-theme-base' ),
            'targets'              => static::get_target_info( $event ),
            'tags_title'           => __( 'Tags', 'tms-theme-base' ),
            'tags'                 => static::get_tag_info( $event ),
            'keywords'             => $topics ?? null,
            'primary_keyword'      => empty( $topics ) ? null : $topics[0],
            'links_title'          => __( 'Links', 'tms-theme-base' ),
            'links'                => $event_other_links,
            'sign_up_title'        => __( 'Sign up', 'tms-theme-base' ),
            'link_sign_up'         => $event_link_sign_up,
            'link_purchase_ticket' => $event_link_purchase_ticket,
            'image'                => $image ?? null,
            'url'                  => static::get_event_url( $event->_id ),
            'is_dynamic'           => static::get_dynamic_event( $event->_id ),
        ];
    }

    /**
     * Normalize event name data
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function normalize_event_title( $event ) : array {
        return [
            'name' => $event->name ?? null,
        ];
    }

    /**
     * Normalize event description data
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function normalize_event_description( $event ) : array {
        return [
            'short_description' => nl2br( $event->descriptionShort ) ?? null,
        ];
    }

    /**
     * Normalize event url data
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function normalize_event_url( $event ) : array {
        return [
            'url' => static::get_event_url( $event->_id ),
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

        $date_format = \get_option( 'date_format' );

        // If date-parameter exists in url
        if ( ! empty( $_GET['date'] ) ) {
            list( $start_date, $end_date ) = array_merge( explode( ' - ', urldecode( $_GET['date'] ) ), array( false ) );

            $start_datetime = static::get_as_datetime( $start_date );
            $end_datetime   = ! empty( $end_date ) ? static::get_as_datetime( $end_date ) : '';

            if ( $start_datetime && $end_datetime && $start_datetime->diff( $end_datetime )->days >= 1 ) {
                return sprintf(
                    '%s - %s',
                    $start_datetime->format( $date_format ),
                    $end_datetime->format( $date_format )
                );
            }

            return $start_datetime->format( $date_format );
        }

        $start_time  = static::get_as_datetime( $event->event->start );
        $end_time    = static::get_as_datetime( $event->event->end );

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

        // If time-parameter exists in url
        if ( ! empty( $_GET['time'] ) ) {
            list( $start_time, $end_time ) = array_merge( explode( ' - ', urldecode( $_GET['time'] ) ), array( false ) );

            if ( $start_time && $end_time ) {
                return sprintf(
                    '%s - %s',
                    $start_time,
                    $end_time
                );
            }

            return $start_time;
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
     * Get event location.
     *
     * @param object $event    Event object.
     *
     * @return array
     */
    public static function get_event_location( $event ) {
        return [
            'name' => $event->locations[0]->address ?? null,
        ];
    }

    /**
     * Get event location.
     *
     * @param object $event    Event object.
     *
     * @return array
     */
    public static function get_event_multiple_locations( $event ) {
        $event_locations = [];

        if ( count( $event->locations ) <= 1 ) {
            return $event_locations;
        }

        foreach ( $event->locations as $location ) {
            $event_locations[] = [
                'name' => $location->address ?? null
            ];
        }

        return $event_locations;
    }

    /**
     * Get event location.
     *
     * @param object $event    Event object.
     *
     * @return array
     */
    public static function count_locations( $event ) {
        return [
            'other_locations_string' => count( $event->locations ) > 1 ? \__( 'and other locations', 'tms-theme-base' ) : null,
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
        if ( empty( $event ) || empty( $event->price ) ) {
            return null;
        }

        $price = self::format_price( $event->price );

        return [
            [
                'is_free'     => property_exists( $event->price, 'isFree' ) ? true : null,
                'price'       => $price,
                'info_url'    => [
                    'title' => __( 'Additional information', 'tms-theme-base' ),
                    'url'   => null,
                ],
                'description' => null,
            ],
        ];
    }

    /**
     * Format price.
     *
     * @param object $price Event price.
     *
     * @return string|null
     */
    public static function format_price( $price ) : ?string {
        if ( property_exists( $price, 'isFree' ) ) {
            return __( 'Free', 'tms-theme-base' );
        }

        $formatted_price = '';

        // Price data might differ.
        if ( $price->min === 0 && $price->max > 0 ) {
            $formatted_price = $price->max;
        }

        if ( $price->max === 0 && $price->min > 0 ) {
            $formatted_price = $price->min;
        }

        if ( empty( $formatted_price ) ) {
            $formatted_price = $price->min . '-' . $price->max;
        }

        return $formatted_price . '€';
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

        if ( empty( $event->event->dates ) ) {
            return $dates;
        }

        foreach ( $event->event->dates as $date ) {
            $time_now    = \current_datetime();
            $current_end = new \DateTime( $date->end );
            $current_end->setTimezone( new \DateTimeZone( 'Europe/Helsinki' ) );

            // Don't show past dates
            if ( $current_end >= $time_now ) {
                $dates[] = [
                    'date'        => self::compare_dates( $date->start, $date->end ),
                    'is_sold_out' => $date->isSoldOut,
                ];
            }
        }

        return $dates;
    }

    /**
     * Get event entries info
     * Entries occur if the event is held weekly for specific days
     *
     * @param object $event Event object.
     *
     * @return array
     */
    public static function get_event_entries( $event ) {
        $entries    = [];
        $entry_data = [];

        if ( empty( $event->event->entries ) ) {
            return $entries;
        }

        // Get single entry data
        foreach ( $event->event->entries as $entry ) {
            $entry_data[] = [
                'day_of_week' => $entry->dayOfWeek,
                'start_time'  => $entry->startTimeLocal,
                'end_time'    => $entry->endTimeLocal,
                'sold_out'    => $entry->isSoldOut,
            ];
        }

        $end_date   = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            date( 'Y-m-d H:i:s', strtotime( $event->event->end ) )
        );

        // Loop through days and get the dates each week
        foreach ( $entry_data as $entry ) {
            // Reset start_date each iteration
            $start_date = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                date( 'Y-m-d H:i:s', strtotime( $event->event->start ) )
            );

            // Check if the day of week matches the events start date
            if ( date( 'D', strtotime( $entry['day_of_week'] ) ) !== $start_date->format( 'D' ) ) {
                $day_of_week = date( 'D', strtotime( $entry['day_of_week'] ) );
                $start_date->modify( "next $day_of_week" );
            }

            // Get all occurences of day in the time range
            while ( $start_date <= $end_date ) {
                $current_start = new \DateTime( $start_date->format( 'Y-m-d' ) . ' ' . $entry['start_time'] );
                $current_end   = new \DateTime( $start_date->format( 'Y-m-d' ) . ' ' . $entry['end_time'] );
                $time_now      = \current_datetime();

                // Don't show past dates
                if ( $current_end >= $time_now ) {
                    $event_dates = sprintf(
                        '%s - %s',
                        $current_start->format( 'j.n.Y H.i' ),
                        $current_end->format( 'H.i' )
                    );

                    $entries[] = [
                        'date'        => $event_dates,
                        'is_sold_out' => $entry['sold_out'] ?? '',
                        'start_date'  => $current_start->getTimestamp(),
                    ];
                }

                $start_date->modify( '+1 week' );
            }
        }

        // Sort entry-dates by start_date timestamp
        usort( $entries, function ($a, $b) {
            return $a['start_date'] <=> $b['start_date'];
        });

        return $entries;
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
            return \add_query_arg(
                [
                    'event-id' => $event_id,
                ],
                \get_permalink( $event_page )
            );
        }

        return '#';
    }

    /**
     * Get dynamic events
     *
     * @param string $event_id Event's API ID.
     *
     * @return string
     */
    public static function get_dynamic_event( string $event_id ) : string {
        $dynamic_events = DynamicEvent::get_link_list();

        if ( isset( $dynamic_events[ $event_id ] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get event date
     *
     * @param string $start Event startdate.
     * @param string $end Event enddate.
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
}
