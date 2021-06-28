<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Taxonomy\BlogCategory;
use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\Traits\Pagination;

/**
 * The ArchiveBlogArticle class.
 */
class ArchiveBlogArticle extends Home {

    use Pagination;

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
     * Get the blog title.
     *
     * @return string|null
     */
    public function page_title() : ?string {
        return Settings::get_setting( 'blog_name' );
    }

    /**
     * Get the blog subtitle.
     *
     * @return string|null
     */
    public function page_subtitle() : ?string {
        return Settings::get_setting( 'blog_subtitle' );
    }

    /**
     * Get the blog subtitle.
     *
     * @return string|null
     */
    public function page_description() : ?string {
        return Settings::get_setting( 'blog_description' );
    }

    /**
     * Get the blog logo.
     *
     * @return string|null
     */
    public function page_logo() : ?string {
        return Settings::get_setting( 'blog_logo' );
    }

    /**
     * Get highlight item
     *
     * @return object|null
     */
    public function highlight() : ?object {
        $highlight = self::get_highlight();

        if ( empty( $highlight ) ) {
            return null;
        }

        return BlogArticle::enrich_post( $highlight, BlogCategory::has_multiple() );
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

        $display_categories = BlogCategory::has_multiple();
        $use_images         = Settings::get_setting( 'archive_use_images' ) ?? true;

        return array_map( function ( $article ) use ( $display_categories, $use_images ) {
            return BlogArticle::enrich_post( $article, $display_categories, $use_images );
        }, $wp_query->posts );
    }

    /**
     * Modify query
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    public static function modify_query( WP_Query $wp_query ) : void {
        if (
            is_admin() ||
            ( ! $wp_query->is_main_query() || ! $wp_query->is_post_type_archive( BlogArticle::SLUG ) )
        ) {
            return;
        }

        $highlight = self::get_highlight();

        if ( ! empty( $highlight ) ) {
            $wp_query->set( 'post__not_in', [ $highlight->ID ] );
        }

        static::modify_query_date( $wp_query );
    }

    /**
     * Get highlight post
     *
     * @return mixed
     */
    protected static function get_highlight() {
        return Settings::get_setting( 'blog_archive_highlight' );
    }

    /**
     * Get filter categories
     *
     * @return array
     */
    protected function get_filter_categories() : array {
        $categories = get_terms( [
            'taxonomy'   => BlogCategory::SLUG,
            'hide_empty' => true,
        ] );

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

        array_unshift( $categories, [
            'name'      => __( 'All', 'tms-theme-base' ),
            'url'       => add_query_arg(
                [
                    'filter-month' => $month_filter,
                    'filter-year'  => $year_filter,
                ],
                get_post_type_archive_link( BlogArticle::SLUG )
            ),
            'is_active' => empty( $category_filter ),
        ] );

        return $categories;
    }
}
