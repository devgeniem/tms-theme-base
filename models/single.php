<?php
/**
 * Define the single post class.
 */

use DustPress\Query;
use TMS\Theme\Base\Taxonomy\BlogCategory;
use TMS\Theme\Base\Taxonomy\BlogTag;
use TMS\Theme\Base\Taxonomy\Category;
use TMS\Theme\Base\Taxonomy\PostTag;
use TMS\Theme\Base\Traits;

/**
 * The Single class.
 */
class Single extends BaseModel {

    use Traits\Sharing;
    use Traits\Components;

    /**
     * Content
     *
     * @return array|object|WP_Post|null
     * @throws Exception If global $post is not available or $id param is not defined.
     */
    public function content() {
        $single = Query::get_acf_post( get_queried_object_id() );

        $single->image = $single->image === 0
            ? false
            : $single->image;

        if ( 'blog-article' === $single->post_type ) {
            $single->categories = BlogCategory::get_post_categories( $single->ID );
            $single->tags       = BlogTag::get_post_categories( $single->ID );
        }
        else {
            $single->categories = Category::get_post_categories( $single->ID );
            $single->tags       = PostTag::get_post_tags( $single->ID );
        }

        return $single;
    }

    /**
     * Get related posts
     *
     * @return array|null
     */
    public function related() : ?array {
        $post_id    = get_queried_object_id();
        $term_args  = [ 'fields' => 'ids' ];
        $categories = wp_get_post_terms( $post_id, Category::SLUG, $term_args );
        $limit      = 4;

        $args = [
            'post_type'      => get_post_type( get_the_ID() ),
            'posts_per_page' => $limit,
            'no_found_rows'  => true,
            'post__not_in'   => [ $post_id ],
        ];

        if ( ! empty( $categories ) ) {
            $args['category__in'] = $categories;
        }

        $posts = Query::get_posts( $args );

        if ( empty( $posts ) || count( $posts ) < $limit ) {
            return null;
        }

        $posts = apply_filters(
            'tms/single/related',
            array_map( function ( $item ) {
                $categories = wp_get_post_terms( $item->ID, Category::SLUG );

                if ( ! empty( $categories ) ) {
                    $item->category      = $categories[0]->name;
                    $item->category_link = get_category_link( $categories[0]->ID );
                }

                $item->image_id = $item->image_id === 0
                    ? false
                    : $item->image_id;

                if ( ! has_excerpt( $item->ID ) ) {
                    $item->post_excerpt = $this->get_related_excerpt( $item );
                }

                return $item;
            }, $posts )
        );

        return [
            'title' => get_field( 'related_title' ) ?? '',
            'posts' => $posts,
            'link'  => get_field( 'related_link' ) ?? '',
        ];
    }

    /**
     * Get related post excerpt.
     *
     * @param WP_Post $item           Related post item.
     * @param int     $excerpt_length Target excerpt length.
     */
    protected function get_related_excerpt( $item, $excerpt_length = 10 ) : string {
        $item_excerpt = get_the_excerpt( $item->ID );

        return strlen( $item_excerpt ) > $excerpt_length
            ? wp_trim_words( $item_excerpt, $excerpt_length, '...' )
            : $item_excerpt;
    }
}
