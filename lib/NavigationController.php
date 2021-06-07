<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class NavigationController
 *
 * @package TMS\Theme\Base
 */
class NavigationController implements Interfaces\Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_action(
            'after_setup_theme',
            \Closure::fromCallable( [ $this, 'register_nav_menus' ] )
        );
    }

    /**
     * Register navigation menus
     */
    protected function register_nav_menus() : void {
        register_nav_menu( 'primary', __( 'Primary Navigation', 'tms-theme-base' ) );
        register_nav_menu( 'secondary', __( 'Secondary Navigation', 'tms-theme-base' ) );
    }
}
