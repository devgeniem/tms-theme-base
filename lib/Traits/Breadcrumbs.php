<?php
/**
 * Breadcrumbs formatting.
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

use TMS\Theme\Base\PostType;
use TMS\Theme\Base\Taxonomy\BlogCategory;
use TMS\Theme\Base\Taxonomy\Category;

/**
 * Trait Breadcrumbs
 *
 * @package TMS\Theme\Base\Traits
 */
trait Breadcrumbs {

    /**
     * Format by queried type.
     *
     * @param string $current_type Queried post type slug.
     * @param int    $current_id   Queried ID.
     * @param string $home_url     Home page url.
     * @param array  $breadcrumbs  Breadcrumbs to format.
     *
     * @return array
     */
    private function prepare_by_type( $current_type, $current_id, $home_url = '', $breadcrumbs = [] ) : array {
        $breadcrumbs = apply_filters(
            'tms/base/breadcrumbs/before_prepare',
            $breadcrumbs,
            $current_type,
            $current_id,
            $home_url,
        );

        switch ( $current_type ) {
            case PostType\Page::SLUG:
                $breadcrumbs = $this->format_page( $current_id, $home_url, $breadcrumbs );
                break;
            case PostType\Post::SLUG:
                $breadcrumbs = $this->format_post( $current_id, $breadcrumbs );
                break;
            case PostType\BlogArticle::SLUG:
                $breadcrumbs = $this->format_blog_article( $current_id, $breadcrumbs );
                break;
            case 'post-type-archive':
                $breadcrumbs = $this->format_post_type_archive( $breadcrumbs );
                break;
            case 'tax-archive':
                $breadcrumbs = $this->format_tax_archive( $breadcrumbs );
                break;
            case PostType\DynamicEvent::SLUG:
                $breadcrumbs = $this->format_page( $current_id, $home_url, $breadcrumbs );
                break;
        }

        return apply_filters( 'tms/base/breadcrumbs/after_prepare', $breadcrumbs );
    }

