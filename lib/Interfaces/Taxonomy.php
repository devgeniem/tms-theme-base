<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Interfaces;

/**
 * Interface Taxonomy
 *
 * @package TMS\Theme\Base\Interfaces
 */
interface Taxonomy {
    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void;
}
