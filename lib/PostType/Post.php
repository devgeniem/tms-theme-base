<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use \TMS\Theme\Base\Interfaces\PostType;
use TMS\Theme\Base\Taxonomy\Category;

/**
 * This class defines the post type.
 *
 * @package TMS\Theme\Base\PostType
 */
class Post implements PostType {

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
     * Enrich post data
     *
     * @param \WP_Post $post
     * @param bool     $display_categories
     * @param bool     $use_images
     * @param int      $excerpt_length
     * @param string   $custom_excerpt
     *
     * @return \WP_Post
     */
    public static function enrich_post(
        \WP_Post $post,
        bool $display_categories,
        bool $use_images = true,
        int $excerpt_length = 160,
        string $custom_excerpt = ''
    ) {
        if ( $use_images ) {
            $post->featured_image = has_post_thumbnail( $post->ID )
                ? get_post_thumbnail_id( $post->ID )
                : null;
        }

        $post->permalink = get_permalink( $post->ID );

        if ( ! empty( $custom_excerpt ) ) {
            $post->excerpt = $custom_excerpt;
        }
        else {
            $post->excerpt = get_the_excerpt( $post->ID );

            if ( strlen( $post->excerpt ) > $excerpt_length ) {
                $post->excerpt = trim( substr( $post->excerpt, 0, $excerpt_length ) );
            }
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
