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
        dustpress()->add_helper( 'isset', new Helpers\IssetHelper() );
        dustpress()->add_helper( 'image', new ImageAdvanced() );
        add_filter(
            'dustpress/pagination/data',
            \Closure::fromCallable( [ $this, 'alter_pagination_data' ] )
        );
        add_filter(
            'dustpress/image/allowed_attributes',
            \Closure::fromCallable( [ $this, 'disable_image_ids' ] )
        );
    }

    /**
     * Alter pagination data
     *
     * @param object $data Pagination settings.
     *
     * @return object
     */
    protected function alter_pagination_data( $data ) : object {
        // Disable pagination hellip_end if link to last page is already present.
        if ( ! empty( $data->pages ) && $data->last_page === end( $data->pages )->page ) {
            $data->hellip_end = false;
        }

        $data->S->page_aria_label = _x( 'Go to Page', 'pagination', 'tms-theme-base' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
        $data->S->next            = _x( 'Next', 'pagination', 'tms-theme-base' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
        $data->S->prev            = _x( 'Previous', 'pagination', 'tms-theme-base' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

        return $data;
    }

    /**
     * Remove id attribute from image output in order to prevent duplicate ids in site markup.
     *
     * @param array $allowed_attributes Allowed attributes.
     *
     * @return array
     */
    protected function disable_image_ids( $allowed_attributes ) : array {
        unset( $allowed_attributes['id'] );

        return $allowed_attributes;
    }
}
