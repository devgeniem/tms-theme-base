<?php
/**
 * Copyright (c) 2021. Geniem Oy
 * Template Name: Etusivu
 */

use TMS\Theme\Base\Traits\Components;

/**
 * Class PageFrontPage
 */
class PageFrontPage extends BaseModel {

    use Components;

    /**
     * Template
     */
    const TEMPLATE = 'models/page-front-page.php';

    /**
     * Setup hooks.
     */
    public function hooks() {
        add_filter( 'tms/theme/breadcrumbs/page', function ( $formatted, $original, $object ) {
            unset( $formatted, $original, $object );
            return [];
        }, 10, 3 );

        add_filter( 'tms/theme/breadcrumbs/show_breadcrumbs_in_header', function ( $status, $context ) {
            unset( $context, $status );

            return false;
        }, 10, 2 );
    }
}
