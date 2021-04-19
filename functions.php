<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

// Autoload theme
require_once dirname( __FILE__ ) . '/lib/autoload.php';

TMS\Theme\Base\ThemeController::instance();

/**
 * Global helper function to fetch the ThemeController instance
 *
 * @return TMS\Theme\Base\ThemeController
 */
function ThemeController() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return TMS\Theme\Base\ThemeController::instance();
}
