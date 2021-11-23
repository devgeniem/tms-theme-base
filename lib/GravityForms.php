<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class GravityForms
 *
 * @package TMS\Theme\Base
 */
class GravityForms implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'gform_confirmation_anchor', '__return_true' );
    }
}
