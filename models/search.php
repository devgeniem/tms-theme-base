<?php
/**
 * Define the search class.
 */

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;
use TMS\Theme\Base\Traits\Breadcrumbs;

/**
 * The Search class.
 */
class Search extends BaseModel {

    use Breadcrumbs;

    /**
     * Get search results.
     *
     * @throws Exception If global $post is not available or $id param is not defined.
     * @global WP_Query $wp_query The main query object.
     */
    public function results() {
        global $wp_query;

        $search_clause = get_search_query();
        $result_count  = $wp_query->found_posts;

        if ( $wp_query->have_posts() ) {
            $results_text = sprintf(
            // translators: 1. placeholder is number of search results, 2. placeholder contains the search term(s).
                _nx(
                    '%1$1s result found for "%2$2s"',
                    '%1$1s results found for "%2$2s"',
                    $result_count,
                    'search results summary',
                    'tms-theme-base'
                ),
                $result_count,
                $search_clause
            );
        }
        else {
            $results_text = __( 'No search results', 'tms-theme-base' );
        }

        return [
            'summary'    => $results_text,
            'posts'      => $wp_query->have_posts() ? $this->enrich_results( $wp_query->posts ) : [],
            'pagination' => [
                'paged'          => $wp_query->query_vars['paged'] > 1 ? $wp_query->query_vars['paged'] : 1,
                'posts_per_page' => $wp_query->query_vars['posts_per_page'],
                'found_posts'    => $wp_query->found_posts,
                'max_num_pages'  => $wp_query->max_num_pages,
            ],
        ];
    }

    /**
     * Get template classes.
     *
     * @return array
     */
    public function template_classes() {
        return apply_filters(
            'tms/theme/search/search_item',
            [
                'search_item' => 'has-background-secondary',
            ]
        );
    }

    /**
     * Enrich results.
     *
     * @param array $posts Posts.
     *
     * @return mixed
     */
    private function enrich_results( $posts ) {
        foreach ( $posts as $post_item ) {
            $meta = false;

            switch ( $post_item->post_type ) {
                case Page::SLUG:
                    $post_item->content_type = get_post_type_object( Page::SLUG )->labels->singular_name;
                    $post_item->breadcrumbs  = $this->prepare_by_type(
                        Page::SLUG,
                        $post_item->ID,
                        '',
                        [ $this->get_home_link() ],
                    );

                    break;
                case Post::SLUG:
                    $post_item->content_type = get_post_type_object( Post::SLUG )->labels->singular_name;

                    $meta = $this->format_result_item_meta(
                        $post_item,
                        Post::get_primary_category( $post_item->ID )
                    );

                    break;
                case BlogArticle::SLUG:
                    $post_item->content_type = get_post_type_object( BlogArticle::SLUG )->labels->singular_name;

                    $meta = $this->format_result_item_meta(
                        $post_item,
                        BlogArticle::get_primary_category( $post_item->ID )
                    );

                    break;
                default:
                    $meta = $this->format_result_item_meta( $post_item );

                    break;
            }

            $post_item->meta      = $meta;
            $post_item->permalink = get_permalink( $post_item->ID );
        }

        return $posts;
    }

    /**
     * Format result item meta data.
     *
     * @param WP_Post      $post_item Result item post object.
     * @param WP_Term|null $tax_term  Related tax term.
     *
     * @return array        Meta data.
     */
    private function format_result_item_meta( $post_item, $tax_term = null ) {
        $meta_data['date'] = $post_item->post_date;

        if ( ! empty( $tax_term ) ) {
            $meta_data['category'] = [
                'name'      => $tax_term->name,
                'permalink' => get_term_link( $tax_term->term_id ),
            ];
        }

        return $meta_data;
    }
}
