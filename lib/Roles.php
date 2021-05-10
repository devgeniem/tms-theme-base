<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Roles
 *
 * @package TMS\Theme\Base
 */
class Roles implements \TMS\Theme\Base\Interfaces\Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'map_meta_cap',
            \Closure::fromCallable( [ $this, 'add_unfiltered_html_capability' ] ),
            1,
            3
        );
    }

    /**
     * Enable unfiltered_html capability for Editors and Administrators.
     *
     * @param array  $caps    The user's capabilities.
     * @param string $cap     Capability name.
     * @param int    $user_id The user ID.
     *
     * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
     */
    protected function add_unfiltered_html_capability( $caps, $cap, $user_id ) {
        if ( $cap === 'unfiltered_html' && user_can( $user_id, 'administrator' ) ) {
            $caps = [ 'unfiltered_html' ];
        }

        return $caps;
    }
}
