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
        $content = get_field( 'components' ) ?? [];

        if ( empty( $content ) || ! is_array( $content ) ) {
            return [];
        }

        return $this->handle_layouts( $content );
    }

    /**
     * Format layout data
     *
     * @param array $fields Array of Layout fields.
     *
     * @return array
     */
    protected function handle_layouts( array $fields ) : array {
        $handled = [];

        if ( empty( $fields ) ) {
            return $handled;
        }

        foreach ( $fields as $layout ) {
            if ( empty( $layout['acf_fc_layout'] ) ) {
                continue;
            }

            $acf_layout        = $layout['acf_fc_layout'];
            $layout_name       = str_replace( '_', '-', $acf_layout );
            $layout['partial'] = 'layout-' . $layout_name . '.dust';

            $handled[] = apply_filters(
                "tms/acf/layout/{$acf_layout}/data",
                $layout
            );
        }

        return $handled;
    }
}
