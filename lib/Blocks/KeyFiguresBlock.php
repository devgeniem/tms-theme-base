<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\KeyFiguresFields;

/**
 * Class KeyFiguresBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class KeyFiguresBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'key-figures';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'key_figures';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'admin-network';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Numeronostot';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new KeyFiguresFields( $this->title, self::NAME );

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
    public function base_filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        $layouts = [
            '50-50' => [
                'is-6-tablet',
                'is-6-tablet',
            ],
            '70-30' => [
                'is-6-tablet is-8-desktop',
                'is-6-tablet is-4-desktop',
            ],
            '30-70' => [
                'is-6-tablet is-4-desktop',
                'is-6-tablet is-8-desktop',
            ],
        ];

        $altered_data = $data;

        foreach ( $altered_data['rows'] as $row_key => $row_data ) {
            $row_layout = $row_data['layout'];

            foreach ( $row_data['numbers'] as $numbers_key => $numbers_data ) {
                $altered_data['rows'][ $row_key ]['numbers'][ $numbers_key ]['column_class'] = $layouts[ $row_layout ][ $numbers_key ];
            }
        }

        return apply_filters(
            'tms/block/' . self::KEY . '/data',
            $altered_data,
            $data
        );
    }
}
