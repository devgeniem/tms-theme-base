<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\PostType\Post;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Taxonomy\Category;

/**
 * The Home class.
 */
class Home extends BaseModel {

    /**
     * Pagination data.
     *
     * @var object
     */
    protected object $pagination;

    /**
     * Hooks
     */
    public static function hooks() : void {
        add_action(
            'pre_get_posts',
            [ __CLASS__, 'modify_query' ]
        );
    }

    /**
     * Get view type
     *
     * @return string
     */
    public function view_type() : string {
        return Settings::get_setting( 'archive_view_type' ) ?? 'grid';
    }

    /**
     * Get filter category
     *
     * @return int|null
     */
    protected static function get_filter_category() : ?int {
        return static::get_filter_value( 'filter-category' );
    }

    /**
     * Get filter month
     *
     * @return int|null
     */
    protected static function get_filter_month() : ?int {
        return static::get_filter_value( 'filter-month' );
    }

    /**
     * Get filter year
     *
     * @return int|null
     */
    protected static function get_filter_year() : ?int {
        return static::get_filter_value( 'filter-year' );
    }

    /**
     * Get filter value
     *
     * @param string $filter Filter name.
     *
     * @return int|null
     */
    protected static function get_filter_value( string $filter ) : ?int {
        $value = get_query_var( $filter, false );

        return ! $value
            ? null
            : intval( $value );
    }

    /**
     * Modify query
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    public static function modify_query( WP_Query $wp_query ) : void {
        if ( is_admin() || ( ! $wp_query->is_main_query() || ! $wp_query->is_home() ) ) {
            return;
        }

        $filter_category = static::get_filter_category();

        if ( ! empty( $filter_category ) ) {
            $wp_query->set( 'cat', $filter_category );
        }

        static::modify_query_date( $wp_query );
    }

    /**
     * Modify query date params
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    protected static function modify_query_date( $wp_query ) {
        $filter_month = static::get_filter_month();
        $filter_year  = static::get_filter_year();
        $date_query   = [];

        if ( ! empty( $filter_month ) ) {
            $date_query['month'] = $filter_month;
        }

        if ( ! empty( $filter_year ) ) {
            $date_query['year'] = $filter_year;
        }

        if ( ! empty( $date_query ) ) {
            $wp_query->set( 'date_query', [ $date_query ] );
        }
    }

    /**
     * Get year choices
     *
     * @return array
     */
    protected function get_year_choices() : array {
        $cache_key = 'post-year-choices';
        $result    = wp_cache_get( $cache_key );

        if ( ! empty( $result ) ) {
            return $result;
        }

        global $wpdb;

        $years = $wpdb->get_results( // phpcs:ignore
            "SELECT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' GROUP BY YEAR(post_date) DESC",
            ARRAY_N
        );

        $result = [];

        if ( is_array( $years ) && count( $years ) > 0 ) {
            foreach ( $years as $year ) {
                $result[] = intval( $year[0] );
            }
        }

        wp_cache_set( $cache_key, $result, '', HOUR_IN_SECONDS );

        return $result;
    }

    /**
     * Get the page title.
     *
     * @return string
     */
    public function page_title() : string {
        return get_the_title( get_queried_object_id() );
    }

    /**
     * Get highlight item
     *
     * @return object|null
     */
    public function highlight() : ?object {
        $highlight = get_field( 'highlight', get_option( 'page_for_posts' ) );

        if ( empty( $highlight ) ) {
            return null;
        }

        return Post::enrich_post( $highlight, Category::has_multiple() );
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function filters() : array {
        $current_year_filter  = static::get_filter_year();
        $current_month_filter = static::get_filter_month();

        $month_strings = ( new Strings() )->s()['months'];
        $months        = [];
        $idx           = 1;

        foreach ( $month_strings as $month ) {
            $months[] = [
                'name'        => $month,
                'key'         => $idx,
                'is_selected' => $current_month_filter === $idx ? 'selected' : '',
            ];

            $idx ++;
        }

        $years        = $this->get_year_choices();
        $year_choices = [];

        foreach ( $years as $year ) {
            $year_choices[] = [
                'name'        => $year,
                'key'         => $year,
                'is_selected' => $current_year_filter === $year ? 'selected' : '',
            ];
        }

        return [
            'categories'      => $this->get_filter_categories(),
            'months'          => $months,
            'years'           => $year_choices,
            'active_category' => static::get_filter_category(),
        ];
    }

    /**
     * Get filter categories
     *
     * @return array
     */
    protected function get_filter_categories() : array {
        $categories = get_categories();

        if ( empty( $categories ) ) {
            return [];
        }

        $year_filter     = static::get_filter_year();
        $month_filter    = static::get_filter_month();
        $category_filter = static::get_filter_category();
        $permalink       = get_the_permalink( get_queried_object_id() );

        $categories = array_map( function ( $item ) use ( $permalink, $category_filter, $month_filter, $year_filter ) {
            $item->is_active = $category_filter === $item->term_id;
            $item->url       = add_query_arg(
                [
                    'filter-category' => $item->term_id,
                    'filter-month'    => $month_filter,
                    'filter-year'     => $year_filter,
                ],
                $permalink
            );

            return $item;
        }, $categories );

        array_unshift( $categories, [
            'name'      => __( 'All', 'tms-theme-base' ),
            'url'       => add_query_arg(
                [
                    'filter-month' => $month_filter,
                    'filter-year'  => $year_filter,
                ],
                $permalink
            ),
            'is_active' => empty( $category_filter ),
        ] );

        return $categories;
    }

    /**
     * Get articles
     *
     * @return array|null
     */
    public function articles() : ?array {
        global $wp_query;

        if ( empty( $wp_query->posts ) ) {
            return [];
        }

        $this->set_pagination_data( $wp_query );

        $display_categories = Category::has_multiple();
        $use_images         = Settings::get_setting( 'archive_use_images' ) ?? true;

        return array_map( function ( $article ) use ( $display_categories, $use_images ) {
            return Post::enrich_post( $article, $display_categories, $use_images );
        }, $wp_query->posts );
    }

    /**
     * Set pagination data
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    protected function set_pagination_data( $wp_query ) : void {
        $per_page = get_option( 'posts_per_page' );
        $paged    = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $this->pagination           = new stdClass();
        $this->pagination->page     = $paged;
        $this->pagination->per_page = $per_page;
        $this->pagination->items    = $wp_query->found_posts;
        $this->pagination->max_page = (int) ceil( $wp_query->found_posts / $per_page );
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
