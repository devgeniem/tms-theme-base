<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;
use TMS\Theme\Base\PostType\Post;

/**
 * Class SubpagesFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class ArticlesFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Articles';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/articles/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $args = [
            'post_type'              => Post::SLUG,
            'posts_per_page'         => ( ! empty( $data['number'] ) ) ? $data['number'] : 12,
            'update_post_meta_cache' => false,
            'no_found_rows'          => true,
        ];

        if ( $data['highlight_article'] ) {
            $data['highlight']    = Post::enrich_post( $data['highlight_article'], true );
            $args['post__not_in'] = [
                $data['highlight_article']->ID,
            ];
        }

        $is_manual_feed = 'manual' === $data['feed_type'];
        $manual_posts   = [];

        if ( $is_manual_feed && ! empty( $data['article_repeater'] ) ) {
            $manual_posts = $this->format_repeater_data( $data['article_repeater'] );

            if ( empty( $manual_posts ) ) {
                return $data;
            }

            $args['post__in'] = array_keys( $manual_posts );
            $args['orderby']  = 'post__in';
        }

        if ( ! $is_manual_feed && ! empty( $data['category'] ) ) {
            $args['category__in'] = $data['category'];
        }

        $wp_query = new \WP_Query( $args );

        if ( $wp_query->have_posts() ) {
            foreach ( $wp_query->posts as $post ) {
                if ( $is_manual_feed && ! empty( $manual_posts[ $post->ID ]['excerpt'] ) ) {
                    $post->post_excerpt = $manual_posts[ $post->ID ]['excerpt'];
                }

                $data['posts'][] = Post::enrich_post( $post, true, $data['display_image'] );
            }
        }

        return $data;
    }

    /**
     * Format repeater articles.
     *
     * @param array $repeater_data Repeater rows.
     */
    private function format_repeater_data( array $repeater_data ) : array {
        $items = [];

        foreach ( $repeater_data as $repeater_row ) {
            if ( empty( $repeater_row['article_item']['article'] ) ) {
                continue;
            }

            $article_item_excerpt = $repeater_row['article_item']['article_excerpt'];

            $items[ $repeater_row['article_item']['article'] ]['excerpt'] = $article_item_excerpt;
        }

        return $items;
    }
}
