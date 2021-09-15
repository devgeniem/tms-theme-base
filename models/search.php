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
        $results_text  = sprintf(
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

        return [
            'summary' => $results_text,
            'posts'   => $wp_query->have_posts() ? $this->enrich_results( $wp_query->posts ) : [],
            'query'   => $wp_query,
        ];
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
            $meta = '';

            switch ( $post_item->post_type ) {
                case Page::SLUG:
                    $meta = dustpress()->render( [
                        'partial' => 'breadcrumbs',
                        'type'    => 'html',
                        'echo'    => false,
                        'data'    => [
                            'breadcrumbs' => $this->format_page(
                                $post_item->ID,
                                '',
                                [ $this->get_home_link() ]
                            ),
                        ],
                    ] );

                    break;
                case Post::SLUG:
                    $meta = $this->format_result_item_meta(
                        $post_item,
                        Post::get_primary_category( $post_item->ID )
                    );

                    break;
                case BlogArticle::SLUG:
                    $meta = $this->format_result_item_meta(
                        $post_item,
                        BlogArticle::get_primary_category( $post_item->ID )
                    );

                    break;
                default:
                    $meta = $this->format_result_item_meta( $post_item->ID );

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
     * @return string|bool Meta as html.
     */
    private function format_result_item_meta( $post_item, $tax_term = null ) {
        $meta_data['date'] = [
            'date_formatted' => get_the_date( '', $post_item->ID ),
            'date'           => $post_item->post_date,
        ];

        if ( ! empty( $tax_term ) ) {
            $meta_data['category'] = [
                'name'      => $tax_term->name,
                'permalink' => get_term_link( $tax_term->term_id ),
            ];
        }

        return dustpress()->render( [
            'partial' => 'search-item-meta',
            'type'    => 'html',
            'echo'    => false,
            'data'    => [
                'meta' => $meta_data,
            ],
        ] );
    }
}
