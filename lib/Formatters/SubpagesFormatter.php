<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;
use TMS\Theme\Base\PostType\Page;
use WP_Query;

/**
 * Class SubpagesFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class SubpagesFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Subpages';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/subpages/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/subpages/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format layout or block data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) : array {
        $data['subpages']     = $this->get_subpages( $data );
        $data['icon_classes'] = $data['background_color'] === 'primary'
            ? 'is-primary-invert'
            : 'is-primary-light';

        return $data;
    }

    /**
     * Get current page subpages.
     *
     * @param array $data Layout/block data.
     *
     * @return array
     */
    private function get_subpages( array $data ) : array {
        $args = [
            'post_type'              => Page::SLUG,
            'posts_per_page'         => 100,
            'post_parent'            => get_the_ID(),
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'no_found_rows'          => true,
            'fields'                 => 'ids',
            'order'                  => 'ASC',
            'orderby'                => 'menu_order',
        ];

        $wp_query = new WP_Query( $args );

        if ( ! $wp_query->have_posts() ) {
            return [];
        }

        return array_map( function ( $post_id ) use ( $data ) {
            $item = [
                'title' => get_the_title( $post_id ),
                'url'   => get_the_permalink( $post_id ),
            ];

            $item_classes = [];

            if ( $data['display_image'] ) {
                $item_classes[] = 'is-tall';
            }

            if ( $data['display_image'] && has_post_thumbnail( $post_id ) ) {
                $item_classes[] = 'has-background-image has-background-cover is-relative has-text-primary-invert';

                $item['image_id'] = get_post_thumbnail_id( $post_id );
            }
            else {
                $item_classes[] = 'has-background-' . $data['background_color'];
                $item_classes[] = 'has-text-' . $data['background_color'] . '-invert';
            }

            $item['classes'] = implode( ' ', $item_classes );

            return $item;
        }, $wp_query->posts );
    }
}
