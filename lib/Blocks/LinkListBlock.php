<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

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
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Linkkkilista';

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
            $group->get_fields()
        );
    }
}
