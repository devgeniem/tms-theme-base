<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use TMS\Theme\Base\ACF\Fields\ImageBannerFields;

/**
 * Class ImageBannerBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class ImageBannerBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'image-banner';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'image_banner';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'embed-photo';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Kuvabanneri';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $fields = new ImageBannerFields( $this->title, self::NAME );

        return $fields->get_fields();
    }
}
