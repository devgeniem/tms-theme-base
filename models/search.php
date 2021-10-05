<?php
/**
 * Define the search class.
 */

use TMS\Theme\Base\PostType\BlogArticle;
use TMS\Theme\Base\PostType\Page;
use TMS\Theme\Base\PostType\Post;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits\Breadcrumbs;

/**
 * The Search class.
 */
class Search extends BaseModel {

    use Breadcrumbs;

    /**
     * Cpt query var name.
     */
    const SEARCH_CPT_QUERY_VAR = 'search_post_types';

    const SEARCH_START_DATE = 'search_start_date';

    const SEARCH_END_DATE = 'search_end_date';

    /**
     * Hooks
     *
     * @return void
     */
    public static function hooks() {
        add_action( 'pre_get_posts', [ __CLASS__, 'modify_query' ] );
    }

    /**
     * Page title
     */
    public function page_title() : string {
        return __( 'Search results', 'tms-theme-base' );
    }

    /**
     * Return form fields.
     *
     * @return array
     */
    public function form() {
        $posts_types           = get_query_var( 'search_post_types', [] );
        $searchable_post_types = static::get_searchable_post_types();

        foreach ( $searchable_post_types as $key => $post_type ) {
            $checked = in_array( 'all', $posts_types, true )
                ? false
                : in_array( $post_type['slug'], $posts_types, true );

            $searchable_post_types[ $key ]['is_checked'] = $checked;
        }

        array_unshift(
            $searchable_post_types,
            [
                'slug'       => 'all',
                'name'       => __( 'All', 'tms-theme-base' ),
                'is_checked' => in_array( 'all', $posts_types, true ),
            ]
        );

        return [
            'search_link'         => trailingslashit( get_site_url() ) . '/?s=',
            'post_types'          => $searchable_post_types,
            'search_term'         => trim( get_query_var( 's' ) ),
            'form_start_date'     => get_query_var( self::SEARCH_START_DATE ),
            'form_end_date'       => get_query_var( self::SEARCH_END_DATE ),
            'filter_by_post_type' => __( 'Filter by post type', 'tms-theme-base' ),
            'filter_by_date'      => __( 'Filter by date', 'tms-theme-base' ),
            'filter_start_date'   => __( 'Start date', 'tms-theme-base' ),
            'filter_end_date'     => __( 'End date', 'tms-theme-base' ),
            'filter_results'      => __( 'Filter results', 'tms-theme-base' ),
        ];
    }

    /**
     * Get event search link
     *
     * @return array|null
     */
    public function event_search() : ?array {
        $page = Settings::get_setting( 'events_search_page' );

        if ( empty( $page ) ) {
            return null;
        }

        return [
            'title' => __( 'Looking for events? Use the event search!', 'tms-theme-base' ),
            'url'   => get_the_permalink( $page ),
        ];
    }

    /**
     * Modify search query.
     *
     * @param WP_Query $wp_query Instance of WP_Query.
     */
    public static function modify_query( WP_Query $wp_query ) {
        if ( is_admin() || ( ! $wp_query->is_main_query() || ! is_search() ) ) {
            return;
        }

        $selected_post_types = get_query_var( self::SEARCH_CPT_QUERY_VAR, [] );

        if ( empty( $selected_post_types ) || in_array( 'all', $selected_post_types, true ) ) {
            $selected_post_types = array_map( function ( $item ) {
                return $item['slug'];
            }, static::get_searchable_post_types() );
        }

        $date_query = [];
        $start_date = get_query_var( self::SEARCH_START_DATE );
        $end_date   = get_query_var( self::SEARCH_END_DATE );

        if ( ! empty( $start_date ) ) {
            $dt = new DateTime( $start_date );

            $date_query[] = [
                'after'     => [
                    'year'  => $dt->format( 'Y' ),
                    'month' => $dt->format( 'm' ),
                    'day'   => $dt->format( 'd' ),
                ],
                'inclusive' => true,
            ];
        }

        if ( ! empty( $end_date ) ) {
            $dt = new DateTime( $end_date );

            $date_query[] = [
                'before'    => [
                    'year'  => $dt->format( 'Y' ),
                    'month' => $dt->format( 'm' ),
                    'day'   => $dt->format( 'd' ),
                ],
                'inclusive' => true,
            ];
        }

        if ( ! empty( $date_query ) ) {
            $wp_query->set( 'date_query', $date_query );
        }

        $wp_query->set( 'post_type', $selected_post_types );
    }

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
                'search_form'          => 'has-background-secondary',
                'search_item'          => 'has-background-secondary',
                'search_item_excerpt'  => '',
                'search_filter_button' => '',
                'event_search_section' => 'has-border-bottom-1 has-border-divider',
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
                        $this->get_ancestors(
                            $post_item->ID,
                            Page::SLUG,
                            [ $this->get_home_link() ]
                        )
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

            apply_filters( 'tms/theme/base/search_result_item', $post_item );
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

    /**
     * Get searchable post types
     *
     * @return array
     */
    protected static function get_searchable_post_types() : array {
        $post_types = get_post_types( [], 'objects' );

        return [
            [
                'slug' => Page::SLUG,
                'name' => $post_types[ Page::SLUG ]->labels->singular_name,
            ],
            [
                'slug' => Post::SLUG,
                'name' => $post_types[ Post::SLUG ]->labels->singular_name,
            ],
            [
                'slug' => BlogArticle::SLUG,
                'name' => $post_types[ BlogArticle::SLUG ]->labels->singular_name,
            ],
        ];
    }
}
