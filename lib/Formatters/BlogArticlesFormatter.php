<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\Taxonomy\BlogCategory;

/**
 * Class SubpagesFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class BlogArticlesFormatter extends ArticlesFormatter {

    /**
     * Define formatter name
     */
    const NAME = 'BlogArticles';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/blog_articles/data',
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
            'post_type'              => BlogArticle::SLUG,
            'posts_per_page'         => $data['number'] ?? 12,
            'update_post_meta_cache' => false,
            'no_found_rows'          => true,
        ];

        if ( $data['highlight_article'] ) {
            $data['highlight']    = BlogArticle::enrich_post( $data['highlight_article'], true );
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
            $args['tax_query'] = [
                [
                    'taxonomy' => BlogCategory::SLUG,
                    'terms'    => $data['category'],
                ],
            ];
        }

        $wp_query = new \WP_Query( $args );

        if ( $wp_query->have_posts() ) {
            foreach ( $wp_query->posts as $post_item ) {
                if ( $is_manual_feed && ! empty( $manual_posts[ $post_item->ID ]['excerpt'] ) ) {
                    $post_item->post_excerpt = $manual_posts[ $post_item->ID ]['excerpt'];
                }

                $data['posts'][] = BlogArticle::enrich_post( $post_item, true, $data['display_image'] );
            }
        }

        return $data;
    }
}
