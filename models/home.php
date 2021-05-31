<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

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

        $years = $wpdb->get_results(
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

        return $this->enrich_article( $highlight, Category::has_multiple() );
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

        $current_year_filter     = static::get_filter_year();
        $current_month_filter    = static::get_filter_month();
        $current_category_filter = static::get_filter_category();
        $permalink               = get_the_permalink( get_queried_object_id() );

        $categories = array_map( function ( $item ) use ( $permalink, $current_category_filter, $current_month_filter, $current_year_filter ) {
            $item->is_active = $current_category_filter === $item->term_id;
            $item->url       = add_query_arg(
                [
                    'filter-category' => $item->term_id,
                    'filter-month'    => $current_month_filter,
                    'filter-year'     => $current_year_filter,
                ],
                $permalink
            );

            return $item;
        }, $categories );

        array_unshift( $categories, [
            'name'      => __( 'All', 'tms-theme-base' ),
            'url'       => add_query_arg(
                [
                    'filter-month' => $current_month_filter,
                    'filter-year'  => $current_year_filter,
                ],
                $permalink
            ),
            'is_active' => empty( $current_category_filter ),
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

        return array_map( function ( $article ) use ( $display_categories ) {
            return $this->enrich_article( $article, $display_categories );
        }, $wp_query->posts );
    }

    /**
     * Enrich article data
     *
     * @param WP_Post $article            WP_Post.
     * @param bool    $display_categories Should category to be displayed.
     * @param int     $excerpt_length     Excerpt length.
     *
     * @return object
     */
    protected function enrich_article( WP_Post $article, bool $display_categories, int $excerpt_length = 160 ) {
        $article->featured_image = \has_post_thumbnail( $article->ID ) ? \get_post_thumbnail_id( $article->ID ) : null;
        $article->permalink      = \get_permalink( $article->ID );
        $article->excerpt        = \get_the_excerpt( $article->ID );

        if ( strlen( $article->excerpt ) > $excerpt_length ) {
            $article->excerpt = trim( substr( $article->excerpt, 0, $excerpt_length ) );
        }

        if ( $display_categories ) {
            $categories = Category::get_post_categories( $article->ID );

            if ( ! empty( $categories ) ) {
                $article->category = $categories[0];
            }
        }

        return $article;
    }

    /**
     * Set pagination data
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    protected function set_pagination_data( $wp_query ) : void {
        $per_page = \get_option( 'posts_per_page' );
        $paged    = ( \get_query_var( 'paged' ) ) ? \get_query_var( 'paged' ) : 1;

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
