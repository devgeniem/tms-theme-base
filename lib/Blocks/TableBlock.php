<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\TableFields;

/**
 * Class TableBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class TableBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'table';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'table';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'table-col-after';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Taulukko';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new TableFields( $this->title, self::NAME );

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
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array { // phpcs:ignore
        if ( empty( $data['table'] ) || empty( $data['table'][0] ) ) {
            return $data;
        }

        $table_post_id     = $data['table'][0];
        $tablepress_tables = json_decode( \get_option( 'tablepress_tables' ), true );
        $tables            = $tablepress_tables['table_post'] ?? [];

        if ( ! empty( $tables ) ) {
            $id = array_search( $table_post_id, $tables, true );

            if ( false !== $id ) {
                $data['table_markup'] = \do_shortcode( '[table id=' . $id . ' responsive="scroll" /]' );
            }
        }

        return $data;
    }
}
