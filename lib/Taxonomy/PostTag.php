<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Taxonomy;

use \TMS\Theme\Base\Interfaces\Taxonomy;

/**
 * This class defines the taxonomy.
 *
 * @package TMS\Theme\Base\Taxonomy
 */
class PostTag implements Taxonomy {

    /**
     * This defines the slug of this taxonomy.
     */
    const SLUG = 'post_tag';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {}
}
