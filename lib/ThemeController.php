<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class ThemeController
 *
 * This class sets up the theme functionalities.
 *
 * @package TMS\Theme\Base
 */
class ThemeController {

    /**
     * The controller instance
     *
     * @var self|null
     */
    private static ?ThemeController $instance = null;

    /**
     * The class instances
     *
     * @var array
     */
    private array $classes = [];

    /**
     * Get the ThemeController
     *
     * @return ThemeController
     */
    public static function instance() : ThemeController {
        if ( ! static::$instance ) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_classes();
    }

    /**
     * Get a single class instance from Theme Controller
     *
     * @param string|null $class Class name to retrieve. See init_classes().
     *
     * @return Interfaces\Controller|null
     */
    public function get_class( ?string $class ) : ?Interfaces\Controller {
        return $this->classes[ $class ] ?? null;
    }

    /**
     * Run setup for theme functionality.
     *
     * @return void
     */
    private function init_classes() : void {
        $this->classes = [
            'ACFController'       => new ACFController(),
            'Admin'               => new Admin(),
            'Assets'              => new Assets(),
            'BlocksController'    => new BlocksController(),
            'Cleanup'             => new Cleanup(),
            'Constants'           => new Constants(),
            'DustPressController' => new DustPressController(),
            'Emojis'              => new Emojis(),
            'Formatters'          => new FormatterController(),
            'Images'              => new Images(),
            'LinkedEvents'        => new LinkedEvents(),
            'Localization'        => new Localization(),
            'PostTypeController'  => new PostTypeController(),
            'Roles'               => new Roles(),
            'TaxonomyController'  => new TaxonomyController(),
            'ThemeSupports'       => new ThemeSupports(),
        ];

        // Loop through the classes and run hooks methods of all controllers.
        array_walk( $this->classes, function ( $instance ) {
            if ( $instance instanceof Interfaces\Controller ) {
                $instance->hooks();
            }
        } );
    }
}
