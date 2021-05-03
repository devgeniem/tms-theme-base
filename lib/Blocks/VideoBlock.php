<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\VideoFields;

/**
 * Class VideoBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class VideoBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'video';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'video';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'video-alt2';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Video';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new VideoFields( $this->title, self::NAME );

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
        $data['id']        = wp_unique_id( 'video-' );
        $data['skip_text'] = ( new \Strings() )->s()['video']['skip_embed'];

        return $data;
    }
}
