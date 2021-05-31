<?php
/**
 * Breadcrumbs formatting.
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

/**
 * Trait Breadcrumbs
 *
 * @package TMS\Theme\Base\Traits
 */
trait Breadcrumbs {
    /**
     * Get Object Ancestors.
     *
     * @param int|null $queried_object_id Ancestors of this ID.
     * @param string   $object_type       Type of ancestors to get.
     * @param array    $breadcrumbs       Array where the results should be added to.
     *
     * @return array
     */
    public function get_ancestors(
        int $queried_object_id = null,
        string $object_type = 'page',
        array $breadcrumbs = []
    ) : array {
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
            'icon'      => 'icon-koti',
        ];
    }
}
