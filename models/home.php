<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\Taxonomy\Category;

/**
 * The Home class.
 */
class Home extends BaseModel {

    /**
     * Pagination data.
     *
     * @var object
     */
    private object $pagination;

    /**
     * Get the page title.
     *
     * @return string
     */
    public function page_title() : string {
        return get_the_title( get_queried_object_id() );
    }

    /**
     * Get highlight item
     *
     * @return object|null
     */
    public function highlight() : ?object {
        $highlight = get_field( 'highlight', get_queried_object_id() );

        if ( empty( $highlight ) ) {
            return null;
        }

        return $this->enrich_article( $highlight, Category::has_multiple() );
    }

    /**
     * Get articles
     *
     * @return array|null
     */
    public function articles() : ?array {
        global $wp_query;

        if ( empty( $wp_query->posts ) ) {
            return [];
        }

        $this->set_pagination_data( $wp_query );

        $display_categories = Category::has_multiple();

        return array_map( function ( $article ) use ( $display_categories ) {
            return $this->enrich_article( $article, $display_categories );
        }, $wp_query->posts );
    }

    /**
     * Enrich article data
     *
     * @param WP_Post $article            WP_Post.
     * @param bool    $display_categories Should category to be displayed.
     * @param int     $excerpt_length     Excerpt length.
     *
     * @return object
     */
    protected function enrich_article( WP_Post $article, bool $display_categories, int $excerpt_length = 160 ) {
        $article->featured_image = \has_post_thumbnail( $article->ID ) ? \get_post_thumbnail_id( $article->ID ) : null;
        $article->permalink      = \get_permalink( $article->ID );
        $article->excerpt        = \get_the_excerpt( $article->ID );

        if ( strlen( $article->excerpt ) > $excerpt_length ) {
            $article->excerpt = trim( substr( $article->excerpt, 0, $excerpt_length ) );
        }

        if ( $display_categories ) {
            $categories = Category::get_post_categories( $article->ID );

            if ( ! empty( $categories ) ) {
                $article->category = $categories[0];
            }
        }

        return $article;
    }

    /**
     * Set pagination data
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     *
     * @return void
     */
    protected function set_pagination_data( $wp_query ) : void {
        $per_page = \get_option( 'posts_per_page' );
        $paged    = ( \get_query_var( 'paged' ) ) ? \get_query_var( 'paged' ) : 1;

        $this->pagination           = new stdClass();
        $this->pagination->page     = $paged;
        $this->pagination->per_page = $per_page;
        $this->pagination->items    = $wp_query->found_posts;
        $this->pagination->max_page = (int) ceil( $wp_query->found_posts / $per_page );
    }

    /**
     * Returns pagination data.
     *
     * @return object
     */
    public function pagination() : ?object {
        if ( isset( $this->pagination->page ) && isset( $this->pagination->max_page ) ) {
            if ( $this->pagination->page <= $this->pagination->max_page ) {
                return $this->pagination;
            }
        }

        return null;
    }
}
