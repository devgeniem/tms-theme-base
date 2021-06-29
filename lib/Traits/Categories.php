<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

use WP_Term;

/**
 * Trait PrimaryCategory
 *
 * Provides additional post data.
 *
 * @package TMS\Theme\Base\Traits
 */
trait Categories {

    /**
     * Get primary category
     *
     * @param int $post_id Post ID.
     *
     * @return WP_Term|null
     */
    public static function get_primary_category( $post_id ) : ?WP_Term {
        $primary_category_id = (int) get_post_meta( $post_id, '_primary_term_' . self::SLUG, true );

        if ( empty( $primary_category_id ) ) {
            $categories = self::get_post_categories( $post_id );

            if ( ! empty( $categories ) ) {
                return $categories[0];
            }
        }

        $primary_term = get_term( $primary_category_id, self::SLUG );

        if ( is_null( $primary_term ) || is_wp_error( $primary_term ) ) {
            return null;
        }

        $primary_term->permalink = get_term_link( $primary_term, static::SLUG );

        return $primary_term;
    }

    /**
     * Has multiple categories
     *
     * @return bool
     */
    public static function has_multiple() {
        $categories = get_categories( [
            'hide_empty' => true,
            'taxonomy'   => self::SLUG,
        ] );

        if ( empty( $categories ) ) {
            return false;
        }

        return 1 < count( $categories );
    }

    /**
     * Get post categories
     *
     * @param int $post_id WP_Post ID.
     *
     * @return array
     */
    public static function get_post_categories( int $post_id ) : array {
        $categories = wp_get_post_terms( $post_id, self::SLUG );

        if ( empty( $categories ) ) {
            return [];
        }

        return array_map( function ( $item ) {
            $item->permalink = get_term_link( $item, self::SLUG );

            return $item;
        }, $categories );
    }
}
