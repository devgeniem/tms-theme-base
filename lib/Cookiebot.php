<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class Cookiebot
 *
 * @package TMS\Theme\Base
 */
class Cookiebot implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter( 'script_loader_tag', 
        \Closure::fromCallable( [ $this, 'add_data_attribute' ] ),
        10, 2 );

    }

   /**
     * Add data attribute data-cookieconsent="ignore" to
     * ignore script by cookiebot.
     * This prevents Cookiebot to block the scripts.
     *
     * @param string $tag Script tag.
     * @param string $handle Script handle name.
     * @return string The script tag.
     */

    private function add_data_attribute( $tag, $handle ) {
        $scripts_to_ignore_by_cookiebot = [
            'jquery-core',
            'dustpress',
            'jquery.jsonview',
            'hoverintent-js',
            'admin-bar',
            'dustpress_debugger',
            'tms-plugin-materials-public-js',
            'vendor-js',
            'theme-js',
            'ina-logout-js'
        ];
        if ( ! in_array( $handle, $scripts_to_ignore_by_cookiebot, true ) ) {
            return $tag;
        }
       
        $tag = str_replace( '>', ' data-cookieconsent="ignore">', $tag );
        return $tag;
    }
}
