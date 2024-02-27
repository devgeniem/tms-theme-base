<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Eventz;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Localization;
use TMS\Plugin\ManualEvents\PostType;
use TMS\Plugin\ManualEvents\Taxonomy;

/**
 * Class EventzFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class EventzFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

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
     *
     * @return array
     */
    public function format( array $layout ) : array {
        $layout['category_id'] = $layout['category'] ? array_values( $layout['category'] ) : [];
        $layout['areas']       = $layout['area'] ? array_values( $layout['area'] ) : [];
        $layout['targets']     = $layout['target'] ? array_values( $layout['target'] ) : [];
        $layout['tags']        = $layout['tag'] ? array_values( $layout['tag'] ) : [];

        $query_params             = $this->format_query_params( $layout );
        $query_params['language'] = function_exists( 'pll_current_language' )
            ? pll_current_language()
            : get_locale();

        $events = $this->get_events( $query_params );

        if ( empty( $events ) ) {
            $events = [];
        }

        // Create recurring events
        $event_data['events'] = $events ?? [];
        if ( ! empty( $event_data['events'] ) ) {
            $events = self::create_recurring_events( $event_data );
        }

        $manual_events = [];
        if ( ! empty( $layout['manual_event_categories'] ) ) {
            $manual_events           = self::get_manual_events( $layout['manual_event_categories'] );
            $recurring_manual_events = self::get_recurring_manual_events( $layout['manual_event_categories'] );
        }

        $events = array_merge( $events['events'] ?? [], $manual_events, $recurring_manual_events );

        if ( empty( $events ) ) {
            return $layout;
        }

        // Sort events by start datetime objects.
        usort( $events, function( $a, $b ) {
            return $a['start_date_raw'] <=> $b['start_date_raw'];
        } );

        // Show selected amount of events
        $events = array_slice( $events, 0, $layout['page_size'] );

        $layout['events']  = $this->format_events( $events, $layout['show_images'] );
        $layout['classes'] = [
            'event_item_bg'   => apply_filters( 'tms/theme/layout_events/item_bg_class', 'has-background-secondary' ),
            'event_item_text' => apply_filters( 'tms/theme/layout_events/item_text_class', '' ),
            'event_item_icon' => apply_filters( 'tms/theme/layout_events/item_icon_class', '' ),
            'all_events_link' => apply_filters( 'tms/theme/layout_events/all_events_link', 'is-size-7' ),
            'event_item_pill' => apply_filters( 'tms/theme/layout_events/event_item', 'is-primary-invert' ),
        ];

        return $layout;
    }


    /**
     * Create recurring events as single item.
     *
     * @param array $events Events.
     *
     * @return void
     */
    public static function create_recurring_events( $events )  {

        $recurring_events = [];
        if( ! empty( $events['events'] ) ) {
            foreach ( $events['events'] as $event ) {

                // Chek if event has dates or entries
                if ( count( $event['dates'] ) > 1 ) {
                    $recurring_event_dates = $event['dates'];
                } else if ( ! empty( $event['entries'] ) ) {
                    $recurring_event_dates = $event['entries'];
                }

                // Get recurring event single dates
                if ( isset( $recurring_event_dates ) ) {
                    foreach ( $recurring_event_dates as $date ) {
                        $clone = $event;
                        unset( $endDate );

                        // Split the dates and times into parts
                        list( $startPart, $endPart )   = explode( ' - ', $date['date'], 2 );
                        list( $startDate, $startTime ) = explode( ' ', $startPart, 2 );

                        // Check if endPart includes date & time
                        if ( strpos($endPart, ' ') ) {
                            list( $endDate, $endTime ) = explode( ' ', $endPart, 2 );
                        }
                        else {
                            $endTime = $endPart;
                        }

                        // Parse the dates
                        $newStartDate = \DateTime::createFromFormat( 'd.m.Y', $startDate );
                        $newEndDate   = isset( $endDate ) ? \DateTime::createFromFormat( 'd.m.Y', $endDate ) : null;

                        // Parse the start and end times
                        $startDateTime = \DateTime::createFromFormat( 'H.i', $startTime );
                        $startDateTime->setDate( $newStartDate->format( 'Y' ), $newStartDate->format( 'm' ), $newStartDate->format( 'd' ) );
                        if ( $newEndDate ) {
                            $endDateTime = \DateTime::createFromFormat( 'H.i', $endTime );
                            $endDateTime->setDate( $newEndDate->format( 'Y' ), $newEndDate->format( 'm' ), $newEndDate->format( 'd' ) );
                        }

                        // Create time & date-ranges
                        if ( $endTime ) {
                            $timeRange = $startTime . ' - ' . $endTime;
                        }
                        else {
                            $timeRange = $startTime;
                        }

                        if ( $newEndDate ) {
                            $dateRange = $newStartDate->format( 'd.m.Y' ) . ' - ' . $newEndDate->format( 'd.m.Y' );
                        }
                        else {
                            $dateRange = $newStartDate->format( 'd.m.Y' );
                        }

                        $clone['date']           = $dateRange;
                        $clone['time']           = $timeRange;
                        $clone['start_date_raw'] = $startDateTime;
                        $clone['end_date_raw']   = $endDateTime ?? '';
                        $clone['url']            = $event['url'] . '&date=' . urlencode( $dateRange ) . '&time=' . urlencode( $timeRange );

                        $recurring_events[] = $clone;
                    }
                } else {
                    $recurring_events[] = $event;
                }
            }
        }

        $events['events'] = $recurring_events;

        return $events;
    }

    /**
     * Format events
     *
     * @param array $events      Array of events.
     * @param bool  $show_images Show images flag.
     *
     * @return array
     */
    public function format_events( array $events, bool $show_images = true ) : array {
        if ( ! $show_images ) {
            return array_map( function ( $item ) {
                $item['image'] = false;

                return $item;
            }, $events );
        }

        $default_image     = Settings::get_setting( 'events_default_image' );
        $default_image_url = wp_get_attachment_image_url( $default_image, 'large' );

        if ( ! empty( $default_image ) ) {
            $events = array_map( function ( $item ) use ( $default_image_url ) {
                if ( empty( $item['image'] ) ) {
                    $item['image'] = $default_image_url;
                }

                return $item;
            }, $events );
        }

        return $events;
    }

    /**
     * Format query params
     *
     * @param array $layout ACF Layout data.
     *
     * @return array
     */
    public function format_query_params( array $layout ) : array {
        $query_params = [
            'q'           => null,
            'start'       => null,
            'end'         => null,
            'category_id' => null,
            'areas'       => null,
            'tags'        => null,
            'targets'     => null,
            'sort'        => null,
            'size'        => null,
            'skip'        => null,
            'page_size'   => null,
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

        if ( ! empty( $layout['starts_today'] ) && true === $layout['starts_today'] ) {
            $query_params['start'] = date( 'Y-m-d' );
        }

        // Force sort param
        $query_params['sort'] = 'startDate';

        $query_params['language'] = DPT_PLL_ACTIVE
            ? pll_current_language()
            : get_locale();

        return $query_params;
    }

    /**
     * Get events
     *
     * @param array $query_params API query params.
     *
     * @return array|null
     */
    private function get_events( array $query_params ) : ?array {
        // Force sort param
        $query_params['sort'] = 'startDate';

        if ( ! empty( $query_params['page_size'] ) ) {
            $query_params['size'] = $query_params['page_size'];
        }

        $client = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );

        try {
            $lang_key = Localization::get_current_language();
            $response = $client->search_events( $query_params, $lang_key );

            if ( empty( $response ) ) {
                return null;
            }

            return array_map(
                fn( $item ) => Eventz::normalize_event( $item ),
                $response->items
            );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get manual events by taxonomy.
     *
     * @param array $category_ids List of taxonomy ids.
     *
     * @return array
     */
    public static function get_manual_events( array $category_ids = null ) : array {
        $args = [
            'post_type'      => PostType\ManualEvent::SLUG,
            'posts_per_page' => 200, // phpcs:ignore
            'meta_query'     => [
                [
                    'key'     => 'start_datetime',
                    'value'   => date( 'Y-m-d' ),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
            ],
        ];

        if ( ! empty( $category_ids ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => Taxonomy\ManualEventCategory::SLUG,
                    'field'    => 'term_id',
                    'terms'    => array_values( $category_ids ),
                    'operator' => 'IN',
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( empty( $query->posts ) ) {
            return [];
        }

        $events = array_map( function ( $e ) {
            $id           = $e->ID;
            $event        = (object) get_fields( $id );
            $event->id    = $id;
            $event->title = get_the_title( $id );
            $event->url   = get_permalink( $id );
            $event->image = has_post_thumbnail( $id ) ? get_the_post_thumbnail_url( $id, 'medium_large' ) : null;

            return PostType\ManualEvent::normalize_event( $event );
        }, $query->posts );

        return $events;
    }

    /**
     * Get recurring manual events.
     *
     * @return array
     */
    protected function get_recurring_manual_events( array $category_ids = null ) : array {
        $args = [
            'post_type'      => PostType\ManualEvent::SLUG,
            'posts_per_page' => 200, // phpcs:ignore
            'meta_query'     => [
                [
                    'key'     => 'recurring_event',
                    'value'   => 1,
                ],
            ],
        ];

        if ( ! empty( $category_ids ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => Taxonomy\ManualEventCategory::SLUG,
                    'field'    => 'term_id',
                    'terms'    => array_values( $category_ids ),
                    'operator' => 'IN',
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( empty( $query->posts ) ) {
            return [];
        }

        // Loop through events
        $recurring_events = array_map( function ( $e ) {
            $id    = $e->ID;
            $event = (object) \get_fields( $id );

            foreach ( $event->dates as $date ) {
                date_default_timezone_set( 'Europe/Helsinki' );
                $time_now    = \current_datetime()->getTimestamp();
                $event_start = strtotime( $date['start'] );
                $event_end   = strtotime( $date['end'] );

                // Return only ongoing or next upcoming event
                if ( ( $time_now > $event_start && $time_now < $event_end ) || $time_now < $event_start ) {
                    $event->id             = $id;
                    $event->title          = \get_the_title( $id );
                    $event->url            = \get_permalink( $id );
                    $event->image          = \has_post_thumbnail( $id ) ? \get_the_post_thumbnail_url( $id, 'medium_large' ) : null;
                    $event->start_datetime = $date['start'];
                    $event->end_datetime   = $date['end'];

                    return PostType\ManualEvent::normalize_event( $event );
                }
            }
        }, $query->posts );

        return array_filter( $recurring_events );
    }
}
