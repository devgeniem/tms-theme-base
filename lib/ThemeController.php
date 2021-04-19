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
            // Initialize DustPress and the specific helpers.
            'DustPressController' => new DustPressController(),

            // Define all constants.
            'Constants'           => new Constants(),

            // Control theme assets.
            'Assets'              => new Assets(),

            // Control theme images.
            'Images'              => new Images(),

            // Control rewrite rules and other url manipulations.
            'Rewrites'            => new Rewrites(),

            // Clean up default WP markup bloat.
            'Cleanup'             => new Cleanup(),

            // Initialize CPT and Taxonomy translation settings.
            'Localization'        => new Localization(),

            // This controls CPT functionality in the theme.
            'PostTypeController'  => new PostTypeController(),

            // This controls taxonomy functionality in the theme.
            'TaxonomyController'  => new TaxonomyController(),

            // This controls the ACF functionality in the theme.
            'ACFController'       => new ACFController(),

            // This controls the Gutenberg blocks functionalities,
            // set allowed block types and add custom block categories.
            'BlocksController'    => new BlocksController(),

            // Add emoji related modifications.
            'Emojis'              => new Emojis(),

            // Add supported theme functionality.
            'ThemeSupports'       => new ThemeSupports(),

            // Add admin related modifications.
            'Admin'               => new Admin(),
        ];

        // Loop through the classes and run hooks methods of all controllers.
        array_walk( $this->classes, function ( $instance ) {
            if ( $instance instanceof Interfaces\Controller ) {
                $instance->hooks();
            }
        } );
    }
}
