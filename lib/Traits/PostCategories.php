<?php
/**
 * Post Categories.
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

/**
 * Trait PostCategories
 *
 * @package TMS\Theme\Base\Traits
 */
trait PostCategories {

    /**
     * Get post categories
     *
     * @param int $post_id WP Post ID.
     *
     * @return array Post categories
     */
    protected function get_post_categories( int $post_id ) : array {
        $categories = wp_get_post_categories( $post_id, [ 'fields' => 'all' ] );

        return array_map( function ( $category ) {
            $category->url = get_category_link( $category->term_id );

            return $category;
        }, $categories );
    }
}
