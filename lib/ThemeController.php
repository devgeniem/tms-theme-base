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
     * Constructor
     */
    public function __construct() {
        $this->init_classes();
    }

    /**
     * Run setup for theme functionality.
     *
     * @return void
     */
    protected function init_classes() : void {
        $classes = [
            ACFController::class        => new ACFController(),
            Admin::class                => new Admin(),
            Assets::class               => new Assets(),
            BlocksController::class     => new BlocksController(),
            Cleanup::class              => new Cleanup(),
            Constants::class            => new Constants(),
            DustPressController::class  => new DustPressController(),
            Emojis::class               => new Emojis(),
            FormatterController::class  => new FormatterController(),
            Images::class               => new Images(),
            LinkedEvents::class         => new LinkedEvents(),
            Localization::class         => new Localization(),
            NavigationController::class => new NavigationController(),
            PostTypeController::class   => new PostTypeController(),
            Roles::class                => new Roles(),
            TaxonomyController::class   => new TaxonomyController(),
            ThemeSupports::class        => new ThemeSupports(),
            Comments::class             => new Comments(),
        ];

        $classes = apply_filters(
            'tms/theme/base/functionality/classes',
            $classes
        );

        // Loop through the classes and run hooks methods of all controllers.
        array_walk( $classes, function ( $instance ) {
            if ( $instance instanceof Interfaces\Controller ) {
                $instance->hooks();
            }
        } );

        \Archive::hooks();
        \Home::hooks();
        \ArchiveBlogArticle::hooks();
    }
}
