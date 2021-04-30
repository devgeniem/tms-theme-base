<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\GridFields;

/**
 * Class GridBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class GridBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'grid';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'grid';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'forms';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Grid';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new GridFields( $this->title, self::NAME );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields()
        );
    }

    /**
     * This filters the block ACF data.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        if ( empty( $data['repeater'] ) ) {
            return $data;
        }

        $home_url = defined( 'DPT_PLL_ACTIVE' ) && DPT_PLL_ACTIVE
            ? \pll_home_url()
            : \home_url();

        $highlight_first = $data['highlight_first'] ?? false;
        $is_first        = true;
        $even_odd        = 0;

        $display_default = 'secondary';
        $display_alt     = 'primary';
        $order_default   = 'reversed';
        $order_alt       = '';

        foreach ( $data['repeater'] as $key => $item ) {
            $filtered = $item['selector'] === 'relationship'
                ? $this->filter_data_relationship( $item )
                : $this->filter_data_custom( $item );

            if ( is_array( $filtered['link'] ) && isset( $filtered['link']['url'] ) ) {
                $filtered['link']['is_external'] = false === strpos( $filtered['link']['url'], $home_url );
            }

            if ( $highlight_first && $is_first ) {
                $is_first             = false;
                $filtered['featured'] = true;
            }

            $classes = [];
            // $classes[] = 'item_count_' . $even_odd; // For debugging

            switch ( $even_odd ) {
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
                $classes['display'] = ( $even_odd % 2 ? $display_default : $display_alt );
                $classes['order']   = $order_alt;

                if ( in_array( $even_odd, [ 2, 3, 6, 7 ], true ) ) {
                    $classes['order'] = $order_default;
                }
            }

            $filtered['display'] = 'is-' . $classes['display'];
            $classes['display']  = 'has-colors-' . $classes['display'];

            $classes                  = array_map( 'trim', $classes );
            $filtered['classes']      = trim( implode( ' ', $classes ) );
            $data['repeater'][ $key ] = $filtered;

            $even_odd ++;
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
    private function filter_data_relationship( $data = [] ) {
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
            // TODO: Replace false with site default image ID.
            $image_id = false;
        }

        $link_title = $data['grid_item_relationship']['link_title'] ?? '';
        $link_title = empty( $link_title ) ? $item->post_title : $link_title;

        return [
            'title'       => $item->post_title,
            'link'        => [
                'title'  => $link_title,
                'url'    => get_permalink( $item->ID ),
                'target' => '',
            ],
            'description' => wp_trim_words( get_the_excerpt( $item->ID ), 10 ),
            'image'       => [
                'id' => $image_id,
            ],
        ];
    }

    /**
     * Filter data: Custom.
     *
     * @param array $data Data payload.
     *
     * @return array|false
     */
    private function filter_data_custom( $data = [] ) {
        $item = $data['grid_item_custom'] ?? [];

        if ( empty( $item ) ) {
            return false;
        }

        return $item;
    }

}
