<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use \TMS\Theme\Base\Interfaces\PostType;
use TMS\Theme\Base\Taxonomy\Category;
use TMS\Theme\Base\Traits\EnrichPost;

/**
 * This class defines the post type.
 *
 * @package TMS\Theme\Base\PostType
 */
class Post implements PostType {

    use EnrichPost;

    /**
     * This defines the slug of this post type.
     */
    const SLUG = 'post';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {}

    /**
     * Get primary category.
     *
     * @param string $post_id Post ID.
     *
     * @return \WP_Term|null
     */
    public static function get_primary_category( $post_id ) {
        return Category::get_primary_category( $post_id );
    }

    public static function enrich_post(
        \WP_Post $post,
        bool $display_categories,
        bool $use_images = true,
        int $excerpt_length = 160
    ) {
        if ( $use_images ) {
            $post->featured_image = has_post_thumbnail( $post->ID )
                ? get_post_thumbnail_id( $post->ID )
                : null;
        }

        $post->permalink = get_permalink( $post->ID );
        $post->excerpt   = get_the_excerpt( $post );

        if ( ! has_excerpt( $post->ID ) && strlen( $post->excerpt ) > $excerpt_length ) {
            $post->excerpt = trim( substr( $post->excerpt, 0, $excerpt_length ) );
        }

        if ( $display_categories ) {
            $categories = Category::get_post_categories( $post->ID );

            if ( ! empty( $categories ) ) {
                $post->category = $categories[0];
            }
        }

        return $post;
    }
}
