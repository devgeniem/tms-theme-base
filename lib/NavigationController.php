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

        // Remove custom links from the menu tool
        $remove_custom_links = apply_filters( 'tms/theme/remove_custom_links', true );

        if ( $remove_custom_links ) {
            add_action(
                'admin_head-nav-menus.php',
                \Closure::fromCallable( [ $this, 'remove_custom_links' ] )
            );
        }
    }

    /**
     * Register navigation menus
     */
    protected function register_nav_menus() : void {
        register_nav_menu( 'primary', __( 'Primary Navigation', 'tms-theme-base' ) );
        register_nav_menu( 'secondary', __( 'Secondary Navigation', 'tms-theme-base' ) );
    }

    /**
     * Remove nav menu meta-box links
     */
    protected function remove_custom_links() : void {
        global $wp_meta_boxes;

        if ( isset( $wp_meta_boxes['nav-menus'], $wp_meta_boxes['nav-menus']['side'] ) ) {
            foreach ( $wp_meta_boxes['nav-menus']['side'] as $nav_menus ) {
                foreach ( $nav_menus as $nav_id => $nav_menu ) {
                    if ( $nav_id === 'add-custom-links' ) {
                        remove_meta_box( $nav_id, 'nav-menus', 'side' );
                    }
                }
            }
        }
    }
}
