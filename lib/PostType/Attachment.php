<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use \TMS\Theme\Base\Interfaces\PostType;

/**
 * This class represents WordPress default post type 'attachment'.
 *
 * @package TMS\Theme\Base\PostType
 */
class Attachment implements PostType {

    /**
     * This defines the slug of this post type.
     */
    const SLUG = 'attachment';

    /**
     * This is called in setup automatically.
     *
     * @return void
     */
    public function hooks() : void {}
}
