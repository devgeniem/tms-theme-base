<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtumahaku
 */

use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Traits;
use TMS\Theme\Base\EventzClient;
use TMS\Theme\Base\Formatters\EventzFormatter;
use TMS\Theme\Base\Eventz;
use TMS\Theme\Base\Localization;

/**
 * The PageEventsSearch class.
 */
class PageEventsSearch extends BaseModel {

    use Traits\Pagination;

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
            'action'           => get_the_permalink(),
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
        return empty( get_query_var( self::EVENT_SEARCH_TEXT ) )
            ? __( 'No search term given', 'tms-theme-base' )
            : __( 'No results', 'tms-theme-base' );
    }

    /**
     * Get events
     *
     * @return array
     */
    protected function get_events() : array {
        $event_search_text = get_query_var( self::EVENT_SEARCH_TEXT );
        $start_date        = get_query_var( self::EVENT_SEARCH_START_DATE );
        $start_date        = ! empty( $start_date ) ? $start_date : date( 'Y-m-d' );
        $end_date          = get_query_var( self::EVENT_SEARCH_END_DATE );
        $end_date          = ! empty( $end_date ) ? $end_date : date( 'Y-m-d', strtotime( '+1 year' ) );

        $paged = get_query_var( 'paged', 1 );
        $skip  = 0;

        if ( $paged > 1 ) {
            $skip = ( $paged - 1 ) * get_option( 'posts_per_page' );
        }

        // Set user defined and default search parameters
        $params = [
            'q'     => $event_search_text,
            'start' => $start_date,
            'end'   => $end_date,
            'sort'  => 'startDate',
            'size'  => get_option( 'posts_per_page' ),
            'skip'  => $skip,
        ];

        $formatter = new EventzFormatter();
        $params    = $formatter->format_query_params( $params );

        $cache_group = 'page-events-search';
        $cache_key   = md5( wp_json_encode( $params ) );
        $response    = wp_cache_get( $cache_key, $cache_group );

        if ( empty( $response ) ) {
            $response = $this->do_get_events( $params );

            if ( ! empty( $response ) ) {
                wp_cache_set(
                    $cache_key,
                    $response,
                    $cache_group,
                    MINUTE_IN_SECONDS * 15
                );
            }
        }

        $this->set_pagination_data( $response['meta']->total );

        return [
            'summary' => $this->get_results_text( $response['meta']->total ?? 0 ),
            'posts'   => $response['events'],
        ];
    }

    /**
     * Get results text
     *
     * @param int $event_count Event count.
     *
     * @return string|null
     */
    protected function get_results_text( $event_count ) : ?string {
        $event_search_text = get_query_var( self::EVENT_SEARCH_TEXT );

        if ( $event_count > 0 && ! empty( $event_search_text ) ) {
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

        return $results_text;
    }

    /**
     * Fetch results from API.
     *
     * @param array $params API query params.
     *
     * @return array
     */
    protected function do_get_events( array $params ) : array {
        $event_data = $this->do_api_call( $params );

        if ( ! empty( $event_data['meta'] ) ) {
            $this->set_pagination_data( $event_data['meta']->total );
        }

        if ( ! empty( $event_data['events'] ) ) {
            $event_data['events'] = ( new EventzFormatter() )->format_events( $event_data['events'] );
            $event_data['events'] = array_map( function ( $item ) {
                $item['short_description'] = wp_trim_words( $item['short_description'], 30 );
                $item['location_icon']     = $item['is_virtual_event']
                    ? 'globe'
                    : 'location';

                return $item;
            }, $event_data['events'] );
        }

        return $event_data ?? [];
    }

    /**
     * Do an API call
     *
     * @param array $params API query params.
     *
     * @return array
     */
    protected function do_api_call( array $params ) : array {
        $client = new EventzClient( PIRKANMAA_EVENTZ_API_URL, PIRKANMAA_EVENTZ_API_KEY );

        try {
            $lang_key = Localization::get_current_language();
            $response = $client->search_events( $params, $lang_key );

            if ( ! empty( $response ) ) {
                $events = array_map(
                    fn( $item ) => Eventz::normalize_event( $item ),
                    $response->items ?? []
                );
            }
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        return [
            'events' => $events ?? null,
            'meta'   => $response->meta ?? null,
        ];
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
}
