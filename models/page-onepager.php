<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Onepager
 */

use TMS\Theme\Base\Traits\Components;

/**
 * The Onepager class.
 */
class PageOnepager extends BaseModel {

    use Components;

    /**
     * Template
     */
    const TEMPLATE = 'models/page-onepager.php';

    /**
     * Hooks
     *
     * @return void
     */
    public function hooks() : void {
        add_filter( 'tms/theme/hide_main_nav', '__return_true', 10, 0 );
    }

    /**
     * Component navigation items.
     *
     * @return mixed|null
     */
    public function component_nav() {
        $components = get_field( 'components' );

        if ( empty( $components ) ) {
            return null;
        }

        $nav_items = [];

        foreach ( $components as $component ) {
            if ( empty( $component['menu_text'] ) ) {
                continue;
            }

            $component['anchor'] = sanitize_title( $component['menu_text'] );
            $nav_items[]         = $component;
        }

        return $nav_items;
    }
}
