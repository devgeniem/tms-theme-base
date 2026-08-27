<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class ACFController
 *
 * @package TMS\Theme\Base
 */
class ACFController implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'acf/init',
            \Closure::fromCallable( [ $this, 'require_acf_files' ] )
        );

        \add_filter( 'acf/settings/show_admin', '__return_false' );

        // Use ACF block v3 features in editor (expanded editor popup/button).
        \add_filter(
            'acf/blocks/default_block_version',
            \Closure::fromCallable( [ $this, 'force_acf_block_version' ] ),
            50,
            2
        );
    }

    /**
     * Force ACF blocks to use block version 3.
     *
     * @param int   $default_version The default ACF block version.
     * @param array $block           ACF block settings.
     *
     * @return int
     */
    protected function force_acf_block_version( int $default_version, array $block ) : int {
        unset( $default_version, $block );

        return 3;
    }

    /**
     * This method loops through all files in the
     * ACF directory and requires them.
     */
    protected function require_acf_files() : void {
        $files = array_diff(
            scandir( $this->get_base_dir() ),
            [ '.', '..', 'Field', 'Fields', 'Layouts' ]
        );

        array_walk(
            $files,
            function ( $file ) {
                require_once $this->get_base_dir() . '/' . basename( $file );
            }
        );
    }

    /**
     * Get ACF base dir
     *
     * @return string
     */
    protected function get_base_dir() : string {
        return __DIR__ . '/ACF';
    }
}
