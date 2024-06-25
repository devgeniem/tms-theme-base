<?php
/**
 * Define the generic Page class.
 */

use TMS\Theme\Base\Traits\Components;
use TMS\Theme\Base\Settings;

/**
 * The Page class.
 */
class PageExtend extends BaseModel {

    use Components;

    /**
     * Hooks
     */
    public function hooks() : void {
        \add_filter( 'tms/theme/breadcrumbs/show_breadcrumbs_in_header', fn() => false );
    }

    /**
     * Hero image
     *
     * @return int|null
     */
    public function hero_image() : ?int {
        return \has_post_thumbnail()
            ? \get_post_thumbnail_id()
            : null;
    }

    /**
     * Get post siblings.
     *
     * @return array|array[]|false
     */
    public function post_siblings() {
        $current_post_id = \get_the_ID();
        $parent_post_id  = \wp_get_post_parent_id( $current_post_id );

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
            'orderby'                => 'menu_order title',
            'order'                  => 'ASC',
        ];

        $wp_query = new \WP_Query( $query_args );

        if ( 1 >= count( $wp_query->posts ) ) {
            return false;
        }

        return array_map( function ( $post ) use ( $current_post_id ) {
            $post->permalink  = \get_the_permalink( $post->ID );
            $post->is_current = $post->ID === $current_post_id;

            return $post;
        }, $wp_query->posts );
    }

    /**
     * Use overlay
     *
     * @return bool
     */
    public function use_overlay() {
        return \get_field( 'remove_overlay' ) === true ? false : true;
    }
}
