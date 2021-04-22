<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

/**
 * Trait Components
 *
 * Provides Flexible Content handling for views.
 *
 * @package TMS\Theme\Base\Traits
 */
trait Components {

    /**
     * View's flexible layouts
     *
     * @return array
     */
    public function components() : array {
        $content = get_field( 'components' );
        $handled = [];

        if ( empty( $content ) ) {
            return $handled;
        }

        foreach ( $content as $layout ) {
            if ( empty( $layout['acf_fc_layout'] ) ) {
                continue;
            }

            $acf_layout        = $layout['acf_fc_layout'];
            $layout_name       = str_replace( '_', '-', $acf_layout );
            $layout['partial'] = 'partials/layouts/layout-' . $layout_name . '.dust';

            $handled[] = apply_filters(
                "acf/layout/${acf_layout}/data",
                $layout
            );
        }

        return $handled;
    }
}
