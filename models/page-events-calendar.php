<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Tapahtumakalenteri
 */

use TMS\Theme\Base\Formatters\EventsFormatter;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Traits\Pagination;
use TMS\Theme\Base\Logger;

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
     * Description text
     */
    public function description() : ?string {
        return get_field( 'description' );
    }

    /**
     * Get no results text
     *
     * @return string
     */
    public function no_results() : string {
        return __( 'No results', 'tms-theme-base' );
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
     */
    public function events() : ?array {
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
    protected function get_events() : array {
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
            'page'        => get_query_var( 'paged', 1 ),
            'include'     => 'organization,location,keywords',
        ];

        if ( ! empty( get_field( 'starts_today' ) ) && true === get_field( 'starts_today' ) ) {
            $params['start'] = 'today';
        }

        $formatter         = new EventsFormatter();
        $params            = $formatter->format_query_params( $params );
        $params['include'] = 'organization,location,keywords';

        $cache_group = 'page-events-calendar';
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

        $this->set_pagination_data( $response['meta']->count );

        return $response;
    }

    /**
     * Calendar pages
     *
     * @return array|null
     */
    public function calendar_pages() : ?array {
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
