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
        add_filter(
            'dustpress/pagination/data',
            \Closure::fromCallable( [ $this, 'disable_pagination_hellip_duplicate_link' ] )
        );
    }

    /**
     * Disable pagination hellip_end if link to last page is already present.
     *
     * @param object $data Pagination settings.
     *
     * @return object
     */
    protected function disable_pagination_hellip_duplicate_link( $data ) : object {
        if ( $data->last_page === end( $data->pages )->page ) {
            $data->hellip_end = false;
        }

        return $data;
    }
}
