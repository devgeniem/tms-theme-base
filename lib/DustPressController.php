<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Helpers\ImageAdvanced;

/**
 * Class DustPressController
 *
 * @package TMS\Theme\Base
 */
class DustPressController implements Interfaces\Controller {

    /**
     * Constructor
     */
    public function __construct() {
        dustpress();
    }

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_filter( 'codifier/components/dustphp', function ( $dust ) { // phpcs:ignore
            return dustpress()->dust;
        } );

        dustpress()->add_helper( 'inlinebg', new Helpers\InlineBackgroundHelper() );
        dustpress()->add_helper( 'image', new ImageAdvanced() );
    }
}
