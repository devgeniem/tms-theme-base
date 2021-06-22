<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Taxonomy\Category;
use TMS\Theme\Base\PostType\BlogArticle;

/**
 * The Archive class.
 */
class ArchiveBlogArticle extends Archive {

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
        $highlight = Settings::get_setting( 'blog_archive_highlight' );

        if ( empty( $highlight ) ) {
            return null;
        }

        return BlogArticle::enrich_post( $highlight, Category::has_multiple() );
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
