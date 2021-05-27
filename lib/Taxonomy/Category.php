<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Taxonomy;

use \TMS\Theme\Base\Interfaces\Taxonomy;

/**
 * This class defines the taxonomy.
 *
 * @package TMS\Theme\Base\Taxonomy
 */
class Category implements Taxonomy {

    /**
     * This defines the slug of this taxonomy.
     */
    const SLUG = 'category';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
    }

    /**
     * Get post categories
     *
     * @param int $post_id WP_Post ID.
     *
     * @return array
     */
    public static function get_post_categories( int $post_id ) : array {
        $categories = wp_get_post_terms( $post_id, static::SLUG );

        if ( empty( $categories ) ) {
            return [];
        }

        return array_map( function ( $item ) {
            $item->permalink = get_term_link( $item, static::SLUG );

            return $item;
        }, $categories );
    }

    /**
     * Has multiple categories
     *
     * @return bool
     */
    public static function has_multiple() {
        $categories = get_categories( [
            'hide_empty' => true,
        ] );

        if ( empty( $categories ) ) {
            return false;
        }

        return 1 < count( $categories );
    }
}
