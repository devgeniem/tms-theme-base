<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtumakalenteri
 */

use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Traits\Pagination;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\Formatters\EventzFormatter;

/**
 * The PageEventsCalendar class.
 */
class PageEventsCalendar extends PageEventsSearch {

    use Components;
    use Pagination;

    /**
     * Template
     */
    const TEMPLATE = 'models/page-events-calendar.php';


    /**
     * Maximum events per page.
     */
    const MAX_EVENTS_PER_PAGE = '999';

    /**
     * Description text
     */
    public function description(): ?string {
        return get_field( 'description' );
    }

    /**
     * Get no results text
     *
     * @return string
     */
    public function no_results(): string {
        return __( 'No results', 'tms-theme-base' );
    }

    /**
     * Is grid view
     *
     * @return bool
     */
    public function is_grid(): bool {
        $is_grid = get_field( 'layout' ) ?? 'grid';

        return $is_grid === 'grid';
    }

    /**
     * Get item partial
     *
     * @return string
     */
    public function item_partial(): string {
        $part = $this->is_grid() ? 'grid' : 'list';

        return 'views/page-events-calendar/page-events-calendar-item-' . $part;
    }

    /**
     * Get events
     */
    public function events(): ?array {
        try {
            $response = $this->get_events();

            return $response['events'] ?? [];
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
    protected function get_events(): array {
        $disable_pagination = \get_field( 'disable_pagination' );
        $events_per_page    = $disable_pagination === true ? self::MAX_EVENTS_PER_PAGE : \get_option( 'posts_per_page' );

        $paged = \get_query_var( 'paged', 1 );
        $skip  = 0;

        if ( $paged > 1 ) {
            $skip = ( $paged - 1 ) * $events_per_page;
        }

        $params = [
            'q'           => \get_field( 'text' ),
            'start'       => \get_field( 'start' ),
            'end'         => \get_field( 'end' ),
            'category_id' => \get_field( 'category' ),
            'areas'       => \get_field( 'area' ),
            'targets'     => \get_field( 'target' ),
            'tags'        => \get_field( 'tag' ),
            'sort'        => 'startDate',
            'show_images' => \get_field( 'show_images' ),
        ];

        if ( ! empty( \get_field( 'starts_today' ) ) && true === \get_field( 'starts_today' ) ) {
            $params['start'] = date( 'Y-m-d' );
        }

        // Start date must be at least current date.
        if ( $params['start'] < date( 'Y-m-d' ) ) {
            $params['start'] = date( 'Y-m-d' );
        }

        $formatter = new EventzFormatter();
        $params    = $formatter->format_query_params( $params );

        $cache_group = 'page-events-calendar';
        $cache_key   = md5( \wp_json_encode( $params ) );
        $response    = \wp_cache_get( $cache_key, $cache_group );

        if ( empty( $response ) ) {

            $response = $this->do_get_events( $params );

            if ( ! empty( $response ) ) {
                \wp_cache_set(
                    $cache_key,
                    $response,
                    $cache_group,
                    MINUTE_IN_SECONDS * 15
                );
            }
        }

        if ( ! empty( $response['events'] ) ) {

            // Sort events.
            usort( $response['events'], function ( $a, $b ) {
                return $a['start_date_raw'] <=> $b['start_date_raw'];
            } );

            $this->set_pagination_data( count( $response['events'] ) );

            $response['events'] = array_slice( $response['events'], $skip, $events_per_page );
        }

        return $response;
    }

    /**
     * Calendar pages
     *
     * @return array|null
     */
    public function calendar_pages(): ?array {
        if ( ! Settings::get_setting( 'show_related_events_calendars' ) ) {
            return null;
        }

        $the_query = new WP_Query( [
            'post_type'              => \TMS\Theme\Base\PostType\Page::SLUG,
            'posts_per_page'         => 100,
            'update_post_term_cache' => false,
            'meta_key'               => '_wp_page_template',
            'meta_value'             => 'models/page-events-calendar.php', // phpcs:ignore
            'no_found_rows'          => true,
        ] );

        if ( ! $the_query->have_posts() ) {
            return null;
        }

        $current_page = \get_queried_object_id();

        $pages = array_filter( $the_query->posts, function ( $item ) use ( $current_page ) {
            return $item->ID !== $current_page;
        } );

        return array_map( function ( $item ) {
            return [
                'url'   => \get_the_permalink( $item->ID ),
                'title' => $item->post_title,
            ];
        }, $pages );
    }

    /**
     * Set pagination data
     *
     * @param int $event_count Event count.
     *
     * @return void
     */
    protected function set_pagination_data( int $event_count ): void {
        $disable_pagination = \get_field( 'disable_pagination' );
        $events_per_page    = $disable_pagination === true ? self::MAX_EVENTS_PER_PAGE : \get_option( 'posts_per_page' );

        $per_page = $events_per_page;
        $paged    = \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) : 1;

        $this->pagination           = new stdClass();
        $this->pagination->page     = $paged;
        $this->pagination->per_page = $per_page;
        $this->pagination->items    = $event_count;
        $this->pagination->max_page = (int) ceil( $event_count / $per_page );
    }
}
