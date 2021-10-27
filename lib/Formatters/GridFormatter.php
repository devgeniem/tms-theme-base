<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Formatters;

use TMS\Theme\Base\Images;

/**
 * Class GridFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class GridFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'Grid';

    /**
     * Formatter hooks.
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/grid/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/layout/grid/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/block/grid/data/type_custom',
            [ $this, 'format_type_custom' ]
        );

        add_filter(
            'tms/acf/block/grid/data/type_relationship',
            [ $this, 'format_type_relationship' ]
        );
    }

    /**
     * Format block data
     *
     * @param array $data ACF Block data.
     *
     * @return array
     */
    public static function format( array $data ) : array {
        if ( empty( $data['repeater'] ) ) {
            return $data;
        }

        $home_url = defined( 'DPT_PLL_ACTIVE' ) && DPT_PLL_ACTIVE
            ? \pll_home_url()
            : \home_url();

        $highlight_first = $data['highlight_first'] ?? false;
        $is_first        = true;
        $iterator        = 0;

        // If we are not getting overriding CSS Classes from $data, use defaults.
        $display_default = ! empty( $data['display_default'] ?? '' ) ? $data['display_default'] : 'secondary';
        $display_alt     = ! empty( $data['display_alt'] ?? '' ) ? $data['display_alt'] : 'primary';
        $order_default   = ! empty( $data['order_default'] ?? '' ) ? $data['order_default'] : 'reversed';
        $order_alt       = ! empty( $data['order_alt'] ?? '' ) ? $data['order_alt'] : '';

        foreach ( $data['repeater'] as $key => $item ) {
            $filtered = apply_filters(
                'tms/acf/block/grid/data/type_' . $item['selector'],
                $item
            );

            if ( ! empty( $filtered ) && is_array( $filtered['link'] ) && isset( $filtered['link']['url'] ) ) {
                $filtered['link']['is_external'] = false === strpos( $filtered['link']['url'], $home_url );
            }

            if ( $highlight_first && $is_first ) {
                $is_first             = false;
                $filtered['featured'] = true;
            }

            $classes = self::grid_item_visuals_selector(
                $iterator,
                $highlight_first,
                $display_default,
                $display_alt,
                $order_default,
                $order_alt
            );

            $filtered['button'] = $classes['display'] === 'primary' ? 'is-primary-invert' : 'is-primary';

            $filtered['display'] = 'is-' . $classes['display'];
            $classes['display']  = 'has-colors-' . $classes['display'];

            if ( $classes['display'] === 'has-colors-primary' ) {
                $classes['display'] .= ' has-colors-accent';
            }

            $classes                  = array_map( 'trim', $classes );
            $filtered['classes']      = trim( implode( ' ', $classes ) );
            $data['repeater'][ $key ] = $filtered;

            $iterator ++;
        }

        return $data;
    }

    /**
     * Filter data: Relationship
     *
     * @param array $data Data payload.
     *
     * @return array|false
     */
    public static function format_type_relationship( $data = [] ) {
        $item = $data['grid_item_relationship']['item'] ?? [];

        if ( empty( $item ) ) {
            return false;
        }

        /**
         * It's a WP_Post object.
         *
         * @var \WP_Post $item
         */
        $item->post_content = '';

        $image_id = get_post_thumbnail_id( $item->ID ) ?? false;
        if ( ! $image_id || $image_id < 1 ) {
            $image_id = Images::get_default_image_id();
        }

        $link_title   = $data['grid_item_relationship']['link_title'] ?? '';
        $link_title   = empty( $link_title ) ? __( 'Read more', 'tms-theme-base' ) : $link_title;
        $item_excerpt = get_the_excerpt( $item->ID );

        if ( ! has_excerpt( $item->ID ) ) {
            $item_excerpt = wp_trim_words( get_the_excerpt( $item->ID ), 10 );
        }

        return [
            'title'       => $item->post_title,
            'link'        => [
                'title'  => $link_title,
                'url'    => get_permalink( $item->ID ),
                'target' => '',
            ],
            'description' => $item_excerpt,
            'image'       => [
                'id' => $image_id,
            ],
        ];
    }

    /**
     * Format data: Custom.
     *
     * @param array $data Data payload.
     *
     * @return array|false
     */
    public static function format_type_custom( $data = [] ) {
        $item = $data['grid_item_custom'] ?? [];

        if ( empty( $item ) ) {
            return false;
        }

        return $item;
    }

    /**
     * Determine the visuals of element in question.
     * The function is expecting that numbering is from 0 to 7 (by specification 3-8 items).
     *
     * @param int    $counter         Item order number in grid. Used to determine what colors and order to use.
     * @param bool   $highlight_first Are we highlighting the first item. Affects the item display.
     * @param string $display_default CSS Class to use when coloring the default way.
     * @param string $display_alt     CSS Class to use when coloring the alternative way.
     * @param string $order_default   CSS Class to use when ordering items the default way.
     * @param string $order_alt       CSS Class to use when ordering items the alternative way.
     *
     * @return array
     */
    public static function grid_item_visuals_selector(
        int    $counter,
        bool   $highlight_first,
        string $display_default,
        string $display_alt,
        string $order_default,
        string $order_alt
    ) : array {
        $classes = [];

        switch ( $counter ) {
            case 0:
            case 2:
            case 5:
                $classes['display'] = $display_alt;
                $classes['order']   = $order_alt;
                break;
            case 1:
            case 4:
            case 7:
                $classes['display'] = $display_default;
                $classes['order']   = $order_default;
                break;
            case 3:
                $classes['display'] = $display_alt;
                $classes['order']   = $order_default;
                break;
            case 6:
                $classes['display'] = $display_default;
                $classes['order']   = $order_alt;
                break;
        }

        if ( ! $highlight_first ) {
            $classes['display'] = ( $counter % 2 ? $display_default : $display_alt );
            $classes['order']   = $order_alt;

            if ( in_array( $counter, [ 2, 3, 6, 7 ], true ) ) {
                $classes['order'] = $order_default;
            }
        }

        return $classes;
    }
}
