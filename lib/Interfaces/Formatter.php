<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Interfaces;

/**
 * Interface Formatter
 *
 * @package TMS\Theme\Base\Interfaces
 */
interface Formatter {

    /**
     * Add hooks and filters from this formatter
     *
     * @return void
     */
    public function hooks() : void;
}
