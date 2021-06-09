<?php
/**
 * Define the generic Page class.
 */

use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits;

/**
 * The Page class.
 */
class Page extends BaseModel {

    use Traits\Components;

    /**
     * Return page title.
     *
     * @return string
     */
    public function title() : string {
        return get_the_title();
    }

    /**
     * Return featured image ID.
     *
     * @return false|int
     */
    public function featured_image_id() {
        return has_post_thumbnail() ? get_post_thumbnail_id() : false;
    }

    /**
     * Get post siblings.
     *
     * @return array|array[]|false
     */
    public function post_siblings() {
        $current_post_id = get_the_ID();
        $parent_post_id  = wp_get_post_parent_id( $current_post_id );

        if ( ! Settings::get_setting( 'enable_sibling_navigation' ) || $parent_post_id === 0 ) {
            return false;
        }

        $query_args = [
            'post_type'              => 'page',
            'posts_per_page'         => 100,
            'post_parent'            => $parent_post_id,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'no_found_rows'          => true,
            'fields'                 => 'ids',
            'orderby'                => 'title',
            'order'                  => 'ASC',
        ];

        $wp_query = new WP_Query( $query_args );

        if ( 1 >= count( $wp_query->posts ) ) {
            return false;
        }

        return array_map( function ( $post_id ) use ( $current_post_id ) {
            return [
                'title'      => get_the_title( $post_id ),
                'url'        => get_the_permalink( $post_id ),
                'is_current' => $post_id === $current_post_id,
            ];
        }, $wp_query->posts );
    }
}
