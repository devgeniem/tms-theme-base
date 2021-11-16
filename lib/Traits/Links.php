<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

/**
 * Trait Links
 *
 * Common link functions
 *
 * @package TMS\Theme\Base\Traits
 */
trait Links {

    /**
     * Get search action
     *
     * @return string
     */
    protected function get_search_action() : string {
        $link = DPT_PLL_ACTIVE
            ? PLL()->links->get_home_url( pll_current_language(), true )
            : home_url();

        return trailingslashit( $link );
    }
}
