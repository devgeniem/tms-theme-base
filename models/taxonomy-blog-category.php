<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Taxonomy\BlogCategory;

/**
 * The TaxonomyBlogCategory class.
 */
class TaxonomyBlogCategory extends Archive {

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

        $display_categories = BlogCategory::has_multiple();
        $use_images         = Settings::get_setting( 'archive_use_images' ) ?? true;

        return array_map( function ( $article ) use ( $display_categories, $use_images ) {
            return BlogArticle::enrich_post( $article, $display_categories, $use_images );
        }, $wp_query->posts );
    }
}
