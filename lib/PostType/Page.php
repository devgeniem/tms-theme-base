<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use \TMS\Theme\Base\Interfaces\PostType;

/**
 * This class defines the post type.
 *
 * @package TMS\Theme\Base\PostType
 */
class Page implements PostType {

    /**
     * This defines the slug of this post type.
     */
    const SLUG = 'page';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {}
}
