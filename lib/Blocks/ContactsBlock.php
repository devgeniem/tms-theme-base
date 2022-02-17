<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\ContactsFields;
use TMS\Theme\Base\ACF\Fields\ImageFields;
use TMS\Theme\Base\ACF\Fields\MapFields;
use TMS\Theme\Base\Formatters\ContactFormatter;
use TMS\Theme\Base\PostType\Contact;
use TMS\Theme\Base\Settings;

/**
 * Class ContactsBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class ContactsBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'contacts';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'contacts';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'groups';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Yhteystiedot';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new ContactsFields( $this->title, self::NAME );

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
        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }
}
