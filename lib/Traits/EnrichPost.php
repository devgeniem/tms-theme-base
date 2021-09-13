<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

use TMS\Theme\Base\Images;

/**
 * Trait EnrichPost
 *
 * Provides additional post data.
 *
 * @package TMS\Theme\Base\Traits
 */
trait EnrichPost {

    /**
     * Enrich post data
     *
     * @param \WP_Post $post               The target post object.
     * @param bool     $display_categories Include categories.
     * @param bool     $use_images         Include images. Defaults to true.
     * @param int      $excerpt_length     Set custom excerpt length. Defaults to 160.
     *
     * @return \WP_Post
     */
    public static function enrich_post(
        \WP_Post $post,
        bool $display_categories,
        bool $use_images = true,
        int $excerpt_length = 160
    ) {
        if ( $use_images ) {
            $post->featured_image = has_post_thumbnail( $post->ID )
                ? get_post_thumbnail_id( $post->ID )
                : Images::get_default_image_id();
        }

        $post->permalink = get_permalink( $post->ID );
        $post->excerpt   = get_the_excerpt( $post );

        if ( ! has_excerpt( $post->ID ) && strlen( $post->excerpt ) > $excerpt_length ) {
            $post->excerpt = trim( substr( $post->excerpt, 0, $excerpt_length ) );
        }

        if ( $display_categories ) {
            $primary_category = self::get_primary_category( $post->ID );

            if ( ! empty( $primary_category ) ) {
                $post->category = $primary_category;
            }
        }

        return $post;
    }
}
