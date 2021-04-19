<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

require_once dirname( __FILE__ ) . '/lib/autoload.php';

// Theme setup
TMS\Theme\Base\ThemeController::instance();

/**
 * Global helper function to fetch the ThemeController instance
 *
 * @return TMS\Theme\Base\ThemeController
 */
function ThemeController() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return TMS\Theme\Base\ThemeController::instance();
}
