<?php
/**
 * Copyright (c) 2023. Hion Digital
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\AnchorLinksFields;

/**
 * Class AnchorLinksBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class AnchorLinksBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'anchor-links';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'anchor_links';

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
        $this->title = 'Ankkurilinkit';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new AnchorLinksFields( $this->title, self::NAME );

        return \apply_filters(
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
        if ( empty( $data['anchor_links'] ) ) {
            return $data;
        }

        foreach ( $data['anchor_links'] as $key => $link ) {
            if ( ! isset( $link['anchor_link']['url'] ) ) {
                continue;
            }
        }

        return \apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }

}
