<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Interfaces\Formatter;
use TMS\Theme\Base\PostType\Page;
use WP_Query;

/**
 * Class SitemapFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class SitemapFormatter implements Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Sitemap';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/layout/sitemap/data',
            [ $this, 'format' ]
        );

        add_filter( 'page_menu_link_attributes', [ $this, 'page_menu_link_attributes' ], 10, 5 );
    }

    /**
     * Format layout or block data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data = [] ) : array {
        $data['sitemap'] = $this->get_sitemap();

        return $data;
    }

    /**
     * Get sitemap.
     *
     * @return string
     */
    private function get_sitemap() : string {
        $args = [
            'post_type'   => Page::SLUG,
            'title_li'    => '',
            'post_status' => 'publish',
            'echo'        => false,
            'sort_column' => 'menu_order, post_title',
        ];

        $pages = wp_list_pages( $args );

        if ( ! empty( $pages ) ) {
            $pages = '<ul class="sitemap--wrapper" role="navigation">' . $pages . '</ul>';
        }

        return $pages;
    }

    /**
     * Filters the HTML attributes applied to a page menu item's anchor element.
     *
     * @since 4.8.0
     *
     * @param array    $attributes   {
     *                               The HTML attributes applied to the menu item's `<a>` element,
     *                               empty strings are ignored.
     *
     * @type string    $href         The href attribute.
     * @type string    $aria_current The aria-current attribute.
     * }
     *
     * @param \WP_Post $page         Page data object.
     * @param int      $depth        Depth of page, used for padding.
     * @param array    $args         An array of arguments.
     * @param int      $current_page ID of the current page.
     *
     * @return array
     */
    public function page_menu_link_attributes( $attributes, $page, $depth, $args, $current_page ) : array {
        unset( $page, $args, $current_page );

        $attributes['data-depth'] = $depth + 1; // Depth 0 is the first level, this makes more sense.

        if (
            $depth === 2 || // Actually the third level
            $depth % 3 === 2 // If depth/3 is int
        ) {
            $attributes['data-depth-toggle'] = 'true';
            $attributes['data-depth-id']     = wp_unique_id( 'sitemap-item-' );
        }

        return $attributes;
    }
}
