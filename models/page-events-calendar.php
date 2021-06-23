<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtumakalenteri
 */

use Geniem\LinkedEvents\LinkedEventsClient;
use TMS\Theme\Base\Formatters\EventsFormatter;
use TMS\Theme\Base\LinkedEvents;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Components;

/**
 * The PageEventsCalendar class.
 */
class PageEventsCalendar extends BaseModel {

    use Components;

    /**
     * Template
     */
    const TEMPLATE = 'models/page-events-calendar.php';

    /**
     * Pagination data.
     *
     * @var object
     */
    protected object $pagination;

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
     * Is grid view
     *
     * @return bool
     */
    public function is_grid() : bool {
        $is_grid = get_field( 'layout' ) ?? 'grid';

        return $is_grid === 'grid';
    }

    /**
     * Get item partial
     *
     * @return string
     */
    public function item_partial() : string {
        $part = $this->is_grid() ? 'grid' : 'list';

        return 'views/page-events-calendar/page-events-calendar-item-' . $part;
    }

    /**
     * Get events
     *
     * @return array
     */
    private function get_events() : array {
        $all_events = $this->do_get_events();
        $per_page   = get_option( 'posts_per_page' );
        $paged      = get_query_var( 'paged', 0 );
        $paged      = $paged > 0 ? -- $paged : $paged;

        $chunks = array_chunk( $all_events, $per_page );

        $this->set_pagination_data( count( $all_events ) );

        return $chunks[ $paged ];
    }

    /**
     * Fetch results from API.
     *
     * @return array
     */
    private function do_get_events() : array {
        $params = [
            'start'       => get_field( 'start' ),
            'end'         => get_field( 'end' ),
            'keyword'     => get_field( 'keyword' ),
            'location'    => get_field( 'location' ),
            'publisher'   => get_field( 'publisher' ),
            'sort'        => get_field( 'sort' ),
            'page_size'   => get_option( 'posts_per_page' ),
            'text'        => get_field( 'text' ),
            'show_images' => get_field( 'show_images' ),
        ];

        $cache_group = 'page-events-calendar';
        $cache_key   = md5( wp_json_encode( $params ) );
        $events      = wp_cache_get( $cache_key, $cache_group );

        if ( ! empty( $events ) ) {
            return $events;
        }

        $formatter = new EventsFormatter();
        $data      = $formatter->format( $params, true );
        $events    = $data['events'] ?? [];

        if ( ! empty( $events ) ) {
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

    /**
     * Calendar pages
     *
     * @return array|null
     */
    public function calendar_pages() : ?array {
        if ( ! Settings::get_setting('show_related_events_calendars')) {
            return null;
        }

        $the_query = new WP_Query( [
            'post_type'              => \TMS\Theme\Base\PostType\Page::SLUG,
            'posts_per_page'         => 100,
            'update_post_term_cache' => false,
            'meta_key'               => '_wp_page_template',
            'meta_value'             => "models/page-events-calendar.php",
            'no_found_rows'          => true,
        ] );

        if ( ! $the_query->have_posts() ) {
            return null;
        }

        $current_page = get_queried_object_id();

        $pages = array_filter( $the_query->posts, function ( $item ) use ( $current_page ) {
            return $item->ID !== $current_page;
        } );

        return array_map( function ( $item ) {
            return [
                'url'   => get_the_permalink( $item->ID ),
                'title' => $item->post_title,
            ];
        }, $pages );
    }
}
