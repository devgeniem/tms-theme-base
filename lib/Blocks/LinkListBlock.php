<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\LinkListFields;

/**
 * Class LinkListBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class LinkListBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'link-list';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'link_list';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'excerpt-view';

    /**
     * The block title.
     *
     * @var string
     */
    protected string $title;

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Linkkilista';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new LinkListFields( $this->title, self::NAME );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields(),
            self::KEY
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
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array { // phpcs:ignore
        if ( empty( $data['links'] ) ) {
            return $data;
        }

        $home_url = defined( 'DPT_PLL_ACTIVE' ) && DPT_PLL_ACTIVE
            ? \pll_home_url()
            : \home_url();

        foreach ( $data['links'] as $key => $link ) {
            if ( ! isset( $link['link']['url'] ) ) {
                continue;
            }

            $is_external_link     = false === strpos( $link['link']['url'], $home_url );
            $is_external_selected = $link['link']['target'] === '_blank';

            $data['links'][ $key ]['link']['is_external'] = $is_external_link || $is_external_selected;
        }

        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }

}
