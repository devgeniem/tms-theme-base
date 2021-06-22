<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\PostType\Post;

/**
 * The Archive class.
 */
class Archive extends Home {

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
        return is_category()
            ? get_queried_object_id()
            : null;
    }

    /**
     * Modify query
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    public static function modify_query( WP_Query $wp_query ) : void {
        if ( is_admin() || ( ! $wp_query->is_main_query() || ! $wp_query->is_archive() ) ) {
            return;
        }

        if ( is_category() || is_tag() ) {
            static::modify_query_post_type( $wp_query );
        }

        static::modify_query_date( $wp_query );
    }

    /**
     * Get the page title.
     *
     * @return string|null
     */
    public function page_title() : ?string {
        return single_term_title( '', false );
    }

    /**
     * Get highlight item
     *
     * @return object|null
     */
    public function highlight() : ?object {
        return null;
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

        $categories = array_map( function ( $item ) use ( $category_filter, $month_filter, $year_filter ) {
            $item->is_active = $category_filter === $item->term_id;
            $item->url       = add_query_arg(
                [
                    'filter-month' => $month_filter,
                    'filter-year'  => $year_filter,
                ],
                get_category_link( $item )
            );

            return $item;
        }, $categories );

        $home_page = get_option( 'page_for_posts' );

        if ( ! empty( $home_page ) ) {
            array_unshift( $categories, [
                'name'      => __( 'All', 'tms-theme-base' ),
                'url'       => add_query_arg(
                    [
                        'filter-month' => $month_filter,
                        'filter-year'  => $year_filter,
                    ],
                    get_the_permalink( $home_page )
                ),
                'is_active' => empty( $category_filter ),
            ] );
        }

        return $categories;
    }

    /**
     * Modify query post_type param
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    protected static function modify_query_post_type( $wp_query ) {
        $wp_query->set( 'post_type', [ Post::SLUG, BlogArticle::SLUG ] );
    }
}
