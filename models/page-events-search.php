<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtumahaku
 */

use TMS\Theme\Base\Formatters\EventsFormatter;
use TMS\Theme\Base\Logger;

/**
 * The PageEventsSearch class.
 */
class PageEventsSearch extends BaseModel {

    /**
     * Template
     */
    const TEMPLATE = 'models/page-events-search.php';

    /**
     * Events search query var names.
     */
    const EVENT_SEARCH_TEXT = 'event_search_text';

    const EVENT_SEARCH_START_DATE = 'event_search_start_date';

    const EVENT_SEARCH_END_DATE = 'event_search_end_date';

    /**
     * Pagination data.
     *
     * @var object
     */
    protected object $pagination;

    /**
     * Return form fields.
     *
     * @return array
     */
    public function form() {
        return [
            'search_term'      => trim( get_query_var( self::EVENT_SEARCH_TEXT ) ),
            'form_start_date'  => get_query_var( self::EVENT_SEARCH_START_DATE ),
            'form_end_date'    => get_query_var( self::EVENT_SEARCH_END_DATE ),
            'seach_term_label' => __( 'Search term', 'tms-theme-base' ),
            'time_frame_label' => __( 'Events from', 'tms-theme-base' ),
            'start_date_label' => __( 'Start date', 'tms-theme-base' ),
            'end_date_label'   => __( 'End date', 'tms-theme-base' ),
        ];
    }

    /**
     * Item template classes.
     *
     * @return string
     */
    public function item_classes() : array {
        return apply_filters( 'tms/theme/page_events_search/item_classes', [
            'list' => [
                'item'        => 'has-background-secondary',
                'item_inner'  => '',
                'icon'        => 'is-accent',
                'description' => '',
            ],
            'grid' => [
                'item'       => 'has-background-secondary',
                'item_inner' => '',
                'icon'       => 'is-accent',
            ],
        ] );
    }

    /**
     * Get template classes.
     *
     * @return array
     */
    public function template_classes() {
        return apply_filters(
            'tms/theme/search/search_item',
            [
                'search_form'         => 'has-background-secondary',
                'search_item'         => 'has-background-secondary',
                'search_item_excerpt' => '',
            ]
        );
    }

    /**
     * Get events
     */
    public function events() : ?array {
        try {
            return $this->get_events();
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get no results text
     *
     * @return ?string
     */
    public function no_results() : ?string {
        try {
            $events = $this->get_events();

            if ( ! empty( $events['posts'] ) ) {
                return null;
            }

            if ( empty( get_query_var( self::EVENT_SEARCH_TEXT ) ) ) {
                return __( 'No search term given', 'tms-theme-base' );
            }
            else {
                return __( 'No results', 'tms-theme-base' );
            }
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return null;
    }

    /**
     * Get events
     *
     * @return array
     */
    private function get_events() : array {
        $event_search_text = get_query_var( self::EVENT_SEARCH_TEXT );
        $all_events        = $this->do_get_events();

        if ( empty( $event_search_text ) || empty( $all_events ) ) {
            return [];
        }

        $per_page = get_option( 'posts_per_page' );
        $paged    = get_query_var( 'paged', 0 );
        $paged    = $paged > 0 ? -- $paged : $paged;

        $chunks      = array_chunk( $all_events, $per_page );
        $event_count = count( $all_events );

        $this->set_pagination_data( $event_count );

        if ( $event_count > 0 ) {
            $results_text = sprintf(
            // translators: 1. placeholder is number of search results, 2. placeholder contains the search term(s).
                _nx(
                    '%1$1s result found for "%2$2s"',
                    '%1$1s results found for "%2$2s"',
                    $event_count,
                    'search results summary',
                    'tms-theme-base'
                ),
                $event_count,
                $event_search_text
            );
        }
        else {
            $results_text = null;
        }

        return [
            'summary' => $results_text,
            'posts'   => $event_count > 0 ? $chunks[ $paged ] : null,
        ];
    }

    /**
     * Fetch results from API.
     *
     * @return array
     */
    private function do_get_events() : array {
        $start_date = get_query_var( self::EVENT_SEARCH_START_DATE );
        $start_date = ! empty( $start_date ) ? $start_date : 'today';
        $end_date   = get_query_var( self::EVENT_SEARCH_END_DATE );
        $end_date   = ! empty( $end_date ) ? $end_date : date( 'Y-m-d', strtotime( '+1 year' ) );

        // Set user defined and default search parameters
        $params = [
            'text'        => get_query_var( self::EVENT_SEARCH_TEXT ),
            'start'       => $start_date,
            'end'         => $end_date,
            'sort'        => 'start_time',
            'page_size'   => get_option( 'posts_per_page' ),
            'show_images' => true,
            'keyword'     => [],
            'location'    => '',
            'publisher'   => '',
        ];

        $cache_group = 'page-events-search';
        $cache_key   = md5( wp_json_encode( $params ) );
        $events      = wp_cache_get( $cache_key, $cache_group );

        if ( ! empty( $events ) ) {
            return $events;
        }

        $formatter = new EventsFormatter();
        $data      = $formatter->format( $params, true );
        $events    = $data['events'] ?? [];

        if ( ! empty( $events ) ) {
            $events = array_map( function ( $item ) {
                $item['short_description'] = wp_trim_words( $item['short_description'], 30 );
                $item['location_icon']     = $item['is_virtual_event']
                    ? 'globe'
                    : 'location';

                return $item;
            }, $events );

            wp_cache_set( $cache_key, $events, $cache_group, MINUTE_IN_SECONDS * 15 );
        }

        return $events;
    }

    /**
     * Set pagination data
     *
     * @param int $event_count Event count.
     *
     * @return void
     */
    protected function set_pagination_data( int $event_count ) : void {
        $per_page = get_option( 'posts_per_page' );
        $paged    = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

        $this->pagination           = new stdClass();
        $this->pagination->page     = $paged;
        $this->pagination->per_page = $per_page;
        $this->pagination->items    = $event_count;
        $this->pagination->max_page = (int) ceil( $event_count / $per_page );
    }

    /**
     * Returns pagination data.
     *
     * @return object
     */
    public function pagination() : ?object {
        if ( isset( $this->pagination->page ) && isset( $this->pagination->max_page ) ) {
            if ( $this->pagination->page <= $this->pagination->max_page ) {
                return $this->pagination;
            }
        }

        return null;
    }
}
