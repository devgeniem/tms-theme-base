<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

/**
 * BaseLayout
 */
class BaseLayout extends \Geniem\ACF\Field\Flexible\Layout {
    /**
     * Layout key
     */
    const KEY = '_base_layout';

    /**
     * Run default filters to our fields.
     *
     * @param array  $fields ACF Fields.
     * @param string $key    ACF Group Key.
     *
     * @return array
     */
    public function filter_layout_fields( $fields, $key ) : array {
        $filtered = apply_filters( 'tms/acf/layout/' . self::KEY . '/fields', $fields, $key );
        $filtered = apply_filters( 'tms/acf/layout/' . $key . '/fields', $filtered, $key );

        return $filtered;
    }
}
