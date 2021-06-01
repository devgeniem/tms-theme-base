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
     * Init model.
     */
    public function init() : void {
        add_filter( 'tms/theme/breadcrumbs/page', function ( $formatted, $original, $object ) {
            unset( $formatted, $original, $object );
            return [];
        } );
    }
}
