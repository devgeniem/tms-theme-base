<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\ShareLinksFields;

/**
 * Class ShareLinksBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class ShareLinksBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'share-links';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'share_links';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'format-status';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Somejakopainikkeet';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new ShareLinksFields( $this->title, self::NAME );

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
        $data = self::add_filter_attributes( $data, $instance, $block, $content, $is_preview, $post_id );

        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }

}
