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
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array {
        $data = self::add_filter_attributes( $data, $instance, $block, $content, $is_preview, $post_id );

        $layouts = [
            '50-50' => [
                'is-6-desktop',
                'is-6-desktop',
            ],
            '70-30' => [
                'is-6-desktop is-8-widescreen',
                'is-6-desktop is-4-widescreen',
            ],
            '30-70' => [
                'is-6-desktop is-4-widescreen',
                'is-6-desktop is-8-widescreen',
            ],
        ];

        $altered = $data;
        $chars   = 0;

        foreach ( $altered['rows'] as $row => $row_data ) {
            $row_layout = $row_data['layout'];

            foreach ( $row_data['numbers'] as $number => $numbers_data ) {
                $altered['rows'][ $row ]['numbers'][ $number ]['column_class'] = $layouts[ $row_layout ][ $number ];

                // Setup accent color override.
                $background_color = $altered['rows'][ $row ]['numbers'][ $number ]['background_color'];
                $extra_class      = $background_color === 'primary' ? 'has-colors-accent' : '';

                $altered['rows'][ $row ]['numbers'][ $number ]['extra_class'] = $extra_class;

                // Count the chars in the number field to determine the longest number in the component
                $num_len = strlen( $numbers_data['number'] );
                $chars   = $chars < $num_len ? $num_len : $chars;
            }
        }

        // Set the number text class according to the length of the number field
        if ( $chars <= 4 ) {
            $altered['num_class'] = 'is-text-huge';
        }
        elseif ( $chars <= 6 ) {
            $altered['num_class'] = 'is-text-bigger';
        }
        elseif ( $chars <= 10 ) {
            $altered['num_class'] = 'is-text-big';
        }

        return apply_filters(
            'tms/block/' . self::KEY . '/data',
            $altered,
            $data
        );
    }
}