    /**
     * Format breadcrumbs for: Post
     *
     * @param int   $current_id  Current page ID.
     * @param array $breadcrumbs Breadcrumbs array.
     *
     * @return array
     */
    private function format_post( $current_id, array $breadcrumbs ) : array {
        $breadcrumbs['home'] = $this->get_home_link();

        $primary_category = Category::get_post_categories( $current_id );

        if ( ! empty( $primary_category ) ) {
            $breadcrumbs[] = [
                'title'     => $primary_category[0]->name,
                'permalink' => $primary_category[0]->permalink,
                'icon'      => false,
            ];
        }

        $breadcrumbs[] = [
            'title'     => get_the_title( $current_id ),
            'permalink' => false,
            'icon'      => false,
            'is_active' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Format breadcrumbs for: Post
     *
     * @param int   $current_id  Current page ID.
     * @param array $breadcrumbs Breadcrumbs array.
     *
     * @return array
     */
    private function format_blog_article( $current_id, array $breadcrumbs ) : array {
        $breadcrumbs['home'] = $this->get_home_link();

        $primary_category = BlogCategory::get_primary_category( $current_id );

        if ( ! empty( $primary_category ) ) {
            $breadcrumbs[] = [
                'title'     => $primary_category->name,
                'permalink' => $primary_category->permalink,
                'icon'      => false,
            ];
        }
        else {
            $post_type = get_post_type_object( PostType\BlogArticle::SLUG );

            $breadcrumbs[] = [
                'title'     => esc_html( $post_type->labels->singular_name ),
                'permalink' => get_post_type_archive_link( PostType\BlogArticle::SLUG ),
                'icon'      => false,
            ];
        }

        $breadcrumbs[] = [
            'title'     => get_the_title( $current_id ),
            'permalink' => false,
            'icon'      => false,
            'is_active' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Format breadcrumbs for: Page
     *
     * @param int    $current_id  Current page ID.
     * @param string $home_url    Home URL.
     * @param array  $breadcrumbs Breadcrumbs array.
     *
     * @return array
     */
    private function format_page( $current_id, string $home_url, array $breadcrumbs ) : array {
        /**
         * Add current page to breadcrumbs and set its
         * link status to false, unless it's the front page, then remove it.
         */
        if ( trailingslashit( get_the_permalink( $current_id ) ) !== $home_url ) {
            $breadcrumbs[] = [
                'title'     => get_the_title( $current_id ),
                'permalink' => false,
                'icon'      => false,
                'is_active' => true,
            ];
        }
        else {
            unset( $breadcrumbs['home'] ); // Not showing frontpage on frontpage.
        }

        return $breadcrumbs;
    }

    /**
     * Format breadcrumbs for: Post Type Archive
     *
     * @param array $breadcrumbs Breadcrumbs array.
     *
     * @return array
     */
    private function format_post_type_archive( array $breadcrumbs ) : array {
        $breadcrumbs['home'] = $this->get_home_link();

        $queried_object = get_queried_object();

        $breadcrumbs[] = [
            'title'     => $queried_object->label,
            'permalink' => get_post_type_archive_link( $queried_object->name ),
            'icon'      => false,
            'is_active' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Format breadcrumbs for: Archive
     *
     * @param array $breadcrumbs Breadcrumbs array.
     *
     * @return array
     */
    private function format_tax_archive( array $breadcrumbs ) : array {
        $breadcrumbs['home'] = $this->get_home_link();

        $queried_object = get_queried_object();

        $breadcrumbs[] = [
            'title'     => $queried_object->name,
            'permalink' => get_term_link( $queried_object->term_id ),
            'icon'      => false,
            'is_active' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Get Object Ancestors.
     *
     * @param int|null $queried_object_id Ancestors of this ID.
     * @param string   $object_type       Type of ancestors to get.
     * @param array    $breadcrumbs       Array where the results should be added to.
     *
     * @return array
     */
    public function get_ancestors( int $queried_object_id = null, string $object_type = 'page', array $breadcrumbs = [] ) : array { // phpcs:ignore
        $home_url          = trailingslashit( get_home_url() );
        $ancestors         = get_ancestors( $queried_object_id, $object_type );
        $ancestors_reverse = array_reverse( $ancestors );

        /**
         * Add all page ancestors to breadcrumbs.
         */
        foreach ( $ancestors_reverse as $ancestor ) {
            $permalink = trailingslashit( get_permalink( $ancestor ) );

            if ( $permalink === $home_url ) {
                continue;
            }

            $breadcrumbs[] = [
                'title'     => get_the_title( $ancestor ),
                'permalink' => $permalink,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Breadcrumbs formatter: One place to format them all.
     *
     * @param array $breadcrumbs Array of breadcrumbs to format.
     *
     * @return array Formatted breadcrumbs.
     */
    public function format_breadcrumbs( array $breadcrumbs = [] ) : array {
        $count = count( $breadcrumbs );

        if ( $count < 2 ) { // No need to show the first level, or empty.
            return [];
        }

        $first      = array_shift( $breadcrumbs );
        $last_three = array_splice( $breadcrumbs, - 3, 3 ); // Last 3 available.

        $prefix = [ $first ];

        // Add padding (...) between the first and last 3, if we had more than 4 breadcrumbs.
        if ( $count > 4 ) {
            $prefix[] = [
                'title'     => '...',
                'permalink' => false,
                'icon'      => false,
                'class'     => 'pl-1 pr-2',
            ];
        }

        $breadcrumbs = array_merge( $prefix, $last_three ); // First, padding ... (if needed), and 3 last items.
        $breadcrumbs = array_filter( $breadcrumbs );

        return array_map( static function ( $crumb ) {
            $crumb['class']      = $crumb['class'] ?? [];
            $crumb['icon']       = $crumb['icon'] ?? false;
            $crumb['icon_class'] = $crumb['icon_class'] ?? 'icon--large';

            return $crumb;
        }, $breadcrumbs ?? [] );
    }

    /**
     * Generates the most used link.
     *
     * @return array
     */
    private function get_home_link() : array {
        return [
            'title'     => _x( 'Home', 'Breadcrumbs', 'tms-theme-base' ),
            'permalink' => trailingslashit( get_home_url() ),
            'icon'      => '',
        ];
    }
}
